<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Opportunities_GroupedBySalesStage_Dashboard extends Potentials_GroupedBySalesStage_Dashboard
{

    /**
     * Retrieves css styles that need to loaded in the page
     * @param Vtiger_Request $request - request model
     * @return <array> - array of Vtiger_CssScript_Model
     */
    public function getHeaderCss(Vtiger_Request $request)
    {
        $cssFileNames = array(
            //Place your widget specific css files here
        );
        $headerCssScriptInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $headerCssScriptInstances;
    }

    public function getSearchParams($stage, $assignedto, $dates)
    {
        $listSearchParams = array();
        $conditions = array();
        array_push($conditions, array("sales_stage", "e", $stage));
        if ($assignedto == '') {
            $currenUserModel = Users_Record_Model::getCurrentUserModel();
            $assignedto = $currenUserModel->getId();
        }
        if ($assignedto != 'all') {
            $groupName = getGroupName($assignedto);
            $groupName = $groupName[0];
            if (!empty($groupName)) {
                array_push($conditions, array("sales_person", "e", $groupName));
            }
        }
        if (!empty($dates)) {
            array_push($conditions, array("closingdate", "bw", $dates['start'].','.$dates['end']));
        }
        $listSearchParams[] = $conditions;
        return '&search_params='. json_encode($listSearchParams);
    }

    public function process(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();

        $linkId = $request->get('linkid');
        $owner = $request->get('owner');
        $db = PearDatabase::getInstance();
        $sql = "SELECT depth FROM `vtiger_role` JOIN `vtiger_user2role` ON `vtiger_role`.roleid = `vtiger_user2role`.roleid AND `vtiger_user2role`.userid = ?";
        $result = $db->pquery($sql, [$currentUser->getId()]);
        $depth = $result->fetchRow()[0];
        if ($depth < 5) {
            $viewer->assign('SHOW_ALL', true);
        } else {
            $viewer->assign('SHOW_ALL', false);
        }

        $dates = $request->get('expectedclosedate');

        //Date conversion from user to database format
        if (!empty($dates)) {
            $dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($dates['start']);
            $dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($dates['end']);
        }
        //file_put_contents('logs/devLog.log', "\n moduleName : {$moduleName}", FILE_APPEND);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $data = $moduleModel->getPotentialsCountBySalesStage($owner, $dates);

        $listViewUrl = $moduleModel->getListViewUrl();
        $owner = $data['owner'];
        unset($data['owner']);
        //file_put_contents('logs/devLog.log', "\n data : ".print_r($data,true)."\n owner : {$owner}", FILE_APPEND);
        for ($i = 0;$i<count($data);$i++) {
            $data[$i][] = $listViewUrl.$this->getSearchParams($data[$i][0], $owner, $dates);
            //@NOTE: sales_stage is vtranslated in Opportunities_Module_Model->getPotentialsCountBySalesStage
        }
        //file_put_contents('logs/devLog.log', "\n data after mystery loop : ".print_r($data,true), FILE_APPEND);
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
            $viewer->view('dashboards/GroupBySalesStage.tpl', $moduleName);
        }
    }
}
