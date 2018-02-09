<?php

class Vtiger_Datezone_UIType extends Vtiger_Date_UIType {
    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName() {
        return 'uitypes/DateZone.tpl';
    }

    public function getDisplayValue($value) {
        if(empty($value)){
            return $value;
        } else {
            $dateValue = self::getDisplayDateValue($value);
        }

        if($dateValue == '--') {
            return "";
        } else {
            return $dateValue;
        }
    }

    /**
     * Function to get the Detailview template name for the current UI Type Object
     * @return <String> - Template Name
     */
    public function getDetailViewTemplateName() {
        return 'uitypes/DateZoneDetailView.tpl';
    }
}