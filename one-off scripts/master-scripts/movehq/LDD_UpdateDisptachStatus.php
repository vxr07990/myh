<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();

$db->pquery('UPDATE vtiger_orders_otherstatus  SET orders_otherstatus=?  WHERE orders_otherstatus=?',['Unplanned','Non-Planned']);
$db->pquery('UPDATE vtiger_orders  SET orders_otherstatus=?  WHERE orders_otherstatus=?',['Unplanned','Non-Planned']);

//Move the field to dispatch block and make it available. Otherwise we can save it with vtwsrevise

$block = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', Vtiger_Module::getInstance('Orders'));
$db->pquery('UPDATE vtiger_field  SET presence=?, block=?  WHERE fieldname=?',['2',$block->id, 'orders_otherstatus']);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";