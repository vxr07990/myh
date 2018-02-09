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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

$moduleName = 'Opportunities';
$blockName = 'LBL_POTENTIALS_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

echo "<br><h1>Starting To add status field is_competitive to opportunities</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $fieldCheck = Vtiger_Field::getInstance('is_competitive', $module);
    if ($fieldCheck) {
        echo '<p>Opportunities competitive field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_OPPORTUNITIES_COMPETITIVE';
        $field->name = 'is_competitive';
        $field->table = 'vtiger_potential';
        $field->column = 'is_competitive';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = '56';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        echo '<p>Added is_competitive to opportunities field</p>';
    }
} else {
    echo '<p>Opportunities is_competitive field could not be added, couldn\'t find LBL_POTENTIALS_INFORMATION block</p>';
}

echo "<br><h1>Finished adding is_competitive to opportunities field</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";