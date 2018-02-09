<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

class Opportunities_ParticipatingAgentStatus_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        $this->exposeMethod('update');
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if (!$permission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function update(Vtiger_Request $request)
    {
        $inbox_id = $request->get('inboxid');
        $paid = $request->get('paid');
        $status = $request->get('status');

        $error='';
        $user = Users_Record_Model::getCurrentUserModel();
        $depth = Settings_Roles_Record_Model::getInstanceById($user->getRole())->getDepth();
        if (!empty($status) && !empty($paid) && ($depth==6 || $depth==7)) {
            $db = PearDatabase::getInstance();
            $query = 'UPDATE vtiger_participatingagents SET status=? WHERE participatingagentsid=?';
            $result = $db->pquery("SELECT * FROM `vtiger_participatingagents` WHERE participatingagentsid = ? AND vtiger_participatingagents.deleted = 0", array($paid));
            $row = $result->fetchRow();
            $db->pquery($query, array($status, $user->getId(), $paid));
            if ($status=='3') {
                $group =  $db->pquery("SELECT vtiger_groups.groupid FROM vtiger_agents, vtiger_groups WHERE vtiger_agents.agentname = vtiger_groups.groupname AND vtiger_agents.agentsid=?", array($row['agent_id'], ));
                $group = $group->fetchRow()[0];
                $opp = Vtiger_Record_Model::getInstanceById($row['crmentity_id'])->getData();
                $to;
                if ($opp["sales_person"]!='0') {
                    $to = vtws_getWebserviceEntityId('Users', $opp["sales_person"]);
                } else {
                    $to = vtws_getWebserviceEntityId('Groups', $opp["assigned_user_id"]);
                }
                $data = array(
                    'inbox_message' => 'The Participating Agent Request was declined by '.$user->getDisplayName(),
                    'inbox_priority'=> 'MEDIUM',
                    'inbox_announce'  => '1',
                    'inbox_read'  => '0',
                    'inbox_for_crmentity' => $row['crmentity_id'],
                    'assigned_user_id' => $to,
                    'inbox_from' => vtws_getWebserviceEntityId('Users', $user->getId()),
                    'inbox_type' => 'Participating Agent Declined',
                    'inbox_link' => $paid
                );
                try {
                    $adminToBe = new Users();
                    $current_user = $adminToBe->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                    $cool = vtws_create('Inbox', $data, $current_user);
                    // file_put_contents('logs/devLog.log', "\nNotifying agent Original ".print_r($datum, true),FILE_APPEND);
                    // file_put_contents('logs/devLog.log', "\nNotifying agent Return ".print_r($cool, true),FILE_APPEND);
                } catch (WebServiceException $ex) {
                    file_put_contents('logs/devLog.log', "\n Error:".print_r($ex->getMessage(), true), FILE_APPEND);
                }
            }
            $db->completeTransaction();
        } elseif ($depth!=6 && $depth!=7) {
            $error = 'You do not have sufficient privileges to accept this request.';
        } else {
            $error = 'There was an error saving your request. Please reload the page and try again.';
        }

        $response = new Vtiger_Response();
        $response->addResult($error);
        $response->emit();
    }
}
