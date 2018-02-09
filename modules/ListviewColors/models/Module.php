<?php
/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
class ListviewColors_Module_Model extends Vtiger_Module_Model {

    var $user;
    var $db;

    function __construct() {
        $this->user = Users_Record_Model::getCurrentUserModel();
        $this->db = PearDatabase::getInstance();
    }

    public function getConditionalColors($moduleName){
        $list = array();
        $res = $this->db->pquery("SELECT * FROM vte_listview_colors WHERE modulename = ? AND `status` = ? ORDER BY `priority` DESC, `id` ASC", array($moduleName, 'Active'));

        if($this->db->num_rows($res)){
            while($row = $this->db->fetchByAssoc($res)){
                $list[] = $row;
            }
        }

        return $list;
    }

    public function getRecordsByCondition($condition, $records){
        $list = array();

        $advanceFilter = json_decode(html_entity_decode($condition['conditions']), true);

        if(count($advanceFilter[1]['columns']) > 0 && count($advanceFilter[2]['columns']) == 0){
            unset($advanceFilter[1]['condition']);
        }

        $queryGenerator = new QueryGenerator($condition['modulename'], $this->user);
        $queryGenerator->parseAdvFilterList($advanceFilter);

        $query = "SELECT vtiger_crmentity.crmid ";
        $query .= $queryGenerator->getFromClause();
        $query .= $queryGenerator->getWhereClause();
        if(!empty($records)){
            $query .= ' AND vtiger_crmentity.crmid IN('.implode(',', $records).') ';
        }
        $position = stripos($query, ' GROUP BY ');
        if ($position) {
            $split = spliti(' GROUP BY ', $query);
            $order = spliti(' AND ', $split[1]);
            $query = $split[0]. ' AND ' .$order[1]. ' GROUP BY ' .$order[0];
        }
        $res = $this->db->pquery($query);
        if($this->db->num_rows($res)){
            while($row = $this->db->fetchByAssoc($res)){
                $list[] = $row['crmid'];
            }
        }

        return $list;
    }

    function getDataSource($module){
        $query = $_SESSION[$module.'_listquery'];
        $count_replace = 1;
        $query = str_replace('SELECT', 'SELECT vtiger_crmentity.crmid, ', $query, $count_replace);

        $result = $this->db->pquery($query, array());
        $listSource = array();
        if($this->db->num_rows($result)){
            while($row = $this->db->fetchByAssoc($result)){
                $listSource[$row['crmid']] = $row;
            }
        }

        return $listSource;
    }

    /**
     * Function to get Settings links for admin user
     * @return Array
     */
    public function getSettingLinks() {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        if ($currentUserModel->isAdminUser()) {
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'Settings',
                'linkurl' =>'index.php?module=ListviewColors&view=Settings&parent=Settings',
                'linkicon' => ''
            );
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'Uninstall',
                'linkurl' =>'index.php?module=ListviewColors&view=Uninstall&parent=Settings',
                'linkicon' => ''
            );
        }
        return $settingsLinks;
    }
}