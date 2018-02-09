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



//HOTFIX agentid for OPList

//$Vtiger_Utils_Log = true;
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');

echo "<br>BEGINNING Hotfix: Add agentid to OPList<br>";

$moduleInstance = Vtiger_Module::getInstance('OPList');
$blockInstance = Vtiger_Block::getInstance('LBL_OPLIST_INFORMATION', $moduleInstance);

$field6 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'Owner';
    $field6->name = 'agentid';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'agentid';
    $field6->columntype = 'INT(10)';
    $field6->uitype = 1002;
    $field6->typeofdata = 'I~M';

    $blockInstance->addField($field6);
}

echo "<br>COMPLETED Hotfix: Add agentid to OPList<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";