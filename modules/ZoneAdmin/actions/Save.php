<?php

include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'includes/main/WebUI.php';

class ZoneAdmin_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
		if($request->get("record")){
			$zoneadminRecordModel = Vtiger_Record_Model::getInstanceById($request->get("record"), 'ZoneAdmin');
			$oldZaZone = $zoneadminRecordModel->get('za_zone');
		}else{
			$oldZaZone = "";
		}
		$this->addZone($request->get("za_zone"),$oldZaZone);
        parent::process($request);
    }

    public function addZone($newValue,$oldZaZone)
    {
        $pickListNames = array('origin_zone','empty_zone', 'currentzone', 'intransitzone');

        foreach ($pickListNames as $pickListName) {
            $moduleModel = Settings_Picklist_Module_Model::getInstance('Trips');

            $picklistValues = Vtiger_Util_Helper::getPickListValues($pickListName);
			
            if (!in_array($newValue, $picklistValues)) {
                $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
                $rolesSelected = array();
                if ($fieldModel->isRoleBased()) {
                    $roleRecordList = Settings_Roles_Record_Model::getAll();
                    foreach ($roleRecordList as $roleRecord) {
                        $rolesSelected[] = $roleRecord->getId();
                    }
                }
                try {
					if($oldZaZone != ""){
						$db = PearDatabase::getInstance();
						$db->pquery("DELETE FROM vtiger_origin_zone WHERE origin_zone = ?",array($oldZaZone));
					}
                } catch (Exception $e) {
                }
            }
        }
    }
}
