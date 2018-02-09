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



Vtiger_Utils::ExecuteQuery("ALTER TABLE  `vtiger_quotes` CHANGE  `ori_sit2_container_or_warehouse`  `ori_sit2_container_or_warehouse` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE  `vtiger_quotes` CHANGE  `des_sit2_container_or_warehouse`  `des_sit2_container_or_warehouse` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 16 WHERE `fieldname` LIKE 'des_sit2_container_or_warehouse'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `uitype` = 16 WHERE `fieldname` LIKE 'ori_sit2_container_or_warehouse'");

if (!Vtiger_utils::CheckTable('vtiger_ori_sit2_container_or_warehouse')) {
    $moduleEstimates = Vtiger_Module::getInstance('Estimates');
    $moduleQuotes = Vtiger_Module::getInstance('Quotes');

    $field = Vtiger_Field::getInstance('ori_sit2_container_or_warehouse', $moduleEstimates);
    $field->setPicklistValues(array('Container', 'Warehouse', 'In Van'));

    $field = Vtiger_Field::getInstance('des_sit2_container_or_warehouse', $moduleEstimates);
    $field->setPicklistValues(array('Container', 'Warehouse', 'In Van'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";