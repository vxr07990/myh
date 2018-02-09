<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class Flights_Module_Model extends Vtiger_Module_Model {
    public function setViewerForFlights(&$viewer, $recordId = false){
        $moduleFields = $this->getFields('LBL_FLIGHT_INFORMATION');
        $viewer->assign('FLIGHTS_LIST', $this->getFlights($recordId));
        $viewer->assign('FLIGHTS_BLOCK_FIELDS', $moduleFields);
    }

    public function getFlights($recordId){
        $flights=array();
        $adb = PearDatabase::getInstance();

        if($recordId) {
            $rs = $adb->pquery("SELECT vtiger_flights.*
                FROM vtiger_flights
                INNER JOIN vtiger_crmentity ON vtiger_flights.flightsid=vtiger_crmentity.crmid
                WHERE deleted=0 AND flights_timecalc=?", array($recordId));
            if ($adb->num_rows($rs) > 0) {
                while ($row = $adb->fetch_array($rs)) {
                    $flights[] = $row;
                }
            }else{
                if ($_REQUEST['relatedMethod'] == 'Edit'){
                    // Set default items
                    $flights=array(
                        array('flightsid'=>'none','flights_number'=>'0', 'flights_percent' => '0'),
                        array('flightsid'=>'none','flights_number'=>'1', 'flights_percent' => '10'),
                        array('flightsid'=>'none','flights_number'=>'2', 'flights_percent' => '20'),
                        array('flightsid'=>'none','flights_number'=>'3', 'flights_percent' => '30'),
                        array('flightsid'=>'none','flights_number'=>'4', 'flights_percent' => '40'),
                        array('flightsid'=>'none','flights_number'=>'5', 'flights_percent' => '50'),
                        array('flightsid'=>'none','flights_number'=>'6', 'flights_percent' => '60'),
                        array('flightsid'=>'none','flights_number'=>'7', 'flights_percent' => '70'),
                    );
                }
            }
        }else{
            if ($_REQUEST['relatedMethod'] == 'Edit'){
                // Set default items
                $flights=array(
                    array('flightsid'=>'none','flights_number'=>'0', 'flights_percent' => '0'),
                    array('flightsid'=>'none','flights_number'=>'1', 'flights_percent' => '10'),
                    array('flightsid'=>'none','flights_number'=>'2', 'flights_percent' => '20'),
                    array('flightsid'=>'none','flights_number'=>'3', 'flights_percent' => '30'),
                    array('flightsid'=>'none','flights_number'=>'4', 'flights_percent' => '40'),
                    array('flightsid'=>'none','flights_number'=>'5', 'flights_percent' => '50'),
                    array('flightsid'=>'none','flights_number'=>'6', 'flights_percent' => '60'),
                    array('flightsid'=>'none','flights_number'=>'7', 'flights_percent' => '70'),
                );
            }
        }
        return $flights;
    }

    public function isSummaryViewSupported() {
        return false;
    }

    public function saveFlights($request, $relId) {
        for($index = 1; $index <= $request['numFlightsAgents']; $index++){
            if(!$request['flightId_'.$index]){
                continue;
            }
            $deleted = $request['flightDelete_'.$index];
            $flightsid = $request['flightId_'.$index];
            if($deleted == 'deleted'){
                $recordModel=Vtiger_Record_Model::getInstanceById($flightsid);
                $recordModel->delete();
            }else{
                if($flightsid == 'none'){
                    $recordModel=Vtiger_Record_Model::getCleanInstance("Flights");
                    $recordModel->set('mode','');
                }else{
                    $recordModel=Vtiger_Record_Model::getInstanceById($flightsid);
                    $recordModel->set('id',$flightsid);
                    $recordModel->set('mode','edit');
                }

                $recordModel->set('flights_number',$request['flights_number_'.$index]);
                $recordModel->set('flights_percent',$request['flights_percent_'.$index]);
                $recordModel->set('flights_timecalc',$relId);
                $recordModel->save();
            }
        }
    }
}