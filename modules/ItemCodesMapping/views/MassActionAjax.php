<?php

class ItemCodesMapping_MassActionAjax_View extends Vtiger_IndexAjax_View
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
        $recordModel = Vtiger_Record_Model::getCleanInstance('ItemCodesMapping');

        $moduleModel = $recordModel->getModule();

        $moduleFields = $moduleModel->getFields('LBL_ITEMCODES_MAPPING');
        $viewer->assign('ITEMCODES_MAPPING_RECORD_MODEL', $recordModel);
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('BLOCK_TITLE', "{$recordModel->get('itcmapping_businessline')} / {$recordModel->get('itcmapping_billingtype')} / {$recordModel->get('itcmapping_authority')}");
        $viewer->assign('FIELDS_LIST', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('NEW_BLOCK', true);
        echo $viewer->view('BlockEditFields.tpl', 'ItemCodesMapping', true);
    }

    public function duplicateBlock(Vtiger_Request $request)
    {
        global $adb;
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $copyRowNo = $request->get('copy_rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('ItemCodesMapping');

        $moduleModel = $recordModel->getModule();
        $moduleFields = $moduleModel->getFields('LBL_ITEMCODES_MAPPING');

        foreach ($moduleFields as $fieldName=>$fieldModel) {
            $fieldValue = $request->get($fieldName."_{$copyRowNo}");
            if (in_array($fieldName, array('itcmapping_businessline', 'itcmapping_billingtype', 'itcmapping_authority', 'commodities')) && is_array($fieldValue) && in_array('All', $fieldValue)) {
//                $fieldValue=getAllPickListValues($fieldName);
                $fieldValue=$fieldModel->getPicklistValues();
            }
            if ($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        $viewer->assign('ITEMCODES_MAPPING_RECORD_MODEL', $recordModel);
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('BLOCK_TITLE', "{$recordModel->get('itcmapping_businessline')} / {$recordModel->get('itcmapping_billingtype')} / {$recordModel->get('itcmapping_authority')}");
        $viewer->assign('FIELDS_LIST', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('NEW_BLOCK', true);
        echo $viewer->view('BlockEditFields.tpl', 'ItemCodesMapping', true);
    }
}
