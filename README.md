# Event2sns - Moodle Plugin

A moodle plugin which can publish an AWS SNS Message after some events.

To use this plugin, please place the following settings in the config.php

```php
// This is the AWS SNS Topic ARN which we publish the message
$CFG->event2sns_topicarn = 'arn:aws:sns:us-central-1:9999999999999:my_cool_topic';
// This is a custom site id which will be used by the application / service will receive 
// the message, in order to identify the source (from where that message came)
$CFG->event2sns_siteid = 'my_custom_site_id';

// to filter which users creation/update should be tracked. If value is * then all users are tracked, otherwise only the specified end of string
$CFG->filter_event_user_suffix = 'your-custom-domain.org';
``` 

## Current Supported Events
The following list, are the events that are currently being catch by the plugin and publishing 
sns messages

* \mod_assign\event\assessable_submitted
* \mod_quiz\event\attempt_submitted
* \mod_assign\event\submission_graded
* \core\event\course_module_created
* \core\event\course_module_deleted
* \core\event\course_module_updated
* \core\event\grade_item_updated 
* \core\event\course_deleted
* \core\event\course_restored
* \core\event\user_graded
* \core\event\user_created
* \core\event\user_updated


## Requirements
*  Moodle 3.1 or greater
*  local_aws plugin (can be found in Moodle Plugin Directory)

## Installation

You can install this plugin by getting the latest version on GitHub.

```bash
cd /path/to/moodle/source
git clone https://github.com/unicaf/moodle-local_event2sns local/event2sns
```

# Developed by Unicaf LTD
This plugin developed by Unicaf LTD:

https://www.unicaf.org/

![Unicaf LTD](/pix/unicaf_logo.png?raw=true)


# Contribution
Issues, and pull requests using github are welcome and encouraged! 

https://github.com/unicaf/moodle-local_event2sns/issues
