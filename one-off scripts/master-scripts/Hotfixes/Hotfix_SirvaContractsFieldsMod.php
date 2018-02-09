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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "<br>Begin Sirva modifications to contracts fields<br>";

$contractsModule = Vtiger_Module::getInstance('Contracts');

echo "<br>Got instance of Contracts Module<br>";

if ($contractsModule) {
    echo "<br>Contracts module exists! Modifying fields.<br>";
    $contractsBilling = Vtiger_Block::getInstance('LBL_CONTRACTS_BILLING', $contractsModule);
    if ($contractsBilling) {
        //create field for move type
        $billingContact = Vtiger_Field::getInstance('billing_contact', $contractsModule);
        if ($billingContact) {
            echo "<br> Field 'billing_contact' is already present. <br>";
        } else {
            echo "<br> Field 'billing_contact' not present. Creating it now<br>";
            $billingContact = new Vtiger_Field();
            $billingContact->label = 'LBL_CONTRACTS_BILLINGCONTACT';
            $billingContact->name = 'billing_contact';
            $billingContact->table = 'vtiger_contracts';
            $billingContact->column = 'billing_contact';
            $billingContact->columntype = 'VARCHAR(255)';
            $billingContact->uitype = 10;
            $billingContact->typeofdata = 'V~O';
            $billingContact->quickcreate = 0;

            $contractsBilling->addField($billingContact);
            $billingContact->setRelatedModules(array('Contacts'));
            echo "<br> Field 'billing_contact' added.<br>";
        }
    } else {
        echo "<br>LBL_CONTRACTS_BILLING NOT FOUND! NO ACTION TAKEN!<br>";
    }
    $contractsTariff = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $contractsModule);
    if ($contractsTariff) {
        //create field for move type
        $useCurrentRates = Vtiger_Field::getInstance('use_current_rates', $contractsModule);
        if ($useCurrentRates) {
            echo "<br> Field 'use_current_rates' is already present. <br>";
        } else {
            echo "<br> Field 'use_current_rates' not present. Creating it now<br>";
            $useCurrentRates = new Vtiger_Field();
            $useCurrentRates->label = 'LBL_CONTRACTS_USECURRENTRATES';
            $useCurrentRates->name = 'use_current_rates';
            $useCurrentRates->table = 'vtiger_contracts';
            $useCurrentRates->column = 'use_current_rates';
            $useCurrentRates->columntype = 'VARCHAR(3)';
            $useCurrentRates->uitype = 56;
            $useCurrentRates->typeofdata = 'V~O';
            $useCurrentRates->quickcreate = 0;

            $contractsTariff->addField($useCurrentRates);
            echo "<br> Field 'use_current_rates' added.<br>";
        }
    } else {
        echo "<br>LBL_CONTRACTS_INFORMATION NOT FOUND! NO ACTION TAKEN!<br>";
    }
    
    $contractsAnnualRate = Vtiger_Block::getInstance('LBL_CONTRACTS_ANNUALRATE', $contractsModule);
    
    if (!$contractsAnnualRate) {
        $contractsAnnualRate = new Vtiger_Block();
        $contractsAnnualRate->label = 'LBL_CONTRACTS_ANNUALRATE';
        $contractsModule->addBlock($contractsAnnualRate);
        echo "<br>LBL_CONTRACTS_ANNUALRATE DOESN'T EXIST! MAKING IT NOW<br>";
    } else {
        echo "<br>LBL_CONTRACTS_ANNUALRATE ALREADY EXISTS!!! NO ACTION TAKEN<br>";
    }
    
    if (Vtiger_Utils::CheckTable('vtiger_annual_rate')) {
        echo "<br>vtiger_annual_rate already exists! No action taken<br>";
    } else {
        echo "<br>vtiger_annual_rate doesn't exist! Creating it now.<br>";
        Vtiger_Utils::CreateTable('vtiger_annual_rate',
                          '(
							annualrateid INT(11),
							date VARCHAR(255),
							rate DECIMAL(5,2),
							contractid INT(11),
							accountid INT(11)
							)', true);
        Vtiger_Utils::CreateTable('vtiger_annual_rate_seq',
                          '(
							id INT(11)
							)', true);
        echo "<br>vtiger_annual_rate table created successfully<br>";
    }
} else {
    echo "<br>CONTRACTS MODULE DOESN'T EXIST! FIELDS NOT MODIFIED<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";