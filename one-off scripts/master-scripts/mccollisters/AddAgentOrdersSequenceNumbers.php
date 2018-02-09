<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/24/2017
 * Time: 3:51 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

include_once('includes/main/WebUI.php');

$db = &PearDatabase::getInstance();

$res = $db->pquery('SELECT 1 FROM vtiger_modentity_num WHERE agentmanagerid IS NOT NULL');
if($db->num_rows($res))
{
    return;
}

$res = $db->pquery('SELECT cur_id FROM vtiger_modentity_num WHERE semodule=? AND active=1 AND agentmanagerid IS NULL',
                   ['Orders']);

$cur = $res->fetchRow()['cur_id'];

// McCollister's Auto
$mcauto = $db->pquery('SELECT agentmanagerid FROM vtiger_agentmanager WHERE agency_code=?',
                   ['10001'])->fetchRow()[0];

// Mecum Auctions
$mecum = $db->pquery('SELECT agentmanagerid FROM vtiger_agentmanager WHERE agency_code=?',
                      ['10002'])->fetchRow()[0];


$rec = Vtiger_Record_Model::getCleanInstance('AgentSequenceNumber');
$rec->set('agent_sn_agentmanagerid', $mcauto);
$rec->set('agent_sn_modulename', 'Orders');
$rec->set('agent_sn_format', 'MCAT__y__%\'05d');
$rec->set('assigned_user_id', 1);
$rec->save();

$rec = Vtiger_Record_Model::getCleanInstance('AgentSequenceNumber');
$rec->set('agent_sn_agentmanagerid', $mecum);
$rec->set('agent_sn_modulename', 'Orders');
$rec->set('agent_sn_format', 'MCMT__y__%\'05d');
$rec->set('assigned_user_id', 1);
$rec->save();

$id1 = $db->getUniqueID('vtiger_modentity_num');

$db->pquery('INSERT INTO vtiger_modentity_num (num_id,semodule,start_id,cur_id,active,agentmanagerid) 
            VALUES (?,?,?,?,?,?)',
            [$id1, 'Orders', 1, $cur, 1, $mcauto]);

$id2 = $db->getUniqueID('vtiger_modentity_num');

$db->pquery('INSERT INTO vtiger_modentity_num (num_id,semodule,start_id,cur_id,active,agentmanagerid) 
            VALUES (?,?,?,?,?,?)',
            [$id2, 'Orders', 1, 1, 1, $mecum]);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";