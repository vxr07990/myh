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



 //$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');



// Table fieldmodulerel was missing a row that was causing contacts module to break
//HARDCODED = BAAAAAAD
//Vtiger_Utils::ExecuteQuery("INSERT INTO`vtiger_fieldmodulerel` (fieldid,module,relmodule,status,sequence) VALUES (786,'Contacts','Vanlines',NULL,NULL)");

$module = Vtiger_Module::getInstance('Contacts');
$field = Vtiger_Field::getInstance('vanlines', $module);

$db = PearDatabase::getInstance();
$sql = "SELECT * FROM `vtiger_fieldmodulerel` WHERE fieldid=?";
$result = $db->pquery($sql, [$field->id]);
$row = $result->fetchRow();
if ($row == null) {
    //Only insert if row does not already exist
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_fieldmodulerel` (fieldid, module, relmodule, status, sequence) VALUES (".$field->id.", 'Contacts', 'Vanlines', NULL, NULL)");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";