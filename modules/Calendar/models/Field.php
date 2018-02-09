<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Calendar Field Model Class
 */
class Calendar_Field_Model extends Vtiger_Field_Model
{

    /**
     * Function returns special validator for fields
     * @return <Array>
     */
    public function getValidator()
    {
        $validator = array();
        $fieldName = $this->getName();

        //file_put_contents('logs/devLog.log', "\n I never get here do I? :\n", FILE_APPEND);
        //the answer is no apparently

        switch ($fieldName) {
            case 'due_date': $funcName = array('name' => 'greaterThanDependentField',
                                    'params' => array('date_start'));
                    array_push($validator, $funcName);
                    break;
            case 'eventstatus':    $funcName = array('name' => 'futureEventCannotBeHeld',
                                    'params' => array('date_start'));
                    array_push($validator, $funcName);
                    break;
            // NOTE: Letting user to add pre or post dated Event.
            /*case 'date_start' : $funcName = array('name'=>'greaterThanToday');
                    array_push($validator, $funcName);
                    break;*/
            default: $validator = parent::getValidator();
                    break;
        }
        return $validator;
    }

    /**
     * Function to get the Webservice Field data type
     * @return <String> Data type of the field
    */
    public function getFieldDataType()
    {
        //file_put_contents('logs/devLog.log', "\n getFieldDataType name: ".$this->getName(), FILE_APPEND);
        if ($this->getName() == 'date_start' || $this->getName() == 'due_date') {
            return 'datetime';
        }

        if ($this->get('uitype') == '30') {
            return 'reminder';
        } elseif ($this->getName() == 'recurringtype') {
            return 'recurrence';
        }
        $webserviceField = $this->getWebserviceFieldObject();
        return $webserviceField->getFieldDataType();
    }

    /**
     * Customize the display value for detail view.
     */
    public function getDisplayValue($value, $record = false, $recordInstance = false)
    {
        //file_put_contents('logs/devLog.log', "\n RecordInstance: ".print_r($recordInstance, true), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n VALUE: ".print_r($value, true), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n RECORD: $record", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n RecordInstance name: ".$this->getName(), FILE_APPEND);
        global $current_user;
        if ($recordInstance) {
            if ($this->getName() == 'date_start') {
                $time_start = $recordInstance->get('time_start');

                if(!$time_start){
                    $time_start = Vtiger_Record_Model::getInstanceById($recordInstance->getId())->get('time_start');
                }

                $timeZone=getFieldTimeZoneValue('time_start', $record);
                if(!$timeZone) {
                    $timeZone = $current_user->time_zone;
                }

                $timezoneOffsetDateTimeValue = $value .' '.$time_start;

                $date = DateTimeField::convertTimeZone($value.' '.$time_start, DateTimeField::getDBTimeZone(), $timeZone);
                $value = $date->format('Y-m-d');
                $time_start = $date->format("H:i:s");

                $dateTimeValue = $value . ' '. $time_start;
                $value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
                list($startDate, $startTime) = explode(' ', $value);

                /* $currentUser = Users_Record_Model::getCurrentUserModel();
                if($currentUser->get('hour_format') == '12'){
                    $startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);
                } */
                return $value . $this->getTimeZoneOffset($timeZone,$timezoneOffsetDateTimeValue);
            } elseif ($this->getName() == 'due_date') {
                //this logic ACTUALLY occurs in the events field.php
                //thanks core vtiger!
                $time_end = $recordInstance->get('time_end');
                if($time_end == NULL) {
                    $time_end = Vtiger_Record_Model::getInstanceById($recordInstance->getId())->get('time_end');
                }
                if(!empty($time_end)) {
                    $timeZone=getFieldTimeZoneValue('time_end', $record);

                    if(!$timeZone) {
                        $timeZone = $current_user->time_zone;
                    }

                    $date = DateTimeField::convertTimeZone($time_end, DateTimeField::getDBTimeZone(), $timeZone);
                    $time_end = $date->format("H:i:s");

                    $dateTimeValue = $value . ' '. $time_end;
                    $value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
                    list($endDate, $endTime) = explode(' ', $value);

                    $currentUser = Users_Record_Model::getCurrentUserModel();
                    /* if($currentUser->get('hour_format') == '12'){
                        $endTime = Vtiger_Time_UIType::getTimeValueInAMorPM($endTime);
                    } */

                    return $value . $this->getTimeZoneOffset($timeZone,$dateTimeValue);
                } else {
                    return $this->getUITypeModel()->getDisplayValue($value);
                }
            } elseif ($this->getName() == 'time_start' || $this->getName() == 'time_end') {
                $dateField = $this->getName() == 'time_start' ? 'date_start' : 'due_date';
                $dateValue = $recordInstance->get($dateField);
                $timeZone = getFieldTimeZoneValue($this->getName(), $record);

                if(!$timeZone) {
                    $timeZone = $current_user->time_zone;
                }

                $date = DateTimeField::convertTimeZone($dateValue.' '.$value, DateTimeField::getDBTimeZone(), $timeZone);
                $value = $date->format('H:i:s');

                $currentUser = Users_Record_Model::getCurrentUserModel();
                if($currentUser->get('hour_format') == '12'){
                    $value = Vtiger_Time_UIType::getTimeValueInAMorPM($value);
                }

                return $value . $this->getTimeZoneOffset($timeZone, $dateValue.' '.$value);
            }
        }
        return parent::getDisplayValue($value, $record, $recordInstance);
    }

    protected function getTimeZoneOffset($timeZone, $dateValue) {
        $timeZoneObject = new DateTimeZone($timeZone);
        $dateTimeForTimeZone = new DateTime($dateValue, new DateTimeZone(DateTimeField::getDBTimeZone()));
        $dateTimeForTimeZone->setTimezone($timeZoneObject);

        $offset = $timeZoneObject->getOffset($dateTimeForTimeZone) / 3600; //offset gets returned in seconds - converting to hours
        $timeZoneDisplay = $offset < 0 ? '-' : '+';
        $offset = abs($offset);
        if($timeZoneObject->getOffset($dateTimeForTimeZone) % 3600 == 0) {
            $timeZoneDisplay .= $offset.':00';
        } else {
            $timeZoneDisplay .= $offset.':30';
        }

        return ' '. $dateTimeForTimeZone->format('T') . ' (UTC' . $timeZoneDisplay . ')';
    }

    /**
     * Function to get Edit view display value
     * @param <String> Data base value
     * @return <String> value
     */
    public function getEditViewDisplayValue($value)
    {
        $fieldName = $this->getName();

        if ($fieldName == 'time_start' || $fieldName == 'time_end') {
            return $this->getUITypeModel()->getDisplayTimeDifferenceValue($fieldName, $value);
        }

        //Set the start date and end date
        if (empty($value)) {
            if ($fieldName === 'date_start') {
                return DateTimeField::convertToUserFormat(date('Y-m-d'));
            } elseif ($fieldName === 'due_date') {
                $currentUser = Users_Record_Model::getCurrentUserModel();
                $minutes = $currentUser->get('callduration');
                return DateTimeField::convertToUserFormat(date('Y-m-d', strtotime("+$minutes minutes")));
            }
        }
        return parent::getEditViewDisplayValue($value);
    }

    /**
     * Function which will give the picklist values for a recurrence field
     * @param type $fieldName -- string
     * @return type -- array of values
     */
    public static function getReccurencePicklistValues()
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $fieldModel = Vtiger_Field_Model::getInstance('recurringtype', Vtiger_Module_Model::getInstance('Events'));
        if ($fieldModel->isRoleBased() && !$currentUser->isAdminUser()) {
            $userModel = Users_Record_Model::getCurrentUserModel();
            $picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('recurringtype', $userModel->get('roleid'));
        } else {
            $picklistValues = Vtiger_Util_Helper::getPickListValues('recurringtype');
        }
        foreach ($picklistValues as $value) {
            $fieldPickListValues[$value] = vtranslate($value, 'Events');
        }
        return $fieldPickListValues;
    }

    /**
     * Function to get the advanced filter option names by Field type
     * @return <Array>
     */
    public static function getAdvancedFilterOpsByFieldType()
    {
        $filterOpsByFieldType = parent::getAdvancedFilterOpsByFieldType();
        $filterOpsByFieldType['O'] = array('e','n');

        return $filterOpsByFieldType;
    }

    /**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed()
    {
        if ($this->getFieldName() == 'visibility') {
            return false;
        }
        return true;
    }
}
