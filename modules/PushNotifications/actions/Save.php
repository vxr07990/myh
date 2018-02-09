<?php

require_once('libraries/nusoap/nusoap.php');

class PushNotifications_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($request->get('record') != null) {
            header('Location: index.php?module=PushNotifications&view=Detail&record='.$request->get('record').'&unsaved=1');
            return;
        }
        $request->set('reportSave', 1);
        parent::process($request);

        // Generate and send SOAP request to push out notification
        $wsdlURL = getenv('SURVEY_SYNC_URL');
        $soapclient = new soapclient2($wsdlURL, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        $userId = $request->get('assigned_user_id');

        $db = PearDatabase::getInstance();
        $sql = "SELECT user_name, accesskey FROM `vtiger_users` WHERE id=?";
        $result = $db->pquery($sql, [$userId]);

        $username = $result->fields['user_name'];
        $accesskey = $result->fields['accesskey'];

        $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
        $result = $db->pquery($sql, [$request->get('agentId')]);

        $vanlineIds = array();
        $agentIds = array();

        if ($result->fields['setype'] == 'VanlineManager') {
            $vanlineIds[] = $request->get('agentId');
        } else {
            $agentIds[] = $request->get('agentId');
        }

        $address = getenv('SITE_URL');
        $message = $request->get('message');

        $wsdlParams = [
            'username' => $username,
            'accessKey' => $accesskey,
            'message' => $request->get('message'),
            'vanlineIDs ' => $vanlineIds,
            'agentIDs' => $agentIds,
            'address' => $address
        ];
        file_put_contents('logs/devLog.log', "\n WsdlParams : ".print_r($wsdlParams, true), FILE_APPEND);

        $soapRequest = "
    <PushNotificationMessage xmlns=\"http://igcsoftware.com/reloCRMSync\">
      <username>$username</username>
      <accessKey>$accesskey</accessKey>
      <message>$message</message>
      ";
        if (sizeof($vanlineIds) == 0) {
            $soapRequest .= "<vanlineIDs xmlns:d4p1=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\" />\n      ";
        } else {
            $soapRequest .= "<vanlineIDs xmlns:d4p1=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">\n";
            foreach ($vanlineIds as $vanlineId) {
                $soapRequest .= "        <d4p1:string>$vanlineId</d4p1:string>\n";
            }
            $soapRequest .= "      </vanlineIDs>\n      ";
        }
        if (sizeof($agentIds) == 0) {
            $soapRequest .= "<agentIDs xmlns:d4p1=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\" />";
        } else {
            $soapRequest .= "<agentIDs xmlns:d4p1=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">\n      ";
            foreach ($agentIds as $agentId) {
                $soapRequest .= "        <d4p1:string>$agentId</d4p1:string>\n";
            }
            $soapRequest .= "      </agentIDs>\n      ";
        }
        $soapRequest .= "<address>$address</address>
    </PushNotificationMessage>";

        $msg = $soapclient->serializeEnvelope($soapRequest);

        $soapResult = $soapclient->send($msg, 'http://igcsoftware.com/reloCRMSync/IIntegrationService/PushNotificationMessage');

//        $soapResult = $soapProxy->PushNotificationMessage($wsdlParams);
//        //$soapResult = $soapclient->call('PushNotificationMessage', array('parameters'=>$wsdlParams));
        file_put_contents('logs/devLog.log', "\n SoapResult : ".print_r($soapResult, true), FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n SoapRequest : ".print_r($soapclient->request, true), FILE_APPEND);

        header('Location: index.php?module=PushNotifications&view=Detail&record='.$request->get('record'));
    }
}
