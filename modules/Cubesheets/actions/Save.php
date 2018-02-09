<?php

use Carbon\Carbon;

class Cubesheets_Save_Action extends Inventory_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $cubesheetid = $request->get('record');

        if ($request->get('is_primary') == 'on') {
            $isPrimary = true;
            $potentialid = $request->get('potential_id');
            $db = PearDatabase::getInstance();
            $sql = "UPDATE `vtiger_cubesheets` SET `is_primary`=0 WHERE `potential_id`=?";
            $params[] = $potentialid;

            $result = $db->pquery($sql, $params);
            unset($params);
        }

        parent::process($request);
        if(getenv('IGC_MOVEHQ')) {
            $recordId          = $request->get('record');
            $currentModuleName = $request->get('module');
            $recordModel       = Vtiger_Record_Model::getInstanceById($recordId, $currentModuleName);
            $loadUrl           = $recordModel->getDetailViewUrl();
            $loadUrl           .= "&sourceModule=".$request->get('sourceModule');
            header("Location: $loadUrl");
        }

//        if ($isPrimary) {
//            //Update parent Opportunity record's Survey Date and Survey Time to reflect createdtime of Cubesheet
//            $sql = "SELECT createdtime FROM `vtiger_crmentity` WHERE crmid=?";
//            $result = $db->pquery($sql, [$request->get('record')]);
//
//            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $result->fields['createdtime']);
//
//            $sql = "UPDATE `vtiger_potentialscf` SET survey_date=?, survey_time=? WHERE potentialid=?";
//            $db->pquery($sql, [$carbon->toDateString(), $carbon->toTimeString(), $potentialid]);
//        }
    }
}
