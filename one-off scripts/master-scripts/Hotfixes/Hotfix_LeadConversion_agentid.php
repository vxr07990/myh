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

$leadsField = Vtiger_Field::getInstance('agentid', Vtiger_Module::getInstance('Leads'));
$oppsField  = Vtiger_Field::getInstance('agentid', Vtiger_Module::getInstance('Opportunities'));
$accsField  = Vtiger_Field::getInstance('agentid', Vtiger_Module::getInstance('Accounts'));
$contField  = Vtiger_Field::getInstance('agentid', Vtiger_Module::getInstance('Contacts'));

if($leadsField && $oppsField && $accsField && $contField) {
    if(!$db) {
        $db = PearDatabase::getInstance();
    }
    $sql = "SELECT cfmid FROM `vtiger_convertleadmapping` WHERE leadfid=? AND accountfid=? AND contactfid=? AND potentialfid=?";
    $result = $db->pquery($sql, [$leadsField->id, $accsField->id, $contField->id, $oppsField->id]);
    if($db->num_rows($result) == 0) {
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) 
                                        VALUES (".$leadsField->id.",".$accsField->id.",".$contField->id.",".$oppsField->id.",0)");
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";