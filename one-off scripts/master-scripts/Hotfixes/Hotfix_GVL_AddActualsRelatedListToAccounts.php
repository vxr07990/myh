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
 * Date: 9/24/2016
 * Time: 12:58 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$Vtiger_Utils_Log = true;

$db = PearDatabase::getInstance();

$accountsModule = Vtiger_Module::getInstance('Accounts');
$actualsModule = Vtiger_Module::getInstance('Actuals');
if (!$accountsModule || !$actualsModule) {
    return;
}
$accountTabId = $accountsModule->id;
$actualsTabId = $actualsModule->id;
$relationExists = $db->pquery("SELECT * FROM `vtiger_relatedlists` WHERE tabid = ? AND related_tabid = ?", [$accountTabId, $actualsTabId])->fetchRow();

if (!$relationExists) {
    $accountsModule->setRelatedList($actualsModule, 'Actuals', ['ADD'], 'get_dependents_list');
}

$field = Vtiger_Field::getInstance('account_id', $actualsModule);
if ($field) {
    $field->setRelatedModules(['Accounts']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";