<?php
/* ********************************************************************************
* The content of this file is subject to the Quoting Tool ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */

include_once 'modules/QuotingTool/QuotingTool.php';

/**
 * Class QuotingTool_List_View
 */
class QuotingTool_List_View extends Vtiger_List_View
{


    /**
     * @param Vtiger_Request $request
     * @return array
     */
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/layouts/vlayout/modules/QuotingTool/resources/css/font-awesome-4.5.0/css/font-awesome.min.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {

        $headerScriptInstances = parent::getHeaderScripts($request);

        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.Vtiger.resources.List",
            "modules.$moduleName.resources.List",
            "modules.Settings.Vtiger.resources.Index",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
