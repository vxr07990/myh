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
// 3380: Lead Module - Insert module Move Roles into Block below Date Details Block. Remove Sales, Coordinator from Lead Info Block
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance('Leads');
if (!$moduleInstance) {
    return;
}
// delete field Sales Person
$field1 = Vtiger_Field::getInstance('sales_person', $moduleInstance);
if ($field1) {
    $field1->delete();
}
//***Absolutely DO NOT delete the assigned_user_id field as it is required for a number of backend reasons***
//delete field Coordinator
//$field2 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
//if ($field2) {
//    $field2->delete();
//}
//***
$block = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $moduleInstance);
$field2 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field2) {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_LEADS_ASSIGNEDTO';
    $field2->name       = 'assigned_user_id';
    $field2->table      = 'vtiger_crmentity';
    $field2->column     = 'smownerid';
    $field2->uitype     = 53;
    $field2->typeofdata = 'V~M';
    $block->addField($field2);
}

// create field in MoveRoles Module
$moduleMoveRoles = Vtiger_Module::getInstance('MoveRoles');
if (!$moduleMoveRoles) {
    return;
}
$blockInstance = Vtiger_Block::getInstance('LBL_MOVEROLES_INFORMATION', $moduleMoveRoles);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_MOVEROLES_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

// Remove related field to Leads module
$field3 = Vtiger_Field::getInstance('moveroles_relcrmid', $moduleMoveRoles);
if($field3) {
    $field3->delete();
}
// Add Leads to old related field
$field4 = Vtiger_Field::getInstance('moveroles_orders', $moduleMoveRoles);
if ($field4) {
    $field4->setRelatedModules(['Opportunities', 'Orders','Leads']);
    echo "<br>set related modules";
};


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";