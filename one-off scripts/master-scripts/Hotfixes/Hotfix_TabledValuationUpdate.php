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

echo 'Begin Update Tabled Valuation Hotfix<br>';
Vtiger_Utils::AddColumn('vtiger_tariffvaluations', 'amount_row', 'VARCHAR(40)');
echo 'Added column amount_row<br>';
Vtiger_Utils::AddColumn('vtiger_tariffvaluations', 'deductible_row', 'VARCHAR(40)');
echo 'Added column deductible_row<br>';

echo 'Update Tabled Valuation complete!<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";