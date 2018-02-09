<?php
/*******************************************************************************
 * @file			PopulateAccountData.php
 * @author			Louis Robinson
 * @company			IGC Software
 * @contact			lrobinson@igcsoftware.com
 * @description		1. Recieves a Vtiger_Request object and builds a query for an
 *						account id.
 *					2. Takes the Vtiger_Request object again for the account
 *					   id and queries for matching customer data for the object
 *					3. Returns the customer and shipping data to the browser via
 *  					jQuery and appends it to the DOM
 *               Is this accurate? Doesn't it return a json which is
 * 			 	processed by jQuery which in turn updates the DOM
 *
 * @NOTE adapted from populateOppData.php in Estimates.
 *******************************************************************************/
class Orders_GetAccountDetail_Action extends Vtiger_BasicAjax_Action
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
        $accountid = $request->get('accountid');
        $result = [];
        if(!empty($accountid)){
            $accountRecord = Vtiger_Record_Model::getInstanceById($accountid);
            $result['national_account_number'] = $accountRecord->get('national_account_number');
            $result['account_address1'] = $accountRecord->get('address1');
            $result['account_address2'] = $accountRecord->get('address2');
            $result['account_city'] = $accountRecord->get('city');
            $result['account_state'] = $accountRecord->get('state');
            $result['account_zip_code'] = $accountRecord->get('zip');
            $result['account_country'] = $accountRecord->get('country');

            $listViewModel     = Vtiger_RelationListView_Model::getInstance($accountRecord,'Contracts');
            if($listViewModel->getRelatedEntriesCount() == 1){
                $pagingModel = new Vtiger_Paging_Model();
                $pagingModel->set('page', 1);
                $listRecord = $listViewModel ->getEntries($pagingModel);
                $contractRecord = array_values($listRecord)[0];
                $result['account_contract']=$contractRecord->getId();
                $result['account_contract_display']=$contractRecord->getDisplayName();
            }
        }
        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
