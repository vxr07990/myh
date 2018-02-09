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

$moduleInstance = Vtiger_Module::getInstance('Orders');
if($moduleInstance){
    $field1 = Vtiger_Field::getInstance('orders_actualweights', $moduleInstance);
    if (!$field1){
        $blockInstance = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', $moduleInstance);
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_ACTUALWEIGHT';
        $field->name = 'orders_actualweight';
        $field->table = 'vtiger_orders';
        $field->column ='orders_actualweight';
        $field->columntype = 'INT(10)';
        $field->uitype = 7;
        $field->typeofdata = 'I~O';
        
        $blockInstance->addField($field);

    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";