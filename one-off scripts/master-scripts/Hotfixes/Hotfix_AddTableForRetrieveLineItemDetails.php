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
echo "<br><h1>Starting Hotfix Add Table For Retrieve Line Item Details</h1><br>\n";

if (!Vtiger_Utils::CheckTable('vtiger_rating_line_item_details')) {
    //The table doesn't exist let's make it
    echo "<br><h2>Creating the vtiger_rating_line_item_details table</h2>";
    Vtiger_Utils::CreateTable('vtiger_rating_line_item_details',
                              '(
                line_item_detail_id INT(19) AUTO_INCREMENT UNIQUE,
						    estimate_id INT(19),
								line_item_id INT(19),
								amount DEC(12,2),
								quantity INT(19),
								location VARCHAR(50),
								schedule VARCHAR(10),
								description VARCHAR(255),
								rate DEC(12,2),
								weight INT(19),
                ratingitem VARCHAR(255)
							   )', true);
} else {
    echo "<br><h2>The vtiger_rating_line_item_details table already exists</h2>";
}

echo "<br><h1>Finished Hotfix Add Table For Retrieve Line Item Details</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";