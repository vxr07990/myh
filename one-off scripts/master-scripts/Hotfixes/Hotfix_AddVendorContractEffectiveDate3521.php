<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/10/2017
 * Time: 2:46 PM
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

$module = Vtiger_Module::getInstance('Vendors');

if($module)
{
    $block = Vtiger_Block::getInstance('LBL_CONTRACTORS_INFORMATION', $module);
    if($block)
    {
        $field = Vtiger_Field::getInstance('contract_effective_date', $module);
        if ($field) {
            echo "The contract_effective_date field already exists<br>\n";
        } else {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_VENDORS_CONTRACT_EFFECTIVE_DATE';
            $field->name       = 'contract_effective_date';
            $field->table      = 'vtiger_vendor';
            $field->column     = 'contract_effective_date';
            $field->columntype = 'DATE';
            $field->uitype     = 5;
            $field->typeofdata = 'D~O';
            $block->addField($field);
        }
    }
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";