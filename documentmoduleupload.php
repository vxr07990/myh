<?php

/**
 * - `$_POST['element']`: Uploaded file contents
 */

/*
 * HTTP POST
 * Parameter name: sessionName
 * Parameter type: String
 * Parameter contents: valid Session Identifier for web service
 *
 * Parameter name: element
 * Parameter type: JSON
 * Parameter contents:
 * {
 *     filename: filename to be used for storing file on server
 *     doctitle: title of the document to be used in the CRM
 *     filetype: MIME type of file being uploaded
 *     userid: userid of CRM user to whom the Document should be assigned
 *     data: base64 encoded string with contents of file
 *     parentid: Optional - Array - ids of CRM entities (Opportunity, Account, etc.) to which document is related
 *     folderid: Optional - id of folder to which document should be linked; if not provided, default folder will be used
 *     invoice_pkg_format: tells us what packet it belongs to for Graebel
 * }
 */

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

global $current_user;
$requiredFields = [
    'data',
    'filename',
    'userid',
    'agentid',
    'doctitle',
    'filetype'
];

//default folderId
$folderId = '22x1';

$res = process($folderId, $requiredFields);
die($res);

function process($folderId, $requiredFields) {
    global $current_user;
    if ($failResponse = validatePOSTForDocumentUpload($_POST)) {
        return $failResponse;
    }
    if ($failResponse = validateWSSession($_POST['sessionName'])) {
        return $failResponse;
    }
    //Session Identifier has been verified - proceed with element parameter check

    //First urldecode the element string.
    //@NOTE: This makes the + in the base64 Data spaces... so whatever fix them below.
    $_POST['element'] = urldecode($_POST['element']);

    //decode the element string into a hash.
    $postdata = json_decode($_POST['element'], true);

    //Check that all Element parameters are all present
    foreach ($requiredFields as $fieldName) {
        if ($failResponse = validatePostDataParameter($postdata, $fieldName)) {
            return $failResponse;
        }
    }
    $db = PearDatabase::getInstance();
    //Verify that provided User ID is valid
    list ($wsId, $userId) = explode('x', $postdata['userid']);
    //get the module name of the webservice ID.
    $webserviceObject = VtigerWebserviceObject::fromId($db, $wsId);
    $idType           = $webserviceObject->getEntityName();
    if ($failResponse = validateUserID($idType, $postdata['userid'])) {
        return $failResponse;
    }
    //pull a user record model.
    if ($idType == 'Users') {
        $current_user = Users_Record_Model::getInstanceById($userId, 'Users');
    } else {
        $current_user = Users_Record_Model::getCurrentUserModel();
    }
    if (isset($postdata['folderid'])) {
        if ($validFolderId = validateFolderId($postdata['folderid'])) {
            $folderId = $validFolderId;
        }
    }
    $documentInfo = [
        'notes_title'           => $postdata['doctitle'],
        'filename'              => $postdata['filename'],
        'assigned_user_id'      => $postdata['userid'],  //needs to be a wsID
        'folderid'              => $folderId,
        'filetype'              => $postdata['filetype'],
        'agentid'               => $postdata['agentid'],
        'invoice_pkg_format'    => $postdata['invoice_pkg_format'],
        'invoice_document_type' => $postdata['invoice_document_type']
    ];
    $saveDocRes  = saveDocument($postdata['data'], $documentInfo, $current_user, (array) $postdata['parentid']);
    if (is_array($saveDocRes)) {
        $tmpResponse = [
            'success' => 'true',
            'result' => $saveDocRes['createResponse']
            ];
        if (empty($saveDocRes['errResponse'])) {
            $tmpResponse['result']['parentid'] = $postdata['parentid'];
        } else {
            $tmpResponse['result']['parentid'] = $saveDocRes['errResponse'];
        }
    } else {
        $tmpResponse = json_decode($saveDocRes, true);
    }
    return json_encode($tmpResponse);
}

function saveDocument(&$data, $documentInfo, $current_user, $linkedToRecords)
{
    //the server will remove + and makes them spaces, this may not always occur though.
    //No it'll always occur since we have to decode the whole element.
    $data = str_replace(' ', '+', $data);

    //Checks to make sure the report generated
    $decodedData = base64_decode($data);

    if (empty($decodedData)) {
        $errCode = "INVALID_DATA";
        $errMessage = "Parameter 'data' contains invalid base64 encoding";
        return json_encode(generateErrorArray($errCode, $errMessage));
    }

    $db = PearDatabase::getInstance();
    $filename = cleanDocumentName($documentInfo['filename']);
    $filePath = "/tmp";
    //@TODO: I'm saving the file to /tmp named temp_$PID_blah
    //the thought here is that we can't have concurrency with PID because this same process will move the file, if the file already
    //exists with that name it can be clobbered because that means the other process failed to finish.
    $tmp_filename = $filePath.'/temp_'.getmypid().'_'.$filename;
    $written = file_put_contents($tmp_filename, $decodedData);

    //@NOTE: $written is false on fail, 0 on zero written.
    if ($written === false) {
        return json_encode(generateErrorArray("UNABLE_TO_WRITE_FILE", "Unable to write to file"));
    } else if ($written == 0) {
        return json_encode(generateErrorArray("UNABLE_TO_WRITE_FILE", "Wrote 0 bytes"));
    }

    $documentInfo['filelocationtype'] = 'I';
    $documentInfo['filestatus'] = 1;
    $documentInfo['filesize'] = $written;

    //@TODO: discussed this with RP, I decided either way was kludgy, so I've chosen to use the existing crmentity function
    //and pass it reports=> true to allow it the option of using rename instead of move_uploaded_file.
    $_FILES[] = [
        'type' => $documentInfo['filetype'],
        'size' => $written,
        'tmp_name' => $tmp_filename,
        'original_name' => $documentInfo['filename'],
        'name' => checkExtension($documentInfo['filetype'], $documentInfo['filename']),
        //misnomer unfortunately it simply means use a rename because it's not in the upload dir.
        'reports' => true
    ];

    try {
        $docCreateResponse = vtws_create('Documents', $documentInfo, $current_user);
    } catch (Exception $ex) {
        return json_encode(generateErrorArray("UNABLE_TO_SAVE_FILE", $ex->getMessage()));
    } catch (WebServiceException $ex) {
        return json_encode(generateErrorArray("UNABLE_TO_SAVE_FILE", $ex->getMessage()));
    }
    list($wsID, $docid) = explode('x',$docCreateResponse['id']);

    $errResponse = [];
    if (!empty($docid)) {
        foreach ($linkedToRecords as $parentID) {
            list($wsID, $recordId) = explode('x',$parentID);
            if (!preg_match('/^[0-9]+$/',$recordId)) {
                continue;
            }
            if ($failResponse = checkParentIdExists($db, $parentID)) {
                $errResponse[] = $failResponse;
                continue;
            }
            $sql    = "INSERT INTO `vtiger_senotesrel` VALUES (?, ?)";
            $params = [$recordId, $docid];
            $db->pquery($sql, $params);
            addWalkDetail($docid, $recordId);
        }
    }
    return [
        'createResponse' => $docCreateResponse,
        'errResponse' => $errResponse
        ];
}

//@TODO: This doesn't enforce a string length is that an issue?
function cleanDocumentName($string)
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $string); // Removes special chars.
    $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.

    return $string;
}

function validateFolderId($folderid) {

    if (!$folderid) {
        return false;
    }

    $folderRecordId = 0;

    if (preg_match('/^[0-9]+x[0-9]+$/', $folderid)) {
        list ($wsId, $folderRecordId) = explode('x',$folderid);
    } else if (preg_match('/^[0-9]+$/', $folderid)) {
        $folderRecordId = $folderid;
    } else {
        return false;
    }

    //@NOTE: This function expects the ID to be a webservice string.
    if (validate('22x'.$folderRecordId, 'webservice', true, 'DocumentFolders')) {
        return $folderid;
    }

    return false;
}

function addWalkDetail($docID, $crmId) {
    if (!$docID) {
        return;
    }
    if (!$crmId) {
        return;
    }
    //@TODO, decide if we should limit this to the GMMS instance
    $db = PearDatabase::getInstance();
    $sql    = 'SELECT setype FROM vtiger_crmentity WHERE crmid=? LIMIT 1';
    $result = $db->pquery($sql, [$crmId]);
    $row    = $result->fetchRow();
    if ($row['setype'] == 'WalkDetails') {
        $sql    = 'SELECT `order_id` FROM `vtiger_walkdetails` WHERE walkdetailsid=? LIMIT 1';
        $result = $db->pquery($sql, [$crmId]);
        $row    = $result->fetchRow();
        if ($row != NULL && $row['order_id']) {
            $sql    = 'INSERT INTO `vtiger_senotesrel` VALUES (?,?)';
            $db->pquery($sql, [$row['order_id'], $docID]);
        }
    }
}

function checkExtension($filetype, $filename) {
    if ($filetype == 'application/pdf') {
        if (!preg_match('/\.pdf$/i', $filename)) {
            $filename = $filename.'.pdf';
        }
    } elseif ($filetype == 'image/jpeg') {
        if (preg_match('/\.jpeg$/i', $filename)) {
            $filename = preg_replace('/\.jpeg$/i', '.jpg', $filename);
        } else if (!preg_match('/\.jpg$/i', $filename)) {
            $filename .= '.jpg';
        }
    }

    return $filename;
}

function checkParentIdExists($db, $crmId) {
    $sql = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid=?";
    $result = $db->pquery($sql, [$crmId]);
    $row = $result->fetchRow();

    if ($row == null) {
        return json_encode(['error' => ["code"=>"INVALID_PARENTID", "message"=>"Provided ParentID is invalid. Document will remain unlinked"]]);
    }
}
