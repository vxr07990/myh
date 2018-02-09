<?php

class Opportunities_SaveParticipants_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/devLog.log', "\n REQUEST: ".print_r($request, true), FILE_APPEND);
        $field = $request->get('field');
        $fieldValue = $request->get('fieldvalue');
        $radioPrev = $request->get('radioprev');
        $typePrev = $request->get('typeprev');
        $agentPrev = $request->get('agentprev');
        $record = $request->get('record');
        $id = $request->get('id');

        /*file_put_contents('logs/devLog.log', "\n record: ".$record, FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n field: ".$field, FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n fieldValue: ".$fieldValue, FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n radioPrev: ".$radioPrev, FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n typePrev: ".$typePrev, FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n agentPrev: ".$agentPrev, FILE_APPEND);*/
        //file_put_contents('logs/devLog.log', "\n id: ".$id, FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n record: ".$record, FILE_APPEND);
        if ($field == 'participantPermission') {
            $db = PearDatabase::getInstance();
            $sql = 'UPDATE `vtiger_potential_participatingagents` SET permissions = ? WHERE opportunityid = ? AND participantid = ?';
            $db->pquery($sql, array($fieldValue, $record, $id));
        } elseif ($field == 'agentType') {
            $db = PearDatabase::getInstance();
            $sql = 'UPDATE `vtiger_potential_participatingagents` SET agenttype = ? WHERE opportunityid = ? AND participantid = ?';
            $db->pquery($sql, array($fieldValue, $record, $id));
        }
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
}
