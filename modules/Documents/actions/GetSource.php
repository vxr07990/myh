<?php
class Documents_GetSource_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT path, vtiger_attachments.attachmentsid, name FROM vtiger_attachments JOIN vtiger_seattachmentsrel ON vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid WHERE crmid=?";
        $params[] = $request->get('record');
        
        $result = $db->pquery($sql, $params);
        
        $row = $result->fetchRow();
        
        if ($row == null) {
            return;
        }
        
        $info['source'] = $row[0].$row[1]."_".$row[2];
        
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
