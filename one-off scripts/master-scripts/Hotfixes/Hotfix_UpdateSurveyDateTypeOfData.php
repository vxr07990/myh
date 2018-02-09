<?php
if (function_exists("call_ms_function_ver")) {
    $version = '1';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

if(!$db) {
    $db = PearDatabase::getInstance();
}

$fieldsToAdjust = [
    'Opportunities' => [
        'survey_date' => 'DT~O~REL~survey_time'
    ],
    'Surveys' => [
        'survey_date' => 'DT~M~REL~survey_time'
    ]
];

foreach($fieldsToAdjust as $moduleName=>$fieldArr) {
    $module = Vtiger_Module::getInstance($moduleName);
    foreach($fieldArr as $fieldName=>$typeofdata) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if($field) {
            $sql = "UPDATE `vtiger_field` SET typeofdata=? WHERE fieldid=?";
            $db->pquery($sql, [$typeofdata, $field->id]);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";