<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/8/2016
 * Time: 4:24 PM
 */
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

$db = &PearDatabase::getInstance();

$sql = 'UPDATE vtiger_quotes,vtiger_contracts 
          SET effective_tariff=COALESCE(related_tariff,local_tariff,effective_tariff) 
          WHERE contract=contractsid';

$db->pquery($sql);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";