<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();
$leadsModule = Vtiger_Module::getInstance('Leads');
$leadsCommodity = Vtiger_Field::getInstance('commodities', $leadsModule);
$oppsModule = Vtiger_Module::getInstance('Opportunities');
$oppsCommodity = Vtiger_Field::getInstance('commodities', $oppsModule);
$accsModule = Vtiger_Module::getInstance('Accounts');
$accsCommodity = Vtiger_Field::getInstance('commodities', $accsModule);

$failed = false;
if($leadsCommodity && $oppsCommodity && $accsCommodity) {
    $sql = "SELECT cfmid FROM `vtiger_convertleadmapping` WHERE leadfid=? AND potentialfid=?";
    $result = $db->pquery($sql, [$leadsCommodity->id, $oppsCommodity->id]);
    if($result && $db->num_rows($result) == 0) {
        $sql = "INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) VALUES (?,?,?,?,?)";
        $db->pquery($sql, [$leadsCommodity->id, $accsCommodity->id, 0, $oppsCommodity->id, 1]);
    } else {
        $failed = true;
    }
} else {
    $failed = true;
}
if ($failed) {
    if (function_exists("removeScriptFromVersionLogs")) {
        removeScriptFromVersionLogs(__FILE__);
    }
}
print "\e[32mFINISH: " . __FILE__ . "<br />\n\e[0m";
