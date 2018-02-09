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
$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('ListviewColors');
if ($moduleInstance) {
    echo "<h2>ListviewColors module already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ListviewColors';
    $moduleInstance->save();
    $isNew = true;
}
if($isNew){
    $db = PearDatabase::getInstance();

    if (!Vtiger_Utils::CheckTable('vte_modules')) {
        $stmt = 'CREATE TABLE `vte_modules` (
                `module`  varchar(50) NOT NULL ,
                `valid`  int(1) NULL ,
                PRIMARY KEY (`module`));';
        $db->pquery($stmt);
        echo "Created table vte_modules success <br>";
    } else {
        echo 'vte_modules already exists' . PHP_EOL . '<br>';
    }
    if (!Vtiger_Utils::CheckTable('vte_listview_colors')) {
        $stmt = 'CREATE TABLE `vte_listview_colors` ( `id` int(11) NOT NULL AUTO_INCREMENT, `modulename` varchar(100) DEFAULT NULL, `condition_name` text, `text_color` varchar(100) DEFAULT NULL, `bg_color` varchar(100) DEFAULT NULL, `related_record_color` varchar(100) DEFAULT NULL, `conditions` text, `conditions_count` int(10) DEFAULT \'0\', `priority` int(10) DEFAULT \'1\', `status` varchar(50) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
        $db->pquery($stmt);
        echo "Created table vte_listview_colors success <br>";
    } else {
        echo 'vte_listview_colors already exists' . PHP_EOL . '<br>';
    }
    $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array('ListviewColors'));
    $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array('ListviewColors', '1'));


    //Fix issue: module does not work with Non Admin user_error
    $adb->pquery("UPDATE vtiger_links SET tabid=0 WHERE linklabel = ? ", array($widgetName));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";