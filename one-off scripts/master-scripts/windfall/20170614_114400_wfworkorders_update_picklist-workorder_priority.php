<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/14/2017
 * Time: 11:46 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}


print "[RUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$picklistvalues = array(
    'Urgent', 'High', 'Medium', 'Low'
);

$moduleName = 'WFWorkOrders';
$fieldName = 'wfworkorder_priority';

$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

if(!$moduleModel){
    return;
}
$fieldModel1 = Vtiger_Field_Model::getInstance($fieldName, $moduleModel);
if(!$fieldModel1){
    return;
}

Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE vtiger_'.$fieldName);
$fieldModel1->setPicklistValues($picklistvalues);
