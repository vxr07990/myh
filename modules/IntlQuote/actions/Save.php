<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class IntlQuote_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        try {
            $record_id = $request->get('record');
            $request->set('potential_id', $record_id);
            $db = PearDatabase::getInstance();
            $sql = "SELECT * FROM `vtiger_intlquote` WHERE potential_id = ?";
            $result = $db->pquery($sql, [$record_id]);

            $moduleModel = Vtiger_Module_Model::getInstance('IntlQuote');
            $fieldModelList = $moduleModel->getFields();

            $private_transferee = $request->get('private_transferee');
            $first_rate = $request->get('first_rate_request');
            $re_quote = $request->get('re_quote');
            
            if ($private_transferee!='on') {
                $request->set('private_transferee', 0);
            }
            if ($first_rate!='on') {
                $request->set('first_rate_request', 0);
            }
            if ($re_quote!='on') {
                $request->set('re_quote', 0);
            }
            $row = $result->fetchRow();
            if ($row) {
                $request->set('record', $row['intlquoteid']);
            } else {
                $request->set('record', '');
            }
            $recordId = $request->get('record');

            parent::process($request);

            $response = new Vtiger_Response();
            $response->setResult('Done');
            $response->emit();
        } catch (Exception $e) {
            $response = new Vtiger_Response();
            $response->setResult('failed');
            $response->setError('404', $e->getMessage());
            $response->emit();
        }
    }

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
}
