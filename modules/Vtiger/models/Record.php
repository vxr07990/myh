<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class Vtiger_Record_Model extends Vtiger_Base_Model
{
    protected $module = false;

    /**
     * Function to get the id of the record
     * @return <Number> - Record Id
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * Function to set the id of the record
     *
     * @param  <type> $value - id value
     *
     * @return <Object> - current instance
     */
    public function setId($value)
    {
        return $this->set('id', $value);
    }

    /**
     * Fuction to get the Name of the record
     * @return <String> - Entity Name of the record
     */
    public function getName()
    {
        $displayName = $this->get('label');
        if (empty($displayName)) {
            $displayName = $this->getDisplayName();
        }

        return Vtiger_Util_Helper::toSafeHTML(decode_html($displayName));
    }

    /**
     * Function to get the Module to which the record belongs
     * @return Vtiger_Module_Model
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Function to set the Module to which the record belongs
     *
     * @param <String> $moduleName
     *
     * @return Vtiger_Record_Model or Module Specific Record Model instance
     */
    public function setModule($moduleName)
    {
        $this->module = Vtiger_Module_Model::getInstance($moduleName);

        return $this;
    }

    /**
     * Function to set the Module to which the record belongs from the Module model instance
     *
     * @param <Vtiger_Module_Model> $module
     *
     * @return Vtiger_Record_Model or Module Specific Record Model instance
     */
    public function setModuleFromInstance($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Function to get the entity instance of the recrod
     * @return CRMEntity object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Function to set the entity instance of the record
     *
     * @param CRMEntity $entity
     *
     * @return Vtiger_Record_Model instance
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Function to get raw data
     * @return <Array>
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Function to set raw data
     *
     * @param <Array> $data
     *
     * @return Vtiger_Record_Model instance
     */
    public function setRawData($data)
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * Function to get the Detail View url for the record
     * @return <String> - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId();
    }

    /**
     * Function to get the complete Detail View url for the record
     * @return <String> - Record Detail View Url
     */
    public function getFullDetailViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId().'&mode=showDetailViewByMode&requestMode=full';
    }

    /**
     * Function to get the Edit View url for the record
     * @return <String> - Record Edit View Url
     */
    public function getEditViewUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view='.$module->getEditViewName().'&record='.$this->getId();
    }

    /**
     * Function to get the Duplicate url for the record
     * @return String URL
     */
    public function getDuplicateUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view=MassActionAjax&mode=duplicateRecords&selected_ids=['.$this->getId().']';
    }

    /**
     * Function to get the Update View url for the record
     * @return <String> - Record Upadte view Url
     */
    public function getUpdatesUrl()
    {
        return $this->getDetailViewUrl()."&mode=showRecentActivities&page=1&tab_label=LBL_UPDATES";
    }

    /**
     * Function to get the Delete Action url for the record
     * @return <String> - Record Delete Action Url
     */
    public function getDeleteUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&action='.$module->getDeleteActionName().'&record='.$this->getId();
    }

    /**
     * Function to get the name of the module to which the record belongs
     * @return <String> - Record Module Name
     */
    public function getModuleName()
    {
        return $this->getModule()->get('name');
    }

    /**
     * Function to get the Display Name for the record
     * @return <String> - Entity Display Name for the record
     */
    public function getDisplayName()
    {
        return Vtiger_Util_Helper::getLabel($this->getId());
    }

    /**
     * Function to retieve display value for a field
     *
     * @param  <String> $fieldName - field name for which values need to get
     *
     * @return <String>
     */
    public function getDisplayValue($fieldName, $recordId = false)
    {
        global $current_user;

        if (empty($recordId)) {
            $recordId = $this->getId();
        }
        $fieldModel = $this->getModule()->getField($fieldName);
        // For showing the "Date Sent" and "Time Sent" in email related list in user time zone
        if ($fieldName == "time_start" && $this->getModule()->getName() == "Emails") {
            $date     = new DateTime();
            $dateTime = new DateTimeField($date->format('Y-m-d').' '.$this->get($fieldName));
            $value    = $dateTime->getDisplayTime($current_user, true);
            $this->set($fieldName, $value);

            return $value;
        } elseif ($fieldName == "date_start" && $this->getModule()->getName() == "Emails") {
            $dateTime = new DateTimeField($this->get($fieldName).' '.$this->get('time_start'));
            $value    = $dateTime->getDisplayDate($current_user);
            $this->set($fieldName, $value);

            return $value;
        }
        // End
        if ($fieldModel) {
            return $fieldModel->getDisplayValue($this->get($fieldName), $recordId, $this);
        }

        return false;
    }

    /**
     * Function returns the Vtiger_Field_Model
     *
     * @param  <String> $fieldName - field name
     *
     * @return <Vtiger_Field_Model>
     */
    public function getField($fieldName)
    {
        return $this->getModule()->getField($fieldName);
    }

    /**
     * Function returns all the field values in user format
     * @return <Array>
     */
    public function getDisplayableValues()
    {
        $displayableValues = [];
        $data              = $this->getData();
        foreach ($data as $fieldName => $value) {
            $fieldValue                    = $this->getDisplayValue($fieldName);
            $displayableValues[$fieldName] = ($fieldValue)?$fieldValue:$value;
        }

        return $displayableValues;
    }

    /**
     * Function to save the current Record Model
     */
    public function save()
    {
        //file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Entering Vtiger_Record::save\n", FILE_APPEND);
        //file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Preparing to call saveRecord for ".print_r($this->getModule(), true)."\n", FILE_APPEND);
        $this->getModule()->saveRecord($this);
        //file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Exiting Vtiger_Record::save\n", FILE_APPEND);
    }

    /**
     * Function to delete the current Record Model
     */
    public function delete()
    {
        $this->getModule()->deleteRecord($this);
        return true;
    }

    /**
     * Static Function to get the instance of a clean Vtiger Record Model for the given module name
     *
     * @param <String> $moduleName
     *
     * @return Vtiger_Record_Model or Module Specific Record Model instance
     */
    public static function getCleanInstance($moduleName)
    {
        //TODO: Handle permissions
        $focus          = CRMEntity::getInstance($moduleName);
        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
        $instance       = new $modelClassName();

        return $instance->setData($focus->column_fields)->setModule($moduleName)->setEntity($focus);
    }

    /**
     * Static Function to get the instance of the V
     * tiger Record Model given the recordid and the module name
     *
     * @param <Number> $recordId
     * @param <String> $moduleName
     *
     * @return Vtiger_Record_Model or Module Specific Record Model instance
     */
    public static function getInstanceById($recordId, $module = null)
    {

        //TODO: Handle permissions
        if (is_object($module) && is_a($module, 'Vtiger_Module_Model')) {
            $moduleName = $module->get('name');
        } elseif (is_string($module)) {
            $module     = Vtiger_Module_Model::getInstance($module);
            $moduleName = $module->get('name');
        } elseif (empty($module)) {
            $moduleName = getSalesEntityType($recordId);
            $module     = Vtiger_Module_Model::getInstance($moduleName);
        }

        //@TODO: caching the record model does help speed, HOWEVER, changes to the record model aren't properly reflected
        // so this needs some work figuring out where and why that step is getting missed.
        //Performance patch 11/20/2016
        //$instance = Vtiger_Cache::get('instance', $moduleName.$recordId);

        //if (!$instance || $moduleName == 'Tariffs') {
            $focus = CRMEntity::getInstance($moduleName);
            if ($focus) {
                $focus->id = $recordId;
                $focus->retrieve_entity_info($recordId, $moduleName);
            }
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
            $instance       = new $modelClassName();

            $instance = $instance->setData($focus->column_fields)->set('id', $recordId)->setModuleFromInstance($module)->setEntity($focus);

            //Vtiger_Cache::set('instance', $moduleName.$recordId, $instance);
        //}

        return $instance;
    }

    public static function getRecordAsArray($recordId, $moduleInstance = null)
    {
      $instance = Vtiger_Record_Model::getInstanceById($recordId,$moduleInstance);

      if($instance && ($moduleInstance == null || gettype($moduleInstance) == 'string' )) {
        $moduleInstance = $instance->getModule();
      }

      if(!$moduleInstance) {
        return;
      }

      $vals = [];

      foreach($moduleInstance->getFields() as $field) {
        $vals[$field->getName()] = $instance->get($field->getName());
      }

      return $vals;
    }

    /**
     * Static Function to get the list of records matching the search key
     *
     * @param  <String> $searchKey
     *
     * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
     */
    public static function getSearchResult($searchKey, $module = false, $startLimit = 0, $searchField = null)
    {
        $db = PearDatabase::getInstance();
        if($searchField == 'carrier_scac_code') {
            $query = 'SELECT scac_code as label, crmid, setype, createdtime FROM `vtiger_crmentity` JOIN `vtiger_carriers` ON crmid=carriersid WHERE scac_code LIKE ? AND vtiger_crmentity.deleted=0';
        } else if($module == 'Tariffs') {
            $query = "SELECT label, crmid, setype, createdtime FROM `vtiger_crmentity` JOIN `vtiger_tariffs` ON `vtiger_crmentity`.crmid=`vtiger_tariffs`.tariffsid WHERE label LIKE ? AND `vtiger_crmentity`.deleted=0 AND (vtiger_tariffs.`tariff_status` != 'Inactive' OR vtiger_tariffs.`tariff_status` IS NULL)";
        } else {
        $query  = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
        }
        //Cubesheets don't have an owner and need to be available to participating agents. Removing from global search for now.
        $query .= ' AND setype != "Cubesheets" ';
        $params = ["%$searchKey%"];
        if ($module !== false) {
            $query .= ' AND setype = ?';
            $params[] = $module;
        }

        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if ($_REQUEST['agentId']){
                $accessible = array($_REQUEST['agentId']);
                $result          =
                        $db->pquery("SELECT vanline_id FROM vtiger_agentmanager INNER JOIN vtiger_crmentity ON vtiger_agentmanager.agentmanagerid = vtiger_crmentity.crmid "
                                . "WHERE agentmanagerid IN (". implode(',', $accessible). ")");
                    $accessibleAgents = [];
                    if ($result && $db->num_rows($result) > 0) {
                        while ($row = $db->fetch_row($result)) {
                            $accessibleAgents[] = $row['vanline_id'];
                        }
                        $accessible = array_merge($accessibleAgents,$accessible);
                    }
                $query .= ' AND vtiger_crmentity.agentid IN ('.implode(',', $accessible).')';
            }
        }

        $query .= ' ORDER BY crmid DESC';
        $query .= " LIMIT $startLimit,100";

        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';
        $result   = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);
        $moduleModels = $matchingRecords = $leadIdsList = [];
        for ($i = 0; $i < $noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $leadIdsList[] = $row['crmid'];
            }
        }
        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);
        $recordsCount = 0;

        if($noOfRows == 100 && $startLimit == 0)
        {
            while($recordsCount < 100) {
                $startLimit += 100;
                $nextMatchingRecords = self::getSearchResult($searchKey, $module, $startLimit);
                if(count($nextMatchingRecords) == 0)
                {
                    break;
                }
                foreach ($nextMatchingRecords as $moduleName => $ids) {
                    foreach ($ids as $id => $rec) {
                        $matchingRecords[$moduleName][$id] = $rec;
                        $recordsCount++;
                        if($recordsCount == 100)
                        {
                            break;
                        }
                    }
                }
            }
        }

        for ($i = 0, $recordsCount = 0; $i < $noOfRows && $recordsCount < 100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                continue;
            }
            if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                $row['id']  = $row['crmid'];
                $moduleName = $row['setype'];
                if (!array_key_exists($moduleName, $moduleModels)) {
                    file_put_contents('logs/nm.log', "\n\tARRAY KEY EXISTS!", FILE_APPEND);
                    $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                }
                $moduleModel                              = $moduleModels[$moduleName];
                $modelClassName                           = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                $recordInstance                           = new $modelClassName();
                $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                $recordsCount++;
            }
        }

        return $matchingRecords;
    }

    /**
     * Function to get details for user have the permissions to do actions
     * @return <Boolean> - true/false
     */
    public function isEditable()
    {
        return Users_Privileges_Model::isPermitted($this->getModuleName(), 'EditView', $this->getId());
    }

    /**
     * Function to get details for user have the permissions to do actions
     * @return <Boolean> - true/false
     */
    public function isDeletable()
    {
        return Users_Privileges_Model::isPermitted($this->getModuleName(), 'Delete', $this->getId());
    }

    /**
     * Funtion to get Duplicate Record Url
     * @return <String>
     */
    public function getDuplicateRecordUrl()
    {
        $module = $this->getModule();

        return 'index.php?module='.$this->getModuleName().'&view='.$module->getEditViewName().'&record='.$this->getId().'&isDuplicate=true';
    }

    /**
     * Function to get Display value for RelatedList
     *
     * @param  <String> $value
     *
     * @return <String>
     */
    public function getRelatedListDisplayValue($fieldName)
    {
        $fieldModel = $this->getModule()->getField($fieldName);

        return $fieldModel->getRelatedListDisplayValue($this->get($fieldName));
    }

    /**
     * Function to delete corresponding image
     *
     * @param <type> $imageId
     */
    public function deleteImage($imageId)
    {
        $db = PearDatabase::getInstance();
        $checkResult = $db->pquery('SELECT crmid FROM vtiger_seattachmentsrel WHERE attachmentsid = ?', [$imageId]);
        $crmId       = $db->query_result($checkResult, 0, 'crmid');
        if ($this->getId() === $crmId) {
            $db->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid = ?', [$imageId]);
            $db->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid = ?', [$imageId]);

            return true;
        }

        return false;
    }

    /**
     * Function to get Descrption value for this record
     * @return <String> Descrption
     */
    public function getDescriptionValue()
    {
        $description = $this->get('description');
        if (empty($description)) {
            $db          = PearDatabase::getInstance();
            $result      = $db->pquery("SELECT description FROM vtiger_crmentity WHERE crmid = ?", [$this->getId()]);
            $description = $db->query_result($result, 0, "description");
        }

        return $description;
    }

    /**
     * Function to transfer related records of parent records to this record
     *
     * @param  <Array> $recordIds
     *
     * @return <Boolean> true/false
     */
    public function transferRelationInfoOfRecords($recordIds = [])
    {
        if ($recordIds) {
            $moduleName = $this->getModuleName();
            $focus      = CRMEntity::getInstance($moduleName);
            if (method_exists($focus, 'transferRelatedRecords')) {
                $focus->transferRelatedRecords($moduleName, $recordIds, $this->getId());
            }
        }

        return true;
    }

    public function getMappingFields($forModuleName)
    {
        return [];
    }

    public function setParentRecordData(Vtiger_Record_Model $parentRecordModel)
    {
        $fieldMappingList = $parentRecordModel->getMappingFields($this->getModuleName());
        foreach ($fieldMappingList as $from => $to) {
            $v = $parentRecordModel->get($from);
            if($v) {
                $this->set($to, $v);
            }
        }
    }

    //@TODO: this needs merged or made use of in:
    //modules/Vtiger/models/Module.php public function getGuestBlockForModule($all = false)
    //modules/Vtiger/models/Record.php public function getGuestModuleRecords($moduleFind = false, $record = false)
    public function getGuestModuleRecords($moduleFind = false, $record = false)
    {
        $guestRecords = [];
        if (!$record) {
            $record = $this->getId();
        }
        $db = PearDatabase::getInstance();
        $stmt = "SELECT guestmodule FROM `vtiger_guestmodulerel` WHERE active = 1 AND hostmodule = ? GROUP BY guestmodule";
        $params = [$this->getModuleName()];

        if ($moduleFind) {
            $stmt = "SELECT guestmodule FROM `vtiger_guestmodulerel` WHERE active = 1 AND hostmodule = ? AND guestmodule = ? GROUP BY guestmodule";
            $params = [$this->getModuleName(), $moduleFind];
        }

        $result = $db->pquery($stmt, $params);

        $guestModules = [];
        while ($row =& $result->fetchRow()) {
            $guestModules[] = $row['guestmodule'];
        }
        foreach ($guestModules as $key => $guestModuleName) {
            $guestModuleModel = Vtiger_Module_Model::getInstance($guestModuleName);
            if ($guestModuleModel && $guestModuleModel->isActive()) {
                $linkColumn = $guestModuleModel->getLinkColumn($this->getModuleName());
                $idFieldName = $guestModuleModel->basetableid;
                $baseTableName = $guestModuleModel->basetable;

                $stmt = 'SELECT * FROM `' . $baseTableName . '`'
                        . ' INNER JOIN `vtiger_crmentity` ON (`' . $baseTableName . '`.`'. $idFieldName . '` = `vtiger_crmentity`.`crmid`)'
                        . ' WHERE '
                        //. ' `vtiger_crmentity`.`deleted` = 0' //the function below is doing this.
                        . $guestModuleModel->getDeletedRecordCondition()
                        . ' AND `'. $baseTableName .'`.`' . $linkColumn . '` = ?';
                $guestRows = $db->pquery($stmt, [$record]);

                if (method_exists($guestRows, 'fetchRow')) {
                    while ($guestRow = $guestRows->fetchRow()) {
                        $guestRecords[$guestRow[$idFieldName]] = Vtiger_Record_Model::getInstanceById($guestRow[$idFieldName]);
                    }
                }
            }
        }
        return $guestRecords;
    }

    //@NOTE this is "Documents" not "Document Attachments".
    //retrieve the Documents related to this record, override with $record.
    public function getDocumentIds($record = false)
    {
        $rv = [];
        if (!$record) {
            $record = $this->getId();
        }

        if ($record) {
            //pull the related document id's for this order.
            $db         = PearDatabase::getInstance();
            $relatedSql = 'SELECT `vtiger_crmentity`.`crmid` FROM `vtiger_crmentity`
                          LEFT JOIN `vtiger_senotesrel` ON `vtiger_crmentity`.`crmid` = `vtiger_senotesrel`.`notesid`
                          WHERE `vtiger_senotesrel`.`crmid` = ?
                          AND `vtiger_crmentity`.`deleted` = 0';
            $result     = $db->pquery($relatedSql, [$record]);
            if (method_exists($result, 'fetchRow')) {
                while ($row = $result->fetchRow()) {
                    $rv[$row['crmid']] = 1;
                }
            }
        }
        return array_keys($rv);
    }

    //@TODO: refactor this into the existing stuff that does this on it's own.
    //Being the change that I want to see.
    //setting getAny to true will return the first estimate assigned to the opportunity.
    //changed to be always getAny instead of having to pass true;
    public function getPrimaryEstimateRecordModel($getAny = true, $setype = 'Estimates', $relatedField = 'potentialid')
    {

        $estimateRecordModel = false;
        $db                  = &PearDatabase::getInstance();

        $stmt   = "SELECT `quoteid`,`effective_tariff` FROM `vtiger_quotes`"
                  ." INNER JOIN `vtiger_crmentity` ON (`vtiger_quotes`.`quoteid` = `vtiger_crmentity`.`crmid`)"
                  ." WHERE `vtiger_quotes`.`".$relatedField."` = ? "
                  ." AND `vtiger_quotes`.`is_primary` = '1'"
                  ." AND `vtiger_crmentity`.`deleted` = 0"
                  ." AND `vtiger_crmentity`.`setype` = ?"
                  ." ORDER BY `vtiger_crmentity`.`modifiedtime` DESC"
                  ." LIMIT 1";
        $result = $db->pquery($stmt, [$this->getId(), $setype]);

        if (
            method_exists($result, 'fetchRow') &&
            $row = $result->fetchRow()
        ) {
            $estimateID      = $row['quoteid'];
            $effectiveTariff = $row['effective_tariff'];
        } elseif ($getAny) {
            $stmt   = "SELECT `quoteid`,`effective_tariff` FROM `vtiger_quotes`"
                      ." INNER JOIN `vtiger_crmentity` ON (`vtiger_quotes`.`quoteid` = `vtiger_crmentity`.`crmid`)"
                      ." WHERE `vtiger_quotes`.`".$relatedField."` = ? "
                      ." AND `vtiger_crmentity`.`deleted` = 0"
                      ." AND `vtiger_crmentity`.`setype` = ?"
                      ." ORDER BY `vtiger_crmentity`.`modifiedtime` DESC"
                      ." LIMIT 1";
            $result = $db->pquery($stmt, [$this->getId(), $setype]);
            if (
                method_exists($result, 'fetchRow') &&
                $row = $result->fetchRow()
            ) {
                $estimateID      = $row['quoteid'];
                $effectiveTariff = $row['effective_tariff'];
            }
        }

        if ($estimateID) {
            try {
                $estimateRecordModel = Vtiger_Record_Model::getInstanceById($estimateID, $setype);
                //because this isn't part of the returned record.
                $estimateRecordModel->set('effective_tariff', $effectiveTariff);
            } catch (WebServiceException $ex) {
                //@NOTE: Not really necessary to make this distinction.
                $estimateRecordModel = false;
            } catch (Exception $ex) {
                $estimateRecordModel = false;
            }
        }

        return $estimateRecordModel;
    }

    public function getPrimaryEstimateRecordId($getAny = true) {
        $primaryEstimateRecordModel = $this->getPrimaryEstimateRecordModel($getAny);
        if ($primaryEstimateRecordModel) {
            return $primaryEstimateRecordModel->getId();
        }
        return false;
    }

    public function getPrimaryActualRecordModel($getAny = true) {
        return $this->getPrimaryEstimateRecordModel($getAny, 'Actuals');
    }

    public function getPrimaryActualRecordId($getAny = true) {
        $primaryActualRecordModel = $this->getPrimaryActualRecordModel($getAny);
        if ($primaryActualRecordModel) {
            return $primaryActualRecordModel->getId();
        }
        return false;
    }
public function checkDuplicate() {
        $db = PearDatabase::getInstance();
        $moduleName=$this->getModule()->getName();
        $focus=CRMEntity::getInstance($moduleName);

        $query = "SELECT 1 FROM vtiger_crmentity
            INNER JOIN {$focus->table_name} ON {$focus->table_name}.{$focus->table_index} = vtiger_crmentity.crmid";
        // Consider custom table join as well.
        if (!empty($focus->customFieldTable)) {
            $query .= " INNER JOIN ".$focus->customFieldTable[0]." ON ".$focus->customFieldTable[0].'.'.$focus->customFieldTable[1] .
                      " = $focus->table_name.$focus->table_index";
        }
        $query .= " WHERE setype = ? AND deleted = 0";
        $params = array($moduleName);

        $fields=$this->get('fields');
        foreach($fields as $fldName => $fldVal) {
            $query .=" AND $fldName=?";
            array_push($params,$fldVal);
        }

        $record = $this->getId();
        if ($record) {
            $query .= " AND crmid != ?";
            array_push($params, $record);
        }

        $result = $db->pquery($query, $params);

        if ($db->num_rows($result)) {
            return true;
        }
        return false;
    }

}
