<?php

class Opportunities_CheckMoveType_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $db        = PearDatabase::getInstance();
            $contactId = $request->get('contact_id');
            if ($contactId) {
                $sql    = "SELECT move_type FROM `vtiger_potential` WHERE potentialid = ?";
                $result = $db->pquery($sql, [$contactId]);
                $row    = $result->fetchRow();
            }
            $response = new Vtiger_Response();
            $response->setResult($row);
            $response->emit();
        } else {
            $response = new Vtiger_Response();
            $response->setResult('');
            $response->emit();
        }
    }
}
