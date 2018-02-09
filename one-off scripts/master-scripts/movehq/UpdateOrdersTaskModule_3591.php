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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
if ($moduleInstance)
{
    $blockInstance = Vtiger_Block::getInstance('LBL_DISPATCH_UPDATES', $moduleInstance);
    if ($blockInstance){

        $field1 = Vtiger_Field::getInstance('actual_of_crew',$moduleInstance);
        if($field1) {
            echo "<h3>The actual_of_crew Field already exists</h3><br> \n";
        }else{
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_ACTUAL_OF_CREW';
            $field1->name = 'actual_of_crew';
            $field1->table = 'vtiger_orderstask';
            $field1->column = 'actual_of_crew';
            $field1->columntype = 'INT(3)';
            $field1->uitype = 7;
            $field1->typeofdata = 'I~O';
            $blockInstance->addField($field1);
        }

        $field2 = Vtiger_Field::getInstance('actual_of_vehicles',$moduleInstance);
        if($field2) {
            echo "<h3>The actual_of_vehicles Field already exists</h3><br> \n";
        }else{
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_ACTUAL_OF_VEHICLES';
            $field2->name = 'actual_of_vehicles';
            $field2->table = 'vtiger_orderstask';
            $field2->column = 'actual_of_vehicles';
            $field2->columntype = 'INT(3)';
            $field2->uitype = 7;
            $field2->typeofdata = 'I~O';
            $blockInstance->addField($field2);
        }
    }

    //remove Actual Date Field
    $field3 = Vtiger_Field::getInstance('disp_actualdate',$moduleInstance);
    if ($field3)
    {
        $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field3->id));
    }

    //remove Service Provider Field
    $field4 = Vtiger_Field::getInstance('assigned_vendor',$moduleInstance);
    if ($field4)
    {
        $adb->pquery("UPDATE `vtiger_field` SET `presence`=? WHERE `fieldid`=?",array('1',$field4->id));
    }

    //update sequence
    $Fields = array(
        1 => 'dispatch_status',
        2 => 'disp_assigneddate',
        3 => 'disp_assignedstart',
        4 => 'disp_actualend',
        5 => 'actual_of_crew',
        6 => 'assigned_employee',
        7 => 'actual_of_vehicles',
        8 => 'assigned_vehicles',
        9 => 'disp_actualhours',
        10 => 'check_call'
    );
    foreach ($Fields as $k => $val) {
        $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleInstance->id, $blockInstance->id));
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";