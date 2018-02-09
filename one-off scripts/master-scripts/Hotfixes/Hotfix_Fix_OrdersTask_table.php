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

// Remove the relationship between the agent manager and the field
$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
$field = Vtiger_Field::getInstance('participating_agent', $moduleInstance);
if($field){
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_fieldmodulerel` WHERE `fieldid` = '$field->id' AND `module` = 'OrdersTask' AND `relmodule` = 'AgentManager' AND `status` IS NULL AND `sequence` IS NULL");
}

// Updated the records that are wrongly related to Agent Manager Id

$db = PearDatabase::getInstance();

$result = $db->pquery("SELECT agentsid, agentmanager_id FROM vtiger_agents WHERE agentmanager_id IS NOT NULL AND agentmanager_id <> ''",array());
if ($db->num_rows($result) > 0) {
	while ($arr = $db->fetch_array($result)) {
		$res = $db->pquery("SELECT orderstaskid FROM vtiger_orderstask ot INNER JOIN vtiger_crmentity cr ON ot.orderstaskid = cr.crmid WHERE cr.deleted = 0 AND ot.participating_agent = ?",array($arr['agentmanager_id']));
		if ($db->num_rows($res) > 0) {
			while ($arr2 = $db->fetch_array($res)) {
				$db->pquery("UPDATE vtiger_orderstask SET participating_agent = ? WHERE orderstaskid = ?",array($arr['agentsid'],$arr2['orderstaskid']));
			}
		}	
	}
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";