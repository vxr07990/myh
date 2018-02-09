<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 8/25/2017
 * Time: 5:04 PM
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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$moduleInstance = Vtiger_Module::getInstance('WFArticles');

if(!$moduleInstance){
    return;
}

$block = Vtiger_Block::getInstance('LBL_WFARTICLES_ARTICLE_INFORMATION', $moduleInstance);

if(!$block){
    return;
}
$fieldStatus = Vtiger_Field::getInstance('article_status', $moduleInstance);
if ($fieldStatus) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET defaultvalue = 'Active' WHERE fieldid = $fieldStatus->id");
} else {
    $fieldStatus             = new Vtiger_Field();
    $fieldStatus->label      = 'LBL_ARTICLE_STATUS';
    $fieldStatus->name       = 'article_status';
    $fieldStatus->table      = 'vtiger_wfarticles';
    $fieldStatus->column     = 'article_status';
    $fieldStatus->columntype = 'VARCHAR(100)';
    $fieldStatus->uitype     = 16;
    $fieldStatus->typeofdata = 'V~O';
    $fieldStatus->defaultvalue = 'Active';
    $block->addField($fieldStatus);
}
