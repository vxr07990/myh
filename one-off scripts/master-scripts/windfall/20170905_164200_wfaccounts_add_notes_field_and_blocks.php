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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFAccounts');
if(!$module) {
    echo "WFAccounts does not exist";
    return;
}
$blockNotes = Vtiger_Block::getInstance('LBL_WFACCOUNTS_NOTES', $module);
if ($blockNotes) {
    echo "<h3>The LBL_WFACCOUNTS_NOTES block already exists</h3><br> \n";
} else {
    $blockNotes        = new Vtiger_Block();
    $blockNotes->label = 'LBL_WFACCOUNTS_NOTES';
    $module->addBlock($blockNotes);
}

$notesField = Vtiger_Field::getInstance('wfaccounts_notes', $module);
if ($notesField) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockNotes->id, `sequence` = 1 WHERE `fieldid` = $notesField->id");;
} else {
    $notesField             = new Vtiger_Field();
    $notesField->label      = 'LBL_WFACCOUNTS_NOTES_FIELD';
    $notesField->name       = 'wfaccounts_notes';
    $notesField->table      = 'vtiger_wfaccounts';
    $notesField->column     = 'wfaccounts_notes';
    $notesField->columntype = 'TEXT';
    $notesField->uitype     = 19;
    $notesField->typeofdata = 'V~O';
    $blockNotes->addField($notesField);
}
