<?php
class Contracts_PopulateAnnualRates_Action extends Vtiger_BasicAjax_Action
{

    // inherit from parent constructor
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Setup the main function in which we process HTTP Requests
     * and emit data back to JavaScript to be processed on the DOM
     */
    public function process(Vtiger_Request $request)
    {
        
        // Instantiate pear db object
        $db = PearDatabase::getInstance();
        
        $accountId = $request->get('account_id');
        
        $annualRates = array();
        $sql = 'SELECT annualrateid, date, rate FROM `vtiger_annual_rate` WHERE accountid = ?';
        $result = $db->pquery($sql, array($accountId));
        $row = $result->fetchRow();
        
        while ($row != null) {
            $currentRate = array(
                'annualrateid' => $row['annualrateid'],
                'date' => $row['date'],
                'rate' => $row['rate'],
            );
            $annualRates[] = $currentRate;
            $row = $result->fetchRow();
        }
        
        //file_put_contents('logs/devLog.log', "\n ANNUAL_RATES: ".print_r($annualRates, true), FILE_APPEND);

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($annualRates);
        $response->emit();
    }
}
