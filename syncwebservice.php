<?php
/*
 * HTTP POST
 * Parameter name: mode
 * Parameter type: String
 * Parameter options: auth, getwalks
 *
 * Parameter name: element
 * Parameter type: JSON
 * Parameter contents:
 * {
 *     username: only used with auth mode; username of user to login
 *     password: only used with auth mode; password of user to login
 * }
 */
//file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Entering webservice\n", FILE_APPEND);
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once 'customWebserviceFunctions.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';
include_once 'modules/Users/Users.php';
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/SessionManager.php';
//@TODO: to consider maybe we need to run the vendor autoload here.
require_once 'vendor/autoload.php';

use Aws\Sdk;

global $current_user;

//file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."After include statements\n", FILE_APPEND);
if (!isset($_POST) || empty($_POST)) {
    $errCode    = "NO_POST_DATA_FOUND";
    $errMessage = "No POST data was found in the request";
    $response   = json_encode(generateErrorArray($errCode, $errMessage));
    //file_put_contents('logs/uploadCloneTest.log', json_decode($response)."\n", FILE_APPEND);
    //echo $response;
    logAndEmitResponse($response);
}
if (!isset($_POST['mode'])) {
    $errCode    = "NO_MODE_FOUND";
    $errMessage = "Mode was not provided";
    $response   = json_encode(generateErrorArray($errCode, $errMessage));
    //file_put_contents('logs/uploadCloneTest.log', json_decode($response)."\n", FILE_APPEND);
    //echo $response;
    logAndEmitResponse($response);
}
if (!isset($_POST['element'])) {
    $errCode    = "NO_ELEMENT_FOUND";
    $errMessage = "Element was not provided";
    $response   = json_encode(generateErrorArray($errCode, $errMessage));
    //file_put_contents('logs/uploadCloneTest.log', json_decode($response)."\n", FILE_APPEND);
    //echo $response;
    logAndEmitResponse($response);
} else{
    $_POST['element'] = urldecode($_POST['element']);
}

//file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."POST data verified\n", FILE_APPEND);
//split to two lines for clarity.
$mode     = strtolower($_POST['mode']);
$postdata = json_decode($_POST['element'], true);
$postdata['syncwebservice'] = 1;
if ($mode != 'auth' && $mode != 'getadmin') {

    if (!isset($_POST['sessionName'])) {
        $errCode    = "MISSING_SESSIONID";
        $errMessage = "Session Identifier was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    try {
        $sessionName = $_POST['sessionName'];
        $sessionManager = new SessionManager();
        if (!$sessionName || strcasecmp($sessionName, "null")===0) {
            $sessionName = null;
        }
        $sid = $sessionManager->startSession($sessionName, false);

        if (!$sid) {
            $errCode    = "INVALID_SESSION";
            $errMessage = "Provided sessionName is invalid or expired";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            logAndEmitResponse($response);
        }

        $userid = $sessionManager->get("authenticatedUserId");

        if ($userid) {
            $seed_user = new Users();
            $current_user = $seed_user->retrieveCurrentUserInfoFromFile($userid);
        } else {
            $current_user = null;
        }
    } catch (WebServiceException $e) {
        $errCode    = $e->getCode();
        $errMessage = $e->getMessage();
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    } catch (Exception $e) {
        $errCode    = WebServiceErrorCode::$INTERNALERROR;
        $errMessage = "Unknown Error while processing request";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
}
//if ($mode != 'auth' && $mode != 'getadmin') {
//    if (!isset($_POST['sessionName'])) {
//        $errCode    = "MISSING_SESSIONID";
//        $errMessage = "Session Identifier was not provided";
//        $response   = json_encode(generateErrorArray($errCode, $errMessage));
//        logAndEmitResponse($response);
//    }
//    $sessionId     = $_POST['sessionName'];
//    $webserviceURL = getenv('SITE_URL').'/webservice.php';
//    //Perform Describe operation in order to verify that Session Identifier is valid
//    $ch = curl_init();
//    curl_setopt($ch,
//                CURLOPT_URL,
//                $webserviceURL."?operation=describe&sessionName=".$sessionId."&elementType=Documents");
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
//    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//    $curlResult = curl_exec($ch);
//    curl_close($ch);
//    $describeResult = json_decode($curlResult);
//    //file_put_contents('logs/uploadCloneTest.log', "After describeResult\n", FILE_APPEND);
//    if ($describeResult->success != 1) {
//        logAndEmitResponse($curlResult);
//    }
//    //Session Identifier has been verified - proceed with element parameter check
//}

if ($mode == 'auth') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Auth mode found\n", FILE_APPEND);
    logAndEmitResponse(authenticate($postdata));
} elseif ($mode == 'getadmin') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetAdmin mode found\n", FILE_APPEND);
    logAndEmitResponse(getAdmin($postdata));
} elseif ($mode == 'getwalks') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetWalks mode found\n", FILE_APPEND);
    logAndEmitResponse(getwalks($postdata));
} elseif ($mode == 'getrelated') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetRelated mode found\n", FILE_APPEND);
    logAndEmitResponse(getrelated($postdata));
} elseif ($mode == 'getnextwalk') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetNextWalk mode found\n", FILE_APPEND);
    logAndEmitResponse(getnextwalk($postdata));
} elseif ($mode == 'getlocaltariffs') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetLocalTariffs mode found\n", FILE_APPEND);
    logAndEmitResponse(getlocaltariffs($postdata));
} elseif ($mode == 'getimage') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetImage mode found\n", FILE_APPEND);
    //file_put_contents('logs/getImage.log', date('Y-m-d H:i:s - ').print_r($postdata, true)."\n", FILE_APPEND);
    logAndEmitResponse(getimage($postdata));
} elseif ($mode == 'createestimate') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."CreateEstimate mode found\n", FILE_APPEND);
    logAndEmitResponse(createEstimate($postdata));
} elseif ($mode == 'getsurveys') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetSurveys mode found\n", FILE_APPEND);
    logAndEmitResponse(getSurveys($postdata));
} elseif ($mode == 'retrievesurvey') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."RetrieveSurvey mode found\n", FILE_APPEND);
    logAndEmitResponse(retrieveSurvey($postdata));
} elseif ($mode == 'retrieveratinglineitems') {
    logAndEmitResponse(retrieveRatingLineItems($postdata));
} elseif ($mode == 'getstopscount') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetStopsCount mode found\n", FILE_APPEND);
    logAndEmitResponse(getStopsCount($postdata));
} elseif ($mode == 'createlead') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetStopsCount mode found\n", FILE_APPEND);
    logAndEmitResponse(createLead($postdata));
} elseif ($mode == 'createleadsource') {
    logAndEmitResponse(createLeadSource($postdata));
} elseif ($mode == 'retrieveleadsource') {
    logAndEmitResponse(retrieveLeadSource($postdata));
} elseif ($mode == 'retrieveopportunitybyleadid') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."RetrieveOpportunityByLeadId mode found\n",
    //                  FILE_APPEND);
    logAndEmitResponse(retrieveOpportunityByLeadId($postdata));
} elseif ($mode == 'retrieveopportunity') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."RetrieveOpportunityByLeadId mode found\n",
    //                  FILE_APPEND);
    logAndEmitResponse(retrieveOpportunity($postdata));
}  elseif ($mode == 'retrievelead') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."RetrieveLead mode found\n", FILE_APPEND);
    logAndEmitResponse(retrieveLead($postdata));
} elseif ($mode == 'updatelead') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."UpdateLead mode found\n", FILE_APPEND);
    logAndEmitResponse(updateLead($postdata));
} elseif ($mode == 'getpreshipchecklist') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."GetPreShipChecklist mode found\n", FILE_APPEND);
    logAndEmitResponse(getPreShipChecklist($postdata));
} elseif ($mode == 'createvehicle') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."CreateVehicle mode found\n", FILE_APPEND);
    logAndEmitResponse(createVehicle($postdata, $current_user));
} elseif ($mode == 'retrievevehicles') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."RetrieveVehicles mode found\n", FILE_APPEND);
    logAndEmitResponse(retrieveVehicles($postdata, $current_user));
} elseif ($mode == 'updatevehicle') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."UpdateVehicle mode found\n", FILE_APPEND);
    logAndEmitResponse(updateVehicle($postdata, $current_user));
} elseif ($mode == 'deletevehicle') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."DeleteVehicle mode found\n", FILE_APPEND);
    logAndEmitResponse(deleteVehicle($postdata, $current_user));
} elseif ($mode == 'retrieveleadactivities') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."RetrieveLeadActivities mode found\n", FILE_APPEND);
    logAndEmitResponse(retrieveLeadActivities($postdata));
} elseif ($mode == 'createmilitaryopportunity') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."CreateMilitaryOpportunity mode found\n",
    //                  FILE_APPEND);
    logAndEmitResponse(createMilitaryOpportunity($postdata));
} elseif ($mode == 'deletelead') {
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."deleteLead mode found\n",
    //                  FILE_APPEND);
    logAndEmitResponse(deleteLead($postdata));
} elseif ($mode == 'getmontwaylist') {
    logAndEmitResponse(getMontwayList());
} elseif ($mode == 'seteffectivetariff') {
    logAndEmitResponse(setEffectiveTariff($postdata));
} elseif ($mode == 'getagentsbyuser') {
    logAndEmitResponse(getAgentsByUser($postdata));
} elseif ($mode == 'getevents') {
    logAndEmitResponse(getEvents($postdata));
} elseif ($mode == 'updatepushtoken') {
    logAndEmitResponse(updatePushToken($postdata));
} elseif ($mode == 'updateoipushtoken') {
    logAndEmitResponse(updateOiPushToken($postdata));
} elseif ($mode == 'testoplist') {
    logAndEmitResponse(testOpList($postdata));
} elseif ($mode == 'saveoplistanswers') {
    logAndEmitResponse(saveOpListAnswers($postdata));
} elseif ($mode == 'getoplistsbyagentcode') {
    logAndEmitResponse(getOpListsByAgentCode($postdata));
} elseif ($mode == 'getstops') {
    logAndEmitResponse(getStops($postdata));
} elseif ($mode == 'getuserdepth') {
    logAndEmitResponse(getUserDepth($postdata));
} elseif ($mode == 'isadminonlytariff') {
    logAndEmitResponse(isAdminOnlyTariff($postdata));
} elseif ($mode == 'gettariffsbytype') {
    logAndEmitResponse(getTariffsByType($postdata));
} elseif ($mode == 'rateestimate') {
    logAndEmitResponse(rateEstimate($postdata));
} elseif ($mode == 'syncestimate') {
    logAndEmitResponse(syncEstimate($postdata));
} elseif ($mode == 'addfieldhistory') {
    logAndEmitResponse(addFieldHistory($postdata));
} elseif ($mode == 'geteventsbyid') {
    logAndEmitResponse(getEventsById($postdata));
} elseif ($mode == 'checkrecordfordeletion') {
    logAndEmitResponse(checkRecordForDeletion($postdata));
} elseif ($mode == 'migratedocumentsfororders') {
    logAndEmitResponse(migrateDocumentsForOrders($postdata));
} elseif ($mode == 'getmediaimage') {
    logAndEmitResponse(getMediaImage($postdata));
} else {
    $errCode    = "INVALID_MODE_FOUND";
    $errMessage = "An invalid mode was found in the request";
    $response   = json_encode(generateErrorArray($errCode, $errMessage));
    //file_put_contents('logs/uploadCloneTest.log', json_decode($response)."\n", FILE_APPEND);
    //echo $response;
    logAndEmitResponse($response);
}

function authenticate($postdata)
{
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Entering authenticate function\n", FILE_APPEND);
    if (!isset($postdata['username'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'username' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        //file_put_contents('logs/uploadCloneTest.log', print_r(json_decode($response), true)."\n", FILE_APPEND);
        //echo $response;
        logAndEmitResponse($response);
    }
    if (!isset($postdata['password'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'password' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        //file_put_contents('logs/uploadCloneTest.log', print_r(json_decode($response), true)."\n", FILE_APPEND);
        //echo $response;
        logAndEmitResponse($response);
    }
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Element data verified\n", FILE_APPEND);
    $user                             = CRMEntity::getInstance('Users');
    $user->column_fields['user_name'] = $postdata['username'];
    if ($user->doLogin($postdata['password'])) {
        //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Login successful\n", FILE_APPEND);
        //User authenticates correctly
        //Retrieve accesskey
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT accesskey FROM `vtiger_users` WHERE user_name=?";
        $result = $db->pquery($sql, [$postdata['username']]);
        $row    = $result->fetchRow();
        if ($row == null) {
            $errCode    = "SQL_ERROR";
            $errMessage = "Unexpected error occurred while querying the database";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            logAndEmitResponse($response);
        }

        //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ').$row[0]."\n", FILE_APPEND);
        return json_encode(['success' => 'true', 'result' => ['accesskey' => $row[0]]]);
    } else {
        $errCode    = "INVALID_LOGIN";
        $errMessage = "Provided username/password combination is invalid";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    return false;
}

function getAdmin($postdata)
{
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Entering getAdmin function\n", FILE_APPEND);
    if (!isset($postdata['appKey'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'appKey' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    if ($postdata['appKey'] != "FFAe9MVWMXVQDvfM8QJB") {
        $errCode    = "UNAUTHORIZED_REQUEST";
        $errMessage = "The provided appKey is incorrect";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $adminId = Users::getActiveAdminId();
    $db      = PearDatabase::getInstance();
    $sql     = "SELECT user_name, accesskey FROM `vtiger_users` WHERE id=?";
    $result  = $db->pquery($sql, [$adminId]);
    $row     = $result->fetchRow();
    if ($row == null) {
        $errCode    = "NO_ACTIVE_ADMIN";
        $errMessage = "There are currently no active administrators";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    return json_encode(['success' => 'true', 'result' => ['user_name' => $row[0], 'accesskey' => $row[1]]]);
}

function getwalks($postdata)
{
    if (!isset($postdata['accesskey'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'accesskey' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT id FROM `vtiger_users` WHERE accesskey=?";
    $result = $db->pquery($sql, [$postdata['accesskey']]);
    $row    = $result->fetchRow();
    if ($row == null) {
        $errCode    = "INCORRECT_ACCESSKEY";
        $errMessage = "The provided accesskey is not valid";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $userid    = $row[0];
    $walksList = [];
    $sql       = "SELECT walksid FROM `vtiger_walks` JOIN `vtiger_crmentity` ON walksid=crmid WHERE smownerid=?";
    $result    = $db->pquery($sql, [$userid]);
    while ($row =& $result->fetchRow()) {
        $walksList[] = $row[0];
    }

    return json_encode(['success' => 'true', 'result' => ['walks' => $walksList]]);
}

function getnextwalk($postdata)
{
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Entering getnextwalk function\n", FILE_APPEND);
    if (!isset($postdata['accesskey'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'accesskey' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Accesskey verified\n", FILE_APPEND);
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT id FROM `vtiger_users` WHERE accesskey=?";
    $result = $db->pquery($sql, [$postdata['accesskey']]);
    $row    = $result->fetchRow();
    if ($row == null) {
        $errCode    = "INCORRECT_ACCESSKEY";
        $errMessage = "The provided accesskey is not valid";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $userid = $row[0];
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Userid retrieved\n", FILE_APPEND);
    $sql    =
        "SELECT walksid, walk_type FROM `vtiger_walks` JOIN `vtiger_crmentity` ON walksid=crmid WHERE deleted=0 AND sent_to_mobile=0 AND smownerid=? ORDER BY crmid ASC LIMIT 1";
    $result = $db->pquery($sql, [$userid]);
    $row    = $result->fetchRow();
    if ($row == null) {
        return json_encode(['success' => 'true', 'result' => new stdClass()]);
    }
    $walkid    = $row[0];
    $walk_type = $row[1];
    $resArray  = ['walkid' => getObjectTypeId($db, 'Walks').$walkid, 'walk_type' => $walk_type, 'orders' => []];
    $sql       =
        "SELECT orderid, movee_name, origin_building, origin_office, destination_building, destination_office, origin_office_type, dest_office_type FROM `vtiger_walksrel` JOIN `vtiger_crmentity` ON orderid=crmid JOIN `vtiger_walks` ON vtiger_walks.walksid=vtiger_walksrel.walksid JOIN `vtiger_orders` ON ordersid=orderid WHERE deleted=0 AND vtiger_walks.walksid=?";
    $result    = $db->pquery($sql, [$walkid]);
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Query on orders completed\n", FILE_APPEND);
    while ($row =& $result->fetchRow()) {
        $resArray['orders'][] =
            ['id'                   => getObjectTypeId($db, 'Orders').$row[0],
             'movee_name'           => $row[1],
             'origin_building'      => $row[2],
             'origin_office'        => $row[3],
             'destination_building' => $row[4],
             'destination_office'   => $row[5],
             'origin_office_type'   => $row[6],
             'dest_office_type'     => $row[7]];
    }
    //if(count($resArray['orders']) == 0) {$resArray['orders'] = new stdClass();}
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."After while loop\n", FILE_APPEND);
    return json_encode(['success' => 'true', 'result' => $resArray]);
}

function isAdminOnlyTariff($postdata)
{
    if (!isset($postdata['tariffid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'tariffid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $webServiceId = explode('x', $postdata['tariffid']);
    if (!is_string($postdata['tariffid']) || count($webServiceId) <= 1) {
        $errCode    = "INVALID_FORMAT";
        $errMessage = "Parameter 'tariffid' must be in webservice format";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT admin_access FROM `vtiger_tariffs` WHERE tariffsid = ?";
    $result = $db->pquery($sql, [$webServiceId[1]]);
    $row    = $result->fetchRow();
    if ($row[0] == 'on' || $row[0] == 1) {
        $returnString = json_encode(['success' => 'true', 'result' => ['admin_access' => true]], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    } elseif ($row[0] == 0) {
        $returnString = json_encode(['success' => 'true', 'result' => ['admin_access' => false]], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    } else {
        $errCode    = "TARIFF_NOT_FOUND";
        $errMessage = "The provided tariffid is not valid";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    return $returnString;
}

function getlocaltariffs($postdata)
{
    if ($postdata['admin_access'] != 'on' && !isset($postdata['agentid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'agentid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $db     = PearDatabase::getInstance();
    $sql    =
        "SELECT tariffsid, tariff_name, tariff_state, modifiedtime FROM `vtiger_tariffs` JOIN `vtiger_crmentity` ON tariffsid=crmid WHERE `deleted` = 0 AND `tariff_status` != 'Inactive'";
    $params = [];
    if (isset($postdata['agentid'])) {
        $webServiceId = convertFromWebservice($postdata['agentid']);
        if (!validate($postdata['agentid'], 'webservice', false, 'AgentManager')) {
            $errCode    = "INVALID_FORMAT";
            $errMessage = "Parameter 'agentid' must be in webservice format";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            logAndEmitResponse($response);
        }
        $result = $db->pquery("SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agentmanagerid=?", [$webServiceId]);
        $row    = $result->fetchRow();
        if ($row == null) {
            $errCode    = "INVALID_AGENTID";
            $errMessage = "The provided agentid is not valid";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            logAndEmitResponse($response);
        }
        $sql .= " AND `vtiger_crmentity`.agentid=?";
        $params[] = $row[0];
    }
    if ($postdata['admin_access'] == 'on') {
        //if (isset($postdata['agentid'])) {
            $sql .= " AND `vtiger_tariffs`.admin_access IN ('on', 1) ";
        //} else {
        //    $sql .= " WHERE `vtiger_tariffs`.admin_access IN ('on', 1) ";
        //}
    }
    $result = $db->pquery($sql, $params);
    $row    = $result->fetchRow();
    if ($row == null) {
        return json_encode(['success' => 'true', 'result' => new stdClass()]);
    }
    $resArray = ['tariffs' => []];
    while ($row != null) {
        $tariffId      = $row[0];
        $tariffName    = $row[1];
        $tariffState   = $row[2];
        $tariffModTime = $row[3];
        $tariffArray   = ['id'              => $tariffId,
                          'name'            => $tariffName,
                          'state'           => $tariffState,
                          'modified_time'   => $tariffModTime,
                          'sections'        => [],
                          'effective_dates' => []];
        $sql           =
            "SELECT tariffsectionsid, section_name, is_discountable, modifiedtime FROM `vtiger_tariffsections` JOIN `vtiger_crmentity` ON `vtiger_tariffsections`.tariffsectionsid = `vtiger_crmentity`.crmid WHERE related_tariff=?";
        $sectionResult = $db->pquery($sql, [$tariffId]);
        while ($sectionRow =& $sectionResult->fetchRow()) {
            $sectionId                 = $sectionRow[0];
            $sectionName               = $sectionRow[1];
            $sectionDiscountable       = $sectionRow[2];
            $sectionModTime            = $sectionRow[3];
            $tariffArray['sections'][] =
                ['id' => $sectionId, 'name' => $sectionName, 'discountable' => $sectionDiscountable, 'modified_time' => $sectionModTime];
        }
        //Getting the Estimate Type options
        $sql = "SELECT DISTINCT tariff_orders_type FROM `vtiger_tariffreportsections` WHERE tariff_orders_tariff = ?";
        $tariffResult = $db->pquery($sql, [$tariffId]);
        $estimateTypes = [];
        while ($tariffRow =& $tariffResult->fetchRow()) {
            $estimateTypes[] = $tariffRow[0];
        }
        $tariffArray['estimate_types'] = implode(',',$estimateTypes);

        $sql        =
            "SELECT effectivedatesid, effective_date, modifiedtime FROM `vtiger_effectivedates` JOIN `vtiger_crmentity` ON `vtiger_effectivedates`.effectivedatesid = `vtiger_crmentity`.crmid WHERE related_tariff=? AND `deleted` = 0";
        $dateResult = $db->pquery($sql, [$tariffId]);
        while ($dateRow =& $dateResult->fetchRow()) {
            $effectiveDateId      = $dateRow[0];
            $effectiveDate        = $dateRow[1];
            $effectiveDateModTime = $dateRow[2];
            $dateArray            = ['id' => $effectiveDateId, 'date' => $effectiveDate, 'modified_time' => $effectiveDateModTime, 'services' => []];
            $sql                  =
                "SELECT tariffservicesid, service_name, tariff_section, rate_type, applicability, is_required, modifiedtime FROM `vtiger_tariffservices` JOIN `vtiger_crmentity` ON `vtiger_tariffservices`.tariffservicesid = `vtiger_crmentity`.crmid WHERE related_tariff=? AND effective_date=? AND deleted=0";
            $serviceResult        = $db->pquery($sql, [$tariffId, $effectiveDateId]);
            while ($serviceRow =& $serviceResult->fetchRow()) {
                $serviceId            = $serviceRow[0];
                $serviceName          = $serviceRow[1];
                $serviceSection       = $serviceRow[2];
                $serviceType          = $serviceRow[3];
                $serviceApplicability = $serviceRow[4];
                $serviceRequired      = $serviceRow[5];
                $serviceModTime       = $serviceRow[6];
                $serviceArray         = ['id'            => $serviceId,
                                         'name'          => $serviceName,
                                         'section'       => $serviceSection,
                                         'applicability' => $serviceApplicability,
                                         'required'      => $serviceRequired,
                                         'rate_type'     => $serviceType,
                                         'modified_time' => $serviceModTime];
                //file_put_contents('logs/log.log', "\n serviceArray : ".print_r($serviceArray,true), FILE_APPEND);
                $rateDetails = getRateDetails($db, $serviceId, $serviceType);
                //file_put_contents('logs/log.log', "\n rateDetails : ".print_r($rateDetails,true), FILE_APPEND);
                //file_put_contents('logs/log.log', "\n count : ".count($rateDetails), FILE_APPEND);
                if (((count($rateDetails) > 1) || $serviceType == 'Break Point Trans.' ||
                     $serviceType == 'Base Plus Trans.' || $serviceType == 'Weight/Mileage Trans.' ||
                     $serviceType == 'County Charge' || $serviceType == 'Service Base Charge' ||
                     $serviceType == 'Storage Valuation') && $serviceType != 'Hourly Set' &&
                     $serviceType != 'Tabled Valuation' && $serviceType != 'Packing Items' &&
                     $serviceType != 'Crating Item' && $serviceType != 'Bulky List' &&
                     $serviceType != 'Charge Per $100 (Valuation)'
                ) {
                    foreach ($rateDetails as $key => $item) {
                        if (
                            ($serviceType == 'Service Base Charge' || $serviceType == 'Storage Valuation') &&
                            (
                                $key === 'rate' ||
                                $key === 'service_base_charge_applies' ||
                                $key === 'service_base_charge_matrix'
                            )
                        ) {
                            $serviceArray[$key] = $item;
                        } else {
                            $serviceArray['rate_details'][] = $item;
                        }
                    }
                } elseif ($serviceType == 'Hourly Set' || $serviceType == 'Tabled Valuation' ||
                           $serviceType == 'Packing Items' || $serviceType == 'Crating Item' ||
                           $serviceType == 'Bulky List' || $serviceType == 'Charge Per $100 (Valuation)'
                ) {
                    if ($serviceType == 'Crating Item') {
                        $rateDetails = $rateDetails[0];
                    }
                    foreach ($rateDetails as $key => $item) {
                        //file_put_contents('logs/log.log', "\n key : ".$key, FILE_APPEND);
                        if ($key === 'has_van' || $key === 'has_travel' || $key === 'add_man_rate' ||
                            $key === 'add_van_rate' || $key === 'has_released' || $key === 'released_amount' ||
                            $key === 'has_container_rate' || $key === 'has_packing_rate' ||
                            $key === 'has_unpacking_rate' || $key === 'sales_tax' || $key === 'crate_inches' ||
                            $key === 'crate_mincube' || $key === 'crate_packrate' || $key === 'crate_unpackrate' ||
                            $key === 'charge_per'
                        ) {
                            $serviceArray[$key] = $item;
                        } elseif ($serviceType != 'Crating Item') {
                            //file_put_contents('logs/log.log', "\n item : ".print_r($item,true), FILE_APPEND);
                            $serviceArray['rate_details'][] = $item;
                        }
                    }
                } else {
                    $serviceArray['rate'] = $rateDetails[0]['rate'];
                }
                //file_put_contents('logs/log.log', "\nafter serviceArray ".print_r($serviceArray,true), FILE_APPEND);
                $dateArray['services'][] = $serviceArray;
            }
            $tariffArray['effective_dates'][] = $dateArray;
        }
        $resArray['tariffs'][] = $tariffArray;
        $row                   = $result->fetchRow();
    }
    $returnString =
        json_encode(['success' => 'true', 'result' => $resArray], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    //file_put_contents('logs/jsonSync.log', $returnString);
    return $returnString;
}

/**
 * @param $postdata
 * @param string $type
 * @return string
 */
function getImageFromPostData($postData, $type = 'userid')
{
    if (! isset($postData[$type])) {
        logAndEmitResponse(json_encode(generateErrorArray('MISSING_REQ_PARAM', sprintf("Required parameter '%s' was not provided", $type))));
    }

    $temp   = explode('x', $postData[$type]);

    if ($type == 'userid') {
    $userId = $temp[1];
        $recordModel = Users_Record_Model::getInstanceById($userId, 'Users');
    } elseif ($type == 'agentid') {
        $agentId = $temp[1];
        $recordModel = Vtiger_Record_Model::getInstanceById($agentId, 'AgentManager');
    }
    $imageData = $recordModel->getImageDetails();

    file_put_contents('logs/imagePull.log', date('Y-m-d H:i:s - ').print_r($imageData, true)."\n", FILE_APPEND);

    $imagePath = $imageData[0]['path'];

    $image     = file_get_contents($imagePath."_".$imageData[0]['name']);

    return json_encode(['success' => 'true', 'result' => base64_encode($image)]);
}

/**
 * @param $postData
 * @return string
 */
function getImage($postData)
{
    $response = '';

    if (array_key_exists('userid', $postData)) {
        $response = getImageFromPostData($postData);
    } elseif (array_key_exists('agentid', $postData)) {
        $response = getImageFromPostData($postData, 'agentid');
    } else {
        $response = json_encode(generateErrorArray('MISSING_REQ_PARAM', "Required parameter was not provided. Must have either userid or agentid."));
    }

    return $response;
}

function getSurveys($postdata)
{
    $db       = PearDatabase::getInstance();
    $username = $postdata['username'];
    //file_put_contents('logs/devLog.log', "\n username : ".$username, FILE_APPEND);
    $fieldList = ['`vtiger_surveys`.survey_no', '`vtiger_surveys`.survey_date', '`vtiger_crmentity`.smownerid AS assigned_user_id', '`vtiger_surveys`.account_id', '`vtiger_surveys`.contact_id', '`vtiger_surveys`.potential_id', '`vtiger_crmentity`.createdtime', '`vtiger_crmentity`.modifiedtime', '`vtiger_surveys`.survey_time', '`vtiger_surveys`.sent_to_mobile', '`vtiger_surveys`.order_id', '`vtiger_surveys`.address1', '`vtiger_surveys`.address2', '`vtiger_surveys`.city', '`vtiger_surveys`.state', '`vtiger_surveys`.zip', '`vtiger_surveys`.country', '`vtiger_surveys`.phone1', '`vtiger_surveys`.phone2', '`vtiger_surveys`.address_desc', '`vtiger_surveys`.comm_res', '`vtiger_surveys`.survey_end_time', '`vtiger_surveys`.survey_notes', '`vtiger_surveys`.surveysid AS id'];
    $sql = "SELECT * FROM `vtiger_field` WHERE fieldname='survey_type' AND tablename='vtiger_surveys'";
    $result = $db->query($sql);
    if ($db->num_rows($result) > 0) {
        $fieldList[] = '`vtiger_surveys`.survey_type';
    }
    $sql     =
        "SELECT ".implode(',', $fieldList)." FROM `vtiger_surveys` JOIN `vtiger_crmentity` ON `vtiger_surveys`.surveysid = `vtiger_crmentity`.crmid JOIN `vtiger_users` ON `vtiger_crmentity`.smownerid = `vtiger_users`.id WHERE user_name = ? AND `vtiger_crmentity`.deleted = 0";
    $result  = $db->pquery($sql, [$username]);
    $surveys = [];
    while ($row =& $result->fetchRow()) {
        foreach ($row as $key => $value) {
            if (is_numeric($key)) {
                unset($row[$key]);
            }
        }
        $surveys[] = $row;
    }

    return json_encode(['success' => 'true', 'result' => $surveys]);
}

function getPreShipChecklist($postdata)
{
    if (!isset($postdata['agentid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'agentid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_code=?";
    $result = $db->pquery($sql, [$postdata['agentid']]);
    $row    = $result->fetchRow();
    if ($row == null) {
        $errCode    = "INVALID_AGENTID";
        $errMessage = "The provided agentid is not valid";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $agentId        = $row[0];
    $checklistArray = [];
    $sql            = "SELECT checklist_string FROM `vtiger_vehiclelookup_checklist` WHERE agentmanagerid=?";
    $result         = $db->pquery($sql, [$agentId]);
    if ($result->numRows() == 0) {
        $result = $db->pquery($sql, [0]);
    }
    while ($row =& $result->fetchRow()) {
        $checklistArray[] = $row[0];
    }

    return json_encode(['success' => 'true', 'result' => $checklistArray]);
}

function createVehicle($postdata, $current_user)
{
    if (!isset($postdata['vehicle_vin'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'vehicle_vin' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    if (!validateWebservice($postdata['orderid'], 'Orders')) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'orderid' must be in the format <ordersEntityId>x<id>";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    $rec = Vtiger_Record_Model::getCleanInstance('VehicleLookup');
    return putToVehicleRecord($rec, $postdata, $current_user);
}

function retrieveVehicles($postdata, $current_user)
{
    if (!validateWebservice($postdata['orderid'], 'Orders')) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'orderid' must be in the format <ordersEntityId>x<id>";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    list($orderEntityId, $orderid) = explode('x', $postdata['orderid']);

    if (!isset($orderid)) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'orderid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    $db           = PearDatabase::getInstance();
    $vehicleArray = [];
    $sql          =
        "SELECT vehiclelookupid FROM `vtiger_vehiclelookup`
               INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_vehiclelookup.vehiclelookupid)
            WHERE vehiclelookup_relcrmid=? AND deleted=0";
    $result       = $db->pquery($sql, [$orderid]);
    while ($row =& $result->fetchRow()) {
        $vehicleArray[] = getVehicleArray($row['vehiclelookupid'], $current_user);
    }

    return json_encode(['success' => 'true', 'result' => $vehicleArray]);
}

function updateVehicle($postdata, $current_user)
{
    if (!$postdata['vehicleid'] && $postdata['id']) {
        $postdata['vehicleid'] = $postdata['id'];
    }
    if (preg_match('/x/i', $postdata['vehicleid'])) {
        list($VehicleLookupID, $vehicleID) = explode('x', $postdata['vehicleid']);
        $postdata['vehicleid'] = $vehicleID;
    }

    if (!isset($postdata['vehicleid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'vehicleid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    if (intval($postdata['vehicleid']) < 1) {
        $errCode    = "INVALID_ID";
        $errMessage = "Value of parameter 'vehicleid' must be greater than 0";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    if (!validateWebservice($postdata['orderid'], 'Orders')) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'orderid' must be in the format <ordersEntityId>x<id>";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    list ($success, $rec) = getAndVerifyRecordModel($postdata['vehicleid'], 'vehicleid', 'VehicleLookup');
    if (!$success) {
        logAndEmitResponse($rec);
        return false;
    }

    $rec->set('mode', 'edit');
    return putToVehicleRecord($rec, $postdata, $current_user);
}

function deleteVehicle($postdata, $current_user)
{
    if (!$postdata['vehicleid'] && $postdata['id']) {
        $postdata['vehicleid'] = $postdata['id'];
    }
    if (preg_match('/x/i', $postdata['vehicleid'])) {
        list($VehicleLookupID, $vehicleID) = explode('x', $postdata['vehicleid']);
        $postdata['vehicleid'] = $vehicleID;
    }

    if (!isset($postdata['vehicleid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'vehicleid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    list ($success, $rec) = getAndVerifyRecordModel($postdata['vehicleid'], 'vehicleid', 'VehicleLookup');
    if (!$success) {
        logAndEmitResponse($rec);
        return false;
    }
    $rec->delete();

    return json_encode(['success' => 'true', 'result' => []]);
}

function getMontwayList()
{
    $list = [];

    $db = PearDatabase::getInstance();
    $query = $db->pquery("SELECT `sourcevalue` AS make, `targetvalues` AS models FROM `vtiger_picklist_dependency` WHERE `sourcefield` = 'auto_make'", []);
    if ($db->num_rows($query)) {
        while ($row =& $query->fetchRow()) {
            $models = json_decode($row['models']);
            foreach ($models as $model) {
                $list[$row['make']][] = $model;
            }
        }
    }

    return json_encode(['success' => 'true', 'result' => $list]);
}

function getStopsCount($postdata)
{
    $db          = PearDatabase::getInstance();
    $cubesheetid = $postdata['cubesheetid'];
    if ($cubesheetid) {
        //file_put_contents('logs/devLog.log', "\n username : ".$username, FILE_APPEND);
        $sql             =
            "SELECT COUNT(*) FROM `vtiger_extrastops` INNER JOIN `vtiger_cubesheets` ON `vtiger_cubesheets`.potential_id = `vtiger_extrastops`.extrastops_relcrmid WHERE `vtiger_extrastops`.extrastops_type = 'Origin' AND `vtiger_cubesheets`.cubesheetsid = ?";
        $result          = $db->pquery($sql, [$cubesheetid]);
        $row             = $result->fetchRow();
        $originStopCount = $row[0];
        $sql             =
            "SELECT COUNT(*) FROM `vtiger_extrastops` INNER JOIN `vtiger_cubesheets` ON `vtiger_cubesheets`.potential_id = `vtiger_extrastops`.extrastops_relcrmid WHERE `vtiger_extrastops`.extrastops_type = 'Destination' AND `vtiger_cubesheets`.cubesheetsid = ?";
        $result          = $db->pquery($sql, [$cubesheetid]);
        $row             = $result->fetchRow();
        $destStopCount   = $row[0];
        $total           = $originStopCount + $destStopCount;
        $result          = ['origin' => $originStopCount, 'destination' => $destStopCount, 'total' => $total];

        return json_encode(['success' => 'true', 'result' => $result]);
    } else {
        logAndEmitResponse("CubesheetId is required");
    }
}

function retrieveSurvey($postdata)
{
    //take the given LeadId to find the primary survey's ID
    $db = PearDatabase::getInstance();
    //Make sure LeadId or OpportunityId exists
    if (empty($postdata['LeadId']) && empty($postdata['OpportunityId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'LeadId or OpportunityId must be provided');

        return json_encode($response);
    } elseif (!empty($postdata['LeadId'])) {
        //If both aren't empty we use LeadId
        $leadId = explode('x', $postdata['LeadId'])[1];
        if (empty($leadId) || explode('x', $postdata['LeadId'])[0] != 10) {
            $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be in the format of 10x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE converted_from = ?";
            $result = $db->pquery($sql, [$leadId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found that was converted from that lead
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'No Opportunity was found that was converted from '.$postdata['LeadId']);

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    } else {
        //If LeadId is empty use OpportunityId
        $oppId = explode('x', $postdata['OpportunityId'])[1];
        if (empty($oppId) || explode('x', $postdata['OpportunityId'])[0] != 46) {
            $response = generateErrorArray('INVALID_ID', 'OpportunityId is expected to be in the format of 46x###');

            return json_encode($response);
        } else {
            //file_put_contents('logs/devLog.log', "\n OppId : ".print_r($oppId, true), FILE_APPEND);
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE potentialid = ?";
            $result = $db->pquery($sql, [$oppId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found with that OpportunityId
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'OpportunityId is expected to be a valid OpportunityId in the system');

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    }
    $sql    = "SELECT cubesheetsid FROM `vtiger_cubesheets`
            WHERE potential_id = ?
            ORDER BY cubesheetsid DESC,
            is_primary DESC
            LIMIT 1";
    $result = $db->pquery($sql, [$oppId]);
    $row    = $result->fetchRow();
    if (empty($row)) {
        $response = generateErrorArray('NO_SURVEYS', 'No Surveys were found that were associated with '.$postdata['LeadId']);

        return json_encode($response);
    }
    $surveyId = $row[0];
    //getCubesheetDetailsByRelatedRecord this gives us a CubesheetId
    require_once('libraries/nusoap/nusoap.php');
    $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
    $soapclient->setDefaultRpcParams(true);
    $soapProxy        = $soapclient->getProxy();
    $soapResponse     = $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => (string) $surveyId]);
    $cubesheetDetails = !empty($soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'][0])
        ?$soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'][0]
        :$soapResponse
         ['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'];
    $cubesheetId      = $cubesheetDetails['CubeSheetId'];
    //Use CubesheetId to getSurveyedItems this gives us some basic item info and an ItemId
    $soapResponse  = $soapProxy->getSurveyedItems(['CubeSheetId' => $cubesheetId, 'CubeSheetIdSpecified' => true]);
    $surveyedItems = $soapResponse['GetSurveyedItemsResult']['SurveyedItems'];
    //Use getItems with the currentUserId to match and get anything else for the item or their custom items
    $sql          = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?";
    $result       = $db->pquery($sql, [$surveyId]);
    $row          = $result->fetchRow();
    $ownerId      = $row[0];
    $soapResponse = $soapProxy->GetItems(['userID' => $ownerId]);
    $userItems    = [];
    foreach ($soapResponse['GetItemsResult']['Item'] as $item) {
        $userItems[$item['ItemId']] = $item;
    }
    $soapResponse = $soapProxy->GetRooms(['userID' => $ownerId]);
    $userRooms    = [];
    foreach ($soapResponse['GetRoomsResult']['Room'] as $room) {
        $userRooms[$room['RoomId']] = $room;
    }
    foreach ($surveyedItems as $surveyedItem) {
        $items[] = [
            'Cube'         => $cubesheetId,
            'RoomsName'    => $userRooms[$surveyedItem['RoomId']]['Name'],
            'Id'           => $surveyedItem['ItemId'],
            'ArticleName'  => $userItems[$surveyedItem['ItemId']]['ItemName'],
            'Length'       => $surveyedItem['Length'],
            'Width'        => $surveyedItem['Width'],
            'Height'       => $surveyedItem['Height'],
            'Weight'       => $surveyedItem['Weight'],
            'Quantity'     => $surveyedItem['ShipQty'],
            'NoShip'       => $surveyedItem['NotShipQty'],
            'PackQty'      => $surveyedItem['PackQty'],
            'UnpackQty'    => $surveyedItem['UnpackQty'],
            'ContainerQty' => 0, //Cubesheets doesn't handle this.
            'Bulky'        => ($userItems[$surveyedItem['ItemId']]['IsBulky'] == 'true')?'Yes':'No',
            'FragileFlag'  => '',
            'PBOFlag'      => ($userItems[$surveyedItem['ItemId']]['IsPbo'] == 'true')?'Yes':'No',
            'CPFlag'       => ($userItems[$surveyedItem['ItemId']]['IsCp'] == 'true')?'Yes':'No',
            'Carton'       => ($userItems[$surveyedItem['ItemId']]['IsPbo'] == 'true' ||
                               $userItems[$surveyedItem['ItemId']]['IsCp'] == 'true')?'Yes':'No',
            'Comments'     => $surveyedItem['Comment'],
        ];
    }
    $response = ['success' => true, 'result' => ['ListOfSurveyItems' => $items]];

    return json_encode($response);
    //This will be returned as follows
    /*
    {
        "success":true,
        "result":{
            "ListOfSurveyItems":
            [
                {
                    "Cube" : "CubeSheetId",
                    "RoomsName" : "Name",
                    "Id" : "ItemId",
                    "ArticleName" : "description",
                    "Length" : "length",
                    "Width" : "width",
                    "Height" : "height",
                    "Weight" : "weight",
                    "Quantity" : "qtyRateQty",
                    "NoShip" : "NotShipQtySpecified",
                    "PackQty" : "pack_qty",
                    "UnpackQty" : "unpack_qty",
                    "ContainerQty" : "container_qty",
                    "Bulky" : "IsBulky",
                    "FragileFlag" : "always blank",
                    "PBOFlag" : "IsPbo",
                    "CPFlag" : "IsCp",
                    "Carton" : "Yes or No if CP or PBO",
                    "Comments" : "commentcontent"
                }
            ]
        }
    }
    */
}

function retrieveRatingLineItems($postdata)
{
    //take the given LeadId to find the primary survey's ID
    $db = PearDatabase::getInstance();
    //Make sure LeadId or OpportunityId exists
    if (empty($postdata['LeadId']) && empty($postdata['OpportunityId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'LeadId or OpportunityId must be provided');

        return json_encode($response);
    } elseif (!empty($postdata['LeadId'])) {
        //If both aren't empty we use LeadId
        $leadId = explode('x', $postdata['LeadId'])[1];
        if (empty($leadId) || explode('x', $postdata['LeadId'])[0] != 10) {
            $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be in the format of 10x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE converted_from = ?";
            $result = $db->pquery($sql, [$leadId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found that was converted from that lead
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'No Opportunity was found that was converted from '.$postdata['LeadId']);

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    } else {
        //If LeadId is empty use OpportunityId
        $oppId = explode('x', $postdata['OpportunityId'])[1];
        if (empty($oppId) || explode('x', $postdata['OpportunityId'])[0] != 46) {
            $response = generateErrorArray('INVALID_ID', 'OpportunityId is expected to be in the format of 46x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE potentialid = ?";
            $result = $db->pquery($sql, [$oppId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found with that OpportunityId
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'OpportunityId is expected to be a valid OpportunityId in the system');

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    }
    $sql    = "SELECT quoteid FROM `vtiger_quotes`
            WHERE potentialid = ?
            ORDER BY quoteid DESC,
            is_primary DESC
            LIMIT 1";
    $result = $db->pquery($sql, [$oppId]);
    $row    = $result->fetchRow();
    if (empty($row)) {
        $response = generateErrorArray('NO_ESTIMATE', 'No Estimates were found that were associated with '.(!empty($postdata['LeadId'])?$postdata['LeadId']:$postdata['OpportunityId']));

        return json_encode($response);
    }
    $estId           = $row[0];
    $sql             = "SELECT * FROM `vtiger_rating_line_items` WHERE estimate_id = ?";
    $result          = $db->pquery($sql, [$estId]);
    $RatingLineItems = [];
    while ($row =& $result->fetchRow()) {
        $RatingLineItems[] = [
            "Amount"      => $row['amount'],
            "Quantity"    => $row['quantity'],
            "Location"    => $row['location'],
            "Schedule"    => $row['schedule'],
            "BillingItem" => $row['billing_item'],
            "Rate"        => $row['rate'],
            "Weight"      => $row['weight'],
        ];
    }
    if (empty($RatingLineItems)) {
        $response = generateErrorArray('NO_RATING_DATA', 'The Primary Estimate has not been rated yet');

        return json_encode($response);
    }
    $response = ['success' => true, 'result' => ['ListOfRatingLineItems' => $RatingLineItems]];
    //file_put_contents('logs/devLog.log', "\n Response : ".json_encode($response, JSON_PRETTY_PRINT),
    //                  FILE_APPEND);
    return json_encode($response);
    //This will be returned as follows
    /*
    {
        "success":true,
        "result":{
            "ListOfRatingLineItems":
            [
                {
                    "Amount" : "amount",
                    "Quantity" : "quantity",
                    "Location" : "location",
                    "Schedule" : "schedule",
                    "BillingItem" : "billing_item",
                    "Rate" : "rate",
                    "Weight" : "weight"
                }
            ]
        }
    }
    */
}

function getAgentsByUser($postdata)
{
    $db         = PearDatabase::getInstance();
    $userAgents = [];
    if (empty($postdata['UserId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'UserId is missing or empty');

        return json_encode($response);
    }
    $userId = explode('x', $postdata['UserId'])[1];
    if (empty($userId) || explode('x', $postdata['UserId'])[0] != 19) {
        $response = generateErrorArray('INVALID_ID', 'UserId is expected to be in the format of 19x###');

        return json_encode($response);
    }
    $sql    = "SELECT * FROM `vtiger_users` WHERE id = ?";
    $result = $db->pquery($sql, [$userId]);
    $row    = $result->fetchRow();
    if (empty($row)) {
        $response = generateErrorArray('INVALID_ID', 'No users found with that UserId');

        return json_encode($response);
    }
    $sql    =
        "SELECT `vtiger_agentmanager`.agentmanagerid, `vtiger_agentmanager`.agency_name FROM `vtiger_users` LEFT JOIN `vtiger_user2agency` ON `vtiger_users`.id = `vtiger_user2agency`.userid LEFT JOIN `vtiger_agentmanager` ON `vtiger_user2agency`.agency_code = `vtiger_agentmanager`.agentmanagerid WHERE `vtiger_users`.id = ?";
    $result = $db->pquery($sql, [$userId]);
    $row    = $result->fetchRow();
    while ($row != null) {
        if ($row[0] && $row[1]) {
            $userAgents[] = ['agent_name' => $row[1], 'agent_id' => $row[0]];
        }
        $row = $result->fetchRow();
    }
    $response = ['success' => true, 'result' => $userAgents];

    return json_encode($response);
}

function setEffectiveTariff($postdata)
{
    $db                = PearDatabase::getInstance();
    $effectiveTariffId = $postdata['TariffId'];
    if (empty($postdata['EstimateId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'EstimateId is missing or empty');

        return json_encode($response);
    } else {
        $estId = explode('x', $postdata['EstimateId'])[1];
        if (empty($estId) || explode('x', $postdata['EstimateId'])[0] != 45) {
            $response = generateErrorArray('INVALID_ID', 'EstimateId is expected to be in the format of 45x###');

            return json_encode($response);
        }
        $sql    = "SELECT * FROM `vtiger_quotes` WHERE quoteid = ?";
        $result = $db->pquery($sql, [$estId]);
        $row    = $result->fetchRow();
        if (empty($row)) {
            $response = generateErrorArray('INVALID_ID', 'No Estimates found with that EstimateId');

            return json_encode($response);
        }
    }
    $sql = "UPDATE `vtiger_quotes` SET effective_tariff=? WHERE quoteid=?";
    $db->pquery($sql, [$effectiveTariffId, $estId]);
    $response = ['success' => true];

    return json_encode($response);
}

function retrieveOpportunity($postdata)
{
    $db = PearDatabase::getInstance();
    //Make sure LeadId or OpportunityId exists
    if (empty($postdata['LeadId']) && empty($postdata['OpportunityId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'LeadId or OpportunityId must be provided');

        return json_encode($response);
    } elseif (!empty($postdata['LeadId'])) {
        //If both aren't empty we use LeadId
        $leadId = explode('x', $postdata['LeadId'])[1];
        if (empty($leadId) || explode('x', $postdata['LeadId'])[0] != 10) {
            $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be in the format of 10x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE converted_from = ?";
            $result = $db->pquery($sql, [$leadId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found that was converted from that lead
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'No Opportunity was found that was converted from '.$postdata['LeadId']);

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    } else {
        //If LeadId is empty use OpportunityId
        $oppId = explode('x', $postdata['OpportunityId'])[1];
        if (empty($oppId) || explode('x', $postdata['OpportunityId'])[0] != 46) {
            $response = generateErrorArray('INVALID_ID', 'OpportunityId is expected to be in the format of 46x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE potentialid = ?";
            $result = $db->pquery($sql, [$oppId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found with that OpportunityId
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'OpportunityId is expected to be a valid OpportunityId in the system');

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    }

    //These can be turned on for whoever needs them. In this case, just Sirva for now.
    $includeContact = false;
    $includeLead = false;
    $includeUser = false;
    $includeEstimateComments = false;
    $includeEstimateDocs = false;
    $includeLineItems = false;
    if(getenv('INSTANCE_NAME') == 'sirva'){
        $includeContact = true;
        $includeLead = true;
        $includeUser = true;
        $includeEstimateComments = true;
        $includeEstimateDocs = true;
        $includeLineItems = true;
    }

    $resultObject = ['Opportunity'     => [],
                     'ListOfEstimates' => [],
                     'ListOfDocuments' => [],
                     'Comments' => retrieveCommentsForAccount($oppId)
    ];

    include_once 'include/Webservices/Retrieve.php';
    include_once 'modules/Users/Users.php';
    $user         = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    try {
        $wsid = vtws_getWebserviceEntityId('Opportunities', $oppId); // Module_Webservice_ID x CRM_ID
        $opp  = vtws_retrieve($wsid, $current_user);
        //file_put_contents('logs/devLog.log', "\n opp : ".print_r($opp,true), FILE_APPEND);
        $resultObject['Opportunity'] = $opp;

        if ($opp['related_to']) {
            $relatedId = explode('x', $opp['related_to'])[1];
            $sql = "SELECT * FROM vtiger_contactdetails WHERE accountid = ? LIMIT 1";
            $result = $db->pquery($sql, [$relatedId]);
            $columns = $db->getFieldsArray($result);
            $data = $result->fetchRow();
            $data = array_filter($data);
            if ($data) {
                foreach ($data as $key => $val) {
                    $resultObject['Opportunity']['contact_'.$key] = $val;
                }
            }
        }
    } catch (WebServiceException $ex) {
        $response = generateErrorArray('FAILED_TO_RETRIEVE_OPPORTUNITY', $ex->getMessage());

        return json_encode($response);
    }
    // Handling for load_date
    if(empty($resultObject['Opportunity']['load_date']) && !empty($resultObject['Opportunity']['load_to_date'])) {
        $resultObject['Opportunity']['load_date'] = $resultObject['Opportunity']['load_to_date'];
    }

    $sql    = "SELECT quoteid FROM `vtiger_quotes` WHERE potentialid = ?";
    $result = $db->pquery($sql, [$oppId]);
    while ($row =& $result->fetchRow()) {
        try {
            $wsid = vtws_getWebserviceEntityId('Estimates', $row[0]); // Module_Webservice_ID x CRM_ID
            $est  = vtws_retrieve($wsid, $current_user);
            //file_put_contents('logs/devLog.log', "\n est : ".print_r($est,true), FILE_APPEND);
            if($includeContact && !empty($est['contact_id'])){
                try {
                    $wsid = vtws_getWebserviceEntityId('Contacts', explode('x', $est['contact_id'])[1]);
                    $contact  = vtws_retrieve($wsid, $current_user);
                    $est['Contact'] = $contact;
                } catch (WebServiceException $ex) {
                    $response = generateErrorArray('FAILED_TO_RETRIEVE_CONTACT', $ex->getMessage());

                    return json_encode($response);
                }
            }

            if($includeEstimateComments){
                $est['Comments'] = retrieveCommentsForRecord($row[0]);
            }

            if($includeEstimateDocs){
                $sql    = "SELECT `notesid` FROM `vtiger_senotesrel` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_senotesrel`.`notesid` WHERE `vtiger_crmentity`.`deleted` = 0 AND `vtiger_senotesrel`.`crmid` = ?";
                $result = $db->pquery($sql, [$row[0]]);
                while ($docRow =& $result->fetchRow()) {
                    try {
                        $wsid = vtws_getWebserviceEntityId('Documents', $docRow[0]); // Module_Webservice_ID x CRM_ID
                        $doc  = vtws_retrieve($wsid, $current_user);

                        $est['ListOfDocuments'][] = $doc;
                    } catch (WebServiceException $ex) {
                        $response = generateErrorArray('FAILED_TO_RETRIEVE_DOCUMENT', $ex->getMessage());

                        return json_encode($response);
                    }
                }
            }

            if($includeLineItems){
                $sql    = "SELECT * FROM `vtiger_detailed_lineitems` WHERE `dli_relcrmid` = ?";
                $result = $db->pquery($sql, [$row[0]]);
                while ($lineItem =& $result->fetchRow()) {
                    //So.. since we get assoc keys AND a copy as non-assoc, we'll remove the no-assoc values
                    //I'd prefer to just use array filter alone, but we are currently on 5.5.9 and we need 5.6 to use ARRAY_FILTER_USE_KEY
                    $lineItem = array_intersect_key($lineItem, array_flip(array_filter(array_keys($lineItem), function($k) {return !is_numeric($k);})));
                    $est['EstimateItems'][] = $lineItem;
                }
            }

            $resultObject['ListOfEstimates'][] = $est;
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_ESTIMATE', $ex->getMessage());

            return json_encode($response);
        }
    }
    $sql    = "SELECT `notesid` FROM `vtiger_senotesrel` LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_senotesrel`.`notesid` WHERE `vtiger_crmentity`.`deleted` = 0 AND `vtiger_senotesrel`.`crmid` = ?";
    $result = $db->pquery($sql, [$oppId]);
    while ($row =& $result->fetchRow()) {
        try {
            $wsid = vtws_getWebserviceEntityId('Documents', $row[0]); // Module_Webservice_ID x CRM_ID
            $doc  = vtws_retrieve($wsid, $current_user);
            //file_put_contents('logs/devLog.log', "\n doc : ".print_r($doc,true), FILE_APPEND);
            $resultObject['ListOfDocuments'][] = $doc;
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_DOCUMENT', $ex->getMessage());

            return json_encode($response);
        }
    }
    if($includeContact && !empty($resultObject['Opportunity']['contact_id'])){
        try {
            $wsid = vtws_getWebserviceEntityId('Contacts', explode('x', $resultObject['Opportunity']['contact_id'])[1]);
            $contact  = vtws_retrieve($wsid, $current_user);
            $resultObject['Contact'] = $contact;
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_CONTACT', $ex->getMessage());

            return json_encode($response);
        }
    }
    //getLeadIdFromOppId returns false if no lead is found
    if($includeLead){
        $leadId = getLeadIdFromOppId($oppId);
        if($leadId){
            try {
                $wsid = vtws_getWebserviceEntityId('Leads', $leadId);
                $lead  = vtws_retrieve($wsid, $current_user);
                $resultObject['Lead'][] = $lead;
            } catch (WebServiceException $ex) {
                $response = generateErrorArray('FAILED_TO_RETRIEVE_LEAD', $ex->getMessage());

                return json_encode($response);
            }
        }
    }
    if($includeUser){
        try {
            $userId = vtws_getWebserviceEntityId('Users' , $db->getOne("SELECT smcreatorid FROM `vtiger_crmentity` WHERE crmid = $oppId"));
            $user   = vtws_retrieve($userId, $current_user);
            $resultObject['User']['name'] = $user['first_name'] . ' ' . $user['last_name'];
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_USER', $ex->getMessage());

            return json_encode($response);
        }
    }
    //return should be in the form of
    /*
    {
        "success": true,
        "result": {
            "Opportunity": {Opportunity Result JSON},
            "ListOfEstimates": [ {Estimate Result JSON} ],
            "ListOfDocuments": [ {Document Result JSON} ]
        }
    }
     */
    $response = ['success' => true, 'result' => $resultObject];

    return json_encode($response);
}

function retrieveCommentsForAccount($id)
{
    $db = PearDatabase::getInstance();
    $resultArray = array();
    $sql = "SELECT `commentcontent` FROM `vtiger_modcomments` WHERE related_to = ?";
    $result = $db->pquery($sql, [$id]);
    while ($row =& $result->fetchRow()) {
        $resultArray[] = $row[0];
    }
    return $resultArray;
}

function retrieveCommentsForRecord($id)
{
    $db = PearDatabase::getInstance();
    $resultArray = array();
    $sql = "SELECT `commentcontent`, `createdtime`,`smcreatorid`  FROM `vtiger_modcomments` JOIN `vtiger_crmentity` ON `crmid` = `modcommentsid` WHERE related_to = ?";
    $result = $db->pquery($sql, [$id]);
    while ($row =& $result->fetchRow()) {

        //sigh.. I wish there was a way to only pass assoc values, but we need to do this so we are not sending duplicated info
        foreach ($row as $key => $value) {
            if (is_int($key)) {
                unset($row[$key]);
            }
        }

        $resultArray[] = $row;
    }
    return $resultArray;
}

function retrieveOpportunityByLeadId($postdata)
{
    $db = PearDatabase::getInstance();
    if (empty($postdata['LeadId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'LeadId is missing or empty');

        return json_encode($response);
    } else {
        $leadId = explode('x', $postdata['LeadId'])[1];
        if (empty($leadId) || explode('x', $postdata['LeadId'])[0] != 10) {
            $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be in the format of 10x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE converted_from = ?";
            $result = $db->pquery($sql, [$leadId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                //error no opp found that was converted from that lead
                $response = generateErrorArray('OPPORTUNITY_NOT_FOUND', 'No Opportunity was found that was converted from '.$postdata['LeadId']);

                return json_encode($response);
            }
            $oppId = $row[0];
        }
    }
    $resultObject = ['Opportunity'     => [],
                     'ListOfEstimates' => [],
                     'ListOfDocuments' => [],
    ];
    include_once 'include/Webservices/Retrieve.php';
    include_once 'modules/Users/Users.php';
    $user         = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    try {
        $wsid = vtws_getWebserviceEntityId('Opportunities', $oppId); // Module_Webservice_ID x CRM_ID
        $opp  = vtws_retrieve($wsid, $current_user);
        //file_put_contents('logs/devLog.log', "\n opp : ".print_r($opp,true), FILE_APPEND);
        $resultObject['Opportunity'] = $opp;
    } catch (WebServiceException $ex) {
        $response = generateErrorArray('FAILED_TO_RETRIEVE_OPPORTUNITY', $ex->getMessage());

        return json_encode($response);
    }
    $sql    = "SELECT quoteid FROM `vtiger_quotes` WHERE potentialid = ?";
    $result = $db->pquery($sql, [$oppId]);
    while ($row =& $result->fetchRow()) {
        try {
            $wsid = vtws_getWebserviceEntityId('Estimates', $row[0]); // Module_Webservice_ID x CRM_ID
            $est  = vtws_retrieve($wsid, $current_user);
            //file_put_contents('logs/devLog.log', "\n est : ".print_r($est,true), FILE_APPEND);
            $resultObject['ListOfEstimates'][] = $est;
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_ESTIMATE', $ex->getMessage());

            return json_encode($response);
        }
    }
    // Handling for load_date
    if(empty($resultObject['Opportunity']['load_date']) && !empty($resultObject['Opportunity']['load_to_date'])) {
        $resultObject['Opportunity']['load_date'] = $resultObject['Opportunity']['load_to_date'];
    }

    $sql    = "SELECT notesid FROM `vtiger_senotesrel` WHERE crmid = ?";
    $result = $db->pquery($sql, [$oppId]);
    while ($row =& $result->fetchRow()) {
        try {
            $wsid = vtws_getWebserviceEntityId('Documents', $row[0]); // Module_Webservice_ID x CRM_ID
            $doc  = vtws_retrieve($wsid, $current_user);
            //file_put_contents('logs/devLog.log', "\n doc : ".print_r($doc,true), FILE_APPEND);
            $resultObject['ListOfDocuments'][] = $doc;
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_DOCUMENT', $ex->getMessage());

            return json_encode($response);
        }
    }
    //return should be in the form of
    /*
    {
        "success": true,
        "result": {
            "Opportunity": {Opportunity Result JSON},
            "ListOfEstimates": [ {Estimate Result JSON} ],
            "ListOfDocuments": [ {Document Result JSON} ]
        }
    }
     */
    $response = ['success' => true, 'result' => $resultObject];

    return json_encode($response);
}

/**
 * retrieves a lead based on the LeadId passed in the postdata array
 *
 * @param array $postdata An array containing any postdata passed in specifically will have the LeadId
 *
 * @return string $response A JSON string of success true with the data or success false and an error code
 */
function retrieveLead($postdata)
{
    $db = PearDatabase::getInstance();
    //verify that we have a usable leadid and give back understandable error messages if we don't
    if (empty($postdata['LeadId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'LeadId is missing or empty');

        return json_encode($response);
    } else {
        $leadId = explode('x', $postdata['LeadId'])[1];
        if (empty($leadId) || explode('x', $postdata['LeadId'])[0] != 10) {
            $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be in the format of 10x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT * FROM `vtiger_leaddetails` WHERE leadid = ?";
            $result = $db->pquery($sql, [$leadId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                $sql    = "SELECT * FROM `vtiger_potential` WHERE isconvertedfromlead = 1 AND converted_from = ?";
                $result = $db->pquery($sql, [$leadId]);
                $row    = $result->fetchRow();
                if (empty($row)) {
                    $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be for a Lead that has not been converted to an Opportunity');

                    return json_encode($response);
                }
                $response = generateErrorArray('INVALID_ID', 'LeadId is expected to be a valid lead in the system');

                return json_encode($response);
            }
        }
    }
    //we know our lead so lets retrieve it and map fields over to the right names and such
    include_once 'include/Webservices/Retrieve.php';
    include_once 'modules/Users/Users.php';
    $user         = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    try {
        $wsid = vtws_getWebserviceEntityId('Leads', $leadId); // Module_Webservice_ID x CRM_ID
        $lead = vtws_retrieve($wsid, $current_user);
        //file_put_contents('logs/devLog.log', print_r($lead, true), FILE_APPEND);
    } catch (WebServiceException $ex) {
        $response = generateErrorArray('FAILED_TO_RETRIEVE_LEAD', $ex->getMessage());

        return json_encode($response);
    }

    $phoneType = 'HomePhone';
    switch ($lead['primary_phone_type']) {
        case 'Home':
            $lead['primary_phone_type'] = 'Home Phone';
            break;
        case 'Work':
            $lead['primary_phone_type'] = 'Work Phone';
            $phoneType                  = 'WorkPhone';
            break;
        case 'Cell':
            $lead['primary_phone_type'] = 'Cell Phone';
            break;
    }

    switch ($lead['origin_phone1_type']) {
        case 'Home':
            $lead['origin_phone1_type'] = 'Home Phone';
            break;
        case 'Work':
            $lead['origin_phone1_type'] = 'Work Phone';
            $phoneType                  = 'WorkPhone';
            break;
        case 'Cell':
            $lead['origin_phone1_type'] = 'Cell Phone';
            break;
    }

    switch ($lead['destination_phone1_type']) {
        case 'Home':
            $lead['destination_phone1_type'] = 'Home Phone';
            break;
        case 'Work':
            $lead['destination_phone1_type'] = 'Work Phone';
            $phoneType                  = 'WorkPhone';
            break;
        case 'Cell':
            $lead['destination_phone1_type'] = 'Cell Phone';
            break;
    }

    switch ($lead['origin_phone2_type']) {
        case 'Home':
            $lead['origin_phone2_type'] = 'Home Phone';
            break;
        case 'Work':
            $lead['origin_phone2_type'] = 'Work Phone';
            $phoneType                  = 'WorkPhone';
            break;
        case 'Cell':
            $lead['origin_phone2_type'] = 'Cell Phone';
            break;
    }

    switch ($lead['destination_phone2_type']) {
        case 'Home':
            $lead['destination_phone2_type'] = 'Home Phone';
            break;
        case 'Work':
            $lead['destination_phone2_type'] = 'Work Phone';
            $phoneType                  = 'WorkPhone';
            break;
        case 'Cell':
            $lead['destination_phone2_type'] = 'Cell Phone';
            break;
    }

    $sql                   =
        "SELECT agency_code FROM `vtiger_agentmanager` WHERE groupid = ?";
    $result                = $db->pquery($sql, [explode('x', $lead['assigned_user_id'])[1]]);
    $row                   = $result->fetchRow();
    $LMPAssignedAgentOrgId = $row[0];
    $sql                   = "SELECT user_name FROM `vtiger_users` WHERE id = ?";
    $result                = $db->pquery($sql, [explode('x', $lead['sales_person'])[1]]);
    $row                   = $result->fetchRow();
    $sales_person          = $row[0];
    $data                  = [
        'LeadId'                    => $postdata['LeadId'],
        'LMPLeadId'                 => $lead['lmp_lead_id'],
        'CCDisposition'             => $lead['cc_disposition'],
        'AgentDisposition'          => $lead['leadstatus'],
        'MoveType'                  => $lead['move_type'],
        'Brand'                     => $lead['brand'],
        'LMPAssignedAgentOrgId'     => $LMPAssignedAgentOrgId,
        'AMCSalesPersonId'          => $sales_person,
        'FirstName'                 => $lead['firstname'],
        'LastName'                  => $lead['lastname'],
        'Organization'              => $lead['organization'],
        'EmailAddress'              => $lead['email'],
        'PrimaryPhoneType'          => $lead['primary_phone_type'],
        $phoneType                  => $lead['phone'],
        'WorkPhExt'                 => ($phoneType == 'WorkPhone')?$lead['phone_primary_ext']:'',
        'CellularPhone'             => $lead['mobile'],
        'FaxPhone'                  => $lead['origin_fax'],
        'TimeZone'                  => $lead['timezone'],
        'PreferTime'                => $lead['prefer_time'],
        'Language'                  => $lead['languages'],
        'DwellingType'              => $lead['dwelling_type'],
        'CorporateLead'             => (($lead['business_line'] == 'O&I')?'Y':'N'),
        'OriginAddress1'            => $lead['origin_address1'],
        'OriginAddress2'            => $lead['origin_address2'],
        'OriginCity'                => $lead['origin_city'],
        'OriginState'               => $lead['origin_state'],
        'OriginZip'                 => $lead['origin_zip'],
        'OriginCountry'             => $lead['origin_country'],
        'OwnCurrent'                => $lead['own_current'],
        'OriginPhone1Type'          => $lead['origin_phone1_type'],
        'OriginPhone2Type'          => $lead['origin_phone2_type'],
        'DestinationAddress1'       => $lead['destination_address1'],
        'DestinationAddress2'       => $lead['destination_address2'],
        'DestinationCity'           => $lead['destination_city'],
        'DestinationState'          => $lead['destination_state'],
        'DestinationZip'            => $lead['destination_zip'],
        'DestinationCountry'        => $lead['destination_country'],
        'DestinationPhone1Type'   => $lead['destination_phone1_type'],
        'DestinationPhone2Type'   => $lead['destination_phone2_type'],
        'OwnNew'                    => $lead['own_new'],
        'FlexibleOnDays'            => (($lead['flexible_on_days'] == '1')?'Y':'N'),
        'LeadCreatedDate'           => (!empty($lead['createdtime'])?DateTime::createFromFormat('Y-m-d H:i:s',
                                                                                                $lead['createdtime'])->format('d/m/Y'):null),
        'LeadReceiveDate'           => (!empty($lead['lead_receive_date'])?DateTime::createFromFormat('Y-m-d',
                                                                                                      $lead['lead_receive_date'])->format('m/d/Y'):null),
        'RequiredMoveDate'          => $lead['preferred_pldate'],
        'ExpectedDeliveryDate'      => $lead['preferred_pddate'],
        'DaysToMove'                => $lead['days_to_move'],
        'FulfillmentDate'           => $lead['fulfillment_date']?DateTime::createFromFormat('Y-m-d',
                                                                                            $lead['fulfillment_date'])
                                                                         ->format('m/d/Y'):null,
        'Funded'                    => $lead['funded'],
        'AAProgramName'             => $lead['program_name'],
        'AASourceName'              => $lead['source_name'],
        'MarketingChannel'          => $lead['leadsource'],
        'OfferNumber'               => $lead['offer_number'],
        'PromotionTerms'            => $lead['promotion_terms'],
        'SpecialItems'              => $lead['special_terms'],
        'BusinessChannel'           => $lead['business_channel'],
        'EmployerAssistingFlg'      => (($lead['enabled'] == '1')?'Y':'N'),
        'EmployerCompanyName'       => $lead['company'],
        'EmployerContactName'       => $lead['contact_name'],
        'EmployerContactEmail'      => $lead['contact_email'],
        'EmployerContactPhone'      => $lead['contact_phone'],
        'FurnishLevel'              => $lead['furnish_level'],
        'MovingVehicleFlg'          => (($lead['moving_vehicle'] == '1')?'Y':'N'),
        'NumberOfVehicles'          => $lead['number_of_vehicles'],
        'VehicleYear'               => $lead['vehicle_year'],
        'VehicleMake'               => $lead['vehicle_make'],
        'VehicleModel'              => $lead['vehicle_model'],
        'OfferValuationFlg'         => (($lead['offer_valuation'] == '1')?'Y':'N'),
        'OutofOriginFlg'            => (($lead['out_of_origin'] == '1')?'Y':'N'),
        'OutofTimeFlg'              => (($lead['out_of_time'] == '1')?'Y':'N'),
        'OfficeandIndustrial'       => (($lead['comm_res'] == 'Commercial')?'Y':'N'),
        'SmallMoveFlg'              => (($lead['small_move'] == '1')?'Y':'N'),
        'OutofAreaFlg'              => (($lead['out_of_area'] == '1')?'Y':'N'),
        'SIRVAExpectsPhEstimateFlg' => (($lead['phone_estimate'] == '1')?'Y':'N'),
        'Comments'                  => $lead['employer_comments'],
        'Surveyor'                  => '',
        'SurveyAppointmentPlanned'  => '',
        'SurveyAppointmentDuration' => '',
    ];
    $sql                   = 'SELECT * FROM `vtiger_modcomments` WHERE related_to = ?';
    $result                = $db->pquery($sql, [$leadId]);
    $listOfLeadNote        = [];
    while ($row =& $result->fetchRow()) {
        $sql              =
            "SELECT `vtiger_users`.user_name, `vtiger_crmentity`.createdtime FROM `vtiger_crmentity` JOIN `vtiger_users` ON `vtiger_crmentity`.smownerid = `vtiger_users`.id WHERE crmid = ?";
        $lookupResult     = $db->pquery($sql, [$lead['modcommentsid']]);
        $lookupRow        = $lookupResult->fetchRow();
        $createdBy        = $lookupRow['user_name'];
        $createdDate      = (!empty($lookupRow['createdtime'])?DateTime::createFromFormat('Y-m-d H:i:s',
                                                                                          $lookupRow['createdtime'])->format('d/m/Y'):null);
        $listOfLeadNote[] = ['Provider'   => $row['provider'],
                             'NoteSource' => $row['note_source'],
                             'CreatedBy'  => $createdBy,
                             'DateTime'   => $createdDate,
                             'Note'       => $row['commentcontent'],
        ];
    }
    $data['ListOfLeadNote'] = $listOfLeadNote;
    $response               = ['success' => true, 'result' => ['Lead' => $data]];

    return json_encode($response);
}

/**
 * Creates a Military Opportunity based on an array passed in from an external source such as MoveStar
 *
 * @param array $postdata An array of information for the Opp
 *
 * @return string $response A JSON string of success true with the OppId or success false and an error code
 */
function createMilitaryOpportunity($postdata)
{
    $db = PearDatabase::getInstance();
    $logThisFunction = true;
    $logLineLeader = getmypid(). " : ";
    //code to verify that they gave us good data
    $users = getUsersForAgency($postdata['assigned_to']);
    $agencyCode = $postdata['assigned_to'];
//    if(!empty($users)){
//        foreach($users as $userFromUsers){
//            $tempUser = Users_Record_Model::getInstanceById($userFromUsers,'Users');
//            if($tempUser->isCoordinator()){
//                $assigned_user_id = $tempUser->getId();
//                break;
//            }
//        }
//        if ($assigned_user_id) {
//            $assigned_user_id = '19x'.$assigned_user_id;
//        } else {
//            $assigned_user_id = Users_Record_Model::getInstanceById($users[0],'Users')->getId();
//            if (empty($assigned_user_id)) {
//                $response = generateErrorArray('INVALID_VALUE', 'LMPAssignedAgentOrgId has to be a valid agency_code in the system');
//
//                if ($logThisFunction) {
//                    file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
//                }
//                return json_encode($response);
//            }
//        }
//    } else {
        $assigned_user_id = '19x1';
//    }
    $agentId = $db->pquery('SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_code = ?', [$agencyCode])->fetchRow()[0];
    $sql    = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
    $result = $db->pquery($sql, [$postdata['salesperson']]);
    $row    = $result->fetchRow();
    if ($row) {
        //file_put_contents('logs/devLog.log', "\n Sales Person Row : ".print_r($row, true), FILE_APPEND);
        $salesperson = $row[0];
    } else {
        $response = generateErrorArray('INVALID_VALUE', 'salesperson has to be a valid user_name in the system');

        return json_encode($response);
    }
    //create the Opportunity
    include_once 'include/Webservices/Create.php';
    include_once 'modules/Users/Users.php';
    $user                   = new Users();
    $current_user           = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $contact_name           = $postdata['contact_name'];
    $contact_name_parts     = explode(' ', $contact_name);
    $first_name             = '';
    $last_name              = '';
    $contact_name_parts_len = count($contact_name_parts);
    if ($contact_name_parts_len > 1) {
        $first_name = $contact_name_parts[0];
        for ($i = 1; $i <= $contact_name_parts_len; $i++) {
            if ($last_name == '') {
                $last_name .= $contact_name_parts[$i];
            } else {
                $last_name .= ' '.$contact_name_parts[$i];
            }
        }
    }
    $sql    = "SELECT contactid FROM `vtiger_contactdetails` WHERE firstname = ? AND lastname = ? AND email = ?";
    $result = $db->pquery($sql, [$first_name, $last_name, $postdata['contact_email']]);
    $row    = $result->fetchRow();
    if ($row) {
        $contact_id = '12x'.$row[0];
    } else {
        try {
            $data    = ['contact_type'     => 'Transferee',
                        'lastname'         => $last_name,
                        'firstname'        => $first_name,
                        'email'            => $postdata['contact_email'],
                        'assigned_user_id' => $assigned_user_id,
                        'agentid'          => $agentId,
            ];
            $contact = vtws_create('Contacts', $data, $current_user);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_CREATION_OF_CONTACT', $ex->getMessage());
            return json_encode($response);
        }
        $contact_id = $contact['id'];
    }
    //file_put_contents('logs/devLog.log', "\n contact : ".print_r($contact), FILE_APPEND);
    $format_date_array = ['deliver_date','deliver_to_date','pack_date',
                          'pack_to_date','preferred_pack_date','load_date','load_to_date','requested_move_date',
                          'expected_delivery_date','survey_date','follow_up_date','decision_date'];
    foreach ($format_date_array as $date_key_index) {
        //loops through the post variable dates
        //Sometimes sirva just sends spaces.. need to remove them
        if (isset($postdata[$date_key_index]) && str_replace(' ', '', $postdata[$date_key_index]) != '') {
            //if the post is set
            try {
                $valid_date = new DateTime($postdata[$date_key_index]);//will throw an exception if not a valid date
                $postdata[$date_key_index] = strtotime($postdata[$date_key_index]);
                $postdata[$date_key_index] = date('Y-m-d', $postdata[$date_key_index]);
            } catch (Exception $e) {
                //the DateTime will through an error if the variable is not a proper date
                $postdata[$date_key_index] = '';//set to blank
            }
        } else {
            $postdata[$date_key_index] = '';
        }
    }

    //Fix to make sure incoming country matches our system
    switch ($postdata['origin_country']) {
        case 'Canada':
        case 'canada':
        case 'CAN':
            $postdata['origin_country'] = 'Canada';
            break;

        case 'United States of America':
        case 'United States':
        case 'USA':
        case 'US':
        case 'usa':
        case 'us':
            $postdata['origin_country'] = 'United States';
            break;

        default:
            break;
    }
    switch ($postdata['destination_country']) {
        case 'Canada':
        case 'canada':
        case 'CAN':
            $postdata['destination_country'] = 'Canada';
            break;

        case 'United States of America':
        case 'United States':
        case 'USA':
        case 'US':
        case 'usa':
        case 'us':
            $postdata['destination_country'] = 'United States';
            break;

        default:
            break;
    }

    try {
        $data    = [
            'business_line'           => 'Military',
            'closingdate'             => 'turtles',//This is just to bypass the fact that this field is mandatory
            'potentialname'           => $postdata['contact_name'],//$postdata['opportunity_name'], sourcing this
            // from contact name for now until we get word on what it will actually need to be set to
            'contact_id'              => $contact_id,
            //'related_to'=>'',
            'move_type'               => 'Sirva Military',
            'lead_type'               => 'Sirva Military',
            'sales_stage'             => 'Closed Won',
            'leadsource'              => 'Sirva Military',
            'amount'                  => $postdata['amount'],
            'assigned_user_id'        => $assigned_user_id,
            'nextstep'                => $postdata['next_step'],
            'sales_person'            => $salesperson,
            'opportunity_disposition' => 'Booked',
            'order_number'            => $postdata['order_number'],
            'register_sts_number'     => substr($postdata['order_number'], 0, -6),
            'assigned_date'           => $postdata['assigned_date'],
            'receive_date'            => $postdata['received_date'],
            'opportunity_type'        => 'Military Award Survey',
            'opp_type'                => 'Military Award Survey',
            'employer_comments'       => $postdata['comments'],
            'origin_address1'         => $postdata['origin_address_1'],
            'origin_address2'         => $postdata['origin_address_2'],
            'origin_city'             => $postdata['origin_city'],
            'origin_state'            => $postdata['origin_state'] != '' ? $postdata['origin_state'] : 'N/A',
            'origin_zip'              => $postdata['origin_zip'],
            'origin_phone1'           => $postdata['origin_phone_1'],
            'origin_phone1_type'      => $postdata['origin_phone_1_type'],
            'origin_phone2'           => $postdata['origin_phone_2'],
            'origin_phone2_type'      => $postdata['origin_phone_2_type'],
            'origin_country'          => $postdata['origin_country'],
            'origin_description'      => $postdata['origin_description'],
            'destination_address1'    => $postdata['destination_address_1'],
            'destination_address2'    => $postdata['destination_address_2'],
            'destination_city'        => $postdata['destination_city'],
            'destination_state'       => $postdata['destination_state'] != '' ? $postdata['destination_state'] : 'N/A',
            'destination_zip'         => $postdata['destination_zip'],
            'destination_phone1'      => $postdata['destination_phone_1'],
            'destination_phone1_type' => $postdata['destination_phone_1_type'],
            'destination_phone2'      => $postdata['destination_phone_2'],
            'destination_phone2_type' => $postdata['destination_phone_2_type'],
            'destination_country'     => $postdata['destination_country'],
            'destination_description' => $postdata['destination_description'],
            'pack_date'               => $postdata['pack_date'],
            'pack_to_date'            => $postdata['pack_to_date'],
            'preffered_ppdate'        => $postdata['preferred_pack_date'],
            'load_date'               => $postdata['load_date'],
            'load_to_date'            => $postdata['load_to_date'],
            'preferred_pldate'        => $postdata['requested_move_date'],
            'deliver_date'            => $postdata['deliver_date'],
            'deliver_to_date'         => $postdata['deliver_to_date'],
            'preferred_pddate'        => $postdata['expected_delivery_date'],
            'survey_date'             => $postdata['survey_date'],
            'survey_time'             => $postdata['survey_time'],
            'followup_date'           => $postdata['follow_up_date'],
            'decision_date'           => $postdata['decision_date'],
            'days_to_move'            => $postdata['days_to_move'],
            'brand'                   => $postdata['brand'],
            'shipper_type'            => 'NAT',
            'lock_military_fields'    => 'on',
            'preferred_language'      => $postdata['preferred_language']?:'English',
            'agentid'                 => $agentId,
        ];
        $counter = 1;
        $errors  = [];
        $data['numAgents'] = count($postdata['participating_agents']);
        foreach ($postdata['participating_agents'] as $participant) {
            //$participant['type']; // Should be Select an Option, Booking Agent, Destination Agent,
            // Destination Storage Agent, Hauling Agent, Invoicing Agent,
            // Origin Agent, Origin Storage Agent
            switch ($participant['type']) {
                case 'Select an Option':
                    $participant['type'] = null;
                    break;
                case 'Booking Agent':
                    break;
                case 'Destination Agent':
                    break;
                case 'Destination Storage Agent':
                    break;
                case 'Hauling Agent':
                    break;
                case 'Invoicing Agent':
                    break;
                case 'Origin Agent':
                    break;
                case 'Origin Storage Agent':
                    break;
                case 'Estimating Agent':
                    break;
                default:
                    $errors[] = ['code'    => 'INVALID_VALUE',
                                 'message' => 'Participating Agents type is expected to be Booking Agent, Destination Agent, Destination Storage Agent, Hauling Agent, Invoicing Agent, Origin Agent, Origin Storage Agent'];
            }
            //$participant['agent']; // Should be the agent code within the system
            $sql    = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_code = ?";
            $result = $db->pquery($sql, [$participant['agent']]);
            $row    = $result->fetchRow();
            if ($row) {
                $agency = $row[0];
                $participantAgentId = $db->pquery('SELECT `vtiger_agents`.agentsid
                                          FROM `vtiger_agents`
                                          LEFT JOIN `vtiger_agentmanager`
                                            ON `vtiger_agents`.agentmanager_id = `vtiger_agentmanager`.agentmanagerid
                                        WHERE `vtiger_agentmanager`.agentmanagerid = ?',
                                        [$agency])->fetchRow()['agentsid'];
            } else {
                $errors[] = ['code'    => 'INVALID_VALUE',
                             'message' => 'Participating Agents agent is expected to be a valid agency_code in the system'];
            }
            //$participant['permission']; // Should be Full = 0, Read-only = 1, No-rates = 2, No-Access = 3
            switch ($participant['permission']) {
                case 'Full':
                    $participant['permission'] = 'full';
                    break;
                case 'Read-only':
                    $participant['permission'] = 'read_only';
                    break;
                case 'No-rates':
                    $participant['permission'] = 'no_rates';
                    break;
                case 'No-Access':
                    $participant['permission'] = 'no_access';
                    break;
                default:
                    $errors[] = ['code'    => 'INVALID_VALUE',
                                 'message' => 'Participating Agents permissions are expected to be Full, Read-only, No-rates, or No-Access'];
            }
            $data['participantDelete_'.$counter] = false;
            $data['participantId_'.$counter]     = 'none';
            $data['agent_permission_'.$counter]  = $participant['permission'];

            //TFS24463 requests that destination agent have no access
            if($participant['type'] == 'Destination Agent'){
                $data['agent_permission_'.$counter] = 'no_access';
            }

            $data['agents_id_'.$counter]         = $participantAgentId;
            $data['agent_type_'.$counter]        = $participant['type'];

            //For movestar, OA and EA agent are the same. Since they don't sent EA, copy the OA
            if ($participant['type'] == 'Origin Agent') {
                $counter++;
                $data['numAgents'] += 1;
                $data['participantDelete_'.$counter] = false;
                $data['participantId_'.$counter]     = 'none';
                $data['agent_permission_'.$counter]  = $participant['permission'];
                $data['agents_id_'.$counter]         = $participantAgentId;
                $data['agent_type_'.$counter]        = 'Estimating Agent';
            }

            $counter++;
        }
        //spit out the errors after looking at all the participating agents since there could be several problems
        if (count($errors) > 0) {
            $response = ['success' => false,
                         'errors'  => $errors];

            return json_encode($response);
        }
        $opportunity = vtws_create('Opportunities', $data, $current_user);
    } catch (WebServiceException $ex) {
        $response = generateErrorArray('FAILED_CREATION_OF_OPPORTUNITY', $ex->getMessage());

        return json_encode($response);
    }
    $opp_wsid = $opportunity['id'];
    $opp_id = explode('x', $opp_wsid)[1];
    //This can be done with the single vtws create call to Opps now should probably rework this
    //stops
    foreach ($postdata['extra_stops'] as $stop) {
        $stop_contact           = $stop['contact_name'];
        $stop_contact_parts     = explode(' ', $stop_contact);
        $first_name             = '';
        $last_name              = '';
        $stop_contact_parts_len = count($stop_contact_parts);
        if ($stop_contact_parts_len > 1) {
            $first_name = $stop_contact_parts[0];
            for ($i = 1; $i <= $stop_contact_parts_len; $i++) {
                if ($last_name == '') {
                    $last_name .= $stop_contact_parts[$i];
                } else {
                    $last_name .= ' '.$stop_contact_parts[$i];
                }
            }
        }
        $sql    = "SELECT contactid FROM `vtiger_contactdetails` WHERE firstname = ? AND lastname = ? AND email = ?";
        $result = $db->pquery($sql, [$first_name, $last_name, $stop['contact_email']]);
        $row    = $result->fetchRow();
        if ($row) {
            $stop_contact_id = $row[0];
        } else {
            try {
                $data    = ['contact_type'     => 'Transferee',
                            'lastname'         => $last_name,
                            'firstname'        => $first_name,
                            'email'            => $postdata['contact_email'],
                            'assigned_user_id' => $assigned_user_id,
                            'agentid'          => $agentId,
                ];
                $contact = vtws_create('Contacts', $data, $current_user);
            } catch (WebServiceException $ex) {
                $response = generateErrorArray('FAILED_CREATION_OF_CONTACT', $ex->getMessage());

                return json_encode($response);
            }
            //$stop_contact_id = explode('x', $contact['id'])[1];
            $stop_contact_id = $contact['id'];
        }
        $stopsData = [
                        'extrastops_sequence' => $stop['sequence'],
                        'extrastops_name' => $stop['name'],
                        'extrastops_weight' => $stop['weight'],
                        'extrastops_isprimary' => $stop['is_primary'],
                        'extrastops_address1' => $stop['address_1'],
                        'extrastops_address2' => $stop['address_2'],
                        'extrastops_phone1' => $stop['phone_1'],
                        'extrastops_phone2' => $stop['phone_2'],
                        'extrastops_phonetype1' => $stop['phone_1_type'],
                        'extrastops_phonetype2' => $stop['phone_2_type'],
                        'extrastops_city' => $stop['city'],
                        'extrastops_state' => $stop['state'],
                        'extrastops_zip' => $stop['zip'],
                        'extrastops_country' => $stop['country'],
                        'extrastops_date' => $stop['date'],
                        'extrastops_relcrmid' => $opp_wsid,
                        'extrastops_contact' => vtws_getWebserviceEntityId('Contacts', $stop_contact_id),
                        'extrastops_type' => $stop['stop_type'],
                        'agentid' => $agentId,
                        'assigned_user_id' => $assigned_user_id,
                    ];
        if ($stop['extrastops_sirvastoptype']) {
            $stop['sirva_stop_type'];
        }
        if ($stop['extrastops_description']) {
            $stop['description'];
        }
        vtws_create('ExtraStops', $stopsData, $current_user);
        //changed this to a vtws create to take advantage of the new ExtraStops module
        //OLD STOPS:
        /*$sql    = 'SELECT id FROM `vtiger_extrastops_seq`';
        $result = $db->pquery($sql, []);
        $row    = $result->fetchRow();
        $id     = $row[0];
        if (!$id) {
            $id  = 1;
            $sql = 'INSERT INTO `vtiger_extrastops_seq` (id) VALUES (2)';
            $db->pquery($sql, []);
        }
        $sql = 'UPDATE `vtiger_extrastops_seq` SET id = ?';
        $db->pquery($sql, [($id + 1)]);
        $sql =
            'INSERT INTO `vtiger_extrastops` (extrastopsid, stop_sequence, stop_description, stop_weight,
            stop_isprimary, stop_address1, stop_address2, stop_phone1, stop_phone2, stop_phonetype1,
            stop_phonetype2, stop_city, stop_state, stop_zip, stop_country, stop_date, stop_opp,
            stop_contact, stop_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $db->pquery($sql,
                    [$id,
                     $stop['sequence'],
                     $stop['name'],
                     $stop['weight'],
                     $stop['is_primary'],
                     $stop['address_1'],
                     $stop['address_2'],
                     $stop['phone_1'],
                     $stop['phone_2'],
                     $stop['phone_1_type'],
                     $stop['phone_2_type'],
                     $stop['city'],
                     $stop['state'],
                     $stop['zip'],
                     $stop['country'],
                     $stop['date'],
                     $opp_id,
                     $stop_contact_id,
                     $stop['stop_type']]);*/
    }
    //Grabs the comments related to the account
    if ($postdata['ListOfLeadNote']) {
        foreach ($postdata['ListOfLeadNote'] as $note) {
            try {
                $sql    = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
                $result = $db->pquery($sql, [$note['CreatedBy']]);
                $row    = $result->fetchRow();
                if ($row) {
                    $CreatedBy = '19x'.$row[0];
                } else {
                    $sql    = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
                    $result = $db->pquery($sql, [getenv('SIRVA_INTEGRATION_USER')]);
                    $row    = $result->fetchRow();
                    if ($row) {
                        $CreatedBy = '19x'.$row[0];
                    }
                }
                if ($note['DateTime']) {
                    $dateTime = DateTime::createFromFormat('m/d/Y H:i:s', $note['DateTime']);
                }
                $data = [
                    'commentcontent'   => $note['Note'],
                    'assigned_user_id' => (($CreatedBy)?$CreatedBy:'19x1'),
                    'related_to'       => (($convertlead['Opportunities'])?$convertlead['Opportunities']:$wsLeadId),
                    'provider'         => $note['Provider'],
                    'createdtime'      => $opp_id,
                    'note_source'      => $note['NoteSource'],
                    'agentid'          => $agentId,
                ];
                $note = vtws_create('ModComments', $data, $current_user);
            } catch (WebServiceException $ex) {
                $response = generateErrorArray('FAILED_CREATION_OF_NOTE', $ex->getMessage());

                return json_encode($response);
            }
        }
    }
    //return the ID if successful
    $response = ['success' => true, 'result' => ['OpportunityId' => $opportunity['id']]];

    return json_encode($response);
}


/**
 * Deletes a lead with the given lead id.
 *
 * @param array $postdata an array with the the lead id
 *
 * @return string $response A JSON string of success true or false
 */
function deleteLead($postdata)
{
    if (!isset($postdata['LeadId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_VALUE', 'LeadId is required to delete a lead');

        return json_encode($response);
    }
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT deleted FROM `vtiger_crmentity` WHERE crmid = ? AND setype = 'Leads'";
    $result = $db->pquery($sql, [explode('x', $postdata['LeadId'])[1]]);
    $row    = $result->fetchRow();
    if ($row) {
        if ($row[0] == 1) {
            $response = generateErrorArray('LEAD_ALREADY_DELETED', 'This lead is already deleted.');

            return json_encode($response);
        }
    } else {
        $response = generateErrorArray('INVALID_VALUE', 'LeadId has to be a valid LeadId in the system');

        return json_encode($response);
    }
    include_once 'include/Webservices/Delete.php';
    include_once 'modules/Users/Users.php';
    try {
        $user         = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        $wsid         = $postdata['LeadId'];
        vtws_delete($wsid, $current_user);
    } catch (WebServiceException $ex) {
        $response = generateErrorArray('FAILED_DELETION_OF_LEAD', $ex->getMessage());

        return json_encode($response);
    }

    //Check and delete converted Opp if available
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT `potentialid`, `contact_id` FROM `vtiger_potential` WHERE `converted_from` = ?";
    $result = $db->pquery($sql, [explode('x', $postdata['LeadId'])[1]]);
    $row    = $result->fetchRow();

    $user         = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

    if ($row) {
        try {
            $wsid = vtws_getWebserviceEntityId('Opportunities', $row['potentialid']);
            vtws_delete($wsid, $current_user);
            //Check and delete contact. Yea seems like a bad idea: TFS31922
            if ($row['contact_id'] != '' && $row['contact_id'] != 0) {
                try {
                    $wsid = vtws_getWebserviceEntityId('Contacts', $row['contact_id']);
                    vtws_delete($wsid, $current_user);
                } catch (WebServiceException $ex) {
                    $response = generateErrorArray('FAILED_DELETION_OF_Contact', $ex->getMessage());

                    return json_encode($response);
                }
            }
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_DELETION_OF_Opp', $ex->getMessage());

            return json_encode($response);
        }
    }

    $response = ['success' => true];

    return json_encode($response);
}

/**
 *     Updates an Lead/Opportunity based on an array passed in from an external source such as LMP
 *
 * @param array $postdata An array of information for the Lead/Opp
 *
 * @return string $response A JSON string of success true with the LeadId or success false and an error code
 */
function updateLead($postdata)
{
    return processLead($postdata, true);
}

/**
 *     Creates an Lead/Opportunity based on an array passed in from an external source such as LMP
 *
 * @param array $postdata An array of information for the Lead/Opp
 *
 * @return string $response A JSON string of success true with the LeadId or success false and an error code
 */
function createLead($postdata)
{
    return processLead($postdata);
}

/**
 *     Creates or updates an Lead/Opportunity based on an array passed in from an external source such as LMP
 *
 * @param array $postdata An array of information for the Lead/Opp
 *
 * @return string $response A JSON string of success true with the LeadId or success false and an error code
 */
function processLead($postdata, $updateLead = false)
{
    foreach (['HomePhone', 'WorkPhone', 'CellularPhone'] as $phoneType) {
            $postdata[$phoneType] = preg_replace('/[^0-9]/s', '', $postdata[$phoneType]);
        }

    $db     = PearDatabase::getInstance();
    $errors = [];

    $logThisFunction = true;
    //$logLineLeader = getmypid(). " : " . time() . " : ";
    $logLineLeader = getmypid(). " : ";
    if ($logThisFunction) {
        //$postdata = json_decode($_POST['element'], true);
        file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader."##################################\n", FILE_APPEND);
        file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader."#   START - ".date('Y-m-d H:i:s')."  #\n", FILE_APPEND);
        file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader."#########   JSON INPUT   #########\n", FILE_APPEND);
        file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.$_POST['element']."\n", FILE_APPEND);
        //file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader."#--------  PARSED INPUT  --------#\n", FILE_APPEND);
        //file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.print_r($postdata, true)."\n", FILE_APPEND);
        file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader."#-------   JSON RESPONSE  -------#\n", FILE_APPEND);
    }

    //Check Required Fields
    //Contact Primary Phone Type Valid Values: “Home”, “Work”, “Cell”
    $workPrimary = false;
    if (!validateMandatory($postdata['PrimaryPhoneType'])) {
        $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                     'message' => 'PrimaryPhoneType is missing or empty'];
    } else {
        $phoneTypes = ['Home Phone', 'Work Phone', 'Cell Phone'];
        if (!validate($postdata['PrimaryPhoneType'], 'multi', true, false, $phoneTypes)) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'PrimaryPhoneType is expected to be Home Phone, Work Phone or Cell Phone'];
        }
        switch ($postdata['PrimaryPhoneType']) {
            case 'Home Phone':
                $postdata['PrimaryPhoneType'] = 'Home';
                break;
            case 'Work Phone':
                $postdata['PrimaryPhoneType'] = 'Work';
                break;
            case 'Cell Phone':
                $postdata['PrimaryPhoneType'] = 'Cell';
                break;
        }
        //Contact Home Phone Required when PrimaryPhoneType = “Home”, Valid Format: “1234567890”
        if ($postdata['PrimaryPhoneType'] == 'Home') {
            if (!validateMandatory($postdata['HomePhone'])) {
                $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                             'message' => 'HomePhone is missing or empty'];
            } elseif (!validate($postdata['HomePhone'], 'phone', true)) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                             'message' => 'HomePhone is expected to be a valid phone number in the format 1234567890 or 1234567'];
            }
            //Contact Work Phone Required when PrimaryPhoneType = “Work”, Valid Format: “1234567890”
        } elseif ($postdata['PrimaryPhoneType'] == 'Work') {
            $workPrimary = true;
            if (!validateMandatory($postdata['WorkPhone'])) {
                $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                             'message' => 'WorkPhone is missing or empty'];
            } elseif (!validate($postdata['WorkPhone'], 'phone', true)) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                             'message' => 'WorkPhone is expected to be a valid phone number in the format 1234567890 or 1234567'];
            }
            //Contact Mobile Phone	Required when PrimaryPhoneType = “Cell”, Valid Format: “1234567890”
        } elseif ($postdata['PrimaryPhoneType'] == 'Cell') {
            if (!validateMandatory($postdata['CellularPhone'])) {
                $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                             'message' => 'CellularPhone is missing or empty'];
            } elseif (!validate($postdata['CellularPhone'], 'phone', true)) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                             'message' => 'CellularPhone is expected to be a valid phone number in the format 1234567890 or 1234567'];
            }
        }
    }
    //Contact First Name
    if (!validateMandatory($postdata['FirstName'])) {
        $errors[] = ['code' => 'MISSING_REQUIRED_FIELD', 'message' => 'FirstName is missing or empty'];
    }
    //Contact Last Name
    if (!validateMandatory($postdata['LastName'])) {
        $errors[] = ['code' => 'MISSING_REQUIRED_FIELD', 'message' => 'LastName is missing or empty'];
    }
    //Origin Address 1
    if (!validateMandatory($postdata['OriginAddress1'])) {
        $postdata['OriginAddress1'] = 'Will Advise';
    }
    //Origin City
    if (!validateMandatory($postdata['OriginCity'])) {
        $errors[] = ['code' => 'MISSING_REQUIRED_FIELD', 'message' => 'OriginCity is missing or empty'];
    }
    //Origin State
    if (!validateMandatory($postdata['OriginState'])) {
        $errors[] = ['code' => 'MISSING_REQUIRED_FIELD', 'message' => 'OriginState is missing or empty'];
    }
    //Origin Zip
    if (!validateMandatory($postdata['OriginZip'])) {
        $errors[] = ['code' => 'MISSING_REQUIRED_FIELD', 'message' => 'OriginZip is missing or empty'];
    }
    //Origin Country
    if (!validateMandatory($postdata['OriginCountry'])) {
        $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                     'message' => 'OriginCountry is missing or empty'];
    }
    //Destination Address 1
    if (!validateMandatory($postdata['DestinationAddress1'])) {
        $postdata['DestinationAddress1'] = 'Will Advise';
    }
    if (!validateMandatory($postdata['ListOfLeadNote'])) {
        foreach ($postdata['ListOfLeadNote'] as $note) {
            if (!validateMandatory($note['Note'])) {
                $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                             'message' => 'ListOfLeadNote:Note is missing or empty'];
            }
        }
    }
    //Contact Email Address 		Valid Format: “user@domain.ext”
    if (!validate($postdata['EmailAddress'], 'email')) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'EmailAddress is expected to be a valid email in the format user@domain.ext'];
    }
    //Validate field inputs
    //Lead Disposition Valid values: “New”, “Fax/busy”, “No answer”, “Left voicemail”, “Prefer call back”, “Do not call requested”, “Not interested”, “Wrong/disconnected #”, “Converted to qualified”
    $Dispositions = ['New',
                     'Fax/busy',
                     'No answer',
                     'Left voicemail',
                     'Prefer call back',
                     'Do not call requested',
                     'Not interested',
                     'Wrong/disconnected #',
                     'Converted to qualified'];
    if (!validate($postdata['AgentDisposition'], 'multi', false, false, $Dispositions)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'AgentDisposition is expected to be New, Fax/busy, No answer, left voicemail, Prefer call back, Do not call requested, Not interested, Wrong/disconnected #, or Converted to qualified'];
    }
    //Furnish Level Valid values: “Heavy”, “Light”, “Medium”
    $FurnishLevels = ['Heavy', 'Light', 'Medium'];
    if (!validate($postdata['FurnishLevel'], 'multi', false, false, $FurnishLevels)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'FurnishLevel is expected to be Heavy, Light, or Medium'];
    }
    //Contact Time Zone Valid Values: “ADT”, “AST”, “AKDT”, “AKST”, “CDT”, “CST”, “EDT”, “EST”, “HADT”, “HAST”, “MDT”, “MST”, “NDT”, “NST”, “PDT”, “PST”
    $TimeZones =
        ['ADT',
         'AST',
         'AKDT',
         'AKST',
         'CDT',
         'CST',
         'EDT',
         'EST',
         'HADT',
         'HAST',
         'MDT',
         'MST',
         'NDT',
         'NST',
         'PDT',
         'PST',
         ''];
    if (!validate($postdata['TimeZone'], 'multi', false, false, $TimeZones)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'TimeZone is expected to be ADT, AST, AKDT, AKST, CDT, CST, EDT, EST, HADT, HAST, MDT, MST, NDT, NST, PDT, or PST'];
    }

    //Dwelling Type Valid values: “1 Bedroom Apt.”, “1 Bedroom House”, “2 Bedroom Apt.”, “2 Bedroom House”, “3 Bedroom House”, “3+ Bedroom Apt.”
    $DwellingTypesMisSpelled = [
        '1 Bedroom Apt.',
        '2 Bedroom Apt.',
        '3 Bedroom Apt.'
    ];
    $postdata['DwellingType'] = base64_decode($postdata['DwellingType']);
    if (in_array($postdata['DwellingType'], $DwellingTypesMisSpelled)) {
        $postdata['DwellingType'] = rtrim($postdata['DwellingType'], '.');
    }
    $DwellingTypes = [
        'Studio',
        'Small Move (1000 lbs.)',
        'Small Move (2000 lbs.)',
        '1 Bedroom Apt',
        '1 Bedroom House',
        '2 Bedroom Apt',
        '2 Bedroom House',
        '3 Bedroom House',
        '3 Bedroom Apt.',
        '4+ Bedroom House',
        '3+ Bedroom Apt.',
        ];

    if (!in_array($postdata['DwellingType'], $DwellingTypes)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'DwellingType is expected to be Studio, Small Move (1000 lbs.), Small Move (2000 lbs.), 1 Bedroom Apt, 1 Bedroom House, 2 Bedroom Apt, 2 Bedroom House, 3 Bedroom House, 3 Bedroom Apt., 4+ Bedroom House, 3+ Bedroom Apt.'];
    }

    //Contact Prefer Time Valid Values: “AM”,”PM”, “Either”
    $ContactTimes = ['AM', 'PM', 'Either', ''];
    if (!validate($postdata['PreferTime'], 'multi', false, false, $ContactTimes)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'PreferTime is expected to be AM, PM, or Either'];
    }
    //Business Channel Valid values: Consumer, Corporate, Government, Military
    $BusinessChannels = ['Consumer', 'Corporate', 'Government', 'Military', ''];
    if (!validate($postdata['BusinessChannel'], 'multi', false, false, $BusinessChannels)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'BusinessChannel is expected to be Consumer, Corporate, Government, or Military'];
    }
    //Move Type Valid values: Alaska, Canada, Cross Border, Hawaii, International, Interprovincial, Interstate, Intraprovincial, Intrastate, Local, Local Canada, Local US
    $MoveType = ['Alaska',
                 'Canada',
                 'Cross Border',
                 'Hawaii',
                 'International',
                 'Interprovincial',
                 'Interstate',
                 'Intraprovincial',
                 'Intrastate',
                 'Local',
                 'Local Canada',
                 'Local US',
                 ''];
    if (!validate($postdata['MoveType'], 'multi', false, false, $MoveType)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'MoveType is expected to be Alaska, Canada, Cross Border, Hawaii, International, Interprovincial, Interstate, Intraprovincial, Intrastate, Local, Local Canada, or Local US'];
    }
    //Marketing Channel Valid values: Affinity, Interactive, Lead Buying, Other, Referrals, Telephone, Traditional Marketing
    $MarketingChannel =
        ['Affinity', 'Interactive', 'Lead Buying', 'Other', 'Referrals', 'Telephone', 'Traditional Marketing', ''];
    if (!validate($postdata['MarketingChannel'], 'multi', false, false, $MarketingChannel)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'MarketingChannel is expected to be Affinity, Interactive, Lead Buying, Other, Referrals, Telephone, or Traditional Marketing'];
    }
    //Contact Language Valid Values: English, French, Spanish, Others
    $Languages = ['English', 'French', 'Spanish', 'Others', ''];
    if (!validate($postdata['Language'], 'multi', false, false, $Languages)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'Language is expected to be English, French, Spanish, or Others'];
    }
    //Own New 	Valid values: Yes, No, Not Sure, Refused
    $OwnTypes = ['Yes', 'No', 'Not Sure', 'Refused', ''];
    if (validateMandatory($postdata['OwnNew'])) {
        if (!validate($postdata['OwnNew'], 'multi', false, false, $OwnTypes)) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'OwnNew is expected to be Yes, No, Not Sure, or Refused'];
        }
    } else {
        $postdata['OwnNew'] = 'Select an Option';
    }
    //Own Current Valid values: “Yes”, “No”, “Not Sure”, “Refused”
    if (validateMandatory($postdata['OwnCurrent'])) {
        if (!validate($postdata['OwnCurrent'], 'multi', false, false, $OwnTypes)) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'OwnCurrent is expected to be Yes, No, Not Sure, or Refused'];
        }
    } else {
        $postdata['OwnCurrent'] = 'Select an Option';
    }
    //Funded Valid values: AGT LMP, AGT QLAB, CORP LMP
    $Funded = ['AGT LMP', 'AGT QLAB', 'CORP LMP', ''];
    if (!validate($postdata['Funded'], 'multi', false, false, $Funded)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'Funded is expected to be AGT LMP, AGT QLAB, or CORP LMP'];
    }
    //Employer Assisting 		Valid values: “Yes”, “No”
    $Flags = ['Y', 'N', ''];
    if (!validate($postdata['EmployerAssistingFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'EmployerAssistingFlg is expected to be Y or N'];
    }
    //Office and Industrial 	Valid values: “Yes”, “No”
    if (!validate($postdata['OfficeandIndustrialFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'OfficeandIndustrialFlg is expected to be Y or N'];
        if ($postdata['OfficeandIndustrialFlg'] == 'Y') {
            $postdata['move_type'] = 'O&I';
        }
    }
    //Offer Valuation  		Valid values: “Yes”, “No”
    if (!validate($postdata['OfferValuationFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'OfferValuationFlg is expected to be Y or N'];
    }
    //Moving a Vehicle 		Valid values: “Yes”, “No”
    if (!validate($postdata['MovingVehicleFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'MovingVehicleFlg is expected to be Y or N'];
    }
    //Out of Origin 			Valid values: “Yes”, “No”
    if (!validate($postdata['OutofOriginFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'OutofOriginFlg is expected to be Y or N'];
    }
    //Out of Time 			Valid values: “Yes”, “No”
    if (!validate($postdata['OutofTimeFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'OutofTimeFlg is expected to be Y or N'];
    }
    //Small Move 				Valid values: “Yes”, “No”
    if (!validate($postdata['SmallMoveFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'SmallMoveFlg is expected to be Y or N'];
    }
    //Out of Area 			Valid values: “Yes”, “No”
    if (!validate($postdata['OutofAreaFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'OutofAreaFlg is expected to be Y or N'];
    }
    //FlexibleOnDays			Valid values: “Yes”, “No”
    if (!validate($postdata['FlexibleOnDays'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'FlexibleOnDays is expected to be Y or N'];
    }
    //SIRVAExpectsPhEstimateFlg 			Valid values: “Yes”, “No”
    if (!validate($postdata['SIRVAExpectsPhEstimateFlg'], 'multi', false, false, $Flags)) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'SIRVAExpectsPhEstimateFlg is expected to be Y or N'];
    }
    //Employer Contact Email 	Valid Format: “user@domain.ext”
    if (!validate($postdata['EmployerContactEmail'], 'email')) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'EmployerContactEmail is expected to be a valid email in the format user@domain.ext'];
    }
    //Employer Contact Phone 	Valid Format: “1234567890”
    if (!validate($postdata['EmployerContactPhone'], 'phone')) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'EmployerContactPhone is expected to be a valid phone number in the format 1234567890 or 1234567'];
    }

    include_once 'include/Webservices/Create.php';
    include_once 'modules/Users/Users.php';

    $agencyCode = $postdata["LMPAssignedAgentOrgId"];

    //$user         = new Users();
    //$users = getUsersForAgency($agencyCode);
    //if(!empty($users)){
    //    foreach($users as $userFromUsers){
    //        $tempUser = Users_Record_Model::getInstanceById($userFromUsers,'Users');
    //        if($tempUser->isCoordinator()){
    //            $assigned_user_id = $tempUser->getId();
    //            break;
    //        }
    //    }
    //    if ($assigned_user_id) {
    //        $assigned_user_id = '19x'.$assigned_user_id;
    //    } else {
    //        $assigned_user_id = Users_Record_Model::getInstanceById($users[0],'Users')->getId();
    //        if (empty($assigned_user_id)) {
    //            $response = generateErrorArray('INVALID_VALUE', 'LMPAssignedAgentOrgId has to be a valid agency_code in the system');
//
    //            if ($logThisFunction) {
    //                file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
    //            }
    //            return json_encode($response);
    //        }
    //    }
    //} else {
    //    $assigned_user_id = '19x1';
    //}
    $assigned_user_id = '1'; //'19x1';
    $agentid = $db->pquery('SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_code = ?', [$agencyCode])->fetchRow()[0];

    $convertLead = false;
    if (validateMandatory($postdata['SurveyAppointmentPlanned'])) {
        $convertLead = true;
        if (!validateMandatory($postdata['Surveyor'])) {
            //$postdata['Surveyor'] = getUserNameForUserId(getUsersForAgency($postdata['LMPAssignedAgentOrgId'])[0]);
            $postdata['Surveyor'] = 'admin';
            if (!validateMandatory($postdata['Surveyor'])) {
                $errors[] = ['code'    => 'INVALID_VALUE',
                             'message' => 'Surveyor must be specified or there must be a user in the agency'];
            }
        }
    }
    if ($workPrimary) {
        $phone = $postdata["WorkPhone"];
        $ext   = $postdata["WorkPhExt"];
    } elseif ($postdata['PrimaryPhoneType'] == 'Home') {
        $phone = $postdata["HomePhone"];
    } else {
        $phone = $postdata['CellularPhone'];
    }
    $sales_person = '1';
    $coordinator = false;
    if (!is_null($postdata["AMCSalesPersonId"])) {
    $sql    = "SELECT id, estimates_destination_country as avl_code, estimate_id as nvl_code, move_coordinator, move_coordinator_navl FROM `vtiger_users` WHERE estimates_destination_country = ? OR estimate_id = ?";
    $result = $db->pquery($sql, [$postdata["AMCSalesPersonId"], $postdata["AMCSalesPersonId"]]);
    $row    = $result->fetchRow();
    if ($row) {
        $sales_person = $row['id'];
        //If this is an AVL move and we have a coordinator,
        //else, use NVL if available
        if($row['avl_code'] == $postdata["AMCSalesPersonId"] && $row["move_coordinator"] != ''){
            $coordinator = "19x".$row["move_coordinator"];
        }elseif($row['nvl_code'] == $postdata["AMCSalesPersonId"] && $row["move_coordinator_navl"] != ''){
            $coordinator = "19x".$row["move_coordinator_navl"];
        }
    } elseif ($postdata["AMCSalesPersonId"] != '') {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'AMCSalesPersonId has to belong to a user in the system'];
        } else {
            $sales_person = '1';
        }
    }

    $business_line = false;
    switch ($postdata['MoveType']) {
        case 'Local Canada':
        case 'Local US':
        case 'Local':
            $business_line = "Local Move";
            break;
        case 'Interstate':
        case 'Inter-Provincial':
        case 'Cross Border':
            $business_line = "Interstate Move";
            break;
        case 'O&I':
            $business_line = "Commercial Move";
            break;
        case 'Intrastate':
        case 'Intra-Provincial':
            $business_line = "Intrastate Move";
            break;
        case 'Alaska':
        case 'Hawaii':
        case 'International':
            $business_line = "International Move";
            break;
    }
    $validate_dates = [
        [
            'name'=>'LeadReceiveDate',
            'required'=>1
        ],
        [
            'name'=>'FulfillmentDate',
            'required'=>0
        ],
        [
            'name'=>'ExpectedDeliveryDate',
            'required'=>0
        ],
        [
            'name'=>'RequiredMoveDate',
            'required'=>0
        ]
    ];
    foreach ($validate_dates as $check_date_key) {
        if ($check_date_key['required']===1&&!validateMandatory($postdata[$check_date_key['name']])) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => $check_date_key['name'].' requires a value.'];
        }
        try {
            if (!isset($postdata[$check_date_key['name']])||empty($postdata[$check_date_key['name']])) {
                throw new Exception($check_date_key['name'].' was not set');
            }
            $valid_date = new DateTime($postdata[$check_date_key['name']]);//will throw an exception if not a valid date
            $postdata[$check_date_key['name']] = strtotime($postdata[$check_date_key['name']]);
            $postdata[$check_date_key['name']] = date('Y-m-d', $postdata[$check_date_key['name']]);
        } catch (Exception $e) {
            //the DateTime will through an error if the variable is not a proper date
            $postdata[$check_date_key['name']] = null;//set to blank
        }
    }

    if (count($errors) > 0) {
        $response = ['success' => false, 'errors' => $errors];

        if ($logThisFunction) {
            file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
        }
        return json_encode($response);
    }
    //file_put_contents('logs/devLog.log', "\n Works up to here : ".$postdata['LeadReceiveDate'], FILE_APPEND);
    if(empty($postdata['AMCSalesPersonId'])) {
      $apptType = 'AAS';
    } else {
      $apptType = 'CAS';
    }
    //TODO: This should have better rules.
    if (!validateMandatory($postdata['AMCSalesPersonId'])) {
        $salesPersonModel             = Users_Record_Model::getInstanceById($sales_person, 'Users');
        $postdata['AMCSalesPersonId'] = $salesPersonModel->get('amc_salesperson_id');
    }
    if (validateMandatory($postdata['LMPAssignedAgentOrgId'])) {
        if($coordinator){
            $assigned_user_id = $coordinator;
        }else{
            $users = getUsersForAgency($postdata['LMPAssignedAgentOrgId']);
            foreach ($users as $user) {
                $tempUser = Users_Record_Model::getInstanceById($user, 'Users');
                if ($tempUser && $tempUser->isCoordinator()) {

                    //TFS32091
                    //String match username for 'AGT'.  Per Josh @ 2017-09-19 10:20AM.
                    if(getenv('INSTANCE_NAME') == 'sirva'){
                        if(substr( $tempUser->get('user_name'), 0, 3 ) === "AGT"){
                            $assigned_user_id = '19x'.$tempUser->getId();

                            break;
                        }
                        //If they want, in the future, to fall back to the last coordinator if an 'AGT' user does not exists, comment this out.
                        continue;
                    }

                    if (!validateMandatory($postdata['AMCSalesPersonId'])) {
                        $postdata['AMCSalesPersonId'] = $tempUser->get('amc_salesperson_id');
                    }
                    $assigned_user_id = '19x'.$tempUser->getId();
                    //return the "Last" coordinator.  Per Josh @ 2017-04-28 7pm.
                    //break;
                }
            }
        }
    }
    if (!validateMandatory($postdata['AAProgramName'])) {
        /*
         * pull this because the create will return the existing entity id.
        $sql = "SELECT leadsourcemanagerid FROM `vtiger_leadsourcemanager` WHERE source_name = ? AND agency_code = ?";
        $result = $db->pquery($sql,[$postdata['AASourceName'],$agencyCode]);
        $row = $result->fetchRow();
        if($row){
            $sourceId = $row[0];
            //encode it as is expected of a related id
            $sourceId = vtws_getWebserviceEntityId('LeadSourceManager', $sourceId);
        } else {
        */
        //Handle stuff when it's like not there.
        $leadSrcResponse = json_decode(createLeadSource($postdata));
        if (
            $leadSrcResponse->success == false ||
            $leadSrcResponse->success === 'false'
        ) {
            //so we failed... just die
            if ($logThisFunction) {
                file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($leadSrcResponse)."\n", FILE_APPEND);
            }
            return json_encode($leadSrcResponse);
        } else {
            $sourceId = $leadSrcResponse->result->LeadSrcId;
            //echo $sourceId;
            //$sourceId = substr($sourceId,3,strlen($sourceId));
            //echo $sourceId;
        }
        //}
    }

    //Fix to make sure incoming country matches our system
    switch ($postdata['OriginCountry']) {
        case 'Canada':
        case 'canada':
        case 'CAN':
            $postdata['OriginCountry'] = 'Canada';
            break;

        case 'United States of America':
        case 'United States':
        case 'USA':
        case 'US':
        case 'usa':
        case 'us':
            $postdata['OriginCountry'] = 'United States';
            break;

        default:
            break;
    }
    switch ($postdata['DestinationCountry']) {
        case 'Canada':
        case 'canada':
        case 'CAN':
            $postdata['DestinationCountry'] = 'Canada';
            break;

        case 'United States of America':
        case 'United States':
        case 'USA':
        case 'US':
        case 'usa':
        case 'us':
            $postdata['DestinationCountry'] = 'United States';
            break;

        default:
            break;
    }

    try {
        $data = [
            'agentid'              => $agentid,
            'firstname'            => $postdata["FirstName"],
            'lastname'             => $postdata["LastName"],
            'phone'                => $phone,
            'primary_phone_type'   => $postdata["PrimaryPhoneType"],
            'mobile'               => $postdata["CellularPhone"],
            'email'                => $postdata["EmailAddress"],
            // 'secondaryemail'=>$postdata[""],
            // 'emailoptout'=>$postdata[""],
            'leadsource'           => $postdata["MarketingChannel"],
            'assigned_user_id'     => $assigned_user_id,
            'leadstatus'           => $postdata["AgentDisposition"],
            // 'include_packing'=>$postdata[""],
            'comm_res'             => (($postdata["OfficeandIndustrial"] == 'Y')?'Commercial':'Residential'),
            'sales_person'         => $sales_person,
            'shipper_type'         => 'COD',
            // 'lead_type'=>$postdata[""],
            'move_type'            => $postdata['MoveType'],
            'lead_type'            => $postdata['BusinessChannel'],
            'business_channel'     => $postdata["BusinessChannel"],
            'funded'               => $postdata["Funded"],
            'out_of_area'          => (($postdata["OutofAreaFlg"] == 'Y')?'1':'0'),
            'out_of_origin'        => (($postdata["OutofOriginFlg"] == 'Y')?'1':'0'),
            'small_move'           => (($postdata["SmallMoveFlg"] == 'Y')?'1':'0'),
            'phone_estimate'       => (($postdata["SIRVAExpectsPhEstimateFlg"] == 'Y')?'1':'0'),
            'origin_address1'      => $postdata["OriginAddress1"],
            'origin_address2'      => $postdata["OriginAddress2"],
            'origin_city'          => $postdata["OriginCity"],
            'origin_state'         => $postdata["OriginState"] != '' ? $postdata["OriginState"] : 'N/A',
            'origin_zip'           => $postdata["OriginZip"],
            'origin_country'       => $postdata["OriginCountry"],
            'origin_phone1'        => $postdata["origin_phone1"],
            'origin_phone1_ext'    => $postdata["origin_phone1_ext"],
            'origin_phone1_type'   => $postdata["OriginPhone1Type"],
            'origin_phone2'        => $postdata["origin_phone2"],
            'origin_phone2_ext'    => $postdata["OriginPhone2Ext"],
            'origin_phone2_type'   => $postdata["OriginPhone2Type"],
            'origin_fax'           => $postdata["FaxPhone"],
            // 'origin_flightsofstairs'=>$postdata[""],
            // 'origin_description'=>$postdata[""],
            'destination_address1' => $postdata["DestinationAddress1"],
            'destination_address2' => $postdata["DestinationAddress2"],
            'destination_city'     => $postdata["DestinationCity"],
            'destination_state'    => $postdata["DestinationState"] != '' ? $postdata["DestinationState"] : 'N/A',
            'destination_zip'      => $postdata["DestinationZip"],
            'destination_country'  => $postdata["DestinationCountry"],
            'destination_phone1'      => $postdata["DestinationPhone1"],
            'destination_phone1_ext'  => $postdata["DestinationPhone1Ext"],
            'destination_phone1_type' => $postdata["DestinationPhone1Type"],
            'destination_phone2'      => $postdata["DestinationPhone2"],
            'destination_phone2_ext'  => $postdata["DestinationPhone2Ext"],
            'destination_phone2_type' => $postdata["DestinationPhone2Type"],
            // 'destination_phone1'=>$postdata[""],
            // 'destination_phone1_ext'=>$postdata[""],
            // 'destination_phone1_type'=>$postdata[""],
            // 'destination_phone2'=>$postdata[""],
            // 'destination_phone2_ext'=>$postdata[""],
            // 'destination_phone2_type'=>$postdata[""],
            // 'destination_fax'=>$postdata[""],
            // 'destination_flightsofstairs'=>$postdata[""],
            // 'destination_description'=>$postdata[""],
            // 'pack'=>$postdata[""],
            // 'pack_to'=>$postdata[""],
            // 'preferred_ppdate'=>$postdata[""],
            // 'load_from'=>$postdata[""],
            // 'load_to'=>$postdata[""],
            'preferred_pldate'     => $postdata["RequiredMoveDate"],
            // 'deliver'=>$postdata[""],
            // 'deliver_to'=>$postdata[""],
            'preferred_pddate'     => $postdata["ExpectedDeliveryDate"],
            // 'follow_up'=>$postdata[""],
            // 'decision'=>$postdata[""],
            'days_to_move'         => $postdata["DaysToMove"],
            // 'description'=>$postdata[""],
            'enabled'              => (($postdata["EmployerAssistingFlg"] == 'Y')?'1':'0'),
            'contact_name'         => $postdata["EmployerContactName"],
            'contact_email'        => $postdata["EmployerContactEmail"],
            'contact_phone'        => $postdata["EmployerContactPhone"],
            'company'              => $postdata["EmployerCompanyName"],
            'employer_comments'    => $postdata["Comments"],
            'special_terms'        => $postdata["SpecialItems"],
            'lmp_lead_id'          => $postdata['LMPLeadId'],
            'cc_disposition'       => $postdata['CCDisposition'],
            'brand'                => $postdata['Brand'],
            'organization'         => $postdata['Organization'],
            'timezone'             => $postdata['TimeZone'],
            'prefer_time'          => $postdata['PreferTime'],
            'languages'            => $postdata['Language'],
            'dwelling_type'        => $postdata['DwellingType'],
            'own_current'          => $postdata['OwnCurrent'],
            'own_new'              => $postdata['OwnNew'],
            'flexible_on_days'     => (($postdata['FlexibleOnDays'] == 'Y')?'1':'0'),
            'lead_receive_date'    => $postdata['LeadReceiveDate'],
            'fulfillment_date'     => $postdata['FulfillmentDate'],
            'program_name'         => $postdata['AASourceName'],
            //pulled from leadsourcemanager
            'source_name'          => $sourceId,
            'offer_number'         => $postdata['OfferNumber'],
            'promotion_terms'      => $postdata['PromotionTerms'],
            'moving_vehicle'       => (($postdata['MovingVehicleFlg'] == 'Y')?'1':'0'),
            'number_of_vehicles'   => $postdata['NumberOfVehicles'],
            'vehicle_year'         => $postdata['VehicleYear'],
            'vehicle_make'         => $postdata['VehicleMake'],
            'vehicle_model'        => $postdata['VehicleModel'],
            'offer_valuation'      => (($postdata['OfferValuationFlg'] == 'Y')?'1':'0'),
            'out_of_time'          => (($postdata['OutofTimeFlg'] == 'Y')?'1':'0'),
            'furnish_level'        => $postdata['FurnishLevel'],
            //mapped from other fields
            'business_line'        => $business_line,
            //not in UI but still need to capture
            'amc_salesperson_id'   => $postdata['AMCSalesPersonId'],
            'ade_lead_id'          => $postdata['ADELeadId']
            //no longer in UI
            // 'salutationtype'=>$postdata[""],
            // 'lead_no'=>$postdata[""],
            // 'createdtime'=>$postdata[""],
            // 'modifiedtime'=>$postdata[""],
            // 'modifiedby'=>$postdata[""],
            // 'lane'=>$postdata[""],
            // 'code'=>$postdata[""],
            // 'city'=>$postdata[""],
            // 'country'=>$postdata[""],
            // 'state'=>$postdata[""],
            // 'pobox'=>$postdata[""],
            // 'created_user_id'=>$postdata[""],
            // 'id'=>$postdata[""],
        ];
        if($updateLead){
            $data['id'] = $postdata['LeadId'];
        }
        if ($ext) {
            $data['phone_primary_ext'] = $ext;
        }

        $user                   = new Users();
        $current_user           = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        $lead = $updateLead ? vtws_revise($data, $current_user) : vtws_create('Leads', $data, $current_user);
    } catch (WebServiceException $ex) {
        $response = $updateLead ? generateErrorArray('FAILED_UPDATE_OF_LEAD', $ex->getMessage()) : generateErrorArray('FAILED_CREATION_OF_LEAD', $ex->getMessage());

        if ($logThisFunction) {
            file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
        }
        return json_encode($response);
    }
    $wsLeadId = $lead['id'];
    $leadId   = explode('x', $wsLeadId)[1];
    //if we have survey data and a lead to convert do it
    if (($lead && $postdata['Surveyor'] && $postdata['SurveyAppointmentPlanned'] && $postdata['SurveyAppointmentDuration'] && $convertLead) || $postdata["AutoAssign"] == 'N') {
        include_once 'include/Webservices/ConvertLead.php';
        $appointment = isset($postdata['SurveyAppointmentPlanned']) && isset($postdata['SurveyAppointmentDuration']) && $postdata['SurveyAppointmentPlanned'] != '' && $postdata['SurveyAppointmentDuration'] != '';
        if(!$appointment) {
          $apptType = '';
        }
        try {
            $non_conforming = 0;
            $non_conforming_inputs = ['OutofAreaFlg' => 'Out of Area','OutofOriginFlg' => 'Out of Origin','OutofTimeFlg' => 'Out of Time','OfficeandIndustrialFlg' => 'Office And Industrial','SmallMoveFlg' => 'Small Move','SIRVAExpectsPhEstimateFlg' => 'Phone Estimate'];
            $non_conforming_params = '';
            foreach ($non_conforming_inputs as $key => $param) {
                if ($postdata[$key] == 'Y') {
                    $non_conforming = 1;
                    if ($non_conforming_params == '') {
                        $non_conforming_params .= "$param";
                    } else {
                        $non_conforming_params .= ", $param";
                    }
                }
            }

            switch($postdata["PersonaCode"]){
                case '100':
                    $segment = 'Amenity Seeker';
                    $segment_desc = 'VIP/white glove treatment where we take care of everything so they don’t have to lift a finger.';
                    break;
                case '200':
                    $segment = 'Reliable Partner';
                    $segment_desc = 'Value the relationship with the mover and want the reassurance that their goods are handled with care and safely transported.';
                    break;
                case '300':
                    $segment = 'Efficiency Fanatic';
                    $segment_desc = 'Want a well-defined move process that’s done quickly and done right.  We have to anticipate what their needs are and keep up with their demands.';
                    break;
                case '400':
                    $segment = 'Value Driven';
                    $segment_desc = 'Expects exceptional, professional value without compromise. Sense that scored quality service for a great price.';
                    break;
                default:
                    $segment = '';
                    $segment_desc = '';
            }
            $entityValues = [
                'transferRelatedRecordsTo' => 'Contacts',
                'assignedTo'               => $assigned_user_id,
                'leadId'                   => $wsLeadId,
                'entities'                 => [
                    'Contacts'      => [
                        'create'       => 1,
                        'name'         => 'Contacts',
                        'contact_type' => 'Transferee',
                        'lastname'     => $postdata["LastName"],
                        'firstname'    => $postdata["FirstName"],
                        'email'        => $postdata["EmailAddress"],
                    ],
                    'Opportunities' => [
                        'create'               => 1,
                        'name'                 => 'Opportunities',
                        'origin_address1'      => $postdata["OriginAddress1"],
                        'billing_type'         => 'COD',
                        'origin_city'          => $postdata["OriginCity"],
                        'opp_type'             => $postdata['BusinessChannel'],
                        'lmp_lead_id'          => $postdata['LMPLeadId'],
                        'program_name'         => $postdata['AASourceName'],
                        'source_name'          => $sourceId,
                        'origin_state'         => $postdata["OriginState"],
                        'non_conforming'       => $non_conforming,
                        'non_conforming_params'=> $non_conforming_params,
                        'origin_zip'           => $postdata["OriginZip"],
                        'origin_country'       => $postdata["OriginCountry"],
                        'warm_transfer'        => $postdata["WTIndicator"] == 'Y' ? 1 : 0,
                        'origin_phone1'           => $postdata["origin_phone1"],
                        'origin_phone1_ext'       => $postdata["origin_phone1_ext"],
                        'segment'                 => $segment,
                        'segment_desc'            => $segment_desc,
                        'origin_phone1_type'      => $postdata["OriginPhone1Type"],
                        'origin_phone2'           => $postdata["origin_phone2"],
                        'appointment_type'        => $apptType,
                        'origin_phone2_ext'       => $postdata["OriginPhone2Ext"],
                        'origin_phone2_type'      => $postdata["OriginPhone2Type"],
                        'destination_address1' => $postdata["DestinationAddress1"],
                        'destination_country'  => $postdata["DestinationCountry"],
                        'destination_phone1'      => $postdata["DestinationPhone1"],
                        'destination_phone1_ext'  => $postdata["DestinationPhone1Ext"],
                        'destination_phone1_type' => $postdata["DestinationPhone1Type"],
                        'destination_phone2'      => $postdata["DestinationPhone2"],
                        'destination_phone2_ext'  => $postdata["DestinationPhone2Ext"],
                        'destination_phone2_type' => $postdata["DestinationPhone2Type"],
                        'potentialname'        => (($postdata["EmployerCompanyName"])?$postdata["EmployerCompanyName"]
                            :$postdata["FirstName"]." ".$postdata["LastName"]),
                        'sales_stage'          => 'Prospecting',
                        'closingdate'          => isset($postdata['FulfillmentDate']) ? $postdata['FulfillmentDate'] : null,
                        'move_type'            => $postdata['MoveType'],
                        'business_line'        => $business_line,
                        'preferred_language'      => $postdata['Language']?:'English',
                    ],
                ],
            ];
            if ($postdata["EmployerCompanyName"]) {
                $entityValues['entities']['Accounts'] = [
                    'create'      => 1,
                    'name'        => 'Accounts',
                    'accountname' => $postdata["EmployerCompanyName"],
                ];
            }
            $convertlead = vtws_convertlead($entityValues, $current_user, $updateLead);
        } catch (Exception $e) {
            $response = generateErrorArray('FAILED_LEAD_CONVERSION', 'Failed to convert the Lead to an Opportunity');

            if ($logThisFunction) {
                file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
            }
            return json_encode($response);
        }
        if ($appointment) {
        try {
            $sql    = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
            $result = $db->pquery($sql, [$postdata['Surveyor']]);
            $row    = $result->fetchRow();
            if ($row) {
                $surveyor = '19x'.$row[0];
            } else {
                $response = generateErrorArray('INVALID_VALUE', 'Surveyor has to be a valid username in the system');

                if ($logThisFunction) {
                    file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
                }
                return json_encode($response);
            }
            $final_survey_date = '';
            $final_survey_time = '';
            $final_end_survey = '';
                try {
                    if (isset($postdata['SurveyAppointmentPlanned'])&&isset($postdata['SurveyAppointmentDuration'])) {
                    $postdata['SurveyAppointmentPlanned'] = strtotime($postdata['SurveyAppointmentPlanned']);
                        $postdata['SurveyAppointmentPlanned'] = date('m/d/Y H:i:s', $postdata['SurveyAppointmentPlanned']);

                    $surveyDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $postdata['SurveyAppointmentPlanned']);
                    $surveyEndTime  = new DateTime;
                    $surveyEndTime->setTimestamp(strtotime('+'.$postdata['SurveyAppointmentDuration'].' minutes',
                                                           $surveyDateTime->getTimestamp()));
                    $final_survey_date = $surveyDateTime->format('Y-m-d');
                        $final_survey_time = DateTimeField::convertToDBTimeZone($surveyDateTime->format('H:i:s'))->format('H:i:s');
                        $final_end_survey = DateTimeField::convertToDBTimeZone($surveyEndTime->format('H:i:s'))->format('H:i:s');

                        if ($convertlead['Opportunities']) {
                        $opp = Vtiger_Record_Model::getInstanceById(explode('x', $convertlead['Opportunities'])[1], 'Opportunities');
                        $opp->set('survey_date', $final_survey_date);
                        $opp->set('survey_time', $final_survey_time);
                            $opp->set('mode', 'edit');
                        $opp->save();
                    }
                }
                } catch (Exception $e) {
                $response = generateErrorArray('FAILED_CREATION_OF_LEAD', $e->getMessage());
                return json_encode($response);
            }


            //file_put_contents('logs/devLog.log', "\n surveyStartTime : ".$surveyDateTime->getTimestamp(), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n surveyEndTime : ".$surveyEndTime->getTimestamp(), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n difference in seconds : ".(int)((int)$surveyDateTime->getTimestamp()-(int)$surveyEndTime->getTimestamp()), FILE_APPEND);
            $data   = [
                'survey_date'      => $final_survey_date,
                'survey_type'      => 'On-site',
                'assigned_user_id' => $surveyor,
                'survey_status'    => 'Assigned',
                'survey_time'      => $final_survey_time,
                'comm_res'         => (($postdata["OfficeandIndustrial"] == 'Y')?'Commercial':'Residential'),
                'survey_end_time'  => $final_end_survey,
                'account_id'       => (($convertlead['Accounts'])?$convertlead['Accounts']:''),
                'contact_id'       => (($convertlead['Contacts'])?$convertlead['Contacts']:''),
                'potential_id'     => (($convertlead['Opportunities'])?$convertlead['Opportunities']:''),
                'address1'         => $postdata["OriginAddress1"],
                'address2'         => $postdata["OriginAddress2"],
                'city'             => $postdata["OriginCity"],
                'state'            => $postdata["OriginState"],
                'zip'              => $postdata["OriginZip"],
                'country'          => $postdata["OriginCountry"],
                'phone1'           => $phone,
                'phone2'           => $postdata["CellularPhone"],
                'google_apt_id'    => $postdata["AppointmentID"],
            ];
            $survey = vtws_create('Surveys', $data, $current_user);
            //file_put_contents('logs/devLog.log', "\n survey : ".print_r($survey, true), FILE_APPEND);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_CREATION_OF_SURVEY', $ex->getMessage());

            if ($logThisFunction) {
                file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
            }
            return json_encode($response);
        }
    }
    }
    if ($postdata['ListOfLeadNote']) {
        foreach ($postdata['ListOfLeadNote'] as $note) {
            try {
                $sql    = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
                $result = $db->pquery($sql, [$note['CreatedBy']]);
                $row    = $result->fetchRow();
                if ($row) {
                    $CreatedBy = '19x'.$row[0];
                } else {
                    $sql    = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
                    $result = $db->pquery($sql, [getenv('SIRVA_INTEGRATION_USER')]);
                    $row    = $result->fetchRow();
                    if ($row) {
                        $CreatedBy = '19x'.$row[0];
                    }
                }
                if ($note['DateTime']) {
                    $dateTime = DateTime::createFromFormat('m/d/Y H:i:s', $note['DateTime']);
                }
                $data = [
                    'commentcontent'   => $note['Note'],
                    'assigned_user_id' => (($CreatedBy)?$CreatedBy:'19x1'),
                    'related_to'       => (($convertlead['Opportunities'])?$convertlead['Opportunities']:$wsLeadId),
                    'provider'         => $note['Provider'],
                    'createdtime'      => (($dateTime)?date_format($dateTime, 'Y-m-d H:i:s'):''),
                    'note_source'      => $note['NoteSource'],
                ];
                $note = vtws_create('ModComments', $data, $current_user);
            } catch (WebServiceException $ex) {
                $response = generateErrorArray('FAILED_CREATION_OF_NOTE', $ex->getMessage());

                if ($logThisFunction) {
                    file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
                }
                return json_encode($response);
            }
        }
    }
    if (empty($convertlead['Opportunities'])) {
        $response = ['success' => true, 'result' => ['LeadId' => $wsLeadId]];
    } else {
        $response = ['success' => true,
                     'result'  => ['LeadId'        => $wsLeadId,
                                   'OpportunityId' => $convertlead['Opportunities']]];
    }

    if ($logThisFunction) {
        file_put_contents('logs/syncwebservice_createLead.log', $logLineLeader.json_encode($response)."\n", FILE_APPEND);
    }
    return json_encode($response);
}

//create a lead source from the posted data.
function createLeadSource($postdata)
{
    $db     = PearDatabase::getInstance();
    //This is to match the "wildcard" agency_code.
    $specialAllAgency = 9999000;
    $errors = [];

    //verify inputs.
    //MUST HAVE THIS
    if (!validateMandatory($postdata['Brand']) && !validateMandatory($postdata['LMPAssignedAgentOrgId'])) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'Either LMPAssignedAgentOrgId OR Brand must be specified.'];
    }

    //MUST HAVE THIS
    if (!validateMandatory($postdata['AAProgramName'])) {
        $errors[] = ['code'    => 'INVALID_VALUE',
                     'message' => 'AAProgramName must be specified.'];
    }

    //return if there are errors.
    if (count($errors) > 0) {
        $response = ['success' => false, 'errors' => $errors];
        return json_encode($response);
    }

    //we are clear of validation, so let's initialize!
    //I realize this is unnecessary, but it makes it clearer to me.
    $agentid = '';
    $vanlinemanager_id = '';
    $agencyCode = $postdata['LMPAssignedAgentOrgId'];
    $brand      = $postdata['Brand'];
    $active     = $postdata['LeadSourceActive'];

    //if active is explicitly false it's off otherwise it's on.
    if ($active === false || $active === 'false' || $active == 'off' || strtolower($active) == 'n') {
        $active = 'off';
    } elseif (strtolower($active) == 'y') {
        $active = 'on';
    } else {
        $active = 'on';
    }

    if ($agencyCode != $specialAllAgency) {
        //require Agency unless it's special vanline agency
        $sql    = "SELECT agentmanagerid,vanline_id FROM `vtiger_agentmanager` WHERE agency_code=?";
        $result = $db->pquery($sql, [$agencyCode]);
        $row    = $result->fetchRow();
        if ($row == null) {
            $errCode    = "INVALID_AGENTID";
            $errMessage = "The provided agentid is not valid";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            return($response);
        }
        $agentid = $row['agentmanagerid'];
        $vanlinemanager_id = $row['vanline_id'];
        $brand = getCarrierCodeFromAgencyCode($agencyCode);
    } else {
        //require a vanlinemanager_id if there is not agency.
        //@TODO: replace if we add a brand to the database so we can select the id.
        if ($brand == 'AVL') {
            $vanline_id = 1;
        } elseif ($brand == 'NAVL') {
            $vanline_id = 9;
        } else {
            $errCode    = "INVALID_BRAND";
            $errMessage = "The provided brand is not valid";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            return($response);
        }
        $sql    = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_id=?";
        $result = $db->pquery($sql, [$vanline_id]);
        $row    = $result->fetchRow();
        if ($row == null) {
            $errCode    = "INVALID_VANLINE_ID";
            $errMessage = "The calculated vanline_id is not valid";
            $response   = json_encode(generateErrorArray($errCode, $errMessage));
            return($response);
        }
        $vanlinemanager_id = $row['vanlinemanagerid'];
    }

    //now that we have verified everything else, and are ready to go with these values,
    //make sure it doesn't already exist!
    //So we THINK we need to select for the specific agency OR the willcard agency AND brand.
    $params = [];
    $sqlWhere = '';

    if ($postdata['AAProgramName']) {
        $sqlWhere .= " source_name = ? ";
        $params[] = $postdata['AAProgramName'];
    }

    /*
     * taken out because alex overpromised.
    if ($postdata['LMPSourceId']) {
        $sqlWhere .= ($sqlWhere?' AND ':'')." lmp_source_id = ? ";
        $params[] = $postdata['LMPSourceId'];
    }

    if ($postdata['AASourceType']) {
        $sqlWhere .= ($sqlWhere?' AND ':'')." source_type = ? ";
        $params[] = $postdata['AASourceType'];
    }

    if ($postdata['MarketingChannel']) {
        $sqlWhere .= ($sqlWhere?' AND ':'')." marketing_channel = ? ";
        $params[] = $postdata['MarketingChannel'];
    }

    if ($postdata['AASourceName']) {
        $sqlWhere .= ($sqlWhere?' AND ':'')." source_name = ? ";
        $params[] = $postdata['AASourceName'];
    }
    */
    //set row to false so that unless it gets set we create
    $row = false;
    if ($sqlWhere) {
        //We need something seemingly unique to select on.
        $sql      = "SELECT leadsourcemanagerid FROM `vtiger_leadsourcemanager` WHERE "
                    .($sqlWhere?$sqlWhere.' AND ':'')." (agency_code = ? OR agency_code = ?)"
                    ." AND `brand`=?";
        $sql .= " LIMIT 1";
        $params[] = $agencyCode;
        $params[] = $specialAllAgency;
        $params[] = $brand;
        $result = $db->pquery($sql, $params);
        $row    = $result->fetchRow();
    }

    if ($row) {
        //already exists
        //We could fail since it's "createLeadSource", but seems softer to just return the existing id.
        $sourceId = $row[0];
        //encode it to proper format
        $wsLeadSrcId = vtws_getWebserviceEntityId('LeadSourceManager', $sourceId);
    } else {
        //so we are creating one do some extra validation
        if (!validateMandatory($postdata['AASourceName'])) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'AASourceName must be specified.'];
        }

        if (!validateMandatory($postdata['AASourceType'])) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'AASourceType must be specified.'];
        }

        if (!validateMandatory($postdata['MarketingChannel'])) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'MarketingChannel must be specified.'];
        }

        //return if there are errors.
        if (count($errors) > 0) {
            $response = ['success' => false, 'errors' => $errors];
            return json_encode($response);
        }

        //SOOO we are good! let's add it.
        $leadSrcData = [
            'agentid'           => $agentid,
            'source_name'       => $postdata['AASourceName'],
            'source_type'       => $postdata['AASourceType'],
            'marketing_channel' => $postdata['MarketingChannel'],
            'lmp_program_id'    => $postdata['LMPProgramId'],
            'lmp_source_id'     => $postdata['LMPSourceId'],
            'program_name'      => $postdata['AAProgramName'],
            'program_terms'     => $postdata['AAProgramTerms'],
            'brand'             => $brand,
            'agency_code'       => $agencyCode,
            'active'            => $active,
            'vanlinemanager_id' => $vanlinemanager_id,
            'agency_related'    => vtws_getWebserviceEntityId('AgentManager', $agentid),
            'vanline_related'   => vtws_getWebserviceEntityId('VanlineManager', $vanlinemanager_id),
        ];
        try {
            $user         = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $leadSrc      = vtws_create('LeadSourceManager', $leadSrcData, $current_user);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_CREATION_OF_LEADSOURCE', $ex->getMessage());

            return json_encode($response);
        }
        $wsLeadSrcId = $leadSrc['id'];
    }

    //$leadSrcId   = explode('x', $wsLeadSrcId)[1];
    $response = ['success' => true, 'result' => ['LeadSrcId' => $wsLeadSrcId]];
    return json_encode($response);
}

//so this sends back what we have if they give us the thing
function retrieveLeadSource($postdata)
{
    $db = PearDatabase::getInstance();
    if (empty($postdata['LeadSrcId'])) {
        $response = generateErrorArray('MISSING_REQUIRED_FIELD', 'LeadSrcId is missing or empty');

        return json_encode($response);
    } else {
        $leadSrcId = explode('x', $postdata['LeadSrcId'])[1];
        if (empty($leadSrcId) || explode('x', $postdata['LeadSrcId'])[0] != 73) {
            $response = generateErrorArray('INVALID_ID', 'LeadSrcId is expected to be in the format of 10x###');

            return json_encode($response);
        } else {
            $sql    = "SELECT * FROM `vtiger_leadsourcemanager` WHERE leadsourcemanagerid = ?";
            $result = $db->pquery($sql, [$leadSrcId]);
            $row    = $result->fetchRow();
            if (empty($row)) {
                $response = generateErrorArray('INVALID_ID', 'LeadSrcId is expected to be a valid lead in the system');

                return json_encode($response);
            }
        }
        //we know our lead so lets retrieve it and map fields over to the right names and such
        include_once 'include/Webservices/Retrieve.php';
        include_once 'modules/Users/Users.php';
        $user         = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        try {
            $wsid    = vtws_getWebserviceEntityId('LeadSourceManager', $leadSrcId); // Module_Webservice_ID x CRM_ID
            $leadSrc = vtws_retrieve($wsid, $current_user);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_LEAD_SOURCE', $ex->getMessage());

            return json_encode($response);
        }
        $data     = [
            "Brand"                 => $leadSrc['brand'],
            "LMPAssignedAgentOrgId" => $leadSrc['agency_code'],
            "AAProgramName"         => $leadSrc['program_name'],
            "AAProgramTerms"        => $leadSrc['program_terms'],
            "AASourceName"          => $leadSrc['source_name'],
            "AASourceType"          => $leadSrc['source_type'],
            "LMPProgramId"          => $leadSrc['lmp_program_id'],
            "LMPSourceId"           => $leadSrc['lmp_source_id'],
            "MarketingChannel"      => $leadSrc['marketing_channel'],
            "LeadSourceActive"      => $leadSrc['active']
        ];
        $response = ['success' => true, 'result' => ['LeadSrc' => $data]];
        return json_encode($response);
    }
}

//@TODO: Work in progress
function rateEstimate($postdata)
{
    if (!$postdata['id']) {
        return; //error;
    }
    $quoteid = explode('x', $postdata['id'])[1];
    if (!$quoteid) {
        return; //error
    }
    if (!$postdata['business_line_est']) {
        return; //error
    }
    if (!$postdata['local_tariff'] && !$postdata['effective_tariff']) {
        return; //error
    }

    $params = [
        'business_line_est' => $postdata['business_line_est'],
        'local_tariff'      => $postdata['local_tariff'],
        'effective_tariff'  => $postdata['effective_tariff'],
        'syncwebservice' => 1,
        'syncrate' => 1,
    ];

    require_once('modules/Estimates/Estimates.php');
    $EstimateObject = new Estimates;
    return $EstimateObject->rateEstimate($quoteid, $params);
}

function syncEstimate($postdata)
{
    //these are set by vtiger
    $_REQUEST['isSyncEstimate'] = 1;
    unset($postdata['modifiedby']);
    unset($postdata['modifiedtime']);
    //add this so we know we're allowed to rate and to handle effective_date without user pref.
    $user         = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    if ($postdata['id']) {
        try {
            //add some postdata variables to ensure they are things
            if (empty($postdata['record'])) {
                $postdata['record'] = explode('x', $postdata['id'])[1];
            }
            if (empty($postdata['currentid'])) {
                $postdata['currentid'] = explode('x', $postdata['id'])[1];
            }
            $est = vtws_revise($postdata, $current_user);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_UPDATE_OF_ESTIMATE', $ex->getMessage());
            return json_encode($response);
        }
    } else {
        try {
            $est = vtws_create('Estimates', $postdata, $current_user);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_CREATION_OF_ESTIMATE', $ex->getMessage());

            return json_encode($response);
        }
    }
    $response = ['success' => true, 'result' => ['Estimate' => $est]];

    return json_encode($response);
}

/**
 *     Creates an Estimate based on an array passed in from an external source such as devices.
 *
 * @param array $postdata An array of information for the Estimate
 *
 * @return void Void because this is only inserting values into the database
 */

function createEstimate($postdata)
{
    //file_put_contents('logs/createEstimate.log', date('Y-m-d H:i:s - ')." postdata :".print_r($postdata, true)."\n",
    //                 FILE_APPEND);
    $db     = PearDatabase::getInstance();
    $sql    =
        "SELECT groupid FROM `vtiger_agentmanager` WHERE agency_code = ?";
    $result = $db->pquery($sql, [$postdata['estimate_upload']['agent_code']]);
    $row    = $result->fetchRow();
    if (!empty($row[0])) { //don't do anything if we don't have a valid agency
        $group     = $row[0];
        $tariff_id = $postdata['estimate_upload']['dynamic_local_data']['tariff_id'];
        $sql       = "SELECT tariffsid FROM `vtiger_tariffs` WHERE tariffsid = ?";
        $result    = $db->pquery($sql, [$tariff_id]);
        $row       = $result->fetchRow();
        if (!empty($row[0])) { //don't do anything if we don't have a valid tariff
            if ($postdata['estimate_upload']['business_line'] == 'Local') { //this makes local estimates
                $postdata['estimate_upload']['business_line'] =
                    'Local Move'; //the JSON sends it as just local, it the DB its 'Local Move'
                try {
                    //get our User to make things
                    $user         = new Users();
                    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
                    //populate the data for the Estimate record itself
                    $data = [
                        'subject'              => 'Estimate',
                        'quotestage'           => 'Created',
                        //the device has no way of obtaining data for these at this point, uncomment when it can send them
                        //'contact_id' => '12x'.$postdata['estimate_upload']['contact_id'],
                        //'account_id' => '11x'.$postdata['estimate_upload']['account_id'],
                        //'orders_id' => '56x'.$postdata['estimate_upload']['order_id'],
                        'subtotal'             => 0,
                        'taxtype'              => 'individual',
                        'currency_id'          => '21x1',
                        'effective_date'       => $postdata['estimate_upload']['dynamic_local_data']['effective_date'],
                        'local_bl_discount'    => $postdata['estimate_upload']['dynamic_local_data']['bottom_line_discount'],
                        'is_primary'           => $postdata['estimate_upload']['is_primary'],
                        'business_line_est'    => $postdata['estimate_upload']['business_line'],
                        'bill_street'          => $postdata['estimate_upload']['billing_info']['address'],
                        'bill_city'            => $postdata['estimate_upload']['billing_info']['city'],
                        'bill_state'           => $postdata['estimate_upload']['billing_info']['state'],
                        'bill_code'            => $postdata['estimate_upload']['billing_info']['zip'],
                        'bill_pobox'           => $postdata['estimate_upload']['billing_info']['po_box'],
                        'bill_country'         => $postdata['estimate_upload']['billing_info']['country'],
                        'origin_address1'      => $postdata['estimate_upload']['origin_info']['address1'],
                        'origin_address2'      => $postdata['estimate_upload']['origin_info']['address2'],
                        'origin_city'          => $postdata['estimate_upload']['origin_info']['city'],
                        'origin_state'         => $postdata['estimate_upload']['origin_info']['state'],
                        'origin_zip'           => $postdata['estimate_upload']['origin_info']['zip'],
                        'origin_phone1'        => $postdata['estimate_upload']['origin_info']['phone1'],
                        'origin_phone2'        => $postdata['estimate_upload']['origin_info']['phone2'],
                        'destination_address1' => $postdata['estimate_upload']['dest_info']['address1'],
                        'destination_address2' => $postdata['estimate_upload']['dest_info']['address2'],
                        'destination_city'     => $postdata['estimate_upload']['dest_info']['city'],
                        'destination_state'    => $postdata['estimate_upload']['dest_info']['state'],
                        'destination_zip'      => $postdata['estimate_upload']['dest_info']['zip'],
                        'destination_phone1'   => $postdata['estimate_upload']['dest_info']['phone1'],
                        'destination_phone2'   => $postdata['estimate_upload']['dest_info']['phone2'],
                        'assigned_user_id'     => '20x'.$group,
                    ];

                    //OT4801 - When an estimate is created from the Mobile side, the "Pricing Type" value should ALWAYS be "Estimate".
                    $data['pricing_mode'] = 'Estimate';

                    //file_put_contents('logs/devLog.log', "\n New data : ".print_r($data, true), FILE_APPEND);
                    //create the estimate
                    $est = vtws_create('Estimates', $data, $current_user);
                    //file_put_contents('logs/devLog.log', "\n New Estimate : ".print_r($est, true), FILE_APPEND);
                } catch (WebServiceException $ex) {
                    //handle errors
                    logAndEmitResponse('Something went horribly wrong, Error : '.$ex->getMessage());
                }
                //take the WS id and turn it into the CRM Id
                $ids     = explode('x', $est['id']);
                $quoteid = $ids[1];
                //create the lineitems
                $sql       = "SELECT serviceid FROM `vtiger_service`";
                $result    = $db->pquery($sql, []);
                $lineitems = [];
                while ($row =& $result->fetchRow()) {
                    $lineitems[] = $row[0];
                }
                $sql         = "SELECT MAX(lineitem_id) FROM `vtiger_inventoryproductrel`";
                $result      = $db->pquery($sql, []);
                $row         = $result->fetchRow();
                $lineitem_id = $row[0];
                $seq         = 0;
                foreach ($lineitems as $item) {
                    $lineitem_id++;
                    $seq++;
                    $sql    =
                        "INSERT INTO `vtiger_inventoryproductrel` (id,productid,sequence_no,quantity,listprice,discount_percent,discount_amount,comment,description,incrementondel,lineitem_id,tax1,tax2,tax3) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $result = $db->pquery($sql,
                                          [$quoteid,
                                           $item,
                                           $seq,
                                           1,
                                           0,
                                           null,
                                           null,
                                           '',
                                           null,
                                           0,
                                           $lineitem_id,
                                           0,
                                           0,
                                           0]);
                }
                //save the effective_tariff since it doesn't have a standard vTiger save function
                Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_quotes` SET effective_tariff = ".
                                           $postdata['estimate_upload']['dynamic_local_data']['tariff_id'].
                                           " WHERE quoteid = ".$quoteid);
                foreach ($postdata['estimate_upload']['dynamic_local_data']['estimate']['sections']['section'] as
                         $section) {
                    //save the section discount
                    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_quotes_sectiondiscount` (estimateid, sectionid, discount_percent) VALUES (".
                                               $quoteid.", ".$section['section_id'].", ".$section['section_discount'].
                                               ")");
                    foreach ($section['services']['service'] as $service) {
                        //save each service in its correct table, recycled code from Estimates/Save.php, took out the check if it exists since these will always be new.
                        $id       = $service['service_id'];
                        $rateType = $service['rate_type'];
                        if ($rateType == 'Base Plus Trans.') {
                            $mileage = $service['miles'];
                            $weight  = $service['weight'];
                            $rate    = $service['rate'];
                            $excess  = $service['excess'];
                            $sql     =
                                "INSERT INTO `vtiger_quotes_baseplus` (estimateid, serviceid, mileage, weight, rate, excess) VALUES (?,?,?,?,?,?)";
                            $result  = $db->pquery($sql, [$quoteid, $id, $mileage, $weight, $rate, $excess]);
                        } elseif ($rateType == 'Break Point Trans.') {
                            $mileage    = $service['miles'];
                            $rate       = $service['rate'];
                            $weight     = $service['weight'];
                            $breakpoint = $service['calcweight'];
                            $sql        =
                                "INSERT INTO `vtiger_quotes_breakpoint` (estimateid, serviceid, mileage, weight, rate, breakpoint) VALUES (?,?,?,?,?,?)";
                            $result     = $db->pquery($sql, [$quoteid, $id, $mileage, $weight, $rate, $breakpoint]);
                        } elseif ($rateType == 'Weight/Mileage Trans.') {
                            $mileage = $service['miles'];
                            $rate    = $service['rate'];
                            $weight  = $service['weight'];
                            $sql     =
                                "INSERT INTO `vtiger_quotes_weightmileage` (estimateid, serviceid, mileage, weight, rate) VALUES (?,?,?,?,?)";
                            $result  = $db->pquery($sql, [$quoteid, $id, $mileage, $weight, $rate]);
                        } elseif ($rateType == 'Bulky List') {
                            $sql      = "SELECT MIN(line_item_id) FROM `vtiger_tariffbulky` WHERE serviceid = ?";
                            $result   = $db->pquery($sql, [$id]);
                            $row      = $result->fetchRow();
                            $bulky_id = $row[0];
                            foreach ($service['bulky_items']['bulky'] as $bulky) {
                                $description     = $bulky['description'];
                                $qty             = $bulky['qty'];
                                $weight          = $bulky['weight_add'];
                                $rate            = $bulky['rate'];
                                $cost_bulky_item = $bulky['cost'];
                                $sql             =
                                    "INSERT INTO `vtiger_quotes_bulky` (estimateid, serviceid, description, qty, weight, rate, bulky_id) VALUES (?,?,?,?,?,?,?)";
                                $result          = $db->pquery($sql,
                                                               [$quoteid,
                                                                $id,
                                                                $description,
                                                                $qty,
                                                                $weight,
                                                                $rate,
                                                                $bulky_id,
                                                                $cost_bulky_item]);
                                $bulky_id++;
                            }
                        } elseif ($rateType == 'Charge Per $100 (Valuation)') {
                            $qty1   = $service['deductible'];
                            $qty2   = $service['amount'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'County Charge') {
                            $county = $service['county'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_countycharge` (estimateid, serviceid, county, rate) VALUES (?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $county, $rate]);
                        } elseif ($rateType == 'Crating Item') {
                            $line_item_id = 0;
                            foreach ($service['crates']['crate'] as $crate) {
                                $crateid        = $crate['ID'];
                                $description    = $crate['description'];
                                $crating_qty    = $crate['crating_qty'];
                                $crating_rate   = $crate['crating_rate'];
                                $uncrating_qty  = $crate['uncrating_qty'];
                                $uncrating_rate = $crate['uncrating_rate'];
                                $length         = $crate['length'];
                                $width          = $crate['width'];
                                $height         = $crate['height'];
                                $inches_added   = $crate['inches_added'];
                                $line_item_id++;
                                $sql    =
                                    "INSERT INTO `vtiger_quotes_crating` (estimateid, serviceid, crateid, description, crating_qty, crating_rate, uncrating_qty, uncrating_rate, length, width, height, inches_added, line_item_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                                $result = $db->pquery($sql,
                                                      [$quoteid,
                                                       $id,
                                                       $crateid,
                                                       $description,
                                                       $crating_qty,
                                                       $crating_rate,
                                                       $uncrating_qty,
                                                       $uncrating_rate,
                                                       $length,
                                                       $width,
                                                       $height,
                                                       $inches_added,
                                                       $line_item_id]);
                            }
                        } elseif ($rateType == 'Flat Charge') {
                            $qty1   = null;
                            $qty2   = null;
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Hourly Set') {
                            $men        = $service['men'];
                            $vans       = $service['vans'];
                            $hours      = $service['hours'];
                            $traveltime = $service['travel_time'];
                            $rate       = $service['rate'];
                            $sql        =
                                "INSERT INTO `vtiger_quotes_hourlyset` (estimateid, serviceid, men, vans, hours, traveltime, rate) VALUES (?,?,?,?,?,?,?)";
                            $result     = $db->pquery($sql, [$quoteid, $id, $men, $vans, $hours, $traveltime, $rate]);
                        } elseif ($rateType == 'Hourly Simple') {
                            $qty1   = $service['quantity'];
                            $qty2   = $service['hours'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Packing Items') {
                            foreach ($service['packing_items']['packing_item'] as $pack) {
                                $name           = $pack['name'];
                                $container_qty  = $pack['container_qty'];
                                $container_rate = $pack['container_rate'];
                                $pack_qty       = (!empty($pack['pack_qty']))?$pack['pack_qty']:0;
                                $pack_rate      = $pack['pack_rate'];
                                $unpack_qty     = (!empty($pack['unpack_qty']))?$pack['unpack_qty']:0;
                                $unpack_rate    = $pack['unpack_rate'];
                                $packing_id     = $pack['line_item_id'];
                                $sql            =
                                    "INSERT INTO `vtiger_quotes_packing` (estimateid, serviceid, name, container_qty, container_rate, pack_qty, pack_rate, unpack_qty, unpack_rate, packing_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                                $result         = $db->pquery($sql,
                                                              [$quoteid,
                                                               $id,
                                                               $name,
                                                               $container_qty,
                                                               $container_rate,
                                                               $pack_qty,
                                                               $pack_rate,
                                                               $unpack_qty,
                                                               $unpack_rate,
                                                               $packing_id]);
                            }
                        } elseif ($rateType == 'Per Cu Ft') {
                            $qty1   = $service['cubic_feet'];
                            $qty2   = null;
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per Cu Ft/Per Day') {
                            $qty1   = $service['cubic_feet'];
                            $qty2   = $service['days'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per Cu Ft/Per Month') {
                            $qty1   = $service['cubic_feet'];
                            $qty2   = $service['months'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per CWT' || $rateType == 'SIT First Day Rate') {
                            $qty1   = $service['weight'];
                            $qty2   = null;
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per CWT/Per Day' || $rateType == 'SIT Additional Day Rate') {
                            $qty1   = $service['weight'];
                            $qty2   = $service['days'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per CWT/Per Month') {
                            $qty1   = $service['weight'];
                            $qty2   = $service['months'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per Quantity') {
                            $qty1   = $service['quantity'];
                            $qty2   = null;
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per Quantity/Per Day') {
                            $qty1   = $service['quantity'];
                            $qty2   = $service['days'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Per Quantity/Per Month') {
                            $qty1   = $service['quantity'];
                            $qty2   = $service['months'];
                            $rate   = $service['rate'];
                            $sql    =
                                "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } elseif ($rateType == 'Tabled Valuation') {
                            $valuation = $service['valuationtype'];
                            if ($valuation == 'Select an Option') {
                                $released = 2;
                            } else {
                                $released = ($service['valuationtype'] == 'Released Valuation');
                            }
                            $released_amount = ($released == 1)?$service['coverage']:null;
                            $amount          = ($released == 0)?$service['amount']:null;
                            $deductible      = ($released == 0)?$service['deductible']:null;
                            $rate            = ($released == 0)?$service['rate']:null;
                            $sql             =
                                "INSERT INTO `vtiger_quotes_valuation` (estimateid, serviceid, released, released_amount, amount, deductible, rate) VALUES (?,?,?,?,?,?,?)";
                            $result          = $db->pquery($sql,
                                                           [$quoteid,
                                                            $id,
                                                            $released,
                                                            $released_amount,
                                                            $amount,
                                                            $deductible,
                                                            $rate]);
                        }
                    }
                }
                logAndEmitResponse('Success');
            }
            logAndEmitResponse('Was not a local estimate');
        }
        logAndEmitResponse('Tariff was invalid');
    }
    logAndEmitResponse('Agency was invalid');
}

function getrelated($postdata)
{
    //file_put_contents('logs/authTest.log', date('Y-m-d H:i:s - ')."Entering getrelated function\n", FILE_APPEND);
    if (!isset($postdata['walkid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'walkid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $db          = PearDatabase::getInstance();
    $sql         =
        "SELECT `vtiger_walksrel`.walkdetailsid, order_id FROM `vtiger_walksrel` JOIN `vtiger_walkdetails` ON `vtiger_walksrel`.walkdetailsid=`vtiger_walkdetails`.walkdetailsid WHERE walksid=?";
    $result      = $db->pquery($sql, [$postdata['walkid']]);
    $recordsList = [];
    while ($row =& $result->fetchRow()) {
        $recordsList[$row[1]] = $row[0];
    }

    return json_encode(['success' => 'true', 'result' => ['relatedrecords' => $recordsList]]);
}

function retrieveLeadActivities($postdata)
{
    if (!isset($postdata['LeadId'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'LeadId' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $leadIdPrefix = strstr($postdata['LeadId'], 'x', true);
    if ($leadIdPrefix != '10') {
        $errCode    = "INCORRECT_IDENTIFIER_PREFIX";
        $errMessage = "Identifier prefix provided for LeadId does not correspond to an object of the type Leads";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $leadId = substr(strstr($postdata['LeadId'], 'x'), 1);
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT * FROM `vtiger_crmentity` WHERE crmid=? AND setype='Leads'";
    $result = $db->pquery($sql, [$leadId]);
    $row    = $result->fetchRow();
    if ($row == null) {
        $errCode    = "INCORRECT_IDENTIFIER";
        $errMessage = "Identifier provided for LeadId does not correspond to an object of the type Leads";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $retrieveSql =
        "SELECT vtiger_activity.activityid, activitytype FROM `vtiger_seactivityrel` JOIN `vtiger_activity` ON vtiger_seactivityrel.activityid=vtiger_activity.activityid WHERE crmid=?";
    $result      = $db->pquery($retrieveSql, [$leadId]);
    $activities  = [];
    try {
        $user         = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        while ($row =& $result->fetchRow()) {
            $wsId = vtws_getWebserviceEntityId(($row[1] === 'Task'?'Calendar':'Events'), $row[0]);
            //file_put_contents('logs/LeadActivities.log', date('Y-m-d H:i:s - ').$wsId."\n", FILE_APPEND);
            $activity = vtws_retrieve($wsId, $current_user);
            //file_put_contents('logs/LeadActivities.log', date('Y-m-d H:i:s - ').print_r($activity, true)."\n", FILE_APPEND);
            $activities[] = $activity;
        }
    } catch (WebServiceException $ex) {
        //file_put_contents('logs/LeadActivities.log', date('Y-m-d H:i:s - ').print_r($ex, true)."\n", FILE_APPEND);
    }
    $oppSql = "SELECT potentialid FROM `vtiger_potential` WHERE converted_from=?";
    $result = $db->pquery($oppSql, [$leadId]);
    $row    = $result->fetchRow();
    if ($row != null) {
        $res = $db->pquery($retrieveSql, [$row[0]]);
        try {
            $user         = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            while ($row =& $res->fetchRow()) {
                $wsId = vtws_getWebserviceEntityId(($row[1] === 'Task'?'Calendar':'Events'), $row[0]);
                //file_put_contents('logs/LeadActivities.log', date('Y-m-d H:i:s - ').$wsId."\n", FILE_APPEND);
                $activity = vtws_retrieve($wsId, $current_user);
                //file_put_contents('logs/LeadActivities.log', date('Y-m-d H:i:s - ').print_r($activity, true)."\n", FILE_APPEND);
                $activities[] = $activity;
            }
        } catch (WebServiceException $ex) {
            //file_put_contents('logs/LeadActivities.log', date('Y-m-d H:i:s - ').print_r($ex, true)."\n", FILE_APPEND);
        }
    }

    return json_encode(['success' => 'true', 'result' => ['ListOfActivities' => $activities]]);
}

function updatePushToken($postdata)
{
    if (!isset($postdata['userid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'userid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $userIdPrefix = strstr($postdata['userid'], 'x', true);
    if ($userIdPrefix != '19') {
        $errCode    = "INCORRECT_IDENTIFIER_PREFIX";
        $errMessage = "Identifier prefix provided for userid does not correspond to an object of the type Users";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $userId = substr(strstr($postdata['userid'], 'x'), 1);
    $db     = PearDatabase::getInstance();
    $sql    = "UPDATE `vtiger_users` SET push_notification_token=? WHERE id=?";
    $db->pquery($sql, [$postdata['token'], $userId]);
    $sql    = "SELECT push_notification_token FROM `vtiger_users` WHERE id=?";
    $result = $db->pquery($sql, [$userId]);
    $row    = $result->fetchRow();

    return json_encode(['success' => 'true', 'result' => ['push_notification_token' => $row['push_notification_token']]]);
}

function updateOiPushToken($postdata)
{
    if (!isset($postdata['userid'])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'userid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $userIdPrefix = strstr($postdata['userid'], 'x', true);
    if ($userIdPrefix != '19') {
        $errCode    = "INCORRECT_IDENTIFIER_PREFIX";
        $errMessage = "Identifier prefix provided for userid does not correspond to an object of the type Users";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }
    $userId = substr(strstr($postdata['userid'], 'x'), 1);
    $db     = PearDatabase::getInstance();
    $sql    = "UPDATE `vtiger_users` SET oi_push_notification_token=? WHERE id=?";
    $db->pquery($sql, [$postdata['token'], $userId]);
    $sql    = "SELECT oi_push_notification_token FROM `vtiger_users` WHERE id=?";
    $result = $db->pquery($sql, [$userId]);
    $row    = $result->fetchRow();

    return json_encode(['success' => 'true', 'result' => ['oi_push_notification_token' => $row['oi_push_notification_token']]]);
}

function getObjectTypeId($db, $modName)
{
    $sql      = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
    $params[] = $modName;
    $result   = $db->pquery($sql, $params);

    return $db->query_result($result, 0, 'id').'x';
}

function getRateDetails($db, $id, $type)
{
    if ($type == 'SIT Item') {
        return;
    }
    $rateDetails = [];
    $params      = [$id];
    if ($type == 'Base Plus Trans.') {
        $sql =
            "SELECT from_miles, to_miles, from_weight, to_weight, base_rate, excess FROM `vtiger_tariffbaseplus` WHERE serviceid=?";
    } elseif ($type == 'Break Point Trans.') {
        $sql =
            "SELECT from_miles, to_miles, from_weight, to_weight, break_point, base_rate FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
    } elseif ($type == 'Weight/Mileage Trans.') {
        $sql =
            "SELECT from_miles, to_miles, from_weight, to_weight, base_rate FROM `vtiger_tariffweightmileage` WHERE serviceid=?";
    } elseif ($type == 'Bulky List') {
        $sql         = "SELECT bulky_chargeper FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();
        $rateDetails = ['charge_per' => $row[0]];
        $sql         =
            "SELECT description, weight, rate, CartonBulkyId, standardItem, line_item_id AS id FROM `vtiger_tariffbulky` WHERE serviceid=?";
    } elseif ($type == 'Charge Per $100 (Valuation)') {
        $sql         = "SELECT valuation_released, valuation_releasedamount FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();
        $rateDetails = ['has_released' => $row[0], 'released_amount' => $row[1]];
        $sql = "SELECT deductible, rate, multiplier FROM `vtiger_tariffchargeperhundred` WHERE serviceid=?";
    } elseif ($type == 'County Charge') {
        $sql = "SELECT name, rate FROM `vtiger_tariffcountycharge` WHERE serviceid=?";
    } elseif ($type == 'Crating Item') {
        $sql =
            "SELECT crate_inches, crate_mincube, crate_packrate, crate_unpackrate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Flat Charge') {
        $sql = "SELECT flat_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Hourly Avg Lb/Man/Hour') {
        $sql = "SELECT hourlyavg_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Hourly Set') {
        $sql         =
            "SELECT hourlyset_hasvan, hourlyset_hastravel, hourlyset_addmanrate, hourlyset_addvanrate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();
        $rateDetails =
            ['has_van' => $row[0], 'has_travel' => $row[1], 'add_man_rate' => $row[2], 'add_van_rate' => $row[3]];
        $sql         = "SELECT men, vans, rate FROM `vtiger_tariffhourlyset` WHERE serviceid=?";
    } elseif ($type == 'Hourly Simple') {
        $sql = "SELECT hourlysimple_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Packing Items') {
        $sql         =
            "SELECT packing_containers, packing_haspacking, packing_hasunpacking, packing_salestax FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();
        $rateDetails = ['has_container_rate' => $row[0],
                        'has_packing_rate'   => $row[1],
                        'has_unpacking_rate' => $row[2],
                        'sales_tax'          => $row[3]];
        $sql         = "SELECT *, line_item_id AS id FROM `vtiger_tariffpackingitems` WHERE serviceid=?";
    } elseif ($type == 'Per Cu Ft') {
        $sql = "SELECT cuft_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per Cu Ft/Per Day') {
        $sql = "SELECT cuftperday_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per Cu Ft/Per Month') {
        $sql = "SELECT cuftpermonth_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per CWT' || $type == 'SIT First Day Rate') {
        $sql = "SELECT cwt_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per CWT/Per Day' || $type == 'SIT Additional Day Rate') {
        $sql = "SELECT cwtperday_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per CWT/Per Month') {
        $sql = "SELECT cwtpermonth_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per Quantity') {
        $sql = "SELECT qty_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per Quantity/Per Day') {
        $sql = "SELECT qtyperday_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Per Quantity/Per Month') {
        $sql = "SELECT qtypermonth_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Service Base Charge' || $type == 'Storage Valuation') {
        $sql = "SELECT service_base_charge AS rate, service_base_charge_applies, service_base_charge_matrix FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();
        $rateDetails = ['service_base_charge_matrix' => $row['service_base_charge_matrix']];

        if (isset($row['service_base_charge_applies'])) {
            $rateDetails['service_base_charge_applies'] = str_ireplace(' |##| ', ', ', $row['service_base_charge_applies']);
        }

        if (empty($row['service_base_charge_matrix'])) {
            $rateDetails['rate'] = $row['rate'];
        }
        $sql = "SELECT price_from AS `from`, price_to AS `to`, factor AS `percent`, line_item_id FROM `vtiger_tariffservicebasecharge` WHERE serviceid=?";
    } elseif ($type == 'Tabled Valuation') {
        $sql         = "SELECT valuation_released, valuation_releasedamount FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
        $result      = $db->pquery($sql, $params);
        $row         = $result->fetchRow();
        $rateDetails = ['has_released' => $row[0], 'released_amount' => $row[1]];
        $sql         = "SELECT amount, deductible, cost, line_item_id FROM `vtiger_tariffvaluations` WHERE serviceid=?";
    } elseif ($type == 'CWT by Weight' || $type == 'SIT Cartage') {
        $sql = "SELECT from_weight, to_weight, rate FROM `vtiger_tariffcwtbyweight` WHERE serviceid=?";
    } elseif ($type == 'CWT Per Quantity') {
        $sql = "SELECT cwtperqty_rate AS rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
    } elseif ($type == 'Flat Rate By Weight') {
      $sql = "SELECT from_weight, to_weight, cwt_rate, rate FROM `vtiger_tariffflatratebyweight` WHERE serviceid=?";
    }
    if($sql) {
      $result = $db->pquery($sql, $params);
      while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
          $rateDetails[] = $row;
      }
    }

    return $rateDetails;
}

function getEvents($postdata)
{
    $db = PearDatabase::getInstance();
    //takes in the following
    //EventStartDtTm
    //EventEndDtTm
    //EventCode
    //AgentCode
    //validate the inputs
    $errors = [];
    $params = [];
    //EventStartDtTm
    if (!empty($postdata['EventStartDtTm'])) {
        //we have an EventStartDtTm make sure it's in the format we are expecting
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $postdata['EventStartDtTm']) == 0) {
            $errors[] = ['code'    => 'INVALID_FORMAT',
                         'message' => 'EventStartDtTm is expected to be in the format of 2016-01-01 00:00:00'];
        } else {
            $params[] = $postdata['EventStartDtTm'];
        }
    }
    //EventEndDtTm
    if (!empty($postdata['EventEndDtTm'])) {
        //we have an EventStartDtTm make sure it's in the format we are expecting
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $postdata['EventEndDtTm']) == 0) {
            $errors[] = ['code'    => 'INVALID_FORMAT',
                         'message' => 'EventEndDtTm is expected to be in the format of 2016-01-01 00:00:00'];
        } else {
            $params[] = $postdata['EventEndDtTm'];
        }
    }
    //EventCode
    if (!empty($postdata['EventCode'])) {
        //We have an EventCode make sure it's in the format we are expecting
        $eventCode = [ 'ACA',
                      'ACC',
                      'AOF',
                      'AOS',
                      'COA',
                      'COD',
                      'CTA',
                      'CTC',
                      'LDA',
                      'LDC',
                      'NTA',
                      'NTC',
                      'NTD',
                      'OAA',
                      'OAD',
                      'OPA',
                      'OPC',
                      'QAA',
                      'QAD',
                      'QAC',
                      'QTA',
                      'QTB',
                      'QTC',
                      'QTD',
                      'OAC'
        ];
        if (!in_array($postdata['EventCode'], $eventCode)) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'EventCode is expected to be '.implode(', ', $eventCode)];
        }
    }
    //AgentCode
    if (empty($postdata['AgentCode'])) {
        $errors[] = ['code'    => 'MISSING_REQUIRED_FIELD',
                     'message' => 'AgentCode is missing or empty'];
    } else {
        $sql    = "SELECT * FROM `vtiger_agentmanager` WHERE agency_code = ?";
        $result = $db->pquery($sql, [$postdata['AgentCode']]);
        $row    = $result->fetchRow();
        if (empty($row)) {
            $errors[] = ['code'    => 'INVALID_VALUE',
                         'message' => 'AgentCode is expected to be a valid Agency Code in the system'];
        } else {
            $params[] = $postdata['AgentCode'];
        }
    }
    //if we have errors spit the back out
    if (count($errors) > 0) {
        $response = ['success' => false,
                     'errors'  => $errors];

        return json_encode($response);
    }
    //start by grabbing everything from mod tracker with this
    $sql = "SELECT `vtiger_modtracker_basic`.id, `vtiger_modtracker_basic`.crmid, `vtiger_modtracker_basic`.module,
                   `vtiger_modtracker_basic`.changedon, `vtiger_crmentity`.smownerid,`vtiger_agentmanager`.agency_code,
                    IF(`vtiger_vanlinemanager`.vanline_id = 9, 'NAVL', IF(`vtiger_vanlinemanager`.vanline_id = 1, 'AVL', '')) AS carrier_code,
                   `vtiger_crmentity`.createdtime, `vtiger_crmentity`.modifiedtime, `vtiger_modtracker_basic`.`status`
            FROM `vtiger_modtracker_basic`
            JOIN `vtiger_crmentity` ON `vtiger_modtracker_basic`.crmid = `vtiger_crmentity`.crmid
            LEFT JOIN `vtiger_agentmanager` ON `vtiger_agentmanager`.groupid = `vtiger_crmentity`.smownerid
            LEFT JOIN `vtiger_vanlinemanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
            WHERE (
            `vtiger_modtracker_basic`.module = 'Leads'
            OR `vtiger_modtracker_basic`.module = 'Opportunities'
            OR `vtiger_modtracker_basic`.module = 'Estimates'
            OR `vtiger_modtracker_basic`.module = 'Surveys'
            OR `vtiger_modtracker_basic`.module = 'Contacts'
            OR `vtiger_modtracker_basic`.module = 'ModComments'
            OR `vtiger_modtracker_basic`.module = 'Documents') ";

    //do this for start time
    if ($postdata['EventStartDtTm']) {
        $sql .= "AND `vtiger_modtracker_basic`.changedon > ? ";
    }
    //do this for end time
    if ($postdata['EventEndDtTm']) {
        $sql .= "
                 AND `vtiger_modtracker_basic`.changedon < ? ";
    }
    //this is so we get things in the order we want
    $sql .= "
            AND ( `vtiger_agentmanager`.agency_code = ?";
    foreach (getUsersForAgency($postdata['AgentCode']) as $userId) {
        $sql .= "
            OR `vtiger_crmentity`.smownerid = ?";
        $params[] = $userId;
    }
    $sql .= " )
            ORDER BY `vtiger_modtracker_basic`.changedon ASC";
    //Only AgentCode is required the rest are just there to filter stuff down
    //returns should be limited to 1000 events and be listed from oldest to newest
    //Event Codes
    /******************************************************************************************************************
     *** Code     * Short for                     * Description                                                       *
     ******************************************************************************************************************
     * --* OAA      * OA Assigned                   * Resource (origin agent) assigned to shipment                      *
     * ACA      * Activity Added                * Activity (Appointment) Added                                      *
     *=== ACC      * Activity Changed              * Activity (Appointment) Changed                                    *
     *** AOF      * Add Opportunity Failed        * Add Opportunity via API Failed                                    *
     *=== AOS      * Add Opportunity Success           * Add Opportunity via API succeeded                                 *
     * --* COA      * Collaboration Added           * Qualified Lead Collaborated with another agency added             *
     * --* COD      * Collaboration Deleted         * Qualified lead Collaborated with another agency deleted           *
     * //* CTA      * Contact Added                 * Contact Information Added for Lead / Opportunity                  *
     * //* CTC      * Contact Changed               * Contact Information Changed for Lead / Opportunity                *
     * //* LDA      * Lead Created                  * New un-qualified lead Created                                     *
     * //* LDC      * Lead Changed                  * Un-qualified lead has changed                                     *
     * //* NTA      * Note Added                    * Note Added                                                        *
     * //* NTC      * Note Changed                  * Note Changed                                                      *
     *** NTD      * Note Deleted                  * Note Deleted                                                      *
     * //* OAA      * Opportunity Attachment Added  * Attachment (Documents and Pictures) Added                         *
     * //* OAD      * Opportunity Attachment Deleted* Attachment (Documents and Pictures) Deleted                       *
     * //* OPA      * Opportunity Created           * New qualified lead created                                        *
     * //* OPC      * Opportunity Changed           * Qualified Lead Changed                                            *
     * //* QAA      * Quote Attachment Added        * Attachment (Documents and Pictures) Added                         *
     * //* QTA      * Quote Created                 * New Quote (Survey) Created     Survey/Cubesheet created           *
     * //* QTB      * Quote Booked                  * Quote (Survey) Registered      Opportunity set to booked          *
     * //* QTC      * Quote Changed                 * Quote (Survey) Changed         Estimate had a change              *
     * //* QTD      * Quote Deleted                 * Quote (Survey) Deleted         Survey/Cubesheet deleted           *
     ****************************************************************************************************************/
    //file_put_contents('logs/devLog.log', "\n Sql : ".print_r($sql, true), FILE_APPEND);
    //file_put_contents('logs/devLog.log', "\n Params : ".print_r($params, true), FILE_APPEND);
    $result  = $db->pquery($sql, $params);
    $results = [];

    while ($row =& $result->fetchRow() && count($results) <= 1000) {
        $code        = false;
        $agencyCode  = false;
        $carrierCode = false;

        if ($row['module'] == 'Leads') {
            if ($row['changedon'] == $row['createdtime']) {
                $code = 'LDA';
            } elseif ($row['changedon'] != $row['createdtime']) {
                $code = 'LDC';
            }
            $leadId = $row['crmid'];
            $oppId  = getOppIdFromLeadId($row['crmid']);
        }

        if ($row['module'] == 'ModComments') {
            if ($row['changedon'] == $row['createdtime']) {
                $code = 'NTA';
            } elseif ($row['changedon'] != $row['createdtime']) {
                $code = 'NTC';
            }
            $leadId     = getIdsFromModComment($row['crmid'])['LeadId'];
            $oppId      = getIdsFromModComment($row['crmid'])['OpportunityId'];
            $agencyCode = $oppId?getAgentCodeForRecord($oppId):getAgentCodeForRecord($leadId);
            if ($agencyCode != $postdata['AgentCode']) {
                continue;
            }
            $carrierCode = getCarrierCodeFromAgencyCode($agencyCode);
        }

        else if ($row['module'] == 'Opportunities') {
            $lead_id = getLeadIdFromOppId($row['id']);
            $lmp_id = getLMPLeadId($lead_id);

            if ($row['changedon'] == $row['createdtime']) {
                if ($lmp_id && $postdata['EventCode'] == 'AOS') {
                    $code = 'AOS';
                } else {
                    $code = 'OPA';
                }
            } elseif ($row['changedon'] != $row['createdtime']) {
                $changedFields = getChangeDetails($row['id']);
                if ($changedFields) {
                    foreach ($changedFields as $changedField) {
                        if ($changedField['fieldname'] == 'contact_id') {
                            if ($changedField['prevalue']) {
                                $code = 'CTC';
                            } else {
                                $code = 'CTA';
                            }
                            break;
                        } elseif ($changedField['fieldname'] == 'sales_stage' && $changedField['postvalue'] == 'Closed Won') {
                            $code = 'QTB';
                        }
                    }
                }
                $code = $code?$code:'OPC';
            }
            $oppId  = $row['crmid'];
            $leadId = getLeadIdFromOppId($oppId);
        }

        else if ($row['module'] == 'Documents') {
            $details = getChangeDetails($row['id']);
            $related = getRelatedFromDocumentId($row['crmid']);
            if (array_key_exists('Estimates', $related)) {
                if (empty($details)) {
                    $code = 'QAD';
                } elseif ($row['changedon'] == $row['createdtime']) {
                    $code = 'QAA';
                } elseif ($row['changedon'] != $row['createdtime']) {
                    $code = 'QAC';
                }
                $oppId  = getOppIdFromEstimateId($related['Estimates']);
                $leadId = getLeadIdFromOppId($oppId);
            } elseif (array_key_exists('Opportunities', $related)) {
                //added to an opportunity
                if (empty($details)) {
                    $code = 'OAD';
                } elseif ($row['changedon'] == $row['createdtime']) {
                    $code = 'OAA';
                } elseif ($row['changedon'] != $row['createdtime']) {
                    $code = 'OAC';
                }
                $oppId  = $related['Opportunities'];
                $leadId = getLeadIdFromOppId($oppId);
            }
            $agencyCode  = getAgencyCodeForUser($row['smownerid']);
            $carrierCode = getCarrierCodeFromAgencyCode($agencyCode);
        }

        else if ($row['module'] == 'Surveys' || $row['module'] == 'Estimates') {
            $details = getChangeDetails($row['id']);
            if ($row['changedon'] == $row['createdtime']) {
                if ($postdata['EventCode'] == 'ACA') {
                    $code = 'ACA';
                } else {
                    $code = 'QTA';
                }
            } elseif ($row['changedon'] != $row['createdtime']) {
                if (empty($details)) {
                    $code = 'QTD';
                } else {
                    foreach ($details as $field) {
                        if (($field['fieldname'] == 'survey_date' && $changedField['postvalue'] != '') ||
                           ($field['fieldname'] == 'survey_time' && $changedField['postvalue'] != '')  ||
                           ($field['fieldname'] == 'survey_end_time'  && $changedField['postvalue'] != '')
                           && $row['module'] == 'Surveys') {
                            $code = 'ACC';
                        }
                    }
                    if (empty($code)) {
                        $code = 'QTC';
                    }
                }
            }
        }

        if($row['status'] == 4){
            $code = 'COA';
        } elseif($row['status'] == 5){
            $code = 'COD';
        }

        if ($code) {
            if ($postdata['EventCode']) {
                if ($code != $postdata['EventCode']) {
                    continue;
                }
            }

            // If we already haven't determined a carrier code then make one last effort to get it.
            // Also, I'd like to point out just how filthy this entire function is...
            // I mean.. it grabs a bunch of records and then attemps to match them.. I mean.. C'mon..
            $carrierCode = $carrierCode?:$row['carrier_code'];
            if($carrierCode == ''){
                $carrierCode = getCarrierCodeFromAgencyCode(getAgentCodeForRecord($oppId));
            }

            $results[] = ["EventDtTm"                    => $row['changedon'],
                          "EventCode"                    => $code,
                          "CarrierCode"                  => $carrierCode,
                          "LeadId"                       => $leadId?'10x'.$leadId:false,
                          "OpportunityId"                => $oppId?'46x'.$oppId:false,
                          "QuoteId"                      => getQuoteFromOpp($oppId, true),
                          "ExternalRefId"                => $row['id'],
                          "BookerServiceProvider"        => getAgentCodeByCrmid($oppId, 'Booking Agent'),
                          "CollaborationServiceProvider" => getAgentCodeByCrmid($oppId, 'Origin Agent'),
                          "CollaborationCarrierCode"     => getCarrierCodeFromAgencyCode(getAgentCodeByCrmid($oppId, 'Origin Agent')),
            ];
        }
    }
    $response = ['success' => true,
                 'result'  => $results];
    //file_put_contents('logs/devLog.log',
    //                  "\n Response : ".print_r(json_encode($response, JSON_PRETTY_PRINT), true),
    //                  FILE_APPEND);
    return json_encode($response);
    //Will return in the format of :
    /*
     * {
     *      "success":true,
     *      "result": [
     *          {
     *              "EventDtTm":"DateTimeValue",
     *              "EventCode":"EventCode",
     *              "CarrierCode":"AVL|NAVL",
     *              "LeadId":"10x##",
     *              "OpportunityId":"46x##",
     *              "QuoteId":"46x##", //for now we will be putting OppId here this will change in the future
     *              "ExternalRefId":"TBD",
     *              "BookerServiceProvider":"2222000",
     *              "CollaborationServiceProvider":"2222222",
     *              "CollaborationCarrierCode":"AVL|NAVL"
     *          },
     *          //repeat for each 'Event'
     *      ]
     * }
     */
}

function getQuoteFromOpp($opportunityId, $webServiceId = false){
    if(!is_numeric($opportunityId)){
        return false;
    }
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT `quoteid` FROM `vtiger_quotes` where `potentialid` = ? AND `is_primary` = '1' LIMIT 1";
    $result = $db->pquery($sql, [$opportunityId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $webServiceId ? vtws_getWebserviceEntityId('Estimates', $row['quoteid']) : $row['quoteid'];
    } else{
        return false;
    }
}

function getAgentCodeByCrmid($opportunityId, $agentType = 'Booking Agent'){
    if(!is_numeric($opportunityId)){
        return false;
    }
    $db     = PearDatabase::getInstance();
    $sql    = 'SELECT agent_number FROM `vtiger_participatingagents` LEFT JOIN `vtiger_agents` ON `agents_id` = `agentsid` WHERE rel_crmid = ? AND agent_type = ?';
    $result = $db->pquery($sql, [$opportunityId, $agentType]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['agent_number'] != '' ? $row['agent_number'] : false;
    }
}

function getOppIdFromLeadId($leadId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT potentialid FROM `vtiger_potential` WHERE converted_from = ?";
    $result = $db->pquery($sql, [$leadId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['potentialid'];
    }

    return false;
}

function getLeadIdFromOppId($oppId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT converted_from FROM `vtiger_potential` WHERE potentialid = ?";
    $result = $db->pquery($sql, [$oppId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['converted_from'];
    }

    return false;
}

function getOppIdFromEstimateId($estId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT potentialid FROM `vtiger_quotes` WHERE quoteid = ?";
    $result = $db->pquery($sql, [$estId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['potentialid'];
    }

    return false;
}

function getOppIdFromSurveyId($surveyId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT potentialid FROM `vtiger_surveys` WHERE surveysid = ?";
    $result = $db->pquery($sql, [$surveyId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['potentialid'];
    }

    return false;
}

function getIdsFromModComment($modcommentId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT `vtiger_modcomments`.related_to, `vtiger_crmentity`.setype
               FROM `vtiger_modcomments` JOIN `vtiger_crmentity`
               ON `vtiger_modcomments`.related_to = `vtiger_crmentity`.crmid WHERE modcommentsid = ?";
    $result = $db->pquery($sql, [$modcommentId]);
    $row    = $result->fetchRow();
    if ($row) {
        switch ($row['setype']) {
            case 'Leads':
                $leadId = $row['related_to'];
                $oppId  = (getOppIdFromLeadId($leadId))?getOppIdFromLeadId($leadId):false;
                break;
            case 'Opportunities':
                $oppId  = $row['related_to'];
                $leadId = (getLeadIdFromOppId($oppId))?getLeadIdFromOppId($oppId):false;
                break;
            case 'Estimates':
                $oppId  = (getOppIdFromEstimateId($row['related_to']))?getOppIdFromEstimateId($row['related_to']):false;
                $leadId = (getLeadIdFromOppId($oppId))?getLeadIdFromOppId($oppId):false;
                break;
            case 'Surveys':
                $oppId  = (getOppIdFromSurveyId($row['related_to']))?getOppIdFromSurveyId($row['related_to']):false;
                $leadId = (getLeadIdFromOppId($oppId))?getLeadIdFromOppId($oppId):false;
                break;
            default:
                return false;
        }

        return ["LeadId" => $leadId, "OpportunityId" => $oppId];
    }

    return false;
}

function getChangeDetails($crmid)
{
    $db      = PearDatabase::getInstance();
    $sql     = "SELECT * FROM `vtiger_modtracker_detail` WHERE id = ?";
    $result  = $db->pquery($sql, [$crmid]);
    $details = [];
    while ($row =& $result->fetchRow()) {
        $details[] = $row;
    }
    if (count($details) > 0) {
        return $details;
    }

    return false;
}

function getRecordOwner($crmId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?";
    $result = $db->pquery($sql, [$crmId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['smownerid'];
    }

    return false;
}

function checkIfIdIsGroup($smowner)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT groupid FROM `vtiger_groups` WHERE groupid = ?";
    $result = $db->pquery($sql, [$smowner]);
    $row    = $result->fetchRow();

    return $row?true:false;
}

function getAgentCodeForRecord($crmId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT agency_code FROM `vtiger_crmentity` JOIN `vtiger_agentmanager` ON `agentid` = `agentmanagerid` WHERE crmid = ?";
    $result = $db->pquery($sql, [$crmId]);
    $row    = $result->fetchRow();
    return $row['agency_code'];
    //$owner = getRecordOwner($crmId);
    //if (checkIfIdIsGroup($owner)) {
    //    return getAgencyCodeForGroup($owner);
    //} else {
    //    return getAgencyCodeForUser($owner);
    //}
}

function getUsersForAgency($agentCode)
{
    //file_put_contents('logs/devLog.log', "\n AgentCode : ".print_r($agentCode, true), FILE_APPEND);
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_code = ?";
    $result = $db->pquery($sql, [$agentCode]);
    $row = $result->fetchRow();
    $agentManagerId = $row['agentmanagerid'];
    //file_put_contents('logs/devLog.log', "\n AgentMangerId : ".print_r($agentManagerId, true), FILE_APPEND);
    $sql = "SELECT * FROM `vtiger_users` WHERE deleted=0 and status='Active' and (agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?)";
    $result = $db->pquery($sql, ['% '.$agentManagerId, '% '.$agentManagerId.' %', $agentManagerId.' %', $agentManagerId]);
    $users  = [];
    while ($row =& $result->fetchRow()) {
        $users[] = $row['id'];
    }
    if (count($users) > 0) {
        return $users;
    }

    return false;
}

function getUserNameForUserId($userId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT user_name FROM `vtiger_users` WHERE id = ?";
    $result = $db->pquery($sql, [$userId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['user_name'];
    }

    return false;
}

function getAgencyCodeForGroup($groupId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT `vtiger_agentmanager`.agency_code FROM `vtiger_agentmanager` WHERE `vtiger_agentmanager`.groupid = ?";
    $result = $db->pquery($sql, [$groupId]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['agency_code'];
    }

    return false;
}

function getAgencyCodeForUser($userid)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT `vtiger_agentmanager`.agency_code FROM `vtiger_agentmanager`
               JOIN `vtiger_user2agency` ON `vtiger_agentmanager`.agentmanagerid = `vtiger_user2agency`.agency_code
               WHERE `vtiger_user2agency`.userid = ?";
    $result = $db->pquery($sql, [$userid]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['agency_code'];
    }

    return false;
}

function getCarrierCodeFromAgencyCode($agentCode)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT IF(`vtiger_vanlinemanager`.vanline_id = 9, 'NAVL', IF(`vtiger_vanlinemanager`.vanline_id = 1, 'AVL', '')) AS carrier_code FROM `vtiger_vanlinemanager`
               JOIN `vtiger_agentmanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
               WHERE `vtiger_agentmanager`.agency_code = ?";
    $result = $db->pquery($sql, [$agentCode]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['carrier_code'];
    }

    return false;
}

function getRelatedFromDocumentId($docId)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT crmid FROM `vtiger_senotesrel` WHERE notesid = ?";
    $result = $db->pquery($sql, [$docId]);
    $crmIds = [];
    while ($row =& $result->fetchRow()) {
        $crmIds[] = $row['crmid'];
    }
    $results = [];
    foreach ($crmIds as $crmId) {
        $sql                     = "SELECT crmid, setype FROM `vtiger_crmentity` WHERE crmid = ?";
        $result                  = $db->pquery($sql, [$crmId]);
        $row                     = $result->fetchRow();
        $results[$row['setype']] = $row['crmid'];
    }

    return $results;
}

function testOpList($postdata)
{
    $opListId     = $postdata['OpListId'];
    $user         = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    try {
        $wsid   = vtws_getWebserviceEntityId('OPList', $opListId); // Module_Webservice_ID x CRM_ID
        $opList = vtws_retrieve($wsid, $current_user);
    } catch (WebServiceException $ex) {
        $response = generateErrorArray('FAILED_TO_RETRIEVE_OPLIST', $ex->getMessage());

        return json_encode($response);
    }

    return json_encode($opList);
}

function ConvertDatesToUserFormatForOpList($inputPostArray) {
    //@NOTE: The current date standard is MM-DD-YYYY when an ops list answer is sent
    global $current_user;
    $outputArray = [];
    foreach ($inputPostArray as $fieldName => $fieldValue) {
        if (is_array($fieldValue)) {
            //@TODO Test
            $outputArray[$fieldName] = ConvertDatesToUserFormat($fieldValue);
        } else {
            if ($fieldValue && preg_match('/answer_.*_date_/', $fieldName)) {
                $fieldValue = DateTimeField::__convertToYMDTimeFormat($fieldValue, 'mm-dd-yyyy');
                $fieldValue = DateTimeField::convertToUserFormat($fieldValue, $current_user);
            }
            $outputArray[$fieldName] = $fieldValue;
        }
    }
    return $outputArray;
}

function saveOpListAnswers($postdata)
{
    //@TODO: Maybe this comes out sometime in the future?
    $postdata = ConvertDatesToUserFormatForOpList($postdata);

    $postdata['NoRedirect'] = true; //the save action normally header redirects we don't want to do that
    $saveAction             = new OPList_SaveOpListAnswers_Action();
    $request                = new Vtiger_Request($postdata);
    $saveAction->process($request);

    return json_encode(['success' => 'true', 'result' => []]);
}

function getOpListsByAgentCode($postdata)
{
    global $current_user;
    $db     = PearDatabase::getInstance();
    $sql    = 'SELECT * FROM `vtiger_agentmanager`
                JOIN `vtiger_crmentity` ON `vtiger_agentmanager`.`agentmanagerid` = `vtiger_crmentity`.`crmid`
                WHERE `vtiger_crmentity`.`deleted` = 0
                AND `vtiger_agentmanager`.`agency_code` = ?';
    $result = $db->pquery($sql, [$postdata['agent_code']]);
    if ($db->num_rows($result) <= 0) {
        $response = generateErrorArray('INVALID_VALUE', 'agent_code is expected to be a valid agency_code in the system');

        return json_encode($response);
    }

    //so what we want to do here is find out the agent and their vanline then
    //find all the OpList assigned to those, then spam retrieve to get the OpList
    //then we say gg.
    $assignedTo = [];

    //find out the vanline
    $assignedTo[] = getVanlineGroupByAgentCode($postdata['agent_code'], $result);

    //find out the agency's agentID (and or groupid)
    $assignedTo[] = getAgentGroupByAgentCode($postdata['agent_code'], $result);

    //pull the agency's assigned oplists
    $crmIds = getOpListsIdsByOwners($assignedTo);

    //take array of OpListIds and spam retrieve
    $opLists      = [];
    $user         = new Users();
    // making this the global, since down the line permissions are checked for the current user, not the passed in one.
    global $current_user;
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    foreach ($crmIds as $crmId) {
        try {
            $wsid   = vtws_getWebserviceEntityId('OPList', $crmId); // Module_Webservice_ID x CRM_ID
            $opList = vtws_retrieve($wsid, $current_user);
        } catch (WebServiceException $ex) {
            $response = generateErrorArray('FAILED_TO_RETRIEVE_OPLIST', $ex->getMessage());

            return json_encode($response);
        }
        $opLists[] = $opList;
    }

    //return array of retrieved OpList records.
    $response = ['success' => true, 'result' => $opLists];

    return json_encode($response);
}

function getVanlineGroupByAgentCode($agentCode, $result = false)
{
    if ($result && method_exists($result, 'fetchRow')) {
        while ($row = $result->fetchRow()) {
            if (array_key_exists('vanline_id', $row)) {
                return $row['vanline_id'];
            }
        }
    }

    $db     = &PearDatabase::getInstance();
    $sql    = 'SELECT `vanline_id` FROM `vtiger_agentmanager`
                JOIN `vtiger_crmentity` ON `vtiger_agentmanager`.`agentmanagerid` = `vtiger_crmentity`.`crmid`
                WHERE `vtiger_crmentity`.`deleted` = 0
                AND `vtiger_agentmanager`.`agency_code` = ?';
    $result = $db->pquery($sql, [$agentCode]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['vanline_id'];
    }

    return false;
}

function getAgentGroupByAgentCode($agentCode, $result = false)
{
    if ($result && method_exists($result, 'fetchRow')) {
        while ($row = $result->fetchRow()) {
            if (array_key_exists('agentmanagerid', $row)) {
                return $row['agentmanagerid'];
            }
        }
    }

    $db     = &PearDatabase::getInstance();
    $sql    = 'SELECT `agentmanagerid` FROM `vtiger_agentmanager`
                JOIN `vtiger_crmentity` ON `vtiger_agentmanager`.`agentmanagerid` = `vtiger_crmentity`.`crmid`
                WHERE `vtiger_crmentity`.`deleted` = 0
                AND `vtiger_agentmanager`.`agency_code` = ?';
    $result = $db->pquery($sql, [$agentCode]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['agentmanagerid'];
    }

    return false;
}

function getOpListsIdsByOwners($owners)
{
    $db  = PearDatabase::getInstance();
    $params = [];
    $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE setype = 'OPList' AND deleted = 0 AND ( ";
    foreach ($owners as $key => $owner) {
        if ($key != 0) {
            $sql .= " OR ";
        }
        //can smownerid be a vanline_id?
        //smownerid can't be a vanline or agentmanager id,
        //but this shouldn't be a problem because the crmid is "unique"
        //so this will allow passing in of "groups" when that is implemented.
        //$sql .= "smownerid = ? OR agentid = ?";
        $sql .= "agentid = ?";
        //$params[] =  $owner;
        $params[] =  $owner;
    }
    $sql .= " )";
    $result    = $db->pquery($sql, $params);
    $oplistIds = [];
    while ($row =& $result->fetchRow()) {
        $oplistIds[] = $row['crmid'];
    }

    return $oplistIds;
}

function getUserDepth($postdata)
{
    $parts = explode('x', $postdata['userId']);
    if ($parts[0] != '19' || count($parts) != 2) {
        return json_encode(generateErrorArray('INVALID_FORMAT', 'userId is expected to be in the format of 19x##'));
    }
    $user = Users_Record_Model::getInstanceById($parts[1], 'Users');
    if (empty($user)) {
        return json_encode(generateErrorArray('INVALID_VALUE', 'userId must be a valid userId in the system'));
    }
    $depth = getRoleDepth($user->get('roleid'));
    if ($depth) {
        return json_encode(['success' => 'true', 'result' => $depth]);
    }

    return json_encode(generateErrorArray('INVALID_VALUE', 'userId must be a valid userId in the system'));
}

function getTariffsByType($postdata)
{
    global $current_user;
    $db = PearDatabase::getInstance();
    $valid_types = [];
    $sql         = "SELECT custom_tariff_type FROM `vtiger_custom_tariff_type`";
    $result      = $db->pquery($sql, []);
    while ($row =& $result->fetchRow()) {
        $valid_types[] = $row['custom_tariff_type'];
    }
    if (!in_array($postdata['Type'], $valid_types)) {
        return json_encode(generateErrorArray('INVALID_VALUE', 'Type must be a valid custom_tariff_type in the system'));
    }
    $tariffs = [];
    $sql     = "SELECT tariffmanagerid FROM `vtiger_tariffmanager` JOIN `vtiger_crmentity` ON `vtiger_tariffmanager`.tariffmanagerid=`vtiger_crmentity`.crmid WHERE custom_tariff_type = ? AND deleted = 0";
    $result  = $db->pquery($sql, [$postdata['Type']]);
    while ($row =& $result->fetchRow()) {
        try {
            $user         = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $wsid         = vtws_getWebserviceEntityId('TariffManager', $row['tariffmanagerid']);
            $tariff       = vtws_retrieve($wsid, $current_user);
            $tariffs[]    = $tariff;
        } catch (WebServiceException $ex) {
            return json_encode(generateErrorArray('FAILED_TO_RETRIEVE_TARIFF', $ex->getMessage()));
        }
    }

    return json_encode(['success' => 'true', 'result' => ['Tariffs' => $tariffs]]);
}

function getAccounts($postdata)
{
    $db = PearDatabase::getInstance();

    $sql = 'SELECT accountid FROM vtiger_account';

    if (in_array($postdata['SearchColumn']) && in_array($postdata['SearchValue'])) {
        $sql = $sql . ' WHERE ? = ?';
        $result = $db->pquery($sql, [$postdata['SearchColumn'], $postdata['SearchValue']]);
    } else {
        $result = $db->pquery($sql, []);
    }

    $sql     = "SELECT tariffmanagerid FROM `vtiger_tariffmanager` WHERE custom_tariff_type = ?";
    $result  = $db->pquery($sql, [$postdata['Type']]);
    while ($row =& $result->fetchRow()) {
        try {
            $user         = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $wsid         = vtws_getWebserviceEntityId('TariffManager', $row['tariffmanagerid']);
            $tariff       = vtws_retrieve($wsid, $current_user);
            $tariffs[]    = $tariff;
        } catch (WebServiceException $ex) {
            return json_encode(generateErrorArray('FAILED_TO_RETRIEVE_TARIFF', $ex->getMessage()));
        }
    }

    return json_encode(['success' => 'true', 'result' => ['Tariffs' => $tariffs]]);
}

function getStops($postData)
{
    $recordId = $postData['record'];
    $module   = $postData['module'];
    $db = PearDatabase::getInstance();
    $stops  = [];
    $sql    = 'SELECT * FROM `vtiger_extrastops` WHERE extrastops_relcrmid = ?';
    $result = $db->pquery($sql, [$recordId]);
    $row    = $result->fetchRow();
    while ($row != null) {
        $stops[] = $row;
        $row     = $result->fetchRow();
    }
    return json_encode(['success' => 'true', 'result' => $stops]);
}

function getLMPLeadId($leadid)
{
    $db     = PearDatabase::getInstance();
    $sql    = "SELECT `lmp_lead_id` FROM `vtiger_leaddetails` WHERE `leadid` = ?";
    $result = $db->pquery($sql, [$leadid]);
    $row    = $result->fetchRow();
    if ($row) {
        return $row['lmp_lead_id'];
    }

    return false;
}

function addFieldHistory($postData)
{
    $db = PearDatabase::getInstance();
    $recordId = $postData['record'];
    $field    = $postData['field'];
    $oldValue = $postData['oldValue'];
    $newValue = $postData['newValue'];

    //error handling
    if (!$recordId) {
        $errCode    = "MISSING_REQUIRED_DATA";
        $errMessage = "Missing required information: 'record'";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    } elseif (!$field) {
        $errCode    = "MISSING_REQUIRED_DATA";
        $errMessage = "Missing required information: 'field'";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    } elseif (!$oldValue) {
        $errCode    = "MISSING_REQUIRED_DATA";
        $errMessage = "Missing required information: 'oldValue'";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    } elseif (!$newValue) {
        $errCode    = "MISSING_REQUIRED_DATA";
        $errMessage = "Missing required information: 'newValue'";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        logAndEmitResponse($response);
    }

    $date = date('Y-m-d H:i:s');
    $module   = $db->pquery('SELECT setype FROM `vtiger_crmentity` WHERE crmid=?', [$recordId])->fetchRow()['setype'];

    //concurrency issue
    $trackerId = $db->pquery("SELECT id FROM `vtiger_modtracker_basic_seq`", [])->fetchRow()['id'];
    $trackerId++;
    $db->pquery("UPDATE `vtiger_modtracker_basic_seq` SET id = ?", [$trackerId]);

    $sql = "INSERT INTO `vtiger_modtracker_basic` (id, crmid, module, whodid, changedon, status) VALUES (?,?,?,1,?,0)";
    $db->pquery($sql, [$trackerId, $recordId, $module, $date]);

    $sql = "INSERT INTO `vtiger_modtracker_detail` (id, fieldname, prevalue, postvalue) VALUES (?,?,?,?)";
    $db->pquery($sql, [$trackerId, $field, $oldValue, $newValue]);

    //handling for ui type 10's
    $uiType = $db->pquery(
        "SELECT `vtiger_field`.uitype FROM `vtiger_field` INNER JOIN `vtiger_tab` ON `vtiger_field`.tabid = `vtiger_tab`.tabid
                            WHERE `vtiger_field`.fieldname = ? AND `vtiger_tab`.name = ?",
        [$field, $module]
    )->fetchRow()['uitype'];
    if ($uiType == 10) {
        $relatedModule = $db->pquery("SELECT setype FROM `vtiger_crmentity` WHERE crmid = ?", [$newValue])->fetchRow()['setype'];
        file_put_contents('logs/devLog.log', "\n UITYPE $uiType", FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n REL-MOD $relatedModule", FILE_APPEND);
        $sql = "INSERT INTO `vtiger_modtracker_relations` (id, targetmodule, targetid, changedon) VALUES (?,?,?,?)";
        $db->pquery($sql, [$trackerId, $relatedModule, $newValue, $date]);
    }

    return json_encode(['success' => 'true']);
}

//function getEventsById($postdata) {
//    $db = PearDatabase::getInstance();
//    $sql = "SELECT * FROM `vtiger_modtracker_basic` RIGHT JOIN `vtiger_modtracker_detail` USING(id) WHERE crmid = ? ORDER BY `changedon` DESC, `fieldname` DESC";
//
//    $result = $db->pquery($sql, [$postdata['RecordId']]);
//
//    $response = [];
//
//    while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)){
//        //NOT FINISHED, BECUASE ALEX IS IMPATIENT
//    }
//
//    return json_encode($response);
//}

function getEventsById($postdata)
{
    $db = PearDatabase::getInstance();
    $sql = "SELECT * FROM `vtiger_modtracker_basic` WHERE crmid=? AND changedon>?";

    $recordId = convertFromWebservice($postdata['RecordId']);
    $changedAfter = $postdata['ChangedAfter'];

    $result = $db->pquery($sql, [$recordId, $changedAfter]);

    $changes = [];

    while ($row =& $result->fetchRow()) {
        $change = [];
        $sql = "SELECT * FROM `vtiger_modtracker_detail` WHERE id=?";
        $changeResult = $db->pquery($sql, [$row['id']]);
        $change['changedon'] = $row['changedon'];
        $change['userid'] = vtws_getWebserviceEntityId('Users', $row['whodid']);
        $change['module'] = $row['module'];
        $fieldChanges = [];
        while ($changeRow =& $changeResult->fetchRow()) {
            $fieldChange = [];
            $fieldChange['fieldname'] = $changeRow['fieldname'];
            $fieldChange['prevalue'] = $changeRow['prevalue'];
            $fieldChange['postvalue'] = $changeRow['postvalue'];
            $fieldChanges[] = $fieldChange;
        }
        $change['field_changes'] = $fieldChanges;
        $changes[] = $change;
    }

    return json_encode(['success'=>'true', 'result'=>$changes]);
}

function checkRecordForDeletion($postdata)
{
    $recordId = convertFromWebservice($postdata['RecordId']);
    $db = PearDatabase::getInstance();
    $sql = "SELECT * FROM `vtiger_crmentity` WHERE crmid=?";
    $result = $db->pquery($sql, [$recordId]);
    $row = $result->fetchRow();
    if ($row == null) {
        return json_encode(generateErrorArray('INVALID_ID', 'The record ID provided does not exist in this database'));
    }

    $deleted = $row['deleted'] == 0 ? 'false' : 'true';
    return json_encode(['success'=>'true', 'result'=>['deleted'=>$deleted]]);
}

function migrateDocumentsForOrders($postdata)
{
    $oldOrderNumber = $postdata['oldOrder'];
    $newOrderNumber = $postdata['newOrder'];

    $db = PearDatabase::getInstance();
    $sql = "SELECT ordersid FROM `vtiger_orders` WHERE orders_no=?";
    $result = $db->pquery($sql, [$oldOrderNumber]);
    if (!$result->fields) {
        return json_encode(generateErrorArray('INVALID_ORDERNO', 'The Order Number provided for oldOrder does not exist in this database'));
    }
    $oldOrderId = $result->fields['ordersid'];

    $result = $db->pquery($sql, [$newOrderNumber]);
    if (!$result->fields) {
        return json_encode(generateErrorArray('INVALID_ORDERNO', 'The Order Number provided for newOrder does not exist in this database'));
    }
    $newOrderId = $result->fields['ordersid'];

    $sql = "SELECT notesid FROM `vtiger_senotesrel` WHERE crmid=?";
    $result = $db->pquery($sql, [$oldOrderId]);
    $migratedDocs = [];
    while ($row =& $result->fetchRow()) {
        $migratedDocs[] = '15x'.$row['notesid'];
    }

    $sql = "UPDATE `vtiger_senotesrel` SET crmid=? WHERE crmid=?";
    $result = $db->pquery($sql, [$newOrderId, $oldOrderId]);

    return json_encode(['success'=>'true', 'result'=>['documentsMigrated' => $migratedDocs]]);
}

function getMediaImage($postdata)
{
    $db = PearDatabase::getInstance();
    $mediaId = substr(strstr($postdata['mediaid'], 'x'), 1);

    $sql = "SELECT file_name FROM `vtiger_media` WHERE mediaid=?";
    $result = $db->pquery($sql, [$mediaId]);

    if(!$result) {
        return json_encode(generateErrorArray('DATABASE_ERROR', 'An unknown database error has occurred when attempting to lookup the provided mediaid.'));
    }

    $row = $result->fetchRow();

    if(!$row) {
        return json_encode(generateErrorArray('INVALID_MEDIAID', 'The provided mediaid does not correspond to a Media record.'));
    }

    $fileName = $mediaId.'_'.$row['file_name'];

    $sharedConfig = [
        'region'  => 'us-east-1',
        'version' => 'latest',
        'http'    => [
            'verify' => false
        ]
    ];
    $sdk    = new Sdk($sharedConfig);
    $client = $sdk->createS3();
    $key = getenv('INSTANCE_NAME')."_survey_images/".$fileName;

    try {
        $imageResult = $client->getObject([
            'Bucket' => 'live-survey',
            'Key'    => $key
        ]);
    } catch (Exception $e) {
        return json_encode(generateErrorArray('S3_ERROR', $e->getMessage()));
    }

    return json_encode(['success'=>'true', 'result'=>['imageData'=>base64_encode($imageResult['Body'])]]);
}

function logAndEmitResponse($response)
{
    $db = PearDatabase::getInstance();
    $sql = "CREATE TABLE IF NOT EXISTS vtiger_syncwebservice_requestlog (
              id INT(11) NOT NULL AUTO_INCREMENT,
              mode VARCHAR(50),
              sessionName VARCHAR(255),
              element TEXT,
              response TEXT,
              datestamp DATETIME,
              requestIpAddress VARCHAR(50),
              PRIMARY KEY(id)
            )";
    $db->query($sql);

    $mode = $_POST['mode'] ?: '';
    $sessionName = $_POST['sessionName'] ?: '';
    $element = $_POST['element'] ?: '';
    if ($mode == 'auth') {
        //@NOTE: don't store the password at all at all
        $element = preg_replace('/(["\']password["\']\s*:\s*["\']).*?(["\'])/i','$1$2',$element);
    }
    $sql = "INSERT INTO `vtiger_syncwebservice_requestlog` (mode, sessionName, element, response, datestamp, requestIpAddress) VALUES (?,?,?,?,?,?)";
    $db->pquery($sql, [$mode, $sessionName, $element, substr($response, 0, 8000000), date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR']]);
    die($response);
}

function getAndVerifyRecordModel ($recordID, $fieldName, $moduleName) {
    //@NOTE: should be redundant, but I think this will be quicker than going to get the null instance.
    if (!$recordID) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter '$fieldName' value was empty.";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        return [false, $response];
    }

    try {
        $rec = Vtiger_Record_Model::getInstanceById($recordID, $moduleName);
    } catch (\Exception $e) {
        $response = json_encode(generateErrorArray($e->getCode(), $e->getMessage()));
        return [false, $response];
    }
    if (
        !$rec ||
        $rec->getModuleName() != $moduleName
    ) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter '$fieldName' value did not match a valid record";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        return [false, $response];
    }

    return [true, $rec];
}

function putToVehicleRecord($rec, $postdata, $current_user) {
    list($orderEntityId, $orderid) = explode('x', $postdata['orderid']);

    if (!isset($orderid)) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter 'orderid' was not provided";
        $response   = json_encode(generateErrorArray($errCode, $errMessage));
        return $response;
    }

    //This is all the checking I'm doing, use this right.
    if (!$rec) {
        return;
    }
    //These are mapped to the "old" Vehicles module's names
    $rec->set('vehiclelookup_relcrmid', $orderid);
    $rec->set('vehiclelookup_make', $postdata['vehicle_make']);
    $rec->set('vehiclelookup_model', $postdata['vehicle_model']);
    $rec->set('vehiclelookup_year', $postdata['vehicle_year']);
    $rec->set('vehiclelookup_vin', $postdata['vehicle_vin']);
    $rec->set('vehiclelookup_color', $postdata['vehicle_color']);
    $rec->set('vehiclelookup_odometer', $postdata['vehicle_odometer']);
    $rec->set('vehiclelookup_license_state', $postdata['license_state']);
    $rec->set('vehiclelookup_license_number', $postdata['license_number']);
    $rec->set('vehiclelookup_type', $postdata['vehicle_type']);
    $rec->set('vehiclelookup_is_non_standard', $postdata['is_non_standard']);
    $rec->set('vehiclelookup_inoperable', $postdata['inoperable']);
    //This block maps the new module fieldnames to be set from the postdata if they are sent.
    //@NOTE: Overrides the "old" fieldname values.
    $columnKeys = array_keys($rec->getEntity()->column_fields);
    foreach ($columnKeys as $key) {
        if (!array_key_exists($key, $postdata)) {
            continue;
        }
        $rec->set($key, $postdata[$key]);
    }
    $rec->save();
    $vehicleArray = getVehicleArray($rec->getId(), $current_user);
    return json_encode(['success' => 'true', 'result' => $vehicleArray]);
}

function getVehicleArray($id, $current_user) {
    $wsid   = vtws_getWebserviceEntityId('VehicleLookup', $id);
    $entity = vtws_retrieve($wsid, $current_user);

    //We have to return the old values for compatibility, but this will merge in the new values.
    $updatedRow = [
        'vehicleid'        => $id,
        'orderid'          => $entity['vehiclelookup_relcrmid'],
        'vehicle_make'     => $entity['vehiclelookup_make'],
        'vehicle_model'    => $entity['vehiclelookup_model'],
        'vehicle_year'     => $entity['vehiclelookup_year'],
        'vehicle_vin'      => $entity['vehiclelookup_vin'],
        'vehicle_color'    => $entity['vehiclelookup_color'],
        'vehicle_odometer' => $entity['vehiclelookup_odometer'],
        'license_state'    => $entity['vehiclelookup_license_state'],
        'license_number'   => $entity['vehiclelookup_license_number'],
        'vehicle_type'     => $entity['vehiclelookup_type'],
        'is_non_standard'     => $entity['vehiclelookup_is_non_standard'],
        'inoperable'     => $entity['vehiclelookup_inoperable'],
    ];
    return array_merge($entity, $updatedRow);
}
