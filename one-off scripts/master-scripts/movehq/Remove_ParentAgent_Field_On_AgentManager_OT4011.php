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

//4011 Remove Parent / Child Relationship from all modules / fields
echo "<br>4011 Remove Parent / Child Relationship from all modules / fields</br>";

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

error_reporting(E_ERROR);
ini_set('display_errors', 1);

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleName = 'AgentManager';
$moduleInstance = Vtiger_Module::getInstance($moduleName);

$fieldName = 'cf_agent_manager_parent_id';
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if ($field){
    $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence` = ? WHERE `vtiger_field`.`fieldid` = ?";
    $adb->pquery($sql,array(1,$field->id));
    echo "<br>Removed '$fieldName' field on $moduleName Module";

    $stmt = 'UPDATE ' . $field->table . ' SET ' . $field->column . ' = NULL';
    $adb->query($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";