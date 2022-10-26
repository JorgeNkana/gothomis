<?php
// Define username/password for testing or start this script from commandline with username/password cmd line args:
class smsText{
		
define('USERNAME', sizeof($argv) >= 3 ? $argv[1] : 'nsm-ict');
define('PASSWORD', sizeof($argv) >= 3 ? $argv[2] : 'cfjKrlWQ');

// Fill your data:
define('SENDER_ADDRESS', 'GoT-HOMIS');
define('DESTINATION_ADDRESS', $mobile);
define('MO_NUMBER', $mobile);
define('NOTIFY_URL', '');
define('MO_NOTIFY_URL', '');
define('SOCIAL_INVITES_MESSAGE_KEY', '');
define('SOCIAL_INVITES_APP_SECRET', '');
define('MESSAGE_TEXT',$sms);
define('LANGUAGE_CODE', '');
define('USE_LOCKING_SHIFT', true);
define('USE_SINGLE_SHIFT', false);
require_once 'textsms/send_message.php';


}