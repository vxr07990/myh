<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 10/25/2017
 * Time: 2:37 PM
 */
class WFInventoryLocations_PopulateLocationType_Action extends Vtiger_BasicAjax_Action
{

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
        $result = Vtiger_Record_Model::getInstanceById($request->get('source'));
        $response = new Vtiger_Response();
        if ($result) {
            $locationInstance = Vtiger_Record_Model::getInstanceById($result->get('wflocation_type'));
            if($locationInstance) {
                $response->setResult($locationInstance->entity->column_fields);
            } else {
                $response->setError('Error retrieving location type', 'Location type not found for location record');
            }
        } else {
            $response->setError('Error retrieving location type', 'Failed to get location record');
        }
        $response->emit();
    }
}
