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

/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/20/2016
 * Time: 11:35 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

Vtiger_Utils::AddColumn('vtiger_quotes', 'tpg_custom_crate_rate', 'DECIMAL(12,3)');
//Commenting this out because the custom crate rating should key off of apply_custom_pack_rate_override instead
//Vtiger_Utils::AddColumn('vtiger_quotes', 'apply_custom_crate_rate_override', 'VARCHAR(3)');



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";