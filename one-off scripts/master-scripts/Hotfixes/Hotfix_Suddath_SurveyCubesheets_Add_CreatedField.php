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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Surveys';
$blockName = 'LBL_BLOCK_SYSTEM_INFORMATION';

$moduleInstance = Vtiger_Module::getInstance($moduleName);

if(!$moduleInstance) {
    print "Unable to find $moduleName <br />\n";
} else {
    $blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);
    if ($blockInstance) {
        $field2 = Vtiger_Field::getInstance('created_user_id', $moduleInstance);
        if ($field2) {
            echo "The createdby field already exists<br>\n";
        } else {
            $field2               = new Vtiger_Field();
            $field2->label        = 'Created By';
            $field2->name         = 'created_user_id';
            $field2->table        = 'vtiger_crmentity';
            $field2->column       = 'smcreatorid';
            $field2->uitype       = 52;
            $field2->typeofdata   = 'V~O';
            $field2->displaytype  = 2;
            $field2->quickcreate  = 3;
            $field2->masseditable = 0;
            $blockInstance->addField($field2);
        }
    } else {
        print "Unable to find block $blockName <br />\n";
    }
}

$moduleName = 'Cubesheets';
$blockName = 'LBL_CUBESHEETS_INFORMATION';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

if(!$moduleInstance) {
    print "Unable to find $moduleName <br />\n";
} else {
    $blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);
    if ($blockInstance) {
        $field2 = Vtiger_Field::getInstance('created_user_id', $moduleInstance);
        if ($field2) {
            echo "The createdby field already exists<br>\n";
        } else {
            $field2               = new Vtiger_Field();
            $field2->label        = 'Created By';
            $field2->name         = 'created_user_id';
            $field2->table        = 'vtiger_crmentity';
            $field2->column       = 'smcreatorid';
            $field2->uitype       = 52;
            $field2->typeofdata   = 'V~O';
            $field2->displaytype  = 2;
            $field2->quickcreate  = 3;
            $field2->masseditable = 0;
            $blockInstance->addField($field2);
        }
    } else {
        print "Unable to find block $blockName <br />\n";
    }
}
