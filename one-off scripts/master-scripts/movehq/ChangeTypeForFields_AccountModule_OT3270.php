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


$Vtiger_Utils_Log = true;
global $adb;

$accountModuleInstance = Vtiger_Module::getInstance('Accounts');

$field = Vtiger_Field::getInstance('national_account_number',$accountModuleInstance);
if ($field){

    $set = array();
    $param = array();

    if ($field->uitype !== '1'){
        $set[] = "`vtiger_field`.`uitype` = ?";
        $param[] = '1';
    }

    if ($field->typeofdata !== 'V~O'){
        $set[] = "`vtiger_field`.`typeofdata` = ?";
        $param[] = 'V~O';
    }

    if (count($param)>0){
        $setSql = implode(',', $set);
        $updateSql = "UPDATE `vtiger_field` SET $setSql WHERE `vtiger_field`.`fieldid` = $field->id";
        $adb->pquery($updateSql,$param);

        echo "<li>Changed 'national_account_number' field on Accounts Module uitype = 1 and typeofdata = V~O</li>";
    }

    $sql = "ALTER TABLE vtiger_account MODIFY national_account_number VARCHAR (100)";
    $adb->pquery($sql);
}


$field = Vtiger_Field::getInstance('customer_number',$accountModuleInstance);
if ($field){

    $set = array();
    $param = array();

    if ($field->uitype !== '1'){
        $set[] = "`vtiger_field`.`uitype` = ?";
        $param[] = '1';
    }

    if ($field->typeofdata !== 'V~O'){
        $set[] = "`vtiger_field`.`typeofdata` = ?";
        $param[] = 'V~O';
    }

    if (count($param)>0){
        $setSql = implode(',', $set);
        $updateSql = "UPDATE `vtiger_field` SET $setSql WHERE `vtiger_field`.`fieldid` = $field->id";
        $adb->pquery($updateSql,$param);

        echo "<li>Changed 'customer_number' field on Accounts Module uitype = 1 and typeofdata = V~O</li>";
    }

    $sql = "ALTER TABLE vtiger_account MODIFY customer_number VARCHAR (100)";
    $adb->pquery($sql);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";