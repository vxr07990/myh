<?php
include_once 'include/fields/DateTimeField.php';

class OrdersTask_CalendarSettings_View extends Vtiger_Index_View
{
    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function process(Vtiger_Request $request)
    {
        global $adb;
        $viewer         = $this->getViewer($request);
        $moduleName     = $request->getModule();
        $moduleModel    = Vtiger_Module_Model::getInstance($moduleName);
        $this->viewName = $request->get('viewname');
        $currentUserId = Users_Record_Model::getCurrentUserModel()->getId();
        $result   = $adb->query("SELECT * FROM vtiger_calendar_settings WHERE userid=$currentUserId");
        $numrows = $adb->num_rows($result);
        if ($numrows > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $settings['percentage_1'] = $row['percentage_1'];
                $settings['color_1'] = $row['color_1'];
                $settings['percentage_2'] = $row['percentage_2'];
                $settings['color_2'] = $row['color_2'];
                $settings['percentage_3'] = $row['percentage_3'];
                $settings['color_3'] = $row['color_3'];
                $settings['saturday_work_day'] = ( $row['saturday_work_day'] == '1' ? 'Yes' : 'No' );
                $settings['sunday_work_day'] = ( $row['sunday_work_day'] == '1' ? 'Yes' : 'No' );
            }
        } else {
            $settings['percentage_1'] = '50';
            $settings['color_1'] = '#A5D6A7';
            $settings['percentage_2'] = '80';
            $settings['color_2'] = '#FFE082';
            $settings['percentage_3'] = '100';
            $settings['color_3'] = '#EF9A9A';
            $settings['saturday_work_day'] = 'Yes';
            $settings['sunday_work_day'] = 'Yes';
        }
        $viewer->assign('SETTINGS', $settings);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('CalendarSetting.tpl', $moduleName);
    }

    public function postProcess(Vtiger_Request $request)
    {
        parent::postProcess($request);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames           = [
            "~/libraries/jquery/colorpicker/js/colorpicker.js",
            "modules.OrdersTask.resources.CalendarSettings",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames       = [
            '~/libraries/jquery/colorpicker/css/colorpicker.css',
        ];
        $cssInstances       = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
}
