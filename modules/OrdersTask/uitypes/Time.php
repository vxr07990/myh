<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_Time_UIType extends Vtiger_Time_UIType {
    
    
    /**
     * Function to get display value for time
     * @param <String> time
     * @return <String> time
     */
    public static function getDisplayTimeValue($time) {
	$date = new DateTimeField($time);
	return $date->getDisplayTime();
    }

    /**
     * Function to get time value in AM/PM format
     * @param <String> $time
     * @return <String> time
     */
    public static function getTimeValueInAMorPM($time) {
	if ($time) {
	    list($hours, $minutes, $seconds) = explode(':', $time);
	    $format = vtranslate('PM');
	    if ($hours > 12) {
		$hours = (int) $hours - 12;
	    } elseif ($hours < 12) {
		$format = vtranslate('AM');
	    }

	    //If hours zero then we need to make it as 12 AM
	    if ($hours == '00') {
		$hours = '12';
		$format = vtranslate('AM');
	    }

	    return "$hours:$minutes $format";
	} else {
	    return '';
	}
    }

    /**
     * Function to get Time value with seconds
     * @param <String> $time
     * @return <String> time
     */
    public static function getTimeValueWithSeconds($time) {
	if ($time) {
	    $timeDetails = explode(' ', $time);
	    list($hours, $minutes, $seconds) = explode(':', $timeDetails[0]);

	    //If pm exists and if it not 12 then we need to make it to 24 hour format
	    if ($timeDetails[1] === 'PM' && $hours != '12') {
		$hours = $hours + 12;
	    }

	    if ($timeDetails[1] === 'AM' && $hours == '12') {
		$hours = '00';
	    }

	    if (empty($seconds)) {
		$seconds = '00';
	    }

	    return "$hours:$minutes:$seconds";
	} else {
	    return '';
	}
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return $value
     */
    public function getDisplayValue($value, $recordId) {

	if ($this->get('field')->getFieldName() == 'disp_assignedstart' || $this->get('field')->getFieldName() == 'disp_actualend') {
	    $user = Users_Record_Model::getCurrentUserModel();
	    $timeZone =  $user->get('time_zone');
	    $date = DateTimeField::convertTimeZone($value, DateTimeField::getDBTimeZone(), $timeZone);
	    $value = $date->format("H:i:s");
	} else {
	    //VGS Conrado - This breaks the Local disptach fields
	    
	    $timeZone = getFieldTimeZoneValue($this->get('field')->getFieldName(), $recordId);
	    if ($timeZone) {
		$date = DateTimeField::convertTimeZone($value, DateTimeField::getDBTimeZone(), $timeZone);
		$value = $date->format("H:i:s");
	    }
	}


	$userModel = Users_Privileges_Model::getCurrentUserModel();
	if ($userModel->get('hour_format') == '12') {
	    return self::getTimeValueInAMorPM($value);
	}
	return $value;
    }
    
    	/**
	 * Function to get the display value in edit view
	 * @param $value
	 * @return converted value
	 */
	public function getEditViewDisplayValue($value) {
		return self::getDisplayValue($value, '');
	}

  
}
