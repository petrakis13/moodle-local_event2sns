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
 * Event Handler Class perform the logic behind every handler
 *
 * @package    local_event2sns
 * @copyright  2020 UNICAF LTD <info@unicaf.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_event2sns;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use core\event\course_module_created;
use core\event\course_module_deleted;
use core\event\course_module_updated;
use core\event\course_restored;
use core\event\grade_item_updated;
use core\event\user_graded;
use mod_assign\event\assessable_submitted;
use mod_assign\event\submission_graded;
use dml_exception;
use mod_quiz\event\attempt_submitted;
use core\event\course_deleted;

require_once($CFG->dirroot . '/local/aws/sdk/aws-autoloader.php');
require_once($CFG->dirroot . '/local/event2sns/lib.php');

/**
 * Class event_handler
 * @package event2sns
 */
class event_handler
{

    /**
     * Capture only those type of modules
     * @var string[]
     */
    private static $assignment_modules = ['assign', 'quiz'];

    /**
     * Triggers when user submit a assignment
     *
     * @param assessable_submitted $event
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function assessable_submitted(assessable_submitted $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if (!$record) {
            return;
        }

        $user_submission = $event->get_assign()->get_user_submission($event_data['userid'], false);

        $data = [
            'action' => 'assignment_submitted',
            'assignment_id' => $user_submission->assignment,
            'user_id' => $record->userid,
            'course_id' => $event_data['courseid']
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when user submit a quiz
     *
     * @param attempt_submitted $event
     * @throws dml_exception
     */
    public static function attempt_submitted(attempt_submitted $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if (!$record) {
            return;
        }

        $data = [
            'action' => 'attempt_submitted',
            'quiz_id' => $record->quiz,
            'user_id' => $record->userid,
            'course_id' => $event_data['courseid']
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when user grade an assignment
     *
     * @param submission_graded $event
     * @throws dml_exception
     */
    public static function submission_graded(submission_graded $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if (!$record) {
            return;
        }

        $filters = [];
        $filters['itemtype'] = 'mod';
        $filters['itemmodule'] = 'assign';
        $filters['iteminstance'] = $record->assignment;
        $grade_item = $DB->get_record('grade_items', $filters, '*');

        if (!$grade_item) {
            return;
        }

        $data = [
            'action' => 'submission_graded',
            'grade_item_id' => $grade_item->id,
            'user_id' => $record->userid,
        ];


        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when user grade an assignment
     *
     * @param user_graded $event
     * @throws dml_exception|coding_exception
     */
    public static function user_graded(user_graded $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if (!$record) {
            return;
        }

        $grade_item = $event->get_grade()->get_record_data();

        if ($event->get_grade()->grade_item->itemtype == 'course') {
            return;
        }

        $data = [
            'action' => 'submission_graded',
            'grade_item_id' => $grade_item->itemid,
            'user_id' => $record->userid,
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when a module is getting created
     *
     * @param course_module_created $event
     * @throws dml_exception
     */
    public static function course_module_created(course_module_created $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if (!$record) {
            return;
        }

        $module = $DB->get_record('modules', ['id' => $record->module]);

        if (!$module || !in_array($module->name, self::$assignment_modules)) {
            return;
        }

        $data = [
            'action' => 'module_created',
            'module_type' => $module->name,
            'instanceid' => $event_data['other']['instanceid']
        ];


        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when a module is getting deleted
     *
     * @param course_module_deleted $event
     */
    public static function course_module_deleted(course_module_deleted $event)
    {
        $event_data = $event->get_data();

        $module_name = $event_data['other']['modulename'];
        if (!in_array($module_name, self::$assignment_modules)) {
            return;
        }

        $data = [
            'action' => 'module_deleted',
            'module_type' => $event_data['other']['modulename'],
            'instanceid' => $event_data['other']['instanceid']
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when a module is getting updated
     *
     * @param course_module_updated $event
     * @throws dml_exception
     */
    public static function course_module_updated(course_module_updated $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if (!$record) {
            return;
        }

        $module = $DB->get_record('modules', ['id' => $record->module]);

        if (!$module || !in_array($module->name, self::$assignment_modules)) {
            return;
        }

        $data = [
            'action' => 'module_updated',
            'module_type' => $module->name,
            'instanceid' => $event_data['other']['instanceid']
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    /**
     * Triggers when weight is changing
     *
     * @param grade_item_updated $event
     * @throws dml_exception
     */
    public static function grade_item_updated(grade_item_updated $event)
    {
        global $DB;
        $event_data = $event->get_data();
        $record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');

        if ($record->itemtype != 'mod' or !in_array($record->itemmodule, ['assign', 'quiz'])) {
            return;
        }

        $data = [
            'action' => 'module_updated',
            'module_type' => $record->itemmodule,
            'instanceid' => $record->iteminstance
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }

    public static function course_restored(course_restored $event) {
        $event_data = $event->get_data();

        $data = [
            'action' => 'course_restored',
            'courseid' => $event_data['objectid'],
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }
    
    
     /**
     * Triggers when a course is getting deleted
     *
     * @param course_deleted $event
     */
    public static function course_deleted(course_deleted $event)
    {
        $event_data = $event->get_data();

        $data = [
            'action' => 'course_deleted',
            'courseid' => $event_data['objectid'],
        ];

        publish_sns_message($event->get_context(), 'lms_assignments', $data);
    }
    
}
