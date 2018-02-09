<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/27/2017
 * Time: 1:44 PM
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

$db->pquery('DELETE FROM vtiger_convertleadmapping WHERE leadfid IN (SELECT fieldid FROM vtiger_field WHERE fieldname=\'business_line2\'');



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";