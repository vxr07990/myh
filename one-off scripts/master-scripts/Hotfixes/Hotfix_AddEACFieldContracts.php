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


echo "<br><h1>Starting Hotfix Add EAC Field to Contracts Tariff Info</h1><br>\n";

$contracts = Vtiger_Module::getInstance('Contracts');

$field = Vtiger_Field::getInstance('fixed_eac', $contracts);
$field2 = Vtiger_Field::getInstance('fixed_eac_percent', $contracts);

$block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $contracts);


if ($block) {
    if ($field) {
        echo '<p>Field fixed_eac already exists</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_FIXED_EAC';
        $field->name = 'fixed_eac';
        $field->table = 'vtiger_contracts';
        $field->column = 'fixed_eac';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = 56;
        $field->typeofdata = 'C~O';
        $field->displaytype = 1;
        $field->quickcreate = 0;
        $field->presence = 2;
        $field->summaryfield = 0;
        $block->addField($field);

        echo '<p>Added fixed_eac to LBL_CONTRACTS_TARIFF block</p>';
    }

    if ($field2) {
        echo '<p>Field fixed_eac_percent already exists</p>';
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_CONTRACTS_FIXED_EAC_PERCENT';
        $field2->name = 'fixed_eac_percent';
        $field2->table = 'vtiger_contracts';
        $field2->column = 'fixed_eac_percent';
        $field2->columntype = 'VARCHAR(15)';
        $field2->uitype = 9;
        $field2->typeofdata = 'N~O';
        $field2->displaytype = 1;
        $field2->quickcreate = 0;
        $field2->presence = 2;
        $field2->summaryfield = 0;
        $block->addField($field2);

        echo '<p>Added fixed_eac_percent to LBL_CONTRACTS_TARIFF block</p>';
    }
} else {
    echo '<p>Failed to add fixed_eac, could not find LBL_CONTRACTS_TARIFF block</p>';
}



echo "<br><h1>Finished Hotfix Add EAC Field to Contracts Tariff Info</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";