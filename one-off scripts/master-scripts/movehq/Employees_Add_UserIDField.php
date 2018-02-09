<?php
if (function_exists("call_ms_function_ver")) {
	$version = 3;
	if (call_ms_function_ver(__FILE__, $version)) {
		//already ran
		print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
		return;
	}
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'Move HQ User';

$employeesInstance = Vtiger_Module::getInstance('Employees');
$employeesBlock = Vtiger_Block::getInstance($blockName, $employeesInstance);
if(!$employeesBlock){
	echo "Couldn't find block: $blockName. Failed.<br>\n";
	return;
}

$db = PearDatabase::getInstance();

//This was not exactly right:
//$stmt = 'update vtiger_field set presence=1 where tablename=? and fieldname<>?';
//$db->pquery($stmt, ['vtiger_employeescf', 'move_hq_user']);
$fieldsToHide = [
    'user_name',
    'is_admin',
    'user_password',
    'confirm_password',
    'lead_view',
    'end_hour',
    'is_owner',
    'agent_ids',
    'activity_view',
    'hour_format',
    'start_hour',
    'date_format',
    'time_zone',
    'reminder_interval',
    'dayoftheweek',
    'callduration',
    'othereventduration',
    'calendarsharedtype',
    'defaulteventstatus',
    'defaultactivitytype',
    'hidecompletedevents',
    'phone_work',
    'department',
    'reports_to_id',
    'phone_other',
    'email2',
    'phone_fax',
    'secondaryemail',
    'signature',
    'description',
    'internal_mailer',
    'theme',
    'language',
    'phone_crm_extension',
    'default_record_view',
    'leftpanelhide',
    'rowheight',
    'date_modified',
    'accesskey',
    'push_notification_token',
    'dbx_token',
    'oi_enabled',
    'dbx_userid',
    'oi_push_notification_token',
    'vanline',
    'custom_reports_pw',
    'user_alert_show_level',
    'tokbox_permitted',
    'currency_id',
    'currency_grouping_pattern',
    'currency_decimal_separator',
    'currency_grouping_separator',
    'currency_symbol_placement',
    'no_of_currency_decimals',
    'truncate_trailing_zeros',
    'user_smtp_server',
    'user_smtp_username',
    'user_smtp_password',
    'user_smtp_fromemail',
    'user_smtp_authentication',
    'user_exchange_hostname',
    'user_exchange_username',
    'user_exchange_password',
    'roleid',
];

foreach ($fieldsToHide as $fieldName) {
    $field0 = Vtiger_Field::getInstance($fieldName, $employeesInstance);
    if ($field0) {
        if ($field0->presence != 1) {
            $stmt = 'update vtiger_field set presence=? where fieldid=? LIMIT 1';
            $db->pquery($stmt, [1, $field0->id]);
        }
    }
}

$fieldName = 'userid';
$field0 = Vtiger_Field::getInstance($fieldName, $employeesInstance);
if ($field0) {
	echo "The $fieldName field already exists<br>\n";
} else {
	$field0             = new Vtiger_Field();
	$field0->label      = 'LBL_' . strtoupper($fieldName);
	$field0->name       = $fieldName;
	$field0->table      = 'vtiger_employees';
	$field0->column     = $fieldName;
	$field0->columntype = 'int(11), add index '. $fieldName;
	$field0->uitype     = 53;
	$field0->typeofdata = 'V~O';
	$employeesBlock->addField($field0);
	echo "The $fieldName field added successfully<br>\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

