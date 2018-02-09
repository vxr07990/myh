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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Update.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Create Agents From Agent Manager</h1><br>\n";
$db = PearDatabase::getInstance();
$user = new Users();
$current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

//@TODO: THis needs to be done, there's nothing unique at all and no relation from vtiger_agents to the vtiger_vanlinemanager.
$vanline_name = 'Graebel Van Lines LLC';
$vanlineSql = "SELECT * FROM `vtiger_vanlinemanager` WHERE `vanline_name`=? lIMIT 1";
$vanlineInfo = $db->pquery($vanlineSql, [$vanline_name])->fetchRow();
$relatedVanlineManager = vtws_getWebserviceEntityId('VanlineManager', $vanlineInfo['vanlinemanagerid']);

$sql = "SELECT vtiger_agents.* FROM vtiger_agents
        JOIN `vtiger_crmentity` ON (vtiger_crmentity.crmid = vtiger_agents.agentsid)
        WHERE `deleted`=0 AND `setype`='Agents'";
$result = $db->pquery($sql, []);
while ($row =& $result->fetchRow()) {
    try {
        $matchSql = "SELECT * FROM vtiger_agentmanager
                    JOIN vtiger_crmentity ON (vtiger_crmentity.crmid = vtiger_agentmanager.agentmanagerid)
                    WHERE agency_code = ? AND deleted=0";
        $matchedAgent     = $db->pquery($matchSql, [$row['agent_number']])->fetchRow();
        $agentManagerData = [
            'assigned_user_id' => '19x1',
            'agency_name'      => $row['agentname'],
            'agency_code'      => $row['agent_number'],
            'address1'         => $row['agent_address1'],
            'address2'         => $row['agent_address2'],
            'city'             => $row['agent_city'],
            'state'            => $row['agent_state'],
            'zip'              => $row['agent_zip'],
            'country'          => $row['agent_country'],
            'phone1'           => $row['agent_phone'],
            'fax'              => $row['agent_fax'],
            'email'            => $row['agent_email'],
            'vanline_id'       => $relatedVanlineManager,
            //'agent_contacts' => $row[''],//no contacts in agentman
            //'agent_puc' => $row[''],//wut
            //'agent_vanline' => $row[''],//links to vanlines
            //'agentmanager_id' => vtws_getWebserviceEntityId('AgentManager', $row['agentmanagerid']),
        ];
        $agentsData       = [
            'assigned_user_id' => '19x1',
            'agentname'        => $row['agentname'],
            'agent_number'     => $row['agent_number'],
            'agent_address1'   => $row['agent_address1'],
            'agent_address2'   => $row['agent_address2'],
            'agent_city'       => $row['agent_city'],
            'agent_state'      => $row['agent_state'],
            'agent_zip'        => $row['agent_zip'],
            'agent_country'    => $row['agent_country'],
            'agent_phone'      => $row['agent_phone'],
            'agent_fax'        => $row['agent_fax'],
            'agent_email'      => $row['agent_email'],
            'id'               => vtws_getWebserviceEntityId('Agents', $row['agentsid'])
        ];

        //create an agentmanager record to match the agents record
        if (!$matchedAgent) {
            echo "<br><br>agentManager record not found; Creating...";
            print "\n JG HERE (Hotfix_CreateAgentManagerFromAgents.php:".__LINE__.") agentManagerData : ".print_r($agentManagerData, true);
            //$newAgent = vtws_create('AgentManager', $agentManagerData, $current_user);
            echo "done!<br><br>";
            echo "<br>NEW AgentManager RECORD: ".print_r($newAgent, true);
            $agentsData['agentmanager_id'] = $newAgent['id'];
        } else {
            echo "<br><br>agentManager already exists; Linking AgentManager to Agents record...";
            $agentsData['agentmanager_id'] = vtws_getWebserviceEntityId('AgentManager', $matchedAgent['agentmanagerid']);
        }

//        print "\n JG HERE (Hotfix_CreateAgentManagerFromAgents.php:".__LINE__.") agentsData : ".print_r($agentsData, true);
//        //$updatedAgent = vtws_update($agentsData, $current_user);
//        echo "done!<br><br>";
//        echo "<br>UPDATED AGENTS RECORD: ".print_r($updatedAgent, true);
    } catch (WebServiceException $ex) {
        echo $ex->getMessage();
    }
}
echo "<br><h1>Finished Hotfix Create Agents From Agent Manager</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";