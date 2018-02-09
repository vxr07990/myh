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

global $adb;

$moduleName = 'LocationTypes';
$blockName = 'LBL_LOCATIONTYPES_DETAILS';
$moduleLocationTypesInstance = Vtiger_Module::getInstance($moduleName);

$filterAll= Vtiger_Filter::getInstance('All',$moduleLocationTypesInstance);
if($filterAll) $filterAll->delete();
$filterAll = new Vtiger_Filter();
$filterAll->name = 'All';
$filterAll->isdefault = true;
$moduleLocationTypesInstance->addFilter($filterAll);


if($moduleLocationTypesInstance){
    $blockInstance = Vtiger_Block::getInstance($blockName,$moduleLocationTypesInstance);
    if ($blockInstance){
        $fieldName = "locationtypes_id";
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationTypesInstance);
        if ($field){
            $field->delete();
            $sql = "ALTER TABLE `vtiger_locationtypes` DROP COLUMN `locationtypes_id`";
            $adb->pquery($sql);
        }

        // Create new field module
        $fieldName = "location_types";
        $fieldLabel= "LBL_".strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationTypesInstance);
        if (!$field){
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locationtypes';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 1;
            $field->typeofdata = 'V~M';
            $field->sequence = 1;
            $blockInstance->addField($field);
            $moduleLocationTypesInstance->setEntityIdentifier($field);
            $filterAll->addField($field);
            echo "<br>create '$fieldName' in $moduleName Module<br>";
            $filterAll->addField($field,1);
        }else{
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
            $moduleLocationTypesInstance->setEntityIdentifier($field);
        }

        $fieldName = "location_prefix";
        $fieldLabel= "LBL_".strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationTypesInstance);
        if (!$field){
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locationtypes';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(1)';
            $field->uitype = 1;
            $field->typeofdata = 'V~M';
            $field->sequence = 2;
            $blockInstance->addField($field);

            $filterAll->addField($field,2);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        }else{
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_base";
        $fieldLabel= "LBL_".strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationTypesInstance);
        if (!$field){
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locationtypes';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 3;
            $blockInstance->addField($field);

            $filterAll->addField($field,3);
            echo "<br>create '$fieldName' in $moduleName Module<br>";
        }else{
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_container";
        $fieldLabel= "LBL_".strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationTypesInstance);
        if (!$field){
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locationtypes';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 4;
            $blockInstance->addField($field);


            $filterAll->addField($field,4);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        }else{
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";