<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MenuGroups_Record_Model extends Vtiger_Record_Model
{
    public static function getInstanceByRelatedIdForEdit($relatedId, $groupName = false)
    {
        if (!$relatedId) {
            $recordModel = Vtiger_Record_Model::getCleanInstance('MenuGroups');
            $recordModel->set('mode','');
            return $recordModel;
        }

        if (!$groupName) {
            $recordModel = Vtiger_Record_Model::getCleanInstance('MenuGroups');
            $recordModel->set('mode','');
            $recordModel->set('menucreator_id', $relatedId);
            return $recordModel;
        }

        $db = &PearDatabase::getInstance();
        $stmt = 'SELECT * FROM `vtiger_menugroups`'
                . ' LEFT JOIN `vtiger_crmentity` ON (`vtiger_crmentity`.`crmid` = `vtiger_menugroups`.`menugroupsid`)'
                . ' WHERE `vtiger_crmentity`.`deleted` = 0 '
                . ' AND menucreator_id = ? '
                . ' AND `group_name` = ? '
                . ' LIMIT 1';
        $res = $db->pquery($stmt, [$relatedId, $groupName]);

        if (method_exists($res, 'fetchRow') && ($row = $res->fetchRow())) {
            if ($row['menugroupsid'] > 0) {
                try {
                    $recordModel = parent::getInstanceById($row['menugroupsid'], 'MenuGroups');
                    if (
                        $recordModel &&
                        $recordModel->getModuleName() == 'MenuGroups'
                    ) {
                        $recordModel->set('mode','edit');
                        return $recordModel;
                    }
                } catch (Exception $ex) {
                    //Let it return the cleanInstance
                }
            }
        }

        //@TODO: should we throw an error here? Instead of returning an empty one?
        $recordModel = Vtiger_Record_Model::getCleanInstance('MenuGroups');
        $recordModel->set('mode','');
        $recordModel->set('menucreator_id', $relatedId);
        $recordModel->set('group_name', $groupName);
        return $recordModel;
    }
}
