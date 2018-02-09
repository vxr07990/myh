<?php
/**
 * @author            LouReport.php
 * @description       Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact        lrobinson@igcsoftware.com
 * @copyright         IGC Software
 */
require_once('libraries/nusoap/nusoap.php');

class Estimates_GetReportTPGPricelock_Action extends Estimates_QuickEstimate_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/devLog.log', "\n REQUEST TYPE: ".$request->get('requestType'), FILE_APPEND);
        if ($request->get('requestType') === 'GetAvailableReports') {
            $userId      = $request->get('assigned_user_id');
            $recordId    = $request->get('record');
            $requestType = $request->get('type');
            $wsdlURL     = getenv('REPORTS_URL');
            $soapclient  = new soapclient2($wsdlURL, 'wsdl');
            $soapclient->setDefaultRpcParams(true);
            $soapProxy = $soapclient->getProxy();
            $vanLineId = Estimates_Record_Model::getVanlineIdStatic($recordId, '', $userId);
            $db        = PearDatabase::getInstance();
            if ($requestType == 'editview') {
                $effective_tariff = $request->get('effectiveTariff');
            } else {
                $sql    = "SELECT effective_tariff FROM `vtiger_quotes` WHERE quoteid=?";
                $result = $db->pquery($sql, [$recordId]);
                $row    = $result->fetchRow();
                if ($row == null) {
                    throw new Exception('No Estimates record found');
                }
                $effective_tariff = $row[0];
            }
            $isIntrastate          = false;
            $customReportsPassword = '';
            $pricingOption         = '';
            $estimateType          = '';
            if ($vanLineId == 1 || $vanLineId == 9) {
                $sql    = 'SELECT custom_tariff_type FROM `vtiger_tariffmanager` WHERE tariffmanagerid = ?';
                $result = $db->pquery($sql, [$effective_tariff]);
                $row    = $result->fetchRow();
                file_put_contents('logs/devlog.log', "\n effective_tariff : ".$effective_tariff."\n row[0] : ".$row[0], FILE_APPEND);
                switch ($row[0]) {
                    case 'TPG':
                    case 'Allied Express':
                        $pricingOption         = 'INTERSTATE';
                        $estimateType          = 'Binding';
                        $customReportsPassword = 'qlabavl';
                        break;
                    case 'TPG GRR':
                        $pricingOption         = 'INTERSTATE';
                        $estimateType          = 'NotToExceed';
                        $customReportsPassword = 'qlabavl';
                        break;
                    case 'ALLV-2A':
                        $pricingOption         = '10';
                        $estimateType          = 'NonBinding';
                        $customReportsPassword = 'UASavl';
                        break;
                    case 'Pricelock':
                    case 'Blue Express':
                    case 'Truckload Express':
                        $pricingOption         = 'INTERSTATE';
                        $estimateType          = 'Binding';
                        $customReportsPassword = 'qlabnavl';
                        break;
                    case 'Pricelock GRR':
                        $pricingOption         = 'INTERSTATE';
                        $estimateType          = 'NotToExceed';
                        $customReportsPassword = 'qlabnavl';
                        break;
                    case 'NAVL-12A':
                        $pricingOption         = '10';
                        $estimateType          = 'NonBinding';
                        $customReportsPassword = 'UASnavl';
                        break;
                    case '400N Base':
                    case '400N/104G':
                    case '400NG':
                    case 'Intra - 400N':
                        $pricingOption = '11';
                        $estimateType  = 'NonBinding';
                        if ($vanLineId == 9) {
                            $customReportsPassword = 'qlab400navl';
                        } else {
                            $customReportsPassword = 'qlab400n';
                        }
                        break;
                    case 'Intra - 400N':
                        $pricingOption = 'INTERSTATE';
                        $estimateType  = 'Binding';
                        $isIntrastate  = true;
                        if ($vanLineId == 9) {
                            $customReportsPassword = 'qlab400navl';
                        } else {
                            $customReportsPassword = 'qlab400n';
                        }
                        break;
                    case 'Canada Gov\'t':
                    case 'Canada Non-Govt':
                    case 'UAS':
                        $pricingOption = 'INTERSTATE';
                        $estimateType  = 'NonBinding';
                        if ($vanLineId == 9) {
                            $customReportsPassword = 'qlabnavl';
                        } else {
                            $customReportsPassword = 'qlabavl';
                        }
                        break;
                    case 'Autos Only':
                        $pricingOption = '14';
                        $estimateType  = 'Binding';
                        if ($vanLineId == 9) {
                            $customReportsPassword = 'qlabnavl';
                        } else {
                            $customReportsPassword = 'qlabavl';
                        }
                        break;


                }
                $vanLineId = 18;
            }
            file_put_contents('logs/devLog.log', "\n pricingOption: $pricingOption : ", FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n estimateType: $estimateType", FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n vanLineId: $vanLineId", FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n isIntrastate: $isIntrastate", FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n customReportsPassword: $customReportsPassword", FILE_APPEND);
            $wsdlParams                          = [];
            $wsdlParams['pricingOption']         = $pricingOption;
            $wsdlParams['estimateType']          = $estimateType;
            $wsdlParams['vanLineId']             = $vanLineId;
            $wsdlParams['isIntrastate']          = $isIntrastate;
            $wsdlParams['customReportsPassword'] = $customReportsPassword;

            //TFS 24669: according to Robbie, this should ALWAYS be true if the destination and origin state are the same.
            if ($request->get('origin_state') == $request->get('destination_state') && $request->get('origin_state') != '') {
                $wsdlParams['isIntrastate'] = 'true';
            } elseif ($request->get('record') != '') {
                $estimate = Vtiger_Record_Model::getInstanceById($request->get('record'), 'Estimates');
                if ($estimate->get('origin_state') == $estimate->get('destination_state') && $estimate->get('origin_state') != '') {
                    $wsdlParams['isIntrastate'] = 'true';
                }
            }

            file_put_contents('logs/devLog.log', "\n wsdlParams1 : ".print_r($wsdlParams, true), FILE_APPEND);
            $soapResult = $soapProxy->getAvailableReportsCheckCustom($wsdlParams);
            file_put_contents('logs/devLog.log',
                              "\n soapResult for get reports : ".print_r($soapResult, true),
                              FILE_APPEND);
            $xml = base64_decode($soapResult['GetAvailableReportsCheckCustomResult'], true);
            if (!$xml || substr($xml, 0, 5) != "<?xml") {
                $info = "<div class='contents'>No reports are available at this time</div>";
            } else {
                $parser = xml_parser_create();
                $values = [];
                $index  = [];
                xml_parse_into_struct($parser,
                                      $xml, /*&*/
                                      $values, /*&*/
                                      $index);
                $buttons       = [];
                $currentButton = 0;
                foreach ($values as $tagInfo) {
                    if ($tagInfo['tag'] == 'REPORTS' && $tagInfo['type'] == 'open' && $tagInfo['level'] == 1 && isset($tagInfo['attributes'])) {
                        $serviceAddress = $tagInfo['attributes']['SERVICE_ADDRESS'];
                    }
                    if ($tagInfo['tag'] == 'REPORT') {
                        if ($tagInfo['type'] == 'open') {
                            $buttons[] = [];
                        } else {
                            $currentButton++;
                        }
                    } elseif ($tagInfo['tag'] == 'REPORT_NAME') {
                        $buttons[$currentButton]['report_name'] = $tagInfo['value'];
                    } elseif ($tagInfo['tag'] == 'REPORT_ID') {
                        $buttons[$currentButton]['report_id'] = $tagInfo['value'];
                    } elseif ($tagInfo['tag'] == 'REQUIRES_CUSTOMER_INITIALS') {
                        $buttons[$currentButton]['requires_customer_initials'] = $tagInfo['value'];
                    }
                }
                $info = "<table class='contents table table-bordered'><tr><th class='blockheader'>Available Reports</th></tr>";
                $info = $info."<tr><td style='padding: 5px;'><input type='checkbox' name='includeDOV'>  Include DOV?</td></tr>";
                if ($serviceAddress) {
                    $info .= '<input type="hidden" name="wsdlURL" value="'.urlencode($serviceAddress.'?wsdl').'">';
                }
                foreach ($buttons as $button) {
                    if ($button[report_name] == 'Inventory') {
                        continue;
                    }
                    $info = $info."<tr><td><button type='button' name='$button[report_id]'>$button[report_name]</button></td></tr>";
                }

                $info = $info."</table>";
            }
            $response = new Vtiger_Response();
            $response->setResult($info);
            $response->emit();
        } else {
            $recordId    = $request->get('record');
            $reportName  = $request->get('reportName');
            $requestType = $request->get('type');
            $wsdlURL     = $request->get('wsdlURL');
            $dov         = $request->get('includeDOV');
            if ($requestType == 'editview') {
                //save record before generating xml
                $request->set('reportSave', '1');
                $saveAction = new Estimates_Save_Action;
                file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Preparing to call saveAction->process\n", FILE_APPEND);
                $saveAction->process($request);
            }
            /*************************Get the Variables************************/
            if (empty($wsdlURL)) {
                $wsdlURL = getenv('REPORTS_URL');
            }
            $arr = MoveCrm\arrayBuilder::buildArray($recordId, false, true, false, $dov);
            $soapResult = $this->generateSoapResult($wsdlURL, $request->get('reportId'), $recordId, $arr);
            $docid = $this->processReportsResponse($recordId, $reportName, $soapResult['GetReportResult']);
//            file_put_contents('logs/devLog.log', "\n wsdlURL : ".$wsdlURL, FILE_APPEND);
//            $soapclient = new soapclient2($wsdlURL, 'wsdl');
//            $soapclient->setDefaultRpcParams(true);
//            $soapProxy  = $soapclient->getProxy();
//            $vanLineId  = Estimates_Record_Model::getVanlineIdStatic($recordId);
//            $dayCertain = 'False';//(condition) ? true : false;
//            if ($vanLineId == 1 || $vanLineId == 9) {
//                $qlabId    = $vanLineId;
//                $vanLineId = 18;
//            }
//            $arr = MoveCrm\arrayBuilder::buildArray($request->get('record'), false, true, false, $dov);
//            $xml = MoveCrm\xmlBuilder::build($arr);
//            file_put_contents('logs/xmlRework.xml', $xml);
//            if (!isset($db)) {
//                $db = PearDatabase::getInstance();
//            }
//            $sql          = "SELECT id FROM `vtiger_crmentity_seq`";
//            $params       = [];
//            $result       = $db->pquery($sql, $params);
//            $row          = $result->fetchRow();
//            $crmIdToWrite = $row[0] + 2;
//            $wsdlParams   = ['reportID' => $request->get('reportId'), 'byteArray' => base64_encode($xml)];
//            file_put_contents('logs/devLog.log', "\n wsdlParams2 : ".print_r($wsdlParams, true), FILE_APPEND);
//            $soapResult = $soapProxy->getReport($wsdlParams);
//            file_put_contents('logs/devLog.log', "\n soapResult : ".print_r($soapResult, true), FILE_APPEND);
//            //Determine correct filepath and write report to PDF
//            $currentDateFilepath = date("Y/F/");
//            $currentMonth        = date("n");
//            $currentDay          = date("j");
//            $currentDayOfWeek    = date("w");
//            $firstOfMonth        = date("w", mktime(0, 0, 0, $currentMonth, 1));
//            $weekNum             = floor((($currentDay + ($firstOfMonth - 1)) / 7) + 1);
//            $filepath            = "storage/".$currentDateFilepath."week".$weekNum;
//            if (!is_dir($filepath)) {
//                mkdir($filepath, 0777, true);
//            }
//            file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').base64_decode($soapResult['GetReportResult'])."\n", FILE_APPEND);
//            $reportName = $this->clean($reportName);
//            $fileHandle = $filepath.'/'.$crmIdToWrite.'_'.$reportName.'.pdf';
//            $written    = file_put_contents($fileHandle, fopen(base64_decode($soapResult['GetReportResult']), 'rb'));
//            //Fetch current user's ID and Access Key for webservice
//            $currentUser   = Users_Record_Model::getCurrentUserModel();
//            $currentUserId = 1;//$currentUser->getId();
//            $sql           = "SELECT user_name, accesskey FROM `vtiger_users` WHERE id=?";
//            $params[]      = $currentUserId;
//            $result        = $db->pquery($sql, $params);
//            unset($params);
//            $row       = $result->fetchRow();
//            $username  = $row[0];
//            $accesskey = $row[1];
//            //Login to webservice and create new Document with response from MoverDocs
//            $webserviceURL = getenv('SITE_URL').'/webservice.php';
//            $ch            = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$username);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
//            $curlResult = curl_exec($ch);
//            curl_close($ch);
//            $challengeResponse = json_decode($curlResult);
//            $generatedkey      = md5($challengeResponse->result->token.$accesskey);
//            $post_string       = "operation=login&username=".urlencode($username)."&accessKey=".$generatedkey;
//            file_put_contents('logs/devLog.log', "\n ze how you say, post string : ".$post_string."\n", FILE_APPEND);
//            $curlResult    = $this->curlPOST($post_string, $webserviceURL);
//            $loginResponse = json_decode($curlResult);
//            $sessionId     = $loginResponse->result->sessionName;
//            $userid        = $loginResponse->result->userId;
//            $folderId      = "22x1";
//            $filename      = $reportName.'.pdf';
//            $filetype      = 'application/pdf';
//            $sql           = "SELECT smownerid, agentid FROM `vtiger_crmentity` WHERE crmid = ?";
//            $result        = $db->pquery($sql, [$recordId]);
//            $row           = $result->fetchRow();
//            $ownerGroupId  = '19x'.$row['smownerid'];
//            $agentid       = $row['agentid'];
//            file_put_contents('logs/devLog.log', "\n Request : ".print_r($request, true), FILE_APPEND);
//            $documentInfo = ['notes_title'      => $reportName,
//                             'filename'         => $filename,
//                             'assigned_user_id' => $ownerGroupId,
//                             'filetype'         => $filetype,
//                             'filelocationtype' => 'I',
//                             'filestatus'       => 1,
//                             'folderid'         => $folderId,
//                             'filesize'         => $written,
//                             'agentid'          => $agentid];
//            include_once 'include/Webservices/Create.php';
//            include_once 'modules/Users/Users.php';
//            try {
//                $user = new Users();
//                $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
//                $document = vtws_create('Documents', $documentInfo, $current_user);
//                //file_put_contents('logs/testingNew.log', "\n Document : ".print_r($document, true), FILE_APPEND);
//            } catch (WebServiceException $ex) {
//                //file_put_contents('logs/devLog.log', "\n Error : ".print_r($ex->getMessage(), true), FILE_APPEND);
//            }
//            //file_put_contents('logs/devLog.log', "\n DocumentInfo : ".print_r($documentInfo, true), FILE_APPEND);
//            //$post_string    = "operation=create&sessionName=".$sessionId."&element=".json_encode($documentInfo)."&elementType=Documents";
//            //$curlResult     = $this->curlPOST($post_string, $webserviceURL);
//            //$createResponse = json_decode($curlResult);
//            //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').print_r($createResponse, true)." CREATE\n", FILE_APPEND);
//
//            $db->startTransaction();
//
//            $docid    = explode('x', $document['id'])[1];
//            $ctime    = $document['createdtime'];
//            $mtime    = $document['modifiedtime'];
//            $sql      = "SELECT quoteid, potentialid, contactid, accountid FROM `vtiger_quotes` WHERE quoteid=?";
//            $params[] = $recordId;
//            $result   = $db->pquery($sql, $params);
//            unset($params);
//            $row         = $result->fetchRow();
//            $potentialid = $row[1];
//            $contactid   = $row[2];
//            $accountid   = $row[3];
//            $sql         = "INSERT INTO `vtiger_crmentity` (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime,agentid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
//            $params[]    = $docid + 1;
//            $params[]    = $currentUserId;
//            $params[]    = $currentUserId;
//            $params[]    = 'Documents Attachment';
//            $params[]    = '';
//            $params[]    = $ctime;
//            $params[]    = $mtime;
//            $params[]    = $request->get('agentId');
//            $result      = $db->pquery($sql, $params);
//            unset($params);
//            $sql      = "INSERT INTO `vtiger_attachments` (attachmentsid, name, description, type, path) VALUES (?, ?, ?, ?, ?)";
//            $params[] = $docid + 1;
//            $params[] = $filename;
//            $params[] = '';
//            $params[] = $filetype;
//            $params[] = $filepath."/";
//            $result   = $db->pquery($sql, $params);
//            unset($params);
//            $sql      = "UPDATE `vtiger_crmentity_seq` SET id=id+1";
//            $params   = [];
//            $result   = $db->pquery($sql, $params);
//            $sql      = "DELETE FROM `vtiger_seattachmentsrel` WHERE crmid=?";
//            $params[] = $docid;
//            $result   = $db->pquery($sql, $params);
//            unset($params);
//            $sql      = "INSERT INTO `vtiger_seattachmentsrel` VALUES (?, ?)";
//            $params[] = $docid;
//            $params[] = $docid + 1;
//            $result   = $db->pquery($sql, $params);
//            unset($params);
//            $sql      = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid=?";
//            $params[] = $recordId;
//            $result   = $db->pquery($sql, $params);
//            unset($params);
//            $row      = $result->fetchRow();
//            $sql      = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
//            $params[] = $recordId;
//            $params[] = $docid;
//            $result   = $db->pquery($sql, $params);
//            unset($params);
//            if ($contactid != null) {
//                $sql      = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
//                $params[] = $contactid;
//                $params[] = $docid;
//                $result   = $db->pquery($sql, $params);
//                unset($params);
//            }
//            if ($accountid != null) {
//                $sql      = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
//                $params[] = $accountid;
//                $params[] = $docid;
//                $result   = $db->pquery($sql, $params);
//                unset($params);
//            }
//            if ($potentialid != null) {
//                $sql      = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
//                $params[] = $potentialid;
//                $params[] = $docid;
//                $result   = $db->pquery($sql, $params);
//                unset($params);
//            }
//            $db->completeTransaction();
//            rename($fileHandle, $filepath.'/'.($docid+1).'_'.$reportName.'.pdf');
            $response = new Vtiger_Response();
            $response->setResult($docid);
            $response->emit();
        }
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

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}
