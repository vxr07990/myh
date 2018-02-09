<?php

/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_FavoriteList_View extends Vtiger_Index_View
{
    function __construct()
    {
        parent::__construct();
    }
    

    function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->renderList($mode, $request);
        } else {
            $this->renderFavoriteHome($request);
        }
    }

    function renderList($mode, Vtiger_Request $request)
    {
        global $current_user;
        $skinColors = Vtiger_Util_Helper::getAllSkins();
        $themeColor = new VTEFavorite_ThemeColor_Helper();
        $themeColor->baseColor = $skinColors[$current_user->theme];
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $viewer->assign('USER_MODEL', $currentUserModel);
        $viewer->assign('BASE_COLOR', $themeColor->baseColor);
        $moduleName = $request->getModule(false);

        if ($mode == 'favorite') {
            $list = VTEFavorite_Module_Model::getRecordList($mode);
            $viewer->assign('RECORDS', $list);
            echo $viewer->view('List_Fav.tpl', $moduleName, true);
        } elseif ($mode == 'recently') {
            $list = VTEFavorite_Module_Model::getRecordList($mode);
            $viewer->assign('RECORDS', $list);
            echo $viewer->view('List_Rec.tpl', $moduleName, true);
        } elseif ($mode == 'customlist') {
            $list = VTEFavorite_Module_Model::getRecordList_CustomList();
            $viewer->assign('RECORDS', $list);
            echo $viewer->view('List_Clt.tpl', $moduleName, true);
        } else {
            echo $viewer->view('ListHome.tpl', $moduleName, true);
        }
    }

    function renderFavoriteHome(Vtiger_Request $request)
    {
        global $current_user;
        $viewer = $this->getViewer($request);
        //Test:
        $customViewModel = CustomView_Record_Model::getInstanceById(8);
        $records = $customViewModel->getRecordIds('', 'Accounts');
        $viewer->assign('RECORDS', $records);

        $moduleName = $request->getModule(false);
        echo $viewer->view('ListHome.tpl', $moduleName, true);
    }

    function getColumnsListByCvid($cvid)
    {
        global $adb;

        $sSQL = "select vtiger_cvcolumnlist.* from vtiger_cvcolumnlist";
        $sSQL .= " inner join vtiger_customview on vtiger_customview.cvid = vtiger_cvcolumnlist.cvid";
        $sSQL .= " where vtiger_customview.cvid =? order by vtiger_cvcolumnlist.columnindex";
        $result = $adb->pquery($sSQL, array($cvid));
        while ($columnrow = $adb->fetch_array($result)) {
            $columnlist[$columnrow['columnindex']] = $columnrow['columnname'];
        }
        return $columnlist;
    }


}