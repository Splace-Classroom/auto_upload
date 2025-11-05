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
 * Event observers
 *
 * @package    block_auto_upload
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => '\core\event\file_created',
        'callback' => '\block_auto_upload\observer::file_created',
        'internal' => false,
    ),
    array(
        'eventname' => '\core\event\file_uploaded',
        'callback' => '\block_auto_upload\observer::file_created',
        'internal' => false,
    ),
    array(
        'eventname' => '\assignsubmission_file\event\submission_created',
        'callback' => '\block_auto_upload\observer::file_uploaded',
        'internal' => false,
    ),
    array(
        'eventname' => '\assignsubmission_file\event\submission_updated',
        'callback' => '\block_auto_upload\observer::file_uploaded',
        'internal' => false,
    ),
    array(
        'eventname' => '\mod_resource\event\course_module_created',
        'callback' => '\block_auto_upload\observer::file_uploaded',
        'internal' => false,
    ),
    array(
        'eventname' => '\mod_resource\event\course_module_updated',
        'callback' => '\block_auto_upload\observer::file_uploaded',
        'internal' => false,
    ),
    array(
        'eventname' => '\core\event\course_module_created',
        'callback' => '\block_auto_upload\observer::file_uploaded',
        'internal' => false,
    ),
    array(
        'eventname' => '\core\event\course_module_updated',
        'callback' => '\block_auto_upload\observer::file_uploaded',
        'internal' => false,
    ),
);