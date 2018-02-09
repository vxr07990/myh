<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'Helpers.php';

class Settings_MoveEasyIntegration_Index_View extends Settings_Vtiger_Index_View
{

    /*
     * @TODO: Need to add the website field to agent manager and make it required. The database field is already created with add_moveeasy_integration.php
     */

    private $db;

    public function __construct()
    {
        $this->exposeMethod('view');
        $this->db = PearDatabase::getInstance();
    }

    public function process(Vtiger_Request $request)
    {
        global $current_user;

        if(!getenv('MOVEEASY_ENABLED'))
        {
            throw new Exception("Module not enabled, please contact IGC Support.", -1);
        }


        $agents = MEZHelpers::checkForMultipleAgents($current_user);
        if($request->get('enableInt'))
        {
            if(getenv('MOVEEASY_AGENT_LEVEL'))
            {
                $this->checkPermissions($agents, $request->get('agentID'));
                $this->enableIntegration($request, $request->get('agentID'));
            }
            else
            {
                $this->enableIntegration($request, 1);
            }
        }
        else
        {
            $this->view($request, $agents);
        }
    }

    public function view(Vtiger_Request $request, $agents)
    {
        if(getenv('MOVEEASY_AGENT_LEVEL'))
        {
            //check for permission
            if ($request->get('agentID') != "")
            {
                $this->checkPermissions($agents, $request->get('agentID'));
            }

            //check for vanline only users if the instance is running on agent level
            if ($agents === false)
            {
                throw new Exception("User is a vanline user, must be assigned to atleast one agent!", -1);
            }

            // if there is an array of agents, and the agent ID is not set, allow users to select the agent.
            if (is_array($agents) && $request->get('agentID') == "")
            {
                $agents = MEZHelpers::changeAgentIDSToSomethingReadable($agents);
                $viewer = $this->getViewer($request);
                $viewer->assign('AGENTS', $agents);
                return $viewer->view('selectAgent.tpl', $request->getModule(false));
            }

            //sends the agent to the correct agentID page depending on if the user is assigned to multiple agents or not.
            if (is_array($agents))
            {
                return $this->sentToCorrectPage($request, $request->get('agentID'));
            }
            else
            {
                return $this->sentToCorrectPage($request, $agents);
            }
        }
        else
        {
            return $this->sentToCorrectPage($request, 1);
        }
    }

    public function checkPermissions($agents, $agentID)
    {
        if(is_array($agents)){
            if(!in_array($agentID, $agents))
            {
                throw new Exception(vtranslate('LBL_PERMISSION_DENIED'), -1);
            }
        }
        else
        {
            if($agents != $agentID)
            {
                throw new Exception(vtranslate('LBL_PERMISSION_DENIED'), -1);
            }
        }
    }

    public function sentToCorrectPage(Vtiger_Request $request, $agent)
    {
        //checkes if the agent $agent is subscribed to moveeasy
        $subscribed = MEZHelpers::checkSubscription($agent);
        if($subscribed == 0)
        {
            //if the agent is not subscribed, we show the agent not subscribed page
            return $this->showIndex($request, $agent, 'unsubscribed');
        }
        else
        {
            //if the agent is subscribed, we show them the iframe.
            return $this->showIndex($request, $agent, 'subscribed');
        }
    }

    public function showIndex(Vtiger_Request $request, $agentID, $show)
    {

        $viewer = $this->getViewer($request);

        //if the user is subscribed, we set the iframe here
        if($show == 'subscribed')
        {
            $query = $this->db->pquery("SELECT * FROM vtiger_moveeasy_integration WHERE agentID=?", array($agentID));
            $result = $query->fetchRow();
            $url = $result['iframe'];
            $viewer->assign('URL', $url);
        }

        $viewer->assign('ASKFOREMAIL', !getenv('MOVEEASY_AGENT_LEVEL'));
        $viewer->assign('agentID', $agentID);
        $viewer->assign('SHOW', $show);
        return $viewer->view('index.tpl', $request->getModule(false));
    }


    private function enableIntegration(Vtiger_Request $request, $agentID)
    {
        if(!MEZHelpers::checkSubscription($agentID))
        {
            if(getenv('MOVEEASY_AGENT_LEVEL'))
            {
                $userInfo = [
                    'fname' => $request->get('fname'),
                    'mname' => $request->get('mname'),
                    'lname' => $request->get('lname')
                ];
            }
            else
            {
                $userInfo = [
                    'fname' => $request->get('fname'),
                    'mname' => $request->get('mname'),
                    'lname' => $request->get('lname'),
                    'email' => $request->get('email')
                ];
            }

            $sendInfo = $this->sendInfoToMoveEasy($userInfo, $agentID, getenv('MOVEEASY_AGENT_LEVEL'));

            if($sendInfo['success']){
                $params = [
                  $sendInfo['domain_url'],
                    1,
                    $agentID,
                    $sendInfo['uid'],
                    $sendInfo['token'],
                    $sendInfo['iframe_tag']

                ];

                $this->db->pquery("INSERT INTO vtiger_moveeasy_integration (`domain`, isSubscribed, agentID, uid, token, iframe) VALUES (?, ?, ?, ?, ?, ?)", $params);
                header('Location:  index.php?module=MoveEasyIntegration&parent=Settings&view=Index&agentID='.$agentID);
            }
            else
            {
                $viewer = $this->getViewer($request);
                $viewer->assign('SHOW', 'missinginfo');
                $viewer->assign('ERROR', $sendInfo['error']);
                $viewer->assign('agentID', $agentID);
                return $viewer->view('index.tpl', $request->getModule(false));
            }
        }
    }

    private function sendInfoToMoveEasy($userInfo, $agentID, $agentLevel)
    {
        //$url = 'http://quote-dev.moveeasy.com/api/sites/crm_integrate/';
        $url = getenv('MOVEEASY_API');

        if($url == '')
        {
            throw new Exception("Somethings misconfigured, please contact IGCSupport", -1);
        }
        else
        {
            $orgDetails = MEZHelpers::getAgencyDetails($agentID, $agentLevel);

            if($orgDetails['result']) {
                if($agentLevel)
                {
                    $user_detail = array(
                        'email' => $orgDetails['email'],
                        'first_name' => $userInfo['fname'],
                        'middle_name' => $userInfo['mname'],
                        'last_name' => $userInfo['lname'],
                        'phone_no' => $orgDetails['phone']
                    );
                }
                else
                {
                    $user_detail = array(
                        'email' => $userInfo['email'],
                        'first_name' => $userInfo['fname'],
                        'middle_name' => $userInfo['mname'],
                        'last_name' => $userInfo['lname'],
                        'phone_no' => $orgDetails['phone']
                    );
                }

                $company_detail = array(
                    'website' => $orgDetails['website'],
                    'company_name' => $orgDetails['company_name'],
                    'logo' => ''
                );

                $fields = array(
                    'token' => getenv('MOVEEASY_TOKEN'),
                    'crm_name' => getenv('MOVEEASY_APIUSER'),
                    'user_detail' => $user_detail,
                    'company_detail' => $company_detail
                );

                //print_r($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);

                if($errno = curl_errno($ch)) {
                    $error_message = curl_strerror($errno);
                    $return = [
                        'success' => false,
                        'error' => "cURL error ({$errno}):\n {$error_message}"
                    ];
                    curl_close($ch);
                    return $return;
                }

                curl_close($ch);

                $json = json_decode($result);

                if(!is_object($json))
                {
                    $return = [
                        'success' => false,
                        'error' => "Object reference not set to an instance of an object."
                    ];
                    return $return;
                }

                if ($json->message == 'Successfully Created') {
                    $return = [
                        'success' => true,
                        'iframe_tag' => $json->iframe_tag,
                        'domain_url' => $json->domain_url,
                        'uid' => $json->uid,
                        'token' => $json->token
                    ];
                }
                else
                {
                    $message = $json->message;
                    if($message == ''){
                        $message = "An unknown error occurred, please contact support.";
                    }

                    $return = [
                        'success' => false,
                        'error' => $message
                    ];
                }

                return $return;
            }
            else
            {
                $return['success'] = false;
                $return['error'] = $orgDetails['reason'];
                if($return['error'] == ''){
                    $return['error'] = "An unknown error occurred, please contact support.";
                }

                return $return;
            }
        }
    }
}
