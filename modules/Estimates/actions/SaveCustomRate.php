<?php
class Estimates_SaveCustomRate_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param pass an object of type Vtiger_Request to the parent class function process()
     */
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $recordId = $request->get('record');
        $rateType = $request->get('rateType');
        $value = $request->get('value');
        if ($rateType == 'SIT') {
            //save the SIT field
            $fieldName = 'apply_custom_sit_rate_override';
        } elseif ($rateType == 'Packing') {
            //save the Packing field
            $fieldName = 'apply_custom_pack_rate_override';
        }
        $sql = "SELECT ".$fieldName." FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$recordId]);
        $row = $result->fetchRow();
        
        if ($row == null) {
            //Row should only be null if a record has never been saved, since this is detail that shouldn't be possible
            return;
        }
        $sql = "UPDATE `vtiger_quotes` SET ".$fieldName."=? WHERE quoteid=?";
        
        $result = $db->pquery($sql, [$value, $recordId]);
        
        $response = new Vtiger_Response();
        $response->setResult('1');
        $response->emit();
    }
}
