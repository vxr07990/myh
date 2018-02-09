<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include_once 'modules/QuotingTool/QuotingTool.php';

/**
 * Class QuotingTool_ActionAjax_Action
 */
class QuotingTool_ActionAjax_Action extends Vtiger_Action_Controller
{
    /**
     * @constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('save');
        $this->exposeMethod('save_setting');
        $this->exposeMethod('delete');
        $this->exposeMethod('getTemplate');
        $this->exposeMethod('getHistories');
        $this->exposeMethod('getHistory');
        $this->exposeMethod('removeHistories');
        $this->exposeMethod('get_images');
        $this->exposeMethod('upload_image');
        $this->exposeMethod('simple_upload_image');
        $this->exposeMethod('validate_request');
        $this->exposeMethod('duplicate');
    }

    /**
     * @param Vtiger_Request $request
     * @return bool
     */
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * @param Vtiger_Request $request
     */
    public function validate_request(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        return $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function save(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();
        $record = $request->get('record');
        $timestamp = date('Y-m-d H:i:s', time());
        if ($record) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $module);
            $recordModel->set('id', $record);
            $recordModel->set('mode', 'edit');
            $recordModel->set('updated', $timestamp);
        } else {
            /** @var QuotingTool_Record_Model $recordModel */
            $recordModel = Vtiger_Record_Model::getCleanInstance($module);
            $recordModel->set('created', $timestamp);
            $recordModel->set('updated', $timestamp);
        }

        if ($request->has('agentid')) {
            $recordModel->set('agentid', $request->get('agentid'));
        }

        if ($request->has('filename')) {
            $recordModel->set('filename', $request->get('filename'));
        }

        if ($request->has('primary_module')) {
            $recordModel->set('module', $request->get('primary_module'));
        }

        if ($request->has('body')) {
            $recordModel->set('body', $request->get('body'));
        }

        if ($request->has('header')) {
            $recordModel->set('header', $request->get('header'));
        }

        if ($request->has('content')) {
            $recordModel->set('content', $request->get('content'));
        }

        if ($request->has('footer')) {
            $recordModel->set('footer', $request->get('footer'));
        }

        if ($request->has('description')) {
            $recordModel->set('description', $request->get('description'));
        }

        if ($request->has('anwidget')) {
            $recordModel->set('anwidget', ($request->get('anwidget') == 'true') ? 1 : 0);
        }

        if ($request->has('email_subject')) {
            $recordModel->set('email_subject', $request->get('email_subject'));
        }

        if ($request->has('email_content')) {
            $recordModel->set('email_content', $request->get('email_content'));
        }

        if ($request->has('mapping_fields')){
            $mapping_fields = ($request->get('mapping_fields')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('mapping_fields'), JSON_FORCE_OBJECT)) : null;
            $recordModel->set('mapping_fields', $mapping_fields);
        }

        if ($request->has('attachments')){
            $attachments = ($request->get('attachments')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('attachments'))) : null;
            $recordModel->set('body', $attachments);
        }

        $recordModel->save();
        $id = $recordModel->getId();

        if (!$id) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data['id'] = $id;
        $data['message'] = vtranslate('LBL_SUCCESSFUL', $module);

        // Save history
        if ($request->get('history')) {
            $historyRecordModel = new QuotingTool_HistoryRecord_Model();
            $historyParams = array(
                'body' => $recordModel->get('body')
            );
            $newHistory = $historyRecordModel->saveByTemplate($id, $historyParams);

            // Response data
            $calendarDatetimeUIType = new Calendar_Datetime_UIType();
            $data['history'] = array(
                'id' => $newHistory->getId(),
                'created' =>  $calendarDatetimeUIType->getDisplayValue($newHistory->get('created'))
            );
        }

        $response->setResult($data);
        return $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function save_setting(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();
        $params = array();

        /** @var QuotingTool_SettingRecord_Model $recordModel */
        $recordModel = new QuotingTool_SettingRecord_Model();
        $record = $request->get('record');  // templateID

        if ($request->has('description')) {
            $params['description'] = $request->get('description');
        }

        if ($request->has('label_decline')) {
            $params['label_decline'] = $request->get('label_decline');
        }

        if ($request->has('label_accept')) {
            $params['label_accept'] = $request->get('label_accept');
        }

        if ($request->has('background')){
            $params['background'] = ($request->get('background')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('background'), JSON_FORCE_OBJECT)) : null;
        }

        if ($request->has('page_format')){
            $params['page_format'] = ($request->get('page_format')) ?
                QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('page_format'), JSON_FORCE_OBJECT)) : null;
        }

        // Save data
        $id = $recordModel->saveByTemplate($record, $params);

        if (!$id) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data['id'] = $id;
        $data['message'] = vtranslate('LBL_SUCCESSFUL', $module);

        $response->setResult($data);
        return $response->emit();
    }
    
    public function duplicate(Vtiger_Request $request){
        $module = $request->getModule();
        $timestamp = date('Y-m-d H:i:s', time());
        $oldRecordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($oldRecordId, $module);
        $recordModel->set('id', '');
        $recordModel->set('created', $timestamp);
        $recordModel->set('updated', $timestamp);
        $recordModel->save();
        $templateId = $recordModel->getId();
        
        //save history
        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
            $historyParams = array(
                'body' => $recordModel->get('body')
            );
        $newHistory = $historyRecordModel->saveByTemplate($templateId, $historyParams);
        
        //get old record settings, save them in a new record
        $settingsRecordModel = new QuotingTool_SettingRecord_Model();
        $setingsRecord = $settingsRecordModel->findByTemplateId($oldRecordId);
        if($setingsRecord){
            $data['description'] = $setingsRecord->get('description');
            $data['label_decline'] = $setingsRecord->get('label_decline');
            $data['label_accept'] = $setingsRecord->get('label_accept');
            $data['background'] = $setingsRecord->get('background');
            $data['page_format'] = $setingsRecord->get('page_format');
            
            $id = $setingsRecord->saveByTemplate($templateId, $data);
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($templateId);
        return $response->emit();
    }
    

    /**
     * @param Vtiger_Request $request
     */
    public function delete(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $model = new QuotingTool_Record_Model();
        $success = $model->delete($recordId);
        header("Location: index.php?module=QuotingTool&view=List");
    }

    public function getTemplate(Vtiger_Request $request) {
        $module = $request->getModule();
        $record = $request->get('record');
        $relModule = $request->get('rel_module');
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        if (!$relModule) {
            $response->setError(200, vtranslate('LBL_INVALID_MODULE', $module));
            return $response->emit();
        }

        if (!isRecordExists($record)) {
            $response->setError(200, vtranslate('LBL_RECORD_NOT_FOUND', $module));
            return $response->emit();
        }

        $recordModel = Vtiger_Record_Model::getInstanceById($record);
        $agentid = $recordModel->get('agentid');

        $quotingToolRecordModel = new QuotingTool_Record_Model();
        $templates = $quotingToolRecordModel->findByModule($relModule, $agentid);
        /** @var Vtiger_Record_Model $template */
        foreach ($templates as $template) {
            $data[] = array(
                'id' => $template->getId(),
                'filename' => $template->get('filename'),
                'description' => $template->get('description')
            );
        }

        $response->setResult($data);
        return $response->emit();
    }

    public function getHistories(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        $record = $request->get('record');
        $calendarDatetimeUIType = new Calendar_Datetime_UIType();
        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $histories = $historyRecordModel->listAllByTemplateId($record);

        /** @var Vtiger_Record_Model $history */
        foreach ($histories as $history) {
            $data[] = array(
                'id' => intval($history->getId()),
//                'name' => $history->get('filename'),
                'created' =>  $calendarDatetimeUIType->getDisplayValue($history->get('created'))
            );
        }

        $response->setResult($data);
        return $response->emit();
    }

    public function getHistory(Vtiger_Request $request) {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $historyId = $request->get('history_id');

        if (!$historyId) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $history = $historyRecordModel->getById($historyId);

        /** @var Vtiger_Record_Model $history */
        if (!$history) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $data = array(
            'id' => $history->getId(),
            'body' => $history->get('body')
        );

        $response->setResult($data);
        return $response->emit();
    }

    public function removeHistories(Vtiger_Request $request) {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        $historyIds = $request->get('history_id');

        if (!$historyIds) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $historyIds = array_map('trim', explode(',', $historyIds));

        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $success = $historyRecordModel->removeHistories($historyIds);

        if (!$success) {
            // When error
            $response->setError(200, vtranslate('LBL_FAILURE', $module));
            return $response->emit();
        }

        $response->setResult($data);
        return $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function get_images(Vtiger_Request $request) {
        global $adb;

        $moduleName = $request->getModule();
        $configs = QuotingTool::getConfig();
        $quotingToolFolder = QuotingTool::DEFAULT_FOLDER;
        $agentid = $request->get('agentid');
        $userModel = Users_Record_Model::getCurrentUserModel();
        $listAgentManager = $userModel->getAccessibleOwnersForUser($moduleName);
        $attachments = array();

        if (array_key_exists($agentid, $listAgentManager)) {
            $sql = "SELECT
                  vtiger_notes.title,
                  vtiger_notes.filename,
                  vtiger_crmentity.modifiedtime,
                  vtiger_crmentity.smownerid,
                  vtiger_notes.filelocationtype,
                  vtiger_notes.filestatus,
                  vtiger_notes.notesid
                FROM vtiger_notes
                  INNER JOIN vtiger_crmentity ON vtiger_notes.notesid = vtiger_crmentity.crmid
                  LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
                  LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid
                  INNER JOIN vtiger_attachmentsfolder vtiger_attachmentsfolderfolderid
                    ON vtiger_notes.folderid = vtiger_attachmentsfolderfolderid.folderid
                WHERE vtiger_crmentity.deleted = 0 
                  AND vtiger_notes.notesid > 0
                  AND vtiger_crmentity.agentid = ?
                  AND vtiger_attachmentsfolderfolderid.foldername LIKE ?
                GROUP BY `vtiger_crmentity`.crmid
                ORDER BY modifiedtime DESC
                LIMIT 0, 21";
            $params = array($agentid, $quotingToolFolder);
            $result = $adb->pquery($sql, $params);
            $ids = array();

            if ($adb->num_rows($result) > 0) {
                while ($row = $result->fetchRow()) {
                    $ids[] = $row['notesid'];
                }

                $strIds = implode(',', $ids);
                $sql2 = "SELECT * 
                    FROM vtiger_attachments AS attachment
                      INNER JOIN vtiger_seattachmentsrel AS attachmentsrel ON (attachmentsrel.attachmentsid = attachment.attachmentsid)
                    WHERE attachmentsrel.crmid IN ({$strIds}) AND attachment.type LIKE 'image/%'";
                $params2 = array();
                $result2 = $adb->pquery($sql2, $params2);

                if ($adb->num_rows($result2) > 0) {
                    while ($row2 = $result2->fetchRow()) {
                        $attachments[] = array(
                            'image' => $configs['base'] . $row2['path'] . $row2['attachmentsid'] . '_' . $row2['name'],
                            'thumb' => $configs['base'] . $row2['path'] . $row2['attachmentsid'] . '_' . $row2['name'],
                            'folder' => '',
                        );
                    }
                }
            }
        }

        echo json_encode($attachments);
        exit;
    }

    /**
     * @param $string
     * @return mixed
     */
    function jsValue($string)
    {
        return preg_replace('/\r?\n/', "\\n", str_replace('"', "\\\"", str_replace("'", "\\'", $string)));
    }

    /**
     * @param $url
     * @param string $message
     */
    function callBack($url, $message="")
    {
        $message = $this->jsValue($message);
        $CKfuncNum = isset($_REQUEST['CKEditorFuncNum']) ? $_REQUEST['CKEditorFuncNum'] : 0;
        if (!$CKfuncNum) {
            $CKfuncNum = 0;
        }

        $result = '<html><body><script type="text/javascript">' .
            '        var kc_CKEditor = (window.parent && window.parent.CKEDITOR) ? window.parent.CKEDITOR.tools.callFunction' .
            '            : ((window.opener && window.opener.CKEDITOR) ? window.opener.CKEDITOR.tools.callFunction : false);' .
            '        var kc_FCKeditor = (window.opener && window.opener.OnUploadCompleted) ? window.opener.OnUploadCompleted' .
            '            : ((window.parent && window.parent.OnUploadCompleted) ? window.parent.OnUploadCompleted : false);' .
            '        var kc_Custom = (window.parent && window.parent.KCFinder) ? window.parent.KCFinder.callBack' .
            '            : ((window.opener && window.opener.KCFinder) ? window.opener.KCFinder.callBack : false);' .
            '        if (kc_CKEditor)' .
            '            kc_CKEditor(' . $CKfuncNum . ', "' . $url . '", "' . $message . '");' .
            '        if (kc_FCKeditor)' .
            '            kc_FCKeditor(' . (strlen($message) ? 1 : 0) .', "' . $url . '", "", "' . $message . '");' .
            '        if (kc_Custom) {' .
            '            if (' . strlen($message) . ') alert("' . $message . '");' .
            '            kc_Custom("' . $url . '");' .
            '        }' .
            '        if (!kc_CKEditor && !kc_FCKeditor && !kc_Custom)' .
            '            alert("' . $message . '");' .
            '    </script></body></html>';

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_HTML);
        $response->setResult($result);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function upload_image(Vtiger_Request $request) {
        global $adb;

        $moduleName = $request->getModule();
        $agentid = $request->get('agentid');
        $userModel = Users_Record_Model::getCurrentUserModel();
        $listAgentManager = $userModel->getAccessibleOwnersForUser($moduleName);

        if (!array_key_exists($agentid, $listAgentManager)) {
            // invalid agentid
            return;
        }

        /** @var Documents_Record_Model $documentRecordModel */
        $documentRecordModel = Vtiger_Record_Model::getCleanInstance('Documents');
        $documentRecordModel->set('filelocationtype', 'I');

        // Internal file
        if (isset($_FILES['upload'])) {
            $_FILES['filename'] = $_FILES['upload'];
        }

        // Title
        $notes_title = time();

        if ($_FILES['filename']) {
            $notes_title = $_FILES['filename']['name'];
        }

        $documentRecordModel->set('notes_title', $notes_title);
        $documentRecordModel->set('agentid', $agentid);
        $documentRecordModel->set('filestatus', 1);

        // Folder
        $folderid = 1;  // Default DB folder
        $sql = "SELECT * FROM vtiger_attachmentsfolder WHERE foldername LIKE ? LIMIT 1;";
        $params = array(QuotingTool::DEFAULT_FOLDER);
        $result = $adb->pquery($sql, $params);

        if ($adb->num_rows($result)) {
            // Default image folder for Document Designer
            $folderid = $adb->query_result($result, 0,'folderid');
        }

        $documentRecordModel->set('folderid', $folderid);
        $documentRecordModel->save();

        $configs = QuotingTool::getConfig();
        $sql = "SELECT * 
                FROM vtiger_attachments AS attachment
                  INNER JOIN vtiger_seattachmentsrel AS attachmentsrel ON (attachmentsrel.attachmentsid = attachment.attachmentsid)
                WHERE attachmentsrel.crmid = ? AND attachment.type LIKE 'image/%' LIMIT 1;";
        $params = array($documentRecordModel->getId());
        $result = $adb->pquery($sql, $params);
        $attachment = null;

        if ($adb->num_rows($result) > 0) {
            while ($row2 = $result->fetchRow()) {
                $attachment = $configs['base'] . $row2['path'] . $row2['attachmentsid'] . '_' . $row2['name'];
                break;
            }
        }

        // Callback
        $this->callBack($attachment);
    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function simple_upload_image(Vtiger_Request $request) {
        global $adb;

        $moduleName = $request->getModule();
        $agentid = $request->get('agentid');
        $userModel = Users_Record_Model::getCurrentUserModel();
        $listAgentManager = $userModel->getAccessibleOwnersForUser($moduleName);

        if (!array_key_exists($agentid, $listAgentManager)) {
            // invalid agentid
            return;
        }

        /** @var Documents_Record_Model $documentRecordModel */
        $documentRecordModel = Vtiger_Record_Model::getCleanInstance('Documents');
        $documentRecordModel->set('filelocationtype', 'I');

        // Internal file
        if (isset($_FILES['upload'])) {
            $_FILES['filename'] = $_FILES['upload'];
        }

        // Title
        $notes_title = time();

        if ($_FILES['filename']) {
            $notes_title = $_FILES['filename']['name'];
        }

        $documentRecordModel->set('notes_title', $notes_title);
        $documentRecordModel->set('agentid', $agentid);
        $documentRecordModel->set('filestatus', 1);

        // Folder
        $folderid = 1;  // Default DB folder
        $sql = "SELECT * FROM vtiger_attachmentsfolder WHERE foldername LIKE ? LIMIT 1;";
        $params = array(QuotingTool::DEFAULT_FOLDER);
        $result = $adb->pquery($sql, $params);

        if ($adb->num_rows($result)) {
            // Default image folder for Document Designer
            $folderid = $adb->query_result($result, 0,'folderid');
        }

        $documentRecordModel->set('folderid', $folderid);
        $documentRecordModel->save();

        $configs = QuotingTool::getConfig();
        $sql = "SELECT * 
                FROM vtiger_attachments AS attachment
                  INNER JOIN vtiger_seattachmentsrel AS attachmentsrel ON (attachmentsrel.attachmentsid = attachment.attachmentsid)
                WHERE attachmentsrel.crmid = ? AND attachment.type LIKE 'image/%' LIMIT 1;";
        $params = array($documentRecordModel->getId());
        $result = $adb->pquery($sql, $params);
        $attachment = null;

        if ($adb->num_rows($result) > 0) {
            while ($row2 = $result->fetchRow()) {
                $attachments[] = array(
                    'image' => $configs['base'] . $row2['path'] . $row2['attachmentsid'] . '_' . $row2['name'],
                    'thumb' => $configs['base'] . $row2['path'] . $row2['attachmentsid'] . '_' . $row2['name'],
                    'folder' => '',
                );

                if (!$attachment) {
                    // for single attachment
                    $attachment = $configs['base'] . $row2['path'] . $row2['attachmentsid'] . '_' . $row2['name'];
                }
            }
        }

        echo json_encode($attachments);
        exit;
    }

}