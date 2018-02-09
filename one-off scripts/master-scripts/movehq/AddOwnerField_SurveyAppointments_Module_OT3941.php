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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $db;

$moduleInstance = Vtiger_Module::getInstance('Surveys');

$field = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field) {
    $block = Vtiger_Block::getInstance('LBL_SURVEYS_INFORMATION',$moduleInstance);
    if ($block){
        $field = new Vtiger_Field();
        $field->label = 'Owner';
        $field->name = 'agentid';
        $field->table = 'vtiger_crmentity';
        $field->column = 'agentid';
        $field->columntype = 'INT(11)';
        $field->uitype = 1002;
        $field->typeofdata = 'I~M';
        $block->addField($field);
        echo "<br>Created Owner field on Surveys Module success<br>";
    }
}else{
    echo "<br>Owner field on Surveys Module have already exists<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";