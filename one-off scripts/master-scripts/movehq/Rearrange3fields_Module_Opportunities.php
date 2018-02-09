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

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;
//create column agentmanager_id in vtiger_leadsource table
$module = $adb->pquery("SELECT * FROM vtiger_tab WHERE name = ?",array('Opportunities'));

if($adb->num_rows($module) > 0){
    $id = "";
    while ($data = $adb->fetchByAssoc($module)){
        $id = $data['tabid'];
    }
    $adb->pquery("UPDATE `vtiger_field` SET sequence= ? WHERE columnname = ? AND tabid = ?",array('1','contact_id',$id));
    $adb->pquery("UPDATE `vtiger_field` SET sequence= ? WHERE columnname = ? AND tabid = ?",array('2','opportunitystatus',$id));
    $adb->pquery("UPDATE `vtiger_field` SET sequence= ? WHERE columnname = ? AND tabid = ?",array('3','potentialname',$id));
    $adb->pquery("UPDATE `vtiger_field` SET typeofdata = ? WHERE columnname = ? AND tabid = ?",array('V~O','potentialname',$id));
    echo "<br> Rearranged 3 field contact_id,opportunitystatus,potentialname in module Opportunities<br>";
}




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";