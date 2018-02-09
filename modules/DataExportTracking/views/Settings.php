<?php
/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
ini_set('display_errors','0');
class DataExportTracking_Settings_View extends Settings_Vtiger_Index_View {
    function __construct() {
        parent::__construct();
    }



    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if ($mode) {
            $this->$mode($request);
        } else {
            $this->renderSettingsUI($request);
        }
    }

    function renderSettingsUI(Vtiger_Request $request) {

        $moduleName = $request->getModule();


        $viewer = $this->getViewer($request);
        $viewer->assign('MODEL',$this ->getSettingRecord());

        $viewer->assign('QUALIFIED_MODULE',$moduleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('Settings.tpl',$moduleName,true);
    }
    function  EditSettings(Vtiger_Request $request){

        $moduleName = $request->getModule();
        $setting_id = $request->get('setting_id');

        $viewer = $this->getViewer($request);
        $viewer->assign('MODEL',$this ->getSettingRecord($setting_id));

        $viewer->assign('QUALIFIED_MODULE',$moduleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('SettingsEdit.tpl',$moduleName,true);

    }
    function  SaveSettings(Vtiger_Request $request){

        $moduleName = $request->getModule();
        $setting_id = $request->get('setting_id');
        $adb = PearDatabase::getInstance();

        $track_listview_exports  = $request->get('track_listview_exports')=='true'?1:0;
        $track_report_exports   = $request->get('track_report_exports')=='true'?1:0;
        $track_scheduled_reports = $request->get('track_scheduled_reports')=='true'?1:0;
        $track_copy_records      = $request->get('track_copy_records')=='true'?1:0;
        $notification_email      = $request->get('notification_email');

        $values_insert = array($track_listview_exports,$track_report_exports,$track_scheduled_reports,$track_copy_records,$notification_email);
        $arr_value['track_listview_exports']  = $track_listview_exports;
        $arr_value['track_report_exports']    = $track_report_exports;
        $arr_value['track_scheduled_reports'] = $track_scheduled_reports;
        $arr_value['track_copy_records']      = $track_copy_records;
        $arr_value['notification_email']      = $notification_email;

        if($setting_id == ''){
            $sql="INSERT  INTO vte_data_export_tracking(track_listview_exports,track_report_exports,track_scheduled_reports,track_copy_records,notification_email)
                  VALUES(?,?,?,?,?)";
        }
        else {
            $arr_value['id'] = $setting_id;
            $sql="UPDATE  `vte_data_export_tracking`
                              SET track_listview_exports =?,
                                track_report_exports = ?,
                                track_scheduled_reports = ?,
                                track_copy_records=?,
                                notification_email=? WHERE id = ".$setting_id;
        }
        $rs=$adb->pquery($sql,$values_insert);

        $viewer = $this->getViewer($request);
        $viewer->assign('MODEL',$arr_value);

        $viewer->assign('QUALIFIED_MODULE',$moduleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('Settings.tpl',$moduleName,true);

    }

    function getSettingRecord($id =''){
        $adb = PearDatabase::getInstance();
        if($id != '') $sql="SELECT * FROM `vte_data_export_tracking` WHERE id = '.$id.' LIMIT 0,1`";
        else  $sql="SELECT * FROM `vte_data_export_tracking` LIMIT 0,1";

        $rs=$adb->pquery($sql,array());
        $arr_value = array();
        if($adb->num_rows($rs)>0) {
            $arr_value['id'] = $adb->query_result($rs, 0, 'id');
            $arr_value['track_listview_exports'] = $adb->query_result($rs, 0, 'track_listview_exports');
            $arr_value['track_report_exports'] = $adb->query_result($rs, 0, 'track_report_exports');
            $arr_value['track_scheduled_reports'] = $adb->query_result($rs, 0, 'track_scheduled_reports');
            $arr_value['track_copy_records'] = $adb->query_result($rs, 0, 'track_copy_records');
            $arr_value['notification_email'] = $adb->query_result($rs, 0, 'notification_email');
        }
        return $arr_value;
    }

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