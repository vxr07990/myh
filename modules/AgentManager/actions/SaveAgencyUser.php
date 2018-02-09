<?php

require_once('modules/Emails/mail.php');

class AgentManager_SaveAgencyUser_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        //TODO: Check role permissions to ensure that only valid users may add a new user to an agency

        $sql = "SELECT id, user_name, accesskey FROM `vtiger_users` WHERE id=?";
        $result = $db->pquery($sql, array(1));

        $row = $result->fetchRow();

        $id = $row[0];
        $userName = $row[1];
        $accesskey = $row[2];

        $webserviceURL = getenv('WEBSERVICE_URL');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$userName);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        $challengeResponse = json_decode($curlResult);

        $generatedkey = md5($challengeResponse->result->token.$accesskey);
        $post_string = "operation=login&username=".$userName."&accessKey=".$generatedkey;

        $curlResult = $this->curlPOST($post_string, $webserviceURL);

        $loginResponse = json_decode($curlResult);

        $sessionId = $loginResponse->result->sessionName;
        $crmUserId = $loginResponse->result->userId;

        //Generate a new random password for the user that will be emailed after user is created
        $newPassword = $this->generatePassword();

        $newUserInfo = array();
        $newUserInfo['user_name'] = $request->get('email1');
        $newUserInfo['user_password'] = $newPassword;
        $newUserInfo['confirm_password'] = $newPassword;
        $newUserInfo['first_name'] = $request->get('first_name');
        $newUserInfo['last_name'] = $request->get('last_name');
        $newUserInfo['roleid'] = $request->get('roleid');
        $newUserInfo['email1'] = $request->get('email1');

        $post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($newUserInfo)."&elementType=Users";
        $curlResult = $this->curlPOST($post_string, $webserviceURL);

        $curlResultArray = json_decode($curlResult);

        if ($curlResultArray->success == 1) {
            $newUserId = substr(strstr($curlResultArray->result->id, 'x'), 1);
            $newUserId = intval($newUserId);

            $sql = "SELECT * FROM `vtiger_user2agency` WHERE userid=? AND agency_code=?";
            $agentId = $request->get('srcRecord');
            $result = $db->pquery($sql, array($newUserId, $agentId));

            $row = $result->fetchRow();
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_user2agency` VALUES (?,?)";
                $result = $db->pquery($sql, array($newUserId, $agentId));
                unset($params);
            }

            //grab agent name
            $sql = "SELECT agency_name FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
            $result = $db->pquery($sql, array($agentId));
            $row = $result->fetchRow();
            $agentName = $row[0];

            //grab agent group id
            $sql = "SELECT groupid FROM `vtiger_groups` WHERE groupname=?";
            $result = $db->pquery($sql, array($agentName));
            $row = $result->fetchRow();
            $groupId = $row[0];

            //check to see if user already exists in users2group
            $sql = "SELECT groupid, userid FROM `vtiger_users2group` WHERE groupid=? AND userid=?";
            $result = $db->pquery($sql, array($groupId, $newUserId));
            $row = $result->fetchRow();
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_users2group` VALUES (?,?)";
                $result = $db->pquery($sql, array($groupId, $newUserId));
            }

            //Send an email to provided email address with login credentials
            $loginURL = getenv('SITE_URL');

            if (getenv('IGC_MOVEHQ')) {
                $softwareName = 'MoveHQ';
                $developerName = 'WIRG';
                $developerSite = 'www.mobilemover.com';
                $logo = '<img src="test/logo/MoveHQ.png" title="MoveHQ.png" alt="MoveHQ.png">';
                $website = 'www.mobilemover.com';
                $supportTeam = 'MoveHQ Support Team';
                $supportEmail = getenv('SUPPORT_EMAIL_ADDRESS');
            } else {
                $softwareName = 'MoveCRM';
                $developerName = 'IGC Software';
                $developerSite = 'www.igcsoftware.com';
                $logo = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
                $website = 'www.igcsoftware.com';
                $supportTeam = 'MoveCRM Support Team';
                $supportEmail = getenv('SUPPORT_EMAIL_ADDRESS');
            }

            global $vtiger_current_version;

            $subject = 'Welcome to '.$softwareName;
            $message = '<div style="border:2px solid #204e81;"><div style="padding:10px;background-color:#fafafb;">Thank you for using '.$softwareName.'. To get started, proceed to '.$loginURL." and login with the credentials provided below.<br /> <br />Username: ".$newUserInfo['user_name']."<br />Password: ".$newPassword.'<br></div>		<div style="background-color:#204e81;padding:5px;vertical-align:middle">
			<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by '.$softwareName.' '.$vtiger_current_version.'   &copy; '.date("Y").' <a href="http://'.$developerSite.'" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">'.$developerName.'</a></span></p>
			</div></div>';

            $mail_status = send_mail('AgentManager', $request->get('email1'), 'MoveCRM Support', getenv('SUPPORT_EMAIL_ADDRESS'), $subject, $message, '', '', '', '', '', true);
        }
        $response = new Vtiger_Response();
        $response->setResult('this is a response');
        $response->emit();
    }

    public function getObjectTypeId($db, $modName)
    {
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id').'x';
    }

    public function curlPOST($post_string, $webserviceURL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webserviceURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return $curlResult;
    }

    protected function generatePassword()
    {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^';
        $pw = '';
        $count = strlen($charset);
        for ($i=0; $i<8; $i++) {
            $pw .= $charset[mt_rand(0, $count-1)];
        }
        return $pw;
    }
}
