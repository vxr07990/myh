<?php

/**
 * vTiger backup Module
 * @package        VGSBackup Module
 * @author         Conrado Maggi
 * @license        Comercial / VPL
 * @copyright      2014 VGS Global
 * @version        Release: 1.0
 */
class AWSDocs_SettingsDetail_View extends Settings_Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer   = $this->getViewer($request);
        $settings = $this->getSettingsArray();
        $viewer->assign('AWS_SETTINGS', $settings);
        $viewer->view('SettingsDetail.tpl', $request->getModule());
    }

    public function getSettingsArray()
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_awsdocsettings", []);
        while ($result_set = $adb->fetch_array($result)) {
            $settings = $result_set;
        }
        if ($settings['aws_secret'] != '') {
            $settings['aws_secret'] = '***************************';
        }

        return $settings;
    }
}
