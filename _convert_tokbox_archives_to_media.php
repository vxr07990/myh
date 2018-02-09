<?php

require_once 'includes/main/WebUI.php';
require_once 'include/utils/utils.php';
require_once 'modules/Users/CreateUserPrivilegeFile.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'vendor/autoload.php';
require_once 'include/Webservices/Create.php';

$db = PearDatabase::getInstance();

$sql            = "SELECT *
                     FROM `vtiger_tokbox_archives`";

$mediaSql       = "SELECT mediaid
                     FROM `vtiger_media` 
                    WHERE archiveid=?";

$createDataSql  = "SELECT created_at, cubesheetsid
                     FROM `vtiger_tokbox_archives` 
                     JOIN `vtiger_cubesheets` 
                       ON `vtiger_tokbox_archives`.sessionid=`vtiger_cubesheets`.tokbox_sessionid 
                    WHERE `vtiger_tokbox_archives`.archiveid=?";

$result = $db->query($sql);

while($row =& $result->fetchRow()) {
    $mediaResult = $db->pquery($mediaSql, [$row['archiveid']]);
    if($db->num_rows($mediaResult) == 0) {
        $createDataResult = $db->pquery($createDataSql, [$row['archiveid']]);
        $createDataRow = $createDataResult->fetchRow();
        if($createDataRow) {
            createNewMediaRecord($row['archiveid'], $createDataRow['cubesheetsid'], $createDataRow['created_at']);
        }
    }
}


function createNewMediaRecord($archiveId, $recordId, $createdTime) {
    $cubesheetRecord = Vtiger_Record_Model::getInstanceById($recordId);

    $userModule = new Users();
    $createUser = $userModule->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

    $parents = [$cubesheetRecord->getId(), $cubesheetRecord->get('contact_id'), $cubesheetRecord->get('potential_id')];

    $mediaData = [
        'assigned_user_id'  => '19x'.$cubesheetRecord->get('assigned_user_id'),
        'agentid'           => getRecordAgentOwner($cubesheetRecord->get('potential_id')),
        'is_video'          => '1',
        'title'             => 'Video Archive '.$createdTime,
        'archiveid'         => $archiveId,
        'parent_ids'        => $parents
    ];

    try {
        $mediaCreateResponse = vtws_create('Media', $mediaData, $createUser);
    } catch (Exception $e) {
        $response = new Vtiger_Response();
        $response->setError('Error Creating Media Record', $e->getMessage());
        $response->emit();
        return;
    }
}
