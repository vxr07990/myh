<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;

// OT5304 - Update Personnel Assigned to Task block

$module = Vtiger_Module_Model::getInstance('TimeSheets');
if(!$module){
    echo 'Module TimeSheets not present';
    return;
}
$fieldName = 'total_hours'; 
//get total hours field sequence to place new field befor it
$field = Vtiger_Field::getInstance($fieldName, $module);
if(!$field){
    echo "The field $fieldName not exist";
    return;
}else{
    //get the sequence to use in the new field
    $fieldSequence = $field->sequence;
    //update the field so it dont allow negative numbers and set step to 0,1
    $result0 = $adb->pquery("UPDATE vtiger_field SET typeofdata=concat(typeofdata,'~MIN=0~STEP=0.1~10,2') WHERE fieldid = $field->id AND typeofdata NOT LIKE '%~MIN=0~10,2%'");
}

$block = Vtiger_Block_Model::getInstance('LBL_TIMESHEETS_INFORMATION', $module);
if(!$block){
    echo "The block was not found";
    return;
}

$field3 = Vtiger_Field::getInstance('timeoff', $module);
if (!$field3){
    //update sequence of fields to make room for the new field
    $result = $adb->pquery("UPDATE vtiger_field SET sequence = sequence+1 WHERE block = $block->id AND sequence >= $fieldSequence");

    //create field
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TIMEOFF';
    $field3->name = 'timeoff';
    $field3->table = 'vtiger_timesheets';
    $field3->column = 'timeoff';
    $field3->columntype = 'decimal(10,2)';
    $field3->uitype = 7;
    $field3->typeofdata = 'N~O~MIN=0~STEP=0.1~10,2';
    $field3->sequence = $fieldSequence;
    $block->addField($field3);
}

//update vtiger_timesheet table to make total_hours have 2 decimal places
$result2 = $adb->pquery('ALTER TABLE `vtiger_timesheets` CHANGE COLUMN `total_hours` `total_hours` DECIMAL(12,2) NULL DEFAULT NULL AFTER `actual_end_hour`');