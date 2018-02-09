<?php

class OrdersTaskAddresses_MassActionAjax_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('generateNewBlock');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function generateNewBlock(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('OrdersTaskAddresses');

        $moduleModel = $recordModel->getModule();
        $moduleFields = $moduleModel->getFields('LBL_ADDRESS_DETAIL');
        unset($moduleFields['orderstask_id']);
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('BLOCK_TITLE', "");
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('ADDRESSES_BLOCK_FIELDS', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl', $moduleName, true);
    }

}
