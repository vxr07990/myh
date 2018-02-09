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
  *	This hotfix file is to disable the fixed_fuel checkbox in Contracts and replace its functionality
  * with a picklist for fuel_surcharge_type. This picklist will control visibility to other fields
  * where the fuel surcharge is defined.
  */
//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Contracts');
$block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $module);

$field1 = Vtiger_Field::getInstance('fixed_fuel', $module);
if ($field1) {
    echo "Field fixed_fuel exists in Contracts field - disabling<br />";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field1->id);
}

$field2 = Vtiger_Field::getInstance('fuel_surcharge_type', $module);
if ($field2) {
    echo "Field fuel_surcharge_type already exists<br />";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CONTRACTS_FUELSURCHARGETYPE';
    $field2->name = 'fuel_surcharge_type';
    $field2->table = 'vtiger_contracts';
    $field2->column = 'fuel_surcharge_type';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';
    $field2->sequence = $field1->sequence;
    
    $block->addField($field2);
    $field2->setPicklistValues(array('DOE - Rate/CWT/Mile', 'DOE - Fuel Percentage', 'DOE - Rate/Mile', 'DOE - Rate/Mile or Percentage', 'Static Fuel Percentage'));
}

$field3 = Vtiger_Field::getInstance('fuel_disc', $module);
if ($field3) {
    echo "Field fuel_discount already exists<br />";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CONTRACTS_FUELDISCOUNT';
    $field3->name = 'fuel_disc';
    $field3->table = 'vtiger_contracts';
    $field3->column = 'fuel_disc';
    $field3->columntype = 'DECIMAL(7,2)';
    $field3->uitype = 9;
    $field3->typeofdata = 'N~O';
    
    $block->addField($field3);
}

if (!Vtiger_Utils::CheckTable('vtiger_contractfuel')) {
    echo "<li>creating vtiger_contractfuel </li><br>";
    Vtiger_Utils::CreateTable('vtiger_contractfuel',
                              '(
								contractid INT(11),
								from_cost DECIMAL(10,3),
								to_cost DECIMAL(10,3),
								rate DECIMAL(10,2),
								percentage DECIMAL(10,3),
								line_item_id INT(11) AUTO_INCREMENT,
								PRIMARY KEY (line_item_id)
							  )', true);
    echo "<li>Table vtiger_contractfuel created</li><br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";