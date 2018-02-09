<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$newPresence = 0;
$newQuickcreate = 0;
$moduleInstance = Vtiger_Module::getInstance('Surveys');
if ($moduleInstance) {
    if ($field3 = Vtiger_Field::getInstance('survey_notes',$moduleInstance)) {
        if ($field3->presence != $newPresence) {
            $db = &PearDatabase::getInstance();
            $db->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?", [$newPresence, $field3->id]);
        }
        if ($field3->quickcreate != $newQuickcreate) {
            $db = &PearDatabase::getInstance();
            $db->pquery("UPDATE `vtiger_field` SET `quickcreate`=? WHERE `fieldid`=?", [$newQuickcreate, $field3->id]);
        }
    }
}
print "END: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";