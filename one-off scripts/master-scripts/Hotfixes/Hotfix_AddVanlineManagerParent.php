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


//include this stuff to run independent of master script
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//removed for new securities
//include_once 'one-off scripts/master-scripts/makeDefaultProfiles.php';

$moduleInstance = Vtiger_Module::getInstance('VanlineManager');
$blockInstance = Vtiger_Block::getInstance('LBL_VANLINEMANAGER_INFORMATION', $moduleInstance);

$field1 = Vtiger_Field::getInstance('is_parent', $moduleInstance);
if ($field1) {
    echo "Field is_parent already exists";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VANLINEMANAGER_ISPARENT';
    $field1->name = 'is_parent';
    $field1->table = 'vtiger_vanlinemanager';
    $field1->column = 'is_parent';
    $field1->columntype = 'VARCHAR(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'C~O';
    
    $blockInstance->addField($field1);
    
    createDefaultProfile("Parent Vanline User");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";