<?php

class WFInventory_GetUserDefinedFields_Action extends Vtiger_Action_Controller
{
  public function checkPermission(Vtiger_Request $request)
  {
      return;
  }

  public function process(Vtiger_Request $request)
  {

    $account = preg_replace("/[^0-9]/", "", $request->get('id'));

    if($account == '') {
      $result = ['success' => false, 'data' => 'No valid account'];
    } else {
      $db = PearDatabase::getInstance();
      $id = $db->getOne('SELECT `wfconfigurationid` FROM `vtiger_wfconfiguration` WHERE `wfaccount` = ' . $account);

      $module = Vtiger_Module_Model::getInstance('WFConfiguration');

      $moduleArray = Vtiger_Record_Model::getRecordAsArray($id,$module);


      if(!empty($moduleArray)) {
        $result = ['success' => true, 'data' => $moduleArray];
      } else {
        $result = ['success' => false, 'data' => $moduleArray];
      }  
    }

    $response = new Vtiger_Response();
    $response->setResult($result);
    $response->emit();
  }
}
