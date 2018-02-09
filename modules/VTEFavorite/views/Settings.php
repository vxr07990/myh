<?php

/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_Settings_View extends Vtiger_Index_View
{
    function __construct()
    {
        parent::__construct();
    }
    

    function process(Vtiger_Request $request)
    {
        $this->renderSettingsUI($request);
    }

    function renderSettingsUI(Vtiger_Request $request)
    {
        global $current_user;

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule(false);
        $type = $request->get('type');
        if ($type == null) { //default value is favorite
            $type = 'favorite';
        }
        //favorite:
        if ($type == 'favorite') {
            $allFieldsOfFav = array();

            $favSupportedModulesList = VTEFavorite_Module_Model::getSupportedModules("Fav");
            $favSupportedAllModulesList = VTEFavorite_Module_Model::getSupportedModules("AllFav");
            foreach ($favSupportedModulesList as $mmodule) {
                $temp = VTEFavorite_Module_Model::getFieldsOfModule($mmodule["moduleName"]);
                $allFieldsOfFav[$mmodule["moduleName"]] = $temp;
                if($mmodule["moduleName"] =='Contacts' || $mmodule["moduleName"] =='Leads'){
                    $fullNameField = new Vtiger_Field_Model();
                    $fullNameField->set('name','fullname');
                    $fullNameField->set('label','Full Name');
                    $allFieldsOfFav[$mmodule["moduleName"]]['fullname']=$fullNameField;
                }
            }
            $viewer->assign('FAV_MODULE_CONFIG', $favSupportedModulesList);
            $viewer->assign('FAV_ALL_MODULES', $favSupportedAllModulesList);
            $viewer->assign('FAV_ALL_FIELDS', $allFieldsOfFav);
        }
        //Recently:
        if ($type == 'recently') {
            $allFieldsOfRec = array();

            $recSupportedModulesList = VTEFavorite_Module_Model::getSupportedModules("Rec");
            $recSupportedAllModulesList = VTEFavorite_Module_Model::getSupportedModules("AllRec");
            foreach ($recSupportedModulesList as $mmodule) {
                $temp = VTEFavorite_Module_Model::getFieldsOfModule($mmodule["moduleName"]);
                $allFieldsOfRec[$mmodule["moduleName"]] = $temp;
            }
            $viewer->assign('REC_MODULE_CONFIG', $recSupportedModulesList);
            $viewer->assign('REC_ALL_MODULES', $recSupportedAllModulesList);
            $viewer->assign('REC_ALL_FIELDS', $allFieldsOfRec);
        }
        //Custom list:
        if ($type == 'customlist') {
            $allFieldsOfClt = array();

            $cltSupportedModulesList = VTEFavorite_Module_Model::getSupportedModules("Clt");
            $cltSupportedAllModulesList = VTEFavorite_Module_Model::getSupportedModules("AllClt");
            foreach ($cltSupportedModulesList as $mmodule) {
                $temp = VTEFavorite_Module_Model::getFieldsOfModule($mmodule["moduleName"]);
                $allFieldsOfClt[$mmodule["moduleName"]] = $temp;
            }
            $viewer->assign('CLT_MODULE_CONFIG', $cltSupportedModulesList);
            $viewer->assign('CLT_ALL_MODULES', $cltSupportedAllModulesList);
            $viewer->assign('CLT_ALL_FIELDS', $allFieldsOfClt);
        }
        if ($type == 'getcustomview') {
            global $adb;

            //Get param:
            $smodule = $request->get('smodule');
            //Get favorite of record:
            $emparray = array();
            $result = $adb->pquery('SELECT * FROM vtiger_customview where `entitytype`=?;', array($smodule));
            $i = 0; //resultrow
            while ($row = $adb->fetch_array($result)) {
                $emparray[$i] = $row;
                $i = $i + 1;
            }
            $viewer->assign('CUSTOMVIEWS', $emparray);
            echo $viewer->view('SettingCustomListGetViews.tpl', $moduleName, true);
            return;
        }
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('TYPE', $type);

        echo $viewer->view('Settings.tpl', $moduleName, true);
    }
}