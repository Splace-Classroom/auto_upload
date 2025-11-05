<?php
// This file is part of Moodle - http://moodle.org/
//
// Purge cache and upgrade database for auto upload block
//
// @package    block_auto_upload
// @copyright  2024 Your Name
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

require_once('../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url('/blocks/auto_upload/upgrade.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Auto Upload Upgrade');
$PAGE->set_heading('Auto Upload Upgrade & Cache Purge');

echo $OUTPUT->header();

echo '<h2>Auto Upload Upgrade & Cache Purge</h2>';

// Purge all caches
echo '<div class="alert alert-info">';
echo '<h3>Purging All Caches...</h3>';

// Purge all caches
purge_all_caches();

echo '<p>✅ All caches purged successfully!</p>';
echo '</div>';

// Check plugin version
echo '<div class="alert alert-info">';
echo '<h3>Plugin Information</h3>';

$plugin = new stdClass();
include(__DIR__ . '/version.php');

echo '<p><strong>Component:</strong> ' . $plugin->component . '</p>';
echo '<p><strong>Version:</strong> ' . $plugin->version . '</p>';
echo '<p><strong>Requires:</strong> ' . $plugin->requires . '</p>';
echo '<p><strong>Maturity:</strong> ' . $plugin->maturity . '</p>';
echo '</div>';

// Force upgrade check
echo '<div class="alert alert-warning">';
echo '<h3>Manual Event Observer Registration</h3>';

try {
    // Get the event manager and refresh observers
    $manager = \core\event\manager::instance();
    
    // This will reload all observers from all plugins
    $cache = \cache::make('core', 'observers');
    $cache->purge();
    
    echo '<p>✅ Event observers cache purged!</p>';
    
    // Check if our observers are registered
    $observers = $manager->get_observers();
    $our_observers = 0;
    
    foreach ($observers as $observer) {
        if (strpos($observer->callback, 'block_auto_upload') !== false) {
            $our_observers++;
            echo '<p>✅ Found observer: ' . $observer->eventname . ' → ' . $observer->callback . '</p>';
        }
    }
    
    if ($our_observers > 0) {
        echo '<p><strong>✅ Total observers registered: ' . $our_observers . '</strong></p>';
    } else {
        echo '<p><strong>❌ No observers found! Please check events.php file.</strong></p>';
    }
    
} catch (Exception $e) {
    echo '<p><strong>❌ Error:</strong> ' . $e->getMessage() . '</p>';
}

echo '</div>';

// Show current configuration
echo '<div class="alert alert-info">';
echo '<h3>Current Configuration</h3>';

$enabled = get_config('block_auto_upload', 'enabled');
$api_endpoint = get_config('block_auto_upload', 'api_endpoint');

echo '<p><strong>Auto Upload Enabled:</strong> ' . ($enabled ? 'Yes' : 'No') . '</p>';
echo '<p><strong>API Endpoint:</strong> ' . ($api_endpoint ?: 'Not set (using default)') . '</p>';
echo '</div>';

// Test trigger button
echo '<div class="alert alert-success">';
echo '<h3>Test Upload Trigger</h3>';
echo '<p>You can now try uploading a resource to a course. Check your server logs and API endpoint for activity.</p>';
echo '<p><strong>Important:</strong> Make sure "Auto Upload" is enabled in the block settings!</p>';
echo '</div>';

echo '<div>';
echo '<a href="debug.php" class="btn btn-primary">Go to Debug Page</a> ';
echo '<a href="' . new moodle_url('/admin/settings.php', array('section' => 'blocksettingauto_upload')) . '" class="btn btn-secondary">Go to Settings</a>';
echo '</div>';

echo $OUTPUT->footer();
?>