<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Contracts_Save_Action extends Vtiger_Save_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        if (!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        parent::process($request);
        $recordId = $request->get('record');

        // Notify moveCRMSync service OT 2449 - REMOVED DUE TO ERROR IN OT ENTRY - CONTACTS INSTEAD OF CONTRACTS
        if (!isset($recordId)) {
            //Error case:
            //This is a new record and the expected record id does not match the id of the newly
            //saved record in the database, so do not proceed with custom save functionality
            return;
        }

        //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
        foreach ($request->getAll() as $fieldName=>$value) {
            //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').$fieldName.' - '.$value."\n", FILE_APPEND);
            if (substr($fieldName, 0, 7) == 'Vanline') {
                $vanlineId = strstr(substr($fieldName, 7), 'State', true);
                $applyToAllAgents = $request->get('assignVanline'.$vanlineId.'Agents') == 'on' ? 1 : 0;

                $sql = "SELECT vanlineid, contractid FROM `vtiger_contract2vanline` WHERE vanlineid=? AND contractid=?";
                $result = $db->pquery($sql, array($vanlineId, $recordId));
                $row = $result->fetchRow();

                $params = array();

                if ($row != null && $value == 'unassigned') {
                    //Assignment exists, but should be removed
                    $sql = "DELETE FROM `vtiger_contract2vanline` WHERE vanlineid=? AND contractid=?";
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                } elseif ($row == null && $value == 'assigned') {
                    //Assignment does not exist, but should be added
                    $sql = "INSERT INTO `vtiger_contract2vanline` (vanlineid, contractid, apply_to_all_agents) VALUES (?,?,?)";
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                    $params[] = $applyToAllAgents;
                } elseif ($row != null) {
                    //Assignment exists and should - update apply_to_all_agents column
                    $sql = "UPDATE `vtiger_contract2vanline` SET apply_to_all_agents=? WHERE vanlineid=? AND contractid=?";
                    $params[] = $applyToAllAgents;
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                } else {
                    //Assignment is already in correct state
                    $sql = null;
                }

                if (isset($sql)) {
                    //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').$sql."\n".print_r($params, true)."\n", FILE_APPEND);
                    $result = $db->pquery($sql, $params);
                }
            } elseif (substr($fieldName, 0, 11) == 'assignAgent') {
                preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ').print_r($m, true)."\n", FILE_APPEND);
                $agentId = substr($fieldName, $m[0][1]);

                $sql = "SELECT agentid, contractid FROM `vtiger_contract2agent` WHERE agentid=? AND contractid=?";
                $result = $db->pquery($sql, array($agentId, $recordId));
                $row = $result->fetchRow();

                //file_put_contents('logs/devLog.log', "\n fieldName: ".$fieldName, FILE_APPEND);
                //file_put_contents('logs/devLog.log', "\n value: ".$value, FILE_APPEND);
                //file_put_contents('logs/devLog.log', "\n agentId: ".$agentId, FILE_APPEND);
                //file_put_contents('logs/devLog.log', "\n recordId: ".$recordId, FILE_APPEND);

                if ($row != null && $value == '0') {
                    //Assignment exists, but should be removed
                    $sql = "DELETE FROM `vtiger_contract2agent` WHERE agentid=? AND contractid=?";
                } elseif ($row == null && $value == 'on') {
                    //Assignment does not exist, but should be added
                    $sql = "INSERT INTO `vtiger_contract2agent` (agentid, contractid) VALUES (?,?)";
                } else {
                    //Assignment is already in correct state
                    $sql = null;
                }

                //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$sql."\n", FILE_APPEND);

                if (isset($sql)) {
                    $result = $db->pquery($sql, array($agentId, $recordId));
                }
            }
        }
    }

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
    public function saveRecord($request)
    {
        $recordModel = $this->getRecordModelFromRequest($request);
        $recordModel->set('begin_date', DateTimeField::convertToDBFormat($recordModel->get('begin_date')));
        $recordModel->set('effective_date', DateTimeField::convertToDBFormat($recordModel->get('effective_date')));
        $recordModel->save();

        //store the actual recordId to the request for later use.
        $recordId = $recordModel->getId();
        $request->set('record', $recordId);

        /*-----------------------Save misc items--------------------------------*/

        /* $records = $request->getAll();

        $miscItems;

        foreach($records as $key=>$value){
            if(substr($key, 0, 4) === 'Misc'){
                $miscItemInput = explode("-", $key);
                $miscItems[$miscItemInput[1]][$miscItemInput[0]] = $value;
            }
        }

        if(!$db) $db = PearDatabase::getInstance();

        foreach($miscItems as $saveItem) {
            if($saveItem['MiscId'] == "none") { //Save a new entry
                $sql = "INSERT INTO `vtiger_contracts_misc_items` (`contractsid`, `is_quantity_rate`, `description`, `rate`, `quantity`, `discounted`, `discount`) VALUES (?,?,?,?,?,?,?)";
                $result = $db->pquery($sql, array(
                                            $records['record'],
                                            $saveItem['MiscFlatChargeOrQtyRate'],
                                            $saveItem['MiscDescription'],
                                            $saveItem['MiscRate'],
                                            $saveItem['MiscQty'],
                                            $saveItem['MiscDiscounted'],
                                            $saveItem['MiscDiscount']
                                        ));
            }
            else { //Update existing entry
                $sql = "UPDATE `vtiger_contracts_misc_items` SET `is_quantity_rate` = ?, `description` = ?, `rate` = ?, `quantity` = ?, `discounted` = ?, `discount` = ? WHERE `contracts_misc_id` = ?";
                $result = $db->pquery($sql, array(
                                                $saveItem['MiscFlatChargeOrQtyRate'],
                                                $saveItem['MiscDescription'],
                                                $saveItem['MiscRate'],
                                                $saveItem['MiscQty'],
                                                $saveItem['MiscDiscounted'],
                                                $saveItem['MiscDiscount'],
                                                $saveItem['MiscId']
                                            ));
            }
        } */

        /*-----------------------End save misc items----------------------------*/

        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        if ($request->get('imgDeleted')) {
            $imageIds = $request->get('imageid');
            foreach ($imageIds as $imageId) {
                $status = $recordModel->deleteImage($imageId);
            }
        }
        return $recordModel;
    }

    /**
     * Function to get the record model based on the request parameters
     * @param Vtiger_Request $request
     * @return Vtiger_Record_Model or Module specific Record Model instance
     */
    protected function getRecordModelFromRequest(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        if (!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('id', $recordId);
            $recordModel->set('mode', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('mode', '');
        }

        $fieldModelList = $moduleModel->getFields();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $fieldValue = $request->get($fieldName, null);
            $fieldDataType = $fieldModel->getFieldDataType();
            if ($fieldDataType == 'time') {
                $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
            }
            if ($fieldValue !== null) {
                if (!is_array($fieldValue)) {
                    $fieldValue = trim($fieldValue);
                }
                $recordModel->set($fieldName, $fieldValue);
            }
        }
        return $recordModel;
    }

    public function validateRequest(Vtiger_Request $request)
    {
        return $request->validateWriteAccess();
    }

    protected function getAccessKey($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'accesskey');
    }

    protected function getObjTypeId($modName)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name=?";
        $params[] = $modName;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'id');
    }

    protected function getUsername($userId)
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT user_name FROM `vtiger_users` WHERE id=?";
        $params[] = $userId;

        $result = $db->pquery($sql, $params);

        return $db->query_result($result, 0, 'user_name');
    }
}
