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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$newAgentTypePicklist = array(
    'Booking Agent',
    'Origin Agent',
    'Destination Agent',
    'Hauling Agent',
    'SIT Agent',
);

$query = "TRUNCATE `vtiger_agent_type`";
$rs = $adb->pquery($query);
$i = 0;
foreach ($newAgentTypePicklist as $key => $item){


    $key = $key + 1;

    $insertQuery = "INSERT `vtiger_agent_type` (agent_typeid, agent_type, sortorderid, presence)
                    VALUES ($key, '$item', $key, '1')";
    $adb->pquery($insertQuery);
    $i = $key;
}

$query = "TRUNCATE `vtiger_agent_type_seq`";
$adb->pquery($query);

$insertQuery = "INSERT `vtiger_agent_type_seq` (id)
                VALUES ($i)";
$adb->pquery($insertQuery);

echo "<br>Update 'Agent Type' Picklist Values in Participating Agents module<br>";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";