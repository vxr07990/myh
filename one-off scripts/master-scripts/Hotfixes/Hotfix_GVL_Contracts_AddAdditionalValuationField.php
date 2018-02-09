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


//<?php
//OT 1815 - Additional valuation field for Estimates.

echo "<br>Starting Contracts_AddAdditionalValuationField<br>";

//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$module = Vtiger_Module::getInstance('Contracts');

$block0 = Vtiger_Block::getInstance('LBL_CONTRACTS_VALUATION', $module);
if ($block0) {
    echo "<br> The 'LBL_CONTRACTS_VALUATION' block already exists in Contracts <br>";
    $field = Vtiger_Field::getInstance('additional_valuation', $module);
    if ($field) {
        echo "<br> The additional_valuation field already exists in Contracts <br>";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_ADDITIONALVALUATION';
        $field->name = 'additional_valuation';
        $field->table = 'vtiger_contracts';
        $field->column ='additional_valuation';
        $field->columntype = 'decimal(22,8)';
        $field->uitype = 71;
        $field->typeofdata = 'N~O';
        $field->displaytype = 1;
        $field->quickcreate = 1;
        $field->summaryfield = 0;

        $block0->addField($field);
        echo "<br>additional_valuation field added to Contracts<br>";
    }
} else {
    echo "<br>LBL_CONTRACTS_VALUATION block not present. No fields added.</br>";
}
echo "<br>Ending Contracts_AddAdditionalValuationField<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";