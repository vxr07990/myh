<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class EmailTemplates_Detail_View extends Vtiger_Index_View
{
    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = EmailTemplates_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel          = $this->record->getRecord();
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
        $viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));
        $linkParams = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $linkModels = $this->record->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $linkModels);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));
        $viewer->assign('NO_PAGINATION', true);
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'DetailViewPreProcess.tpl';
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName  = $request->getModule();
        $record      = $request->get('record');
        $viewer      = $this->getViewer($request);
        $recordModel = EmailTemplates_Record_Model::getInstanceById($record);
        $recordModel->setModule($moduleName);
        $db        = PearDatabase::getInstance();
        $sql       = "SELECT movehq, db_version FROM `database_version`";
        $result    = $db->pquery($sql, []);
        $row       = $result->fetchRow();
        $dbVersion = $row[1];
        if (getenv('IGC_MOVEHQ')) {
            $softwareName  = 'moveHQ';
            $developerName = 'WIRG';
            $developerSite = 'www.mobilemover.com';
            $logo          = '<img src="test/logo/MoveHQ.png" title="MoveHQ.png" alt="MoveHQ.png">';
            $website       = 'www.mobilemover.com';
            $supportTeam   = 'MoveHQ Support Team';
            $supportEmail  = getenv('SUPPORT_EMAIL_ADDRESS');
        } else {
            $softwareName  = 'moveCRM';
            $developerName = 'IGC Software';
            $developerSite = 'www.igcsoftware.com';
            $logo          = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
            $website       = 'www.igcsoftware.com';
            $supportTeam   = 'MoveCRM Support Team';
            $supportEmail  = getenv('SUPPORT_EMAIL_ADDRESS');
        }
        $body    = $recordModel->get('body');
        $subject = $recordModel->get('subject');
        global $vtiger_current_version;
        $subject = str_replace('$software_name$', $softwareName, $subject);
        $body    = str_replace('$software_name$', $softwareName, $body);
        $body    = str_replace('$logo$', $logo, $body);
        $body    = str_replace('$developer_name$', $developerName, $body);
        $body    = str_replace('$developer_site$', $developerSite, $body);
        $body    = str_replace('$website$', $website, $body);
        $body    = str_replace('$year$', date("Y"), $body);
        $body    = str_replace('$version_num$', $vtiger_current_version, $body);
        $body    = str_replace('$support_team$', $supportTeam, $body);
        $body    = str_replace('$support_email$', $supportEmail, $body);
        $recordModel->set('body', $body);
        $recordModel->set('subject', $subject);
        //file_put_contents('logs/devLog.log', "\n subject: ".$subject, FILE_APPEND);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->view('DetailViewFullContents.tpl', $moduleName);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames           = [
            'modules.Vtiger.resources.Detail',
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
