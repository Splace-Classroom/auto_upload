<?php
// This file is part of Moodle - http://moodle.org/
//
// Debug script for auto upload block
//
// @package    block_auto_upload
// @copyright  2024 Your Name
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

require_once('../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/blocks/auto_upload/debug.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Auto Upload Debug');
$PAGE->set_heading('Auto Upload Debug Information');

echo $OUTPUT->header();

echo '<h2>Auto Upload Debug Information</h2>';

// Check if plugin is enabled
$enabled = get_config('block_auto_upload', 'enabled');
$api_endpoint = get_config('block_auto_upload', 'api_endpoint');

echo '<div class="alert alert-info">';
echo '<h3>Plugin Configuration</h3>';
echo '<p><strong>Auto Upload Enabled:</strong> ' . ($enabled ? 'Yes' : 'No') . '</p>';
echo '<p><strong>API Endpoint:</strong> ' . ($api_endpoint ?: 'Not set') . '</p>';
echo '</div>';

// Check if observers are registered
global $DB;

echo '<div class="alert alert-info">';
echo '<h3>Event Observers Check</h3>';

// Check if events table exists and has our observers
$events_to_check = [
    '\core\event\file_created',
    '\assignsubmission_file\event\submission_created',
    '\assignsubmission_file\event\submission_updated',
    '\mod_resource\event\resource_created',
    '\mod_resource\event\resource_updated'
];

foreach ($events_to_check as $event) {
    echo '<p><strong>Event:</strong> ' . $event . '</p>';
}
echo '</div>';

// Recent file uploads check
echo '<div class="alert alert-info">';
echo '<h3>Recent File Events (Last 24 hours)</h3>';

$sql = "SELECT l.*, c.fullname as coursename 
        FROM {logstore_standard_log} l 
        LEFT JOIN {course} c ON l.courseid = c.id
        WHERE l.eventname LIKE '%file%' 
        AND l.timecreated > :since
        ORDER BY l.timecreated DESC 
        LIMIT 20";

$since = time() - (24 * 60 * 60); // Last 24 hours
$records = $DB->get_records_sql($sql, ['since' => $since]);

if ($records) {
    echo '<table class="table table-striped">';
    echo '<thead><tr><th>Time</th><th>Event</th><th>Course</th><th>User</th><th>Context</th></tr></thead>';
    echo '<tbody>';
    foreach ($records as $record) {
        echo '<tr>';
        echo '<td>' . date('Y-m-d H:i:s', $record->timecreated) . '</td>';
        echo '<td>' . $record->eventname . '</td>';
        echo '<td>' . ($record->coursename ?: 'N/A') . '</td>';
        echo '<td>' . $record->userid . '</td>';
        echo '<td>' . $record->contextlevel . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No recent file events found.</p>';
}
echo '</div>';

// Test file storage
echo '<div class="alert alert-info">';
echo '<h3>File Storage Test</h3>';

$fs = get_file_storage();
$context = context_system::instance();

// Get recent files
$files = $fs->get_area_files($context->id, 'core', 'backup', false, 'timemodified DESC', false, 0, 10);

if ($files) {
    echo '<p>Recent files in system:</p>';
    echo '<ul>';
    foreach ($files as $file) {
        if (!$file->is_directory()) {
            echo '<li>' . $file->get_filename() . ' (Modified: ' . date('Y-m-d H:i:s', $file->get_timemodified()) . ')</li>';
        }
    }
    echo '</ul>';
} else {
    echo '<p>No recent files found in system context.</p>';
}

echo '</div>';

// Manual test upload form
echo '<div class="alert alert-warning">';
echo '<h3>Manual Test Upload</h3>';
echo '<form method="post" enctype="multipart/form-data">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
echo '<input type="file" name="testfile" required>';
echo '<input type="number" name="test_course_id" placeholder="Course ID" value="1" required>';
echo '<input type="number" name="test_module_id" placeholder="Module ID" value="0">';
echo '<button type="submit" name="manual_test" class="btn btn-primary">Test Manual Upload</button>';
echo '</form>';
echo '</div>';

// Handle manual test
if (isset($_POST['manual_test']) && confirm_sesskey()) {
    if (isset($_FILES['testfile']) && $_FILES['testfile']['error'] === UPLOAD_ERR_OK) {
        $course_id = (int)$_POST['test_course_id'];
        $module_id = (int)$_POST['test_module_id'];
        
        $file_content = file_get_contents($_FILES['testfile']['tmp_name']);
        $filename = $_FILES['testfile']['name'];
        $mimetype = $_FILES['testfile']['type'];
        
        echo '<div class="alert alert-success">';
        echo '<h4>Manual Test Results</h4>';
        
        // Call the upload function directly
        require_once(__DIR__ . '/classes/observer.php');
        
        $api_endpoint = get_config('block_auto_upload', 'api_endpoint') ?: 'http://165.22.62.163:5000/uploads';
        
        // Create temporary file for upload
        $temp_file = tempnam(sys_get_temp_dir(), 'moodle_upload_');
        file_put_contents($temp_file, $file_content);

        // Prepare form data
        $post_data = array(
            'course_id' => $course_id,
            'module_id' => $module_id,
            'file' => new CURLFile($temp_file, $mimetype, $filename)
        );

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        // Execute the request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Clean up temporary file
        unlink($temp_file);
        
        echo '<p><strong>API Endpoint:</strong> ' . $api_endpoint . '</p>';
        echo '<p><strong>Course ID:</strong> ' . $course_id . '</p>';
        echo '<p><strong>Module ID:</strong> ' . $module_id . '</p>';
        echo '<p><strong>Filename:</strong> ' . $filename . '</p>';
        echo '<p><strong>HTTP Code:</strong> ' . $http_code . '</p>';
        echo '<p><strong>Response:</strong> ' . htmlspecialchars($response) . '</p>';
        if ($error) {
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}

echo $OUTPUT->footer();
?>