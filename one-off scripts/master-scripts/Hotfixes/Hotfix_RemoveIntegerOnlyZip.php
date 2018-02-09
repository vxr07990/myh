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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting </h1><br>\n";
echo 'Starting RemoveIntegerOnlyZip';
$db = PearDatabase::getInstance();
$leads = Vtiger_Module::getInstance('Leads');
$block = Vtiger_Block::getInstance('LBL_LEADS_ADDRESSINFORMATION', $leads);
if ($block) {
    $field1 = Vtiger_Field::getInstance('origin_zip', $leads);
    if ($field1) {
        $sql = 'ALTER TABLE vtiger_leadscf MODIFY `origin_zip` VARCHAR(25) DEFAULT NULL';
        $db->pquery($sql, []);
        $sql = 'UPDATE `vtiger_field` SET `typeofdata` = "V~O" WHERE `fieldid` = ? LIMIT 1';
        $query = $db->pquery($sql, [$field1->id]);
    }

    $field2 = Vtiger_Field::getInstance('destination_zip', $leads);
    if ($field2) {
        $sql = 'ALTER TABLE vtiger_leadscf MODIFY `destination_zip` VARCHAR(25) DEFAULT NULL';
        $db->pquery($sql, []);
        $sql = 'UPDATE `vtiger_field` SET `typeofdata` = "V~O" WHERE `fieldid` = ?  LIMIT 1';
        $query = $db->pquery($sql, [$field2->id]);
    }
}
echo '<br>Ending RemoveIntegerOnlyZip';
echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";