<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

class Orders_TooltipAjax_View extends Vtiger_TooltipAjax_View
{
    public function preProcess(Vtiger_Request $request)
    {
        return true;
    }

    public function postProcess(Vtiger_Request $request)
    {
        return true;
    }

    public function process(Vtiger_Request $request)
    {
        //TODO make this actually work so we can use it. For now, it bails out. Not sure if it works in other instances.
        if(getenv('INSTANCE_NAME') == 'graebel') {
            return;
        }
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $this->initializeListViewContents($request, $viewer);
        $db     = PearDatabase::getInstance();
        $sql    = 'SELECT * FROM vtiger_participatingagents WHERE rel_crmid = ? AND deleted=0';
        $result = $db->pquery($sql, [$request->get('record')]);
        $row    = $result->fetchRow();
        while ($row != null) {
            $sql2              = 'SELECT agentname FROM `vtiger_agents` WHERE agentsid = ?';
            $result2           = $db->pquery($sql2, [$row[1]]);
            $row2              = $result2->fetchRow();
            $row['agentName']  = $row2[0];
            $agentsLink        = '<a href="index.php?module=Agents&amp;view=Detail&amp;record='.$row[1].'" data-original-title="Agents">'.$row2[0].'</a>';
            $row['agentsLink'] = $agentsLink;
            $participantRows[] = $row;
            $row               = $result->fetchRow();
        }
        $viewer->assign('PARTICIPANT_ROWS', $participantRows);
        $ordersModel = Vtiger_Module_Model::getInstance('Orders');
        $orderFuelSurcharge = $ordersModel->getOrderFuelSurcharge($request->get('record'));
        if ($orderFuelSurcharge !== null) {
            $extraRows[] = ["Order Fuel Surcharge", $orderFuelSurcharge];
        }
        $orderAccessorials = $ordersModel->getOrderAccessorials($request->get('record'));
        if ($orderAccessorials !== null) {
            $extraRows[] = ["Order Accessorials", $orderAccessorials];
        }
        $orderDailyRevenue = $ordersModel->getOrdersDailyRevenue($request->get('record'));
        if (count($orderDailyRevenue) > 0) {
            $extraRows[] = ["Order Daily Revenue", $orderDailyRevenue[$request->get('record')]];
        }
        $ordersMileRevenue = $ordersModel->getOrdersMileRevenue($request->get('record'));
        if (count($ordersMileRevenue) > 0) {
            $extraRows[] = ["Order Mile Revenue", $ordersMileRevenue[$request->get('record')]];
        }
        $ordersRevenue = $ordersModel->getOrdersRevenue($request->get('record'));
        if (count($ordersRevenue) > 0) {
            $extraRows[] = ["Order Revenue", $ordersRevenue[$request->get('record')]];
        }
        $viewer->assign('EXTRA_ROWS', $extraRows);
        echo $viewer->view('TooltipContents.tpl', $moduleName, true);
    }

    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName       = $this->getModule($request);
        $recordId         = $request->get('record');
        $tooltipViewModel = Vtiger_TooltipView_Model::getInstance($moduleName, $recordId);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('MODULE_MODEL', $tooltipViewModel->getRecord()->getModule());
        $viewer->assign('TOOLTIP_FIELDS', $tooltipViewModel->getFields());
        $viewer->assign('RECORD', $tooltipViewModel->getRecord());
        $viewer->assign('RECORD_STRUCTURE', $tooltipViewModel->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
    }
}
