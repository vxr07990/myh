<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$db = PearDatabase::getInstance();


$moduleInstance = Vtiger_Module::getInstance('WFOrders');
$block = Vtiger_Block::getInstance('LBL_WFORDER_INFORMATION', $moduleInstance);


$field3 = Vtiger_Field::getInstance('business_line', $moduleInstance);
if(!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_BUSINESS_LINE';
    $field3->name = 'business_line';
    $field3->table = 'vtiger_wforders';
    $field3->column ='business_line';
    $field3->columntype = 'VARCHAR(150)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~M';

    $block->addField($field3);

}


$field2 = Vtiger_Field::getInstance('commodities', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_COMMODITIES';
    $field2->name = 'commodities';
    $field2->table = 'vtiger_wforders';
    $field2->column = 'commodities';
    $field2->columntype = 'VARCHAR(200)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~M';

    $block->addField($field2);
}





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
