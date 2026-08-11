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
 * Strings for component 'block_auto_upload', language 'en'
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Auto Upload';
$string['auto_upload'] = 'Auto Upload';
$string['auto_upload:addinstance'] = 'Add a new auto upload block';
$string['auto_upload:myaddinstance'] = 'Add auto upload block to Dashboard';

// Block content strings
$string['status'] = 'Status';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['enable'] = 'Enable Auto Upload';
$string['disable'] = 'Disable Auto Upload';
$string['test_api'] = 'Test API Connection';
$string['api_endpoint'] = 'API Endpoint';

// Settings strings
$string['config_title'] = 'Block title';
$string['config_api_endpoint'] = 'API Endpoint URL';
$string['config_api_endpoint_desc'] = 'The endpoint URL where files will be uploaded';
$string['config_api_key'] = 'API Key';
$string['config_api_key_desc'] = 'The API Key sent in the X-API-Key header when making upload requests';
$string['config_enabled'] = 'Enable auto upload';
$string['config_enabled_desc'] = 'Enable automatic file upload to external API';

// Messages
$string['api_test_success'] = 'API connection test successful!';
$string['api_test_failed'] = 'API connection test failed: {$a}';
$string['upload_success'] = 'File uploaded successfully to external API';
$string['upload_failed'] = 'Failed to upload file to external API: {$a}';
$string['auto_upload_enabled'] = 'Auto upload has been enabled';
$string['auto_upload_disabled'] = 'Auto upload has been disabled';

// Privacy
$string['privacy:metadata'] = 'The Auto Upload block does not store any personal data.';