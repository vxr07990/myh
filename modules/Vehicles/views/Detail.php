<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vehicles_Detail_View extends Vtiger_Detail_View
{
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel     = $this->record->getRecord();
        $recordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStructure->getStructure();
        $moduleModel      = $recordModel->getModule();
        //Convert date_out, time_out, and time_in to current user's time zone
        foreach ($structuredValues as $blockName => $blockFields) {
            $vehiclesTime = '';
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                if (($fieldNameTest === 'time_out' || $fieldNameTest === 'time_in') && $fieldModelTest->get('fieldvalue') !== '') {
                    $time = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue'))->format('H:i:s');
                    if ($fieldNameTest === 'time_out') {
                        $vehiclesTime = $fieldModelTest->get('fieldvalue');
                    }
                    $fieldModelTest->set('fieldvalue', $time);
                }
                if (($fieldNameTest === 'date_out' || $fieldNameTest === 'date_in') && $fieldModelTest->get('fieldvalue') !== '') {
                    if ($vehiclesTime === '') {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$blockFields['time_out']->get('fieldvalue'))->format('Y-m-d');
                    } else {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$vehiclesTime)->format('Y-m-d');
                    }
                    $fieldModelTest->set('fieldvalue', $date);
                }
            }
        }
        //End Time Zone Conversion
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        return $this->showModuleDetailView($request);
    }
}
