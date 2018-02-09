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


/**
 * this will add the 400NG tariff to the picklist as a custom_tariff_type
 * This will create the field if it doesn't exist aleady... although it should.
 *
 * we should be able to pull out the add picklist portion to reuse in a class.
 *
 * @TODO: Make it so it removes picklist values NOT in our picklistOrder array.
 *
 */

$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Contracts';

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $module);
print "<h3>Starting AddDistributionDiscountContractsEstimates</h3>\n";
if ($block) {
    print "<br> Block 'LBL_CONTRACTS_TARIFF' is already present <br>\n";
    //////////////////////////////////////

    $field = Vtiger_Field::getInstance('sit_distribution_discount', $module);
    if ($field) {
        print "<br> Field 'sit_distribution_discount' is already present <br>";
    } else {
        print "Creating new field: sit_distribution_discount.<br />";

        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_SIT_DISTRIBUTION_DISCOUNT';
        $field->name = 'sit_distribution_discount';
        $field->table = 'vtiger_contracts';
        $field->column = 'sit_distribution_discount';
        ;
        $field->columntype = 'INT(11)';
        $field->uitype = 9;
        $field->sequence = 14;
        $field->typeofdata = 'N~O';

        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('bottom_line_distribution_discount', $module);
    if ($field) {
        print "<br> Field 'sit_distribution_discount' is already present <br>";
    } else {
        print "Creating new field: sit_distribution_discount.<br />";

        $field = new Vtiger_Field();
        $field->label = 'LBL_CONTRACTS_BOTTOM_LINE_DISTRIBUTION_DISCOUNT';
        $field->name = 'bottom_line_distribution_discount';
        $field->table = 'vtiger_contracts';
        $field->column = 'bottom_line_distribution_discount';
        ;
        $field->columntype = 'INT(11)';
        $field->uitype = 9;
        $field->sequence = 17;
        $field->typeofdata = 'N~O';

        $block->addField($field);
    }
} else {
    print "<br> LBL_CONTRACTS_TARIFF Doesn't Exists<br>/n";
}

$moduleName = 'Estimates';

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
if ($block) {
    print "<br> Block 'LBL_QUOTES_INTERSTATEMOVEDETAILS' is already present <br>\n";

    $field = Vtiger_Field::getInstance('sit_distribution_discount', $module);
    if ($field) {
        print "<br> Field 'sit_distribution_discount' is already present <br>";
    } else {
        print "Creating new field: sit_distribution_discount.<br />";

        $field = new Vtiger_Field();
        $field->label = 'LBL_ESTIMATES_SIT_DISTRIBUTION_DISCOUNT';
        $field->name = 'sit_distribution_discount';
        $field->table = 'vtiger_quotes';
        $field->column = 'sit_distribution_discount';
        ;
        $field->columntype = 'INT(11)';
        $field->uitype = 9;
        $field->sequence = 15;
        $field->typeofdata = 'N~O';

        $block->addField($field);
    }

    $field = Vtiger_Field::getInstance('bottom_line_distribution_discount', $module);
    if ($field) {
        print "<br> Field 'sit_distribution_discount' is already present <br>";
    } else {
        print "Creating new field: sit_distribution_discount.<br />";

        $field = new Vtiger_Field();
        $field->label = 'LBL_ESTIMATES_BOTTOM_LINE_DISTRIBUTION_DISCOUNT';
        $field->name = 'bottom_line_distribution_discount';
        $field->table = 'vtiger_quotes';
        $field->column = 'bottom_line_distribution_discount';
        ;
        $field->columntype = 'INT(11)';
        $field->uitype = 9;
        $field->sequence = 17;
        $field->typeofdata = 'N~O';

        $block->addField($field);
    }
} else {
    print "<br> LBL_QUOTES_INTERSTATEMOVEDETAILS Doesn't Exists<br>/n";
}
print "<h3>Ending AddDistributionDiscountContractsEstimates</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";