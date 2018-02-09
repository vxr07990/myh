<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Surveys_Detail_View extends Vtiger_Detail_View
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
        if (!empty($recordId)) {
            //Convert survey_date, survey_time, and survey_end_time to current user's time zone
            $db     = PearDatabase::getInstance();
            $sql    = "SELECT survey_date, survey_time, survey_end_time FROM vtiger_surveys WHERE surveysid=?";
            $result = $db->pquery($sql, [$recordId]);
            $row    = $result->fetchRow();
            if ($row != null) {
                $date       = $row[0];
                $start_time = $row[1];
                $end_time   = $row[2];
            }
            //file_put_contents('logs/devLog.log', "\n loaded server time	: ".$date .' '.$start_time, FILE_APPEND);
            $datetime = DateTimeField::convertToUserTimeZone($date.' '.$start_time);
            //file_put_contents('logs/devLog.log', "\n loaded user time 	: ".date_format($datetime, 'Y-m-d H:i:s'), FILE_APPEND);
            $user_date       = date_format($datetime, 'Y-m-d');
            $user_start_time = date_format($datetime, 'H:i:s');
            $datetime        = DateTimeField::convertToUserTimeZone($date.' '.$end_time);
            $user_end_time   = date_format($datetime, 'H:i:s');
        }
        foreach ($structuredValues as $blockName => $blockFields) {
            $surveyTime = '';
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                $fieldValue = $fieldModelTest->get('fieldvalue');
                if (isset($fieldValue)) {
//                    if ($fieldNameTest === 'survey_time' || $fieldNameTest === 'survey_end_time') {
//                        $time =
//                            DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue'))->format('H:i:s');
//                        if ($fieldNameTest == 'survey_time') {
//                            $surveyTime = $fieldModelTest->get('fieldvalue');
//                        }
//                        $fieldModelTest->set('fieldvalue', $time);
//                    }
                    if ($fieldNameTest == 'survey_date') {
                        if (!$surveyTime) {
                            $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.
                                                                         $blockFields['survey_time']->get('fieldvalue'))
                                                 ->format('Y-m-d');
                        } elseif ($user_date) {
                            $date = $user_date;
                        }
                        $fieldModelTest->set('fieldvalue', $date);
                    }
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
        $recordId      = $request->get('record');
        $sql           = "SELECT potential_id FROM `vtiger_surveys` WHERE surveysid=?";
        $result        = $db->pquery($sql, [$recordId]);
        $row           = $result->fetchRow();
        $potentialId   = $row[0];
        $sql           = "SELECT potentialname FROM `vtiger_potential` WHERE potentialid=?";
        $result        = $db->pquery($sql, [$potentialId]);
        $row           = $result->fetchRow();
        $potentialName = $row[0];
        $potentialLink = '<a href="index.php?module=Opportunities&amp;view=Detail&amp;record='.$potentialId.'" data-original-title="Opportunities">'.$potentialName.'</a>';
        $viewer->assign('POTENTIAL_LINK', $potentialLink);

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        //file_put_contents('logs/devLog.log', "\n This is getting called from : $moduleName", FILE_APPEND);
        //Added to remove the module specific js, as they depend on inventory files
        $modulePopUpFile  = 'modules.'.$moduleName.'.resources.Popup';
        $moduleEditFile   = 'modules.'.$moduleName.'.resources.Edit';
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.Detail';
        unset($headerScriptInstances[$modulePopUpFile]);
        unset($headerScriptInstances[$moduleEditFile]);
        unset($headerScriptInstances[$moduleDetailFile]);
        $jsFileNames           = [
            'modules.Inventory.resources.Popup',
            'modules.Inventory.resources.Detail',
            'modules.Inventory.resources.Edit',
            'modules.Quotes.resources.Detail',
            'modules.Quotes.resources.Edit',
            "modules.$moduleName.resources.Detail",
        ];
        $jsFileNames[]         = $moduleEditFile;
        $jsFileNames[]         = $modulePopUpFile;
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        //file_put_contents('logs/devLog.log', "\n \$headerScriptInstances : ".print_r($headerScriptInstances,true), FILE_APPEND);
        return $headerScriptInstances;
    }

    public function showModuleBasicView($request)
    {
        return $this->showModuleDetailView($request);
    }
}
