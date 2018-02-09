<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class OrdersTask_Field_Model extends Vtiger_Field_Model
{
    public function getValue($values, $setype)
    {
		$request = new Vtiger_Request($_REQUEST, $_REQUEST);
        foreach ($values as $valueId) {
            if ($valueId == '') {
                continue;
            }

            $relatedRecordModel = Vtiger_Record_Model::getInstanceById($valueId, $setype);

            if ($setype == 'Employees') {
                $displayValue .= $relatedRecordModel->get('name') . ' ' . $relatedRecordModel->get('employee_lastname');
                $displayValue .= ' ('.$relatedRecordModel->get('employee_type');
				$recordId = $request->get("record");
				
                $db = PearDatabase::getInstance();
				$result = $db->pquery("SELECT emprole_desc AS role FROM vtiger_employeeroles WHERE employeerolesid IN (SELECT role FROM vtiger_orderstasksemprel WHERE taskid=? AND employeeid=?)", array($recordId, $valueId));
				if ($result && $db->num_rows($result) > 0) {
                    $displayValue .=  ' - ' .  $db->query_result($result, 0, 'role');
                }
				
				$displayValue .=  ')';
            } elseif ($setype == 'Vehicles') {
                $displayValue .= $relatedRecordModel->get('vechiles_unit') . '  ' . $relatedRecordModel->get('vehicle_platestate') . '(' . $relatedRecordModel->get('vehicle_type') . ')';
            } elseif ($setype == 'Vendors') {
                $displayValue .= $relatedRecordModel->get('vendorname') . ' (' . $relatedRecordModel->get('vendor_no') . ')';
            }

            if ($valueId != end($values)) {
                $displayValue .= ', ';
            }
        }

        return $displayValue;
    }

    public function getFieldInfo()
    {
        $this->fieldInfo = parent::getFieldInfo();
        $fieldDataType= $this->getFieldDataType();
        if ($fieldDataType == 'personnelpicklist') {
            $this->fieldInfo['reference_module'] = 'EmployeeRoles';
            $this->fieldInfo['picklistvalues'] = $this->getReferenceValues('EmployeeRoles');
        }
        return $this->fieldInfo;
    }

    public function getReferenceValues($referenceModule, $seachValue ='', $agentid = false)
    {
        $fieldDataType = $this->getFieldDataType();
        if ($fieldDataType == 'personnelpicklist') {
            $referenceModule = 'EmployeeRoles';
            //global $list_max_entries_per_page;
            $fieldName = $this->getName();
            $fieldValue = $this->get('fieldvalue');

            $cache = Vtiger_Cache::getInstance();
            if ($cache->getPicklistValues($fieldName)) {
                return $cache->getPicklistValues($fieldName);
            }
            $db = PearDatabase::getInstance();
            $seachConditions='';
            $searchConditionParams='';
            if (!empty($seachValue)) {
                $searchConditionParams='%'.$seachValue.'%';
                $seachConditions .= " AND label LIKE ? ";
            }
            $agentCondition = '';
            $agentConditionParams = '';
            if($agentid){
                $agentCondition .= " AND vtiger_crmentity.agentid = ? ";
                $agentConditionParams=$agentid;
            }
            if (!empty($fieldValue)) {
                $fieldValueList = explode(',', $fieldValue);

                $query = "SELECT crmid,label FROM
                              ((SELECT crmid , label,createdtime,2 AS seq  FROM vtiger_crmentity
                              INNER JOIN vtiger_employeeroles ON vtiger_employeeroles.employeerolesid = vtiger_crmentity.crmid
                              WHERE label IS NOT NULL AND TRIM(label) != '' 
                                    AND  crmid in (".generateQuestionMarks($fieldValueList).") 
                                    AND vtiger_employeeroles.emprole_class_type = 'Operations' $agentCondition
                                    AND deleted = 0
                              ORDER BY createdtime DESC) ";
                              //LIMIT $list_max_entries_per_page)
                $query .= "UNION
                              (SELECT crmid , label, createdtime,1 AS seq  FROM vtiger_crmentity
                              INNER JOIN vtiger_employeeroles ON vtiger_employeeroles.employeerolesid = vtiger_crmentity.crmid
                              WHERE label IS NOT NULL AND TRIM(label) != '' 
                                    AND setype = ? 
                                    AND vtiger_employeeroles.emprole_class_type = 'Operations' $agentCondition
                                    AND deleted = 0 $seachConditions
                              ORDER BY createdtime DESC)) AS E ";
                              //LIMIT $list_max_entries_per_page )) AS E
                $query .= "GROUP BY E.crmid
                          ORDER BY E.seq DESC, E.createdtime DESC";
                          //LIMIT $list_max_entries_per_page";
                $params = array($fieldValueList);
                if($agentConditionParams) {
                    $params[]=$agentConditionParams;
                }
                $params[]=$referenceModule;
                if($searchConditionParams) {
                    $params[]=$searchConditionParams;
                }
            } else {
                $query = "SELECT crmid , label FROM vtiger_crmentity
                          INNER JOIN vtiger_employeeroles ON vtiger_employeeroles.employeerolesid = vtiger_crmentity.crmid
                          WHERE setype = ? 
                                AND vtiger_employeeroles.emprole_class_type = 'Operations' $agentCondition
                                AND deleted = 0 $seachConditions
                          ORDER BY createdtime DESC";
                          //LIMIT $list_max_entries_per_page";
                $params = array($referenceModule); //,$agentConditionParams,$searchConditionParams);
                if($agentConditionParams) {
                    $params[]=$agentConditionParams;
                }
                if($searchConditionParams) {
                    $params[]=$searchConditionParams;
                }
            }

            $values = array();
            $result = $db->pquery($query, $params);
            $num_rows = $db->num_rows($result);
            for ($i=0; $i<$num_rows; $i++) {
                //                Need to decode the picklist values twice which are saved from old ui
                $values[$db->query_result($result, $i, 'crmid')] = decode_html(decode_html($db->query_result($result, $i, 'label')));
            }
            $cache->setPicklistValues($fieldName, $values);
            return $values;
        } else {
            return parent::getReferenceValues($referenceModule, $seachValue);
        }
    }
    public function getPicklistValues()
    {
        $fieldDataType = $this->getFieldDataType();
        if ($fieldDataType =='vehiclepicklist') {
            $fieldName = 'vehicle_type';
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, Vtiger_Module::getInstance('Vehicles'));
            $picklistValues = Vtiger_Util_Helper::getCustomPicklistValues($fieldName, $fieldModel->getId());
            foreach ($picklistValues as $value) {
                $fieldPickListValues[$value] = vtranslate($value, 'Vehicles');
            }
            return $fieldPickListValues;
        }else {
            //@TODO find out why this is here and remove it if it's not needed
            $fieldName = $this->getFieldName();
            if ($fieldName == 'business_line' && getenv('INSTANCE_NAME') != 'graebel' && !getenv('IGC_MOVEHQ')) {
                $picklists = array(
                    'HHG - Interstate',
                    'HHG - Intrastate',
                    'HHG - Local',
                    'HHG - International',
                    'Electronics - Interstate',
                    'Electronics - Intrastate',
                    'Electronics - Local',
                    'Electronics - International',
                    'Display & Exhibits - Interstate',
                    'Display & Exhibits - Intrastate',
                    'Display & Exhibits - Local',
                    'Display & Exhibits - International',
                    'General Commodities - Interstate',
                    'General Commodities - Intrastate',
                    'General Commodities - Local',
                    'General Commodities - International',
                    'Auto - Interstate',
                    'Auto - Intrastate',
                    'Auto - Local',
                    'Auto - International',
                    'Commercial - Interstate',
                    'Commercial - Intrastate',
                    'Commercial - Local',
                    'Commercial - International',
                );

                $picklistValues = array();
                foreach ($picklists as $key => $item){
                    $picklistValues[$item] = $item;
                }

                return $picklistValues;
            }elseif($fieldName == 'carton_name'){
                $picklists = Estimates_Record_Model::getPackingLabelsStatic();
                $picklistValues = array();
                foreach ($picklists as $key => $item){
                    $picklistValues[$item] = $item;
                }
                return $picklistValues;
            }
        }
        return parent::getPicklistValues();
    }

    public function getValidator()
    {
        $validator = [];

        $typeOfData = $this->get('typeofdata');
        $components = explode('~', $typeOfData);

        if($components[0] == 'I' && $components[2] == 'MIN=0'){
            $validator[] = ['name'   => 'WholeNumber'];
            return $validator;
        }else{
            return parent::getValidator();
        }

    }
}
