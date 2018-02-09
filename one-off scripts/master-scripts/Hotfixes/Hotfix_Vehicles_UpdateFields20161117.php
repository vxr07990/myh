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


$moduleInstance = Vtiger_Module::getInstance('Vehicles');
if ($moduleInstance) {
    $block = Vtiger_Block::getInstance('LBL_VEHICLES_LICENSE', $moduleInstance);
    if ($block) {
        $field1 = Vtiger_Field::getInstance('vehicle_2290_exp_date', $moduleInstance);
        if (!$field1) {
            print "Creating new field: vehicle_2290_exp_date.<br />";
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_VEHICLES_2290_EXP_DATE';
            $field1->name = 'vehicle_2290_exp_date';
            $field1->table = 'vtiger_vehicles';
            $field1->column = $field1->name;
            $field1->columntype = 'date';
            $field1->uitype = 5;
            $field1->typeofdata = 'D~O';

            $block->addField($field1);
        }
    }
}
echo 'OK<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";