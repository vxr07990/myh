<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~/modules/ModTracker/core/ModTracker_Basic.php');

use Carbon\Carbon;

class ModTracker_Record_Model extends Vtiger_Record_Model
{
    const UPDATE = 0;
    const DELETE = 1;
    const CREATE = 2;
    const RESTORE = 3;
    const LINK = 4;
    const UNLINK = 5;

    /**
     * Function to get the history of updates on a record
     * @param <type> $record - Record model
     * @param <type> $limit - number of latest changes that need to retrieved
     * @return <array> - list of  ModTracker_Record_Model
     */
    public static function getUpdates($parentRecordId, $pagingModel)
    {
        $db = PearDatabase::getInstance();
        $recordInstances = array();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery = "SELECT * FROM vtiger_modtracker_basic WHERE crmid = ? ".
                        " ORDER BY changedon DESC LIMIT $startIndex, $pageLimit";

        $result = $db->pquery($listQuery, array($parentRecordId));
        $rows = $db->num_rows($result);

        for ($i=0; $i<$rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $recordInstance = new self();
            $recordInstance->setData($row)->setParent($row['crmid'], $row['module']);
            $recordInstances[] = $recordInstance;
        }
        return $recordInstances;
    }

    public function setParent($id, $moduleName)
    {
        $this->parent = Vtiger_Record_Model::getInstanceById($id, $moduleName);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function checkStatus($callerStatus)
    {
        $status = $this->get('status');
        if ($status == $callerStatus) {
            return true;
        }
        return false;
    }

    public function isCreate()
    {
        return $this->checkStatus(self::CREATE);
    }

    public function isUpdate()
    {
        return $this->checkStatus(self::UPDATE);
    }

    public function isDelete()
    {
        return $this->checkStatus(self::DELETE);
    }

    public function isRestore()
    {
        return $this->checkStatus(self::RESTORE);
    }

    public function isRelationLink()
    {
        return $this->checkStatus(self::LINK);
    }

    public function isRelationUnLink()
    {
        return $this->checkStatus(self::UNLINK);
    }

    public function getModifiedBy()
    {
        $changeUserId = $this->get('whodid');
        return Users_Record_Model::getInstanceById($changeUserId, 'Users');
    }

    public function getActivityTime()
    {
        return $this->get('changedon');
    }

    public function getFieldInstances()
    {
        $id = $this->get('id');
        $db = PearDatabase::getInstance();
        global $current_user;

        $fieldInstances = array();
        if ($this->isCreate() || $this->isUpdate()) {
            $result = $db->pquery('SELECT * FROM vtiger_modtracker_detail WHERE id = ?', array($id));
            $rows = $db->num_rows($result);
            for ($i=0; $i<$rows; $i++) {
                $data = $db->query_result_rowdata($result, $i);
                $row = array_map('html_entity_decode', $data);

                if ($row['fieldname'] == 'record_id' || $row['fieldname'] == 'record_module') {
                    continue;
                }

                $fieldModel = Vtiger_Field_Model::getInstance($row['fieldname'], $this->getParent()->getModule());
                if (!$fieldModel) {
                    continue;
                }

                $typeofdataPieces = explode('~', $fieldModel->get('typeofdata'));
                if($fieldModel->get('uitype') == 70) {
                    //Special case for createdtime and modifiedtime
                    $timeZone = $current_user->time_zone;

                    if($row['prevalue']) {
                        $date = DateTimeField::convertToUserTimeZone($row['prevalue']);
                        $row['prevalue'] = $date->format('Y-m-d H:i:s');
                    }

                    $date = DateTimeField::convertToUserTimeZone($row['postvalue']);
                    $row['postvalue'] = $date->format('Y-m-d H:i:s');

                    $timeZoneObject = new DateTimeZone($timeZone);
                    $offset = $timeZoneObject->getOffset($date) / 3600; //offset gets returned in seconds - converting to hours
                    $timeZoneDisplay = $offset < 0 ? '-' : '+';
                    $offset = abs($offset);
                    if($timeZoneObject->getOffset($date) % 3600 == 0) {
                        $timeZoneDisplay .= $offset.':00';
                    } else {
                        $timeZoneDisplay .= $offset.':30';
                    }
                    $row['timezone'] = ' (UTC' . $timeZoneDisplay . ')';
                } else if($typeofdataPieces[0] == 'DT') {
                    if(count($typeofdataPieces) > 3) {
                        if($typeofdataPieces[2] == 'REL') {
                            $timeField = $typeofdataPieces[3];
                            $timeZone=getFieldTimeZoneValue($timeField, $this->get('crmid'));
                            if(!$timeZone) {
                                $timeZone = $current_user->time_zone;
                            }

                            if($row['prevalue']) {
                                $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $row['prevalue'].' '.$this->getParent()->get($timeField), DateTimeField::getDBTimeZone());
                                $carbonTime->setTimezone($timeZone);
                                $row['prevalue'] = $carbonTime->format('Y-m-d');
                            }

                            $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $row['postvalue'].' '.$this->getParent()->get($timeField), DateTimeField::getDBTimeZone());
                            $carbonTime->setTimezone($timeZone);
                            $row['postvalue'] = $carbonTime->format('Y-m-d');
                        }
                    }
                } else if ($typeofdataPieces[0] == 'T') {
                    $timeZone=getFieldTimeZoneValue($row['fieldname'], $this->get('crmid'));
                    if(!$timeZone) {
                        $timeZone = $current_user->time_zone;
                    }

                    if(count($typeofdataPieces) > 3 && $typeofdataPieces[2] == 'REL') {
                        $dateField = $typeofdataPieces[3];
                    }

                    if($row['prevalue']) {
                        $carbonTime = new Carbon($this->getValueFromModTrackerRecord($dateField, 'prevalue').' '.$row['prevalue'], DateTimeField::getDBTimeZone());
                        $carbonTime->setTimezone($timeZone);
                        $row['prevalue'] = $carbonTime->format('H:i:s');
                    }

                    $carbonTime = new Carbon($this->getValueFromModTrackerRecord($dateField, 'postvalue').' '.$row['postvalue'], DateTimeField::getDBTimeZone());
                    $carbonTime->setTimezone($timeZone);
                    $row['postvalue'] = $carbonTime->format('H:i:s');

                    $timeZoneObject = new DateTimeZone($timeZone);
                    $offset = $timeZoneObject->getOffset(new DateTime($carbonTime->format('Y-m-d H:i:s'))) / 3600; //offset gets returned in seconds - converting to hours
                    $timeZoneDisplay = $offset < 0 ? '-' : '+';
                    $offset = abs($offset);
                    if($timeZoneObject->getOffset(new DateTime($carbonTime->format('Y-m-d H:i:s'))) % 3600 == 0) {
                        $timeZoneDisplay .= $offset.':00';
                    } else {
                        $timeZoneDisplay .= $offset.':30';
                    }
                    $row['timezone'] = ' (UTC' . $timeZoneDisplay . ')';
                }

                $fieldInstance = new ModTracker_Field_Model();
                $fieldInstance->setData($row)->setParent($this)->setFieldInstance($fieldModel);
                $fieldInstances[] = $fieldInstance;
            }
        }
        return $fieldInstances;
    }

    public function getRelationInstance()
    {
        $id = $this->get('id');
        $db = PearDatabase::getInstance();

        if ($this->isRelationLink() || $this->isRelationUnLink()) {
            $result = $db->pquery('SELECT * FROM vtiger_modtracker_relations WHERE id = ?', array($id));
            $row = $db->query_result_rowdata($result, 0);
            $relationInstance = new ModTracker_Relation_Model();
            $relationInstance->setData($row)->setParent($this);
        }
        return $relationInstance;
    }

    public function getTotalRecordCount($recordId)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT COUNT(*) AS count FROM vtiger_modtracker_basic WHERE crmid = ?", array($recordId));
        return $db->query_result($result, 0, 'count');
    }

    private function getValueFromModTrackerRecord($fieldName, $type) {
        //Attempt to lookup changes to $fieldName that occurred within this ModTracker record. If none exists, fallback to parent value.
        if($type == 'prevalue' || $type == 'postvalue') {
            $db  = PearDatabase::getInstance();
            $sql = "SELECT $type FROM `vtiger_modtracker_detail` WHERE id=? AND fieldname=?";
            $result = $db->pquery($sql, [$this->getId(), $fieldName]);
            if($db->num_rows($result) > 0) {
                return $result->fields[$type];
            }
        }
        //Fallback to parent
        return $this->getParent()->get($fieldName);
    }
}
