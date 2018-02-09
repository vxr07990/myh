<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class LongDistanceDispatch_List_View extends Vtiger_Index_View
{
    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        header('Location:index.php?module=Orders&view=LDDList');
    }




    //	function process (Vtiger_Request $request) {
    //		$viewer = $this->getViewer ($request);
    //		$moduleName = $request->getModule();
    //		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
    //		$this->viewName = $request->get('viewname');
    //
    //		$this->initializeListViewContents($request, $viewer);
    //		$viewer->assign('VIEW', $request->get('view'));
    //		$viewer->assign('MODULE_MODEL', $moduleModel);
    //		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    //		$viewer->view('ListViewContents.tpl', $moduleName);
    //	}
}
