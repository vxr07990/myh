<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('libraries/nusoap/nusoap.php');

class Surveys_Module_Model extends Inventory_Module_Model
{
    public function isQuickCreateSupported()
    {
        return true;
    }

    public function updateSurveyors($oppId, $surveyorID)
    {
        $db = PearDatabase::getInstance();

        $deletedCondition = $this->getDeletedRecordCondition();
        $query = 'SELECT * FROM vtiger_crmentity JOIN `vtiger_surveys` ON `surveysid` = `crmid` WHERE '.$deletedCondition.' AND `potential_id` = ?';
        $params = array($oppId);
        $result = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);

        $records = array();
        for ($i=0; $i<$noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            $row['id'] = $row['crmid'];
            $records[$row['id']] = $this->getRecordFromArray($row);
        }

        foreach ($records as $record) {
            $record->set('assigned_user_id', $surveyorID);
            $record->save();
        }
    }

    public static function SurveyUpdateNotification($wsdl, $params)
    {
        if (!$wsdl) {
            //@TODO add failure handler
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'failed: no wsdl('.$wsdl.')');
            return;
        }
        try {
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'wsdl : ('.$wsdl.')');
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'params : ('.print_r($params, true).')');
            $soapClient = new soapclient2($wsdl, 'wsdl');
            $soapClient->setDefaultRpcParams(true);
            $soapProxy  = $soapClient->getProxy();
            $soapResult = $soapProxy->SurveyUpdateNotification($params);
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'soapResult: ('.print_r($soapResult, true).')');
            return $soapResult;
        } catch (Exception $ex) {
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'Exception : ('.print_r($ex->getMessage(), true).')');
            //SOMETHING FAILED.
            //@TODO add failure handler
        }
    }

    public function searchRecord($searchValue, $parentId=false, $parentModule=false, $relatedModule=false)
    {
        if (!empty($searchValue)&& !empty($parentId) && $parentModule == 'Opportunities') {
            $matchingRecords = Surveys_Record_Model::getSearchResult($searchValue, $this->getName(), $parentId);
        } else {
            if (!empty($searchValue)&& empty($parentId) && empty($parentModule)) {
                $matchingRecords = Vtiger_Record_Model::getSearchResult($searchValue, $this->getName());
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
        }

        return $matchingRecords;
    }

    public function SendSurveyUpdateNotification($recordId, $syncUserId, $recordModuleName) {
        if (!$recordId) {
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'failed: no recordId('.$recordId.')');
            return;
        }

        if (!$syncUserId) {
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'failed: no syncUserId('.$syncUserId.')');
            return;
        }

        if (!$recordModuleName) {
            \MoveCrm\LogUtils::LogToFile('LOG_SURVEY_UPDATE', 'failed: no recordModuleName('.$recordModuleName.')');
            return;
        }

        $username = self::getUsername($syncUserId);
        $accessKey = self::getAccessKey($syncUserId);

        $params = [];

        $params['username'] = $username;
        $params['accessKey'] = $accessKey;
        $params['surveyID'] = vtws_getWebserviceEntityId($recordModuleName, $recordId);
        $params['address'] = getenv('SITE_URL');

        $wsdl = getenv('SURVEY_SYNC_URL');
        return Surveys_Module_Model::SurveyUpdateNotification($wsdl, $params);
    }

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
    }

    protected function getUsername($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'user_name');
    }
}
