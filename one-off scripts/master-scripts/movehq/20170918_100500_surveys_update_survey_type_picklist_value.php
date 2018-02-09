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

//OT4940 Change dropdown option in Survey Type field in Surveys Module

$db = PearDatabase::getInstance();

$db->pquery("UPDATE `vtiger_survey_type` SET survey_type = 'LiveSurvey' WHERE survey_type = 'Virtual'", array());

echo "Picklist value updated!";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";