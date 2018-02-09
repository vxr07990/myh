<?php
/**
  * This class handles the Opportunities PopulateContactData action processing.
  */
class Opportunities_PopulateContactData_Action extends Vtiger_BasicAjax_Action
{

    // inherit from parent constructor
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *     Gets the fields from a Contact as specified in the request to populate the Contact data into an Estimate
     *     @param Vtiger_Request request A Vtiger_Request Object from JS with a Contact ID for finding associated fields to send back
     *     @return void Uses Emit to send corresponding data back to JS, no actual return is used.
     */
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $info = array();
        $contactId = $request->get('contact_id');
        if (!$request->get('get_type')) {
            $sql = "SELECT
			`vtiger_contactaddress`.mailingstreet,
			`vtiger_contactaddress`.otherstreet,
			`vtiger_contactaddress`.mailingcity,
			`vtiger_contactaddress`.mailingstate,
			`vtiger_contactaddress`.mailingzip,
			`vtiger_contactaddress`.mailingcountry,
			`vtiger_contactdetails`.firstname,
			`vtiger_contactdetails`.lastname,
			`vtiger_contactdetails`.accountid,
			`vtiger_contactdetails`.fax,
			`vtiger_contactdetails`.phone,
			`vtiger_contactdetails`.mobile,
			`vtiger_contactdetails`.contact_type,
			`vtiger_contactdetails`.email,
			`vtiger_contactsubdetails`.homephone,
			`vtiger_contactsubdetails`.otherphone
			";

            if (getenv('INSTANCE_NAME') == 'sirva') {
                $sql .= " ,`vtiger_contactdetails`.primary_phone_type ";
            }

            $sql .="
			FROM `vtiger_contactdetails` JOIN `vtiger_contactaddress` ON `vtiger_contactdetails`.contactid = `vtiger_contactaddress`.contactaddressid JOIN `vtiger_contactsubdetails` ON `vtiger_contactdetails`.contactid = `vtiger_contactsubdetails`.contactsubscriptionid
			WHERE `vtiger_contactdetails`.contactid = ?";
        } elseif ($request->get('get_type') == 'true') {
            //file_put_contents('logs/devLog.log', "\n GET_TYPE? ".$request->get('get_type'), FILE_APPEND);
            $sql = "SELECT contact_type FROM `vtiger_contactdetails` WHERE contactid = ?";
        }
        $result = $db->pquery($sql, array($contactId));
        $row = $result->fetchRow();

        //file_put_contents('logs/devLog.log', "\n GET_TYPE ROW: ".print_r($row, true), FILE_APPEND);

        $response = new Vtiger_Response();
        $response->setResult($row);
        $response->emit();
    }
}
