<?php
require_once('libraries/nusoap/nusoap.php');
class Quotes_GetReport_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * if you see spot the difference let me know.
    function process(Vtiger_Request $request) {
        $wsdlURL = 'https://print.moverdocs.com/Base/IGCReportingService.asmx?WSDL';

        $soapclient = new soapclient2($wsdlURL, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        if($request->get('requestType') === 'GetAvailableReports') {
            $userId = Users_Record_Model::getCurrentUserModel()->getId();
            $db = PearDatabase::getInstance();
            $sql = "SELECT vanline_id, custom_reports_pw FROM `vtiger_users` WHERE id=?";
            $params[] = $userId;
            $result = $db->pquery($sql, $params);
            $row = $result->fetchRow();
            $vanLineId = $row[0];
            $customReportsPassword = $row[1];

            $wsdlParams = array();
            $wsdlParams['pricingOption'] = 'INTERSTATE';
            $wsdlParams['estimateType'] = 'Default';
            $wsdlParams['vanLineId'] = $vanLineId;
            $wsdlParams['isIntrastate'] = false;
            $wsdlParams['customReportsPassword'] = $customReportsPassword;

            $soapResult = $soapProxy->getAvailableReportsCheckCustom($wsdlParams);
            $xml = base64_decode($soapResult['GetAvailableReportsCheckCustomResult'], true);
            if(!$xml || substr($xml, 0, 5) != "<?xml") {
                $info = "<div class='contents'>No reports are available at this time</div>";
            } else {
                $parser = xml_parser_create();
                $values = array();
                $index = array();
                xml_parse_into_struct($parser, $xml, &$values, &$index);
                $buttons = array();
                $currentButton = 0;

                foreach($values as $tagInfo) {
                    if($tagInfo['tag'] == 'REPORT') {
                        if($tagInfo['type'] == 'open') {
                            $buttons[] = array();
                        } else {
                            $currentButton++;
                        }
                    } else if($tagInfo['tag'] == 'REPORT_NAME') {
                        $buttons[$currentButton]['report_name'] = $tagInfo['value'];
                    } else if($tagInfo['tag'] == 'REPORT_ID') {
                        $buttons[$currentButton]['report_id'] = $tagInfo['value'];
                    } else if($tagInfo['tag'] == 'REQUIRES_CUSTOMER_INITIALS') {
                        $buttons[$currentButton]['requires_customer_initials'] = $tagInfo['value'];
                    }
                }

                $info = "<div class='contents'><h2>Available Reports</h2>";

                foreach($buttons as $button) {
                    $info = $info."<button type='button' name='$button[report_id]'>$button[report_name]</button><br /><br />";
                }

                $info = $info."</div>";
            }

            $response = new Vtiger_Response();
            $response->setResult($info);
            $response->emit();
        } else {
            $requestType = $request->get('viewtype');
            $recordId = $request->get('record');
            $reportName = $request->get('reportName');

            include_once('generatexml.php');

            if(!isset($db)) {
                $db = PearDatabase::getInstance();
            }
            $sql = "SELECT id FROM `vtiger_crmentity_seq`";
            $params = array();

            $result = $db->pquery($sql, $params);

            $row = $result->fetchRow();
            $crmIdToWrite = $row[0]+2;

            $wsdlParams = array('reportID'=>$request->get('reportId'), 'byteArray'=>base64_encode($xml));

            $soapResult = $soapProxy->getReport($wsdlParams);

            //Determine correct filepath and write report to PDF
            $currentDateFilepath = date("Y/F/");
            $currentMonth = date("n");
            $currentDay = date("j");
            $currentDayOfWeek = date("w");
            $firstOfMonth = date("w", mktime(0, 0, 0, $currentMonth, 1));
            $weekNum = floor((($currentDay+($firstOfMonth-1))/7)+1);

            $filepath = "storage/".$currentDateFilepath."week".$weekNum;

            if(!is_dir($filepath)) {
                mkdir($filepath, 0777, true);
            }

            $written = file_put_contents($filepath.'/'.$crmIdToWrite.'_'.$reportName.'.pdf', fopen(base64_decode($soapResult['GetReportResult']), 'rb'));

            //Fetch current user's ID and Access Key for webservice
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $currentUserId = $currentUser->getId();

            $sql = "SELECT user_name, accesskey FROM `vtiger_users` WHERE id=?";
            $params[] = $currentUserId;

            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();
            $username = $row[0];
            $accesskey = $row[1];

            //Login to webservice and create new Document with response from MoverDocs
            $webserviceURL = getenv('WEBSERVICE_URL');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$username);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
            $curlResult = curl_exec($ch);
            curl_close($ch);

            $challengeResponse = json_decode($curlResult);

            $generatedkey = md5($challengeResponse->result->token.$accesskey);
            $post_string = "operation=login&username=".$username."&accessKey=".$generatedkey;

            $curlResult = $this->curlPOST($post_string, $webserviceURL);

            $loginResponse = json_decode($curlResult);

            $sessionId = $loginResponse->result->sessionName;
            $userid = $loginResponse->result->userId;

            $folderId = "22x1";
            $filename = $reportName.'.pdf';
            $filetype = 'application/pdf';

            $documentInfo = array('notes_title'=>$reportName, 'filename'=>$filename, 'assigned_user_id'=>$userid, 'filetype'=>$filetype, 'filelocationtype'=>'I', 'filestatus'=>1, 'folderid'=>$folderId, 'filesize'=>$written);
            $post_string = "operation=create&sessionName=".$sessionId."&element=".json_encode($documentInfo)."&elementType=Documents";

            $curlResult = $this->curlPOST($post_string, $webserviceURL);

            $createResponse = json_decode($curlResult);

            $docid = substr($createResponse->result->id, strpos($createResponse->result->id, "x")+1);
            $ctime = $createResponse->result->createdtime;
            $mtime = $createResponse->result->modifiedtime;

            $sql = "SELECT quoteid, potentialid, contactid, accountid FROM `vtiger_quotes` WHERE quoteid=?";
            $params[] = $recordId;
            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();
            $potentialid = $row[1];
            $contactid = $row[2];
            $accountid = $row[3];

            $sql = "INSERT INTO `vtiger_crmentity` (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params[] = $docid+1;
            $params[] = $currentUserId;
            $params[] = $currentUserId;
            $params[] = 'Documents Attachment';
            $params[] = '';
            $params[] = $ctime;
            $params[] = $mtime;

            $result = $db->pquery($sql, $params);
            unset($params);

            $sql = "INSERT INTO `vtiger_attachments` (attachmentsid, name, description, type, path) VALUES (?, ?, ?, ?, ?)";
            $params[] = $docid+1;
            $params[] = $filename;
            $params[] = '';
            $params[] = $filetype;
            $params[] = $filepath."/";

            $result = $db->pquery($sql, $params);
            unset($params);

            $sql = "UPDATE `vtiger_crmentity_seq` SET id=id+1";
            $params = array();

            $result = $db->pquery($sql, $params);

            $sql = "DELETE FROM `vtiger_seattachmentsrel` WHERE crmid=?";
            $params[] = $docid;

            $result = $db->pquery($sql, $params);
            unset($params);

            $sql = "INSERT INTO `vtiger_seattachmentsrel` VALUES (?, ?)";
            $params[] = $docid;
            $params[] = $docid+1;

            $result = $db->pquery($sql, $params);
            unset($params);

            $sql = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid=?";
            $params[] = $recordId;

            $result = $db->pquery($sql, $params);
            unset($params);

            $row = $result->fetchRow();

            $sql = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
            $params[] = $recordId;
            $params[] = $docid;

            $result = $db->pquery($sql, $params);
            unset($params);

            if($contactid != NULL) {
                $sql = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
                $params[] = $contactid;
                $params[] = $docid;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
            if($accountid != NULL) {
                $sql = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
                $params[] = $accountid;
                $params[] = $docid;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
            if($potentialid != NULL) {
                $sql = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
                $params[] = $potentialid;
                $params[] = $docid;

                $result = $db->pquery($sql, $params);
                unset($params);
            }

            $response = new Vtiger_Response();
            $response->setResult($docid);
            $response->emit();
        }
    }

    function curlPOST($post_string, $webserviceURL) {
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
    */
}
