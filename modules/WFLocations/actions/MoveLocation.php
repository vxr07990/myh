<?php

class WFLocations_MoveLocation_Action extends Vtiger_ActionAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
      $return = ['success' => true, 'message'=>''];
      $base_location = Vtiger_Record_Model::getInstanceById($request->get('wflocation_base'),'WFLocations');
      if(!$base_location) {
        $return['success'] = false;
        $return['message'] = "Base Location not found";
      }

      $module = $base_location->getModule();

      $locations = $request->get('location_ids');
      foreach($locations as $location_id) {
        $location = Vtiger_Record_Model::getInstanceById($location_id,'WFLocations');
        if(!$location) {
          $return['success'] = false;
          $return['message'] = "Location not found while updating";
        }

        $location->updateBaseLocation($base_location,$request);
      }
      $url = $module->getListViewUrl();
      if($request->get('detail_view')) {
        $url = $location->getDetailViewUrl();
      }

      header('Location:' . $url);
    }
}
