<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/14/2017
 * Time: 12:56 PM
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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$moduleName = 'WFWorkOrders';

$module = Vtiger_Module::getInstance($moduleName);

if ($module) {
    $fields = ['wfaccount','comment'];
    foreach ($fields as $fieldName){
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET presence=1 WHERE fieldid=$field->id");
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
