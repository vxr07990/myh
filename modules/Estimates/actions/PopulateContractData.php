<?php
/**
  * This class handles the Estimates PopulateContractData action processing.
  */
class Estimates_PopulateContractData_Action extends Vtiger_BasicAjax_Action
{

    // inherit from parent constructor
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *     Gets the fields from a Contract as specified in the request to populate the Contract data into an Estimate
     *     @param Vtiger_Request request A Vtiger_Request Object from JS with a Contract ID for finding associated fields to send back
     *     @return void Uses Emit to send corresponding data back to JS, no actual return is used.
     */
    public function process(Vtiger_Request $request)
    {
        $db =& PearDatabase::getInstance();
        $info = array();
        $contractId = $request->get('contract_id');
        $currentOwner = $request->get('current_owner');
//		$sql = "SELECT account_id, contact_id, related_tariff, billing_address1, billing_address2, billing_city, billing_state,
//billing_zip, billing_pobox, billing_country, fixed_eff_date, effective_date, fixed_fuel, fuel_charge, fixed_irr,
//irr_charge, linehaul_disc, accessorial_disc, packing_disc, sit_disc, bottom_line_disc, min_val_per_lb,
//valuation_deductible, free_fvp_allowed, free_fvp_amount, rate_per_100, fuel_charge";
//		if (getenv('INSTANCE_NAME') == 'sirva') {
//			$sql .= ", parent_contract,billing_apn,nat_account_no, contract_no ";
//		} else if (getenv('INSTANCE_NAME') == 'graebel') {
//			//quick fix until the gvl version is merged over which should eliminate this nonsense.
//			$sql .= ", min_weight";
//		}
//		$sql .= " FROM `vtiger_contracts` WHERE contractsid = ? LIMIT 1";
//		$result = $db->pquery($sql, array($contractId));
//		$row = $result->fetchRow();

        //Blame says I made this a straight mysql select, but why? Did I copy it?  Why wouldn't I use:
        //$record = Vtiger_Record_Model::getInstanceById($request->get('source'));
        //These and more questions from future self to a currently unavailable past self.
        //Alright we're doing this right so we don't have to conditionalize.

        try {
            if ($contractRecord = Vtiger_Record_Model::getInstanceById($contractId, 'Contracts')) {
                $info['account'] = $contractRecord->get('account_id');
                $info['contact']       = $contractRecord->get('contact_id');
                $info['effective_tariff_id'] = $contractRecord->get('related_tariff');
                $info['parent_contract_id'] = $contractRecord->get('parent_contract');
                // for SIRVA, the sub-agreement can have a vanline owner,
                // in which case we need to find some kind of default agent owner to set
                // the estimate to use
                // if the current owner has access, don't change
                if(getenv('INSTANCE_NAME') == 'sirva') {
                    $owner = $contractRecord->get('agentid');
                    $res   = $db->pquery('SELECT 1 FROM vtiger_vanlinemanager WHERE vanlinemanagerid=?', [$owner]);
                    if ($db->num_rows($res) > 0) {
                        $agents = array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser($owner));
                        if (count($agents) > 0) {
                            if ($currentOwner && in_array($currentOwner, $agents)) {
                                $owner = $currentOwner;
                            } else {
                                $owner = $agents[0];
                            }
                        }
                    }
                    // but if it's not a vanline, we just use the agent
                    $info['owner'] = $owner;
                }

//        if (getenv('INSTANCE_NAME') == 'sirva') {
//			$info['parent_contract_id'] = $row['parent_contract'];
//			$parent_contract_result = $db->pquery($sql, array($info['parent_contract_id']));
//			$parent_contract_row = $parent_contract_result->fetchRow();
//			$info['parent_contract'] = $parent_contract_row[0];
//
//
//			if(!empty($row['nat_account_no'])){
//				$get_correct_national_account_num = $db->pquery('SELECT apn FROM vtiger_account
//                                                                  WHERE accountid = ?',[$row['nat_account_no']]);
//				if($get_correct_national_account_num){
//					$correct_national_account = $get_correct_national_account_num->fetchRow();
//					if(!empty($correct_national_account['apn'])){
//						$row['nat_account_no'] = $correct_national_account['apn'];
//					}
//				}
//			}
//			$info['nat_account_no'] = $row['nat_account_no'];
//			//not sure if this needs to be sirva only...
//			$info['contract_no'] = $row['contract_no'];
//			$info['billing_apn'] = $row['billing_apn'];
//		}
                //get entity labels to return.
                $info['account_label'] = $this->getEntityLabel($info['account']);
                $info['contact_label'] = $this->getEntityLabel($info['contact']);
                $info['effective_tariff'] = $this->getEntityLabel($info['effective_tariff_id']);
                $info['parent_contract']    = $this->getEntityLabel($info['parent_contract_id']);

                $info['nat_account_no'] = $contractRecord->getDisplayValue('nat_account_no');
                $info['contract_no']          = $contractRecord->get('contract_no');
                $info['extended_sit_mileage'] = $contractRecord->get('extended_sit_mileage');
                $info['local_tariff']         = $contractRecord->get('local_tariff');
                $info['move_type']         = $contractRecord->get('move_type');

                $info['address1'] = $contractRecord->get('billing_address1');
                $info['address2'] = $contractRecord->get('billing_address2');
                $info['city']     = $contractRecord->get('billing_city');
                $info['state']    = $contractRecord->get('billing_state');
                $info['zip']      = $contractRecord->get('billing_zip');
                $info['pobox']    = $contractRecord->get('billing_pobox');
                $info['country']  = $contractRecord->get('billing_country');

                $info['valuation_amount'] = $contractRecord->get('valuation_amount');
                $info['valuation_deductible_amount'] = $contractRecord->get('valuation_deductible_amount');
                $info['valuation_discounted'] = $contractRecord->get('valuation_discounted');
                $info['valuation_discount_amount'] = $contractRecord->get('valuation_discount_amount');
                $info['total_valuation'] = $contractRecord->get('total_valuation');
                $billingapn = $contractRecord->get('billing_apn');

                $info['billing_apn'] = '';
                if ($billingapn != '' && $billingapn != 0) {
                    $info['billing_apn'] = $billingapn;
                    //@TODO: This is not correct anymore.
//                    try {
//                        $billingAcctRecordModel = Vtiger_Record_Model::getInstanceById($billingapn, 'Accounts');
//                        if ($billingAcctRecordModel) {
//                            $info['billing_apn'] = $billingAcctRecordModel->get('apn');
//                        }
//                    } catch (Exception $ex) {
//                        //this try/catch probably should be removed since this field appears to be a text box
//                    }
                }

                $info['fuel_surcharge'] = $contractRecord->get('fuel_charge');
                $info['fuel_type'] = $contractRecord->get('fuel_surcharge_type');

                $info['fuel_discount']        = $contractRecord->get('fuel_disc');
                $info['linehaul_disc']        = $contractRecord->get('linehaul_disc');
                $info['accessorial_disc']     = $contractRecord->get('accessorial_disc');
                $info['packing_disc']         = $contractRecord->get('packing_disc');
                $info['sit_disc']             = $contractRecord->get('sit_disc');
                $info['bottom_line_disc']     = $contractRecord->get('bottom_line_disc');

                $info['min_weight']           = $contractRecord->get('min_weight');
                $info['min_val_per_lb']       = $contractRecord->get('min_val_per_lb');
                $info['valuation_deductible'] = $contractRecord->get('valuation_deductible');
                $info['free_fvp_allowed']     = $contractRecord->get('free_fvp_allowed');
                $info['free_fvp_amount']      = $contractRecord->get('free_fvp_amount');
                $info['business_line']        = $contractRecord->get('business_line');
                $info['waive_peak_rates']     = $contractRecord->get('waive_peak_rates');
                $info['additional_valuation'] = $contractRecord->get('additional_valuation');

                $info['valuation_amount'] = $contractRecord->get('valuation_amount');
                $info['valuation_deductible_amount'] = $contractRecord->get('valuation_deductible_amount');
                $info['valuation_discounted'] = $contractRecord->get('valuation_discounted');
                $info['valuation_discount_amount'] = $contractRecord->get('valuation_discount_amount');
                $info['total_valuation'] = $contractRecord->get('total_valuation');


                $info['bottom_line_distribution_discount'] = $contractRecord->get('bottom_line_distribution_discount');
                $info['sit_distribution_discount'] = $contractRecord->get('sit_distribution_discount');

                //Removed this, was causing an error, must be only present in sirva
                //Readded this because it shouldn't error now that we're using the record->get.
                $info['rate_per_100'] = $contractRecord->get('rate_per_100');

                if ($contractRecord->get('fixed_eff_date') == 1) {
                    $info['effective_date'] = $contractRecord->get('effective_date');
                }
                if ($contractRecord->get('fixed_irr') == 1) {
                    $info['irr'] = $contractRecord->get('irr_charge');
                }

                $info['misc_items'] = [];
                //get the misc items
                $sql    = "SELECT contracts_misc_id, is_quantity_rate, description, rate, quantity, discounted, discount FROM `vtiger_contracts_misc_items` WHERE contractsid = ?";
                $result = $db->pquery($sql, [$contractId]);
                while ($row =& $result->fetchRow()) {
                    $info['misc_items'][] = $row;
                }

                if (getenv('INSTANCE_NAME') != 'sirva' && !empty($contractId)) {
                    $info['flat_rate_auto'] = [];
                    $db           = PearDatabase::getInstance();
                    //Flat Rate Auto table
                    $sql    = "SELECT * FROM `vtiger_contract_flat_rate_auto` WHERE `contractid` =?";
                    $result    = $db->pquery($sql, [$contractId]);
                    while ($row = $result->fetchRow()) {
                        $flat_rate_auto = [
                            'discount' => $row['discount'],
                            'from_mileage' => $row['from_mileage'],
                            'to_mileage' => $row['to_mileage'],
                            'rate' => $row['rate'],
                        ];
                        $info['flat_rate_auto'][] = $flat_rate_auto;
                    }
                }
            } else {
                throw new Exception('Error unable to retrieve contract information.');
            }
        } catch (Exception $ex) {
            throw new Exception('Error retrieving contract information.');
        }
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }

    protected function getEntityLabel($crmid)
    {
        $label = '';
        if ($crmid) {
            $db = PearDatabase::getInstance();
            $sql = 'SELECT label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = ?';
            $result = $db->pquery($sql, [$crmid]);
            $row    = $result->fetchRow();
            if ($row) {
                $label = $row['label'];
            }
        }
        return $label;
    }

    protected function pullRelatedAccount($contractId)
    {
        $account_id = false;
        if ($contractId) {
            try {
                $db = PearDatabase::getInstance();
                $stmt = 'SELECT * FROM `vtiger_crmentityrel` WHERE `relcrmid` = ? AND `module` = ? LIMIT 1';
                $result = $db->pquery($stmt, [$contractId, 'Accounts']);
                if (method_exists($result, 'fetchRow')) {
                    $row = $result->fetchRow();
                    $account_id = $row['crmid'];
                }
            } catch (Exception $ex) {
                //only death is excepted.
            }
        }
        return $account_id;
    }
}
