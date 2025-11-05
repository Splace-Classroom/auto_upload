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
 * Toggle auto upload on/off
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

// Get current setting
$current_setting = get_config('block_auto_upload', 'enabled');

// Toggle the setting
$new_setting = $current_setting ? 0 : 1;
set_config('enabled', $new_setting, 'block_auto_upload');

// Set notification message
$message = $new_setting ? 
    get_string('auto_upload_enabled', 'block_auto_upload') : 
    get_string('auto_upload_disabled', 'block_auto_upload');

\core\notification::success($message);

// Get return URL
$return = optional_param('return', '', PARAM_LOCALURL);
if (empty($return)) {
    $return = new moodle_url('/');
}

redirect($return);