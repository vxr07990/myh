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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>Begin Sirva: create annual rate tables<br>";

$contractsModule = Vtiger_Module::getInstance('Contracts');
$accountsModule = Vtiger_Module::getInstance('Accounts');
    
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

if ($contractsModule) {
    $contractsAnnualRate = Vtiger_Block::getInstance('LBL_CONTRACTS_ANNUALRATE', $contractsModule);
        
    if (!$contractsAnnualRate) {
        $contractsAnnualRate = new Vtiger_Block();
        $contractsAnnualRate->label = 'LBL_CONTRACTS_ANNUALRATE';
        $contractsModule->addBlock($contractsAnnualRate);
        echo "<br>LBL_CONTRACTS_ANNUALRATE DOESN'T EXIST! MAKING IT NOW<br>";
    } else {
        echo "<br>LBL_CONTRACTS_ANNUALRATE ALREADY EXISTS!!! NO ACTION TAKEN<br>";
    }
} else {
    echo "<br>CONTRACTS MODULE NOT FOUND!!!<br>";
}

if ($accountsModule) {
    $accountsAnnualRate = Vtiger_Block::getInstance('LBL_ACCOUNTS_ANNUALRATE', $accountsModule);
        
    if (!$accountsAnnualRate) {
        $accountsAnnualRate = new Vtiger_Block();
        $accountsAnnualRate->label = 'LBL_ACCOUNTS_ANNUALRATE';
        $accountsModule->addBlock($accountsAnnualRate);
        echo "<br>LBL_ACCOUNTS_ANNUALRATE DOESN'T EXIST! MAKING IT NOW<br>";
    } else {
        echo "<br>LBL_ACCOUNTS_ANNUALRATE ALREADY EXISTS!!! NO ACTION TAKEN<br>";
    }
} else {
    echo "<br>ACCOUNTS MODULE NOT FOUND!!!<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";