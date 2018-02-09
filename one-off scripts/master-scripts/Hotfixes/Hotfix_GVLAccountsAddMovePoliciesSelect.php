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

echo "<br>start add account move policies select<br>";

$db = PearDatabase::getInstance();

$sql = "UPDATE `vtiger_relatedlists` SET actions = 'ADD,SELECT' WHERE tabid = 6 AND name = 'get_dependents_list' AND label = 'Move Policies'";

$db->pquery($sql, []);

echo "<br>end add account move policies select";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";