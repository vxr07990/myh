<?php
/**
  * This class handles the Estimates PopulateLocalCarrier action processing.
  */
class LeadSourceManager_PopulateLeadSource_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        //@TODO: should probably add record security checks here too...
        try {
            $record = Vtiger_Record_Model::getInstanceById($request->get('id'), 'LeadSourceManager');
            //OK ensure the right record type, otherwise don't return anything useful.
            if ($record->getModuleName() == 'LeadSourceManager') {
                // Emit data back to jQuery/JavaScript
                $data = ['success'=>true,
                         'data'=>array(
                             'brand'=>$record->get('brand'),'source_name'=>$record->get('source_name'),
                             'marketing_channel'=>$record->get('marketing_channel'),
                             'program_terms'=>$record->get('program_terms')
                         )
                        ];
                $response->setResult($data);
            } else {
                $record = ['success'=>false,'msg'=>'Could not find the record'];
                $response->setResult($record);
            }
        } catch (Exception $e) {
            $record = ['success'=>false,'msg'=>'Could not find the record'];
            $response->setResult($record);
        }
        $response->emit();
    }
}
