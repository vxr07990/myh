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

// OT19543 Local Dispatch - Update default filter - remove incorrect filters as options

$ordersTaskModule = Vtiger_Module::getInstance('OrdersTask');
if($ordersTaskModule){
          $db->pquery("UPDATE vtiger_customview SET view='NewLocalDispatch', status=3 WHERE viewname='Local Dispatch Day Page' AND userid=1");
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";