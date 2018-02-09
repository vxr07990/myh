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

$db = PearDatabase::getInstance();
$moduleName = 'Surveys';
$module = Vtiger_Module_Model::getInstance($moduleName);

$blockName = 'LBL_SURVEYS_INFORMATION';
$block = Vtiger_Block_Model::getInstance($blockName, $module);
if(!$block) {
    print "Unable to find $blockName in $moduleName. Skipping field re-order.<br />\n";
    return;
}

$count = 1;
$newFieldOrder = [
    'survey_date',
    'assigned_user_id',
    'survey_status',
    'comm_res',
    'survey_time',
    'survey_end_time',
    'survey_type',
    'agentid',
    'address1',
    'address2',
    'city',
    'state',
    'zip',
    'country',
    'phone1',
    'phone2',
    'address_desc',
    'potential_id',
    'contact_id',
    'order_id',
    'account_id',
    'survey_notes'
];
foreach ($newFieldOrder as $val) {
    $field = Vtiger_Field::getInstance($val, $module);
    if ($field) {
        $params = [$block->id, $count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET block = ?, sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        $count++;
    } else {
        print $val.' Field doesn\'t exist. Skipping. <br />\n';
    }
}
