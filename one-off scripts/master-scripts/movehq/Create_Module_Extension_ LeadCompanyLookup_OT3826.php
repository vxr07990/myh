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
$isNew=false;

$moduleInstance = Vtiger_Module::getInstance('LeadCompanyLookup');

if ($moduleInstance) {
    echo "<h2>Container Types already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'LeadCompanyLookup';
    $moduleInstance->save();
    $db = PearDatabase::getInstance();
    if(!Vtiger_Utils::CheckTable('vte_modules')) {
        $stmt = 'CREATE TABLE `vte_modules` (
                `module`  varchar(50) NOT NULL ,
                `valid`  int(1) NULL ,
                PRIMARY KEY (`module`));';
        $db->pquery($stmt);
        echo "Created table vte_modules success <br>";
    }else{
        echo 'vte_modules already exists'.PHP_EOL.'<br>';
    }
    $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('LeadCompanyLookup'));
    $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array('LeadCompanyLookup','1'));
}





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";