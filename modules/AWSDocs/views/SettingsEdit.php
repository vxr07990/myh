<?php

/**
 * vTiger backup module by VGS Global
 * @package        VGSBackup Module
 * @author         Conrado Maggi
 * @license        Comercial / VPL
 * @copyright      2014 VGS Global
 * @version        Release: 1.0
 */
class AWSDocs_SettingsEdit_View extends Settings_Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        if ($request->get('msg') == 'credentials-error') {
            $viewer->assign('CREDENTIALS_ERROR', true);
        }
        if ($request->get('msg') == 'bucket-error') {
            $viewer->assign('BUCKET_ERROR', $this->getSettingsArray());
        }
        $viewer->assign('AWS_SETTINGS', $this->getSettingsArray());
        $viewer->view('SettingsEdit.tpl', $request->getModule());
    }

    public function getSettingsArray()
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_awsdocsettings", []);
        while ($result_set = $adb->fetch_array($result)) {
            $settings = $result_set;
        }

        return $settings;
    }
}
