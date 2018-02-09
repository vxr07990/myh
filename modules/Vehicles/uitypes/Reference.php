<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vehicles_Reference_UIType extends Vtiger_Reference_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/Reference.tpl';
    }
    
    public function getDisplayValue($value)
    {
        $referenceModule = $this->getReferenceModule($value);
        if ($referenceModule && !empty($value)) {
            $referenceModuleName = $referenceModule->get('name');
            if ($referenceModuleName == 'Users') {
                $db = PearDatabase::getInstance();
                $nameResult = $db->pquery('SELECT first_name, last_name FROM vtiger_users WHERE id = ?', array($value));
                if ($db->num_rows($nameResult)) {
                    return $db->query_result($nameResult, 0, 'first_name').' '.$db->query_result($nameResult, 0, 'last_name');
                }
            } else {
                $entityNames = getEntityName($referenceModuleName, array($value));
                global $adb;
                $query = $adb->pquery("SELECT * FROM `vtiger_agents` WHERE `agentsid` = ?",array($value));
                if ($adb->num_rows($query)) {
                    $linkValue = "<a href='index.php?module=$referenceModuleName&view=".$referenceModule->getDetailViewName()."&record=$value'
							title='".vtranslate($referenceModuleName, $referenceModuleName)."'>({$adb->query_result($query, 0, 'agent_number')}) $entityNames[$value]</a>";
                    return $linkValue;
                }
                else
                {
                    $linkValue = "<a href='index.php?module=$referenceModuleName&view=".$referenceModule->getDetailViewName()."&record=$value'
							title='".vtranslate($referenceModuleName, $referenceModuleName)."'>$entityNames[$value]</a>";
                    return $linkValue;
                }

            }
        }
        return '';
    }

    public function getEditViewFullName($value){
        global $adb;
        $query = $adb->pquery("SELECT * FROM `vtiger_agents` WHERE `agentsid` = ?",array($value));

            $referenceModule = Vtiger_Reference_UIType::getReferenceModule($value);
            if ($referenceModule) {
                $referenceModuleName = $referenceModule->get('name');
                $entityNames = getEntityName($referenceModuleName, array($value));
                if ($adb->num_rows($query)) {
                    return '(' . $adb->query_result($query, 0, 'agent_number') . ') ' . $entityNames[$value];
                }
                else
                {
                    return  $entityNames[$value];
                }
            }
        return '';
    }
}
