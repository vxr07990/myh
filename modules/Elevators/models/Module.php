<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class Elevators_Module_Model extends Vtiger_Module_Model {
    public function setViewerForElevators(&$viewer, $recordId = false){
        $moduleFields = $this->getFields('LBL_ELEVATOR_INFORMATION');
        $viewer->assign('ELEVATORS_LIST', $this->getElevators($recordId));
        $viewer->assign('ELEVATORS_BLOCK_FIELDS', $moduleFields);
    }

    public function getElevators($recordId){
        $elevators=array();
        $adb = PearDatabase::getInstance();

        if ($recordId){
            $rs=$adb->pquery("SELECT vtiger_elevators.*
                FROM vtiger_elevators
                INNER JOIN vtiger_crmentity ON vtiger_elevators.elevatorsid=vtiger_crmentity.crmid
                WHERE deleted=0 AND elevators_timecalc=?",array($recordId));
            if($adb->num_rows($rs)>0) {
                while($row=$adb->fetch_array($rs)) {
                    $elevators[]=$row;
                }
            }else {
                if ($_REQUEST['relatedMethod'] == 'Edit') {
                    // Set default items
                    $elevators = array(
                        array('elevatorsid' => 'none', 'elevators_number' => '0', 'elevators_percent' => '0'),
                        array('elevatorsid' => 'none', 'elevators_number' => '999', 'elevators_percent' => '25'),
                    );
                }
            }
        } else{
            if ($_REQUEST['relatedMethod'] == 'Edit') {
                // Set default items
                $elevators = array(
                    array('elevatorsid' => 'none', 'elevators_number' => '0', 'elevators_percent' => '0'),
                    array('elevatorsid' => 'none', 'elevators_number' => '999', 'elevators_percent' => '25'),
                );
            }
        }


        return $elevators;
    }

    public function isSummaryViewSupported() {
        return false;
    }

    public function saveElevators($request, $relId) {
        for($index = 1; $index <= $request['numElevatorsAgents']; $index++){
            if(!$request['elevatorId_'.$index]){
                continue;
            }
            $deleted = $request['elevatorDelete_'.$index];
            $elevatorsid = $request['elevatorId_'.$index];
            if($deleted == 'deleted'){
                $recordModel=Vtiger_Record_Model::getInstanceById($elevatorsid);
                $recordModel->delete();
            }else{
                if($elevatorsid == 'none'){
                    $recordModel=Vtiger_Record_Model::getCleanInstance("Elevators");
                    $recordModel->set('mode','');
                }else{
                    $recordModel=Vtiger_Record_Model::getInstanceById($elevatorsid);
                    $recordModel->set('id',$elevatorsid);
                    $recordModel->set('mode','edit');
                }

                $recordModel->set('elevators_number',$request['elevators_number_'.$index]);
                $recordModel->set('elevators_percent',$request['elevators_percent_'.$index]);
                $recordModel->set('elevators_timecalc',$relId);
                $recordModel->save();
            }
        }
    }
}