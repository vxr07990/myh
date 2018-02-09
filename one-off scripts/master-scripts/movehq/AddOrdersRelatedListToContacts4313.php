<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/24/2017
 * Time: 11:55 AM
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

$contactsModule = Vtiger_Module::getInstance('Contacts');
$ordersModule = Vtiger_Module::getInstance('Orders');
if(!$contactsModule || !$ordersModule)
{
    return;
}

$rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",array(getTabid('Contacts'),getTabid('Orders')));
if($db->num_rows($rsRelated) == 0){
    $contactsModule->setRelatedList($ordersModule, 'Orders', [], 'get_dependents_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";