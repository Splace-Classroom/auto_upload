<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Observer for file events
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_auto_upload;

defined('MOODLE_INTERNAL') || die();

class observer {

    /**
     * Handle file created events
     *
     * @param \core\event\base $event
     */
    public static function file_created(\core\event\base $event) {
        self::handle_file_event($event);
    }

    /**
     * Handle file uploaded events
     *
     * @param \core\event\base $event
     */
    public static function file_uploaded(\core\event\base $event) {
        self::handle_file_event($event);
    }

    /**
     * Handle file events and upload to external API
     *
     * @param \core\event\base $event
     */
    private static function handle_file_event(\core\event\base $event) {
        global $DB;

        // Debug logging
        error_log("Auto upload: Event received - " . $event->eventname . " Object ID: " . $event->objectid . " Context ID: " . $event->contextid);

        // Check if auto upload is enabled
        $enabled = get_config('block_auto_upload', 'enabled');
        if (!$enabled) {
            error_log("Auto upload: Disabled, skipping");
            return;
        }

        // Get context information first
        $context = \context::instance_by_id($event->contextid);
        $course_id = 0;
        $module_id = 0;

        // Determine course_id and module_id based on context
        if ($context->contextlevel == CONTEXT_COURSE) {
            $course_id = $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $module_id = $context->instanceid;
            // Get course from module
            $cm = get_coursemodule_from_id('', $module_id);
            if ($cm) {
                $course_id = $cm->course;
            }
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, we might not have specific course/module
            error_log("Auto upload: Course category context, skipping");
            return;
        } else {
            // Try to get course from context path
            $course_context = $context->get_course_context(false);
            if ($course_context) {
                $course_id = $course_context->instanceid;
            }
        }

        // If we still don't have course_id, skip
        if ($course_id <= 0) {
            error_log("Auto upload: No valid course_id found, skipping");
            return;
        }

        // For module events, try to find associated files
        if (in_array($event->eventname, [
            '\core\event\course_module_created',
            '\core\event\course_module_updated',
            '\mod_resource\event\course_module_created',
            '\mod_resource\event\course_module_updated'
        ])) {
            self::handle_module_event($event, $course_id, $module_id);
            return;
        }

        // For file events, get the specific file
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($event->objectid);
        
        if (!$file || $file->is_directory()) {
            error_log("Auto upload: No valid file found or is directory, object ID: " . $event->objectid);
            return;
        }

        // Prepare file data for upload
        $file_content = $file->get_content();
        $filename = $file->get_filename();
        $mimetype = $file->get_mimetype();

        // Get course name
        $course_name = self::get_course_name($course_id);

        error_log("Auto upload: Processing file - $filename, Course: $course_id ($course_name), Module: $module_id");

        // Upload to external API
        self::upload_to_api($file_content, $filename, $mimetype, $course_id, $module_id, $course_name);
    }

    /**
     * Handle module creation/update events to find associated files
     *
     * @param \core\event\base $event
     * @param int $course_id
     * @param int $module_id
     */
    private static function handle_module_event(\core\event\base $event, $course_id, $module_id) {
        global $DB;

        // Get all files in the module context
        $fs = get_file_storage();
        $context = \context::instance_by_id($event->contextid);
        
        // Get files from the module context
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', false, 'timemodified DESC', false);
        
        foreach ($files as $file) {
            if (!$file->is_directory()) {
                $file_content = $file->get_content();
                $filename = $file->get_filename();
                $mimetype = $file->get_mimetype();
                
                // Get course name
                $course_name = self::get_course_name($course_id);

                error_log("Auto upload: Processing module file - $filename, Course: $course_id ($course_name), Module: $module_id");
                
                // Upload to external API
                self::upload_to_api($file_content, $filename, $mimetype, $course_id, $module_id, $course_name);
            }
        }
    }

    /**
     * Get course name from course ID
     *
     * @param int $course_id
     * @return string
     */
    private static function get_course_name($course_id) {
        global $DB;
        
        $course = $DB->get_record('course', array('id' => $course_id), 'fullname');
        if ($course) {
            return $course->fullname;
        }
        return 'Unknown Course';
    }

    /**
     * Upload file to external API
     *
     * @param string $file_content
     * @param string $filename
     * @param string $mimetype
     * @param int $course_id
     * @param int $module_id
     * @param string $course_name
     */
    private static function upload_to_api($file_content, $filename, $mimetype, $course_id, $module_id, $course_name = '') {
        // Get API endpoint
        $api_endpoint = get_config('block_auto_upload', 'api_endpoint');
        if (empty($api_endpoint)) {
            error_log("Auto upload: API endpoint URL is not configured, skipping upload");
            return;
        }

        $api_key = get_config('block_auto_upload', 'api_key');

        error_log("Auto upload: Uploading to API - $api_endpoint");

        // Create temporary file for upload
        $temp_file = tempnam(sys_get_temp_dir(), 'moodle_upload_');
        file_put_contents($temp_file, $file_content);

        // Prepare form data
        $post_data = array(
            'course_id' => (string)$course_id,
            'module_id' => (string)$module_id,
            'course_name' => (string)$course_name,
            'file' => new \CURLFile($temp_file, $mimetype, $filename)
        );

        error_log("Auto upload: Sending data - Course: $course_id, Module: $module_id, File: $filename");

        // Set headers
        $headers = array();
        if (!empty($api_key)) {
            $headers[] = 'X-API-Key: ' . $api_key;
        }

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Execute the request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Clean up temporary file
        unlink($temp_file);

        // Log the result
        if ($response !== false && $http_code == 200) {
            error_log("Auto upload SUCCESS: $filename to course $course_id, module $module_id - Response: " . substr($response, 0, 200));
        } else {
            $error_message = !empty($error) ? $error : "HTTP Code: $http_code";
            error_log("Auto upload FAILED: $filename - $error_message - Response: " . substr($response, 0, 200));
        }
    }
}