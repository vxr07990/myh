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

$sql = "SELECT * FROM `vtiger_agentmanager`";
$result = $db->pquery($sql, []);
while ($row =& $result->fetchRow()) {
    try {
        $matchSql = "SELECT agentsid FROM `vtiger_agents` WHERE agent_number = ? LIMIT 1";
        $matchedAgent = $db->pquery($matchSql, [$row['agency_code']])->fetchRow();
        $data = array(
            'assigned_user_id' => '19x1',
            'agentname' => $row['agency_name'],
            'agent_number' => $row['agency_code'],
            'agent_address1' => $row['address1'],
            'agent_address2' => $row['address2'],
            'agent_city' => $row['city'],
            'agent_state' => $row['state'],
            'agent_zip' => $row['zip'],
            'agent_country' => $row['country'],
            'agent_phone' => $row['phone1'],
            'agent_fax' => $row['fax'],
            'agent_email' => $row['email'],
            //'agent_contacts' => $row[''],//no contacts in agentman
            //'agent_puc' => $row[''],//wut
            //'agent_vanline' => $row[''],//links to vanlines
            'agentmanager_id' => vtws_getWebserviceEntityId('AgentManager', $row['agentmanagerid']),
        );
        $bigD = '<h1>YOU SMELL ME?!</h1>';
        if ($matchedAgent) {
            echo "<br><br>agent already exists; Updating...";
            $data['id'] = vtws_getWebserviceEntityId('Agents', $matchedAgent['agentsid']);
            $updatedAgent = vtws_update($data, $current_user);
            echo "done!<br><br>";
            echo "<br>UPDATED AGENTS RECORD: ".print_r($updatedAgent, true);
        } else {
            echo "<br><br>agent not found; Creating...";
            $newAgent = vtws_create('Agents', $data, $current_user);
            echo "done!<br><br>";
            echo "<br>NEW AGENTS RECORD: ".print_r($newAgent, true);
        }
    } catch (WebServiceException $ex) {
        echo $ex->getMessage();
    }
}

echo "<br><h1>Finished Hotfix Create Agents From Agent Manager</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";