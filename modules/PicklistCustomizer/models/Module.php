<?php

class PicklistCustomizer_Module_Model extends Vtiger_Module_Model
{

    static function addPickListValues($fieldModel, $newValue, $agentid)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUser->getId();
        $currentTime = date('Y-m-d H:i:s');
        $db = PearDatabase::getInstance();

        $accessibleOwners = $currentUser->getBothAccessibleOwnersIdsForUser();

        if(!in_array($agentid, $accessibleOwners)) {
            throw new Exception('Unauthorized', '401');
        }

        $db->startTransaction();
        $insertSql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
        $db->pquery($insertSql, [$agentid, $fieldModel->getId(), $newValue, 'ADDED', $currentTime, $currentTime, $currentUser->getId()]);

        $result = $db->query("SELECT LAST_INSERT_ID() AS `id`");
        $valueid = $result->fields['id'];

        $sql = "SELECT fieldid FROM `vtiger_field` WHERE fieldname=? AND fieldid != ? AND uitype=1500 AND presence != 1";
        $result = $db->pquery($sql, [$fieldModel->getName(), $fieldModel->getId()]);
        while($row =& $result->fetchRow()) {
            $db->pquery($insertSql, [$agentid, $row['fieldid'], $newValue, 'ADDED', $currentTime, $currentTime, $currentUser->getId()]);
        }
        $db->completeTransaction();

        return $valueid;
    }

    static function getPickListModules()
    {
        global $adb;
        // vtlib customization: Ignore disabled modules.
        $query = 'SELECT DISTINCT vtiger_field.fieldname,vtiger_field.tabid,vtiger_tab.tablabel, vtiger_tab.name AS tabname,uitype 
                    FROM vtiger_field INNER JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_field.tabid 
                    WHERE uitype IN (1500) and vtiger_tab.name != "Users" AND vtiger_tab.presence != 1 AND vtiger_field.presence IN (0,2) ORDER BY vtiger_field.tabid ASC';
        // END
        $result = $adb->pquery($query, array());
        while ($row = $adb->fetch_array($result)) {
            $modules[$row['tablabel']] = vtranslate($row['tabname'], $row['tablabel']);
        }
        return $modules;
    }

    public function remove($pickListFieldName, $fieldId, $valueToDeleteId, $replaceValueId, $agentid)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUser->getId();
        $currentTime = date('Y-m-d H:i:s');
        $db = PearDatabase::getInstance();
        $fieldModel = Vtiger_Field_Model::getInstance($fieldId);
        if($fieldModel->getName() != $pickListFieldName) {
            return false;
        }
        $valueTable = 'vtiger_'.$pickListFieldName;

        $sql        = "SHOW KEYS FROM `$valueTable` WHERE Key_name = 'PRIMARY'";
        $result     = $db->query($sql);
        $primaryKey = $result->fields['Column_name'];

        $insertSql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `old_val_id`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?,?)";
        if($valueToDeleteId > 1000) {
            //Custom delete
            $type = 'CUSTOM_DELETED';
            $db->pquery($insertSql, [$agentid, $fieldId, '', $type, $valueToDeleteId, $currentTime, $currentTime, $userId]);

            $sql = "SELECT `value` AS $pickListFieldName FROM `vtiger_picklistexceptions` WHERE `id` = ?";
        } else {
            //Standard item delete
            $type = 'DELETED';
            $db->pquery($insertSql, [$agentid, $fieldId, '', $type, $valueToDeleteId, $currentTime, $currentTime, $userId]);

            $sql = "SELECT `$pickListFieldName` FROM `$valueTable` WHERE `$primaryKey` = ?";
        }
        $result = $db->pquery($sql, [$valueToDeleteId]);
        $oldValue = $result->fields[$pickListFieldName];

        //Get the value of the replacement valueid and populate records
        if($replaceValueId > 1000) {
            $sql = "SELECT `value` AS $pickListFieldName FROM `vtiger_picklistexceptions` WHERE `id` = ?";
        } else {
            $sql = "SELECT `$pickListFieldName` FROM `$valueTable` WHERE `$primaryKey` = ?";
        }
        $result = $db->pquery($sql, [$replaceValueId]);
        $newValue = $result->fields[$pickListFieldName];

        $this->updateRecords($agentid, $fieldModel, $oldValue, $newValue);

        $sql = "SELECT fieldid FROM `vtiger_field` WHERE fieldname=? AND fieldid != ? AND uitype=1500 AND presence != 1";
        $result = $db->pquery($sql, [$fieldModel->getName(), $fieldModel->getId()]);
        while($row =& $result->fetchRow()) {
            $fieldModel = Vtiger_Field_Model::getInstance($row['fieldid']);
            if($type == 'DELETED') {
                $db->pquery($insertSql, [$agentid, $row['fieldid'], '', $type, $valueToDeleteId, $currentTime, $currentTime, $currentUser->getId()]);
            } else {
                //lookup custom value id to delete
                $lookupSql = "SELECT `agentid`,`value` FROM `vtiger_picklistexceptions` WHERE `id`=?";
                $valueLookupResult = $db->pquery($lookupSql, [$valueToDeleteId]);
                if($valueLookupResult) {
                    $lookupSql      = "SELECT `id` FROM `vtiger_picklistexceptions` WHERE `agentid`=? AND `fieldid`=? AND `value`=?";
                    $idLookupResult = $db->pquery($lookupSql, [$valueLookupResult->fields['agentid'], $row['fieldid'], $valueLookupResult->fields['value']]);
                    if($idLookupResult) {
                        $db->pquery($insertSql, [$agentid, $row['fieldid'], '', $type, $idLookupResult->fields['id'], $currentTime, $currentTime, $currentUser->getId()]);
                        $this->updateRecords($agentid, $fieldModel, $oldValue, $newValue);
                    }
                }
            }
        }

        return true;
    }

    protected function removeCustomPicklistTableValueid($fieldid,$valuesToDelete,$agentid){
        $db = PearDatabase::getInstance();
        $sql = "DELETE FROM vtiger_custompicklist WHERE fieldid = ? AND agentid = ? AND valueid IN (".generateQuestionMarks($valuesToDelete).")";
        $result = $db->pquery($sql, array($fieldid,$agentid,$valuesToDelete));
    }

    public function getPickListTableName($fieldName)
    {
        return 'vtiger_'.$fieldName;
    }

    public static function getFieldsForModule($moduleName = '',$onlyCustomPicklistFields = true){
        $fields = array();
        $tabId = null;
        if ( $moduleName && $moduleName != '' ) {
            $tabId = getTabid($moduleName);
        }
        if ( ! $tabId ) {
            return;
        }
        if($onlyCustomPicklistFields){
            $uitypes = array(1500);
        }else{
            $uitypes = array(1500,16);
        }
        $db = PearDatabase::getInstance();
        $sql = 'select vtiger_field.fieldid, vtiger_field.fieldname, vtiger_field.fieldlabel from vtiger_field where uitype IN ('. generateQuestionMarks($uitypes).') and vtiger_field.presence in (0,2) and vtiger_field.tabid = ? order by vtiger_field.fieldname ASC';
        $result = $db->pquery($sql,array($uitypes,$tabId));
        if ( $db->num_rows($result) > 0 ) {
            while ( $row = $db->fetch_array($result) ) {
                $fields[$row['fieldname']]['fieldlabel'] = vtranslate($row['fieldlabel'], $moduleName);
                $fields[$row['fieldname']]['fieldid'] = $row['fieldid'];
            }
        }
        $options = "<option value='default'>--Select Field--</option>";
        foreach ($fields as $fieldname  => $data) {
            $options .= "<option value=" . $data['fieldid'] . " data-fieldname=" . $fieldname . ">" . $data['fieldlabel'] . "</option>";
        }

        return $options;
    }

    public function renamePickListValues($fieldid, $oldValue, $newValue, $id, $agentid)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUser->getId();
        $fieldModel = Vtiger_Field_Model::getInstance($fieldid);
        $currentTime = date('Y-m-d H:i:s');
        $db = PearDatabase::getInstance();
        $insertSql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `old_val_id`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?,?)";
        $secondaryInsertSql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
        if($id > 1000) {
            //Custom value
            //Add DELETED exception
            //Add ADDED exception
            $custom = true;
            $db->pquery($insertSql, [$agentid, $fieldid, '', 'CUSTOM_DELETED', $id, $currentTime, $currentTime, $userId]);
            $db->pquery($secondaryInsertSql, [$agentid, $fieldid, $newValue, 'ADDED', $currentTime, $currentTime, $userId]);
        } else {
            //Default value
            //Add RENAMED exception
            $custom = false;
            $db->pquery($insertSql, [$agentid, $fieldid, $newValue, 'RENAMED', $id, $currentTime, $currentTime, $userId]);
        }

        $this->updateRecords($agentid, $fieldModel, $oldValue, $newValue);

        $sql = "SELECT fieldid FROM `vtiger_field` WHERE fieldname=? AND fieldid != ? AND uitype=1500 AND presence != 1";
        $result = $db->pquery($sql, [$fieldModel->getName(), $fieldModel->getId()]);
        while($row =& $result->fetchRow()) {
            $fieldModel = Vtiger_Field_Model::getInstance($row['fieldid']);
            if(!$fieldModel) {
                continue;
            }
            if($custom) {
                //lookup custom value id to delete
                $lookupSql = "SELECT `agentid`, `value` FROM `vtiger_picklistexceptions` WHERE `id`=?";
                $valueLookupResult = $db->pquery($lookupSql, [$id]);
                if($valueLookupResult) {
                    $lookupSql      = "SELECT `id` FROM `vtiger_picklistexceptions` WHERE `agentid`=? AND `fieldid`=? AND `value`=?";
                    $idLookupResult = $db->pquery($lookupSql, [$valueLookupResult->fields['agentid'], $row['fieldid'], $valueLookupResult->fields['value']]);
                    if($idLookupResult) {
                        $db->pquery($insertSql, [$agentid, $row['fieldid'], '', 'CUSTOM_DELETED', $idLookupResult->fields['id'], $currentTime, $currentTime, $userId]);
                        $db->pquery($secondaryInsertSql, [$agentid, $row['fieldid'], $newValue, 'ADDED', $currentTime, $currentTime, $userId]);
                    }
                }
            } else {
                $db->pquery($insertSql, [$agentid, $row['fieldid'], $newValue, 'RENAMED', $id, $currentTime, $currentTime, $userId]);
            }
            $this->updateRecords($agentid, $fieldModel, $oldValue, $newValue);
        }

        return true;
    }

    private function updateRecords($agentid, $fieldModel, $oldValue, $newValue) {
        //Update records for the corresponding agent/vanline
        $db = PearDatabase::getInstance();
        $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
        $result = $db->pquery($sql, [$agentid]);
        if($result->fields['setype'] == 'VanlineManager') {
            $isVanline = true;
        } else {
            $isVanline = false;
        }

        $ownerAgents = [$agentid];

        if($isVanline) {
            $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id=?";
            $result = $db->pquery($sql, [$agentid]);
            while($row =& $result->fetchRow()) {
                $ownerAgents[] = $row['agentmanagerid'];
            }
        }

        $module = $fieldModel->getModule();
        $moduleName = $module->getName();
        $entity = CRMEntity::getInstance($moduleName);
        $tablename = $fieldModel->get('table');
        $idColumn  = $entity->table_index;
        $fieldColumn = $fieldModel->get('column');

        $sql = "SELECT crmid FROM `vtiger_crmentity` JOIN `$tablename` ON `$tablename`.`$idColumn` = `vtiger_crmentity`.`crmid` WHERE deleted=0 AND agentid IN (".generateQuestionMarks($ownerAgents).") AND `$fieldColumn`=?";
        $result = $db->pquery($sql, [$ownerAgents, $oldValue]);
        while($row =& $result->fetchRow()) {
            $recordModel = Vtiger_Record_Model::getInstanceById($row['crmid'], $moduleName);
            $recordModel->set($fieldModel->getName(), $newValue);
            $recordModel->set('mode', 'edit');
            $recordModel->save();
        }
    }
}
