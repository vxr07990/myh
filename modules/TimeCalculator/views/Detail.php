<?php

class TimeCalculator_Detail_View extends Vtiger_Detail_View {
    public function showModuleBasicView(Vtiger_Request $request) {
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $LongCarriesModel=Vtiger_Module_Model::getInstance('LongCarries');
        if($LongCarriesModel && $LongCarriesModel->isActive()) {
            $viewer->assign('LONGCARRIES_MODULE_MODEL', $LongCarriesModel);
            $LongCarriesModel->setViewerForLongCarries($viewer, $recordId);
        }

        $FlightsModel=Vtiger_Module_Model::getInstance('Flights');
        if($FlightsModel && $FlightsModel->isActive()) {
            $viewer->assign('FLIGHTS_MODULE_MODEL', $FlightsModel);
            $FlightsModel->setViewerForFlights($viewer, $recordId);
        }

        $ElevatorsModel=Vtiger_Module_Model::getInstance('Elevators');
        if($ElevatorsModel && $ElevatorsModel->isActive()) {
            $viewer->assign('ELEVATORS_MODULE_MODEL', $ElevatorsModel);
            $ElevatorsModel->setViewerForElevators($viewer, $recordId);
        }


        parent::showModuleBasicView($request);
    }

    public function showModuleDetailView(Vtiger_Request $request){
        $moduleName  = $request->getModule();
        $recordId    = $request->get('record');

        $viewer = $this->getViewer($request);

        $LongCarriesModel=Vtiger_Module_Model::getInstance('LongCarries');
        if($LongCarriesModel && $LongCarriesModel->isActive()) {
            $viewer->assign('LONGCARRIES_MODULE_MODEL', $LongCarriesModel);
            $LongCarriesModel->setViewerForLongCarries($viewer, $recordId);
        }

        $FlightsModel=Vtiger_Module_Model::getInstance('Flights');
        if($FlightsModel && $FlightsModel->isActive()) {
            $viewer->assign('FLIGHTS_MODULE_MODEL', $FlightsModel);
            $FlightsModel->setViewerForFlights($viewer, $recordId);
        }

        $ElevatorsModel=Vtiger_Module_Model::getInstance('Elevators');
        if($ElevatorsModel && $ElevatorsModel->isActive()) {
            $viewer->assign('ELEVATORS_MODULE_MODEL', $ElevatorsModel);
            $ElevatorsModel->setViewerForElevators($viewer, $recordId);
        }

        parent::showModuleDetailView($request);
    }

}