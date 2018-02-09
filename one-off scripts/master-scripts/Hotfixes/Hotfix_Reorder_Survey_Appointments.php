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

//Updating Survey Appointments
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('Surveys');

$block = Vtiger_Block::getInstance('LBL_SURVEYS_INFORMATION', $moduleInstance);
$blockid = $block->id;

$fieldOrder = [
  "LBL_SURVEYS_NO"                        => 1,
  "LBL_SURVEYS_DATE"                      => 2,
  "LBL_SURVEYS_SURVEYOR"                  => 3,
  "LBL_SURVEYS_SURVEYTIME"                => 4,
  "LBL_SURVEYS_SURVEYENDTIME"             => 5,
  "LBL_SURVEYS_ACCOUNTID"                 => 6,
  "LBL_SURVEYS_CONTACTID"                 => 7,
  "LBL_SURVEYS_POTENTIALID"               => 8,
  "LBL_SURVEYS_STATUS"                    => 9,
  "LBL_SURVEYS_ORDERS"                    => 10,
  "LBL_SURVEYS_ADDRESS1"                  => 11,
  "LBL_SURVEYS_ADDRESS2"                  => 12,
  "LBL_SURVEYS_CITY"                      => 13,
  "LBL_SURVEYS_STATE"                     => 14,
  "LBL_SURVEYS_ZIP"                       => 15,
  "LBL_SURVEYS_COUNTRY"                   => 16,
  "LBL_SURVEYS_PHONE1"                    => 17,
  "LBL_SURVEYS_PHONE2"                    => 18,
  "LBL_SURVEYS_ADDRESSDESCRIPTION"        => 19,
  "LBL_SURVEYS_COMMERCIALORRESIDENTIAL"   => 20,
  "LBL_SURVEYS_NOTES"                     => 21,
  "Record ID"                             => 22
];

// Cycle through the above array
foreach($fieldOrder as $fieldLabel=>$seq) {
  $result = $db->pquery("UPDATE `vtiger_field` SET sequence = ? WHERE block = ? AND fieldlabel = ?", array($seq,$blockid,$fieldLabel));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";