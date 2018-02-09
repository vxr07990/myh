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

echo "<br><h1>Starting Hotfix Waive EAC Rate Field to Contracts Tariff Info</h1><br>\n";

$contracts = Vtiger_Module::getInstance('Contracts');

$field = Vtiger_Field::getInstance('waive_eac_rates', $contracts);
$block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $contracts);

if ($field) {
    echo '<p>Field waive_eac_rates already exists</p>';
} else {
    if ($block) {
        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_WAIVE_EAC';
        $field->name = 'waive_eac_rates';
        $field->table = 'vtiger_contracts';
        $field->column = 'waive_eac_rates';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = 56;
        $field->typeofdata = 'C~O';
        $field->displaytype = 1;
        $field->quickcreate = 0;
        $field->presence = 2;
        $field->summaryfield = 0;
        $block->addField($field);

        echo '<p>Added waive_eac_rates to LBL_CONTRACTS_TARIFF block</p>';
    } else {
        echo '<p>Failed to add waive_eac_rates, could not find LBL_CONTRACTS_TARIFF block</p>';
    }
}

echo "<br><h1>Finished Hotfix Add Waive EAC Rate Field to Contracts Tariff Info</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";