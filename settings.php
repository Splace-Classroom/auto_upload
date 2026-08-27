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
 * Global settings for Auto Upload block
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    
    // Enable/Disable auto upload
    $settings->add(new admin_setting_configcheckbox(
        'block_auto_upload/enabled',
        get_string('config_enabled', 'block_auto_upload'),
        get_string('config_enabled_desc', 'block_auto_upload'),
        0
    ));

    // API Endpoint URL
    $settings->add(new admin_setting_configtext(
        'block_auto_upload/api_endpoint',
        get_string('config_api_endpoint', 'block_auto_upload'),
        get_string('config_api_endpoint_desc', 'block_auto_upload'),
        'https://api.gumilarmn.me/uploads',
        PARAM_URL
    ));

    // API Key
    $settings->add(new admin_setting_configtext(
        'block_auto_upload/api_key',
        get_string('config_api_key', 'block_auto_upload'),
        get_string('config_api_key_desc', 'block_auto_upload'),
        '',
        PARAM_RAW
    ));
}