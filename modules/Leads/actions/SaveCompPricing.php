<?php
class Leads_SaveCompPricing_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        //index.php?module=Leads&action=SaveCompPricing&record=22&type=comp_allied&value=0&prev=0
        //file_put_contents('logs/devLog.log', "\n REQUEST: ".print_r($request, true), FILE_APPEND);
        $record = $request->get('record');
        $type = $request->get('type');
        $value = $request->get('value');
        //file_put_contents('logs/devLog.log', "\n record: ".$record, FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n type: ".$type, FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n value: ".$value, FILE_APPEND);
        $db = PearDatabase::getInstance();
        switch ($type) {
            case 'comp_allied':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET allied = ? WHERE leadid = ?';
                break;
            case 'comp_atlas':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET atlas = ? WHERE leadid = ?';
                break;
            case 'comp_mayflower':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET mayflower = ? WHERE leadid = ?';
                break;
            case 'comp_northamerican':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET north_american = ? WHERE leadid = ?';
                break;
            case 'comp_united':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET united = ? WHERE leadid = ?';
                break;
            case 'comp_independent':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET independent = ? WHERE leadid = ?';
                break;
            case 'comp_other':
                $sql = 'UPDATE `vtiger_sirva_pricing_comp` SET other = ? WHERE leadid = ?';
                break;
        }
        $db->pquery($sql, [$value, $record]);
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
}
