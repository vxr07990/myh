<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Opportunities_GroupedBySalesPerson_Dashboard extends Potentials_GroupedBySalesPerson_Dashboard
{
    public function getSearchParams($assignedto, $stage)
    {
        $listSearchParams = array();
        //$conditions = array(array('assigned_user_id','e',$assignedto),array("opportunitystatus","e",$stage));
		$conditions = array(array("opportunitystatus","e",$stage));
        $listSearchParams[] = $conditions;
        return '&search_params='. urlencode(json_encode($listSearchParams));
    }

    public function process(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $currentUserId = $currentUser->getId();
        $linkId = $request->get('linkid');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $data = $moduleModel->getPotentialsCountBySalesPerson($currentUserId);
        $listViewUrl = $moduleModel->getListViewUrl();
        for ($i = 0;$i<count($data);$i++) {
            $data[$i]["links"] = $listViewUrl.$this->getSearchParams($data[$i]["last_name"], $data[$i]["opportunitystatus"]);
            //OT1884
            //translate the sales stage here because it's used for the search params.
            //$data[$i]['sales_stage'] = vtranslate($data[$i]['sales_stage'], $moduleName);
        }
        //file_put_contents('logs/devLog.log', "\n group by sales person data : ".print_r($data,true), FILE_APPEND);

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('DATA', $data);

        //Include special script and css needed for this widget
        $viewer->assign('STYLES', $this->getHeaderCss($request));
        $viewer->assign('CURRENTUSER', $currentUser);

        $content = $request->get('content');
        if (!empty($content)) {
            $viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/GroupBySalesPerson.tpl', $moduleName);
        }
    }
}
