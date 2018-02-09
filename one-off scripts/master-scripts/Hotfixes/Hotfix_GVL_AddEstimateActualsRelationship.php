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
 * Time: 1:37 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$Vtiger_Utils_Log = true;

$db = PearDatabase::getInstance();

$estimatesModule = Vtiger_Module::getInstance('Estimates');
$actualsModule = Vtiger_Module::getInstance('Actuals');
if (!$estimatesModule || !$actualsModule) {
    return;
}

// add new fields
$block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $estimatesModule);
if ($block1) {
    $field = Vtiger_Field::getInstance('related_record_self', $estimatesModule);
    if (!$field) {
        $field = new Vtiger_Field();
        // reuse label
        $field->label        = 'LBL_QUOTES_RELATED_RECORD_SELF';
        $field->name         = 'related_record_self';
        $field->table        = 'vtiger_quotes';
        $field->column       = 'related_record_self';
        $field->columntype   = 'INT(10)';
        $field->uitype       = 10;
        $field->typeofdata   = 'V~O';
        $block1->addField($field);
        $field->setRelatedModules(['Actuals']);
    }
}
$block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $actualsModule);
if ($block1) {
    $field = Vtiger_Field::getInstance('related_record_self', $actualsModule);
    if (!$field) {
        $field = new Vtiger_Field();
        // reuse label
        $field->label        = 'LBL_QUOTES_RELATED_RECORD_SELF';
        $field->name         = 'related_record_self';
        $field->table        = 'vtiger_quotes';
        $field->column       = 'related_record_self';
        $field->columntype   = 'INT(10)';
        $field->uitype       = 10;
        $field->typeofdata   = 'V~O';
        $block1->addField($field);
        $field->setRelatedModules(['Estimates']);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";