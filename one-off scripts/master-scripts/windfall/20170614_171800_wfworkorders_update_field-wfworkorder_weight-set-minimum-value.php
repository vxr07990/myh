<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/14/2017
 * Time: 5:19 PM
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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$moduleName = 'WFWorkOrders';
$module = Vtiger_Module_Model::getInstance($moduleName);

if(!$module){
    return;
}

$fieldName = 'wfworkorder_weight';
$field = Vtiger_Field_Model::getInstance($fieldName, $module);

if($field){
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'I~O~MIN=0' WHERE fieldid = $field->id");
}
