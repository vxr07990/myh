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
$picklistValues = [
                    'Booking Agent',
                    'Destination Agent',
                    'Hauling Agent',
                    'Invoicing Agent',
                    'Origin Agent',
                    'Estimating Agent',
                    'Carrier',
                    'Collecting Agent',
                    'Coordinating Agent',
                    'D/A Coordinating Agent',
                    'Extra Delivery Agent',
                    'Extra Pickup Agent',
                    'O/A Coordinating Agent',
                    'Packing Agent',
                    'Radial Dispatch Agent',
                    'Sales Org',
                    'Split Booking',
                    'Survey Agent',
                    'Unpacking Agent',
                    'Warehousing Agent',
                  ];
$db = PearDatabase::getInstance();
echo "<br>Attempting to modify participating agent types for Graebel<br>";
echo "<br>Blowing out old picklist values now...";
$db->pquery('TRUNCATE TABLE `vtiger_agent_type`', []);
echo "<br>blowout complete, inserting new picklist values...";
for ($i = 0; $i < count($picklistValues); $i++) {
    $db->pquery('INSERT INTO `vtiger_agent_type` (agent_typeid, agent_type, sortorderid, presence) VALUES (?,?,?,1)', [$i + 1, $picklistValues[$i], $i + 1]);
    $db->pquery('UPDATE `vtiger_agent_type_seq` SET id = ?', [$i+1]);
}
echo "done!<br> Graebel agent types hotfix complete.";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";