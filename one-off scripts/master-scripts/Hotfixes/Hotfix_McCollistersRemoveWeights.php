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



$ordModule = Vtiger_Module::getInstance('Orders');
$weightBlock = Vtiger_Block::getInstance('LBL_ORDERS_WEIGHTS', $ordModule);

if ($weightBlock) {
    echo "Weight block exists in Orders - disabling fields<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE block=".$weightBlock->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";