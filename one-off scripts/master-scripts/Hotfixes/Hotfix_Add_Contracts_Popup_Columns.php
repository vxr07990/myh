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



//Hotfix_Add_Contracts_Popup_Columns.php

echo "<br /> Adding Bottom Line Discount and Distribution Discount field to Popup view <br />";
$contractsInstance = Vtiger_Module::getInstance('Contracts');
//NOTE: added just the barest of chekcing.
if ($contractsInstance) {
    $db = PearDatabase::getInstance();
    $db->pquery("UPDATE vtiger_field SET summaryfield = 1 WHERE tabid=$contractsInstance->id AND (columnname='description' OR columnname = 'bottom_line_disc' OR  columnname = 'bottom_line_distribution_discount')");
} else {
    echo "<br /> Failed to open Contracts Module. <br />\n";
}

echo "<br /> Done!  <br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";