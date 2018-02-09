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


echo "<br><h1>Adding Corporate Vehicles Table</h1><br>";

// $Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

if (!Vtiger_Utils::CheckTable('vtiger_corporate_vehicles')) {
    echo "<li>creating vtiger_corporate_vehicles </li><br>";
    Vtiger_Utils::CreateTable('vtiger_corporate_vehicles',
                              '(
							    estimate_id INT(19),
								vehicle_id INT(19),
								make VARCHAR(50),
								model VARCHAR(50),
								year INT(19),
								weight DECIMAL(10,2),
								cube  INT(19),
								service VARCHAR(20),
								dvp_value INT(19),
								car_on_van VARCHAR(3),
								oversize_class VARCHAR(20),
								inoperable VARCHAR(3),
								length INT(19),
								width INT(19),
								height INT(19),
								charge DECIMAL(10,2),
								shipping_count INT(19),
								not_shipping_count INT(19),
								comment VARCHAR(255)
								)', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";