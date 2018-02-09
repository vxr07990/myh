<?php
/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
class Settings_ListviewColors_EditViewAjax_Model extends Vtiger_Base_Model {
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
            $result = $this->db->pquery('SELECT * FROM vte_listview_colors
                                    WHERE vte_listview_colors.id = ?
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

}
