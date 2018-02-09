<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OPList_Detail_View extends Vtiger_Detail_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showDetailViewByMode');
    }

    public function process(Vtiger_Request $request)
    {
        $viewer      = $this->getViewer($request);
        $moduleName  = $request->getModule();
        $record      = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $viewer->assign('NUM_SECTIONS', $recordModel->getNumSections());
        $viewer->assign('OPLIST_ARRAY', $recordModel->getOpListDataArray());
        parent::process($request);
    }

    public function showModuleDetailView(Vtiger_Request $request)
    {
        parent::showModuleDetailView($request);
    }

    public function showDetailViewByMode($request)
    {
        parent::showDetailViewByMode($request);
        //file_put_contents('logs/devLog.log', "\n showDetailViewByMode: ".print_r($request, true), FILE_APPEND);
        $viewer      = $this->getViewer($request);
        $moduleName  = $request->getModule();
        $record      = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $viewer->assign('NUM_SECTIONS', $recordModel->getNumSections());
        $viewer->assign('OPLIST_ARRAY', $recordModel->getOpListDataArray());
        $viewer->assign('MODULE', $moduleName);
        echo $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
