<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/vtlib/Vtiger/Module.php');

/**
 * Vtiger Module Model Class
 */
class Agents_Module_Model extends Vtiger_Module_Model
{

    //public function isSummaryViewSupported() {
    //	return false;
    //}
    public function isQuickCreateSupported()
    {
        return false;
    }

    /**
     * Function searches the records in the module, if parentId & parentModule
     * is given then searches only those records related to them.
     * @param <String> $searchValue - Search value
     * @param <Integer> $parentId - parent recordId
     * @param <String> $parentModule - parent module name
     * @return <Array of Vtiger_Record_Model>
     */
    public function searchRecord($searchValue, $parentId=false, $parentModule=false, $relatedModule=false)
    {
        if (!empty($searchValue) && empty($parentId) && empty($parentModule)) {
            $matchingRecords = Agents_Record_Model::getSearchResult($searchValue, $this->getName());
        } elseif ($parentId && $parentModule) {
            $db = PearDatabase::getInstance();
            $result = $db->pquery($this->getSearchRecordsQuery($searchValue, $parentId, $parentModule), array());
            $noOfRows = $db->num_rows($result);

            $moduleModels = array();
            $matchingRecords = array();
            for ($i=0; $i<$noOfRows; ++$i) {
                $row = $db->query_result_rowdata($result, $i);
                if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                    $row['id'] = $row['crmid'];
                    $moduleName = $row['setype'];
                    if (!array_key_exists($moduleName, $moduleModels)) {
                        $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                    }
                    $moduleModel = $moduleModels[$moduleName];
                    $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                    $recordInstance = new $modelClassName();
                    $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                }
            }
        }

        return $matchingRecords;
    }
}
