<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Employees_Detail_View extends Vtiger_Detail_View
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
        foreach ($structuredValues as $blockName => $blockFields) {
        //Convert date_out, time_out, and time_in to current user's time zone
            $employeeTime = '';
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                if (($fieldNameTest === 'time_out' || $fieldNameTest === 'time_in') && $fieldModelTest->get('fieldvalue') !== '') {
                    $time = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue'))->format('H:i:s');
                    if ($fieldNameTest === 'time_out') {
                        $employeeTime = $fieldModelTest->get('fieldvalue');
                    }
                    $fieldModelTest->set('fieldvalue', $time);
                }
                if (($fieldNameTest === 'date_out' || $fieldNameTest === 'date_in') && $fieldModelTest->get('fieldvalue') !== '') {
                    if ($employeeTime === '') {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$blockFields['time_out']->get('fieldvalue'))->format('Y-m-d');
                    } else {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$employeeTime)->format('Y-m-d');
                    }
                    $fieldModelTest->set('fieldvalue', $date);
                }
            }
        }

        $viewer       = $this->getViewer($request);
        if(getenv('INSTANCE_NAME') != 'graebel') {
            $prole = [$recordModel->get('employee_primaryrole')];
            $srole = $recordModel->get('employee_secondaryrole')?explode(',', $recordModel->get('employee_secondaryrole')):[];
            $ids   = array_merge($prole, $srole);
            if (count($ids) == 0 || !EmployeeRoles_ActionAjax_Action::isDriver($ids)) {
                $viewer->assign('HIDE_DRIVER_INFO', 1);
            }
        }

        //End Time Zone Conversion
        $imageDetails = (new Employees_Record_Model(['id' => $recordId]))->getImageDetails();
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        //$viewer->assign('LINKED_USER_MODEL', $recordModel->getLinkedUser());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('IMAGE_DETAILS', $imageDetails);

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        return $this->showModuleDetailView($request);
    }
}
