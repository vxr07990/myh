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



$module = Vtiger_Module::getInstance('Orders');

if ($module) {
    $block = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $module);

    if ($block) {
        $field = Vtiger_Field::getInstance('is_locked', $module);
        if ($field) {
            echo "The is_locked field already exists<br>\n";
        } else {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_ORDERS_ISLOCKED';
            $field->name       = 'is_locked';
            $field->table      = 'vtiger_orders';
            $field->column     = 'is_locked';
            $field->columntype = 'VARCHAR(3)';
            $field->uitype     = 56;
            $field->typeofdata = 'C~O';
            $block->addField($field);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";