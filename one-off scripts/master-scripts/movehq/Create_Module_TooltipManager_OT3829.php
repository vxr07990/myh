<?php
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
$isNew=false;

$moduleInstance = Vtiger_Module::getInstance('TooltipManager');

if ($moduleInstance) {
    echo "<h2>Container Types already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'TooltipManager';
    $moduleInstance->save();
    $adb = PearDatabase::getInstance();

    if(!Vtiger_Utils::CheckTable('vte_modules')) {
        $stmt = "CREATE TABLE `vte_modules` (
                `module`  varchar(50) NOT NULL ,
                `valid`  int(1) NULL ,
                PRIMARY KEY (`module`));";
        $adb->pquery($stmt);
        echo "Created table vte_modules success <br>";
    }else{
        echo 'vte_modules already exists'.PHP_EOL.'<br>';
    }
    
    $adb->pquery("DELETE FROM vtiger_links WHERE linklabel IN ('TooltipManagerJS','TooltipManagerjQueryUrlJS','TooltipManagerqTip')",array());
    
    $adb->pquery("ALTER TABLE `vtiger_field` ADD COLUMN `icon`  varchar(255) NULL AFTER `summaryfield`",array());
    $adb->pquery("ALTER TABLE `vtiger_field` ADD `preview_type` TINYINT UNSIGNED NOT NULL DEFAULT '0'",array());



    $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('TooltipManager'));
    $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array('TooltipManager','1'));

}



