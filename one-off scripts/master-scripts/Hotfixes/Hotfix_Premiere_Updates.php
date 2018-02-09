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



//Disable holder field for Local Move Details within Estimates module
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE columnname='cf_1003' OR columnname='cf_1007'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel='LBL_POTENTIALS_DESTINATIONADDRESSDESCRIPTION' WHERE fdieldlabel='LBL_POTENTIALS_DESTIANTIONADDRESSDESCRIPTION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel='LBL_POTENTIALS_PDDELIVER' WHERE fieldlabel='LBL_POTENTIALS_DELIVER'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_crmentity` SET setype='Opportunities' WHERE setype='Potentials'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_crmentity` SET setype='Estimates' WHERE setype='Quotes'");

//Rearrange blocks in Estimates
$blockChanges = array('LBL_QUOTES_SITDETAILS'=>1, 'LBL_QUOTES_ACCESSORIALDETAILS'=>1, 'LBL_DESCRIPTION_INFORMATION'=>0, 'LBL_TERMS_INFORMATION'=>0);
$result = $db->pquery("SELECT tabid FROM `vtiger_tab` WHERE name='Estimates'", array());
$row = $result->fetchRow();
$tabid = $row[0];
foreach ($blockChanges as $label => $subtract) {
    $seqUpdateString = $subtract ? "sequence-2" : "sequence+2";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence=$seqUpdateString WHERE blocklabel='$label' AND (tabid=$tabid || tabid=20)");
}

//Add Users related list in VanlineManager
$vanlineInstance = Vtiger_Module::getInstance('VanlineManager');
$usersInstance = Vtiger_Module::getInstance('Users');
$relationLabel = 'Users';
$vanlineInstance->setRelatedList($usersInstance, $relationLabel, array('Add', 'Select'), 'get_users');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";