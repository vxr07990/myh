<?php
class Surveys_PopulateOppData_Action extends Vtiger_BasicAjax_Action
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
        $sourceRecord = $request->get('record');
        $sourceModule = $request->get('source_module');
        
        

        // Initial query for contact details
        //@NOTE: Conditionalized to preserve old functionality, unsure why an action named PopulateOppData is grabbing account info though.
        if(getenv('INSTANCE_NAME') == 'sirva' || getenv('INSTANCE_NAME') == 'graebel') {
            $sql = "SELECT vtiger_potential.*, vtiger_potentialscf.* FROM vtiger_potential
                    INNER JOIN vtiger_potentialscf
                        ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
                    WHERE vtiger_potential.potentialid = ?";
            // Initial request var from the the DOM upon which query is established
            $params[] = $sourceRecord;


            // pair up $params and the SQL statement in the pquery object call
            $result = $db->pquery($sql, $params);

            // $opportunity
            unset($params);
            $row = $result->fetchRow();
        }elseif(getenv('INSTANCE_NAME') == 'movehq'){
            $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
            $isActiveAddressList = false;
            if ($addressListModule && $addressListModule->isActive()) {
                $isActiveAddressList = true;
            }

            if($isActiveAddressList){
                $data = $addressListModule->getAddressListItem($sourceRecord,$sourceModule);
            }else{
                $recordModel = Vtiger_Record_Model::getInstanceById($sourceRecord);
            }
        }else{
            $sql = "SELECT * FROM vtiger_account
                  INNER JOIN vtiger_accountbillads
                  ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
                  WHERE vtiger_account.accountid = ?";
            // Initial request var from the the DOM upon which query is established
            $params[] = $sourceRecord;


            // pair up $params and the SQL statement in the pquery object call
            $result = $db->pquery($sql, $params);

            // $opportunity
            unset($params);

            $row = $result->fetchRow();

        }
            $info = array();



        // Origin info
        //@NOTE: Same conditionalization note as above.
        if(getenv('INSTANCE_NAME') == 'sirva' || getenv('INSTANCE_NAME') == 'graebel') {
            $info['address']['bill_city'] = $row['origin_city'];
            $info['address']['bill_zip'] = $row['origin_zip'];
            $info['address']['bill_country'] = $row['origin_country'];
            $info['address']['bill_state'] = $row['origin_state'];
            $info['address']['bill_street'] = $row['origin_address1'];
            $info['address']['bill_street2'] = $row['origin_address2'];
            $info['address']['bill_pobox'] = $row['pobox'];
            $info['address']['phone'] = $row['origin_phone1'];
            $info['address']['phone2'] = $row['origin_phone2'];
            $info['address']['address_desc'] = $row['origin_description'];
        }elseif(getenv('INSTANCE_NAME') == 'movehq'){
            if($isActiveAddressList){
                foreach ($data as $values){
                    if ($values['address_type'] == 'Origin'){
                        $info['address']['bill_city'] = html_entity_decode($values['city']);
                        $info['address']['bill_zip'] = html_entity_decode($values['zip_code']);
                        $info['address']['bill_country'] = html_entity_decode($values['country']);
                        $info['address']['bill_state'] = html_entity_decode($values['state']);
                        $info['address']['bill_street'] = html_entity_decode($values['address1']);
                        $info['address']['bill_street2'] = html_entity_decode($values['address2']);
                        $info['address']['phone'] = html_entity_decode($values['address_phone']);
                        $info['address']['address_desc'] = html_entity_decode($values['notes']);
                        break;
                    }
                }
            }else{
                if($sourceModule == 'Orders' || $sourceModule == 'Opportunities'){
                    $info['address']['bill_city'] = html_entity_decode($recordModel->get('origin_city'));
                    $info['address']['bill_zip'] = html_entity_decode($recordModel->get('origin_zip'));
                    $info['address']['bill_country'] = html_entity_decode($recordModel->get('origin_country'));
                    $info['address']['bill_state'] = html_entity_decode($recordModel->get('origin_state'));
                    $info['address']['bill_street'] = html_entity_decode($recordModel->get('origin_address1'));
                    $info['address']['bill_street2'] = html_entity_decode($recordModel->get('origin_address2'));
                    $info['address']['phone'] = html_entity_decode($recordModel->get('origin_phone1'));
                    $info['address']['phone2'] = html_entity_decode($recordModel->get('origin_phone2'));
                    $info['address']['address_desc'] = html_entity_decode($recordModel->get('origin_description'));
                }
            }
        }else{
            $info['address']['bill_city'] = $row['bill_city'];
            $info['address']['bill_zip'] = $row['bill_code'];
            $info['address']['bill_country'] = $row['bill_country'];
            $info['address']['bill_state'] = $row['bill_state'];
            $info['address']['bill_street'] = $row['bill_street'];
            $info['address']['bill_pobox'] = $row['bill_pobox'];
            $info['address']['phone'] = $row['phone'];
        }

        $flag = 0;

        // Emit data back to jQuery/JavaScript
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
