<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Orders_Record_Model extends Vtiger_Record_Model
{

    /**
     * Function to get the summary information for module
     * @return <array> - values which need to be shown as summary
     */
    public function getSummaryInfo()
    {
        /**
         * Tasks Open Block:
         * A) To Do Record: COUNT of Activity records (mode=Calendar) related to the Order that have an {Activities.Status} = "Not Started"
         * B) Events Record: COUNT of Activity records (mode=Events) related to the Order that have an {Activities.Status} = "Not Held"
         * C) Add A and B and display total in block
         */
        $tasksOpen = $this->countActivities("COALESCE(`vtiger_activity`.status, `vtiger_activity`.eventstatus) IN ('Not Started', 'Not Held')");

        /**
         * Progress Block:
         * A) To Do Record: COUNT of Activity records (mode=Calendar)  related to the Order that have an {Activities.Status} = "In Progress" OR {Activities.Status} = "Pending Input" OR {Activities.Status} = "Planned" or {Activities.Status} = "Deferred"
         * B) Events Record: COUNT of Activity records (mode=Events) related to the Order that have an {Activities.Status} = "Planned"
         * C) Add A and B and display total in block
         */
        $tasksInProgress = $this->countActivities("COALESCE(`vtiger_activity`.status, `vtiger_activity`.eventstatus) IN ('In Progress', 'Pending Input', 'Planned', 'Deferred')");

        /**
         * Overdue Tasks Block:
         * A) To Do Record: COUNT of Activity records (mode=Calendar)  related to the Order that have an {Activities.Status} <> "Completed"  AND {Activities.DueDate} < Now
         * B) Events Record: COUNT of Activity records (mode=Events) related to the Order that have an {Activities.Status} <> "Held" AND {Activites.EndDate&Time} < Now
         * C) Add A and B and display total in block
         */
        $taskDue = $this->countActivities("COALESCE(`vtiger_activity`.status, `vtiger_activity`.eventstatus) NOT IN ('Completed', 'Held')
                          AND CASE `vtiger_activity`.activitytype 
                                WHEN 'Task' THEN CONCAT(`vtiger_activity`.due_date, ' 00:00:00')
                                ELSE CONCAT(`vtiger_activity`.due_date, ' ', `vtiger_activity`.time_end)
                          END  < NOW()");

        /**
         * Tasks Completed Block:
         * A) To Do Record: COUNT of Activity records (mode=Calendar)  related to the Order that have an {Activities.Status} = "Completed"
         * B) Events Record: COUNT of Activity records (mode=Events) related to the Order that have an {Activities.Status} =  "Held"
         * C) Add A and B and display total in block
         */
        $taskCompleted = $this->countActivities("COALESCE(`vtiger_activity`.status, `vtiger_activity`.eventstatus) IN ('Completed', 'Held')");

        /**
         * High Priority Block:
         * A) COUNT of all Activity records that have an {Activities.Priority} = "High"
         */
        $highTasks = $this->countActivities("`vtiger_activity`.priority = 'High'");

        /**
         * Medium Priority Block:
         * A) COUNT of all Activity records that have an {Activities.Priority} = "Medium"
         */
        $mediumTasks = $this->countActivities("`vtiger_activity`.priority = 'Medium'");

        /**
         * Low Priority Block:
         * A) COUNT of all Activity records that have an {Activities.Priority} = "Low"
         */
        $lowTasks = $this->countActivities("`vtiger_activity`.priority = 'Low'");

        /**
         * No Priority Block:
         * A) COUNT of all Activity records that have an {Activities.Priority} = Empty or NULL
         */
        $otherTasks = $this->countActivities("`vtiger_activity`.priority IS NULL OR `vtiger_activity`.priority = ''");

        $summaryInfo['orderstaskstatus'] = [
            'LBL_TASKS_OPEN'      => $tasksOpen,
            'Progress'            => $tasksInProgress,
            'LBL_TASKS_DUE'       => $taskDue,
            'LBL_TASKS_COMPLETED' => $taskCompleted,
        ];
        $summaryInfo['orderstaskpriority'] = [
            'LBL_TASKS_HIGH'   => $highTasks,
            'LBL_TASKS_NORMAL' => $mediumTasks,
            'LBL_TASKS_LOW'    => $lowTasks,
            'LBL_TASKS_OTHER'  => $otherTasks,
        ];

        return $summaryInfo;
    }

    private function countActivities($whereClause) {
        global $adb;
        $sql = "SELECT COUNT(`vtiger_activity`.activityid) as numCount FROM `vtiger_crmentity` 
                         JOIN `vtiger_seactivityrel` ON `vtiger_crmentity`.crmid=`vtiger_seactivityrel`.crmid
                         JOIN `vtiger_activity`      ON `vtiger_seactivityrel`.activityid=`vtiger_activity`.activityid
                         WHERE `vtiger_crmentity`.crmid=? AND `vtiger_activity`.activitytype != 'Emails' AND ($whereClause)";
        $result = $adb->pquery($sql, [$this->getId()]);
        return $result ? $result->fields['numCount'] : 0;
    }

    public function setParentRecordData(Vtiger_Record_Model $parentRecordModel)
    {
        global $current_user;
        $moduleName = $parentRecordModel->getModuleName();
        $data             = [];
        $fieldMappingList = $parentRecordModel->getOrderMappingFields();
        foreach ($fieldMappingList as $fieldMapping) {
            $parentField = $fieldMapping['parentField'];
            $orderField  = $fieldMapping['orderField'];
            $fieldModel  = Vtiger_Field_Model::getInstance($parentField, Vtiger_Module_Model::getInstance($moduleName));
            $typeofdata  = explode('~', $fieldModel->typeofdata);
            if (method_exists($fieldModel, 'getPermissions') && $fieldModel->getPermissions()) {
                $value = $parentRecordModel->get($parentField);
                //piggybacking on amount permissions to populate from pseudofields @TODO the right way to do this
                if($parentField == 'amount'){
                    if($parentRecordModel->get('linehaul_net')) {
                        $data['orders_elinehaul'] = $parentRecordModel->get('linehaul_net');
                    }
                    if($parentRecordModel->get('mileage')) {
                        $data['mileage'] = $parentRecordModel->get('mileage');
                    }
                    if($parentRecordModel->get('effective_tariff')) {
                        $data['tariff_id'] = $parentRecordModel->get('effective_tariff');
                    }
                    if($parentRecordModel->get('survey_weight')) {
                        $data['orders_eweight'] = ceil($parentRecordModel->get('survey_weight'));
                    }
                    if($parentRecordModel->get('survey_cube')) {
                        $data['orders_ecube'] = ceil($parentRecordModel->get('survey_cube'));
                    }
                    if($parentRecordModel->get('survey_item_count')) {
                        $data['orders_pcount'] = $parentRecordModel->get('survey_item_count');
                    }

                }
                if($value) {
                    if ($typeofdata[0] == 'T') {
//                        $timeZone = getFieldTimeZoneValue($parentField, $parentRecordModel->getId());
//                        if(!$timeZone) {
//                            $timeZone = $current_user->time_zone;
//                        }
//                        $date = DateTimeField::convertTimeZone($value, DateTimeField::getDBTimeZone(), $timeZone);
//                        $value = $date->format("H:i:s");
                    } else if ($typeofdata[0] == 'DT' && count($typeofdata) > 3 && $typeofdata[2] == 'REL') {
                        $timeField = $typeofdata[3];
                        $timeZone  = getFieldTimeZoneValue($timeField, $parentRecordModel->getId());
                        if (!$timeZone) {
                            $timeZone = $current_user->time_zone;
                        }
                        $date  = DateTimeField::convertTimeZone($value.' '.$parentRecordModel->get($timeField), DateTimeField::getDBTimeZone(), $timeZone);
                        $value = $date->format("Y-m-d");
                    }
                    $data[$orderField] = $value;
                } else {
                    $data[$orderField] = $fieldMapping['defaultValue'];
                }
            } else {
                $data[$orderField] = $fieldMapping['defaultValue'];
            }
        }
        $data['orders_opportunities'] = $parentRecordModel->getId();
        $data['received_date'] = date('Y-m-d');
        $data['ordersstatus'] = 'Booked';
        //$this->mapParticipatingAgents($parentRecordModel);
        //$this->mapExtraStops($parentRecordModel);
        return $this->setData($data);
    }

    public function getMappingFields($forModuleName) {
        if($forModuleName == 'Estimates' || $forModuleName == 'Actuals')
        {
            $res = [
                'id' => 'orders_id',
                'orders_account' => 'account_id',
                'orders_contacts' => 'contact_id',
                'origin_address1' => 'origin_address1',
                'origin_address2' => 'origin_address2',
                'origin_city' => 'origin_city',
                'origin_state' => 'origin_state',
                'origin_phone1' => 'origin_phone1',
                'origin_phone2' => 'origin_phone2',
                'origin_zip' => 'origin_zip',
                'destination_address1' => 'destination_address1',
                'destination_address2' => 'destination_address2',
                'destination_city' => 'destination_city',
                'destination_state' => 'destination_state',
                'destination_zip' => 'destination_zip',
                'destination_phone1' => 'destination_phone1',
                'destination_phone2' => 'destination_phone2',
                'orders_ldate' => 'load_date',
                'billing_type' => 'billing_type',
                'bill_street' => 'bill_street',
                'bill_city' => 'bill_city',
                'bill_state' => 'bill_state',
                'bill_code' => 'bill_code',
                'bill_pobox' => 'bill_pobox',
                'bill_country' => 'bill_country',
                'valuation_deductible' => 'valuation_deductible',
                'valuation_amount' => 'valuation_amount',
                'valuation_deductible_amount' => 'valuation_deductible_amount',
                'valuation_discounted' => 'valuation_discounted',
                'valuation_discount_amount' => 'valuation_discount_amount',
                'additional_valuation' => 'additional_valuation',
                'tariff_id' => 'effective_tariff',
                'account_contract' => 'contract',
                'business_line' => 'business_line_est',
                'business_line2' => 'business_line_est2',
                'commodities' => 'commodities',
                'authority' => 'authority',
            ];
            if(getenv('INSTANCE_NAME') == 'graebel') {
                $res['agentid'] = 'agentid';
                $res['orders_eweight'] = 'weight';
                $res['orders_ldate'] = 'pickup_date';
                $res['orders_discount'] = 'bottom_line_discount';
                $res['orders_discount'] = 'bottom_line_distribution_discount';
            }
            return $res;
        }
        return [];
    }

    public function getInventoryMappingFields()
    {
        file_put_contents('logs/OrderMapping.log', date('Y-m-d H:i:s - ')."Entering getInventoryMappingFields function\n", FILE_APPEND);

        return [
            ['parentField' => 'orders_account', 'inventoryField' => 'account_id', 'defaultValue' => ''],
            ['parentField' => 'orders_contacts', 'inventoryField' => 'contact_id', 'defaultValue' => ''],
            //array('parentField'=>'orderspriority', 'inventoryField'=>'business_line', 'defaultValue'=>''),
            ['parentField' => 'origin_address1', 'inventoryField' => 'origin_address1', 'defaultValue' => ''],
            ['parentField' => 'origin_address2', 'inventoryField' => 'origin_address2', 'defaultValue' => ''],
            ['parentField' => 'origin_city', 'inventoryField' => 'origin_city', 'defaultValue' => ''],
            ['parentField' => 'origin_state', 'inventoryField' => 'origin_state', 'defaultValue' => ''],
            ['parentField' => 'origin_phone1', 'inventoryField' => 'origin_phone1', 'defaultValue' => ''],
            ['parentField' => 'origin_phone2', 'inventoryField' => 'origin_phone2', 'defaultValue' => ''],
            ['parentField' => 'origin_zip', 'inventoryField' => 'origin_zip', 'defaultValue' => ''],
            ['parentField' => 'destination_address1', 'inventoryField' => 'destination_address1', 'defaultValue' => ''],
            ['parentField' => 'destination_address2', 'inventoryField' => 'destination_address2', 'defaultValue' => ''],
            ['parentField' => 'destination_city', 'inventoryField' => 'destination_city', 'defaultValue' => ''],
            ['parentField' => 'destination_state', 'inventoryField' => 'destination_state', 'defaultValue' => ''],
            ['parentField' => 'destination_zip', 'inventoryField' => 'destination_zip', 'defaultValue' => ''],
            ['parentField' => 'destination_phone1', 'inventoryField' => 'destination_phone1', 'defaultValue' => ''],
            ['parentField' => 'destination_phone2', 'inventoryField' => 'destination_phone2', 'defaultValue' => ''],
            //array('parentField' => 'orders_ldate', 'inventoryField' => 'pickup_date', 'defaultValue' => ''),
            ['parentField' => 'orders_ldate', 'inventoryField' => 'load_date', 'defaultValue' => ''],
            //Add additional fields.
            ['parentField' => 'commodities', 'inventoryField' => 'commodities', 'defaultValue' => ''],
            ['parentField' => 'business_line', 'inventoryField' => 'business_line_est', 'defaultValue' => ''],
            ['parentField' => 'business_line2', 'inventoryField' => 'business_line_est2', 'defaultValue' => ''],
            ['parentField' => 'billing_type', 'inventoryField' => 'billing_type', 'defaultValue' => ''],
        ];
    }

    //@TODO: get roles for black list (cancel/uncancel)
    public function isPermittedUserRole($action)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentUserRole = $currentUser->get("roleid");
        $blackListRoles = ($action == "Cancel") ? array("H2") : array("H2");
        if (!in_array($currentUserRole, $blackListRoles)) {
            return true;
        } else {
            return false;
        }
    }

    public function getOrdersStatus($recordId)
    {
        $record = Vtiger_Record_Model::getInstanceById($recordId, 'Orders');
        return $record->get('ordersstatus');
    }

    //function not used any more. OT1978
//    function getOnHoldStatus($recordId) {
//        $record = Vtiger_Record_Model::getInstanceById($recordId, 'Orders');
//
//        return $record->get('orders_onhold');
//    }

    public function getAPUCheck($recordId)
    {
        $record = Vtiger_Record_Model::getInstanceById($recordId, 'Orders');

        return $record->get('orders_apu');
    }

    public function getAPUDate($recordId)
    {
        $record = Vtiger_Record_Model::getInstanceById($recordId, 'Orders');

        return $record->get('orders_onhold');
    }

    public function getShuttleStatus($recordId)
    {
        $shuttleNotification = 'None';
        //Use the existing FUNCTION to get the primary estimate.
        $primaryRecordEstimate = $this->getPrimaryEstimateRecordModel();
        //make sure the record is returned
        if ($primaryRecordEstimate) {
            //get these things!
            $originApplied = $primaryRecordEstimate->get('acc_shuttle_origin_applied');
            $destApplied   = $primaryRecordEstimate->get('acc_shuttle_dest_applied');
            if ($originApplied && $destApplied) {
                $shuttleNotification = 'Both';
            } elseif ($originApplied && !$destApplied) {
                $shuttleNotification = 'Origin';
            } elseif (!$originApplied && $destApplied) {
                $shuttleNotification = 'Destination';
            }
        }

        return $shuttleNotification;
    }

    public function getBackgroundListViewColor($recordId)
    {
        $colorArray = $this->getColors();
        if (count($colorArray) > 0) {
            $record         = Vtiger_Record_Model::getInstanceById($recordId, 'Orders');
            $assignedToTrip = $record->get('orders_assignedtrip');
            $orderAPU       = $record->get('orders_apu');
            $ordersMile     = floatval($record->get('orders_miles'));
            $ordersNo       = $record->get('orders_no');
            if ($this->orderHasOverflow($ordersNo)) {
                return $colorArray['overflow'];
            } elseif ($ordersMile > 0 && $ordersMile < 500) {
                return $colorArray['short_haul'];
            } elseif ($orderAPU) {
                return $colorArray['assigned'];
            } elseif ($assignedToTrip) {
                return $colorArray['apu'];
            } else {
                $loadDate = $record->get('orders_ldate');
                unset($colorArray['assigned']);
                unset($colorArray['apu']);
                unset($colorArray['short_haul']);
                unset($colorArray['overflow']);
                if ($loadDate != '') {
                    $daysToLoad = round((strtotime($loadDate) - time()) / 86400);
                    ksort($colorArray);
                    foreach ($colorArray as $days => $color) {
                        if ($days >= $daysToLoad && $daysToLoad >= 0) {
                            return $colorArray[$days];
                        } elseif ($daysToLoad < 0 && $days == -1) {
                            return $colorArray[$days];
                        }
                    }
                }
            }
        }
    }

    public function getColors()
    {
        $colorArray = Vtiger_Session::get('ldd_backgrondcolors');
        if ($colorArray === false || !is_array($colorArray)) {
            $db     = PearDatabase::getInstance();
            $result = $db->pquery("SELECT * FROM vtiger_colorsettings");
            if ($db->num_rows($result) > 0) {
                while ($arr = $db->fetch_array($result)) {
                    $colorArray[$arr['value']] = $arr['color'];
                }
            } else {
                $colorArray = [];
            }
            Vtiger_Session::set('ldd_backgrondcolors', $colorArray);
        }

        return $colorArray;
    }

    public function orderHasOverflow($ordersNo)
    {
        $db     = PearDatabase::getInstance();
        $result = $db->pquery("SELECT ordersid 
                                    FROM vtiger_orders INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid = vtiger_crmentity.crmid
                                    WHERE deleted = 0
                                    AND orders_no like '$ordersNo O/F%'");
        if ($result && $db->num_rows($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getPrimaryEstimateRecordModel($getAny = true, $setype = 'Estimates', $relatedField = 'orders_id')
    {
        return parent::getPrimaryEstimateRecordModel($getAny, $setype, $relatedField);
    }

    public function isLocked()
    {
        $isLocked = $this->get('is_locked') == 0 ? false : true;

        return $isLocked;
    }

    /**
     * @param bool $roleType moverole type to limit select with
     *
     * @return array
     */
    public function getMoveRoles($roleType = false)
    {
        $mrInfo = [];
        $params = [];
        $id = $this->getId();
        if (!$id) {
            return $mrInfo;
        }
        $db = PearDatabase::getInstance();
        $stmt = 'SELECT * FROM `vtiger_moveroles` INNER JOIN vtiger_crmentity ON(crmid=moverolesid) WHERE `moveroles_orders` = ? AND deleted=0';
        $params[] = $id;
        if ($roleType) {
            $stmt .= ' AND `moveroles_role` = ?';
            $params[] = $roleType;
        }
        $result = $db->pquery($stmt, $params);
        while ($row = $result->fetchRow()) {
            $mrInfo[$row['moverolesid']] = $row;
        }
        return $mrInfo;
    }

     public function getZoneAdminDisplayValue($zoneAdminID){
        return ZoneAdmin_Module_Model::getZoneAdminDisplayValue($zoneAdminID);
     }

}
