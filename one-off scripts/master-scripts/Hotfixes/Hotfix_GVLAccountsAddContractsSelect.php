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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>start add account contracts select<br>";

$db = PearDatabase::getInstance();

$sql = "UPDATE `vtiger_relatedlists` SET actions = 'ADD,SELECT' WHERE tabid = 6 AND name = 'get_related_list' AND label = 'Contracts'";

$db->pquery($sql, []);

echo "<br>end add account contracts select";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";