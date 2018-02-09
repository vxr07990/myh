<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/11/2017
 * Time: 3:37 PM
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

$res = $db->pquery('SELECT 1 FROM vtiger_ws_referencetype WHERE fieldtypeid=? AND type=?',
                   [34, 'OrdersTask']);
if($db->num_rows($res) <= 0)
{
    $db->pquery('INSERT INTO vtiger_ws_referencetype (fieldtypeid, type) VALUES (?,?)',
                [34, 'OrdersTask']);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";