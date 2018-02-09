<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include('modules/Emails/mail.php');
include('modules/QuotingTool/QuotingTool.php');
include('modules/QuotingTool/resources/mpdf/mpdf.php');

/**
 * Class QuotingTool_PDFHandler_Action
 */
class QuotingTool_PDFHandler_Action extends Vtiger_Action_Controller
{
    /**
     * Fn - __construct
     */
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('export');
//        $this->exposeMethod('send_email');
        $this->exposeMethod('download');
//        $this->exposeMethod('download_with_signature');
        $this->exposeMethod('preview_and_send_email');
        $this->exposeMethod('preview_and_edit_pdf');
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
     * Fn - process
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
     * Fn - downloadPreview
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function export(Vtiger_Request $request)
    {
        global $adb, $site_URL, $current_user;

        $moduleName = $request->getModule();
        $entityId = $request->get('record');
        $templateId = $request->get('template_id');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($templateId);

        if (!$record) {
            echo vtranslate('LBL_NOT_FOUND', $moduleName);
            exit;
        }

        $quotingTool = new QuotingTool();
        $module = $record->get('module');
        $record = $record->decompileRecord($entityId, array('header', 'content', 'footer'));
        // File name
        $fileName = $quotingTool->makeUniqueFile($record->get('filename'));

        // Create Documents Attachment
        $attachmentId   = $adb->getUniqueID("vtiger_crmentity");
        $date_var = date("Y-m-d H:i:s");
        $sql1    = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
        $params1 = array($attachmentId, $current_user->id, $current_user->id, "Documents Attachment", '', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
        $adb->pquery($sql1, $params1);

        $sql2    = "insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
        $params2 = array($attachmentId, $fileName, '', 'application/pdf', QuotingTool::DEFAULT_PDF_FOLDER);
        $result  = $adb->pquery($sql2, $params2);

        /** @var QuotingTool_Record_Model $model */
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        // Encode before put to database
        $full_content = base64_encode($record->get('content'));
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $module, $entityId, null, null,
            $full_content, $record->get('description'));

        // Merge special tokens
        $keys_values = array();
        $site = rtrim($site_URL, '/');
        $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$transactionId}";
        $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);

        $varContent = $quotingTool->getVarFromString($record->get('content'));
        foreach ($varContent as $var) {
            if ($var == '$custom_proposal_link$') {
                $keys_values['$custom_proposal_link$'] = $compactLink;
            } else if ($var == '$custom_user_signature$') {
                $keys_values['$custom_user_signature$'] = $current_user->signature;
            }
        }
        if (!empty($keys_values)) {
            $record->set('content', $quotingTool->mergeCustomTokens($record->get('content'), $keys_values));
            $full_content = base64_encode($record->get('content'));
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $module, $entityId,
                null, null, $full_content, $record->get('description'));
        }

        $dataEntityId = Vtiger_Record_Model::getInstanceById($entityId);
        $agentid = $dataEntityId->get('agentid');

        // Template setting
        $settingRecordModel = new QuotingTool_SettingRecord_Model();
        $templateSetting = $settingRecordModel->findByTemplateId($record->getId());
        $pageFormat = $templateSetting->get('page_format');
        $settingPageFormat = 'A4';

        if ($pageFormat) {
            $pageFormat = json_decode(html_entity_decode($pageFormat), true);

            if ($pageFormat['name'] == 'landscape') {
                $settingPageFormat = 'A4-L';
            }
        }

        // Create PDF
        $pdf = $quotingTool->createPdf($record->get('content'), $record->get('header'), $record->get('footer'), "{$attachmentId}_{$fileName}",
            $settingPageFormat);

        // Create Document Record
        $sourceRecordId = $entityId;
        $sourceModule = $request->get('relmodule');
        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);

        $relatedRecordModel = $this->createDocumentRecord($fileName,$agentid, $attachmentId);
        $relatedModuleModel = Vtiger_Module_Model::getInstance('Documents');

        /** @var Vtiger_Relation_Model $relationModel */
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        $relationModel->addRelation($sourceRecordId, $relatedRecordModel->getId());

        $fileContent = '';

        if (is_readable($pdf)) {
            $fileContent = file_get_contents($pdf);
        }

        header("Content-type: application/pdf");
        header("Pragma: public");
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=".html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset')));
        header("Content-Description: PHP Generated Data");

        echo $fileContent;
    }

    /**
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
//    public function send_email(Vtiger_Request $request)
//    {
//        global $current_user, $site_URL;
//
//        $moduleName = $request->getModule();
//        $response = new Vtiger_Response();
//        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
//
//        $data = array();
//        $toEmail = null;
//        $id = null;
//        $recordId = $request->get('record');
//        $module = $request->get('module');
//        $relModule = $request->get('relmodule');
//        $templateId = $request->get('template_id');
//        $signature = null;
//        $signatureName = null;
//        $description = $request->get('description');
//        $selectedEmail = $request->get('selectedEmail');
//        $quotingToolRecordModel = new QuotingTool_Record_Model();
//        $templateRecord = $quotingToolRecordModel->getById($templateId);
//
//        if (!$templateRecord) {
//            $response->setError(200, vtranslate('LBL_NOT_FOUND', $module));
//            $response->emit();
//            exit;
//        }
//
//        // Invalid email template
//        if (!$templateRecord->get('email_subject') || !$templateRecord->get('email_content')) {
//            $response->setError(200, vtranslate('LBL_INVALID_EMAIL_TEMPLATE', $module));
//            $response->emit();
//            exit;
//        }
//
//        list($no, $email_record, $toEmail) = explode("||", $selectedEmail);
//        $emailRecordModel = Vtiger_Record_Model::getInstanceById($email_record);
//        $signatureName = $emailRecordModel->getDisplayName();
//
//        if (!$toEmail) {
//            $response->setError(200, vtranslate('LBL_INVALID_EMAIL', $module));
//            $response->emit();
//            exit;
//        }
//
//        $quotingTool = new QuotingTool();
//        // Content
//        $content = $templateRecord->get('content');
//        $content = $content ? base64_decode($content) : '';
//        $varContent = $quotingTool->getVarFromString($content);
//        if (!empty($varContent)) {
//            $content = $quotingTool->parseTokens($content, $relModule, $recordId);
//        }
//
//        // Encode before put to database
//        $full_content = base64_encode($content);
//
//        /** @var QuotingTool_Record_Model $model */
//        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
//        $saveId = $transactionRecordModel->saveTransaction($id, $templateId, $relModule, $recordId, $signature, $signatureName, $full_content, $description);
//
//        $site = rtrim($site_URL, '/');
//        $link = "{$site}/modules/{$moduleName}/proposal/index.php?record={$saveId}";
//        $compactLink = preg_replace("(^(https?|ftp)://)", "", $link);
//        $keys_values = array();
//
//        foreach ($varContent as $var) {
//            if ($var == '$custom_proposal_link$') {
//                $keys_values['$custom_proposal_link$'] = $compactLink;
//            } else if ($var == '$custom_user_signature$') {
//                $keys_values['$custom_user_signature$'] = $current_user->signature;
//            }
//        }
//        if (!empty($keys_values)) {
//            $content = $quotingTool->mergeCustomTokens($content, $keys_values);
//            $full_content = base64_encode($content);
//            $saveId = $transactionRecordModel->saveTransaction($saveId, $templateId, $relModule, $recordId, $signature, $signatureName, $full_content, $description);
//        }
//
//        // Email subject
//        $emailSubject = base64_decode($templateRecord->get('email_subject'));
//        $varEmailSubject = $quotingTool->getVarFromString($emailSubject);
//        if (!empty($varEmailSubject)) {
//            $emailSubject = $quotingTool->parseTokens($emailSubject, $relModule, $recordId);
//            $keys_values = array();
//
//            foreach ($varEmailSubject as $var) {
//                if ($var == '$custom_proposal_link$') {
//                    $keys_values['$custom_proposal_link$'] = $compactLink;
//                } else if ($var == '$custom_user_signature$') {
//                    $keys_values['$custom_user_signature$'] = $current_user->signature;
//                }
//            }
//            if (!empty($keys_values)) {
//                $emailSubject = $quotingTool->mergeCustomTokens($emailSubject, $keys_values);
//            }
//        }
//
//        // Email content
//        $emailContent = base64_decode($templateRecord->get('email_content'));
//        $varEmailContent = $quotingTool->getVarFromString($emailContent);
//        if (!empty($varEmailContent)) {
//            $emailContent = $quotingTool->parseTokens($emailContent, $relModule, $recordId);
//            $keys_values = array();
//
//            foreach ($varEmailContent as $var) {
//                if ($var == '$custom_proposal_link$') {
//                    $keys_values['$custom_proposal_link$'] = $compactLink;
//                } else if ($var == '$custom_user_signature$') {
//                    $keys_values['$custom_user_signature$'] = $current_user->signature;
//                }
//            }
//            if (!empty($keys_values)) {
//                $emailContent = $quotingTool->mergeCustomTokens($emailContent, $keys_values);
//            }
//        }
//
//        $fromName = $current_user->first_name . ' ' . $current_user->last_name;
//        $fromEmail = null;
//
//        if ($current_user->email1) {
//            $fromEmail = $current_user->email1;
//        } else if ($current_user->email2) {
//            $fromEmail = $current_user->email2;
//        } else if ($current_user->secondaryemail) {
//            $fromEmail = $current_user->secondaryemail;
//        }
//
//        if ($fromEmail) {
//            $fromName = "{$fromName} ({$fromEmail})";
//        }
//
//        $result = send_mail($module, $toEmail, $fromName, $fromEmail, $emailSubject, $emailContent);
//
//        if ($result != 1) {
//            $errorMessage = vtranslate('ERROR_UNABLE_TO_SEND_EMAIL', $module);
//            $response->setError(200, $errorMessage);
//            $response->emit();
//            exit;
//        }
//
//        // Success
//        $data['message'] = vtranslate('LBL_EMAIL_SENT', $module);
//        $response->setResult($data);
//        $response->emit();
//    }

    /**
     * Fn - downloadPreview
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function preview_and_send_email(Vtiger_Request $request)
    {
        global $current_user, $site_URL, $application_unique_key;

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $data = array();

        $quotingTool = new QuotingTool();
        $moduleName = $request->getModule();

        // CC email process
        $strCcEmails = $request->get('ccValues');
        if ($strCcEmails === null) {
            $strCcEmails = '';
        }
        $arrCcEmails = explode(',', trim($strCcEmails));
        $ccEmails = array();
        foreach ($arrCcEmails as $cc) {
            $ccEmails[] = $quotingTool->getEmailFromString($cc);
        }

        // BCC email process
        $strBccEmails = $request->get('bccValues');

        if ($strBccEmails === null) {
            $strBccEmails = '';
        }
        $arrBccEmails = explode(',', trim($strBccEmails));
        $bccEmails = array();
        foreach ($arrBccEmails as $bcc) {
            $bccEmails[] = $quotingTool->getEmailFromString($bcc);
        }

        // Invalid email template
        if (!$request->get('email_subject') || !$request->get('email_content')) {
            $response->setError(200, vtranslate('LBL_INVALID_EMAIL_TEMPLATE', $moduleName));
            $response->emit();
            exit;
        }

        $emails = explode(",", $request->get("toEmail"));

        if (empty($emails)) {
            $response->setError(200, vtranslate('LBL_INVALID_EMAIL', $moduleName));
            $response->emit();
            exit;
        }

        $emailSubject = base64_decode($request->get('email_subject'));
        $emailContent = base64_decode($request->get('email_content'));
        $fromName = $current_user->first_name . ' ' . $current_user->last_name;
        $fromEmail = null;

        if ($current_user->email1) {
            $fromEmail = $current_user->email1;
        } else if ($current_user->email2) {
            $fromEmail = $current_user->email2;
        } else if ($current_user->secondaryemail) {
            $fromEmail = $current_user->secondaryemail;
        }

        if ($fromEmail) {
            $fromName = "{$fromName} ({$fromEmail})";
        }

        $counter = 0;
        $emails = array_unique($emails);

        // Attach document
        $check_attach_file = $request->get('check_attach_file') == 'on';
        $relatedRecordModel = null;

        foreach ($emails as $email) {
            //Storing the details of emails
            $entityId = $request->get('record');
            $cc = implode(',', $ccEmails);
            $bcc = implode(',', $bccEmails);

            $emailModuleName = 'Emails';
            $userId = $current_user->id;
            /** @var Emails $emailFocus */
            $emailFocus = CRMEntity::getInstance($emailModuleName);
            $emailFieldValues = array(
                'assigned_user_id' => $userId,
                'subject' => $emailSubject,
                'description' => $emailContent,
                'from_email' => $fromEmail,
                'saved_toid' => $email,
                'ccmail' => $cc,
                'bccmail' => $bcc,
                'parent_id' => $entityId . "@$userId|",
                'email_flag' => 'SENT',
                'activitytype' => $emailModuleName,
                'date_start' => date('Y-m-d'),
                'time_start' => date('H:i:s'),
                'mode' => '',
                'id' => ''
            );
            $emailFocus->column_fields = $emailFieldValues;
            $emailFocus->save($emailModuleName);

            //Including email tracking details
            $emailId = $emailFocus->id;

            if ($emailId) {
                $trackURL = "$site_URL/modules/Emails/actions/TrackAccess.php?record=$entityId&mailid=$emailId&app_key=$application_unique_key";
                $emailContent = "<img src='$trackURL' alt='' width='1' height='1'>$emailContent";

                $logo = 0;
                if (stripos($emailContent, '<img src="cid:logo" />')) {
                    $logo = 1;
                }

                $transactionId = $request->get('transaction_id');
                $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
                /** @var Vtiger_Record_Model $transactionRecord */
                $transactionRecord = $transactionRecordModel->findById($transactionId);

                $templateId = $transactionRecord->get('template_id');
                $recordModel = new QuotingTool_Record_Model();
                /** @var QuotingTool_Record_Model $record */
                $record = $recordModel->getById($templateId);

                // Create PDF file
                $fileName = $quotingTool->makeUniqueFile($record->get('filename'));
                $attachmentId = $quotingTool->createAttachFile($emailFocus, $fileName);
                $fileName = $attachmentId . '_' . $fileName;

                // Get pdf content in form
                $pdfContent = base64_decode($request->get('pdf_content'));

                // Template setting
                $settingRecordModel = new QuotingTool_SettingRecord_Model();
                $templateSetting = $settingRecordModel->findByTemplateId($record->getId());
                $pageFormat = $templateSetting->get('page_format');
                $settingPageFormat = 'A4';

                if ($pageFormat) {
                    $pageFormat = json_decode(html_entity_decode($pageFormat), true);

                    if ($pageFormat['name'] == 'landscape') {
                        $settingPageFormat = 'A4-L';
                    }
                }

                // Create PDF
                $pdf = $quotingTool->createPdf($pdfContent, $record->get('header'), $record->get('footer'), $fileName,
                    $settingPageFormat);

                $attachment = $check_attach_file ? 'all' : false;

                $result = send_mail($moduleName, $email, $fromName, $fromEmail, $emailSubject, $emailContent, $cc, $bcc, $attachment, $emailId, $logo);
                $emailFocus->setEmailAccessCountValue($emailId);

                // Create Document Record
                $dataEntityId = Vtiger_Record_Model::getInstanceById($entityId);
                $agentid = $dataEntityId->get('agentid');

                $sourceRecordId = $entityId;

                $sourceModule = $request->get('relmodule');
                $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);

                if (!$relatedRecordModel) {
                    // Create new document attachment if not exist
                    $relatedRecordModel = $this->createDocumentRecord($fileName, $agentid, $attachmentId);
                }

                $relatedModuleModel = Vtiger_Module_Model::getInstance('Documents');

                /** @var Vtiger_Relation_Model $relationModel */
                $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
                $relationModel->addRelation($sourceRecordId, $relatedRecordModel->getId());

                if (!$result) {
                    //If mail is not sent then removing the details about email
                    $emailFocus->trash($emailModuleName, $emailId);
                } else {
                    $counter += $result;
                }
            }
        }

        if (!$counter) {
            $errorMessage = vtranslate('ERROR_UNABLE_TO_SEND_EMAIL', $moduleName);
            $response->setError(200, $errorMessage);
            $response->emit();
            exit;
        }

        // Success
        $data['message'] = vtranslate('LBL_EMAIL_SENT', $moduleName);
        $data['total'] = $counter;
        $response->setResult($data);
        $response->emit();
    }

    /**
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function preview_and_edit_pdf(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $templateId = $request->get('template_id');
        // Fix bug when request pdf content (it removed input tag)
        $pdfContent = $_REQUEST['pdf_content']; // $request->get('pdf_content')
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($templateId);

        if (!$record) {
            echo vtranslate('LBL_NOT_FOUND', $moduleName);
            exit;
        }

        $quotingTool = new QuotingTool();
        $record = $record->decompileRecord(0, array('header', 'footer'));
        // File name
        $fileName = $quotingTool->makeUniqueFile($record->get('filename'));

        // Template setting
        $settingRecordModel = new QuotingTool_SettingRecord_Model();
        $templateSetting = $settingRecordModel->findByTemplateId($record->getId());
        $pageFormat = $templateSetting->get('page_format');
        $settingPageFormat = 'A4';

        if ($pageFormat) {
            $pageFormat = json_decode(html_entity_decode($pageFormat), true);

            if ($pageFormat['name'] == 'landscape') {
                $settingPageFormat = 'A4-L';
            }
        }

        // Create PDF
        $pdf = $quotingTool->createPdf($pdfContent, $record->get('header'), $record->get('footer'), $fileName,
            $settingPageFormat);

        // Download the file
        $fileContent = '';

        if (is_readable($pdf)) {
            $fileContent = file_get_contents($pdf);
        }

        header("Content-type: ". mime_content_type($pdf));
        header("Pragma: public");
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=".html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset')));
        header("Content-Description: PHP Generated Data");

        echo $fileContent;
    }

    /**
     * Fn - downloadPreview
     * Save PDF content to the file
     *
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function download(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($recordId);

        if (!$record) {
            echo vtranslate('LBL_NOT_FOUND', $moduleName);
            exit;
        }

        $quotingTool = new QuotingTool();
        $record = $record->decompileRecord(0, array('header', 'content', 'footer'));
        // File name
        $fileName = $quotingTool->makeUniqueFile($record->get('filename'));

        // Template setting
        $settingRecordModel = new QuotingTool_SettingRecord_Model();
        $templateSetting = $settingRecordModel->findByTemplateId($record->getId());
        $pageFormat = $templateSetting->get('page_format');
        $settingPageFormat = 'A4';

        if ($pageFormat) {
            $pageFormat = json_decode(html_entity_decode($pageFormat), true);

            if ($pageFormat['name'] == 'landscape') {
                $settingPageFormat = 'A4-L';
            }
        }

        // Create PDF
        $pdf = $quotingTool->createPdf($record->get('content'), $record->get('header'), $record->get('footer'), $fileName,
            $settingPageFormat);

        // Download the file
        $fileContent = '';

        if (is_readable($pdf)) {
            $fileContent = file_get_contents($pdf);
        }

        header("Content-type: ". mime_content_type($pdf));
        header("Pragma: public");
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=".html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset')));
        header("Content-Description: PHP Generated Data");

        echo $fileContent;
    }

    /**
     * @link http://www.mpdf1.com/forum/discussion/36/how-to-automatically-save-pdf-file/p1
     * @param Vtiger_Request $request
     * @throws Exception
     */
//    public function download_with_signature(Vtiger_Request $request)
//    {
//        $moduleName = $request->getModule();
//        $record = $request->get('record');
//        $templateId = $request->get('template_id');
//        $quotingToolRecordModel = new QuotingTool_Record_Model();
//        $templateRecord = $quotingToolRecordModel->getById($templateId);
//
//        if (!$templateRecord) {
//            echo vtranslate('LBL_NOT_FOUND', $moduleName);
//            exit;
//        }
//
//        $quotingTool = new QuotingTool();
//        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
//        $transactionRecord = $transactionRecordModel->getLastTransactionByModule($request->get('relmodule'), $record);
//        $module = $templateRecord->get('module');
//        // Name
//        $filename = $templateRecord->get('filename');
//        // Header
//        $header = $templateRecord->get('header');
//        $header = $header ? base64_decode($header) : '';
//        // Content
//        $content = ($transactionRecord && $transactionRecord->get('full_content')) ?
//            $transactionRecord->get('full_content') : $templateRecord->get('content');
//        $content = $content ? base64_decode($content) : '';
//        // Parse tokens
//        $tokens = $quotingTool->getFieldTokenFromString($content);
//        // Parse content
//        $content = $quotingTool->mergeBlockTokens($tokens, $record, $content);
//        $content = $quotingTool->mergeTokens($tokens, $record, $content, $module);
//        $content = $quotingTool->mergeCustomFunctions($content);
//        // Footer
//        $footer = $templateRecord->get('footer');
//        $footer = $footer ? base64_decode($footer) : '';
//        // File name
//        $fileName = $quotingTool->makeUniqueFile($filename);
//        // Create PDF
//        $pdf = $quotingTool->createPdf($content, $header, $footer, $fileName);
//
//        // Download the file
//        $fileContent = '';
//        
//        if (is_readable($pdf)) {
//        $fileContent = file_get_contents($pdf);
//        }
//        
//        header("Content-type: ". mime_content_type($pdf));
//        header("Pragma: public");
//        header("Cache-Control: private");
//        header("Content-Disposition: attachment; filename=".html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset')));
//        header("Content-Description: PHP Generated Data");
//        
//        echo $fileContent;
//    }

    /**
     * @param Vtiger_Request $request
     */
    public function duplicate(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $params = array();

        /** @var QuotingTool_Record_Model $recordModel */
        $recordModel = QuotingTool_Record_Model::getCleanInstance($module);
        $recordId = $request->get('record');
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($recordId);

        if (!$record) {
            return;
        }

        $data = $record->getData();
        $allow = array('filename', 'module', 'body', 'header', 'content', 'footer', 'description', 'deleted', 'email_subject',
            'email_content', 'mapping_fields', 'attachments');

        if ($data && !empty($data)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $allow, true)) {
                    continue;
                }

                if ($key == 'filename') {
                    // Add suffix to file name
                    $value = $value . '_' . vtranslate('LBL_COPY', $module);
                } else if (($key == 'mapping_fields' || $key == 'attachments') && $value) {
                    // decode html entity when encode json string
                    $value = html_entity_decode($value);
                }

                $params[$key] = $value;
            }
        }

        // Save data
        $template = $recordModel->save(null, $params);
        $id = $template->getId();

        if (!$id) {
            // When error
            return;
        }

        // Save history
        $historyRecordModel = new QuotingTool_HistoryRecord_Model();
        $historyParams = array(
            'body' => $template->get('body')
        );
        $historyRecordModel->saveByTemplate($id, $historyParams);

        header("Location: index.php?module={$module}&view=List");
    }

    // Create Document Record
    public function createDocumentRecord($filename, $agentid, $attachmentId, $fileType = 'application/pdf')
    {
        global $current_user, $adb;
        $recordModel = Vtiger_Record_Model::getCleanInstance("Documents");
        $recordModel->set('notes_title', $filename);
        $recordModel->set('filename', $filename);
        $recordModel->set('filetype', $fileType);
        $recordModel->set('assigned_user_id', $current_user->id);
        $recordModel->set('agentid', $agentid);
        $recordModel->set('folderid', 1);
        $recordModel->set('filelocationtype', 'I');
        $recordModel->set('filestatus', 1);
        $recordModel->save();
        $sql3 = 'INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)';
        $adb->pquery($sql3, array($recordModel->getId(), $attachmentId));
        return $recordModel;
    }
}
