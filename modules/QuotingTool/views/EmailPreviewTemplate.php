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
 * Class QuotingTool_EmailPreviewTemplate_View
 */
class QuotingTool_EmailPreviewTemplate_View extends Vtiger_IndexAjax_View
{

    /**
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request)
    {
        global $site_URL, $current_user;

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);

        $recordId = $request->get('record');
        $templateId = $request->get('template_id');
        $recordModel = new QuotingTool_Record_Model();
        /** @var QuotingTool_Record_Model $record */
        $record = $recordModel->getById($templateId);
        $relModule = $record->get('module');
        $quotingTool = new QuotingTool();
        $record = $record->decompileRecord($recordId, array('content', 'header', 'footer', 'email_subject', 'email_content'));

        /** @var QuotingTool_Record_Model $model */
        $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
        // Encode before put to database
        $full_content = base64_encode($record->get('content'));
        $transactionId = $transactionRecordModel->saveTransaction(0, $templateId, $record->get('module'), $recordId, null, null, $full_content, $record->get('description'));

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
            $transactionId = $transactionRecordModel->saveTransaction($transactionId, $templateId, $relModule, $recordId, null, null, $full_content, $record->get('description'));
        }

        // Email subject
        $varEmailSubject = $quotingTool->getVarFromString($record->get('email_subject'));
        if (!empty($varEmailSubject)) {
            $keys_values = array();

            foreach ($varEmailSubject as $var) {
                if ($var == '$custom_proposal_link$') {
                    $keys_values['$custom_proposal_link$'] = $compactLink;
                } else if ($var == '$custom_user_signature$') {
                    $keys_values['$custom_user_signature$'] = $current_user->signature;
                }
            }
            if (!empty($keys_values)) {
                $record->set('email_subject', $quotingTool->mergeCustomTokens($record->get('email_subject'), $keys_values));
            }
        }

        // Email content
        $varEmailContent = $quotingTool->getVarFromString($record->get('email_content'));
        if (!empty($varEmailContent)) {
            $keys_values = array();

            foreach ($varEmailContent as $var) {
                if ($var == '$custom_proposal_link$') {
                    $keys_values['$custom_proposal_link$'] = $compactLink;
                } else if ($var == '$custom_user_signature$') {
                    $keys_values['$custom_user_signature$'] = $current_user->signature;
                }
            }
            if (!empty($keys_values)) {
                $record->set('email_content', $quotingTool->mergeCustomTokens($record->get('email_content'), $keys_values));
            }
        }

        // Get to email
        $to              = [];
        $toMailInfo      = [];
        $toMailNamesList = [];
        $relModuleModel=Vtiger_Module_Model::getInstance($relModule);
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $relModule);
        $emailFields = $relModuleModel->getFieldsByType('email');
        $emailFields = array_keys($emailFields);
        if ($emailFields) {
            foreach ($emailFields as $field) {
                $value            = $recordModel->get($field);
                $emailOptOutValue = $recordModel->get('emailoptout');
                if (!empty($value) && (!$emailOptOutValue)) {
                    $to[]                   = $value;
                    $toMailInfo[$recordId][]      = $value;
                    $toMailNamesList[$recordId][] = ['label' => $recordModel->getName(), 'value' => $value];
                }
            }
        }

        $emailModuleModel     = Vtiger_Module_Model::getInstance("Emails");
        $viewer->assign('TO', $to);
        $viewer->assign('TOMAIL_INFO', $toMailInfo);
        $viewer->assign('TOMAIL_NAMES_LIST', $toMailNamesList);
        $viewer->assign('EMAIL_RELATED_MODULES', $emailModuleModel->getEmailRelatedModules());
        // Email list
        $email_field_list = $quotingTool->getEmailList($relModule, $recordId);

        // Get settings
        $settingRecordModel = new QuotingTool_SettingRecord_Model();
        $settingRecord = $settingRecordModel->findByTemplateId($templateId);

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RECORDID', $recordId);
        $viewer->assign('SELECTED_IDS', array($recordId));
        $viewer->assign('TEMPLATEID', $templateId);
        $viewer->assign('EMAIL_FIELD_LIST', $email_field_list);
        $viewer->assign('EMAIL_SUBJECT', $record->get('email_subject'));
        $viewer->assign('EMAIL_CONTENT', $record->get('email_content'));
        $viewer->assign('CUSTOM_PROPOSAL_LINK', $link);
        $viewer->assign('TRANSACTION_ID', $transactionId);
        $viewer->assign('RELATED_MODULE', $request->get('relmodule'));
        $viewer->assign('PDF_CONTENT', $record->get('content'));
        $viewer->assign('SETTINGS', $settingRecord);

        echo $viewer->view('EmailPreviewTemplate.tpl', $moduleName, true);
    }

}
