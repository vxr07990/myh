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



echo "<br>Adding overflow link<br>";

$db = PearDatabase::getInstance();

$linkExists = $db->pquery("SELECT linkid FROM `vtiger_links` WHERE linklabel='LBL_ORDERS_CREATE_OVERFLOW'", [])->fetchRow()['linkid'];

if ($linkExists) {
    echo "overflow link exists, no action taken";
} else {
    $moduleInstance = Vtiger_Module::getInstance('Orders');
    $moduleInstance->addLink('DETAILVIEWBASIC', 'LBL_ORDERS_CREATE_OVERFLOW', 'javascript:Orders_Detail_Js.createWorkflow();');
    echo 'overflow link created';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";