<?php

use MoveCrm\Models\User;

class WFLocationHistory_CreateHistory_Action extends Vtiger_ActionAjax_Action
{
    public function process(Vtiger_Request $request){
      $history = Vtiger_Record_Model::getCleanInstance('WFLocationHistory');
      $relLocation = Vtiger_Record_Model::getInstanceById($request->get('record'),'WFLocations');

      $varArray = [
        'toLocation' => 'wflocation_base',
        'toSlot'     => 'wfslot_configuration',
        'toWarehouse'=> 'wflocation_warehouse',
        'toStatus'   => 'wflocations_status'
      ];

      foreach($varArray as $requestVar=>$requestField) {
        $$requestVar = $request->get($requestField) ? $request->get($requestField) : $relLocation->get($requestField);
      }

      $history->set('datetime',date('Y-m-d H:i:s'));
      $history->set('location',$relLocation->get('id'));
      $history->set('user',User::current()->id);

      $history->set('from_location',$relLocation->get('wflocation_base'));
      $history->set('to_location',$toLocation);

      $history->set('from_slot',$relLocation->get('wfslot_configuration'));
      $history->set('to_slot',$toSlot);

      $history->set('from_warehouse',$relLocation->get('wflocation_warehouse'));
      $history->set('to_warehouse',$toWarehouse);

      $history->set('from_status',$relLocation->get('wflocations_status'));
      $history->set('to_status',$toStatus);

      $history->save();
    }
}
