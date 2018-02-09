<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/15/2017
 * Time: 4:35 PM
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

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$db->pquery('UPDATE vtiger_guestmodulerel SET active=? WHERE guestmodule=?',
            [1, 'ExtraStops']);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";