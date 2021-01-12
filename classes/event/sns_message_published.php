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
 * The event2sns sns message published event.
 *
 * @package    local_event2sns
 * @copyright  2020 UNICAF LTD <info@unicaf.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_event2sns\event;

use coding_exception;
use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * The event2sns sns message published event class.
 *
 * @package    event2sns
 */
class sns_message_published extends base
{
    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description()
    {
        return "The user with id '$this->userid' did something cool " .
            "on course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     * @throws coding_exception
     */
    public static function get_name()
    {
        return get_string('eventsnsmessagepublished', 'local_event2sns');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init()
    {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = 0;
    }

    /**
     * Custom validation.
     *
     * @return void
     */
    protected function validate_data()
    {
        parent::validate_data();
    }
}
