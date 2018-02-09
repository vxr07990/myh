<?php

/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 4/24/2017
 * Time: 10:00 AM
 */
class Containers_PopulateContainerTypeData_Action extends Vtiger_BasicAjax_Action
{

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
        $result = Vtiger_Record_Model::getInstanceById($request->get('source'));
        $response = new Vtiger_Response();
        if ($result) {
            $response->setResult($result->entity->column_fields);
        } else {
            $response->setError('Error retrieving container type', 'Failed to get record');
        }
        $response->emit();
    }
}
