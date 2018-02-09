<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class LongCarries_Module_Model extends Vtiger_Module_Model {
    public function setViewerForLongCarries(&$viewer, $recordId = false){
        $moduleFields = $this->getFields('LBL_LONGCARRY_INFORMATION');
        $viewer->assign('LONGCARRIES_LIST', $this->getLongCarries($recordId));
        $viewer->assign('LONGCARRIES_BLOCK_FIELDS', $moduleFields);
    }

    public function getLongCarries($recordId){
        $longcarries=array();
        $adb = PearDatabase::getInstance();

        if ($recordId) {
            $rs=$adb->pquery("SELECT vtiger_longcarries.*
                FROM vtiger_longcarries
                INNER JOIN vtiger_crmentity ON vtiger_longcarries.longcarriesid=vtiger_crmentity.crmid
                WHERE deleted=0 AND longcarries_timecalc=?",array($recordId));
            if($adb->num_rows($rs)>0) {
                while($row=$adb->fetch_array($rs)) {
                    $longcarries[]=$row;
                }
            }else {
                if ($_REQUEST['relatedMethod'] == 'Edit') {
                    // Set default items
                    $longcarries = array(
                        array('longcarriesid' => 'none', 'longcarries_uptoft' => '75', 'longcarries_percent' => '0'),
                        array('longcarriesid' => 'none', 'longcarries_uptoft' => '125', 'longcarries_percent' => '10'),
                        array('longcarriesid' => 'none', 'longcarries_uptoft' => '175', 'longcarries_percent' => '20'),
                        array('longcarriesid' => 'none', 'longcarries_uptoft' => '225', 'longcarries_percent' => '30'),
                        array('longcarriesid' => 'none', 'longcarries_uptoft' => '275', 'longcarries_percent' => '40'),
                        array('longcarriesid' => 'none', 'longcarries_uptoft' => '999', 'longcarries_percent' => '50'),
                    );
                }
            }
        } else {
            if ($_REQUEST['relatedMethod'] == 'Edit') {
                // Set default items
                $longcarries = array(
                    array('longcarriesid' => 'none', 'longcarries_uptoft' => '75', 'longcarries_percent' => '0'),
                    array('longcarriesid' => 'none', 'longcarries_uptoft' => '125', 'longcarries_percent' => '10'),
                    array('longcarriesid' => 'none', 'longcarries_uptoft' => '175', 'longcarries_percent' => '20'),
                    array('longcarriesid' => 'none', 'longcarries_uptoft' => '225', 'longcarries_percent' => '30'),
                    array('longcarriesid' => 'none', 'longcarries_uptoft' => '275', 'longcarries_percent' => '40'),
                    array('longcarriesid' => 'none', 'longcarries_uptoft' => '999', 'longcarries_percent' => '50'),
                );
            }
        }
        return $longcarries;
    }

    public function isSummaryViewSupported() {
        return false;
    }

    public function saveLongCarries($request, $relId) {
        for($index = 1; $index <= $request['numLongCarriesAgents']; $index++){
            if(!$request['longcarryId_'.$index]){
                continue;
            }
            $deleted = $request['longcarryDelete_'.$index];
            $longcarriesid = $request['longcarryId_'.$index];
            if($deleted == 'deleted'){
                $recordModel=Vtiger_Record_Model::getInstanceById($longcarriesid);
                $recordModel->delete();
            }else{
                if($longcarriesid == 'none'){
                    $recordModel=Vtiger_Record_Model::getCleanInstance("LongCarries");
                    $recordModel->set('mode','');
                }else{
                    $recordModel=Vtiger_Record_Model::getInstanceById($longcarriesid);
                    $recordModel->set('id',$longcarriesid);
                    $recordModel->set('mode','edit');
                }
                $recordModel->set('longcarries_uptoft',$request['longcarries_uptoft_'.$index]);
                $recordModel->set('longcarries_percent',$request['longcarries_percent_'.$index]);
                $recordModel->set('longcarries_timecalc',$relId);
                $recordModel->save();
            }
        }
    }
}