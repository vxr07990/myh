<?php

if (function_exists("call_ms_function_ver")) {
    $version = 5; // Need to add +1 every time you update that script
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

    $Vtiger_Utils_Log = true;
    require_once 'include/utils/utils.php';
    require_once 'include/utils/CommonUtils.php';

    require_once 'includes/Loader.php';
    vimport('includes.runtime.EntryPoint');
    global $adb;

    //Remove orders_contacts field
    $moduleInstance = Vtiger_Module::getInstance('Orders');
    if($moduleInstance){
        $fieldInstance = Vtiger_Field::getInstance('ordersname', $moduleInstance);
        if ($fieldInstance) {
            if ($fieldInstance->presence != 1) {
                $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
                $adb->pquery($stmt, [1, $fieldInstance->id]);
            }
        }

        $fieldInstance = Vtiger_Field::getInstance('order_name', $moduleInstance);
        if ($fieldInstance) {
            if ($fieldInstance->presence != 1) {
                $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
                $adb->pquery($stmt, [1, $fieldInstance->id]);
            }
        }

        $fieldInstance = Vtiger_Field::getInstance('orders_contacts', $moduleInstance);
        if ($fieldInstance) {
            if ($fieldInstance->presence != 0) {
                $stmt = 'UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=? LIMIT 1';
                $adb->pquery($stmt, [0, $fieldInstance->id]);
            }
        }
    }

echo "<br>DONE!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";