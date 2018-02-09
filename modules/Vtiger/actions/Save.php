<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

use Carbon\Carbon;
class Vtiger_Save_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        if (!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $recordModel = $this->saveRecord($request);
        $pseudo = $request->get('pseudoSave') == '1';
        $reportSave = $request->get('reportSave') == '1';

        //we are doubling this up because some things MIGHT have their own saveRecord.
        $recordId = $recordModel->getId();
        $request->set('record', $recordId);

        if (($request->get('relationOperation') && $request->get('sourceModule') == 'Opportunities' && $recordModel->getModule()->getName() == 'Orders') || $recordModel->getModule()->getName() == 'Cubesheets') {
            $loadUrl = $recordModel->getDetailViewUrl();
        } elseif ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentRecordId = $request->get('sourceRecord');
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
            //TODO : Url should load the related list instead of detail view of record
            $detailViewLinkParams = array('MODULE'=>$parentModuleName,'RECORD'=>$parentRecordId);
            $detailViewModel = Vtiger_DetailView_Model::getInstance($parentModuleName, $parentRecordId);
            $detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);
            $currentModuleName = $request->get('module');
            $relatedLinks = $detailViewLinks['DETAILVIEWRELATED'];
            $loadUrl = null;
            foreach ($relatedLinks as $relatedLink) {
                if ($relatedLink->relatedModuleName == $currentModuleName) {
                    $loadUrl = $relatedLink->linkurl;
                    $tabLabel = $relatedLink->linklabel;
                    $loadUrl = "index.php?".$loadUrl."&tab_label=".$tabLabel;
                    //file_put_contents('logs/devLog.log', "\n LINK: ".$loadUrl, FILE_APPEND);
                }
            }
            if (empty($loadUrl)) {
                $loadUrl = $parentRecordModel->getDetailViewUrl();
            }
        } elseif ($request->get('returnToList')) {
            $loadUrl = $recordModel->getModule()->getListViewUrl();
        } else {
            $loadUrl = $recordModel->getDetailViewUrl();
        }
        if (!$pseudo && !$reportSave) {
            header("Location: $loadUrl");
        }
    }

    /**
     * Function to convert the survey_date survey_time values
     * @param <Vtiger_Request> $request - values of the record
     */
    public function convertSurveyDateTime($request)
    {
        $surveyInput = $request->get('survey_time');
        if ($surveyInput) {
            $surveyTime = DateTimeField::convertToDBTimeZone(Vtiger_Time_UIType::getTimeValueWithSeconds($surveyInput))->format('H:i:s');
            $request->set('survey_time', $surveyTime);
            $surveyInput = date('H:i:s', strtotime($surveyInput));
        } else {
            $surveyInput = '';
        }

        $dateTimeInput = $request->get('survey_date');
        if ($dateTimeInput) {
            $datetime = DateTimeField::convertToDBTimeZone($dateTimeInput.' '.$surveyInput);
            $request->set('survey_date', $datetime->format('Y-m-d'));
        }
    }

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
    public function saveRecord($request)
    {
        $newRecord = true;
        if($request->get('record')){
            $newRecord = false;
        }
        // file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Entering Vtiger_Save::saveRecord\n", FILE_APPEND);
        // file_put_contents('logs/SaveLog.log', date("Y-m-d H:i:s")." - ".print_r($request, true)."\n", FILE_APPEND);
        $recordModel = $this->getRecordModelFromRequest($request);
        global $current_user;
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $fieldModelList = $moduleModel->getFields();
        $originalValues = [];
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $uitype = $fieldModel->get('uitype');
            $typeofdata = $fieldModel->get('typeofdata');
            $typeofdataPieces = explode('~', $typeofdata);
            $fldvalue = $recordModel->get($fieldName);
            $originalValues[$fieldName] = $fldvalue;
            if ($uitype == 5 || $uitype == 6 || $uitype == 23 || $uitype == 555) {
                if($fldvalue != ''){
                    $timeField = '';
                    if(count($typeofdataPieces) > 3 && $typeofdataPieces[0] == 'DT' && $typeofdataPieces[2] == 'REL') {
                        $timeField = $typeofdataPieces[3];
                    } elseif ($fieldName == 'date_start') {
                        $timeField = 'time_start';
                    } elseif ($fieldName == 'due_date' && $moduleName == 'Events') {
                        $timeField = 'time_end';
                    }
                    $timeZone = $_REQUEST['timefield_'.$timeField];
                    if(!$timeZone) {
                        $timeZone = $current_user->time_zone;
                    }
                    $datePieces = explode('-', $fldvalue);
                    if(strlen($datePieces[0]) != 4) {
                        $fldvalue = DateTimeField::convertToDBFormat($fldvalue);
                    }
                    if($timeField && !empty($recordModel->get($timeField))) {
                        $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $fldvalue . ' ' . ($originalValues[$timeField] ?: $recordModel->get($timeField)), $timeZone);
                    } else {
                        $carbonTime = Carbon::createFromFormat('Y-m-d', $fldvalue);
                    }
                    $carbonTime->setTimezone(DateTimeField::getDBTimeZone());
                    $fldvalue = $carbonTime->format('Y-m-d');
                }
                if (isset($current_user->date_format)) {
                    $fldvalue = getValidDBInsertDateValue($fldvalue);
                }
            } elseif ($uitype == 700) {
                if (isset($current_user->date_format)) {
                    list($dateVal,$timeVal, $pam) =explode(" ",$fldvalue);
                    $dateVal = getValidDBInsertDateValue($dateVal);
                    $timeVal = Vtiger_Time_UIType::getTimeValueWithSeconds($timeVal." $pam");
                    $fldvalue = getValidDBInsertDateTimeValue($dateVal." ".$timeVal);
                }
            } elseif ($uitype == 14 || $fieldName == 'time_end' || $fieldName == 'time_start') {
                if (isset($current_user->date_format)
                    // special handling is already in place for these fields
                    //&& $fieldName != 'survey_time' && $fieldName != 'survey_end_time'
                    /* && count($_REQUEST['time_fields'])>0*/
                ) {
                    $timeVal = $fldvalue;
                    if(count($typeofdataPieces) > 3 && $typeofdataPieces[0] == 'T' && $typeofdataPieces[2] == 'REL' && $fldvalue != '') {
                        $dateVal = $recordModel->get($typeofdataPieces[3]);
                    } else {
                        $dateVal = date('Y-m-d');
                    }
                    $timeVal = Vtiger_Time_UIType::getTimeValueWithSeconds($timeVal);
                    $timeFields = $_REQUEST['time_fields'];
                    foreach ($timeFields as $index => $field) {
                        $timefield = $_REQUEST[$field];
                        if (empty($timefield)) {
                            $_REQUEST['timefield_'.$field] = NULL;
                        }
                    }
                    $timeZone = $_REQUEST['timefield_'.$fieldName];
                    if ($timeZone && $current_user->time_zone != $timeZone) {
                        $timeVal = DateTimeField::convertTimeZone($dateVal.' '.$timeVal, $timeZone, $current_user->time_zone);
                        $timeVal = $timeVal->format("H:i:s");
                    }
                    $fldvalue = getValidDBInsertDateTimeValue($dateVal." ".$timeVal);
                    list($dateVal, $timeVal) = explode(" ", $fldvalue);
                    $fldvalue = $timeVal;
                }
            }
            $recordModel->set($fieldName, $fldvalue);
        }
        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Record Model Retrieved\n", FILE_APPEND);
        $request->get('pseudoSave') == '1' ? $recordModel->pseudoSave() : $recordModel->save();
        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."After save action\n", FILE_APPEND);
        $recordId = $recordModel->getId();
        $request->set('record', $recordId);
        if ($request->get('relationOperation') && !($request->get('sourceModule') == 'Opportunities' && $recordModel->getModuleName() == 'Orders')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();
            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            //file_put_contents('logs/devLog.log', "\n this thing here : ".print_r(new Vtiger_Relation_Model,true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n parentModuleModel : ".print_r($parentModuleModel,true)."\n relatedModule : ".print_r($relatedModule,true)."\n relationModel : ".print_r($relationModel,true),FILE_APPEND);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        if ($request->get('imgDeleted')) {
            $imageIds = $request->get('imageid');
            foreach ($imageIds as $imageId) {
                $status = $recordModel->deleteImage($imageId);
            }
        }
        $request->set('dupe_or_overflow', $newRecord);
        // Moved to CRMEntity save
        //$this->saveGuests($request);
        //file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Exiting Vtiger_Save::saveRecord\n", FILE_APPEND);
        return $recordModel;
    }

    /**
     * Function to get the record model based on the request parameters
     * @param Vtiger_Request $request
     * @return Vtiger_Record_Model or Module specific Record Model instance
     */
    protected function getRecordModelFromRequest(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        if (!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('id', $recordId);
            $recordModel->set('mode', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('mode', '');
        }

        $fieldModelList = $moduleModel->getFields();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $fieldValue = $request->get($fieldName, null);
            $fieldDataType = $fieldModel->getFieldDataType();
            if ($fieldDataType == 'time') {
                $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
            }
            if ($fieldValue !== null) {
                if (!is_array($fieldValue)) {
                    $fieldValue = trim($fieldValue);
                }
                $recordModel->set($fieldName, $fieldValue);
            }
        }
        return $recordModel;
    }

    public function validateRequest(Vtiger_Request $request)
    {
        return $request->validateWriteAccess();
    }

    //pulled from individual extensions.
    //@TODO: mild concerns about typing to null.  should fine in less than php7.
    protected function getRecordID(Vtiger_Request $request = null)
    {
        $recId = '';
        if ($request) {
            $recId = $request->get('record');
        }
        if (!$recId) {
            //@TODO: I agree this will likely have concurrency issues and won't be reliable. adding a log to see if it gets hit.
            file_put_contents('logs/devLog.log', "\n FAIL ERROR Figure out what did this, as it shouldn't have. (Save.php:". __LINE__ . "): DEBUG TRACE\n" . print_r(debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), TRUE), FILE_APPEND);
            //@TODO: this has faulty assumptions.  maybe... should be a fail condition.  #discuss
            $db     = PearDatabase::getInstance();
            $sql    = "SELECT id FROM `vtiger_crmentity_seq`";
            $params = [];
            $result = $db->pquery($sql, $params);
            $recId = $db->query_result($result, 0, 'id') + 1;
        }
        return $recId;
    }
}
