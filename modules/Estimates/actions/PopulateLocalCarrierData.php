<?php
/**
  * This class handles the Estimates PopulateLocalCarrier action processing.
  */
class Estimates_PopulateLocalCarrier_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $record = Vtiger_Record_Model::getInstanceById($request->get('source'));
        /*
        $current_user = Users_Record_Model::getCurrentUserModel();
        if ($current_user->isAgencyAdmin()) {
            $entity->column_fields['self_haul_allowed'] = 1;
        }
        */

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($record);
        $response->emit();
    }
}
