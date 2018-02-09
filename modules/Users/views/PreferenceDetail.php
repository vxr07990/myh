<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_PreferenceDetail_View extends Vtiger_Detail_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $record = $request->get('record');

        if (!empty($record)) {
            try {
                $recordModel = Users_Record_Model::getInstanceById($record);
            } catch (Exception $ex) {
                throw new AppException('LBL_PERMISSION_DENIED');
            }

            if (!$recordModel) {
                throw new AppException('LBL_PERMISSION_DENIED');
            }

            //This is an existing record, we can call the record model function to ensure the user is allowed to view it.
            if (!$recordModel->allowedToView()) {
                throw new AppException('LBL_PERMISSION_DENIED');
            }

            //@TODO: REVIEW: I think you should be able to view the details of an inactive user, that makes sense.
//            if ($recordModel->get('status') != 'Active') {
//                throw new AppException('LBL_PERMISSION_DENIED');
//            }
            return true;
        } else {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    /**
     * Function to returns the preProcess Template Name
     *
     * @param  <type> $request
     *
     * @return <String>
     */
    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'PreferenceDetailViewPreProcess.tpl';
    }

    /**
     * Function shows basic detail for the record
     *
     * @param <type> $request
     */
    public function showModuleBasicView($request)
    {
        return $this->showModuleDetailView($request);
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        if ($this->checkPermission($request)) {
            $currentUser          = Users_Record_Model::getCurrentUserModel();
            $recordId             = $request->get('record');
            $moduleName           = $request->getModule();
            $detailViewModel      = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            $recordModel          = $detailViewModel->getRecord();
            $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
            $detailViewLinks      = $detailViewModel->getDetailViewLinks($detailViewLinkParams);
            $viewer               = $this->getViewer($request);
            //check if menu cleaner is on, and if it is, then use it, otherwise ignore
            $cleanerEnabled = false;
            $menuCleanerModel    = Vtiger_Module_Model::getInstance('MenuCleaner');
            if ($menuCleanerModel && $menuCleanerModel->isActive()) {
              $cleanerEnabled = true;
            }
            $viewer->assign('RECORD', $recordModel);
            $viewer->assign('MODULE_MODEL', $detailViewModel->getModule());
            $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
            $viewer->assign('IS_EDITABLE', $detailViewModel->getRecord()->isEditable($moduleName));
            $viewer->assign('IS_DELETABLE', $detailViewModel->getRecord()->isDeletable($moduleName));
            $linkParams = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
            $linkModels = $detailViewModel->getSideBarLinks($linkParams);
            $viewer->assign('QUICK_LINKS', $linkModels);
            $viewer->assign('PAGETITLE', $this->getPageTitle($request));
            $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
            $viewer->assign('STYLES', $this->getHeaderCss($request));
            $viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));
            $viewer->assign('SEARCHABLE_MODULES', Vtiger_Module_Model::getSearchableModules());
            $menuModelsList = Vtiger_Menu_Model::getAll(true);
            $selectedModule = $request->getModule();
            $menuStructure  = Vtiger_MenuStructure_Model::getInstanceFromMenuList($menuModelsList, $selectedModule);
            // Order by pre-defined automation process for QuickCreate.
            uksort($menuModelsList, ['Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess']);
            $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
            $companyLogo    = $companyDetails->getLogo();
            $viewer->assign('CURRENTDATE', date('Y-n-j'));
            $viewer->assign('MODULE', $selectedModule);
            $viewer->assign('PARENT_MODULE', $request->get('parent'));
            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MENUS', $menuModelsList);
            $viewer->assign('MENU_STRUCTURE', $menuStructure);
            $viewer->assign('MENU_SELECTED_MODULENAME', $selectedModule);
            $viewer->assign('MENU_TOPITEMS_LIMIT', $menuStructure->getLimit());
            $viewer->assign('MENU_CLEANER_TOGGLE',$cleanerEnabled);
            $viewer->assign('COMPANY_LOGO', $companyLogo);
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $homeModuleModel = Vtiger_Module_Model::getInstance('Home');
            $viewer->assign('HOME_MODULE_MODEL', $homeModuleModel);
            $viewer->assign('HEADER_LINKS', $this->getHeaderLinks());
            $viewer->assign('ANNOUNCEMENT', $this->getAnnouncement());
            $viewer->assign('CURRENT_VIEW', $request->get('view'));
            $viewer->assign('SKIN_PATH', Vtiger_Theme::getCurrentUserThemePath());
            $viewer->assign('LANGUAGE', $currentUser->get('language'));
            if ($display) {
                $this->preProcessDisplay($request);
            }
        }
    }

    protected function preProcessDisplay(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $viewer->view($this->preProcessTplName($request), $request->getModule());
    }

    public function process(Vtiger_Request $request)
    {
        $recordId                = $request->get('record');
        $moduleName              = $request->getModule();
        $recordModel             = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $dayStartPicklistValues  = Users_Record_Model::getDayStartsPicklistValues($recordStructureInstance->getStructure());
        $viewer                  = $this->getViewer($request);
        $viewer->assign("DAY_STARTS", Zend_Json::encode($dayStartPicklistValues));
        $viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

        return parent::process($request);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $moduleDetailFile      = 'modules.'.$moduleName.'.resources.PreferenceDetail';
        unset($headerScriptInstances[$moduleDetailFile]);
        $jsFileNames           = [
            "modules.Users.resources.Detail",
            'modules.'.$moduleName.'.resources.PreferenceDetail',
            'modules.'.$moduleName.'.resources.PreferenceEdit',
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
