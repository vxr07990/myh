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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/1/2016
 * Time: 3:53 PM
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('TariffServices');
if ($module) {
    $block1 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_INFORMATION', $module);
    if ($block1) {
        $field1 = Vtiger_Field::getInstance('invoiceable', $module);
        $field2 = Vtiger_Field::getInstance('distributable', $module);
        if ($field1 || $field2) {
            echo "<li>The invoiceable or distributable field already exists</li><br>";
        } else {
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_TARIFFSERVICES_INVOICEABLE';
            $field1->name       = 'invoiceable';
            $field1->table      = 'vtiger_tariffservices';  // This is the tablename from your database that the new field will be added to.
            $field1->column     = 'invoiceable';   //  This will be the columnname in your database for the new field.
            $field1->columntype = 'VARCHAR(3)';
            $field1->uitype     = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
            $field1->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
            $block1->addField($field1);

            $field2             = new Vtiger_Field();
            $field2->label      = 'LBL_TARIFFSERVICES_DISTRIBUTABLE';
            $field2->name       = 'distributable';
            $field2->table      = 'vtiger_tariffservices';  // This is the tablename from your database that the new field will be added to.
            $field2->column     = 'distributable';   //  This will be the columnname in your database for the new field.
            $field2->columntype = 'VARCHAR(3)';
            $field2->uitype     = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
            $field2->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
            $block1->addField($field2);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";