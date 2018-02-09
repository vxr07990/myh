<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class ItemCodesMapping_Module_Model extends Vtiger_Module_Model
{
    public function setViewerForItemCodesMapping(&$viewer, $recordId = false)
    {
        $moduleFields = $this->getFields('LBL_ITEMCODES_MAPPING');
        if ($recordId) {
            $viewer->assign('ITEMCODES_MAPPING_LIST', $this->getItemCodesMapping($recordId));
        }
        $viewer->assign('ITEMCODES_MAPPING_BLOCK_FIELDS', $moduleFields);
    }

    public function getItemCodesMapping($recordId)
    {
        $itemCodesMapping=array();
        $adb = PearDatabase::getInstance();

        $rs=$adb->pquery("SELECT vtiger_itemcodesmapping.itemcodesmappingid
                FROM vtiger_itemcodesmapping
                INNER JOIN vtiger_crmentity ON vtiger_itemcodesmapping.itemcodesmappingid=vtiger_crmentity.crmid
                WHERE deleted=0 AND itcmapping_itemcode=?", array($recordId));
        if ($adb->num_rows($rs)>0) {
            while ($row=$adb->fetch_array($rs)) {
                $itemCodesMapping[$row['itemcodesmappingid']]=Vtiger_Record_Model::getInstanceById($row['itemcodesmappingid']);
            }
        }
        return $itemCodesMapping;
    }

    public function isSummaryViewSupported()
    {
        return false;
    }

    public function saveItemCodesMapping($request, $relId)
    {
        for ($index = 1; $index <= $request['numMapping']; $index++) {
            $deleted = $request['mapping_deleted_'.$index];
            $itemcodesmappingid = $request['itemcodesmappingid_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($itemcodesmappingid);
                $recordModel->delete();
            } else {
                if ($itemcodesmappingid == '') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("ItemCodesMapping");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($itemcodesmappingid);
                    $recordModel->set('id', $itemcodesmappingid);
                    $recordModel->set('mode', 'edit');
                }
                $recordModel->set('itcmapping_businessline', $request['itcmapping_businessline_'.$index]);
                $recordModel->set('commodities', $request['commodities_'.$index]);
                $recordModel->set('itcmapping_billingtype', $request['itcmapping_billingtype_'.$index]);
                $recordModel->set('itcmapping_authority', $request['itcmapping_authority_'.$index]);
                $recordModel->set('itcmapping_glcode', $request['itcmapping_glcode_'.$index]);
                $recordModel->set('itcmapping_salesexpense', $request['itcmapping_salesexpense_'.$index]);
                $recordModel->set('itcmapping_owner_operatorexpense', $request['itcmapping_owner_operatorexpense_'.$index]);
                $recordModel->set('itcmapping_company_driverexpense', $request['itcmapping_company_driverexpense_'.$index]);
                $recordModel->set('itcmapping_lease_driverexpense', $request['itcmapping_lease_driverexpense_'.$index]);
                $recordModel->set('itcmapping_packer_expense', $request['itcmapping_packer_expense_'.$index]);
                $recordModel->set('itcmapping_3rdparty_serviceexpense', $request['itcmapping_3rdparty_serviceexpense_'.$index]);
                $recordModel->set('itcmapping_itemcode', $relId);
                $recordModel->save();
            }
        }
    }
}
