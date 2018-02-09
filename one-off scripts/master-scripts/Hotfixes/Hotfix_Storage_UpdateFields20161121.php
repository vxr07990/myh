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

$moduleInstance = Vtiger_Module::getInstance('Storage');
if ($moduleInstance) {

    $block = Vtiger_Block::getInstance('LBL_STORAGE_INFORMATION', $moduleInstance);

    if ($block) {

        $field1 = Vtiger_Field::getInstance('storage_status', $moduleInstance);
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_STORAGE_STATUS';
            $field1->name = 'storage_status';
            $field1->table = 'vtiger_storage';
            $field1->column = $field1->name;
            $field1->columntype = 'varchar(255)';
            $field1->uitype = 15;
            $field1->typeofdata = 'V~O';
            $field1->defaultvalue = 'Active';
            $block->addField($field1);
            $field1->setPicklistValues(['Active', 'Cancelled']);
        }

        $field2 = Vtiger_Field::getInstance('storage_datetime_cancelled', $moduleInstance);
        if (!$field2) {
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_STORAGE_DATETIME_CANCELLED';
            $field2->name = 'storage_datetime_cancelled';
            $field2->table = 'vtiger_storage';
            $field2->column = $field2->name;
            $field2->columntype = 'DATETIME';
            $field2->uitype = 1;
            $field2->typeofdata = 'DT~O';
            $block->addField($field2);
        }

        $field3 = Vtiger_Field::getInstance('storage_cancelled_user_id', $moduleInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->label = 'LBL_STORAGE_CANCELLED_USER_ID';
            $field3->name = 'storage_cancelled_user_id';
            $field3->table = 'vtiger_storage';
            $field3->column = $field3->name;
            $field3->columntype = 'INT(10)';
            $field3->uitype = 53;
            $field3->typeofdata = 'I~O';
            $block->addField($field3);
        }
    }
}
echo 'OK<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";