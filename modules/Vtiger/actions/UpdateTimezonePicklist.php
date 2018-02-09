<?php

class Vtiger_UpdateTimezonePicklist_Action extends Vtiger_BasicAjax_Action {
    public function process(Vtiger_Request $request) {
        $formattedDate = DateTimefield::convertToDBFormat($request->get('date'));
        $usersModuleModel = Vtiger_Module_Model::getInstance('Users');
        $timeZoneField = $usersModuleModel->getField('time_zone');

        $timeZoneValues = $timeZoneField->getPicklistValues($formattedDate);

        $response = new Vtiger_Response();
        $response->setResult($timeZoneValues);
        $response->emit();
    }
}
