<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';


class Orders_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);

        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_TASKS_LIST',
                'linkurl' => $this->getTasksListUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_MILESTONES_LIST',
                'linkurl' => $this->getMilestonesListUrl(),
                'linkicon' => '',
            ),
                    
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_LONG_DISTANCE_DISPATCH',
                'linkurl' => 'index.php?module=Orders&view=LDDList',
                'linkicon' => '',
            )
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }

    public function getTasksListUrl()
    {
        $taskModel = Vtiger_Module_Model::getInstance('OrdersTask');
        return $taskModel->getListViewUrl();
    }
    public function getMilestonesListUrl()
    {
        $milestoneModel = Vtiger_Module_Model::getInstance('OrdersMilestone');
        return $milestoneModel->getListViewUrl();
    }
        
    public function createMilestone($recordId, $oldStatus, $newStatus)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        $mainOrder = vtws_retrieve(vtws_getWebserviceEntityId('Orders', $recordId), $currentUser);

        $newMilestone = array(
            'ordersmilestonename' => 'Status changed to: ' . $newStatus,
            'ordersid' => vtws_getWebserviceEntityId('Orders', $recordId),
            'ordersmilestonedate' => date('Y-m-d'),
            'ordersmilestonetype' => '--none--',
            'description' => 'The order status was changed from ' . $oldStatus . ' to ' . $newStatus,
            'agentid' => $mainOrder['agentid'],
            'assigned_user_id' => vtws_getWebserviceEntityId('Users', $currentUser->id),
        );

        try {
            $wts_created = vtws_create('OrdersMilestone', $newMilestone, $currentUser);
        } catch (Exception $exc) {
            global $log;
            
            $log->debug('Milestone for Record: ' . $recordId . ' Could not be created' . $exc->getMessage());
        }
    }
    
    public static function getStops($recordId, $moduleName)
    {
        /*$lookupColumn = '';
        if($moduleName === 'Orders') {
            $lookupColumn = 'stop_order';
        } else if($moduleName === 'Opportunities') {
            $lookupColumn = 'stop_opp';
        } else if($moduleName === 'Estimates') {
            $lookupColumn = 'stop_estimate';
        } else {
            return array();
        }*/
        $stopsRows = array();
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM `vtiger_extrastops` WHERE extrastops_relcrmid = ?';
        $result = $db->pquery($sql, array($recordId));
        if ($result) {
            $row = $result->fetchRow();
            while ($row != null) {
                $sql2 = 'SELECT firstname, lastname FROM `vtiger_contactdetails` WHERE contactid = ?';
                $result2 = $db->pquery($sql2, array($row['stop_contact']));
                $row2 = $result2->fetchRow();
                $row['stop_contact_name'] = $row2[0].' '.$row2[1];
                $stopsRows[] = $row;
                $row = $result->fetchRow();
            }
        }
        
        return $stopsRows;
    }
        
    public function getOrdersRevenue($idList)
    {
        $db = PearDatabase::getInstance();
        if (!is_array($idList)) {
            $idList = array(
                $idList,
            );
        }

        if (count($idList) > 0) {
            $idList = '(' . implode(',', $idList) . ')';

            $sql = "SELECT orders_id, ROUND(SUM(listprice)+SUM(orders_elinehaul),2) AS revenue
                        FROM vtiger_quotes quo
                        INNER JOIN vtiger_inventoryproductrel inv on quo.quoteid = inv.id
                        INNER JOIN vtiger_service serv on inv.productid = serv.serviceid
                        INNER JOIN vtiger_orders orders on quo.orders_id = orders.ordersid
                        INNER JOIN vtiger_crmentity crme on quo.quoteid = crme.crmid
                        WHERE deleted=0 
                        AND is_primary=1 
                        AND quotestage ='Accepted' 
                        AND (servicename LIKE '%Fuel%' OR servicename LIKE '%Accessorials%') 
                        AND orders_id IN $idList GROUP BY orders_id";

            $result = $db->pquery($sql);
            $revArray = array();

            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    $revArray[$row['orders_id']] = $row['revenue'];
                }
            }
        }
        return $revArray;
    }

    public function getOrdersMileRevenue($idList)
    {
        $db = PearDatabase::getInstance();
        if (!is_array($idList)) {
            $idList = array(
                $idList,
            );
        }

        if (count($idList) > 0) {
            $ordersRev = $this->getOrdersRevenue($idList);
            $idList = '(' . implode(',', $idList) . ')';

            $sql = "SELECT orders_miles,ordersid FROM vtiger_orders WHERE ordersid IN $idList";
            $result = $db->pquery($sql);
            $mileRev = array();

            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    if ($row['orders_miles'] == 0) {
                        $mileRev[$row['ordersid']] = 'N/A';
                    } else {
                        $mileRev[$row['ordersid']] = round($ordersRev[$row['ordersid']] / $row['orders_miles'], 2);
                    }
                }
            }
        }

        return $mileRev;
    }

    public function getOrdersDailyRevenue($idList)
    {
        $db = PearDatabase::getInstance();
        if (!is_array($idList)) {
            $idList = array(
                $idList,
            );
        }

        if (count($idList) > 0) {
            $ordersRev = $this->getOrdersRevenue($idList);
            $idList = '(' . implode(',', $idList) . ')';

            $sql = "SELECT DATEDIFF(orders_dtdate,orders_ldate) order_days,ordersid FROM vtiger_orders WHERE ordersid IN $idList";
            $result = $db->pquery($sql);
            $dailyRev = array();

            if ($result && $db->num_rows($result) > 0) {
                while ($row = $db->fetchByAssoc($result)) {
                    if ($row['order_days'] == '') {
                        $dailyRev[$row['ordersid']] = 'N/A';
                    } else {
                        $dailyRev[$row['ordersid']] = round($ordersRev[$row['ordersid']] / $row['order_days'], 2);
                    }
                }
            }
        }
        
        return $dailyRev;
    }
    
    public function getOrderFuelSurcharge($orderId)
    {
        $db = PearDatabase::getInstance();
        $orderFSC = 0;
        $result = $db->pquery("SELECT orders_id, ROUND(SUM(listprice),2) AS revenue
                            FROM vtiger_quotes quo
                            INNER JOIN vtiger_inventoryproductrel inv on quo.quoteid = inv.id
                            INNER JOIN vtiger_service serv on inv.productid = serv.serviceid
                            INNER JOIN vtiger_orders orders on quo.orders_id = orders.ordersid
                            INNER JOIN vtiger_crmentity crme on quo.quoteid = crme.crmid
                            WHERE deleted=0 
                            AND is_primary=1 
                            AND quotestage ='Accepted' 
                            AND servicename LIKE '%Fuel%' 
                            AND orders_id = ?", array($orderId));

        if ($result && $db->num_fields($result) > 0) {
            $orderFSC = $db->query_result($result, 0, 'revenue');
        }

        return $orderFSC;
    }
    
    public function getOrderAccessorials($orderId)
    {
        $db = PearDatabase::getInstance();
        $orderAcce = 0;
        $result = $db->pquery("SELECT orders_id, ROUND(SUM(listprice),2) AS revenue
                            FROM vtiger_quotes quo
                            INNER JOIN vtiger_inventoryproductrel inv on quo.quoteid = inv.id
                            INNER JOIN vtiger_service serv on inv.productid = serv.serviceid
                            INNER JOIN vtiger_orders orders on quo.orders_id = orders.ordersid
                            INNER JOIN vtiger_crmentity crme on quo.quoteid = crme.crmid
                            WHERE deleted=0 
                            AND is_primary=1 
                            AND quotestage ='Accepted' 
                            AND servicename LIKE '%Accessorials%' 
                            AND orders_id = ?", array($orderId));

        if ($result && $db->num_fields($result) > 0) {
            $orderAcce = $db->query_result($result, 0, 'revenue');
        }

        return $orderAcce;
    }
}
