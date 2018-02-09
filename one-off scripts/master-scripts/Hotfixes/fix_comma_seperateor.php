<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}


$seperators = [
    ".",
    ",",
    "'",
    "$",
    "&nbsp;",
];
echo "Starting the update process for the currency_grouping_separator values <br/>";
try {
    foreach ($seperators as $sep) {
        $update_val = $sep == "," ? "." : ",";
        $sql = "UPDATE vtiger_users SET currency_grouping_separator = ? 
                  WHERE currency_grouping_separator = currency_decimal_separator 
                  AND currency_grouping_separator = ?";

        $db->pquery($sql, [$update_val, $sep]);
    }
    echo "The update process for the currency_grouping_separator values has completed!<br/>";
} catch (Exception $e) {
    echo "Failed to update all of the values!";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";