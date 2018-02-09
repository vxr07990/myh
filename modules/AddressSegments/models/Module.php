<?php

class AddressSegments_Module_Model extends Vtiger_Module_Model
{
    public function getAddressSegments($recordId = false)
    {
        $AddressSegmentsRows = array();
        $db              = PearDatabase::getInstance();
        $sql             = 'SELECT * FROM `vtiger_addresssegments` WHERE addresssegments_relcrmid=?';
        $result          = $db->pquery($sql, [$recordId]);

        if ($db->num_rows($result)>0) {
            while ($row=$db->fetchByAssoc($result)) {
                $AddressSegmentsRows[] = $row;
            }
        }

        return $AddressSegmentsRows;
    }

    public function saveAddressSegments($request, $relId)
    {
        //Fix this for sync
        if(array_key_exists('element', $request)){
            $request = json_decode($request['element'], true);
        }
        for ($index = 1; $index <= $request['numAgents']; $index++) {
            if (!$request['addresssegmentId_'.$index]) {
                continue;
            }
            $deleted = $request['addresssegmentDelete_'.$index];
            $participantId = $request['addresssegmentId_'.$index];
            if ($deleted == 'deleted') {
                $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                $recordModel->delete();
            } else {
                if ($participantId == 'none') {
                    $recordModel=Vtiger_Record_Model::getCleanInstance("AddressSegments");
                    $recordModel->set('mode', '');
                } else {
                    $recordModel=Vtiger_Record_Model::getInstanceById($participantId);
                    $recordModel->set('id', $participantId);
                    $recordModel->set('mode', 'edit');
                }
                $recordModel->set('addresssegments_sequence', $request['addresssegments_sequence_'.$index]);
                $recordModel->set('addresssegments_origin', $request['addresssegments_origin_'.$index]);
                $recordModel->set('addresssegments_destination', $request['addresssegments_destination_'.$index]);
                $recordModel->set('addresssegments_transportation', $request['addresssegments_transportation_'.$index]);
                $recordModel->set('addresssegments_cube', $request['addresssegments_cube_'.$index]);
                $recordModel->set('addresssegments_weight', $request['addresssegments_weight_'.$index]);
                $recordModel->set('addresssegments_weightoverride', $request['addresssegments_weightoverride_'.$index]);
                $recordModel->set('addresssegments_cubeoverride', $request['addresssegments_cubeoverride_'.$index]);
                $recordModel->set('addresssegments_relcrmid', $relId);
                $recordModel->save();
            }
        }
    }

    public function getCubeSheetData($cubesheetsid)
    {
        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        return $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => $cubesheetsid]);
    }

    public function getSurveyedItems($cubesheetId)
    {
        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        return $soapProxy->getSurveyedItems(['CubeSheetId' => $cubesheetId, 'CubeSheetIdSpecified' => true]);
    }
}
