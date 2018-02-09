
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


//OT 16040 - Changing uitype of zip fields that are currently set to 7 (should be 1). Will correct dropping of leading 0s. Also makes sure that field type is set to VARCHAR(25)
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting ChangeZipUIType</h1><br>\n";

$db = PearDatabase::getInstance();
$sql = "SELECT fieldid, columnname, tablename, uitype FROM `vtiger_field` WHERE columnname LIKE ? AND uitype = ?";
$result = $db->pquery($sql, ['%zip%', 7]);
while ($row =& $result->fetchRow()) {
    if ($row['uitype'] = 7) { //Redundant, I know but it makes me feel better knowing!
        $sql = 'UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldid` = ?  LIMIT 1';
        $query = $db->pquery($sql, [$row['fieldid']]);
    }
}

echo "<br><h1>Ending ChangeZipUIType</h1><br>\n";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";