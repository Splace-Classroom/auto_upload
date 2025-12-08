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
 * Test API connection
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_login();
require_sesskey();

// Check if user is admin
require_capability('moodle/site:config', context_system::instance());

// Get API endpoint
$api_endpoint = get_config('block_auto_upload', 'api_endpoint');
if (empty($api_endpoint)) {
    $api_endpoint = 'http://103.155.224.67:5200/uploads';
}

// Test connection using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS'); // Use OPTIONS to test connectivity
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Check result
if ($response !== false && ($http_code == 200 || $http_code == 404 || $http_code == 405)) {
    // 404 or 405 might be normal if endpoint doesn't support OPTIONS, but connection is working
    \core\notification::success(get_string('api_test_success', 'block_auto_upload'));
} else {
    $error_message = !empty($error) ? $error : "HTTP Code: $http_code";
    \core\notification::error(get_string('api_test_failed', 'block_auto_upload', $error_message));
}

// Get return URL
$return = optional_param('return', '', PARAM_LOCALURL);
if (empty($return)) {
    $return = new moodle_url('/');
}

redirect($return);