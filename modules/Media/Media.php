<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

use Aws\Sdk;
include_once 'modules/Vtiger/CRMEntity.php';

class Media extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_media';
    public $table_index= 'mediaid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_mediacf', 'mediaid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_media', 'vtiger_mediacf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_media' => 'mediaid',
        'vtiger_mediacf'=>'mediaid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Title' => array('media', 'title'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Title' => 'title',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'title';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Title' => array('media', 'title'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Title' => 'title',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('title');

    // For Alphabetical search
    public $def_basicsearch_col = 'title';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'title';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('title','assigned_user_id');

    public $default_order_by = 'title';
    public $default_sort_order='ASC';

    /**
    * Invoked when special actions are performed on the module.
    * @param String Module name
    * @param String Event Type
    */
    public function vtlib_handler($moduleName, $eventType)
    {
        global $adb;
        if ($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }

    public function saveentity($module, $fileid = '') {
        $fieldList = array_merge($_REQUEST, $this->column_fields);
        parent::saveentity($module, $fileid);
        $db = PearDatabase::getInstance();
        $sql = "INSERT INTO `vtiger_mediarel` (crmid, mediaid) VALUES (?,?)";

        foreach($fieldList['parent_ids'] as $parentId) {
            $db->pquery($sql, [$parentId, $this->id]);
        }
    }
}



function retrieveMediaUrl($element, $user) {
    $client = initializeS3Client();
    $userModel = Vtiger_Record_Model::getInstanceById($user->id, 'Users');
    $accessibleAgents = $userModel->getAccessibleAgentsForUser();

    $db = PearDatabase::getInstance();
    $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
    $res = $db->pquery($sql, ['Media']);
    if($res && $db->num_rows($res) > 0) {
        $mediaEntityId = $res->fields['id'];
    } else {
        throw new WebServiceException(WebserviceErrorCode::$UNKOWNENTITY, "Media entity not defined");
    }

    $urlArray = [];

    foreach($element as $id) {
        $idPieces = explode('x', $id);
        $entityId = $idPieces[0];
        $recordId = $idPieces[1];
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        if($entityId != $mediaEntityId || $recordModel->get('record_module') != 'Media') {
            $urlArray[$recordId] = ['success' => 'false', 'error'=>['code'=>WebServiceErrorCode::$INVALIDID, 'message'=>'Provided ID does not correspond to a Media record']];
            continue;
        }
        if(array_key_exists($recordModel->get('agentid'), $accessibleAgents)) {
            if ($recordModel->get('is_video') == 0) {
                $urlArray[$recordId] = ['success' => 'true', 'result' => generateImageUrl($recordModel->getId().'_'.$recordModel->get('file_name'), $client)];
            } else {
                $urlArray[$recordId] = ['success' => 'true', 'result' => generateVideoUrl($recordModel->get('archiveid'), $client)];
            }
        } else {
            $urlArray[$recordId] = ['success' => 'false', 'error'=>['code'=>WebServiceErrorCode::$ACCESSDENIED, 'message' => 'Permission to read given object is denied']];
        }
    }

    return $urlArray;
}

function initializeS3Client() {
    $sharedConfig = [
        'region'  => 'us-east-1',
        'version' => 'latest',
        'http'    => [
            'verify' => false
        ]
    ];
    $sdk          = new Sdk($sharedConfig);
    return $sdk->createS3();
}

function generateImageUrl($fileName, $client) {
    return generateS3Url(getenv('INSTANCE_NAME')."_survey_images/".$fileName, $client);
}

function generateVideoUrl($archiveId, $client) {
    return generateS3Url(getenv('TOKBOX_API_KEY').'/'.$archiveId.'/archive.mp4', $client);
}

function generateS3Url($key, $client) {
    if(!$client) {
        $client = initializeS3Client();
    }
    $cmd = $client->getCommand('GetObject', [
        'Bucket' => 'live-survey',
        'Key'    => $key
    ]);
    $req = $client->createPresignedRequest($cmd, '+1 minutes');
    $url = (string) $req->getUri();

    return $url;
}
