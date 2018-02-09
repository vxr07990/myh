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


//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');
//include_once('modules/ModComments/ModComments.php');
//include_once 'includes/main/WebUI.php';
//include_once 'include/Webservices/Create.php';
//include_once 'modules/Users/Users.php';
//include_once 'modules/Settings/Picklist/models/Module.php';
//include_once 'modules/Settings/Picklist/models/Field.php';

/* $moduleName = 'Opportunities';
$picklistFieldName = 'sirva_stop_type';

$module = Vtiger_Module::getInstance($moduleName);
$sirvaStopType = Vtiger_Field::getInstance('sirva_stop_type', $module);
$extraStops = Vtiger_Block::getInstance('LBL_OPPORTUNITY_EXTRASTOPS', $oppsModule);

if(!$extraStops){
    echo "<br><h1 style='color:red;'>LBL_OPPORTUNITY_EXTRASTOPS block doesn't exist.</h1><br>";
} else{
    echo "<br>LBL_OPPORTUNITY_EXTRASTOPS exists. Begin modifications to add sirva stop types.<br>";
}*/

//todo: add column to vtiger_extrastops
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_extrastops` ADD sirva_stop_type VARCHAR(50)');

//todo: change sequence to be not mandatory
//todo: conditionalize tpls and give the logic the once over to figure out if a lack of sequence field is going to screw anything up
;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";