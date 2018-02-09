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


include_once('vtlib/Vtiger/Module.php');

//Set up the tab/module
unset($moduleInstance);
$moduleInstance = Vtiger_Module::getInstance('AdminSettings');

echo '<br />Checking if Admin Settings module exists.<br />';

if ($moduleInstance) {
    echo '<br />Tariff Admin Settings already exists.<br />';
} else {
    echo '<br />Tariff Admin Settings does not exist. Creating it now:<br />';
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'AdminSettings';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    echo '<br />Admin Settings created!<br />';
}

echo '<br />Checking if Agency Settings table exists.<br />';
if (Vtiger_utils::CheckTable('vtiger_agencysettings')) {
    echo '<br />Agency Settings table exists.<br />';
} else {
    echo '<br>Agency Settings table does not exist. Creating it now:</br>';
    Vtiger_Utils::CreateTable('vtiger_agencysettings',
                              '(
							    agentmanagerid INT(19),
							    valuation_discount DOUBLE(10,1),
								storage_discount DOUBLE(10,1),
								max_share_variance VARCHAR(10),
								packing_fee TINYINT(1),
								disable_dispatch TINYINT(1),
								apply_packing_discount TINYINT(1),
								allow_irr_discount TINYINT(1),
								allow_ferry_discount TINYINT(1),
								allow_labor_surcharge_discount TINYINT(1)
								)', true);
    echo '<br>Agency Settings table has been created!</br>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";