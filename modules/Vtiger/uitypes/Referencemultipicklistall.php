<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Referencemultipicklistall_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/Referencemultipicklist.tpl';
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getReferenceModule($value)
    {
        $fieldModel = $this->get('field');
        $referenceModuleList = $fieldModel->getReferenceList();
        $referenceEntityType = getSalesEntityType($value);
        if (in_array($referenceEntityType, $referenceModuleList)) {
            return Vtiger_Module_Model::getInstance($referenceEntityType);
        } elseif (in_array('Users', $referenceModuleList)) {
            return Vtiger_Module_Model::getInstance('Users');
        }
        return null;
    }

    /**
     * Function to get the display value in detail view
     * @param <Integer> crmid of record
     * @return <String>
     */
    public function getDisplayValue($value)
    {
        $result = '';
        if (!empty($value)) {
            $arrValue = explode(',', $value);
            $arr = array();
            foreach ($arrValue as $crmid) {
                $referenceModule = $this->getReferenceModule($crmid);
                if ($referenceModule) {
                    $referenceModuleName = $referenceModule->get('name');
                    $entityNames = getEntityName($referenceModuleName, array($crmid));
                    $linkValue = "<a href='index.php?module=$referenceModuleName&view=".$referenceModule->getDetailViewName()."&record=$crmid'
							title='".vtranslate($referenceModuleName, $referenceModuleName)."'>$entityNames[$crmid]</a>";
                    $arr[] =$linkValue;
                } elseif ($value == 'all') {
                    $arr[] =$value;
                }
            }
            $result = implode(', ', $arr);
        }
        return $result;
    }

    public function getDBInsertValue($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        return $value;
    }

    /**
     * Function to get the display value in edit view
     * @param reference record id
     * @return link
     */
    public function getEditViewDisplayValue($value)
    {
        $referenceModule = $this->getReferenceModule($value);
        if ($referenceModule) {
            $referenceModuleName = $referenceModule->get('name');
            $entityNames = getEntityName($referenceModuleName, array($value));
            return $entityNames[$value];
        }
        return '';
    }

    public function getListSearchTemplateName()
    {
        return 'uitypes/ReferencemultipicklistSearchView.tpl';
    }
}
