<?php

class TariffSections_GetSectionInformation_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request) {
        try {
            $record = $request->get('record');

            $response = new Vtiger_Response();
            $response->setResult($this->returnData($record));
            $response->emit();
        } catch (Exception $e) {
            $response     = new Vtiger_Response();
            $response->setError($e->getMessage(), 'Please contact support for assistance.');
            $response->emit();
        }
    }

    /**
     * @param $record
     *
     * @return array
     * @throws Exception
     */
    private function returnData($record) {
        if (!$record) {
            throw new Exception('Record ID not passed');
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($record);

        if (!$recordModel) {
            throw new Exception('Record not found');
        }

        if ($recordModel->getModuleName() != 'TariffSections') {
            throw new Exception('Record not found');
        }

        return $recordModel->getData();
    }
}
