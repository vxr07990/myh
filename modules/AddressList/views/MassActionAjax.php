<?php

class AddressList_MassActionAjax_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('generateNewBlock');
        $this->exposeMethod('duplicateBlock');
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
        global $adb;
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('AddressList');

        $moduleModel = $recordModel->getModule();

        $moduleFields = $moduleModel->getFields('LBL_ADDRESSES');
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('BLOCK_TITLE', "");
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('ADDRESSLIST_BLOCK_FIELDS', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl', $moduleName, true);
    }

    public function duplicateBlock(Vtiger_Request $request)
    {
        global $adb;
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $copyRowNo = $request->get('copy_rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('AddressList');

        $moduleModel = $recordModel->getModule();
        $moduleFields = $moduleModel->getFields('LBL_ADDRESSES');

        foreach ($moduleFields as $fieldName=>$fieldModel) {
            $addressTypes = ['Origin', 'Destination', 'Customer Mailing', 'Customer Billing', 'Customer Shipping'];
            $fieldValue = $request->get($fieldName."_{$copyRowNo}");
            if ($fieldModel->isEditable()) {
                if($fieldName = 'address_type' && in_array($fieldValue,$addressTypes)) continue;
                $fieldModel->set('fieldvalue', $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('BLOCK_TITLE', "");
        $viewer->assign('ADDRESSLIST_BLOCK_FIELDS', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl', 'AddressList', true);
    }
}
