<?php
/* ********************************************************************************
 * The content of this file is subject to the Tooltip Manager ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
ini_set('display_errors', '0');

class TooltipManager_Settings_View extends Settings_Vtiger_Index_View
{

    function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request)
    {
        parent::preProcess($request);
        // Check module valid
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('QUALIFIED_MODULE', $module);
        $rs=$adb->pquery("SELECT * FROM `vte_modules` WHERE module=? AND valid='1';",array($module));
        if($adb->num_rows($rs)==0) {
            $viewer->view('InstallerHeader.tpl', $module);
        }
    }

    public function process(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
        $rs = $adb->pquery("SELECT * FROM `vte_modules` WHERE module=? AND valid='1';", array($module));
        if ($adb->num_rows($rs) == 0) {
            $this->step3($request);
        } else {
            $mode = $request->getMode();
            if ($mode) {
                $this->$mode($request);
            } else {
                $this->renderSettingsUI($request);
            }
        }
    }

    function step3(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view('Step3.tpl', $module);
    }


    public function renderSettingsUI(Vtiger_Request $request)
    {
        global $mod_strings, $app_strings, $theme;
        $module_list = array();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $viewer->assign('MODULE_LBL', $moduleName);

        $i = 0;
        $allEntityModules = Vtiger_Module_Model::getEntityModules();
        foreach ($allEntityModules as $item){
            if ($item->name != 'TooltipManager' && $item->name != 'Potentials' && $item->name != 'Quotes'){
                $module_model = Vtiger_Module_Model::getInstance($item->name);
                if($currentUserModel->hasModulePermission($module_model->getId()) && $module_model->isActive() && $module_model->isPermitted('DetailView') && Users_Privileges_Model::isPermitted($item->name)){
                    $module_list[$i]['tabid'] = $item->id;
                    $module_list[$i]['name'] = $item->name;
                    $module_list[$i]['tablabel'] = $item->name;
                    $i++;
                }

            }
        }

        $viewer->assign('MODULE_LIST', $module_list);

        $selected_module = $request->get('selected_module');
        $viewer->assign('SELECTED_MODULE', $selected_module);
        $viewer->assign('SELECTED_MODULE_NAME', getTabModuleName($selected_module));

        $viewer->assign("MODULE_LBL", $mod_strings[$moduleName]);
        $viewer->assign('TAB_MODULE_NAME', getTabModuleName($selected_module));
        $viewer->assign('APP_STRINGS', $app_strings);

        if ($selected_module) {
            $adb = PearDatabase::getInstance();
            // get all fields
            $moduleInstance = Vtiger_Module::getInstance($selected_module);
            $fields = array();
            foreach ($moduleInstance->getFields() as $row) {
                if ($row->name != 'imagename'
                    && !in_array($row->uitype,array(61, 122))
                    && in_array($row->presence,array(0, 2))){
                    $fields[] = array(
                        'icon' => $row->icon ? $row->icon : 'layouts/vlayout/modules/TooltipManager/resources/info_icon.png',
                        "preview_type"=> $row->previewtype,
                        'helpinfo' => $row->helpinfo,
                        'fieldid' => $row->id,
                        'fieldname' => $row->name,
                        'fieldlabel' => vtranslate($row->label,$selected_module)
                    );
                }
            }

            $saveForm = $request->get('save_form');
            if (isset($saveForm)) {
                foreach ($fields as $field) {
                    $fieldId = $field['fieldid'];
                    $previewTypeFieldId = 'preview_type_' . $fieldId;
                    $getPreviewTypeFieldId = $request->get($previewTypeFieldId);
                    $fieldHelpInfo = 'field_helpinfo_' . $fieldId;
                    $getFieldHelperInfo = $request->get($fieldHelpInfo);
                    $fieldIcon = 'field_icon_' . $fieldId;
                    $getFieldIcon = $request->get($fieldIcon);
                    if ($getPreviewTypeFieldId) {
                        $sql_update = "UPDATE `vtiger_field` SET `helpinfo`=?, `icon`=?  , `preview_type`= ? WHERE (`fieldid`=?)";
                        $adb->pquery($sql_update, array($getFieldHelperInfo, $getFieldIcon, $getPreviewTypeFieldId, $fieldId));
                    }
                }
            }

            $deleteForm = $request->get('delete_form');
            if (!empty($deleteForm) && !empty($request->get('selected_field'))) {
                $fieldId = $request->get('selected_field');
                $sql_update = "UPDATE `vtiger_field` SET `helpinfo`=?, `icon`=?  , `preview_type`= ? WHERE (`fieldid`=?)";
                $adb->pquery($sql_update, array('', '', '', $fieldId));
            }

            $viewer->assign('FIELD_LIST', $fields);

            if ($request->get('selected_field')) {
                $fieldInfo = Vtiger_Field::getInstance($request->get('selected_field'));
                $selected_field['icon'] = $fieldInfo->icon;
                $selected_field['fieldid'] = $fieldInfo->id;
                $selected_field['fieldname'] = $fieldInfo->name;
                $selected_field['fieldlabel'] = $fieldInfo->label;
                $selected_field['helpinfo'] = $fieldInfo->helpinfo;
                $selected_field['preview_type'] = $fieldInfo->previewtype;
                $viewer->assign('SELECTED_FIELD', $selected_field);
            }

        }

        $viewer->view('Settings.tpl', $moduleName);
    }

    // Injecting custom javascript resources
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.$moduleName.resources.Settings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
