<?php
// This file is part of Moodle - http://moodle.org/
//
// Log viewer for auto upload block
//
// @package    block_auto_upload
// @copyright  2024 Your Name
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

require_once('../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/blocks/auto_upload/logs.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Auto Upload Logs');
$PAGE->set_heading('Auto Upload Error Logs');

echo $OUTPUT->header();

echo '<h2>Auto Upload Error Logs</h2>';

// Get PHP error log path
$error_log_path = ini_get('error_log');
if (empty($error_log_path)) {
    // Try common paths
    $possible_paths = [
        '/var/log/apache2/error.log',
        '/var/log/httpd/error_log',
        '/xampp/apache/logs/error.log',
        'C:\xampp\apache\logs\error.log',
        'C:\xampp\php\logs\php_error_log',
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $error_log_path = $path;
            break;
        }
    }
}

echo '<div class="alert alert-info">';
echo '<p><strong>Error Log Path:</strong> ' . ($error_log_path ?: 'Not found') . '</p>';
echo '</div>';

// Show auto upload related logs
if ($error_log_path && file_exists($error_log_path)) {
    echo '<div class="alert alert-success">';
    echo '<h3>Recent Auto Upload Logs (Last 100 lines)</h3>';
    
    // Read last 100 lines and filter for auto upload
    $lines = [];
    $handle = fopen($error_log_path, 'r');
    if ($handle) {
        // Read all lines into array
        while (($line = fgets($handle)) !== false) {
            if (stripos($line, 'auto upload') !== false) {
                $lines[] = $line;
            }
        }
        fclose($handle);
        
        // Show last 50 auto upload related lines
        $recent_lines = array_slice($lines, -50);
        
        if (!empty($recent_lines)) {
            echo '<pre style="background: #f8f9fa; padding: 10px; max-height: 400px; overflow-y: auto;">';
            foreach ($recent_lines as $line) {
                echo htmlspecialchars($line);
            }
            echo '</pre>';
        } else {
            echo '<p>No auto upload related logs found.</p>';
        }
    }
    echo '</div>';
} else {
    echo '<div class="alert alert-warning">';
    echo '<h3>Cannot Access Error Log</h3>';
    echo '<p>Error log file not found or not accessible. Check your PHP configuration.</p>';
    echo '<p>You can check logs in:</p>';
    echo '<ul>';
    echo '<li>PHP error_log setting</li>';
    echo '<li>Apache/Nginx error logs</li>';
    echo '<li>Moodle dataroot logs</li>';
    echo '</ul>';
    echo '</div>';
}

// Show alternative - Moodle logs
echo '<div class="alert alert-info">';
echo '<h3>Alternative: Check Moodle Logs</h3>';
echo '<p>You can also check Moodle system logs at:</p>';
echo '<p><a href="' . new moodle_url('/report/log/index.php', ['id' => 1]) . '" class="btn btn-primary">Site Administration → Reports → Logs</a></p>';
echo '</div>';

// Real-time log monitoring
echo '<div class="alert alert-warning">';
echo '<h3>Real-time Monitoring</h3>';
echo '<p>To monitor logs in real-time, use command line:</p>';
echo '<code>tail -f ' . ($error_log_path ?: '/path/to/error.log') . ' | grep "Auto upload"</code>';
echo '</div>';

echo '<div class="mt-3">';
echo '<a href="debug.php" class="btn btn-secondary">Back to Debug</a> ';
echo '<a href="upgrade.php" class="btn btn-primary">Upgrade & Cache Purge</a>';
echo '</div>';

echo $OUTPUT->footer();
?>