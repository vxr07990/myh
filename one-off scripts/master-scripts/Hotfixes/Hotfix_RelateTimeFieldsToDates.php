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


if(!$db) {
    $db = PearDatabase::getInstance();
}

$fieldsToAdjust = [
    'Calendar' => [
        'time_start' => 'T~O~REL~date_start',
        'time_end'   => 'T~O~REL~due_date'
    ],
    'Emails' => [
        'time_start' => 'T~O~REL~date_start'
    ],
    'Events' => [
        'time_start' => 'T~M~REL~date_start',
        'time_end'   => 'T~M~REL~due_date'
    ],
    'Opportunities' => [
        'survey_time' => 'T~O~REL~survey_date',
    ],
    'Surveys' => [
        'survey_time' => 'T~M~REL~survey_date',
        'survey_end_time' => 'T~M~REL~survey_date'
    ],
    'TimeOff' => [
        'timeoff_hourstart' => 'T~O~REL~timeoff_date',
        'timeoff_hoursend'  => 'T~O~REL~timeoff_date'
    ],
    'Orders' => [
        'orders_surveyt' => 'T~O~REL~orders_surveyd'
    ],
    'Accidents' => [
        'accidents_time' => 'T~O~REL~accidents_date'
    ],
    'TimeSheets' => [
        'actual_start_hour' => 'T~O~REL~actual_start_date',
        'actual_end_hour' => 'T~O~REL~actual_start_date'
    ],
    'LocationHistory' => [
        'locationhistory_time' => 'T~O~REL~locationhistory_date'
    ]
];

foreach($fieldsToAdjust as $moduleName=>$fieldArr) {
    $module = Vtiger_Module::getInstance($moduleName);
    if($module) {
        foreach ($fieldArr as $fieldName => $typeofdata) {
            $field = Vtiger_Field::getInstance($fieldName, $module);
            if ($field) {
                $sql = "UPDATE `vtiger_field` SET typeofdata=? WHERE fieldid=?";
                $db->pquery($sql, [$typeofdata, $field->id]);
            }
        }
    }
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";