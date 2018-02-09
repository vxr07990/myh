<?php

class MenuGroups_MassActionAjax_View extends Vtiger_MassActionAjax_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('generateNewBlock');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    function generateNewBlock(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('MenuGroups');

        $moduleModel = $recordModel->getModule();

        $moduleFields = $moduleModel->getFields('LBL_MENUGROUPS_INFORMATION');
        $viewer->assign('MENUGROUPS_RECORD_MODEL', $recordModel);
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('FIELDS_LIST', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl','MenuGroups',true);
    }

}