<?php
class WFLocations_MoveLocation_View extends Vtiger_Popup_View
{
    public function process(Vtiger_Request $request)
    {
      $db             = PearDatabase::getInstance();
      $viewer         = $this->getViewer($request);
      $moduleName     = $request->getModule();
      $moduleInstance = Vtiger_Module::getInstance($moduleName);
      $location_field = Vtiger_Field_Model::getInstance('wflocation_base',$moduleInstance);
      $location_field->label = "Location";
      $detail_view = $request->get('detail_view') ?: false;

      $viewer->assign('LOCATION_IDS',$request->get('location_ids'));
      $viewer->assign('DETAIL_VIEW',$detail_view);
      $viewer->assign('FIELD_MODEL',$location_field);
      $viewer->assign('MODULE',$moduleName);
      $viewer->view('MoveLocation.tpl', $moduleName);
    }
}
