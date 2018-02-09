<?php

class TimeCalculator_Edit_View extends Vtiger_Edit_View {
    public function process(Vtiger_Request $request) {
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

        parent::process($request);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();

        $jsFileNames = [];
        $LongCarriesModel=Vtiger_Module_Model::getInstance('LongCarries');
        if ($LongCarriesModel && $LongCarriesModel->isActive()) {
            $jsFileNames[] = "modules.LongCarries.resources.EditBlock";
        }

        $FlightsModel=Vtiger_Module_Model::getInstance('Flights');
        if ($FlightsModel && $FlightsModel->isActive()) {
            $jsFileNames[] = "modules.Flights.resources.EditBlock";
        }

        $ElevatorsModel=Vtiger_Module_Model::getInstance('Elevators');
        if ($ElevatorsModel && $ElevatorsModel->isActive()) {
            $jsFileNames[] = "modules.Elevators.resources.EditBlock";
        }

        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}