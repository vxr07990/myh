<?php

class WFLineItems_GetLineItemDetails_Action extends Vtiger_ActionAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
      $return = ['success' => false, 'message'=>'Invalid Search'];
      $fieldName = $request->get('fieldname');
      $record = $request->get('record');
      $db = PearDatabase::getInstance();
      $sql = "SELECT `quantity`, `location` FROM `vtiger_wfinventorylocations`
      WHERE `vtiger_wfinventorylocations`.`inventory` = ?";
      $details = [];


      if(substr($fieldName,0,11) == 'wfinventory') {
        $inventory[] = $record;
      } elseif(substr($fieldName,0,9) == 'wfarticle') {
        $items = $db->pquery("SELECT `wfinventoryid` FROM `vtiger_wfinventory` WHERE `article` = ?",[$record]);
        while($row = $items->fetchRow()) {
          $inventory[] = $row['wfinventoryid'];
        }
      } else {
        return $return;
      }
      if(isset($inventory) ) {
        foreach($inventory as $inv) {
          $record = Vtiger_Record_Model::getInstanceById($inv,'WFInventory');

          $details['description'] = $record->get('description');
          $locations = $db->pquery($sql,[$inv]);

          while($row = $locations->fetchRow()) {
            $details['onhand'] += $row['quantity'];
            $locationObject = Vtiger_Record_Model::getInstanceById($row['location'],'WFLocations');
            $details['locations'][] = $locationObject->get('tag');
          }
        }
      }

      $return['success'] = true;
      $return['data'] = $details;
      unset($return['message']);

      $response = new Vtiger_Response();
      $response->setResult($return);
      $response->emit();
    }
}
