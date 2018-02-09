<?php
/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
class Settings_ListviewColors_Settings_Model extends Vtiger_Base_Model {
    var $user;
    var $db;

    function __construct() {
        global $current_user;

        $this->user = $current_user;
        $this->db = PearDatabase::getInstance();
    }

    function getData(){
        $settings = array();
        $query = "SELECT * FROM vte_listview_colors ORDER BY modulename ASC, priority ASC";
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
        $this->db->pquery("DELETE FROM vte_listview_colors WHERE vte_listview_colors.id=?", array($recordId));
        return true;
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
        $condition_name = $request->get('condition_name');
        $text_color = $request->get('text_color');
        $bg_color = $request->get('bg_color');
        $related_record_color = $request->get('related_record_color');
        $conditions = $request->get('advfilterlist');
        $conditions_count = count($conditions[1]['columns']) + count($conditions[2]['columns']);
        $status = $request->get('status');

        $this->db->pquery('UPDATE vte_listview_colors SET `modulename` = ?, `condition_name` = ?, `text_color` = ?, `bg_color` = ?, `related_record_color` = ?, `conditions` = ?, `conditions_count` = ?,`status` = ?
                            WHERE `id` = ?',
                            array($modulename, $condition_name, $text_color, $bg_color, $related_record_color, json_encode($conditions), $conditions_count, $status, $recordId));

        return true;
    }

    function addSetting($request){
        $modulename = $request->get('modulename');
        $condition_name = $request->get('condition_name');
        $text_color = $request->get('text_color');
        $bg_color = $request->get('bg_color');
        $related_record_color = $request->get('related_record_color');
        $conditions = $request->get('advfilterlist');
        $conditions_count = count($conditions[1]['columns']) + count($conditions[2]['columns']);
        $status = $request->get('status');
        $priority = $this->getMaxPriority();

        $this->db->pquery('INSERT INTO vte_listview_colors(`modulename`, `condition_name`, `text_color`, `bg_color`, `related_record_color`, `conditions`, `conditions_count`, `priority`, `status`)
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            array($modulename, $condition_name, $text_color, $bg_color, $related_record_color, json_encode($conditions), (int)$conditions_count, $priority, $status));

        return true;
    }

    function getMaxPriority(){
        $max = 1;
        $result = $this->db->pquery("SELECT MAX(priority) max_priority FROM vte_listview_colors", array());
        if($this->db->num_rows($result)){
            $max = $this->db->query_result($result, 0, 'max_priority') + 1;
        }

        return $max;
    }

    function updatePriority($request){
        $records = $request->get('items');
        if(!empty($records)){
            foreach($records as $k=>$record){
                $this->db->pquery("UPDATE vte_listview_colors SET vte_listview_colors.priority = ? WHERE vte_listview_colors.id=?", array($k+1, $record));
            }
        }
        return true;
    }
}
