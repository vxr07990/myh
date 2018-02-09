<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/15/2016
 * Time: 11:11 AM
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

$db = &PearDatabase::getInstance();
$moduleNames = ['Estimates', 'Actuals'];


foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        print "Module $moduleName not found. Skipping.<br/>\n";
        continue;
    }
    $block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
    if (!$block) {
        print "Block not found for $moduleName. Skipping. <br/>\n";
        continue;
    }

    $field1 = Vtiger_Field::getInstance('total_ready_to_invoice', $module);
    if ($field1) {
        echo "The total_ready_to_invoice field already exists<br>\n";
    } else {
        $field1             = new Vtiger_Field();
        $field1->label      = 'LBL_TOTAL_READY_TO_INVOICE';
        $field1->name       = 'total_ready_to_invoice';
        $field1->table      = 'vtiger_quotes';
        $field1->column     = 'total_ready_to_invoice';
        $field1->columntype = 'decimal(22,2)';
        $field1->presence   = 2;
        $field1->displaytype = 3;
        $field1->uitype     = 71;
        $field1->typeofdata = 'N~O';
        $field1->summaryfield = 0;
        $block->addField($field1);
        echo "The $field1->name field added to $moduleName<br>\n";
    }
    $field2 = Vtiger_Field::getInstance('total_ready_to_dist', $module);
    if ($field2) {
        echo "The total_ready_to_dist field already exists<br>\n";
    } else {
        $field2             = new Vtiger_Field();
        $field2->label      = 'LBL_TOTAL_READY_TO_DIST';
        $field2->name       = 'total_ready_to_dist';
        $field2->table      = 'vtiger_quotes';
        $field2->column     = 'total_ready_to_dist';
        $field2->columntype = 'decimal(22,2)';
        $field2->presence   = 2;
        $field2->uitype     = 71;
        $field2->displaytype = 3;
        $field2->typeofdata = 'N~O';
        $field2->summaryfield = 1;
        $block->addField($field2);
        echo "The $field2->name field added to $moduleName<br>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";