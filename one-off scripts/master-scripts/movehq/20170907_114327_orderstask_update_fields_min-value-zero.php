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

// OT5139 - Don't allow for negatives in the pricing module

$fields = [
    //operative task information block
    'estimated_hours',
    //equipment block
    'equipmentqty'
];

$module = Vtiger_Module::getInstance('OrdersTask');
if(!$module){
    echo 'Module '.$module->name.' not present.';
    return;
}
$db = PearDatabase::getInstance();

$db->pquery("update `vtiger_field` set typeofdata=CONCAT(typeofdata,'~MIN=0') where tabid = ? AND fieldname IN (". generateQuestionMarks($fields).") AND typeofdata NOT LIKE '%~MIN=0%'",
        array($module->id,$fields));

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";