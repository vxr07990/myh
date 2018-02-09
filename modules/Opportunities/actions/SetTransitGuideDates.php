<?php
/**
 * @author 			Ryan Paulson, Hacked by Louis Robinson
 * @file 			GetDetailedRate.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @copyright		IGC Software
 */
/*
VanlineID - The Vanline ID to do the lookup for.  SIRVA has a VanlineID of 18.  For all other instances, you can pass the VanlineID saved in the Vanline Manager record for the Vanline on the Agent Manager record that owns the Estimate

LoadDate - Currently selected Load Date.  Cannot be null, and interface should enforce this be populated first before they can perform a lookup.
OriginZip / DestinationZip - The Origin/Destination zip codes.
OriginExtraStopZips / DestinationExtraStopZips - Array of Extra stop zip codes for Origin/Destination.  Position in array should match it's Sequence number.  Both values can be nil if no Extra Stops are present.

Weight - Weight populated by the user.  If less than the minimum is provided, it will be enforced by the lookup.
ExpressLoading - A true/false field (can be nil) that should be set to true when the Tariff selected is "Allied Express" or "Blue Express".
OriginExpressTruckload - A true/false field (can be nil) that would be set to the value populated from "Express Truckload" Option under Accessorials for tariffs "Allied Express" and "Blue Express".
CrossBorder - A true/false field (can be nil) that should be set to true if the Origin/Destination Zip Codes are "cross border" (one in Canada, one in US).
PricingMode - Possible Options:
INTERSTATE - Default Pricing Mode to use
SIRVA2A12A - Use this for the 2A or 12A tariff options
INTRA400N - Use this for any of the Intrastate tariff options
Canada options: CNCIV, CNCOR, CNGOV - Not applicable just yet, since Canada tariffs not currently supported in QIO 2.0.
*/
require_once('libraries/nusoap/nusoap.php');
class Opportunities_SetTransitGuideDates_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $opportunityID = $request->get('record');
        $deliver_date = $request->get('deliver_date');
        $deliver_to_date = $request->get('deliver_to_date');
        $load_date = $request->get('load_date');
        $load_to_date = $request->get('load_to_date');
        
        $info = [];

        if ($opportunityID && $deliver_date && $deliver_to_date && $load_date && $load_to_date) {
            try {
                $opportunityRecordModel = Vtiger_Record_Model::getInstanceById($opportunityID, 'Opportunities');
                $opportunityRecordModel->set('load_date', $load_date);
                $opportunityRecordModel->set('deliver_date', $deliver_date);
                $opportunityRecordModel->set('deliver_to_date', $deliver_to_date);
                $opportunityRecordModel->set('load_to_date', $load_to_date);
                //This is required for saving this as an update.  otherwise it creates a new record.
                $opportunityRecordModel->set('mode', 'edit');
                $opportunityRecordModel->save();
                /*
                $db = PearDatabase::getInstance();
                $stmt = 'UPDATE `vtiger_potentialscf` SET '
                        .'`deliver_date` = ?,'
                        .'`deliver_to_date` = ?,'
                        .'`load_date` = ?,'
                        .'`load_to_date` = ?'
                        .' WHERE `potentialid` = ?';
                $db->pquery($stmt,
                            [
                                $deliver_date,
                                $deliver_to_date,
                                $load_date,
                                $load_to_date,
                                $opportunityRecordModel->getId()
                            ]);
                */
                $info['deliver_date'] = DateTimeField::convertToUserFormat($deliver_date);
                $info['deliver_to_date'] = DateTimeField::convertToUserFormat($deliver_to_date);
                $info['load_date'] = DateTimeField::convertToUserFormat($load_date);
                $info['load_to_date'] = DateTimeField::convertToUserFormat($load_to_date);
                $info['success'] = true;
            } catch (WebServiceException $ex) {
                throw new Exception($ex->getMessage());
            }
        } else {
            throw new Exception('Missing input param(s).');
        }

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
