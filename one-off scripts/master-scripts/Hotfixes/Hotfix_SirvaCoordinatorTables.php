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

echo "<br>Begin Sirva: create coordinator tables<br>";

if (Vtiger_Utils::CheckTable('vtiger_coordinators')) {
    echo "<br>vtiger_coordinators already exists! No action taken<br>";
} else {
    echo "<br>vtiger_coordinators doesn't exist! Creating it now.<br>";
    Vtiger_Utils::CreateTable('vtiger_coordinators',
                      '(
						coordinatorsid INT(11),
						agentmanagerid INT(11),
						sales_person INT(11),
						coordinators VARCHAR(255),
						)', true);
    Vtiger_Utils::CreateTable('vtiger_coordinators_seq',
                      '(
						id INT(11)
						)', true);
    echo "<br>vtiger_coordinators table created successfully<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";