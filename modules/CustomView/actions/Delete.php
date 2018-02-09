<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CustomView_Delete_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        $customViewModel = CustomView_Record_Model::getInstanceById($request->get('record'));
        $moduleModel = $customViewModel->getModule();

        $customViewModel->delete();

		$moduleName = $moduleModel->get("name");
		
		if($moduleName == "OrdersTask" && isset($_REQUEST['iscalendar']) && $_REQUEST['iscalendar'] == 'yes'){
			$url = "index.php?module=OrdersTask&view=LocalDispatchCapacityCalendar";
		}else if($moduleName == "OrdersTask" && $customViewModel->get('view') != ''){
			$url = "index.php?module=OrdersTask&view=".$customViewModel->get('view');
		}else{
			$url = $moduleModel->getListViewUrl();
		}
        header("Location: $url");
    }
    
    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
