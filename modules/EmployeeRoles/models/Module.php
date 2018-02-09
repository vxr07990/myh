<?php

class EmployeeRoles_Module_Model extends Vtiger_Module_Model {
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
            $matchingRecords = EmployeeRoles_Record_Model::getSearchResult($searchValue, $this->getName());
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

    public static function getMoveRolesForUser(){
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $accesibleAgents = $currentUserModel->getBothAccessibleOwnersIdsForUser();

        $sql = 'SELECT employeerolesid, emprole_desc FROM vtiger_employeeroles INNER JOIN vtiger_crmentity ON vtiger_employeeroles.employeerolesid = vtiger_crmentity.crmid 
                    WHERE deleted=0 AND emprole_class_type=? AND agentid IN (' . generateQuestionMarks($accesibleAgents) . ')';
        $params = [];
        $params[] = 'Office';
        $params[] = $accesibleAgents;
        $result = $db->pquery($sql, $params);

        $roles = [];
        if($result && $db->num_rows($result)> 0){
            while ($row = $db->fetch_row($result)) {
                $roles[] = $row['employeerolesid'] . '_' . str_replace(' ', '_',$row['emprole_desc']);
            }
        }

        return $roles;
    }
    
    public function getRoleIdFromRoleClassAndAgentId($roleClass,$agentId){
        $db = PearDatabase::getInstance();
        $sql = "SELECT employeerolesid FROM vtiger_employeeroles"
                . " JOIN vtiger_crmentity ON employeerolesid=crmid"
                . " JOIN vtiger_agentmanager ON agentmanagerid=?"
                . " WHERE deleted=0 AND (agentid=vanline_id OR agentid=agentmanagerid) AND emprole_class=?";
        $params = array($agentId,$roleClass);
        $result = $db->pquery($sql, $params);
        if($result && $db->num_rows($result) > 0){
            $roleId = $db->query_result($result, 0, 'employeerolesid');
        }else{
            $roleId = false;
        }
        return $roleId;

    }
}
