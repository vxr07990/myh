<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Accounts_QuickCreateAjax_View extends Vtiger_QuickCreateAjax_View
{
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $moduleName        = $request->getModule();
        $jsFileNames       = [
            "modules.$moduleName.resources.Edit",
            "modules.$moduleName.resources.AnnualRateIncrease",
        ];
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);

        return $jsScriptInstances;
    }
}
