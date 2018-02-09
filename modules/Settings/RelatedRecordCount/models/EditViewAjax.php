<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
class Settings_RelatedRecordCount_EditViewAjax_Model extends Vtiger_Base_Model {
    var $user;
    var $db;

    function __construct() {
        global $current_user;

        $this->user = $current_user;
        $this->db = PearDatabase::getInstance();
    }

    function getData($record){
        $data = array();
        if($record){
            $result = $this->db->pquery('SELECT * FROM vte_related_record_count
                                    WHERE vte_related_record_count.id = ?
                                    LIMIT 0, 1',
                array($record));

            if($this->db->num_rows($result) > 0){
                $data = $this->db->fetchByAssoc($result);
            }

        }

        return $data;
    }

    function getEntityModules(){
        $result = $this->db->pquery('SELECT *
                                    FROM vtiger_tab
                                    WHERE vtiger_tab.isentitytype = 1 AND vtiger_tab.presence = 0
                                        AND vtiger_tab.parent IS NOT NULL
	                                    AND vtiger_tab.parent != ""
                                    ORDER BY vtiger_tab.name', array());
        $arr = array();

        if($this->db->num_rows($result)){
            while($row = $this->db->fetchByAssoc($result)){
                $row['tablabel'] = vtranslate($row['name'], $row['name']);
                $arr[] = $row;
            }
        }

        return $arr;
    }

    function getRelatedModules($moduleName){
        $result = $this->db->pquery('SELECT
                                        vtiger_relatedlists.*, vtiger_tab. NAME AS modulename
                                    FROM
                                        vtiger_relatedlists
                                    INNER JOIN vtiger_tab ON vtiger_relatedlists.related_tabid = vtiger_tab.tabid
                                    WHERE
                                        vtiger_relatedlists.tabid = ?
                                    AND related_tabid != 0
                                    AND vtiger_relatedlists.presence <> 1
                                    AND vtiger_tab.presence <> 1
                                    AND vtiger_relatedlists. NAME NOT IN ("get_history")
                                    ORDER BY vtiger_tab.name',
                    array(getTabid($moduleName)));
        $arr = array();

        if($this->db->num_rows($result)){
            while($row = $this->db->fetchByAssoc($result)){
                $row['tablabel'] = vtranslate($row['modulename'], $row['modulename']);
                $arr[] = $row;
            }
        }

        return $arr;
    }

}
