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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;
$sql = "SELECT * FROM vtiger_guestmodulerel JOIN vtiger_blocks ON (vtiger_guestmodulerel.blockid = vtiger_blocks.blockid) WHERE hostmodule = ? AND vtiger_blocks.blocklabel = ?";
$rs = $adb->pquery($sql,array('Leads','LBL_MOVEROLES_INFORMATION'));
if($adb->num_rows($rs) == 0){
    $rs1 = $adb->pquery("SELECT * FROM vtiger_blocks WHERE blocklabel = ?",array('LBL_MOVEROLES_INFORMATION'));
    if($adb->num_rows($rs1) > 0){
        while ($data = $adb->fetchByAssoc($rs1)){
            $blockid = $data['blockid'];
        }
        $adb->pquery("INSERT INTO vtiger_guestmodulerel (hostmodule, guestmodule, blockid,active) VALUES(?,?,?,?)",array('Leads', 'MoveRoles', $blockid,1));
        echo "Add record vtiger_guestmodulerel success";
    }else{
        echo 'Block LBL_MOVEROLES_INFORMATION does exist';
    }
}else{
    echo "Record table vtiger_guestmodulerel exist";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";