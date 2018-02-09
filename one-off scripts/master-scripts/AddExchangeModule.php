<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



/*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$module = Vtiger_Module::getInstance('Exchange');
if ($module) {
    echo "Exchange module already exists!";
} else {
    $module = new Vtiger_Module();
    $module->name = 'Exchange';
    $module->save();
}

echo '<h1>Adding Exchange Sync Schema</h1>', PHP_EOL;
echo '<ul>', PHP_EOL;

if (!Vtiger_Utils::CheckTable('calendar_exchange_sync')) {
    echo '<li>Creating `calendar_exchange_sync` table.', PHP_EOL;

    Vtiger_Utils::CreateTable('calendar_exchange_sync',
                              '(
                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `user_id` int(11) unsigned NOT NULL,
                                  `state` LONGTEXT DEFAULT NULL,
                                  `last_sync_time` datetime DEFAULT NULL,
                                  `created_at` datetime DEFAULT NULL,
                                  `updated_at` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id`)
							  )', true);
}

// -----

if (!Vtiger_Utils::CheckTable('calendar_exchange_metadata')) {
    echo '<li>Creating `calendar_exchange_metadata` table.', PHP_EOL;

    Vtiger_Utils::CreateTable('calendar_exchange_metadata',
                              '(
                                  `id` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT "",
                                  `activity_id` int(19) unsigned NOT NULL,
                                  `change_key` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                                  `last_sync_time` datetime DEFAULT NULL,
                                  `parent_activity_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
                                  `userid` int(19) unsigned NOT NULL DEFAULT 0,
                                  `is_survey_appointment` tinyint DEFAULT 0,
                                  UNIQUE KEY `id` (`activity_id`)
							   )', true);
} else {
    echo '<li>Adding columns to calendar_exchange_metadata</li>';
    Vtiger_Utils::AddColumn('calendar_exchange_metadata', 'userid', 'int(19) NOT NULL DEFAULT 0');
    Vtiger_Utils::AddColumn('calendar_exchange_metadata', 'is_survey_appointment', 'tinyint DEFAULT 0');
}

if (!Vtiger_Utils::CheckTable('vtiger_recurrencerel')) {
    echo '<l1>Creating `vtiger_recurrencerel` table</l1>', PHP_EOL;

    Vtiger_Utils::CreateTable('vtiger_recurrencerel',
                              '(
                                   `activityid` INT(19) NOT NULL,
                                   `parentid` INT(19) NOT NULL,
                                   PRIMARY KEY (`activityid`)
                               )', true);
}

//Add exchange_freebusy field
$moduleCalendar = Vtiger_Module::getInstance('Calendar'); //LBL_TASK_INFORMATION
$moduleEvents = Vtiger_Module::getInstance('Events'); //LBL_EVENT_INFORMATION

$blockCalendar = Vtiger_Block::getInstance('LBL_TASK_INFORMATION', $moduleCalendar);
$blockEvents = Vtiger_Block::getInstance('LBL_EVENT_INFORMATION', $moduleEvents);

$field = Vtiger_Field::getInstance('exchange_freebusy', $moduleCalendar);
if ($field) {
    echo "<br /> The exchange_freebusy field already exists in Calendar <br />";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_EXCHANGE_FREEBUSY';
    $field->name = 'exchange_freebusy';
    $field->table = 'vtiger_activity';
    $field->column = 'exchange_freebusy';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';

    $blockCalendar->addField($field);

    $field->setPicklistValues(['Free', 'Tentative', 'Busy', 'NoData']);
}

$field = Vtiger_Field::getInstance('exchange_freebusy', $moduleEvents);
if ($field) {
    echo "<br /> The exchange_freebusy field already exists in Events <br />";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_EXCHANGE_FREEBUSY';
    $field->name = 'exchange_freebusy';
    $field->table = 'vtiger_activity';
    $field->column = 'exchange_freebusy';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';

    $blockEvents->addField($field);
}

//Add exchange credentials to Users module
$users = Vtiger_Module::getInstance('Users');

if (Vtiger_Block::getInstance('LBL_USERS_EXCHANGE', $users)) {
    $exchange = Vtiger_Block::getInstance('LBL_USERS_EXCHANGE', $users);
} else {
    $exchange        = new Vtiger_Block;
    $exchange->label = 'LBL_USERS_EXCHANGE';

    $users->addBlock($exchange);
}

//var_dump($exchange);

// -----

if (Vtiger_Field::getInstance('user_exchange_hostname', $users)) {
    //    dump("HOSTNAME FIELD EXISTS!");
} else {
    $hostname             = new Vtiger_Field;
    $hostname->label      = 'LBL_USERS_EXCHANGE_HOSTNAME';
    $hostname->name       = 'user_exchange_hostname';
    $hostname->table      = 'vtiger_users';
    $hostname->column     = 'exchange_hostname';
    $hostname->columntype = 'VARCHAR(100)';
    $hostname->uitype     = 1;
    $hostname->typeofdata = 'V~O';

    $exchange->addField($hostname);
//    echo "\n";
//    var_dump($hostname);
//    echo "\n";
}

// -----

if (Vtiger_Field::getInstance('user_exchange_username', $users)) {
    //    dump("USERNAME FIELD EXISTS!");
} else {
    $username             = new Vtiger_Field;
    $username->label      = 'LBL_USERS_EXCHANGE_USERNAME';
    $username->name       = 'user_exchange_username';
    $username->table      = 'vtiger_users';
    $username->column     = 'exchange_username';
    $username->columntype = 'VARCHAR(100)';
    $username->uitype     = 1;
    $username->typeofdata = 'V~O';

    $exchange->addField($username);
//    echo "\n";
//    var_dump($username);
//    echo "\n";
}

// -----

if (Vtiger_Field::getInstance('user_exchange_password', $users)) {
    //    dump("PASSWORD FIELD EXISTS!");
} else {
    $password              = new Vtiger_Field;
    $password->label       = 'LBL_USERS_EXCHANGE_PASSWORD';
    $password->name        = 'user_exchange_password';
    $password->table       = 'vtiger_users';
    $password->column      = 'exchange_password';
    $password->columntype  = 'VARCHAR(100)';
    $password->uitype      = 2;
    $password->typeofdata  = 'V~O';

    $exchange->addField($password);
//    echo "\n";
//    var_dump($password);
//    echo "\n";
}

//Add widget
$moduleInstance = Vtiger_Module::getInstance('Calendar');
$moduleInstance->addLink('LISTVIEWSIDEBARWIDGET', 'LBL_CALENDAR_EXCHANGESYNC', 'module=Exchange&view=List&sourcemodule=Calendar');

echo "<br />Populating userid column of calendar_exchange_metadata with values from smownerid column of vtiger_crmentity<br />";
$sql = "SELECT * FROM `calendar_exchange_metadata` JOIN `vtiger_crmentity` ON activity_id=crmid";
$result = $db->query($sql);
while ($row =& $result->fetchRow()) {
    $sql = "UPDATE `calendar_exchange_metadata` SET userid=? WHERE activity_id=?";
    $db->pquery($sql, [$row['smownerid'], $row['activity_id']]);
}

echo '</ul>', PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";