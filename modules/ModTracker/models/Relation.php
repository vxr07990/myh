<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModTracker_Relation_Model extends Vtiger_Record_Model
{
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getLinkedRecord()
    {
        $db = PearDatabase::getInstance();

        $targetId = $this->get('targetid');
        $targetModule = $this->get('targetmodule');

        //@TODO: See about simply using:
//        $recordInstance = Vtiger_Record_Model::getInstanceById($targetId, $targetModule);
//        if ($recordInstance && $recordInstance->getModuleName()) {
//            return $recordInstance;
//        }
//        return false;

        if($targetModule == 'ParticipatingAgentsNonGuest') {
            $query = 'SELECT * FROM `vtiger_participatingagents` WHERE `participatingagentsid` = ?';
        } else {
            $query = 'SELECT * FROM vtiger_crmentity WHERE crmid = ?';
        }
        $params = array($targetId);
        $result = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);
        $moduleModels = array();
        if ($noOfRows) {
            if (!array_key_exists($targetModule, $moduleModels)) {
                $moduleModel = Vtiger_Module_Model::getInstance($targetModule);
            }
            $row = $db->query_result_rowdata($result, 0);
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $targetModule);
            $recordInstance = new $modelClassName();
            $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
            $recordInstance->set('id', $row['crmid']);
            return $recordInstance;
        }
        return false;
    }

    public function getParticipatingAgentInfo()
    {
        $db = PearDatabase::getInstance();

        $targetId = $this->get('targetid');
        $targetModule = $this->get('targetmodule');

        if($targetModule != 'ParticipatingAgentsNonGuest') {
            return false;
        }

        $sql = "SELECT agent_number, agentname, agent_type FROM `vtiger_participatingagents` JOIN `vtiger_agents` ON agents_id=agentsid WHERE participatingagentsid=?";
        $result = $db->pquery($sql, [$targetId]);
        if($result) {
            return ['name'=>'('.$result->fields['agent_number'].') '.$result->fields['agentname'], 'type'=>$result->fields['agent_type']];
        }
        return false;
    }
}
