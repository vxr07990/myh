<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
error_reporting("E_ERROR");
//Opensource fix for tracking email access count
chdir(dirname(__FILE__). '/../../../');

require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
class Emails_TrackAccess_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        if (vglobal('application_unique_key') !== $request->get('applicationKey')) {
            exit;
        }

        global $adb, $current_user;
        $current_user=Users::getInstance('Users');
        $current_user->retrieve_entity_info('1','Users');
        $parentId = $request->get('parentId');
        $recordId = $request->get('record');
        if ($parentId && $recordId) {
            $recordModel = Emails_Record_Model::getInstanceById($recordId);
            $recordModel->updateTrackDetails($parentId);

            // Get access count
            $result = $adb->pquery("SELECT access_count FROM vtiger_email_track WHERE crmid = ? AND mailid = ?", array($parentId, $recordId));
            $access_count=$adb->query_result($result,0,'access_count');
            $recordModel->set('id', $recordId);
            $recordModel->set('mode', 'edit');
            $recordModel->set('access_count', $access_count+1);
            $_REQUEST['update_access_count'] = true;
            $recordModel->save();

        }
    }

    public function validateRequest(Vtiger_Request $request)
    {
        // This is a callback entry point file.
        return true;
    }
}

$track = new Emails_TrackAccess_Action();
$track->process(new Vtiger_Request($_REQUEST));
