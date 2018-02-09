<?php
include_once 'include/fields/DateTimeField.php';

class Orders_ColorSettings_View extends Vtiger_Index_View
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
        $query          = $adb->query("SELECT * FROM vtiger_colorsettings");
        $colorAssigned  = $adb->query_result($query, 0, 'color'); //Assigned
        $colorAPU       = $adb->query_result($query, 1, 'color'); //APU
        $colorShorthaul = $adb->query_result($query, 2, 'color'); //Short Haul
        $colorOverflow  = $adb->query_result($query, 3, 'color'); //Overflow
        if ($adb->num_rows($query) > 4) { //no tengo en cuenta los valores "fijos"
            while ($arr = $adb->fetch_array($query)) {
                if ($arr['value'] != 'assigned' && $arr['value'] != 'apu' && $arr['value'] != 'short_haul' && $arr['value'] != 'overflow') {
                    $colorsPercentage[] = ["daystopudate" => $arr['value'], "color" => $arr['color']];
                }
            }
        } else {
            $colorsPercentage = [];
        }
        $viewer->assign('colorapu', $colorAPU);
        $viewer->assign('colorasignacion', $colorAssigned);
        $viewer->assign('colorshorthaul', $colorShorthaul);
        $viewer->assign('coloroverflow', $colorOverflow);
        $viewer->assign('CP_ARRAY', $colorsPercentage);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('ColorSetting.tpl', $moduleName);
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
            "modules.Orders.resources.ColorSettings",
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
