<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class Escrows_Module_Model extends Vtiger_Module_Model {
    public function setViewerForEscrows(&$viewer, $recordId = false){
        $moduleFields = $this->getFields('LBL_ITEMCODES_MAPPING');
        if ($recordId) {
            $viewer->assign('ITEMCODES_MAPPING_LIST', $this->getEscrows($recordId));
        }
        $viewer->assign('ITEMCODES_MAPPING_BLOCK_FIELDS', $moduleFields);
    }

    public function setViewerForEscrowsWithinActual(&$viewer, $recordId = false){
        $moduleFields = $this->getFields('LBL_ITEMCODES_MAPPING');
        if ($recordId) {
            $viewer->assign('ITEMCODES_MAPPING_LIST', $this->getEscrowsWithinActual($recordId));
        }
        $viewer->assign('ITEMCODES_MAPPING_BLOCK_FIELDS', $moduleFields);
    }

    public function getEscrows($recordId){
        $itemCodesMapping=array();
        $adb = PearDatabase::getInstance();

        $rs=$adb->pquery("SELECT vtiger_escrows.escrowsid
                FROM vtiger_escrows
                INNER JOIN vtiger_crmentity ON vtiger_escrows.escrowsid=vtiger_crmentity.crmid
                WHERE deleted=0 AND escrows_agentcompgr=?",array($recordId));
        if($adb->num_rows($rs)>0) {
            while($row=$adb->fetch_array($rs)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($row['escrowsid']);
                if($_REQUEST['isDuplicate'] == true) {
                    $recordModel->set('id','');
                }
                $itemCodesMapping[$row['escrowsid']] = $recordModel;
            }
        }
        return $itemCodesMapping;
    }

    public function getEscrowsWithinActual($recordId){
        $itemCodesMapping=array();
        $adb = PearDatabase::getInstance();

        $rs=$adb->pquery("SELECT vtiger_escrows.escrowsid
                FROM vtiger_escrows
                INNER JOIN vtiger_crmentity ON vtiger_escrows.escrowsid=vtiger_crmentity.crmid
                WHERE deleted=0 AND escrows_to_actual=?",array($recordId));
        if($adb->num_rows($rs)>0) {
            while($row=$adb->fetch_array($rs)) {
                $itemCodesMapping[$row['escrowsid']]=Vtiger_Record_Model::getInstanceById($row['escrowsid']);
            }
        }
        return $itemCodesMapping;
    }

    public function isSummaryViewSupported() {
        return false;
    }

    public function saveEscrows($request, $relId) {
        for($index = 1; $index <= $request['numMapping']; $index++){
            $deleted = $request['mapping_deleted_'.$index];
            $escrowsid = $request['escrowsid_'.$index];
            if($deleted == 'deleted'){
                $recordModel=Vtiger_Record_Model::getInstanceById($escrowsid);
                $recordModel->delete();
            }else{
                if($escrowsid == ''){
                    $recordModel=Vtiger_Record_Model::getCleanInstance("Escrows");
                    $recordModel->set('mode','');
                }else{
                    $recordModel=Vtiger_Record_Model::getInstanceById($escrowsid);
                    $recordModel->set('id',$escrowsid);
                    $recordModel->set('mode','edit');
                }
                $recordModel->set('escrows_desc',$request['escrows_desc_'.$index]);
                $recordModel->set('escrows_status',$request['escrows_status_'.$index]);
                $recordModel->set('escrows_calculation_type',$request['escrows_calculation_type_'.$index]);
                $recordModel->set('escrows_pct_amount',$request['escrows_pct_amount_'.$index]);
                $recordModel->set('escrows_chargeback_from',$request['escrows_chargeback_from_'.$index]);
                $recordModel->set('escrows_discount_type',$request['escrows_discount_type_'.$index]);
                $recordModel->set('escrows_chargeback_type',$request['escrows_chargeback_type_'.$index]);
                $recordModel->set('escrows_chargeback_to',$request['escrows_chargeback_to_'.$index]);
                $recordModel->set('escrows_from_itemcode',$request['escrows_from_itemcode_'.$index]);
                $recordModel->set('escrows_to_itemcode',$request['escrows_to_itemcode_'.$index]);
                $recordModel->set('escrows_agentcompgr',$relId);
                $recordModel->save();
            }
        }
    }

    public function saveEscrowsForActuals($request, $relId) {
        for($index = 1; $index <= $request['numMapping']; $index++){
            $deleted = $request['mapping_deleted_'.$index];
            $escrowsid = $request['escrowsid_'.$index];
            if($deleted == 'deleted'){
                $recordModel=Vtiger_Record_Model::getInstanceById($escrowsid);
                $recordModel->delete();
            }else{
                if($escrowsid == ''){
                    $recordModel=Vtiger_Record_Model::getCleanInstance("Escrows");
                    $recordModel->set('mode','');
                }else{
                    $recordModel=Vtiger_Record_Model::getInstanceById($escrowsid);
                    $recordModel->set('id',$escrowsid);
                    $recordModel->set('mode','edit');
                }
                $recordModel->set('escrows_desc',$request['escrows_desc_'.$index]);
                $recordModel->set('escrows_status',$request['escrows_status_'.$index]);
                $recordModel->set('escrows_calculation_type',$request['escrows_calculation_type_'.$index]);
                $recordModel->set('escrows_pct_amount',$request['escrows_pct_amount_'.$index]);
                $recordModel->set('escrows_chargeback_from',$request['escrows_chargeback_from_'.$index]);
                $recordModel->set('escrows_discount_type',$request['escrows_discount_type_'.$index]);
                $recordModel->set('escrows_chargeback_type',$request['escrows_chargeback_type_'.$index]);
                $recordModel->set('escrows_chargeback_to',$request['escrows_chargeback_to_'.$index]);
                $recordModel->set('escrows_from_itemcode',$request['escrows_from_itemcode_'.$index]);
                $recordModel->set('escrows_to_itemcode',$request['escrows_to_itemcode_'.$index]);
                $recordModel->set('escrows_to_actual',$relId);
                $recordModel->save();
            }
        }
    }
}