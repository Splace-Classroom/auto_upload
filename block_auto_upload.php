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
 * Auto Upload Block
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_auto_upload extends block_base {

    /**
     * Initialize the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_auto_upload');
    }

    /**
     * Check if current user can see this block
     */
    public function user_can_addto($page) {
        global $USER;
        
        // Only show to site administrators
        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Check if current user can edit this block
     */
    public function user_can_edit() {
        global $USER;
        
        // Only allow site administrators to edit
        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Customization after instance is loaded
     */
    public function specialization() {
        if (isset($this->config->title) && !empty($this->config->title)) {
            $this->title = $this->config->title;
        } else {
            $this->title = get_string('pluginname', 'block_auto_upload');
        }
    }

    /**
     * Get the content of the block
     */
    public function get_content() {
        global $CFG, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        // Check if user is admin
        if (!has_capability('moodle/site:config', context_system::instance())) {
            $this->content = new stdClass;
            $this->content->text = '';
            return $this->content;
        }

        $this->content = new stdClass;

        // Get current settings
        $enabled = get_config('block_auto_upload', 'enabled');
        $api_endpoint = get_config('block_auto_upload', 'api_endpoint');
        
        if (empty($api_endpoint)) {
            $api_endpoint = 'http://103.155.224.67:5200/uploads';
        }

        // Build the content
        $content = '';
        
        // Status display
        $status_class = $enabled ? 'alert-success' : 'alert-warning';
        $status_text = $enabled ? get_string('enabled', 'block_auto_upload') : get_string('disabled', 'block_auto_upload');
        
        $content .= '<div class="alert ' . $status_class . ' p-2 mb-2">';
        $content .= '<strong>' . get_string('status', 'block_auto_upload') . ':</strong> ' . $status_text;
        $content .= '</div>';

        // API Endpoint display
        $content .= '<div class="mb-2">';
        $content .= '<small><strong>' . get_string('api_endpoint', 'block_auto_upload') . ':</strong><br>';
        $content .= '<code style="font-size: 10px; word-break: break-all;">' . $api_endpoint . '</code></small>';
        $content .= '</div>';

        // Toggle button
        $toggle_url = new moodle_url('/blocks/auto_upload/toggle.php', array(
            'sesskey' => sesskey(),
            'return' => $PAGE->url->out(false)
        ));
        
        $toggle_text = $enabled ? get_string('disable', 'block_auto_upload') : get_string('enable', 'block_auto_upload');
        $button_class = $enabled ? 'btn-warning' : 'btn-success';
        
        $content .= '<div class="mb-2">';
        $content .= '<a href="' . $toggle_url . '" class="btn ' . $button_class . ' btn-sm w-100">';
        $content .= $toggle_text;
        $content .= '</a>';
        $content .= '</div>';

        // Test API button
        $test_url = new moodle_url('/blocks/auto_upload/test_api.php', array(
            'sesskey' => sesskey(),
            'return' => $PAGE->url->out(false)
        ));
        
        $content .= '<div>';
        $content .= '<a href="' . $test_url . '" class="btn btn-info btn-sm w-100">';
        $content .= get_string('test_api', 'block_auto_upload');
        $content .= '</a>';
        $content .= '</div>';

        $this->content->text = $content;
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Define where this block can be added
     */
    public function applicable_formats() {
        return array(
            'all' => false,
            'site-index' => true,
            'course-view' => true,
            'admin' => true,
            'my' => true
        );
    }

    /**
     * Allow multiple instances
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Has global configuration
     */
    public function has_config() {
        return true;
    }
}