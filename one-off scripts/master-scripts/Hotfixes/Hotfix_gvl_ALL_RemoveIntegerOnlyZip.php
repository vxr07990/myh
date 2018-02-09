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
echo "<br><h1>Starting RemoveIntegerOnlyZip</h1><br>\n";

$db = PearDatabase::getInstance();
$sql = "SELECT fieldid, columnname, tablename, typeofdata FROM `vtiger_field` WHERE columnname LIKE ? AND typeofdata LIKE ?";
$result = $db->pquery($sql, ['%zip%', 'I~%']);
while ($row =& $result->fetchRow()) {
    if ($row['typeofdata'] == 'I~O') { //Redundant, I know but it makes me feel better knowing!
        //Why 25 I have no clue
        $sql = 'ALTER TABLE ? MODIFY `?` VARCHAR(25) DEFAULT NULL';
        $db->pquery($sql, [$row['tablename'], $row['columnname']]);

        $sql = 'UPDATE `vtiger_field` SET `typeofdata` = "V~O" WHERE `fieldid` = ?  LIMIT 1';
        $query = $db->pquery($sql, [$row['fieldid']]);
    }
}

echo "<br><h1>Ending RemoveIntegerOnlyZip</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";