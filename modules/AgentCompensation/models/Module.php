<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class AgentCompensation_Module_Model extends Vtiger_Module_Model {
    public function searchRecord($searchValue, $parentId=false, $parentModule=false, $relatedModule=false)
    {
        $srcField = $_REQUEST['src_field'];
        if($srcField == 'agentcompensationid'){
            $module = $this->getName();
            $db = PearDatabase::getInstance();
            $query  = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
            $params = ["%$searchValue%"];
            if ($module !== false) {
                $query .= ' AND setype = ?';
                $params[] = $module;
            }
            if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
                $agentId = $_REQUEST['agentId'];
                if (!empty($agentId)) {
                    $query .= ' AND vtiger_crmentity.agentid = ? ';
                    $params[] = $agentId;
                }
            }
            //Remove the ordering for now to improve the speed
            //$query .= ' ORDER BY createdtime DESC';
            $query = AgentCompensation_ListView_Model::buildQueryForAgentCompensation($query,$params);

            $result   = $db->pquery($query, $params);
            $noOfRows = $db->num_rows($result);
            $moduleModels = $matchingRecords = $leadIdsList = [];
            for ($i = 0; $i < $noOfRows; ++$i) {
                $row = $db->query_result_rowdata($result, $i);
                if ($row['setype'] === 'Leads') {
                    $leadIdsList[] = $row['crmid'];
                }
            }
            $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);
            for ($i = 0, $recordsCount = 0; $i < $noOfRows && $recordsCount < 100; ++$i) {
                $row = $db->query_result_rowdata($result, $i);
                if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                    continue;
                }
                if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                    $row['id']  = $row['crmid'];
                    $moduleName = $row['setype'];
                    if (!array_key_exists($moduleName, $moduleModels)) {
                        $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                    }
                    $moduleModel                              = $moduleModels[$moduleName];
                    $modelClassName                           = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                    $recordInstance                           = new $modelClassName();
                    $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                    $recordsCount++;
                }
            }
            return $matchingRecords;

        }else{
            return parent::searchRecord($searchValue,$parentId,$parentModule,$relatedModule);
        }
    }
}