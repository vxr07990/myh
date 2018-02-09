<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Calendar_List_View extends Vtiger_List_View
{
  public function process(Vtiger_Request $request)
    {
        $viewer         = $this->getViewer($request);
        $moduleName     = $request->getModule();
        $moduleModel    = Vtiger_Module_Model::getInstance($moduleName);
        $this->viewName = $request->get('viewname');
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $taskstatusArray = Calendar_Module_Model::getStatusValues('task');
        $viewer->assign('TASKSTATUS_ARRAY', $taskstatusArray);
        $eventstatusArray = Calendar_Module_Model::getStatusValues('event');
        $viewer->assign('EVENTSTATUS_ARRAY', $eventstatusArray);
        $viewer->view('ListViewContents.tpl', $moduleName);
    }
}