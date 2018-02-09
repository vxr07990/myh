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
 * Date: 10/3/2016
 * Time: 10:55 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;

$db = PearDatabase::getInstance();

$sql = 'UPDATE `vtiger_quotes` 
        INNER JOIN `vtiger_crmentity` ON (vtiger_quotes.quoteid = vtiger_crmentity.crmid)
        SET vtiger_quotes.actuals_stage=vtiger_quotes.quotestage
        WHERE vtiger_quotes.actuals_stage IS NULL AND vtiger_crmentity.setype = ?';
$db->pquery($sql, ['Actuals']);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";