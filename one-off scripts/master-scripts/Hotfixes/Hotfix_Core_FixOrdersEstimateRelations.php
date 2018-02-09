<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/5/2017
 * Time: 10:09 AM
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

// Remove all Orders -> Estimates

$db->pquery('CREATE TABLE IF NOT EXISTS vtiger_crmentityrel_backup17792 LIKE vtiger_crmentityrel');

$db->pquery('INSERT INTO vtiger_crmentityrel_backup17792 SELECT * FROM vtiger_crmentityrel');

$db->pquery('DELETE FROM vtiger_crmentityrel WHERE module=? and relmodule=?', ['Orders', 'Estimates']);
$db->pquery('DELETE FROM vtiger_crmentityrel WHERE module=? and relmodule=?', ['Orders', 'Actuals']);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";