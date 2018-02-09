<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class RelatedRecordCount_Module_Model extends Vtiger_Module_Model {

    var $user;
    var $db;

    function __construct() {
        $this->user = Users_Record_Model::getCurrentUserModel();
        $this->db = PearDatabase::getInstance();
    }


    /**
     * Function to get Settings links for admin user
     * @return Array
     */
    public function getSettingLinks() {
        $settingsLinks = parent::getSettingLinks();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        if ($currentUserModel->isAdminUser()) {
            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'Settings',
                'linkurl' =>'index.php?module=RelatedRecordCount&view=Settings&parent=Settings',
                'linkicon' => ''
            );

            $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'Uninstall',
                'linkurl' =>'index.php?module=RelatedRecordCount&view=Uninstall&parent=Settings',
                'linkicon' => ''
            );
        }
        return $settingsLinks;
    }

    public function getSettings($pmoduleName, $relatedModuleName){
        $settings = array();
        $result = $this->db->pquery("SELECT *
                            FROM vte_related_record_count
                            WHERE modulename = ? AND related_modulename = ? AND status = ?
                            ORDER BY priority ASC ",
                        array($pmoduleName, $relatedModuleName, 'Active'));
        if($this->db->num_rows($result)>0){
            while($row = $this->db->fetchByAssoc($result)){
                $settings[] = $row;
            }
        }

        return $settings;
    }

    public function getCountLabel($setting, $parentId){
        $count = 0;

        $parentModuleName = $setting['modulename'];
        $relatedModuleName = $setting['related_modulename'];

        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $parentModuleName);
        $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
        $relatedModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModuleModel);
        //set current module is parent module
        global $currentModule;
        $currentModule=$parentModuleName;

        $query = $relatedModel->getQuery($parentRecordModel);

        $relationQuery = ereg_replace("[ \t\n\r]+", " ", $query);
        $position = stripos($relationQuery,' from ');
        if ($position) {
            $split = spliti(' FROM ', $relationQuery);
            $splitCount = count($split);
            $relationQuery = 'SELECT COUNT(DISTINCT vtiger_crmentity.crmid) AS count';
            for ($i=1; $i<$splitCount; $i++) {
                $relationQuery = $relationQuery. ' FROM ' .$split[$i];
            }
        }
        if(strpos($relationQuery,' GROUP BY ') !== false){
            $parts = explode(' GROUP BY ',$relationQuery);
            $relationQuery = $parts[0];
        }

        $advanceFilter = json_decode(html_entity_decode($setting['conditions']), true);

        if(count($advanceFilter[1]['columns']) > 0 && count($advanceFilter[2]['columns']) == 0){
            unset($advanceFilter[1]['condition']);
        }

        if(count($advanceFilter[1]['columns']) + count($advanceFilter[2]['columns']) > 0){
            $queryGenerator = new QueryGenerator($setting['related_modulename'], $this->user);
            $queryGenerator->parseAdvFilterList($advanceFilter);
            $relationQuery .= str_replace('WHERE vtiger_crmentity.deleted=0', ' ', $queryGenerator->getWhereClause());
        }

        $result = $this->db->pquery($relationQuery);
        if($this->db->num_rows($result)){
            $count = $this->db->query_result($result, 0, 'count');
        }

        return str_replace('$count$', $count, $setting['label']);
    }
    public function getCountRelatedListTariffSections($record){
        $result = $this->db->pquery("SELECT
                                            *
                                        FROM
                                            vtiger_tariffsections
                                        JOIN vtiger_crmentity ON (
                                            vtiger_crmentity.crmid = vtiger_tariffsections.tariffsectionsid
                                        )
                                        LEFT JOIN vtiger_tariffs ON (
                                            vtiger_tariffs.tariffsid = vtiger_tariffsections.related_tariff
                                        )
                                        WHERE
                                            vtiger_crmentity.deleted = ?
                                        AND vtiger_tariffsections.related_tariff = ?", array(0, $record));
        $counts = $this->db->num_rows($result);

        return $counts;
    }

    public function getCountRelatedListEffectiveDates($record){
        $result = $this->db->pquery("SELECT
                                            *
                                        FROM
                                            vtiger_effectivedates
                                        JOIN vtiger_crmentity ON (
                                            vtiger_crmentity.crmid = vtiger_effectivedates.effectivedatesid
                                        )
                                        LEFT JOIN vtiger_tariffs ON (
                                            vtiger_tariffs.tariffsid = vtiger_effectivedates.related_tariff
                                        )
                                        WHERE
                                            vtiger_crmentity.deleted = ?
                                        AND vtiger_effectivedates.related_tariff = ?", array(0, $record));
        $counts = $this->db->num_rows($result);

        return $counts;
    }

    public function getCountRelatedListTariffReportSections($record){
        $result = $this->db->pquery("SELECT
                                            *
                                        FROM
                                            vtiger_tariffreportsections
                                        JOIN vtiger_crmentity ON (
                                            vtiger_crmentity.crmid = vtiger_tariffreportsections.tariffreportsectionsid
                                        )
                                        LEFT JOIN vtiger_tariffs ON (
                                            vtiger_tariffs.tariffsid = vtiger_tariffreportsections.tariff_orders_tariff
                                        )
                                        WHERE
                                            vtiger_crmentity.deleted = ?
                                        AND vtiger_tariffreportsections.related_tariff = ?", array(0, $record));
        $counts = $this->db->num_rows($result);

        return $counts;
    }

    public function getCountRelatedListTariffServices($record){
        $result = $this->db->pquery("SELECT
                                            *
                                        FROM
                                            vtiger_tariffservices
                                        JOIN vtiger_crmentity ON (
                                            vtiger_crmentity.crmid = vtiger_tariffservices.tariffservicesid
                                        )
                                        JOIN vtiger_effectivedates ON (
                                            vtiger_effectivedates.effectivedatesid = vtiger_tariffservices.effective_date
                                        )
                                        WHERE
                                            vtiger_crmentity.deleted = ?
                                        AND vtiger_tariffservices.effective_date = ?", array(0, $record));
        $counts = $this->db->num_rows($result);

        return $counts;
    }
}
