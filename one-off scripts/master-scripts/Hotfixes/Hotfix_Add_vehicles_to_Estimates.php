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

echo '<br />Checking if Quotes Vehicles table exists.<br />';
if (Vtiger_utils::CheckTable('vtiger_quotes_vehicles')) {
    echo '<br />Quotes Vehicles table exists.<br />';
} else {
    echo '<br>Quotes Vehicles table does not exist. Creating it now:</br>';
    Vtiger_Utils::CreateTable('vtiger_quotes_vehicles',
                              '(
								vehicle_id INT(11) primary key auto_increment,
							    estimateid INT(19),
							    description VARCHAR(255),
								weight INT(10),
								sit_days INT(10),
								rate_type VARCHAR(10)
								)', true);
    echo '<br>Quotes Vehicles table has been created!</br>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";