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
 * Library of useful functions
 *
 * @package    local_event2sns
 * @copyright  2020 UNICAF LTD <info@unicaf.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_event2sns;

defined('MOODLE_INTERNAL') || die;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;
use coding_exception;
use context;
use context_system;
use local_event2sns\event\sns_message_published;

/**
 * Publish an SNS Message using AWS SDK Plugin
 *
 * @param context $context
 * @param string $action The action will be send
 * @param string|array $data The data will be send
 * @return bool
 */
function publish_sns_message($context, $action, $data)
{
    global $CFG;

    if (!isset($CFG->event2sns_topicarn)) {
        error_log('$CFG->event2sns_arn is missing from the config.php');
        return false;
    }

    if (!isset($CFG->event2sns_siteid)) {
        error_log('$CFG->event2sns_siteid is missing from the config.php');
    }

    // Default configuration
    $configuration = [
        'region' => 'eu-central-1',
        'version' => 'latest',
    ];

    if (isset($CFG->aws_sdk_config)) {
        $configuration = $CFG->aws_sdk_config;
    }

    // Initiate the SNS Client
    $client = new SnsClient($configuration);

    // Get the site id
    $data['siteid'] = $CFG->event2sns_siteid;

    // Prepare the payload
    $final_data = [
        'action' => $action,
        'data' => $data
    ];

    // Encode the data into a json
    $message = json_encode([
        'default' => json_encode($final_data),
    ]);

    // Prepare the SNS Data
    $data = [
        'Message' => $message,
        'MessageStructure' => 'json',
        'TopicArn' => $CFG->event2sns_topicarn
    ];

    // Publish the message
    try {
        $client->publish($data);
        // This might slow down the performance of the moodle and we need
        // to research more about storing this event
//        $event = sns_message_published::create(['context' => $context, 'other' => $data]);
//        $event->trigger();
    } catch (AwsException $e) {
        error_log($e->getMessage());
        return false;
    }

    return true;
}