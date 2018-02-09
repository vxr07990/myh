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



$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';


echo "<br><h1>Starting Hotfix Add Shared Assigned To Field to Employees Module</h1><br>\n";

$employeesModule = Vtiger_Module::getInstance('Employees');

$block = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $employeesModule);
$field = Vtiger_Field::getInstance('shared_assigned_to', $employeesModule);

if ($block) {
    if ($field) {
        echo '<p>Field shared_assigned_to already exists</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_EMPLOYEES_SHARED_ASSIGNED_TO';
        $field->name = 'shared_assigned_to';
        $field->table = 'vtiger_employees';
        $field->column = 'shared_assigned_to';
        $field->columntype = 'VARCHAR(255)';
        $field->uitype = 33;
        $field->typeofdata = 'V~O';
        $field->displaytype = 1;
        $field->quickcreate = 0;
        $field->presence = 2;
        $field->summaryfield = 0;
        $block->addField($field);

        echo '<p>Added shared_assigned_to to LBL_EMPLOYEES_INFORMATION block</p>';
    }
} else {
    echo '<p>Failed to add shared_assigned_to, could not find LBL_EMPLOYEES_INFORMATION block</p>';
}



echo "<br><h1>Finished Hotfix Add Shared Assigned To Field to Employees Module</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";