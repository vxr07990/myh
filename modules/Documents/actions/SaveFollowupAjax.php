<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_SaveFollowupAjax_Action extends Vtiger_SaveAjax_Action
{
    public function __construct()
    {
        $this->exposeMethod('toggleIncluded');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function toggleIncluded(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        if ($recordModel) {
            $recordModel->set('mode', 'edit');

            //@NOTE: Set this to the opposite of what it is, however true/false may not be correct so use 1/0 to be sure.
            $invoicePacketInclude = 1;
            if (\MoveCrm\InputUtils::CheckboxToBool($recordModel->get('invoice_packet_include'))) {
                $invoicePacketInclude = 0;
            }
            $recordModel->set('invoice_packet_include', $invoicePacketInclude);

            $recordModel->save();
            $result = ['valid' => true, 'invoice_packet_include' => \MoveCrm\InputUtils::CheckboxToBool($recordModel->get('invoice_packet_include'))];
        } else {
            $result = ['valid'=>false];
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
