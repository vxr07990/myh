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
echo '<h1>Starting Hotfix Add Table For Create Valuation Tariff Types</h1><br>';

if (!Vtiger_Utils::CheckTable('vtiger_valuation_tariff_types')) {
    //The table doesn't exist let's make it
    echo '<p>Creating the valuation_tariff_types table</p>';
    $db = PearDatabase::getInstance();

    $sql = "CREATE TABLE `vtiger_valuation_tariff_types` (
			    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `related_id` int(11) NOT NULL,
                `valuation_name` varchar(30) NOT NULL,
                `per_pound` decimal(19,4) NULL,
                `active` enum('y','n') NOT NULL DEFAULT 'y',
                `max_amount` decimal(19,4) NULL,
                `additional_price_per` decimal(19,4) NULL,
                `free` enum('y','n') NULL DEFAULT 'y',
                `additional_price_per_sit` decimal(19,4) NULL,
                `free_amount` decimal(19,4) NULL
		)";
    $result = $db->pquery($sql, []);
    echo '<p>vtiger_valuation_tariff_types table created</p>';
} else {
    echo '<p>The valuation_tariff_types table already exists</p>';
}

echo '<h1>Finished Hotfix Add Table For Create Valuation Tariff Types</h1>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";