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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Add extended_sit_mileage to contracts</h1><br>\n";

$blockName = 'LBL_CONTRACTS_INFORMATION';
$moduleName = 'Contracts';

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance($blockName, $module);

if ($block) {
    $field = Vtiger_Field::getInstance('extended_sit_mileage', $quotes);
    if ($field) {
        echo '<p>extended_sit_mileage already exists</p>';
    } else {
        echo "<br> extended_sit_mileage field doesn't exist, adding it now.<br>";

        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_EXTENDED_SIT_MILEAGE';
        $field->name = 'extended_sit_mileage';
        $field->table = 'vtiger_contracts';
        $field->column = 'extended_sit_mileage';
        $field->columntype = 'INT(11)';
        $field->uitype = 7;
        $field->typeofdata = 'I~O';
        $field->sequence = 10;

        $block->addField($field);

        echo '<p>Added extended_sit_mileage field</p>';
    }
} else {
    echo '<p>LBL_CONTRACTS_INFORMATION Block Doesn\'t Exist</p>';
}




echo "<br><h1>End Hotfix extended_sit_mileage to contracts</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";