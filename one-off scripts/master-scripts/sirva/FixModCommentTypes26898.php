<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 3/3/2017
 * Time: 7:37 AM
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

$db->pquery('UPDATE vtiger_modcomments,vtiger_crmentity
                SET cf_comment_type = CASE 
                  WHEN setype=? THEN 1
                  WHEN setype=? THEN 1
                  WHEN setype=? THEN 2
                  WHEN setype=? THEN 3
                  WHEN setype=? THEN 3
                  ELSE 0 END
                WHERE crmid=related_to',
            ['Leads','Opportunities','Cubesheets','Estimates','Actuals']);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";