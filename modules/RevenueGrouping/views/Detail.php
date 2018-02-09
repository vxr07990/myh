<?php

class RevenueGrouping_Detail_View extends Vtiger_Detail_View
{
    public function showModuleDetailView(Vtiger_Request $request)
    {
        $viewer           = $this->getViewer($request);
        $record     = $request->get('record');
        //logic to include RevenueGroupingItem
        $revenueGroupingItemModel = Vtiger_Module_Model::getInstance('RevenueGroupingItem');
        if ($revenueGroupingItemModel && $revenueGroupingItemModel->isActive()) {
            $viewer->assign('REVENUEGROUPINGITEM_MODULE_MODEL', $revenueGroupingItemModel);
            $viewer->assign('REVENUEGROUPINGITEM_BLOCK_FIELDS', $revenueGroupingItemModel->getFields('LBL_REVENUEGROUPINGITEMSDETAIL'));
            $viewer->assign('REVENUEGROUPINGITEM_LIST', $revenueGroupingItemModel->getRevenueGroupingItem($record));
        }
        return parent::showModuleDetailView($request);
    }
    public function showModuleBasicView($request)
    {
        $viewer           = $this->getViewer($request);
        $record     = $request->get('record');
        //logic to include RevenueGroupingItem
        $revenueGroupingItemModel = Vtiger_Module_Model::getInstance('RevenueGroupingItem');
        if ($revenueGroupingItemModel && $revenueGroupingItemModel->isActive()) {
            $viewer->assign('REVENUEGROUPINGITEM_MODULE_MODEL', $revenueGroupingItemModel);
            $viewer->assign('REVENUEGROUPINGITEM_BLOCK_FIELDS', $revenueGroupingItemModel->getFields('LBL_REVENUEGROUPINGITEMSDETAIL'));
            $viewer->assign('REVENUEGROUPINGITEM_LIST', $revenueGroupingItemModel->getRevenueGroupingItem($record));
        }
        return parent::showModuleDetailView($request);
    }
}
