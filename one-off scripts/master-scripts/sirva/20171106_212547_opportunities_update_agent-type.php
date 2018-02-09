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

require_once('vtlib/Vtiger/Module.php');

// TFS32259 -  opportunity filter - no results for booker name or booker city

$module = Vtiger_Module::getInstance('Opportunities');
if ($module) {
    $db = PearDatabase::getInstance();
    $arr = [
        'Booking Agent' => "0",
        'Destination Agent' => "1",
        'Hauling Agent' => "3",
        'Estimating Agent' => "7",
        'Origin Agent' => "5"
    ];

    foreach($arr as $string => $num)
    {
        $sql = "UPDATE vtiger_participatingagents SET agent_type=? WHERE agent_type=?";
        $db->pquery($sql, [$string, $num]);
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";