<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/7/2016
 * Time: 3:31 PM
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

$db->pquery('ALTER TABLE vtiger_tariffcwtbyweight MODIFY rate DECIMAL(11,2)');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";