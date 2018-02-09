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

/*
 * OT16867 - Error seen in mysql fail log for OrdersTask Edit view.
 *
 * Could not find where the insert is done, but brokes User privileges. Description field could not
 * be related to another module as it's a UI Type 19 not 10
 */

$OrdersTask = Vtiger_Module::getInstance('OrdersTask');
$field = Vtiger_Field::getInstance('description', $OrdersTask);



if ($field) {
    Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_fieldmodulerel WHERE fieldid=$field->id");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";