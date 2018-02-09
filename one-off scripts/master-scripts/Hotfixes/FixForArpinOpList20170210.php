<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/10/2017
 * Time: 1:21 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = '2';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$opp = Vtiger_Module::getInstance('Opportunities');
$opp->unsetRelatedList(Vtiger_Module::getInstance('OPList'), 'OP Lists', 'get_dependents_list');
$opp->setRelatedList(Vtiger_Module::getInstance('OPList'), 'OP Lists', ['SELECT'], 'get_dependents_list');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";