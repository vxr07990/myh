<?php

class CommissionPlansFilter_Detail_View extends Vtiger_Detail_View
{
    public function showModuleDetailView(Vtiger_Request $request)
    {
        $this->setValueForView($request);
        return parent::showModuleDetailView($request);
    }
    public function showModuleBasicView($request)
    {
        $this->setValueForView($request);
        return parent::showModuleDetailView($request);
    }
    public function setValueForView($request)
    {
        $viewer= $this->getViewer($request);
        $singles = ['commissionplansfilter_status'];
        $viewer->assign('SINGLE_FIELDS',$singles);
        $record     = $request->get('record');
            //logic to include Commission Plans Item
        $commissionPlansItem = Vtiger_Module_Model::getInstance('CommissionPlansItem');
        if ($commissionPlansItem && $commissionPlansItem->isActive()) {
            $viewer->assign('IS_ACTIVE_COMMISSIONPLANSITEM', true);
            $fields = $commissionPlansItem->getFields('LBL_COMMISSIONPLANITEMSDETAIL');
            //set vars and remove rel_crmid for block view
        foreach ($fields as $key => $field) {
            $fieldName = $field->get('name');
            if ($fieldName == 'commissionplansfilterid') {
                unset($fields[$key]);
            }
        }
            if ($record) {
                $viewer->assign('COMMISSIONPLANITEMS_LIST', $commissionPlansItem->getCommissionPlansItem($record));
            }
            //file_put_contents('logs/devLog.log', "\n StopsFields : ".print_r($stopsFields, true), FILE_APPEND);
            $viewer->assign('COMMISSIONPLANITEMS_BLOCK_FIELDS', $fields);
        }
    }
}
