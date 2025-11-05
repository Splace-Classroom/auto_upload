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
 * Edit form for Auto Upload block
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_auto_upload_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        // Section header
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Block title
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_auto_upload'));
        $mform->setDefault('config_title', get_string('pluginname', 'block_auto_upload'));
        $mform->setType('config_title', PARAM_TEXT);
    }
}