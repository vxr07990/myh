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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$ActualsInstance = Vtiger_Module::getInstance('Actuals');
if ($ActualsInstance){
    $field = Vtiger_Field::getInstance('escrow_item',$ActualsInstance);
    if ($field){
        $field->delete();
    }
}


$EscrowsInstance = Vtiger_Module::getInstance('Escrows');
if ($EscrowsInstance){
    $block = Vtiger_Block::getInstance('LBL_DETAIL',$EscrowsInstance);
    if ($block){
        $fieldName = 'escrows_to_actual';
        $fieldLabel = 'LBL_'.strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName,$EscrowsInstance);
        if (!$field){
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_escrows';
            $field->column = $fieldName;
            $field->columntype = 'INT(10)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';

            $block->addField($field);
            $field->setRelatedModules(array('Actuals'));
            echo "<li>The '$fieldName' field created done</li><br>";
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";