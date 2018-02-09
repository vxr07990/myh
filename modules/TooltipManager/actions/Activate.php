<?php
/* ********************************************************************************
 * The content of this file is subject to the Field Tooltip Manager ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class TooltipManager_Activate_Action extends Vtiger_Action_Controller
{
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function __construct() {
        parent::__construct();
        $this->exposeMethod('valid');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function valid(Vtiger_Request $request) {
        global $adb;
        $response = new Vtiger_Response();
        $module = $request->getModule();
        $adb->pquery("UPDATE `vte_modules` SET `valid`='1' WHERE (`module`=?);",array($module));
        $response->setResult('success');
        $response->emit();
    }

}