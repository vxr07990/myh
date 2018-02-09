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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "<br> begin Set guestModule MoveRoles in Orders";

$ordersInstance = Vtiger_Module::getInstance('Orders');
$ordersInstance->setGuestBlocks('MoveRoles', ['LBL_MOVEROLES_INFORMATION']);
$ordersInstance->unsetrelatedList(Vtiger_Module::getInstance('MoveRoles'), 'MoveRoles', 'get_dependents_list');

echo "<br> end Set guestModule MoveRoles in Orders";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";