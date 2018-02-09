<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/20/2017
 * Time: 1:15 PM
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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('DriverQualification');
if ($module) {
    $block1 = Vtiger_Block::getInstance('LBL_DRIVERQUALIFICATION_INFORMATION', $module);
    if ($block1) {
        $field1 = Vtiger_Field::getInstance('qualification_radius', $module);
        if ($field1) {
            echo "<li>The qualification_radius field already exists</li><br>";
        } else {
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_QUALIFICATION_RADIUS';
            $field1->name       = 'qualification_radius';
            $field1->table      = 'vtiger_driverqualification';  // This is the tablename from your database that the new field will be added to.
            $field1->column     = 'qualification_radius';   //  This will be the columnname in your database for the new field.
            $field1->columntype = 'VARCHAR(100)';
            $field1->uitype     = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
            $field1->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
            $field1->setPicklistValues(['150 Miles', '300 Miles', '500 Miles', '750 Miles', 'Unlimited']);
            $block1->addField($field1);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";