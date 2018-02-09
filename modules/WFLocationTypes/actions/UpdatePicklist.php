<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/29/2017
 * Time: 3:15 PM
 */
class WFLocationTypes_UpdatePicklist_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        $moduleInstance = Vtiger_Module::getInstance($request->get('module'));
        $fieldInstance = Vtiger_Field_Model::getInstance($request->get('name'), $moduleInstance);
        if($fieldInstance){
            $updatedPicklist = $fieldInstance->getPicklistValues($request->get('warehouse'), $request->get('record'));
            $result = array('success'=>true, 'picklist'=>$updatedPicklist);
        } else {
            $result = array('success'=>false);
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
