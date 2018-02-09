<?php

class Vtiger_Datetimezone_UIType extends Vtiger_Datetime_UIType {
    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName() {
        return 'uitypes/DateTimeZone.tpl';
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getDisplayValue($value) {
        if($value !='') {
            return $dateValue = self::getDisplayDateTimeValue($value);
        }
        return $value;
    }

    /**
     * Function to get Date and Time value for Display
     * @param <type> $date
     * @return <String>
     */
    public static function getDisplayDateTimeValue($date) {
        if($date !='') {
            $date = new DateTimeField($date);
            return $date->getDisplayDateTimeValue();
        }
        return $date;
    }

    /**
     * Function to get the display value in edit view
     * @param $value
     * @return converted value
     */
    public function getEditViewDisplayValue($value) {
        if($value !='') {
            return Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($value);
        }

        return $value;
    }

    /**
     * Function to get time value in AM/PM format
     * @param <String> $time
     * @return <String> time
     */
    public static function getTimeValueInAMorPM($time) {
        if($time){
            list($hours, $minutes, $seconds) = explode(':', $time);
            $format = vtranslate('PM');
            if ($hours > 12) {
                $hours = (int)$hours - 12;
            } else if ($hours < 12) {
                $format = vtranslate('AM');
            }

            //If hours zero then we need to make it as 12 AM
            if($hours == '00') {
                $hours = '12';
                $format = vtranslate('AM');
            }

            return "$hours:$minutes $format";
        } else {
            return '';
        }
    }

    /**
     * Function to get the Detailview template name for the current UI Type Object
     * @return <String> - Template Name
     */
    public function getDetailViewTemplateName() {
        return 'uitypes/DateTimeZoneDetailView.tpl';
    }
}