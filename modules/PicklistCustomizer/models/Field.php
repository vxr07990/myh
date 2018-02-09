<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class PicklistCustomizer_Field_Model extends Settings_Picklist_Field_Model {

	public static function getEditablePicklistValues($fieldName,$idAgentManager){
		$cache = Vtiger_Cache::getInstance();
		$EditablePicklistValues = $cache->get('EditablePicklistValues', $fieldName);
        if($EditablePicklistValues) {
            return $EditablePicklistValues;
        }

        $picklistValues = Vtiger_Util_Helper::getPickListValues($fieldName,false);
        $allowedValues = Vtiger_Util_Helper::getAllowedFieldsValuesForUser($fieldName, [$idAgentManager]);
        
        foreach ($picklistValues as $key => $value) {
            if(!in_array($key, $allowedValues)){
                unset($picklistValues[$key]);
            }
        }


		$cache->set('EditablePicklistValues', $fieldName, $picklistValues);
        return $picklistValues;
	}

	/**
     * Function which will give the non editable picklist values for a field
     * @param type $fieldName -- string
     * @return type -- array of values
     */
	public static function getNonEditablePicklistValues($fieldName,$idAgentManager){
		$cache = Vtiger_Cache::getInstance();
		$NonEditablePicklistValues = $cache->get('NonEditablePicklistValues', $fieldName);
        if($NonEditablePicklistValues) {
            return $NonEditablePicklistValues;
        }

        $picklistValues = Vtiger_Util_Helper::getPickListValues($fieldName,false);
        $allowedValues = Vtiger_Util_Helper::getAllowedFieldsValuesForUser($fieldName, [$idAgentManager]);
        $nonAllowedValues = Vtiger_Util_Helper::getNotAllowedFieldsValuesForUser($fieldName);

        //remove All Values that can be edited or belong to others agents
        foreach ($picklistValues as $key => $value) {
            if(in_array($key, $allowedValues) || in_array($key, $nonAllowedValues)){
                unset($picklistValues[$key]);
            }
        }

        $cache->set('NonEditablePicklistValues', $fieldName, $picklistValues);
        return $picklistValues;
	}

    function getAgentSelectablePicklistValues($fieldName,$idAgentManager, $fieldToDelete){
        $picklistValues = Vtiger_Util_Helper::getPickListValues($fieldName,false);
        $allowedValues = Vtiger_Util_Helper::getAllowedFieldsValuesForUser($fieldName, [$idAgentManager]);

        if(PicklistCustomizer_Field_Model::isVanlineID($idAgentManager)){
            $nonAllowedValues = Vtiger_Util_Helper::getNotAllowedFieldsValuesForUser($fieldName, [$idAgentManager]); //If we are deleting a value for am Vanline we can can choose only other values from this vanline or default ones
        }else{
            $nonAllowedValues = Vtiger_Util_Helper::getNotAllowedFieldsValuesForUser($fieldName); //If we are deleting a value for an agent we can choose a vanline value as replacement
        }
        

        
        foreach ($picklistValues as $key => $value) {
            if(in_array($key, $nonAllowedValues) || $value == $fieldToDelete || (is_array($allowedValues) && !in_array($key, $allowedValues))){
                unset($picklistValues[$key]);
            }
        }

        return $picklistValues;

    }

    public static function isVanlineID($idAgentManager){
        $db =  PearDatabase::getInstance();

        $result = $db->pquery('SELECT * FROM vtiger_crmentity WHERE setype=? AND crmid=?', ['VanlineManager', $idAgentManager]);
        if($result){
            return $db->num_rows($result);
        }else{
            return false;
        }
        

    }

}