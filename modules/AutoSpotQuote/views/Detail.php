<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AutoSpotQuote_Detail_View extends Vtiger_Detail_View
{
    public function process(Vtiger_Request $request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel          = $this->record->getRecord();
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);

        $db = PearDatabase::getInstance();

        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());

        $viewer->assign('AUTO_QUOTE_INFO', $recordModel->get('auto_quote_info'));
        $viewer->assign('AUTO_QUOTE_SELECT', $recordModel->get('auto_quote_select'));
        $viewer->assign('AUTO_QUOTE_ID', $recordModel->get('auto_quote_id'));

        //Can't use nested objects in Smarty, because that was a good choice.. instead we will assinge 16 variables, instead of just one.
        $rateInfo = json_decode(urldecode($recordModel->get('auto_quote_info')));
        $viewer->assign('AUTO_QUOTE_10_load', $rateInfo->rates->ten_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_10_from', $rateInfo->rates->ten_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_10_to', $rateInfo->rates->ten_day_pickup->deliver_to_date);
        $viewer->assign('AUTO_QUOTE_10_price', $rateInfo->rates->ten_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_7_load', $rateInfo->rates->seven_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_7_from', $rateInfo->rates->seven_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_7_to', $rateInfo->rates->seven_day_pickup->deliver_to_date);
        $viewer->assign('AUTO_QUOTE_7_price', $rateInfo->rates->seven_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_4_load', $rateInfo->rates->four_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_4_from', $rateInfo->rates->four_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_4_to', $rateInfo->rates->four_day_pickup->deliver_to_date);
        $viewer->assign('AUTO_QUOTE_4_price', $rateInfo->rates->four_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_2_load', $rateInfo->rates->two_day_pickup->load_to_date);
        $viewer->assign('AUTO_QUOTE_2_from', $rateInfo->rates->two_day_pickup->deliver_from_date);
        $viewer->assign('AUTO_QUOTE_2_to', $rateInfo->rates->two_day_pickup->deliver_to_date);
        $viewer->assign('AUTO_QUOTE_2_price', $rateInfo->rates->two_day_pickup->price);

        $viewer->assign('AUTO_QUOTE_ID', $rateInfo->quote_ref_id);
        $viewer->assign('AUTO_QUOTE_expire', date('m-d-Y', strtotime($rateInfo->expires_at)));

        $viewer->assign('AUTO_QUOTE_effective', date('m-d-Y', strtotime($db->getOne('SELECT modifiedtime FROM vtiger_crmentity WHERE crmid =' . intval($recordId)))));

        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }
}
