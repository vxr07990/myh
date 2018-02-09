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
 * Date: 9/21/2016
 * Time: 9:50 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Actuals');
if ($module) {
    $block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
    if ($block1) {
        $field1 = Vtiger_Field::getInstance('delivery_date', $module);
        if ($field1) {
            echo "<li>The delivery_date field already exists</li><br>";
        } else {
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_QUOTES_DELIVERYDATE';
            $field1->name       = 'delivery_date';
            $field1->table      = 'vtiger_quotes';  // This is the tablename from your database that the new field will be added to.
            $field1->column     = 'delivery_date';   //  This will be the columnname in your database for the new field.
            $field1->columntype = 'DATE';
            $field1->uitype     = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
            $field1->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
            $block1->addField($field1);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";