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
 * Define event handlers
 *
 * @package    local_event2sns
 * @copyright  2020 UNICAF LTD <info@unicaf.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => '\local_event2sns\event_handler::assessable_submitted',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => '\local_event2sns\event_handler::attempt_submitted',
    ],
    [
        'eventname' => '\mod_assign\event\submission_graded',
        'callback' => '\local_event2sns\event_handler::submission_graded',
    ],
    [
        'eventname' => '\core\event\user_graded',
        'callback' => '\local_event2sns\event_handler::user_graded',
    ],
    [
        'eventname' => '\core\event\course_module_created',
        'callback' => '\local_event2sns\event_handler::course_module_created',
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback' => '\local_event2sns\event_handler::course_module_deleted',
    ],
    [
        'eventname' => '\core\event\course_module_updated',
        'callback' => '\local_event2sns\event_handler::course_module_updated',
    ],
    [
        'eventname' => '\core\event\grade_item_updated',
        'callback' => '\local_event2sns\event_handler::grade_item_updated',
    ],
    [
        'eventname' => '\core\event\course_restored',
        'callback' => '\local_event2sns\event_handler::course_restored',
    ],
    [
        'eventname' => '\mod_assign\event\user_override_created',
        'callback' => '\local_event2sns\event_handler::user_override_updated',
    ],
    [
        'eventname' => '\mod_assign\event\user_override_updated',
        'callback' => '\local_event2sns\event_handler::user_override_updated',
    ],
    [
        'eventname' => '\mod_quiz\event\user_override_created',
        'callback' => '\local_event2sns\event_handler::user_override_updated',
    ],
    [
        'eventname' => '\mod_quiz\event\user_override_updated',
        'callback' => '\local_event2sns\event_handler::user_override_updated',
    ],
    [
        'eventname' => '\mod_assign\event\user_override_deleted',
        'callback' => '\local_event2sns\event_handler::user_override_deleted',
    ],
    [
        'eventname' => '\mod_assign\event\group_override_created',
        'callback' => '\local_event2sns\event_handler::group_override_updated',
    ],
    [
        'eventname' => '\mod_assign\event\group_override_updated',
        'callback' => '\local_event2sns\event_handler::group_override_updated',
    ],
    [
        'eventname' => '\mod_assign\event\group_override_deleted',
        'callback' => '\local_event2sns\event_handler::group_override_deleted',
    ],
    [
        'eventname' => '\mod_quiz\event\user_override_deleted',
        'callback' => '\local_event2sns\event_handler::user_override_deleted',
    ],
    [
        'eventname' => '\mod_quiz\event\group_override_created',
        'callback' => '\local_event2sns\event_handler::group_override_updated',
    ],
    [
        'eventname' => '\mod_quiz\event\group_override_updated',
        'callback' => '\local_event2sns\event_handler::group_override_updated',
    ],
    [
        'eventname' => '\mod_quiz\event\group_override_deleted',
        'callback' => '\local_event2sns\event_handler::group_override_deleted',
    ],
    [
        'eventname' => '\core\event\course_deleted',
        'callback' => '\local_event2sns\event_handler::course_deleted',
    ],
    [
        'eventname' => '\core\event\user_created',
        'callback' => '\local_event2sns\event_handler::user_created',
    ],
    [
        'eventname' => '\core\event\user_updated',
        'callback' => '\local_event2sns\event_handler::user_updated',
    ]
];
