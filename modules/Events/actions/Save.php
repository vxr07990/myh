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

class Events_Save_Action extends Calendar_Save_Action
{

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
    public function saveRecord($request)
    {
        $adb = PearDatabase::getInstance();
        $recordModel = $this->getRecordModelFromRequest($request);
        global $current_user;
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $fieldModelList = $moduleModel->getFields();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $uitype = $fieldModel->get('uitype');
            $fldvalue = $recordModel->get($fieldName);
            $typeofdata = $fieldModel->get('typeofdata');
            $typeofdataPieces = explode('~', $typeofdata);
            if ($uitype == 5 || $uitype == 6 || $uitype == 23 || $uitype == 555) {
                if(count($typeofdataPieces) > 3 && $typeofdataPieces[0] == 'DT' && $typeofdataPieces[2] == 'REL') {
                    $timeField = $typeofdataPieces[3];
                } elseif ($fieldName == 'date_start') {
                    $timeField = 'time_start';
                } elseif ($fieldName == 'due_date') {
                    $timeField = 'time_end';
                }

                $timeZone = $_REQUEST['timefield_'.$timeField];
                if(!$timeZone) {
                    $timeZone = $current_user->time_zone;
                }

                $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $fldvalue.' '.$recordModel->get($timeField), $timeZone);
                $carbonTime->setTimezone(DateTimeField::getDBTimeZone());
                $fldvalue = $carbonTime->format('Y-m-d');

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
                        $timeVal = DateTimeField::convertTimeZone($timeVal, $timeZone, $current_user->time_zone);
                        $timeVal = $timeVal->format("H:i:s");
                    }
                    $fldvalue = getValidDBInsertDateTimeValue($dateVal." ".$timeVal);
                    list($dateVal, $timeVal) = explode(" ", $fldvalue);
                    $fldvalue = $timeVal;
                }
            }
            $recordModel->set($fieldName, $fldvalue);
        }
        $recordModel->save();
        $originalRecordId = $recordModel->getId();
        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            if ($relatedModule->getName() == 'Events') {
                $relatedModule = Vtiger_Module_Model::getInstance('Calendar');
            }
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }

        // Handled to save follow up event
        $followupMode = $request->get('followup');

        //Start Date and Time values
        $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('followup_time_start'));
        $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($request->get('followup_date_start') . " " . $startTime);
        list($startDate, $startTime) = explode(' ', $startDateTime);

        $subject = $request->get('subject');
        if ($followupMode == 'on' && $startTime != '' && $startDate != '') {
            $recordModel->set('eventstatus', 'Planned');
            $recordModel->set('subject', '[Followup] '.$subject);
            $recordModel->set('date_start', $startDate);
            $recordModel->set('time_start', $startTime);

            $currentUser = Users_Record_Model::getCurrentUserModel();
            $activityType = $recordModel->get('activitytype');
            if ($activityType == 'Call') {
                $minutes = $currentUser->get('callduration');
            } else {
                $minutes = $currentUser->get('othereventduration');
            }
            $dueDateTime = date('Y-m-d H:i:s', strtotime("$startDateTime+$minutes minutes"));
            list($startDate, $startTime) = explode(' ', $dueDateTime);

            $recordModel->set('due_date', $startDate);
            $recordModel->set('time_end', $startTime);
            $recordModel->set('recurringtype', '');
            $recordModel->set('mode', 'create');
            $recordModel->save();
            $heldevent = true;
        }

        //TODO: remove the dependency on $_REQUEST
        if ($_REQUEST['recurringtype'] != '' && $_REQUEST['recurringtype'] != '--None--') {
            vimport('~~/modules/Calendar/RepeatEvents.php');
            $focus =  new Activity();

            //get all the stored data to this object
            $focus->column_fields = $recordModel->getData();

            Calendar_RepeatEvents::repeatFromRequest($focus);
        }
        return $recordModel;
    }


    /**
     * Function to get the record model based on the request parameters
     * @param Vtiger_Request $request
     * @return Vtiger_Record_Model or Module specific Record Model instance
     */
    protected function getRecordModelFromRequest(Vtiger_Request $request)
    {
        $recordModel = parent::getRecordModelFromRequest($request);

        $recordModel->set('selectedusers', $request->get('selectedusers'));
        return $recordModel;
    }
}
