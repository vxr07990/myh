<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/27/2016
 * Time: 10:51 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 7;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Orders');
$actualsModule = Vtiger_Module::getInstance('Actuals');

if (!$module || !$actualsModule) {
    return;
}

$db = &PearDatabase::getInstance();

$db->pquery("DELETE FROM vtiger_relatedlists WHERE tabid=$module->id AND related_tabid=$actualsModule->id AND `name`=?",
            ['get_dependents_list']);

$module->unsetRelatedList($actualsModule, 'Actuals', 'get_related_list');
$module->setRelatedList($actualsModule, 'Actuals', ['ADD'], 'get_related_list');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";