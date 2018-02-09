<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

chdir(dirname(__FILE__) . '/../../..');
require_once 'config.inc.php';
require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
require_once 'modules/Users/Users.php';
include('modules/QuotingTool/QuotingTool.php');

global $adb, $current_user;
$adb = PearDatabase::getInstance();
$current_user = new Users();
$activeAdmin = $current_user->getActiveAdminUser();
$current_user->retrieve_entity_info($activeAdmin->id, 'Users');

// Submit request
$action = (isset($_REQUEST['_action'])) ? $_REQUEST['_action'] : null;
if ($action) {
    switch ($action) {
        case 'submit':
            submit();
            break;
        case 'download_pdf':
            downloadPdf();
            break;
        case 'get_picklist_values':
            get_picklist_values();
            break;
        case 'get_currency_values':
            get_currency_values();
            break;
        case 'an_paid':
            an_paid();
            break;
        default:
            break;
    }
}

/**
 * Fn - submit
 */
function submit()
{
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
    $quotingTool = new QuotingTool();
    $record = $_REQUEST['record'];  // Transaction id
    $status = $_REQUEST['status'];  // Accept = 1; Decline = -1; Cancel: 0;
    $status_text = $_REQUEST['status_text'];
    $signature = $_REQUEST['signature'];
    $signatureName = $_REQUEST['signature_name'];
    $fullContent = $_REQUEST['content'];
    $description = $_REQUEST['description'];
    $customMappingFields = $_REQUEST['custom_mapping_fields'];
    $timestamp = time();

    $transactionRecordModel = new QuotingTool_TransactionRecord_Model();
    $success1 = $transactionRecordModel->updateSignature($record, $signature, $signatureName, $fullContent, $description);
    $success2 = $transactionRecordModel->changeStatus($record, $status);

    // Template
    /** @var Vtiger_Record_Model $transactionRecord */
    $transactionRecord = $transactionRecordModel->findById($record);

    if (!$transactionRecord) {
        $response->setError(200, vtranslate('LBL_INVALID_DOCUMENT', 'QuotingTool'));
        return $response->emit();
    }

//    $refModule = $transactionRecord->get('module');
    $refId = $transactionRecord->get('record_id');  // Module record id
    $recordModel = Vtiger_Record_Model::getInstanceById($refId);
    $quotingToolRecordModel = new QuotingTool_Record_Model();
    $templateRecord = $quotingToolRecordModel->getById($transactionRecord->get('template_id'));
    $mappingFields = array();
    $tempMappingFields = $templateRecord->get('mapping_fields');
    if ($tempMappingFields) {
        $mappingFields = json_decode(htmlspecialchars_decode($tempMappingFields));
    }

    // Mapping module
    if (count($mappingFields) > 0) {
        mappingData($refId, $mappingFields, $status);
    }

    // From custom mapping fields (in form)
    if ($customMappingFields) {
        $tmpCustomMappingFields = json_decode(htmlspecialchars_decode($customMappingFields));
        foreach ($tmpCustomMappingFields as $recordId => $fieldMapping) {
            $mappingFields2 = array();

            foreach ($fieldMapping as $fieldMappingId => $fieldMappingDetail) {
                $fieldMappingValue = $fieldMappingDetail->value;

                switch ($fieldMappingDetail->datatype) {
                    case 'date':
                        $fieldMappingValue = Vtiger_Date_UIType::getDBInsertedValue($fieldMappingValue);
                        break;
                    case 'time':
                        $fieldMappingValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldMappingValue);
                        break;
                    case 'currency':
                        $fieldMappingValue = CurrencyField::convertToDBFormat($fieldMappingValue);
                        break;
                    default:
                        break;
                }

                $objMappingField2 = array(
                    'selected-field' => $fieldMappingId,
                    'selected-value' => $fieldMappingValue,
                    'type' => 1 // Only update when accept proposal
                );
                $objMappingField2 = (object)$objMappingField2;

                $mappingFields2[] = $objMappingField2;
            }

            mappingData($recordId, $mappingFields2, $status);
        }
    }

    // Create PDF file
    $temFilename = $templateRecord->get('filename');
    $tempHeader = $templateRecord->get('header');
    $tempFooter = $templateRecord->get('footer');
    $pdfContent = $fullContent ? base64_decode($fullContent) : '';
    $pdfHeader = $tempHeader ? base64_decode($tempHeader) : '';
    $pdfFooter = $tempFooter ? base64_decode($tempFooter) : '';
    // File name
    $pdfName = $quotingTool->makeUniqueFile($temFilename);

    // Template setting
    $settingRecordModel = new QuotingTool_SettingRecord_Model();
    $templateSetting = $settingRecordModel->findByTemplateId($templateRecord->getId());
    $pageFormat = $templateSetting->get('page_format');
    $settingPageFormat = 'A4';

    if ($pageFormat) {
        $pageFormat = json_decode(html_entity_decode($pageFormat), true);

        if ($pageFormat['name'] == 'landscape') {
            $settingPageFormat = 'A4-L';
        }
    }

    // Create PDF
    $pdf = $quotingTool->createPdf($pdfContent, $pdfHeader, $pdfFooter, $pdfName, $settingPageFormat);

//    // Add new SignatureRecord
//    $signedRecordModel = Vtiger_Module_Model::getInstance($module_SignedRecord);
//    $relatedModules = $quotingTool->getRelatedModules($signedRecordModel);
//    /** @var Vtiger_Module_Model $refModuleModel */
//    $refModuleModel = $relatedModules[$refModule];
    $newSignedRecord = array(
        'signature' => $signature,
        'signature_name' => $signatureName,
        'signature_date' => date('Y-m-d', $timestamp),
        'cf_signature_time' => date('H:i:s', $timestamp),
        'filename' => $pdf,
        'signedrecord_status' => $status_text,
        'signedrecord_type' => SignedRecord_Record_Model::TYPE_SIGNED,
        'related_to' => $refId,
        'agentid' => $recordModel->get('agentid'),
        'assigned_user_id' => $recordModel->get('assigned_user_id'),
    );

    $signedrecordId = (isset($_REQUEST['signedrecord_id']) && $_REQUEST['signedrecord_id']) ? intval($_REQUEST['signedrecord_id']) : 0;
    if ($signedrecordId) {
        $newSignedRecord['signedrecord_type'] = SignedRecord_Record_Model::TYPE_SIGNED;
    }

    saveSignedRecord($signedrecordId, $newSignedRecord);

    return $response->emit();
}

/**
 * @param $refId
 * @param $tempMappingFields
 * @param $status
 */
function mappingData($refId, $tempMappingFields, $status)
{
    $mappingFields = array();
    foreach ($tempMappingFields as $k => $field) {
        $vField = Vtiger_Field_Model::getInstance($field->{'selected-field'});

        if (($field->type == $status) && $vField) {
            $mappingFields[$vField->get('name')] = $field->{'selected-value'};
        }
    }

    // Mapping module
    $mappingModel = Vtiger_Record_Model::getInstanceById($refId);
    $mappingModel->set('id', $refId);
    $mappingModel->set('mode', 'edit');
    foreach ($mappingFields as $field => $value) {
        $mappingModel->set($field, $value);
    }

    return $mappingModel->save();
}

/** Fn - createSignedRecord
 * @param int $id
 * @param array $data
 */
function saveSignedRecord($id, $data)
{
    $signedRecordModel = null;
    if ($id) {
        $signedRecordModel = Vtiger_Record_Model::getInstanceById($id);
        $signedRecordModel->set('id', $id);
        $signedRecordModel->set('mode', 'edit');
    } else {
        $signedRecordModel = Vtiger_Record_Model::getCleanInstance('SignedRecord');
    }

    foreach ($data as $field => $value) {
        $signedRecordModel->set($field, $value);
    }

    return $signedRecordModel->save();  // return Id
}

/**
 * Fn - downloadPdf
 */
function downloadPdf()
{
    ob_start();

    $quotingTool = new QuotingTool();
    $pageFormat = $_REQUEST['page_format'];
    $settingPageFormat = 'A4';

    if ($pageFormat) {
        $pageFormat = json_decode($pageFormat, true);

        if ($pageFormat['name'] == 'landscape') {
            $settingPageFormat = 'A4-L';
        }
    }
    $name = $_REQUEST['name'];
    $pdfContent = $_REQUEST['content'] ? base64_decode($_REQUEST['content']) : '';
    $pdfHeader = $_REQUEST['header'] ? base64_decode($_REQUEST['header']) : '';
    $pdfFooter = $_REQUEST['footer'] ? base64_decode($_REQUEST['footer']) : '';
    // File name
    $pdfName = $quotingTool->makeUniqueFile($name);
    // Create PDF
    $pdf = $quotingTool->createPdf($pdfContent, $pdfHeader, $pdfFooter, $pdfName, $settingPageFormat);

    // some statement that removes all printed/echoed items
    ob_end_clean();

    // Download the file
    $fileContent = '';

    if (is_readable($pdf)) {
        $fileContent = file_get_contents($pdf);
    }

    header("Content-type: ". mime_content_type($pdf));
    header("Pragma: public");
    header("Cache-Control: private");
    header("Content-Disposition: attachment; filename=".html_entity_decode($pdfName, ENT_QUOTES, vglobal('default_charset')));
    header("Content-Description: PHP Generated Data");

    echo $fileContent;
}

/**
 * Fn - get_picklist_values
 */
function get_picklist_values()
{
    $fieldModules = isset($_REQUEST['fields']) ? $_REQUEST['fields'] : null;
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
    $data = array();

    if (!$fieldModules) {
        $response->setResult($data);
        $response->emit();
    }

    foreach ($fieldModules as $moduleName => $fields) {
        $module = Vtiger_Module_Model::getInstance($moduleName);

        foreach ($fields as $fieldName => $fieldValue) {
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $module);
            if (!$fieldModel) {
                continue;
            }

            $data[$moduleName][$fieldName] = array();
            $datatype = $fieldModel->getFieldDataType();

            if ($datatype == 'picklist') {
                $data[$moduleName][$fieldName][''] = vtranslate('Select an Option');
            }

            $data[$moduleName][$fieldName] = array_merge($data[$moduleName][$fieldName], $fieldModel->getPicklistValues());
        }
    }

    $response->setResult($data);
    $response->emit();
}

/**
 * Fn - get_currency_values
 */
function get_currency_values()
{
    $fieldModules = isset($_REQUEST['fields']) ? $_REQUEST['fields'] : null;
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
    $data = array();

    if (!$fieldModules) {
        $response->setResult($data);
        $response->emit();
    }

    foreach ($fieldModules as $moduleName => $fields) {
        $module = Vtiger_Module_Model::getInstance($moduleName);

        foreach ($fields as $fieldName => $fieldValue) {
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $module);
            $data[$moduleName][$fieldName] = $fieldModel->getCurrencyList();

            foreach ($data[$moduleName][$fieldName] as $k => $cf) {
                $data[$moduleName][$fieldName][$k] = vtranslate($cf, $moduleName);
            }
        }
    }

    $response->setResult($data);
    $response->emit();
}

/**
 * Fn - submit payment to Authorize.Net
 */
function an_paid()
{
    require_once "modules/VTEPayments/libs/InvoiceWidget/QuotingTool.php";
    $response = new Vtiger_Response();
    $response->setEmitType(Vtiger_Response::$EMIT_JSON);

    $anQuotingTool = new ANQuotingTool();
    $paid_status = $anQuotingTool->ANPaid();

    $response->setResult($paid_status);
    $response->emit();
}
