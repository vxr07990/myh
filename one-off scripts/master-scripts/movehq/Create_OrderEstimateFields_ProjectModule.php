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

$Vtiger_Utils_Log = true;
global $adb;

$ProjectModuleInstance = Vtiger_Module::getInstance('Project');


if ($ProjectModuleInstance){
    $blockInstance = Vtiger_Block::getInstance('LBL_PROJECT_INFORMATION',$ProjectModuleInstance);
    if ($blockInstance){
        $fieldName = "order_item";
        $fieldLabel= "LBL_PROJECT_".strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $ProjectModuleInstance);
        if ($field) {
            echo "<br>field '$fieldName' already exists <br>";
        } else {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_project';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';

            $blockInstance->addField($field);

            $field->setRelatedModules(array('Orders'));
        }


        $fieldName = "escrow_item";
        $fieldLabel= "LBL_PROJECT_".strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $ProjectModuleInstance);
        if ($field) {
            echo "<br>field '$fieldName' already exists <br>";
        } else {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_project';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';

            $blockInstance->addField($field);

            $field->setRelatedModules(array('Escrows'));
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";