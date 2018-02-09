<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */ 
 
class Settings_RelatedRecordCount_Settings_Model extends Vtiger_Base_Model {
    var $user;
    var $db;

    function __construct() {
        global $current_user;

        $this->user = $current_user;
        $this->db = PearDatabase::getInstance();
    }

    function getData(){
        $settings = array();
        $query = "SELECT * FROM vte_related_record_count ORDER BY modulename ASC, related_modulename ASC, priority ASC";
        $result = $this->db->pquery($query, array());
        if($this->db->num_rows($result)>0){
            while($row=$this->db->fetchByAssoc($result)){
                $settings[] = $row;
            }
        }

        return $settings;
    }

    function getModuleFields($moduleName){
        $moduleHandler = vtws_getModuleHandlerFromName($moduleName, $this->user);
        $moduleMeta = $moduleHandler->getMeta();
        return $moduleMeta->getModuleFields();
    }

    function deleteRecord($request){
        $recordId = $request->get('record', 0);
        if($recordId==0){
            return false;
        }
        $this->db->pquery("DELETE FROM vte_related_record_count WHERE vte_related_record_count.id=?", array($recordId));
        return true;
    }

    function duplicateRecord($request){
        $recordId = $request->get('record', 0);
        if($recordId==0){
            return false;
        }

        $recordData = $this->getRecord($recordId);
        $modulename = $recordData['modulename'];
        $related_modulename = $recordData['related_modulename'];
        $color = $recordData['color'];
        $label = $recordData['label'];
        $conditions = $recordData['advfilterlist'];
        $status = $recordData['status'];
        $priority = $this->getMaxPriority();

        $this->db->pquery('INSERT INTO vte_related_record_count(`modulename`, `related_modulename`, `color`, `label`, `conditions`, `priority`, `status`)
                            VALUES(?, ?, ?, ?, ?, ?, ?)',
            array($modulename, $related_modulename, $color, $label, json_encode($conditions), $priority, $status));

        return true;

    }

    function getRecord($recordId){
        $recordData = array();
        $query = "SELECT * FROM vte_related_record_count WHERE vte_related_record_count.id = ?";
        $result = $this->db->pquery($query, array($recordId));
        if($this->db->num_rows($result)>0){
            while($row=$this->db->fetchByAssoc($result)){
                $recordData = $row;
            }
        }

        return $recordData;

    }

    function saveSetting($request){
        $recordId = $request->get('record', 0);

        if($recordId > 0){
            $this->updateSetting($request);
        }else{
            $this->addSetting($request);
        }

        return true;
    }

    function updateSetting($request){
        $recordId = $request->get('record');
        $modulename = $request->get('modulename');
        $related_modulename = $request->get('related_modulename');
        $color = $request->get('color');
        $label = $this->processLabelValue($request->get('label'));
        $conditions = $request->get('advfilterlist');
        $status = $request->get('status');

        $this->db->pquery('UPDATE vte_related_record_count 
							SET `modulename` = ?, `related_modulename` = ?, `color` = ?, `label` = ?, `conditions` = ?, `status` = ? WHERE `id` = ?',
                            array($modulename, $related_modulename, $color, $label, json_encode($conditions), $status, $recordId));

        return true;
    }

    function addSetting($request){
        $modulename = $request->get('modulename');
        $related_modulename = $request->get('related_modulename');
        $color = $request->get('color');
        $label = $this->processLabelValue($request->get('label'));
        $conditions = $request->get('advfilterlist');
        $status = $request->get('status');
        $priority = $this->getMaxPriority();

        $this->db->pquery('INSERT INTO vte_related_record_count(`modulename`, `related_modulename`, `color`, `label`, `conditions`, `priority`, `status`)
                            VALUES(?, ?, ?, ?, ?, ?, ?)',
                            array($modulename, $related_modulename, $color, $label, json_encode($conditions), $priority, $status));

        return true;
    }

    function getMaxPriority(){
        $max = 1;
        $result = $this->db->pquery("SELECT MAX(priority) max_priority FROM vte_related_record_count", array());
        if($this->db->num_rows($result)){
            $max = $this->db->query_result($result, 0, 'max_priority') + 1;
        }

        return $max;
    }

    function updatePriority($request){
        $records = $request->get('records');
        if(!empty($records)){
            foreach($records as $k=>$record){
                $this->db->pquery("UPDATE vte_related_record_count SET vte_related_record_count.priority = ? WHERE vte_related_record_count.id=?", array($k+1, $record));
            }
        }
        return true;
    }

    function processLabelValue($label){
        if(trim($label)==''){
            $label = '$count$';
        }else{
            if(strpos($label, '$count$') === false){
                $label .= ' $count$';
            }
        }

        return $label;
    }
}
