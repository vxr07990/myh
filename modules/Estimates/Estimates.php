<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
include_once('vendor/nesbot/carbon/src/Carbon/Carbon.php');
use Carbon\Carbon;

class Estimates extends CRMEntity
{
    public $log;
    public $db;
    public $table_name     = "vtiger_quotes";
    public $table_index    = 'quoteid';
    public $tab_name       = ['vtiger_crmentity', 'vtiger_quotes', 'vtiger_quotesbillads', 'vtiger_quotesshipads', 'vtiger_quotescf', 'vtiger_inventoryproductrel'];
    public $tab_name_index = ['vtiger_crmentity'           => 'crmid',
                           'vtiger_quotes'              => 'quoteid',
                           'vtiger_quotesbillads'       => 'quotebilladdressid',
                           'vtiger_quotesshipads'       => 'quoteshipaddressid',
                           'vtiger_quotescf'            => 'quoteid',
                           'vtiger_inventoryproductrel' => 'id'];
    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = ['vtiger_quotescf', 'quoteid'];
    public $entity_table     = "vtiger_crmentity";
    public $billadr_table    = "vtiger_quotesbillads";
    public $object_name      = "Quote";
    public $new_schema       = true;
    public $column_fields    = [];
    public $sortby_fields    = ['subject', 'crmid', 'smownerid', 'accountname', 'lastname'];
    // This is used to retrieve related vtiger_fields from form posts.
    public $additional_column_fields = ['assigned_user_name',
                                     'smownerid',
                                     'opportunity_id',
                                     'case_id',
                                     'contact_id',
                                     'task_id',
                                     'note_id',
                                     'meeting_id',
                                     'call_id',
                                     'email_id',
                                     'parent_name',
                                     'member_id'];
    // This is the list of vtiger_fields that are in the lists.
    public $list_fields        = [
        //'Quote No'=>Array('crmentity'=>'crmid'),
        // Module Sequence Numbering
        'Quote No'       => ['quotes' => 'quote_no'],
        // END
        'Subject'        => ['quotes' => 'subject'],
        'Quote Stage'    => ['quotes' => 'quotestage'],
        'Potential Name' => ['quotes' => 'potentialid'],
        'Account Name'   => ['account' => 'accountid'],
        'Total'          => ['quotes' => 'total'],
        'Assigned To'    => ['crmentity' => 'smownerid'],
    ];
    public $list_fields_name   = [
        'Quote No'       => 'quote_no',
        'Subject'        => 'subject',
        'Quote Stage'    => 'quotestage',
        'Potential Name' => 'potential_id',
        'Account Name'   => 'account_id',
        'Total'          => 'hdnGrandTotal',
        'Assigned To'    => 'assigned_user_id',
    ];
    public $list_link_field    = 'subject';
    public $search_fields      = [
        'Quote No'     => ['quotes' => 'quote_no'],
        'Subject'      => ['quotes' => 'subject'],
        'Account Name' => ['quotes' => 'accountid'],
        'Quote Stage'  => ['quotes' => 'quotestage'],
    ];
    public $search_fields_name = [
        'Quote No'     => 'quote_no',
        'Subject'      => 'subject',
        'Account Name' => 'account_id',
        'Quote Stage'  => 'quotestage',
    ];
    // This is the list of vtiger_fields that are required.
    public $required_fields = ["accountname" => 1];
    //Added these variables which are used as default order by and sortorder in ListView
    public $default_order_by   = 'crmid';
    public $default_sort_order = 'ASC';
    //var $groupTable = Array('vtiger_quotegrouprelation','quoteid');
    public $mandatory_fields = ['subject', 'createdtime', 'modifiedtime', 'assigned_user_id'];
    // For Alphabetical search
    public $def_basicsearch_col = 'subject';
    // For workflows update field tasks is deleted all the lineitems.
    public $isLineItemUpdate = true;

    /**    Constructor which will set the column_fields in this object
     */
    public function Estimates()
    {
        $this->log           = LoggerManager::getLogger('quote');
        $this->db            = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('Estimates');
    }

    public function save_module()
    {
        global $adb;
        $tempTables = $_REQUEST['pseudoSave'] == '1';
        //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."\$tempTables = ".$_REQUEST['pseudoSave']."\n", FILE_APPEND);
        //in ajax save we should not call this function, because this will delete all the existing product values
        if ($_REQUEST['action'] != 'QuotesAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
            && $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'
            && $_REQUEST['action'] != 'SaveAjax' && $this->isLineItemUpdate != false && $_REQUEST['module'] == 'Estimates'
        ) {
            //Based on the total Number of rows we will save the product relationship with this entity
            saveInventoryProductDetails($this, 'Quotes', 'false', '', $tempTables);
        }
        // Update the currency id and the conversion rate for the quotes
        $update_query  = "UPDATE vtiger_quotes SET currency_id=?, conversion_rate=? WHERE quoteid=?";
        $update_params = [$this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id];
        $adb->pquery($update_query, $update_params);
        //Address List save
        $addressListModule= Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->saveAddressList($_REQUEST, $this->id);
        }
    }

    public function saveentity($module, $fileid = '') {
        // if ($_REQUEST['repeat'] === true) {
        //    return;
        //}

        $db =& PearDatabase::getInstance();
        // If the estimate's opportunity is booked and registered, we cannot set a new primary estimate.
        if(getenv('INSTANCE_NAME') == 'sirva') {
            if(isset($_REQUEST['element'])) {
                $elementObject = json_decode($_REQUEST['element']);
                $idArray = explode('x', $elementObject->potential_id);
                $potentialId = $idArray[sizeof($idArray)-1];
            }else {
                $potentialId = $_REQUEST['potential_id'];
            }
            $sql = "SELECT is_primary FROM vtiger_quotes WHERE quoteid=? LIMIT 1";
            $res = $db->pquery($sql, [$this->id]);
            // Only care about this logic when a non-primary estimate is trying to get saved.
            if(!$res->fields['is_primary']) {
                $sql = "SELECT sales_stage, register_sts FROM vtiger_potential WHERE potentialid=?";
                $res = $db->pquery($sql, [$potentialId]);
                if($res) {
                    $row = $res->fetchRow();
                    if($row['sales_stage'] == 'Closed Won' && $row['register_sts'] == 1) {
                        $_REQUEST['is_primary'] = $this->column_fields['is_primary'] = false;
                    }
                }
            }
        }

        if ($_REQUEST['duplicate'] && $_REQUEST['is_primary'] && $_REQUEST['quotestage'] == 'Accepted' && $_REQUEST['estimate_type'] == 'Addendum') {
            $this->setPreviousPrimarySettings($_REQUEST);
            unset($_REQUEST['duplicate']);
            //This appears to be done in the isPrimary if... sadly NOT in the primaryEstimateLogic function.
            // Either way shouldn't be done here.
//            //Add the estimated linehaul to the associated order. when the estimate is primary and accepted.
//            //@TODO: this is for registration of an order pushed to WK44.
//            if ($_REQUEST['orders_id'] && $_REQUEST['linehaul']) {
//                try {
//                    //@TODO: linehaul is currently a fiction, update after conrado code goes in to pull this lineitem if it's not a request value.
//                    //pull associated order:
//                    if ($orderRecordModel = Vtiger_Record_Model::getInstanceById($_REQUEST['orders_id'], 'Orders')) {
//                        //set the linehaul
//                        $orderRecordModel->set('orders_elinehaul', $_REQUEST['linehaul']);
//                        //save the record.
//                        $orderRecordModel->save();
//                    }
//                } catch (Exception $ex) {
//                    //meh
//                }
//            }
        }
        //unset($this->column_fields['pack_rates']);
        //does things twice, this stops it.
        // $_REQUEST['repeat'] = true;

        //@NOTE: When being called from CreateEstimate the record key is being set to the wrong ID, I'm surprised this hasn't broken more stuff.
        if($_REQUEST['module'] == 'Cubesheets' && $_REQUEST['action'] == 'CreateEstimate') {
            unset($_REQUEST['record']);
            unset($this->column_fields['record']);
        }
        $fieldList = array_merge($_REQUEST, $this->column_fields);

        if(!$this->checkIfParentHasPrimary($fieldList['module'], $fieldList['potential_id'], $fieldList['orders_id'])) {
            //Parent has no primary
            $fieldList['is_primary'] = 1;
        }

        if(isset($this->column_fields['pack_rates']) && isset($_REQUEST['pack_rates'])){
            $this->column_fields['pack_rates'] = base64_encode(json_encode($this->column_fields['pack_rates']));
        }
        $pseudo    = $fieldList['pseudoSave'] == '1';

        if(getenv('INSTANCE_NAME') == 'graebel')
        {
            if(!$pseudo && $fieldList['module'] == 'Actuals') {
                if (!$fieldList['effective_tariff'] && !$fieldList['local_tariff']) {
                    throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, "Effective tariff must be set");
                }
                if(!$fieldList['_converting_to_actual'] && !$fieldList['load_date'])
                {
                    throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, "Load date must be set");
                }
            }
        }
        //OT 15866 making number of days in SIT block key off of start and end dates.
        if(getenv('INSTANCE_NAME') != 'sirva') {
            if ($fieldList['sit_origin_date_in'] && $fieldList['sit_origin_pickup_date']) {
                $_REQUEST['sit_origin_number_days'] =
                $fieldList['sit_origin_number_days'] = $this->column_fields['sit_origin_number_days'] = $this->getSITNumberOfDays($fieldList['sit_origin_date_in'], $fieldList['sit_origin_pickup_date']);
            } elseif (getenv('INSTANCE_NAME') == 'graebel') {
                $_REQUEST['sit_origin_number_days'] = $fieldList['sit_origin_number_days'] = $this->column_fields['sit_origin_number_days'] = 0;
            }
            if ($fieldList['sit_dest_date_in'] && $fieldList['sit_dest_delivery_date']) {
                $_REQUEST['sit_dest_number_days'] =
                $fieldList['sit_dest_number_days'] = $this->column_fields['sit_dest_number_days'] = $this->getSITNumberOfDays($fieldList['sit_dest_date_in'], $fieldList['sit_dest_delivery_date']);
            } elseif (getenv('INSTANCE_NAME') == 'graebel') {
                $_REQUEST['sit_dest_number_days'] = $fieldList['sit_dest_number_days'] = $this->column_fields['sit_dest_number_days'] = 0;
            }
        }

        $compiledServiceCharges = is_array($fieldList['compiledServiceCharges']) ? $fieldList['compiledServiceCharges'] : json_decode($fieldList['compiledServiceCharges'], true);
        unset($fieldList['compiledServiceCharges']);
        $pseudo = $fieldList['pseudoSave'] == '1';

        $fieldTariff = $fieldList['effective_tariff'] ?: $fieldList['local_tariff'];

        if(($fieldList['syncwebservice']) && !$fieldTariff)
        {
            $data = Estimates_Record_Model::getAllowedTariffsForUser($fieldList['agentid']);
            if ($fieldList['business_line_est'] == "Interstate Move") {
                foreach ($data as $id => $info) {
                    if ($info['is_managed_tariff']) {
                        $fieldTariff = $fieldList['effective_tariff'] = $this->column_fields['effective_tariff'] = $_REQUEST['effective_tariff'] = $id;
                        break;
                    }
                }
            } else {
                foreach ($data as $id => $info) {
                    if (!$info['is_managed_tariff']) {
                        $fieldTariff = $fieldList['effective_tariff'] = $this->column_fields['effective_tariff'] = $_REQUEST['effective_tariff'] = $id;
                        break;
                    }
                }
            }
        }
        if($fieldList['syncwebservice'])
        {
            $fieldList['local_weight'] = $this->column_fields['local_weight'] = $_REQUEST['local_weight'] = $fieldList['weight'];
        }

        //@TODO: see how not to hack this.
        if (
            !$fieldList['hdnGrandTotal'] ||
            $fieldList['hdnGrandTotal'] == 0
        ) {
            $fieldList['hdnGrandTotal'] = $this->column_fields['hdnGrandTotal'] = $_REQUEST['hdnGrandTotal'] = $fieldList['total'];
        }

        if (
            !$fieldList['hdnSubTotal'] ||
            $fieldList['hdnSubTotal'] == 0
        ) {
            $fieldList['hdnSubTotal'] = $this->column_fields['hdnSubTotal'] = $_REQUEST['hdnSubTotal'] = $fieldList['subtotal'];
        }

        //if (!$pseudo) {
            parent::saveentity($module, $fileid);
        //}
        $fieldList = array_merge($fieldList, $this->column_fields);
        if (empty($fieldList['record'])) {
            if (!empty($fieldList['currentid'])) {
                $fieldList['record'] = $fieldList['currentid'];
            } else {
                //NO NO NO NO NO NO NO NO NO NO
                //$sql                 = "SELECT id FROM `vtiger_crmentity_seq`";
                //$result              = $db->pquery($sql, []);
                //$row                 = $result->fetchRow();
                //$fieldList['record'] = $row[0]++;
                //$sql                 = "UPDATE `vtiger_crmentity_seq` SET id = ?";
                //$db->pquery($sql, [$fieldList['record']]);
                $fieldList['record'] = $this->id;
            }
        }
        $quoteid = $fieldList['record'];
        // We're on an Opportunity save action, so use the field that has the correct ID...
        if($fieldList['module'] == 'Opportunities') {
            // Funny enough this is the correct ID.
            $quoteid = $this->id ?: $fieldList['cf_record_id'];
        }
        if (!$quoteid) {
            //@TODO: we should really never get here, but if we do throw an error... it's probably not caught.
            throw new Exception(vtranslate('LBL_RECORD_NOT_FOUND'), -1);
        }
        if (!$pseudo) {
            $newOrderId = $fieldList['orders_id'];
            CRMEntity::UpdateRelation($quoteid, $module, $newOrderId, 'Orders');
        }
        $tablePrefix = $pseudo?session_id().'_':'';

        if(!$pseudo && getenv('GOOGLE_ADDRESS_MILES_CALCULATOR'))
        {
            $db->pquery('DELETE FROM vtiger_google_addresscalc WHERE quoteid=?', [$quoteid]);
            $googleCount = count($fieldList['googleCalcAddress']);
            for($i=0;$i<$googleCount;++$i)
            {
                $db->pquery('INSERT INTO vtiger_google_addresscalc (`quoteid`,`address`,`miles`,`time`) VALUES (?,?,?,?)',
                            [$quoteid,
                             $fieldList['googleCalcAddress'][$i],
                             $fieldList['googleCalcMiles'][$i],
                             $fieldList['googleCalcTime'][$i],
                            ]);
            }
            if($googleCount > 0)
            {
                $db->pquery('INSERT INTO vtiger_google_addresscalc (`quoteid`,`address`,`miles`,`time`) VALUES (?,?,?,?)',
                            [$quoteid,
                             '_Total_',
                             $fieldList['googleCalcMilesTotal'],
                             $fieldList['googleCalcTimeTotal'],
                            ]);
            }
        }

        if (getenv('INSTANCE_NAME') == 'graebel') {
            $totalAutoWeight = 0;
            for ($i = 0; $i <= $fieldList['numVehicleTransportation']; $i++) {
                if ($fieldList['vehicletrans_ratingtype_'.$i] == 'Bulky') {
                    $totalAutoWeight += $fieldList['vehicletrans_weight_'.$i];
                }
            }
            $sql = 'UPDATE `'.$tablePrefix.'vtiger_quotes` SET `total_auto_weight_1950B`=? WHERE quoteid=?';
            $db->pquery($sql, [$totalAutoWeight, $quoteid]);
        }
        $vanlineId     = Estimates_Record_Model::getVanlineIdStatic($quoteid);
        $tariffId      = Estimates_Record_Model::getCurrentAssignedTariffStatic($quoteid);
        $tariffName    = Estimates_Record_Model::getAssignedTariffName($tariffId);
        $packingLabels = Estimates_Record_Model::getPackingLabelsStatic($vanlineId, $tariffName);
        $bulkyLabels   = Estimates_Record_Model::getBulkyLabelsStatic($vanlineId, $tariffName);
        if (getenv('INSTANCE_NAME') == 'graebel' && $tablePrefix == '') {
            $this->saveDetailedLineItem($fieldList, $quoteid);

            self::updateLineItemTotals($quoteid, $fieldList);
        } elseif ($tablePrefix == '') {
            $this->saveDetailedLineItem($fieldList, $quoteid);
        }
        //store the state of the sit_*_number_days to the quotes table at this point, because pseudo doesn't hit that saveentity above.
        if ($this->column_fields['sit_origin_number_days'] || $this->column_fields['sit_dest_number_days']) {
            $sql = "UPDATE `".$tablePrefix."vtiger_quotes` SET `sit_origin_number_days`=?, `sit_dest_number_days`=? WHERE quoteid=?";
            $db->pquery($sql, [$this->column_fields['sit_origin_number_days'], $this->column_fields['sit_dest_number_days'], $quoteid]);
        }
        //file_put_contents('logs/devLog.log', "\n EST saveentity tablePrefix: $tablePrefix", FILE_APPEND);
        $localTariffSave = Estimates_Record_Model::isLocalTariff($fieldTariff);
        $localTariff         = $fieldTariff;
        if ($localTariffSave && $localTariff) {
            //Logic for Local Move fields
            if($localTariff !== '') {
                $tariffRecordModel = Tariffs_Record_Model::getInstanceById($localTariff, 'Tariffs');
            }
            $effective_date_user = $fieldList['effective_date'];
            $effective_date = $effective_date_user;
            $sql             = "SELECT effectivedatesid FROM `vtiger_effectivedates`
                        INNER JOIN `vtiger_crmentity` ON (crmid=effectivedatesid)
                        WHERE effective_date <= ? AND related_tariff = ? AND deleted=0
                        ORDER BY `vtiger_effectivedates`.`effective_date` DESC LIMIT 1";
            $result          = $db->pquery($sql, [$effective_date, $localTariff]);
            $row             = $result->fetchRow();
            $effectiveDateId = $row['effectivedatesid'];
            $services        = $tariffRecordModel->getServiceIds($effectiveDateId);
            $tariffid        = $fieldTariff;
            if (empty($tariffid)) {
                $tariffid = 0;
            }
            $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes` WHERE quoteid = ?";
            $result = $db->pquery($sql, [$quoteid]);
            $row    = $result->fetchRow();
            if (
                \MoveCrm\InputUtils::CheckboxToBool($fieldList['is_primary']) &&
                !$pseudo
            ) {
                $this->doPrimaryEstimateLogic($fieldList, true);
            }
            if (!empty($row[0])) {
                $sql    = "UPDATE `".$tablePrefix."vtiger_quotes` SET effective_tariff=? WHERE quoteid=?";
                $result = $db->pquery($sql, [$tariffid, $quoteid]);
            } else {
                $sql    = "INSERT INTO `".$tablePrefix."vtiger_quotes` (effective_tariff, quoteid) VALUES (?,?)";
                $result = $db->pquery($sql, [$tariffid, $quoteid]);
                $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes` WHERE quoteid = ?";
                $result = $db->pquery($sql, [$quoteid]);
                $row    = $result->fetchRow();
            }
            foreach ($services as $sectionId => $serviceIds) {
                //Save logic for section-level discounts
                $discount_percent = $fieldList['SectionDiscount'.$sectionId];
                $sql              =
                    "SELECT * FROM `".$tablePrefix."vtiger_quotes_sectiondiscount` WHERE estimateid=? AND sectionid=?";
                $result           = $db->pquery($sql, [$quoteid, $sectionId]);
                $row              = $result->fetchRow();
                if ($row == null) {
                    $sql    = "INSERT INTO `".$tablePrefix.
                              "vtiger_quotes_sectiondiscount` (estimateid, sectionid, discount_percent) VALUES (?,?,?)";
                    $result = $db->pquery($sql, [$quoteid, $sectionId, $discount_percent]);
                } else {
                    $sql    = "UPDATE `".$tablePrefix.
                              "vtiger_quotes_sectiondiscount` SET discount_percent=? WHERE estimateid=? AND sectionid=?";
                    $result = $db->pquery($sql, [$discount_percent, $quoteid, $sectionId]);
                }
                foreach ($serviceIds as $id) {
                    //get the service level totals
                    $cost_service_total   = $fieldList['cost_service_total'.$id];
                    $cost_container_total = $fieldList['cost_container_total'.$id];
                    $cost_packing_total   = $fieldList['cost_packing_total'.$id];
                    $cost_unpacking_total = $fieldList['cost_unpacking_total'.$id];
                    $cost_crating_total   = $fieldList['cost_crating_total'.$id];
                    $cost_uncrating_total = $fieldList['cost_uncrating_total'.$id];
                    $sql                  = "SELECT cost_service_total FROM `".$tablePrefix.
                                            "vtiger_quotes_servicecost` WHERE estimateid = ? AND serviceid=?";
                    $result               = $db->pquery($sql, [$quoteid, $id]);
                    $row                  = $result->fetchRow();
                    if (empty($row)) {
                        $sql    = "INSERT INTO `".
                                  $tablePrefix.
                                  "vtiger_quotes_servicecost` (estimateid, serviceid, cost_service_total, cost_container_total, cost_packing_total, cost_unpacking_total, cost_crating_total, cost_uncrating_total) VALUES (?,?,?,?,?,?,?,?)";
                        $result = $db->pquery($sql,
                                              [$quoteid,
                                               $id,
                                               $cost_service_total,
                                               $cost_container_total,
                                               $cost_packing_total,
                                               $cost_unpacking_total,
                                               $cost_crating_total,
                                               $cost_uncrating_total]);
                    } else {
                        $sql    = "UPDATE `".
                                  $tablePrefix.
                                  "vtiger_quotes_servicecost` SET cost_service_total=?, cost_container_total=?, cost_packing_total=?, cost_unpacking_total=?, cost_crating_total=?, cost_uncrating_total=? WHERE estimateid =? AND serviceid=?";
                        $result = $db->pquery($sql,
                                              [$cost_service_total,
                                               $cost_container_total,
                                               $cost_packing_total,
                                               $cost_unpacking_total,
                                               $cost_crating_total,
                                               $cost_uncrating_total,
                                               $quoteid,
                                               $id]);
                    }
                    $serviceModel = TariffServices_Record_Model::getInstanceById($id);
                    $rateType     = $serviceModel->getRateType();
                    //Rate Type save logic
                    if ($rateType == 'Base Plus Trans.') {
                        $mileage = $fieldList['Miles'.$id];
                        $weight  = $fieldList['Weight'.$id];
                        $rate    = $fieldList['Rate'.$id];
                        $excess  = $fieldList['Excess'.$id];
                        $sql     =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_baseplus` WHERE estimateid=? AND serviceid=?";
                        $result  = $db->pquery($sql, [$quoteid, $id]);
                        $row     = $result->fetchRow();
                        if ($row == null) {
                            //Record does not exist - insert new record
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_baseplus` (estimateid, serviceid, mileage, weight, rate, excess) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $mileage, $weight, $rate, $excess]);
                        } else {
                            //Record exists - update record
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_baseplus` SET mileage=?, weight=?, rate=?, excess=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$mileage, $weight, $rate, $excess, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Break Point Trans.') {
                        $mileage    = $fieldList['Miles'.$id];
                        $rate       = $fieldList['Rate'.$id];
                        $weight     = $fieldList['Weight'.$id];
                        $breakpoint = $fieldList['calcWeight'.$id];
                        $sql        = "SELECT * FROM `".$tablePrefix.
                                      "vtiger_quotes_breakpoint` WHERE estimateid=? AND serviceid=?";
                        $result     = $db->pquery($sql, [$quoteid, $id]);
                        $row        = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_breakpoint` (estimateid, serviceid, mileage, weight, rate, breakpoint) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $mileage, $weight, $rate, $breakpoint]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_breakpoint` SET mileage=?, weight=?, rate=?, breakpoint=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$mileage, $weight, $rate, $breakpoint, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Service Base Charge') {
                        $rate   = $fieldList['ServiceCharge'.$id];
                        $sql    = "SELECT * FROM `".$tablePrefix.
                                  "vtiger_quotes_servicecharge` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_servicecharge` (estimateid, serviceid, rate) VALUES (?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $rate]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_servicecharge` SET rate=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$rate, $quoteid, $id]);
                        }
                    } else if ($rateType == 'Storage Valuation') {
                        $rate    = $fieldList['StorageValuation'.$id];
                        $months    = $fieldList['Month'.$id];
                        $sql        = "SELECT * FROM `".$tablePrefix.
                                    "vtiger_quotes_storage_valution` WHERE estimateid=? AND serviceid=?";
                        $result     = $db->pquery($sql, [$quoteid, $id]);
                        $row        = $result->fetchRow();
                        if ($row == NULL) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                "vtiger_quotes_storage_valution` (estimateid, serviceid, rate, months) VALUES (?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $rate,$months]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                "vtiger_quotes_storage_valution` SET rate=?, months = ?  WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$rate,$months, $quoteid, $id]);
                        }
                    } else if ($rateType == 'Weight/Mileage Trans.') {
                        $mileage = $fieldList['Miles'.$id];
                        $rate    = $fieldList['Rate'.$id];
                        $weight  = $fieldList['Weight'.$id];
                        $sql     = "SELECT * FROM `".$tablePrefix.
                                   "vtiger_quotes_weightmileage` WHERE estimateid=? AND serviceid=?";
                        $result  = $db->pquery($sql, [$quoteid, $id]);
                        $row     = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_weightmileage` (estimateid, serviceid, mileage, weight, rate) VALUES (?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $mileage, $weight, $rate]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_weightmileage` SET mileage=?, weight=?, rate=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$mileage, $weight, $rate, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Bulky List') {
                        $length = $fieldList['NumBulkys'.$id];
                        if ($length > 0) {
                            for ($i = 0; $i < $length; $i++) {
                                $description     = $fieldList['bulkyDescription'.$id.'-'.$i];
                                $qty             = $fieldList['Qty'.$id.'-'.$i];
                                $weight          = $fieldList['WeightAdd'.$id.'-'.$i];
                                $rate            = $fieldList['Rate'.$id.'-'.$i];
                                $bulky_id        = $fieldList['BulkyID'.$id.'-'.$i];
                                $cost_bulky_item = $fieldList['BulkyCost'.$id.'-'.$i];
                                $sql             = "SELECT * FROM `".$tablePrefix.
                                                   "vtiger_quotes_bulky` WHERE estimateid=? AND serviceid=? AND description=? AND bulky_id=?";
                                $result          = $db->pquery($sql, [$quoteid, $id, $description, $bulky_id]);
                                $row             = $result->fetchRow();
                                if ($row == null) {
                                    $sql    = "INSERT INTO `".$tablePrefix.
                                              "vtiger_quotes_bulky` (estimateid, serviceid, description, qty, weight, rate, bulky_id, cost_bulky_item) VALUES (?,?,?,?,?,?,?,?)";
                                    $result = $db->pquery($sql,
                                                          [$quoteid,
                                                           $id,
                                                           $description,
                                                           $qty,
                                                           $weight,
                                                           $rate,
                                                           $bulky_id,
                                                           $cost_bulky_item]);
                                } else {
                                    $sql    = "UPDATE `".$tablePrefix.
                                              "vtiger_quotes_bulky` SET qty=?, weight=?, rate=?, cost_bulky_item=? WHERE estimateid=? AND serviceid=? AND description=? AND bulky_id=?";
                                    $result = $db->pquery($sql,
                                                          [$qty,
                                                           $weight,
                                                           $rate,
                                                           $cost_bulky_item,
                                                           $quoteid,
                                                           $id,
                                                           $description,
                                                           $bulky_id]);
                                }
                            }
                        }
                    } elseif ($rateType == 'Charge Per $100 (Valuation)') {
                        //Keep it confusing!
                        $valuationType = $fieldList['ValuationType'.$id];
                        if($valuationType == 1) {
                            $qty1 = $fieldList['Coverage'.$id] * $fieldList['local_weight'];
                            $qty2 = 0;
                            $rate = $fieldList['Coverage'.$id];
                            $multiplier = 0;
                        }else if($valuationType == 0) {
                            $qty1 = $fieldList['Amount'.$id];
                            if(isset($fieldList['Deductible'.$id]) && $fieldList['Deductible'.$id] !== '') {
                                $qty2   = $fieldList['Deductible'.$id];
                            } else {
                                $qty2 = null;
                            }
                            $rate   = $fieldList['Rate'.$id];
                            $multiplier = $fieldList['Multiplier'.$id];
                        }else {
                            //@NOTE: We do not want to save info if 'Select An Option' is selected, which is the only option left if the above 2 fail.
                            continue;
                        }

                        $sql    = "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype, multiplier,flag) VALUES (?,?,?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType, $multiplier, $valuationType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=?, multiplier=?, flag=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $multiplier, $valuationType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'County Charge') {
                        $county = $fieldList['County'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    = "SELECT * FROM `".$tablePrefix.
                                  "vtiger_quotes_countycharge` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_countycharge` (estimateid, serviceid, county, rate) VALUES (?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $county, $rate]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_countycharge` SET county=?, rate=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$county, $rate, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Crating Item') {
                        $newRow = [];
                        foreach ($fieldList as $fieldName => $value) {
                            $dontDel = false;
                            $str     = 'crateID'.$id.'-';
                            $strpos  = strpos($fieldName, $str);
                            if ($strpos === 0) {
                                $exp            = explode('-', $fieldName);
                                $rowNum         = $exp[1];
                                $crateid        = $fieldList['crateID'.$id.'-'.$rowNum];
                                $description    = $fieldList['Description'.$id.'-'.$rowNum];
                                $crating_qty    = $fieldList['CratingQty'.$id.'-'.$rowNum];
                                $crating_rate   = $fieldList['CratingRate'.$id.'-'.$rowNum];
                                $uncrating_qty  = $fieldList['UncratingQty'.$id.'-'.$rowNum];
                                $uncrating_rate = $fieldList['UncratingRate'.$id.'-'.$rowNum];
                                $cost_crating   = $fieldList['CratingCost'.$id.'-'.$rowNum];
                                $cost_uncrating = $fieldList['UncratingCost'.$id.'-'.$rowNum];
                                $length         = $fieldList['Length'.$id.'-'.$rowNum];
                                $width          = $fieldList['Width'.$id.'-'.$rowNum];
                                $height         = $fieldList['Height'.$id.'-'.$rowNum];
                                $inches_added   = $fieldList['InchesAdded'.$id.'-'.$rowNum];
                                $line_item_id   = $rowNum;
                                $sql            = "SELECT * FROM `".$tablePrefix.
                                                  "vtiger_quotes_crating` WHERE estimateid=? AND serviceid=? AND line_item_id=?";
                                $result         = $db->pquery($sql, [$quoteid, $id, $line_item_id]);
                                $row            = $result->fetchRow();
                                if ($row == null) {
                                    $newRow[] = 'newRow'.$id.'-'.$rowNum;
                                    $sql      = "INSERT INTO `".
                                                $tablePrefix.
                                                "vtiger_quotes_crating` (estimateid, serviceid, crateid, description, crating_qty, crating_rate, uncrating_qty, uncrating_rate, length, width, height, inches_added, line_item_id, cost_crating, cost_uncrating) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                                    $result   = $db->pquery($sql,
                                                            [$quoteid,
                                                             $id,
                                                             $crateid,
                                                             $description,
                                                             $crating_qty,
                                                             $crating_rate,
                                                             $uncrating_qty,
                                                             $uncrating_rate,
                                                             $length,
                                                             $width,
                                                             $height,
                                                             $inches_added,
                                                             $line_item_id,
                                                             $cost_crating,
                                                             $cost_uncrating]);
                                } else {
                                    $sql    = "UPDATE `".
                                              $tablePrefix.
                                              "vtiger_quotes_crating` SET crateid=?, description=?, crating_qty=?, crating_rate=?, uncrating_qty=?, uncrating_rate=?, length=?, width=?, height=?, inches_added=?, cost_crating=?, cost_uncrating=? WHERE line_item_id=? AND estimateid=? AND serviceid=?";
                                    $result = $db->pquery($sql,
                                                          [$crateid,
                                                           $description,
                                                           $crating_qty,
                                                           $crating_rate,
                                                           $uncrating_qty,
                                                           $uncrating_rate,
                                                           $length,
                                                           $width,
                                                           $height,
                                                           $inches_added,
                                                           $cost_crating,
                                                           $cost_uncrating,
                                                           $line_item_id,
                                                           $quoteid,
                                                           $id]);
                                }
                            } else {
                                $str    = 'deleteRow'.$id.'-';
                                $strpos = strpos($fieldName, $str);
                                if ($strpos === 0) {
                                    $exp    = explode('-', $fieldName);
                                    $rowNum = $exp[1];
                                    $newAdd = 'newRow'.$id.'-'.$rowNum;
                                    $sql    = "SELECT * FROM `".$tablePrefix.
                                              "vtiger_quotes_crating` WHERE estimateid=? AND serviceid=? AND line_item_id=?";
                                    $result = $db->pquery($sql, [$quoteid, $id, $rowNum]);
                                    $row    = $result->fetchRow();
                                    for ($i = 0; $i < $newRow.length; $i++) {
                                        if ($newRow[$i] == $newAdd) {
                                            $dontDel = true;
                                        }
                                    }
                                    $tempBool = false;
                                    if ($row != null) {
                                        $tempBool = true;
                                    }
                                    if ($row != null && !$dontDel) {
                                        $sql    = "DELETE FROM `".$tablePrefix.
                                                  "vtiger_quotes_crating` WHERE estimateid=? AND serviceid=? AND line_item_id=?";
                                        $result = $db->pquery($sql, [$quoteid, $id, $rowNum]);
                                    }
                                }
                            }
                        }
                    } elseif ($rateType == 'Flat Charge') {
                        $qty1   = null;
                        $qty2   = null;
                        $rate   = $fieldList['Rate'.$id];
                        $rate_included = \MoveCrm\InputUtils::CheckboxToBool($fieldList['rateIncluded'.$id]);
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, rate_included, ratetype) VALUES (?,?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rate_included, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, rate_included=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rate_included, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Hourly Set') {
                        $men        = $fieldList['Men'.$id];
                        $vans       = $fieldList['Vans'.$id];
                        $hours      = $fieldList['Hours'.$id];
                        $traveltime = $fieldList['TravelTime'.$id];
                        $rate       = $fieldList['Rate'.$id];
                        $sql        = "SELECT * FROM `".$tablePrefix.
                                      "vtiger_quotes_hourlyset` WHERE estimateid=? AND serviceid=?";
                        $result     = $db->pquery($sql, [$quoteid, $id]);
                        $row        = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_hourlyset` (estimateid, serviceid, men, vans, hours, traveltime, rate) VALUES (?,?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $men, $vans, $hours, $traveltime, $rate]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_hourlyset` SET men=?, vans=?, hours=?, traveltime=?, rate=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$men, $vans, $hours, $traveltime, $rate, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Hourly Simple') {
                        $qty1   = $fieldList['Quantity'.$id];
                        $qty2   = $fieldList['Hours'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Packing Items') {
                        $length = $fieldList['NumPacking'.$id];
                        $sales_tax = $fieldList['SalesTax'.$id];
                        if ($length > 0) {
                            for ($i = 0; $i <= $length; $i++) {
                                $name           = $fieldList['Name'.$id.'-'.$i];
                                $container_qty  = $fieldList['containerQty'.$id.'-'.$i];
                                $container_rate = $fieldList['containerRate'.$id.'-'.$i];
                                $pack_qty       = $fieldList['packQty'.$id.'-'.$i];
                                $pack_rate      = $fieldList['packRate'.$id.'-'.$i];
                                $unpack_qty     = $fieldList['unpackQty'.$id.'-'.$i];
                                $unpack_rate    = $fieldList['unpackRate'.$id.'-'.$i];
                                $packing_id     = $fieldList['PackID'.$id.'-'.$i];
                                if (!$name) {
                                    continue;
                                }
                                $cost_container = $fieldList['ContainerCost'.$id.'-'.$i];
                                $cost_packing   = $fieldList['PackingCost'.$id.'-'.$i];
                                $cost_unpacking = $fieldList['UnpackingCost'.$id.'-'.$i];
                                $sql            = "SELECT * FROM `".$tablePrefix.
                                                  "vtiger_quotes_packing` WHERE estimateid=? AND serviceid=? AND name=? AND packing_id=?";
                                $result         = $db->pquery($sql, [$quoteid, $id, $name, $packing_id]);
                                $row            = $result->fetchRow();
                                if ($row == null) {
                                    $sql    = "INSERT INTO `".
                                              $tablePrefix.
                                              "vtiger_quotes_packing` (estimateid, serviceid, name, container_qty, container_rate, pack_qty, pack_rate, unpack_qty, unpack_rate, packing_id, cost_container, cost_packing, cost_unpacking, sales_tax) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                                    $result = $db->pquery($sql,
                                        [
                                            $quoteid,
                                            $id,
                                            $name,
                                            $container_qty,
                                            $container_rate,
                                            $pack_qty,
                                            $pack_rate,
                                            $unpack_qty,
                                            $unpack_rate,
                                            $packing_id,
                                            $cost_container,
                                            $cost_packing,
                                            $cost_unpacking,
                                            $sales_tax
                                        ]
                                    );
                                } else {
                                    $sql    = "UPDATE `".
                                              $tablePrefix.
                                              "vtiger_quotes_packing` SET sales_tax=?, container_qty=?, container_rate=?, pack_qty=?, pack_rate=?, unpack_qty=?, unpack_rate=?, cost_container=?, cost_packing=?, cost_unpacking=? WHERE estimateid=? AND serviceid=? AND name=? AND packing_id=?";
                                    $result = $db->pquery($sql,
                                        [
                                            $sales_tax,
                                            $container_qty,
                                            $container_rate,
                                            $pack_qty,
                                            $pack_rate,
                                            $unpack_qty,
                                            $unpack_rate,
                                            $cost_container,
                                            $cost_packing,
                                            $cost_unpacking,
                                            $quoteid,
                                            $id,
                                            $name,
                                            $packing_id
                                        ]
                                    );
                                }
                            }
                        }
                    } elseif ($rateType == 'Per Cu Ft') {
                        $qty1   = $fieldList['CubicFeet'.$id];
                        $qty2   = null;
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Per Cu Ft/Per Day') {
                        $qty1   = $fieldList['CubicFeet'.$id];
                        $qty2   = $fieldList['Days'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Per Cu Ft/Per Month') {
                        $qty1   = $fieldList['CubicFeet'.$id];
                        $qty2   = $fieldList['Months'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } else if ($rateType == 'Per CWT' || $rateType == 'SIT First Day Rate' ) {
                        $qty1   = $fieldList['Weight'.$id];
                        $qty2   = null;
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } else if ($rateType == 'Per CWT/Per Day' || $rateType == 'SIT Additional Day Rate') {
                        $qty1   = $fieldList['Weight'.$id];
                        $qty2   = $fieldList['Days'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Per CWT/Per Month') {
                        $qty1   = $fieldList['Weight'.$id];
                        $qty2   = $fieldList['Months'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Per Quantity') {
                        $qty1   = $fieldList['Quantity'.$id];
                        $qty2   = null;
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Per Quantity/Per Day') {
                        $qty1   = $fieldList['Quantity'.$id];
                        $qty2   = $fieldList['Days'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'Per Quantity/Per Month') {
                        $qty1   = $fieldList['Quantity'.$id];
                        $qty2   = $fieldList['Months'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    =
                            "SELECT * FROM `".$tablePrefix."vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $qty1, $qty2, $rate, $rateType]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$qty1, $qty2, $rate, $rateType, $quoteid, $id]);
                        }
                    } elseif ($rateType == 'SIT Item') {
                        $cartage       = $fieldList['Cartage'.$id];
                        $firstDay      = $fieldList['FirstDay'.$id];
                        $additionalDay = $fieldList['AdditionalDay'.$id];
                        $sql           = "SELECT * FROM `".$tablePrefix."vtiger_quotes_sit` WHERE estimateid=? AND serviceid=?";
                        $result        = $db->pquery($sql, [$quoteid, $id]);
                        $row           = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `vtiger_quotes_sit`
                                        (estimateid, serviceid, cartage_cwt_rate, first_day_rate, additional_day_rate)
                                        VALUES (?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $cartage, $firstDay, $additionalDay]);
                        } else {
                            $sql    = "UPDATE `vtiger_quotes_sit`
                                          SET cartage_cwt_rate=?, first_day_rate=?, additional_day_rate=?
                                          WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$quoteid, $id, $cartage, $firstDay, $additionalDay]);
                        }
                    } elseif ($rateType == 'Tabled Valuation') {
                        $released        = $fieldList['ValuationType'.$id];
                        $multiplier      = $fieldList['Multiplier'.$id];
                        $released_amount = ($released == 1)?$fieldList['Coverage'.$id]:null;
                        $amount          = ($released == 0)?$fieldList['Amount'.$id]:null;
                        $deductible      = ($released == 0)?$fieldList['Deductible'.$id]:null;
                        $rate            = ($released == 0)?$fieldList['Rate'.$id]:null;
                        $sql             = "SELECT * FROM `".$tablePrefix.
                                           "vtiger_quotes_valuation` WHERE estimateid=? AND serviceid=?";
                        $result          = $db->pquery($sql, [$quoteid, $id]);
                        $row             = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_valuation` (estimateid, serviceid, released, released_amount, amount, deductible, rate, multiplier) VALUES (?,?,?,?,?,?,?,?)";
                            $result = $db->pquery($sql,
                                                  [$quoteid,
                                                   $id,
                                                   $released,
                                                   $released_amount,
                                                   $amount,
                                                   $deductible,
                                                   $rate,
                                                   $multiplier]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_valuation` SET released=?, released_amount=?, amount=?, deductible=?, rate=?, multiplier=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql,
                                                  [$released,
                                                   $released_amount,
                                                   $amount,
                                                   $deductible,
                                                   $rate,
                                                   $multiplier,
                                                   $quoteid,
                                                   $id]);
                        }
                    } else if ($rateType == 'CWT by Weight' || $rateType == 'SIT Cartage' ) {
                        $weight = $fieldList['Weight'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $sql    = "SELECT * FROM `".$tablePrefix.
                                  "vtiger_quotes_cwtbyweight` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_cwtbyweight` (estimateid, serviceid, weight, rate) VALUES (?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $weight, $rate]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_cwtbyweight` SET weight=?, rate=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$weight, $rate, $quoteid, $id]);
                        }
                    } else if ($rateType == 'Flat Rate By Weight') {

                      $cwt          = $fieldList['Excess'.$id] != NULL ? $fieldList['Excess'.$id] : 0;
                      $weight       = $fieldList['Weight'.$id] != NULL ? $fieldList['Weight'.$id] : 0;
                      $weightCap    = $fieldList['frbw_cap'.$id] != NULL ? $fieldList['frbw_cap'.$id] : 0;
                      $rate         = $fieldList['Rate'.$id] != NULL ? $fieldList['Rate'.$id] : 0;
                      if($weight == 0) {
                        $rate = 0;
                      }
                      $line_item_id = 0;

                      $sql = "SELECT * FROM `".$tablePrefix."vtiger_quotes_flatratebyweight` WHERE estimateid = ? AND serviceid = ?";
                      $result = $db->pquery($sql,[$quoteid,$id]);
                      if($db->num_rows($result) > 0) {
                        $sql = "UPDATE `".$tablePrefix."vtiger_quotes_flatratebyweight` SET weight = ?, rate = ?, cwt_rate = ?, weight_cap = ?, line_item_id = ? WHERE estimateid = ? AND serviceid = ?";
                      } else {
                        // In case the weight changes into a new tier
                        $sql = "DELETE FROM `".$tablePrefix."vtiger_quotes_flatratebyweight` WHERE estimateid = ? AND serviceid = ?";
                        $db->pquery($sql,[$quoteid,$id]);
                        $sql = "INSERT INTO `".$tablePrefix."vtiger_quotes_flatratebyweight` (weight, rate, cwt_rate, weight_cap, line_item_id, estimateid, serviceid) VALUES(?,?,?,?,?,?,?)";
                      }
                      $db->pquery($sql,[$weight,$rate,$cwt,$weightCap,$line_item_id,$quoteid,$id]);

                    } else if ($rateType == 'CWT Per Quantity' ) {
                        $quantity = $fieldList['Quantity'.$id];
                        $rate   = $fieldList['Rate'.$id];
                        $weight   = $fieldList['Weight'.$id];
                        $sql    = "SELECT * FROM `".$tablePrefix.
                                  "vtiger_quotes_cwtperqty` WHERE estimateid=? AND serviceid=?";
                        $result = $db->pquery($sql, [$quoteid, $id]);
                        $row    = $result->fetchRow();
                        if ($row == NULL) {
                            $sql    = "INSERT INTO `".$tablePrefix.
                                      "vtiger_quotes_cwtperqty` (estimateid, serviceid, quantity, rate, weight) VALUES (?,?,?,?,?)";
                            $result = $db->pquery($sql, [$quoteid, $id, $quantity, $rate, $weight]);
                        } else {
                            $sql    = "UPDATE `".$tablePrefix.
                                      "vtiger_quotes_cwtperqty` SET quantity=?, rate=?, weight=? WHERE estimateid=? AND serviceid=?";
                            $result = $db->pquery($sql, [$quantity, $rate, $weight, $quoteid, $id]);
                        }
                    }
                }
            }
        } elseif ($fieldTariff) {
            if (getenv('INSTANCE_NAME') == 'graebel') {
                $sql = "DELETE FROM `".$tablePrefix."vtiger_quotes_inter_servchg` WHERE quoteid=?";
                $db->pquery($sql, [$quoteid]);
                if (!empty($compiledServiceCharges)) {
                    foreach ($compiledServiceCharges as $chargeRow) {
                        $chargeApplied = \MoveCrm\InputUtils::CheckboxToBool($chargeRow['applied']);
                        $chargeAlwaysUsed = \MoveCrm\InputUtils::CheckboxToBool($chargeRow['always_used']);
                        if ($chargeAlwaysUsed == 1) {
                            $chargeApplied = 1;
                        }
                        $sql =
                            "INSERT INTO `".
                            $tablePrefix.
                            "vtiger_quotes_inter_servchg` (quoteid, serviceid, is_dest, service_description, always_used, charge, minimum, service_weight, applied) VALUES (?,?,?,?,?,?,?,?,?)";
                        $db->pquery($sql,
                                    [$quoteid,
                                     $chargeRow['serviceid'],
                                     $chargeRow['is_dest'],
                                     $chargeRow['service_description'],
                                     $chargeAlwaysUsed,
                                     $chargeRow['charge'],
                                     $chargeRow['minimum'],
                                     $chargeRow['service_weight'],
                                     $chargeApplied]);
                    }
                }
            }

            $numVehicles = 0;
            if ($fieldList['numCorporateVehicles'] != null) {
                $numVehicles = $fieldList['numCorporateVehicles'];
            }

            if(getenv('INSTANCE_NAME') == 'sirva') {
                if(isset($fieldList['pack_rates']) && $fieldList['pack_rates'] != '') {
                    $sql = "UPDATE `".$tablePrefix."vtiger_quotes` SET pack_rates=? WHERE quoteid=?";
                    $db->pquery($sql, [$fieldList['pack_rates'], $quoteid]);
                }
                $sql = "DELETE FROM `".$tablePrefix."vtiger_quotes_inter_servchg` WHERE quoteid=?";
                $db->pquery($sql, [$quoteid]);
                if(!empty($compiledServiceCharges)) {
                    if(!is_array($compiledServiceCharges)) {
                        $compiledServiceCharges = json_decode($compiledServiceCharges, true);
                    }
                    foreach ($compiledServiceCharges as $chargeRow) {
                        $chargeApplied    = ($chargeRow['applied'] == 1 || $chargeRow['applied'] == 'on') ? 1 : 0;
                        $chargeAlwaysUsed = ($chargeRow['always_used'] == 1 || $chargeRow['always_used'] == 'on') ? 1 : 0;
                        // if ($chargeAlwaysUsed == 1) {
                        //     $chargeApplied = 1;
                        // }
                        $sql = "INSERT INTO `".$tablePrefix."vtiger_quotes_inter_servchg` (quoteid, serviceid, is_dest, service_description, always_used, charge, minimum, service_weight, applied) VALUES (?,?,?,?,?,?,?,?,?)";
                        $db->pquery($sql, [$quoteid, $chargeRow['serviceid'], $chargeRow['is_dest'], $chargeRow['service_description'], $chargeAlwaysUsed, $chargeRow['charge'], $chargeRow['minimum'], $chargeRow['service_weight'], $chargeApplied]);
                    }
                }
            }
            if ($numVehicles > 0) {
                for ($i = 1; $i <= $numVehicles; $i++) {
                    $make               = $fieldList['vehicle_make_'.$i];
                    $model              = $fieldList['vehicle_model_'.$i];
                    $year               = $fieldList['vehicle_year_'.$i];
                    $weight             = $fieldList['vehicle_weight_'.$i];
                    $cube               = $fieldList['vehicle_cube_'.$i];
                    $service            = $fieldList['vehicle_service_'.$i];
                    $dvp_value          = $fieldList['vehicle_dvp_value_'.$i];
                    $car_on_van         = $fieldList['vehicle_car_on_van_'.$i];
                    $oversize_class     = $fieldList['vehicle_oversize_class_'.$i];
                    $inoperable         = $fieldList['vehicle_inoperable_'.$i];
                    $length             = $fieldList['vehicle_length_'.$i];
                    $width              = $fieldList['vehicle_width_'.$i];
                    $height             = $fieldList['vehicle_height_'.$i];
                    $charge             = $fieldList['vehicle_charge_'.$i];
                    $shipping_count     = $fieldList['vehicle_shipping_count_'.$i];
                    $not_shipping_count = $fieldList['vehicle_not_shipping_count_'.$i];
                    $comment            = $fieldList['vehicle_comment_'.$i];
                    if ($fieldList['removeVehicle_'.$i] != null) {
                        $sql = "DELETE FROM `".$tablePrefix.
                               "vtiger_corporate_vehicles` WHERE estimate_id=? AND vehicle_id=?";
                        $db->pquery($sql, [$quoteid, $i]);
                    } elseif ($make !== null) {
                        $sql    = "SELECT * FROM `".$tablePrefix.
                                  "vtiger_corporate_vehicles` WHERE estimate_id=? AND vehicle_id=?";
                        $result = $db->pquery($sql, [$quoteid, $i]);
                        $row    = $result->fetchRow();
                        if ($row == null) {
                            $sql    = "INSERT INTO `".
                                      $tablePrefix.
                                      "vtiger_corporate_vehicles` (estimate_id, vehicle_id, make, model, year, weight, cube, service, dvp_value, car_on_van, oversize_class, inoperable, length, width, height, charge, shipping_count, not_shipping_count, comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $result = $db->pquery($sql,
                                                  [$quoteid,
                                                   $i,
                                                   $make,
                                                   $model,
                                                   $year,
                                                   $weight,
                                                   $cube,
                                                   $service,
                                                   $dvp_value,
                                                   $car_on_van,
                                                   $oversize_class,
                                                   $inoperable,
                                                   $length,
                                                   $width,
                                                   $height,
                                                   $charge,
                                                   $shipping_count,
                                                   $not_shipping_count,
                                                   $comment]);
                        } else {
                            $sql    = "UPDATE `".
                                      $tablePrefix.
                                      "vtiger_corporate_vehicles` SET make=?, model=?, year=?, weight=?, cube=?, service=?, dvp_value=?, car_on_van=?, oversize_class=?, inoperable=?, length=?, width=?, height=?, charge=?, shipping_count=?, not_shipping_count=?, comment=? WHERE estimate_id=? AND vehicle_id=?";
                            $result = $db->pquery($sql,
                                                  [$make,
                                                   $model,
                                                   $year,
                                                   $weight,
                                                   $cube,
                                                   $service,
                                                   $dvp_value,
                                                   $car_on_van,
                                                   $oversize_class,
                                                   $inoperable,
                                                   $length,
                                                   $width,
                                                   $height,
                                                   $charge,
                                                   $shipping_count,
                                                   $not_shipping_count,
                                                   $comment,
                                                   $quoteid,
                                                   $i]);
                        }
                    }
                }
            }
            $i = 1;
            if ($_REQUEST['isSyncEstimate'] == 1) {
                //Delete all crates from this record and add new crates from device.
                $sql = "DELETE FROM `vtiger_crates` WHERE quoteid=?";
                $db->pquery($sql, [$quoteid]);
            }
            $numCrates = 0;
            if ($fieldList['interstateNumCrates']) {
                $numCrates = $fieldList['interstateNumCrates'];
            }
            $i = 0;
            while ($i < $numCrates) {
                $i++;
                $crateid     = $fieldList['crateID'.$i];
                $description = $fieldList['crateDescription'.$i];
                $length      = $fieldList['crateLength'.$i];
                $width       = $fieldList['crateWidth'.$i];
                $height      = $fieldList['crateHeight'.$i];
                $pack        = $fieldList['cratePack'.$i];
                $unpack      = $fieldList['crateUnpack'.$i];
                $otpack      = $fieldList['crateOTPack'.$i];
                $otunpack    = $fieldList['crateOTUnpack'.$i];
                $discount    = $fieldList['crateDiscounted'.$i];
                $lineItemId  = $fieldList['crateLineItemId'.$i];
                $apply_tariff       = $fieldList['crateApplyTariff'.$i];
                $custom_rate_amount = $fieldList['crateCustomRateAmount'.$i];
                $custom_rate_amount_unpack = $fieldList['crateCustomRateAmountUnpack'.$i];
                $padding     = 4;
				if(getenv('INSTANCE_NAME') == 'graebel'){
					$padding = 0;
				}
                if ($description == '' || $length == '' || $length == '0' || $width == '' || $width == '0' ||
                    $height == '' || $height == '0' || ($pack + $unpack + $otpack + $otunpack < 1)
                ) {
                    continue;
                }
                // have to do this to make sure a new row is created if $lineItemId is 0, just in case
                // there is a row in the database with id 0
                if ($lineItemId) {
                    $sql    = "SELECT * FROM `".$tablePrefix.
                              "vtiger_crates` WHERE quoteid=? AND line_item_id=?";
                    $result = $db->pquery($sql, [$quoteid, $lineItemId]);
                    $row    = $result->fetchRow();
                } else {
                    $row = null;
                }
                $params = [];
                if ($row == null) {
                    //Update line_item_id from vtiger_crates_seq and increment id value in table
                    $sql        = "UPDATE `".$tablePrefix."vtiger_crates_seq` SET id=id+1";
                    $result     = $db->pquery($sql, $params);
                    $sql        = "SELECT id FROM `".$tablePrefix."vtiger_crates_seq`";
                    $result     = $db->pquery($sql, $params);
                    $row        = $result->fetchRow();
                    $lineItemId = $row[0];
                    //Create new item
                    if (getenv('IGC_MOVEHQ')) {
                        $sql = "INSERT INTO `".$tablePrefix.
                               "vtiger_crates` (quoteid, crateid, description, length, width, height, pack, unpack, ot_pack, ot_unpack, discount, cube, apply_tariff, custom_rate_amount, custom_rate_amount_unpack, line_item_id)
                               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    } else {
                        $sql = "INSERT INTO `".$tablePrefix.
                               "vtiger_crates` (quoteid, crateid, description, length, width, height, pack, unpack, ot_pack, ot_unpack, discount, cube, line_item_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    }
                } else {
                    if (getenv('IGC_MOVEHQ')) {
                        $sql = "UPDATE `".$tablePrefix.
                               "vtiger_crates` SET quoteid=?, crateid=?, description=?, length=?, width=?, height=?, pack=?, unpack=?, ot_pack=?, ot_unpack=?, discount=?, cube=?, apply_tariff=?,
                               custom_rate_amount=?,custom_rate_amount_unpack=?
                               WHERE line_item_id=?";
                    } else {
                        $sql = "UPDATE `".$tablePrefix.
                               "vtiger_crates` SET quoteid=?, crateid=?, description=?, length=?, width=?, height=?, pack=?, unpack=?, ot_pack=?, ot_unpack=?, discount=?, cube=? WHERE line_item_id=?";
                    }
                }
                $params[] = $quoteid;
                $params[] = $crateid;
                $params[] = $description;
                $params[] = $length;
                $params[] = $width;
                $params[] = $height;
                $params[] = $pack;
                $params[] = $unpack;
                $params[] = $otpack;
                $params[] = $otunpack;
                $params[] = $discount;
                $params[] = ceil(($length + $padding) * ($width + $padding) * ($height + $padding) / (12 * 12 * 12));
                if (getenv('IGC_MOVEHQ')) {
                    $params[] = \MoveCrm\InputUtils::CheckboxToBool($apply_tariff);
                    $params[] = $custom_rate_amount;
                    $params[] = $custom_rate_amount_unpack;
                    $params[] = $lineItemId;
                } else {
                    $params[] = $lineItemId;
                }
                $result   = $db->pquery($sql, $params);
                unset($params);
            }
            foreach ($fieldList as $fieldName => $value) {
                if ($fieldName === 'tpg_custom_crate_rate') {
                    $sql = "UPDATE `".$tablePrefix."vtiger_quotes` SET tpg_custom_crate_rate = ? WHERE quoteid = ?";
                    $db->pquery($sql, [$value, $quoteid]);
                } elseif ($fieldName === 'apply_custom_sit_rate_override') {
                    $value = \MoveCrm\InputUtils::CheckboxToBool($value);
                    $sql   = "UPDATE `".$tablePrefix.
                             "vtiger_quotes` SET apply_custom_sit_rate_override = ? WHERE quoteid = ?";
                    $db->pquery($sql, [$value, $quoteid]);
                } elseif ($fieldName === 'apply_custom_sit_rate_override_dest') {
                    $value = \MoveCrm\InputUtils::CheckboxToBool($value);
                    $sql   = "UPDATE `".$tablePrefix.
                             "vtiger_quotes` SET apply_custom_sit_rate_override_dest = ? WHERE quoteid = ?";
                    $db->pquery($sql, [$value, $quoteid]);
                } elseif ($fieldName === 'apply_custom_pack_rate_override') {
                    $value = \MoveCrm\InputUtils::CheckboxToBool($value);
                    $sql   = "UPDATE `".$tablePrefix.
                             "vtiger_quotes` SET apply_custom_pack_rate_override = ? WHERE quoteid = ?";
                    $db->pquery($sql, [$value, $quoteid]);
                } elseif (preg_match('/^bulky\d+/', $fieldName)) {
                    //Found bulky item
//                    $vanlineId = Estimates_Record_Model::getVanlineIdStatic($quoteid);
//                    $tariffId = Estimates_Record_Model::getCurrentAssignedTariffStatic($quoteid);
//                    $tariffName = Estimates_Record_Model::getAssignedTariffName($tariffId);
//                    $labels = Estimates_Record_Model::getBulkyLabelsStatic($vanlineId, $tariffName);
                    $sql    = "SELECT * FROM `".$tablePrefix."vtiger_bulky_items` WHERE quoteid=? AND bulkyid=?";
                    unset($params);
                    $params[] = $quoteid;
                    $params[] = $bulkyId = substr($fieldName, 5);
                    $result   = $db->pquery($sql, $params);
                    $row      = $result->fetchRow();
                    if ($row == null) {
                        if ($value == '0') {
                            continue;
                        }
                        $sql      = "INSERT INTO `".$tablePrefix."vtiger_bulky_items` VALUES (?,?,?,?)";
                        $params[] = $value;
                        $params[] = $bulkyLabels[$bulkyId];
                    } else {
                        $sql =
                            "UPDATE `".$tablePrefix."vtiger_bulky_items` SET ship_qty=? WHERE quoteid=? AND bulkyid=?";
                        unset($params);
                        $params[] = $value;
                        $params[] = $quoteid;
                        $params[] = substr($fieldName, 5);
                    }
                    $result = $db->pquery($sql, $params);
                    unset($params);
                } elseif (strpos($fieldName, 'pack') !== false || strpos($fieldName, 'containers') !== false) {
                    $params                = [];
                    $notRelevantFieldNames = ['cost_packing_total',
                                              'cost_unpacking_total',
                                              'packRate',
                                              'unpackRate',
                                              'full_pack',
                                              'full_unpack',
                                              'packing_disc',
                                              'apply_full_pack_rate_override',
                                              'full_pack_rate_override',
                                              'apply_custom_pack_rate_override',
                                              'UnpackingCost',
                                              'packQty',
                                              'unpackQty',
                                              'crateUnpack',
                                              'crateOTUnpack',
                                              '',
                                              'crateUnpack',
                                              'crateOTUnpack',
                                              'crateCustomRateAmountUnpack'
                    ];
                    preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                    $itemType = substr($fieldName, 0, $m[0][1]);
                    if (!in_array($itemType, $notRelevantFieldNames)) {
                        //Found packing item
                        preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                        $itemId   = substr($fieldName, $m[0][1]);
                        $itemType = substr($fieldName, 0, $m[0][1]);
                        if (strpos($itemId, '_') !== false) {
                            // extra stop packing item: this will be saved when stops are saved
                            continue;
                        }
                        if (getenv('INSTANCE_NAME') == 'sirva' && $itemId == 103) {
                            $itemId = '102';
                        }

                        $sql    = "SELECT * FROM `".$tablePrefix."vtiger_packing_items` WHERE quoteid=? AND itemid=?";
                        unset($params);
                        $params[] = $quoteid;
                        $params[] = $itemId;
                        $db       = PearDatabase::getInstance();
                        $result   = $db->pquery($sql, $params);
                        if ($itemType == 'packCustomRate') {
                            //custom_rate is a sirva specific column, modifying this to soft fail
                            if (getenv('INSTANCE_NAME') == 'sirva') {
                                $itemType = 'custom_rate';
                            } else {
                                continue;
                            }
                        } elseif ($itemType == 'packPackRate') {
                            //custom_rate is a sirva specific column, modifying this to soft fail
                            if (getenv('INSTANCE_NAME') == 'sirva') {
                                $itemType = 'pack_rate';
                            } else {
                                continue;
                            }
                        } elseif ($itemType == 'containers_pack') {
                            if (getenv('INSTANCE_NAME') != 'graebel') {
                                // this should never be executed, since the template is conditional, but just in case
                                continue;
                            } else {
                                $itemType = 'containers';
                            }
                        } elseif (getenv('INSTANCE_NAME') != 'sirva' && ($itemType == 'pack_cont_qty' || $itemType == 'containers')) {
                            $itemType = 'containers';
                        } elseif ($itemType != 'pack_cont_qty') {
                            $itemType .= "_qty";
                        }


                        $row = $result->fetchRow();
                        if ($row == null) {
                            if ($value == '0') {
                                continue;
                            }
                            $sql      =
                                "INSERT INTO `".$tablePrefix."vtiger_packing_items` (quoteid, itemid, ".$itemType.
                                ", label) VALUES (?,?,?,?)";
                            $params[] = $value;
                            $params[] = $packingLabels[$itemId];
                        } else {
                            $sql = "UPDATE `".$tablePrefix."vtiger_packing_items` SET ".$itemType.
                                   "=? WHERE quoteid=? AND itemid=?";
                            unset($params);
                            $params[] = $value;
                            $params[] = $quoteid;
                            $params[] = $itemId;
                        }
                        $result = $db->pquery($sql, $params);
                        unset($params);
                    }
                } else if ($fieldName == 'is_primary') {
                    $hasPrefix = strpos($fieldList['potential_id'], 'x');
                    $potentialid = $hasPrefix === false ? $fieldList['potential_id'] : substr(strstr($fieldList['potential_id'], 'x'), 1);
                    if (
                        \MoveCrm\InputUtils::CheckboxToBool($value) &&
                        !$pseudo
                    ) {
                        $this->doPrimaryEstimateLogic($fieldList, false);
                        //Add logic to handle OT2016 - Populating Estimate Linehaul in Orders
                        $orderId = $fieldList['orders_id'];
                        //Remove last condition of if statement when a mapping for dli_tariff_item_number exists
                        if ($value == 'on' && $orderId && Vtiger_Utils::CheckTable('vtiger_detailed_lineitems')) {

                            if ($orderRecordModel = Vtiger_Record_Model::getInstanceById($orderId, 'Orders')) {
                                $sql               = "SELECT dli_invoice_net FROM `vtiger_detailed_lineitems` WHERE dli_relcrmid=? AND dli_description = ?";
                                $result            = $db->pquery($sql, [$fieldList['record'], 'Linehaul']);
                                $estimatedLinehaul = 0;
                                while ($row =& $result->fetchRow()) {
                                    $estimatedLinehaul += $row['dli_invoice_net'];
                                }
                                //set the linehaul
                                $orderRecordModel->set('orders_elinehaul', $estimatedLinehaul);
                                $orderRecordModel->set('mode', 'edit');
                                //save the record.
                                $orderRecordModel->save();
                            }
                        }
                    }
                } elseif ($fieldName == 'tpg_transfactor') {
                    $tpg_transfactor_value = $value;
                    if ($tpg_transfactor_value) {
                        $sql = "UPDATE `".$tablePrefix."vtiger_quotes` SET tpg_transfactor=? WHERE quoteid=?";
                        $db->pquery($sql, [$tpg_transfactor_value, $quoteid]);
                    }
                } elseif ($fieldName == 'effective_tariff') {
                    $tariffid = $value;

                    // need to set express_truckload flag if the tariff is truckload because opps use it.
                    $sql = "SELECT tariffmanagername FROM vtiger_tariffmanager WHERE tariffmanagerid = ?";
                    $res = $db->pquery($sql, [$tariffid]);
                    if($res && $res->fetchRow()[0] == 'Truckload Express')  {
                        $sql = "UPDATE `".$tablePrefix."vtiger_quotes` SET express_truckload=1 WHERE quoteid=?";
                        $res = $db->pquery($sql, [$quoteid]);
                    }

                    if ($tariffid) {
                        $sql    = "UPDATE `".$tablePrefix."vtiger_quotes` SET effective_tariff=? WHERE quoteid=?";
                        $result = $db->pquery($sql, [$tariffid, $quoteid]);
                    }
                } elseif ($fieldName == 'sts_vehicles') {
                    $costs = $value;
                    if ($costs) {
                        $sql    = "UPDATE `".$tablePrefix."vtiger_quotes` SET sts_vehicles=? WHERE quoteid=?";
                        $result = $db->pquery($sql, [$costs, $quoteid]);
                    }
                } else if ((strpos($fieldName, 'vehicleDescription') === 0)) {
                    $row = explode('-', $fieldName)[1];
                    if ($row != NULL && $value != '') {
                        $vehicleId          = $fieldList['vehicleID-'.$row];
                        $vehicleDescription = $value;
                        $vehicleWeight      = $fieldList['vehicleWeight-'.$row];

                        $vehicleMake        = $fieldList['vehicleMake-'.$row];
                        $vehicleModel       = $fieldList['vehicleModel-'.$row];
                        $vehicleYear        = $fieldList['vehicleYear-'.$row];

                        $sql    = "SELECT * FROM `".$tablePrefix.
                                              "vtiger_quotes_vehicles` WHERE estimateid = ? AND vehicle_id = ?";
                        $result = $db->pquery($sql, [$quoteid, $vehicleId]);
                        $row    = $result->fetchRow();

                        if ($row == NULL) {
                            $sql = "INSERT INTO `".$tablePrefix.
                                   "vtiger_quotes_vehicles` (estimateid, `description`, `weight`, `make`, `model`, `year`) VALUES (?,?,?,?,?,?)";
                        } else {
                            $sql = "UPDATE `".$tablePrefix.
                                   "vtiger_quotes_vehicles` SET estimateid = ?, description = ?, weight = ?, make = ?, model = ?, year = ? WHERE vehicle_id = ?";
                        }
                        $result = $db->pquery($sql, [$quoteid, $vehicleDescription, $vehicleWeight, $vehicleMake, $vehicleModel, $vehicleYear, $vehicleId]);
                    }
                } else {
                    continue;
                }
            }
        }
        $i = 1;
        while ($fieldList['flatDescription'.$i] != null) {
            $description  = $fieldList['flatDescription'.$i];
            $charge       = $fieldList['flatCharge'.$i];
            $discounted   = $fieldList['flatDiscounted'.$i];
            $discount     = $fieldList['flatDiscountPercent'.$i];
            $lineItemId   = $fieldList['flatLineItemId'.$i];
            $enforced     = $fieldList['flatEnforced'.$i];
            $fromContract = $fieldList['flatFromContract'.$i];
            if (getenv('INSTANCE_NAME') == 'graebel') {
                $included = $fieldList['flatChargeToBeRated'.$i];
            } else {
                $included = 'on';
            }
            if ($description == '') {
                $i++;
                continue;
            }
            //OK we need to account for editview rate... which does have lineitemid but no real table.
            $sql    = "SELECT * FROM `".$tablePrefix.
                      "vtiger_misc_accessorials` WHERE quoteid=? AND line_item_id=?";
            $result = $db->pquery($sql, [$quoteid, $lineItemId]);
            $row    = $result->fetchRow();
            $params = [];
            if ($row == null) {
                //Update line_item_id from vtiger_misc_accessorials_seq and increment id value in table
                $sql        = "UPDATE `".$tablePrefix."vtiger_misc_accessorials_seq` SET id=id+1";
                $result     = $db->pquery($sql, $params);
                $sql        = "SELECT id FROM `".$tablePrefix."vtiger_misc_accessorials_seq`";
                $result     = $db->pquery($sql, $params);
                $row        = $result->fetchRow();
                $lineItemId = $row[0];
                //Create new item
                $sql = "INSERT INTO `".$tablePrefix.
                       "vtiger_misc_accessorials` (quoteid, included, description, charge, qty, discounted, discount,
charge_type, line_item_id, enforced, from_contract) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            } else {
                //Update item
                if ($enforced) {
                    // do not skip, since included for rating needs to be updated
                    //skip updating an enforced item.
                    //$i++;
                    //continue;
                }
                $sql = "UPDATE `".$tablePrefix.
                       "vtiger_misc_accessorials` SET quoteid=?, included=?, description=?, charge=?, qty=?, discounted=?, discount=?, charge_type=? WHERE line_item_id=?";
            }
            $params[] = $quoteid;
            $params[] = \MoveCrm\InputUtils::CheckboxToBool($included);
            $params[] = $description;
            $params[] = $charge;
            $params[] = '1';
            $params[] = \MoveCrm\InputUtils::CheckboxToBool($discounted);
            $params[] = $discount;
            $params[] = 'flat';
            $params[] = $lineItemId;
            $params[] = $enforced;
            $params[] = $fromContract;
            $result   = $db->pquery($sql, $params);
            unset($params);
            $i++;
        }
        // save for both local and interstate
        $i = 1;
        while ($fieldList['qtyRateDescription'.$i] != null) {
            $description  = $fieldList['qtyRateDescription'.$i];
            $charge       = $fieldList['qtyRateCharge'.$i];
            $qty          = $fieldList['qtyRateQty'.$i];
            $discounted   = $fieldList['qtyRateDiscounted'.$i];
            $discount     = 0; //$fieldList['qtyRateDiscountPercent'.$i];
            $lineItemId   = $fieldList['qtyRateLineItemId'.$i];
            $enforced     = $fieldList['qtyRateEnforced'.$i];
            $fromContract = $fieldList['qtyRateFromContract'.$i];
            if (getenv('INSTANCE_NAME') == 'graebel') {
                $included = $fieldList['qtyChargeToBeRated'.$i];
            } else {
                $included = 'on';
            }
            if ($description == '') {
                $i++;
                continue;
            }
            $sql    = "SELECT * FROM `".$tablePrefix.
                      "vtiger_misc_accessorials` WHERE quoteid=? AND line_item_id=?";
            $result = $db->pquery($sql, [$quoteid, $lineItemId]);
            $row    = $result->fetchRow();
            $params = [];
            if ($row == null) {
                //Update line_item_id from vtiger_misc_accessorials_seq and increment id value in table
                $sql        = "UPDATE `".$tablePrefix."vtiger_misc_accessorials_seq` SET id=id+1";
                $result     = $db->pquery($sql, $params);
                $sql        = "SELECT id FROM `".$tablePrefix."vtiger_misc_accessorials_seq`";
                $result     = $db->pquery($sql, $params);
                $row        = $result->fetchRow();
                $lineItemId = $row[0];
                //Create new item
                $sql = "INSERT INTO `".$tablePrefix.
                       "vtiger_misc_accessorials` (quoteid, included, description, charge, qty, discounted, discount,
charge_type, line_item_id, enforced, from_contract) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            } else {
                //Update item
                if ($enforced) {
                    //skip updating an enforced item.
                    //$i++;
                    //continue;
                }
                $sql = "UPDATE `".$tablePrefix.
                       "vtiger_misc_accessorials` SET quoteid=?, included=?, description=?, charge=?, qty=?, discounted=?, discount=?, charge_type=? WHERE line_item_id=?";
            }
            $params[] = $quoteid;
            $params[] = \MoveCrm\InputUtils::CheckboxToBool($included);
            $params[] = $description;
            $params[] = $charge;
            $params[] = $qty;
            $params[] = \MoveCrm\InputUtils::CheckboxToBool($discounted);
            $params[] = $discount;
            $params[] = 'qty';
            $params[] = $lineItemId;
            $params[] = $enforced;
            $params[] = $fromContract;
            $result   = $db->pquery($sql, $params);
            unset($params);
            $i++;
        }


        if(!$pseudo) {
            if(\MoveCrm\InputUtils::CheckboxToBool($fieldList['is_primary'])) {
                $hasPrefix   = strpos($fieldList['potential_id'], 'x');
                $potentialid = $hasPrefix === false?$fieldList['potential_id']:substr(strstr($fieldList['potential_id'], 'x'), 1);
                $hasPrefix   = strpos($fieldList['orders_id'], 'x');
                $orderId     = $hasPrefix === false?$fieldList['orders_id']:substr(strstr($fieldList['orders_id'], 'x'), 1);
                $sql         = "UPDATE `vtiger_quotes`,`vtiger_crmentity` SET `is_primary`=0 WHERE
                                crmid=quoteid AND
                                ((potentialid=? AND potentialid<>0) OR (orders_id=? AND orders_id<>0))
                                AND quoteid!=?
                                AND setype=?";
                $params = [];
                $params[]    = $potentialid;
                $params[]    = $orderId;
                $params[]    = $quoteid;
                $params[]    = $module;
                $result      = $db->pquery($sql, $params);
                unset($params);
            }

            if($module == 'Estimates') {
                $sql       = "UPDATE `vtiger_orders` SET orders_etotal=? WHERE ordersid=?";
                $db->pquery($sql, [CurrencyField::convertToDBFormat($fieldList['hdnGrandTotal']), $orderId]);
            }
        }

        if (
            !$pseudo &&
            \MoveCrm\InputUtils::CheckboxToBool($fieldList['is_primary'])
        ) {
            if ($fieldList['quotestage'] === 'Accepted' || $fieldList['actuals_stage'] === 'Accepted') {
                // update related Orders with mileage
                $miles = $fieldList['interstate_mileage'];
                if ($miles > 0) {
                    $sql = 'UPDATE vtiger_orders SET orders_miles=? WHERE ordersid=?';
                    $db->pquery($sql, [$miles, $fieldList['orders_id']]);
                }
            }

            if (getenv('IGC_MOVEHQ')) {
                $miles = $fieldList['interstate_mileage'];
                if ($miles > 0) {
                    $sql = 'UPDATE vtiger_orders SET mileage=? WHERE ordersid=?';
                    $db->pquery($sql, [$miles, $fieldList['orders_id']]);
                }
            }
        }

        //this portion could be broken out into an aftersave in EstimatesHandler.
        //I did not do this because it would require a hotfix and I'm not positive on the vtlib steps to add
        //an aftersave handler, also I already put it here instead of the right place, so now it's convention.
        //if it's a syncEstimate and the flag is set to rate we need to rate it.
        $fieldList['effective_tariff'] = $fieldList['effective_tariff'] ?: $fieldList['local_tariff'];
        $hasLoadDate = true;
        $isNAT = $fieldList['shipper_type'] == 'NAT' || $fieldList['billing_type'] == 'NAT';
        $isUAS = strpos(TariffManager_Record_Model::getCustomTariffTypeById($fieldList['effective_tariff']), "UAS") !== false;
        if($fieldList['effective_tariff'] != $fieldList['local_tariff'] && !$isNAT && !$isUAS && empty($fieldList['pricing_level'])) {
            $hasLoadDate = $fieldList['load_date'] != '';
        }
        if ($fieldList['syncwebservice'] && $fieldList['syncrate'] && $hasLoadDate) {
            $temp = $fieldList['effective_tariff'];
            try {
                $this->rateEstimate($quoteid, $fieldList);
            }catch(Exception $e) {
                // This has to be here so syncwebservice will actually catch Exceptions, since a lot of them are not WebServiceExceptions.
                throw new WebServiceException(0, "The record has been synced, but was unable to rate.\nMessage: ".$e->getMessage());
            }
            $fieldList['effective_tariff'] = $temp;
        }

        if (getenv('INSTANCE_NAME') == 'sirva') {
            //AddressSegments save
            $AddressSegmentsModel = Vtiger_Module_Model::getInstance('AddressSegments');
            if ($AddressSegmentsModel && $AddressSegmentsModel->isActive()) {
                //one issue:  [module] => Leads
                $AddressSegmentsModel->saveAddressSegments($_REQUEST, $this->id);
            }
        }

        //$this->saveStops($fieldList, $quoteid, $tablePrefix);
        $gotoDocumentId = $fieldList['gotoDocuments'];
        //file_put_contents('logs/devLog.log', "\n GotoDocumentId : ".print_r($gotoDocumentId, true), FILE_APPEND);
        if ($gotoDocumentId) {
            //file_put_contents('logs/devLog.log', "\n Trying to go to the document", FILE_APPEND);
            $url = 'index.php?module=Documents&view=Detail&record='.$gotoDocumentId;
            header("Location: ".$url);
        }
    }

    //@TODO: this is where I was going to take in just the quoteid pull the info and do the rating.
    //I lost my trains though.
//    function syncRate($quoteid = false) {
//        if (!$quoteid) {
//            $quoteid = $this->id;
//        }
//        if (!$quoteid) {
//            return; //fail
//        }
//        $recordModel = Vtiger_Record_Model::getInstanceById($quoteid);
//        self::rateEstimate($quoteid,
//                           [
//                               'business_line_est' => $recordModel->get('business_line_est'),
//                               'local_tariff'      => '<tariffID>',          //local tariff
//                               'effective_tariff'  => '<interstateTariffId>' //interstate tariff
//                           ]
//    }

    /*
usage : $estimate->rateEstimate(
    $quoteid,
[
    'business_line_est' => 'Local Move',          //all others are else
    'local_tariff'      => '<tariffID>',          //local tariff
    'effective_tariff'  => '<interstateTariffId>' //interstate tariff
]
    );
    */

    function rateEstimate($quoteid, $fieldList) {
            $ratingObject = false;
            //do a rating, but because this is madness we need to know what to do.
            if($fieldList['effective_tariff']) {
                if (Estimates_Record_Model::isLocalTariff($fieldList['effective_tariff'])) {
                    //we do to the GetLocalRate for local
                    $ratingObject = new Estimates_GetLocalRate_Action;
                } else {
                    $ratingObject = new Estimates_GetDetailedRate_Action;
                }
            }
            //only rate if we have a rating object
            if ($ratingObject) {
                //create an array to pass to rating.
                $ratingStuff = [
                    'pseudoSave'       => 0,
                    'record'           => $quoteid,
                    'effective_tariff' => $fieldList['effective_tariff'],
                    'syncwebservice' => 1,
                    'syncrate' => 1,
                ];

                $vt_request  = new Vtiger_Request($ratingStuff);
                //capture the output buffer because the emit does an echo
                ob_start();
                //for reasons!
                require_once('libraries/MoveCrm/arrayBuilder.php');
                require_once('libraries/MoveCrm/xmlBuilder.php');
                $ratingObject->process($vt_request);
                $return = ob_get_contents();
                //$return is the json that rating returns. we could parse this for errors or just like do nothing.
                //stop output buffering.
                ob_end_clean();
            }
        //$this->saveStops($fieldList, $quoteid, $tablePrefix);
        $gotoDocumentId = $fieldList['gotoDocuments'];
        //file_put_contents('logs/devLog.log', "\n GotoDocumentId : ".print_r($gotoDocumentId, true), FILE_APPEND);
        if ($gotoDocumentId) {
            //file_put_contents('logs/devLog.log', "\n Trying to go to the document", FILE_APPEND);
            $url = 'index.php?module=Documents&view=Detail&record='.$gotoDocumentId;
            header("Location: ".$url);
        }
        if(getenv('INSTANCE_NAME') == 'sirva')
        {
            return $return;
        }
    }

    /**
     * Retrieve custom record information of the module
     *
     * @param <Integer> $record - crmid of record
     */
    public function retrieve($record)
    {
        global $adb;
        $fieldList = [];
        //Total field
        $sql    = "SELECT `total` FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $adb->pquery($sql, [$record]);
        if($adb->num_rows($result) > 0) {
            $fieldList['total'] = $result->fields['total'];
            //The real fieldname is hdnGrandTotal... what is looking for total?
            $fieldList['hdnGrandTotal'] = $fieldList['total'];
        }
        //Base plus
        $sql    = "SELECT * FROM `vtiger_quotes_baseplus` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Miles'.$row['serviceid']]  = $row['mileage'];
                $fieldList['Weight'.$row['serviceid']] = $row['weight'];
                $fieldList['Rate'.$row['serviceid']]   = $row['rate'];
                $fieldList['Excess'.$row['serviceid']] = $row['excess'];
            }
        }
        //Breakpoint
        $sql    = "SELECT * FROM `vtiger_quotes_breakpoint` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Miles'.$row['serviceid']]      = $row['mileage'];
                $fieldList['Rate'.$row['serviceid']]       = $row['rate'];
                $fieldList['Weight'.$row['serviceid']]     = $row['weight'];
                $fieldList['calcWeight'.$row['serviceid']] = $row['breakpoint'];
            }
        }
        //Quotes Bulky
        $sql    = "SELECT * FROM `vtiger_quotes_bulky` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                isset($fieldList['NumBulkys'.$row['serviceid']])?$fieldList['NumBulkys'.$row['serviceid']]++:$fieldList['NumBulkys'.$row['serviceid']] = 0;

                //Legacy set of values
                $fieldList['bulkyDescription'.$row['serviceid'].'_'.$fieldList['NumBulkys'.$row['serviceid']]] = $row['description'];
                $fieldList['Qty'.$row['serviceid'].'_'.$fieldList['NumBulkys'.$row['serviceid']]]              = $row['qty'];
                $fieldList['WeightAdd'.$row['serviceid'].'_'.$fieldList['NumBulkys'.$row['serviceid']]]        = $row['weight'];
                $fieldList['Rate'.$row['serviceid'].'_'.$fieldList['NumBulkys'.$row['serviceid']]]             = $row['rate'];
                $fieldList['BulkyID'.$row['serviceid'].'_'.$fieldList['NumBulkys'.$row['serviceid']]]          = $row['bulky_id'];
                $fieldList['BulkyCost'.$row['serviceid'].'_'.$fieldList['NumBulkys'.$row['serviceid']]]        = $row['cost_bulky_item'];

                //Correct set of values
                $fieldList['bulkyDescription'.$row['serviceid'].'-'.$fieldList['NumBulkys'.$row['serviceid']]] = $row['description'];
                $fieldList['Qty'.$row['serviceid'].'-'.$fieldList['NumBulkys'.$row['serviceid']]]              = $row['qty'];
                $fieldList['WeightAdd'.$row['serviceid'].'-'.$fieldList['NumBulkys'.$row['serviceid']]]        = $row['weight'];
                $fieldList['Rate'.$row['serviceid'].'-'.$fieldList['NumBulkys'.$row['serviceid']]]             = $row['rate'];
                $fieldList['BulkyID'.$row['serviceid'].'-'.$fieldList['NumBulkys'.$row['serviceid']]]          = $row['bulky_id'];
                $fieldList['BulkyCost'.$row['serviceid'].'-'.$fieldList['NumBulkys'.$row['serviceid']]]        = $row['cost_bulky_item'];
            }
        }
        //Quotes Country Charge
        $sql    = "SELECT * FROM `vtiger_quotes_countycharge` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['County'.$row['serviceid']] = $row['county'];
                $fieldList['Rate'.$row['serviceid']]   = $row['rate'];
            }
        }
        //Quotes Crating
        $sql    = "SELECT * FROM `vtiger_quotes_crating` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['crateID'.$row['serviceid'].'-'.$row['line_item_id']]       = $row['crateid'];
                $fieldList['Description'.$row['serviceid'].'-'.$row['line_item_id']]   = $row['description'];
                $fieldList['CratingQty'.$row['serviceid'].'-'.$row['line_item_id']]    = $row['crating_qty'];
                $fieldList['CratingRate'.$row['serviceid'].'-'.$row['line_item_id']]   = $row['crating_rate'];
                $fieldList['UncratingQty'.$row['serviceid'].'-'.$row['line_item_id']]  = $row['uncrating_qty'];
                $fieldList['UncratingRate'.$row['serviceid'].'-'.$row['line_item_id']] = $row['uncrating_rate'];
                $fieldList['CratingCost'.$row['serviceid'].'-'.$row['line_item_id']]   = $row['cost_crating'];
                $fieldList['UncratingCost'.$row['serviceid'].'-'.$row['line_item_id']] = $row['cost_uncrating'];
                $fieldList['Length'.$row['serviceid'].'-'.$row['line_item_id']]        = $row['length'];
                $fieldList['Width'.$row['serviceid'].'-'.$row['line_item_id']]         = $row['width'];
                $fieldList['Height'.$row['serviceid'].'-'.$row['line_item_id']]        = $row['height'];
                $fieldList['InchesAdded'.$row['serviceid'].'-'.$row['line_item_id']]   = $row['inches_added'];
            }
        }
        //Quotes CWT By Weight
        $sql    = "SELECT * FROM `vtiger_quotes_cwtbyweight` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Weight'.$row['serviceid']] = $row['weight'];
                $fieldList['Rate'.$row['serviceid']]   = $row['rate'];
            }
        }
        if(getenv('INSTANCE_NAME') == 'sirva') {
            //Quotes CWT By Quantity
        $sql    = "SELECT * FROM `vtiger_quotes_cwtperqty` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Quantity'.$row['serviceid']] = $row['quantity'];
		$fieldList['Weight'.$row['serviceid']]   = $row['weight'];
		$fieldList['Rate'.$row['serviceid']]     = $row['rate'];
                }
            }
        }
        //Quotes Hourly Set
        $sql    = "SELECT * FROM `vtiger_quotes_hourlyset` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Men'.$row['serviceid']]        = $row['men'];
                $fieldList['Vans'.$row['serviceid']]       = $row['vans'];
                $fieldList['Hours'.$row['serviceid']]      = $row['hours'];
                $fieldList['TravelTime'.$row['serviceid']] = $row['traveltime'];
                $fieldList['Rate'.$row['serviceid']]       = $row['rate'];
            }
        }
        // Hourly Simple
        $sql    = "SELECT * FROM `vtiger_quotes_perunit` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Rate'.$row['serviceid']]        = $row['rate'];
                $fieldList['Quantity'.$row['serviceid']]       = $row['qty1'];
                $fieldList['Hours'.$row['serviceid']]      = $row['qty2'];
            }
        }
        //Quotes Packing
        $sql    = "SELECT * FROM `vtiger_quotes_packing` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                isset($fieldList['NumPacking'.$row['serviceid']])?$fieldList['NumPacking'.$row['serviceid']]++:$fieldList['NumPacking'.$row['serviceid']] = 1;
                $fieldList['Name'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]          = $row['name'];
                $fieldList['containerQty'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]  = $row['container_qty'];
                $fieldList['containerRate'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]] = $row['container_rate'];
                $fieldList['packQty'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]       = $row['pack_qty'];
                $fieldList['packRate'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]      = $row['pack_rate'];
                $fieldList['unpackQty'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]     = $row['unpack_qty'];
                $fieldList['unpackRate'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]    = $row['unpack_qty'];
                $fieldList['PackID'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]        = $row['packing_id'];
                $fieldList['ContainerCost'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]] = $row['cost_container'];
                $fieldList['PackingCost'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]]   = $row['cost_packing'];
                $fieldList['UnpackingCost'.$row['serviceid'].'-'.$fieldList['NumPacking'.$row['serviceid']]] = $row['cost_unpacking'];
            }
        }
        //Quotes Per Unit
        $sql    = "SELECT * FROM `vtiger_quotes_perunit` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Rate'.$row['serviceid']] = $row['rate'];
                switch ($row['ratetype']) {
                    case 'Per Cu Ft':
                        $fieldList['CubicFeet'.$row['serviceid']] = $row['qty1'];
                        break;
                    case 'Per Cu Ft/Per Day':
                        $fieldList['CubicFeet'.$row['serviceid']] = $row['qty1'];
                        $fieldList['Days'.$row['serviceid']]      = $row['qty2'];
                        break;
                    case 'Per Cu Ft/Per Month':
                        $fieldList['CubicFeet'.$row['serviceid']] = $row['qty1'];
                        $fieldList['Months'.$row['serviceid']]    = $row['qty2'];
                        break;
                    case 'Per CWT':
                    case 'SIT First Day Rate':
                        $fieldList['Weight'.$row['serviceid']] = $row['qty1'];
                        break;
                    case 'Per CWT/Per Day':
                    case 'SIT Additional Day Rate':
                        $fieldList['Weight'.$row['serviceid']] = $row['qty1'];
                        $fieldList['Days'.$row['serviceid']]   = $row['qty2'];
                        break;
                    case 'Per CWT/Per Month':
                        $fieldList['Weight'.$row['serviceid']] = $row['qty1'];
                        $fieldList['Months'.$row['serviceid']] = $row['qty2'];
                        break;
                    case 'Per Quantity':
                        $fieldList['Quantity'.$row['serviceid']] = $row['qty1'];
                        break;
                    case 'Per Quantity/Per Day':
                        $fieldList['Quantity'.$row['serviceid']] = $row['qty1'];
                        $fieldList['Days'.$row['serviceid']]     = $row['qty2'];
                        break;
                    case 'Per Quantity/Per Month':
                        $fieldList['Quantity'.$row['serviceid']] = $row['qty1'];
                        $fieldList['Months'.$row['serviceid']]   = $row['qty2'];
                        break;
                    case 'Charge Per $100 (Valuation)':
                        $fieldList['ValuationType'.$row['serviceid']] = $row['flag'];
                        if($row['flag'] == 0) {
                            $fieldList['Amount'.$row['serviceid']] = $row['qty1'];
                            $fieldList['Deductible'.$row['serviceid']]   = $row['qty2'];
                            $fieldList['Multiplier'.$row['serviceid']]   = $row['multiplier'];
                        }else {
                            $fieldList['Coverage'.$row['serviceid']] = $row['rate'];
                        }
                        break;
                }
            }
        }
        //Quotes Section Discount
        $sql    = "SELECT * FROM `vtiger_quotes_sectiondiscount` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['SectionDiscount'.$row['serviceid']] = $row['discount_percent'];
            }
        }
        //Quotes Service Charge
        $sql    = "SELECT * FROM `vtiger_quotes_servicecharge` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['ServiceCharge'.$row['serviceid']] = $row['rate'];
            }
        }
        //Storage Valuation
        if (Vtiger_Utils::CheckTable('vtiger_quotes_storage_valution')) {
            $sql    = "SELECT * FROM `vtiger_quotes_storage_valution` WHERE `estimateid` =?";
            $result = $adb->pquery($sql, [$record]);
            if ($adb->num_rows($result) > 0) {
                while ($row =& $result->fetchRow()) {
                    $fieldList['StorageValuation'.$row['serviceid']] = $row['rate'];
                    $fieldList['Month'.$row['serviceid']]            = $row['months'];
                }
            }
        }
        //Quotes Service Cost
        $sql    = "SELECT * FROM `vtiger_quotes_servicecost` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['cost_service_total'.$row['serviceid']]   = $row['cost_service_total'];
                $fieldList['cost_container_total'.$row['serviceid']] = $row['cost_container_total'];
                $fieldList['cost_packing_total'.$row['serviceid']]   = $row['cost_packing_total'];
                $fieldList['cost_unpacking_total'.$row['serviceid']] = $row['cost_unpacking_total'];
                $fieldList['cost_crating_total'.$row['serviceid']]   = $row['cost_crating_total'];
                $fieldList['cost_uncrating_total'.$row['serviceid']] = $row['cost_uncrating_total'];
            }
        }
        //Quotes Valuation
        $sql    = "SELECT * FROM `vtiger_quotes_valuation` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['ValuationType'.$row['serviceid']] = $row['released'];
                $fieldList['Amount'.$row['serviceid']]        = $row['amount'];
                $fieldList['Coverage'.$row['serviceid']]      = $row['released_amount'];
                $fieldList['Deductible'.$row['serviceid']]    = $row['deductible'];
                $fieldList['Rate'.$row['serviceid']]          = $row['rate'];
            }
        }
        //Quotes Vehicles
        $sql    = "SELECT * FROM `vtiger_quotes_vehicles` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            $vehicleCount = 0;
            while ($row =& $result->fetchRow()) {
                $vehicleCount++;
                $fieldList['vehicleDescription'.$vehicleCount] = $row['vehicle_id'];
                $fieldList['vehicleID-'.$vehicleCount]         = $row['amount'];
                $fieldList['vehicleWeight-'.$vehicleCount]     = $row['weight'];

                $fieldList['vehicleMake-'.$vehicleCount]       = $row['make'];
                $fieldList['vehicleModel-'.$vehicleCount]      = $row['model'];
                $fieldList['vehicleYear-'.$vehicleCount]       = $row['year'];
            }
        }
        //Quotes Weight Mileage
        $sql    = "SELECT * FROM `vtiger_quotes_weightmileage` WHERE `estimateid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['Miles'.$row['serviceid']]  = $row['mileage'];
                $fieldList['Rate'.$row['serviceid']]   = $row['rate'];
                $fieldList['Weight'.$row['serviceid']] = $row['weight'];
            }
        }
        //Quotes Corporate Vehicles
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $sql    = "SELECT * FROM `vtiger_corporate_vehicles` WHERE `estimate_id` =?";
            $result = $adb->pquery($sql, [$record]);
            if ($adb->num_rows($result) > 0) {
                while ($row =& $result->fetchRow()) {
                    isset($fieldList['numCorporateVehicles'])?$fieldList['numCorporateVehicles']++:$fieldList['numCorporateVehicles'] = 1;
                    $fieldList['vehicle_make_'.$fieldList['numCorporateVehicles']]               = $row['make'];
                    $fieldList['vehicle_model_'.$fieldList['numCorporateVehicles']]              = $row['model'];
                    $fieldList['vehicle_year_'.$fieldList['numCorporateVehicles']]               = $row['year'];
                    $fieldList['vehicle_weight_'.$fieldList['numCorporateVehicles']]             = $row['weight'];
                    $fieldList['vehicle_cube_'.$fieldList['numCorporateVehicles']]               = $row['cube'];
                    $fieldList['vehicle_service_'.$fieldList['numCorporateVehicles']]            = $row['service'];
                    $fieldList['vehicle_dvp_value_'.$fieldList['numCorporateVehicles']]          = $row['dvp_value'];
                    $fieldList['vehicle_car_on_van_'.$fieldList['numCorporateVehicles']]         = $row['car_on_van'];
                    $fieldList['vehicle_oversize_class_'.$fieldList['numCorporateVehicles']]     = $row['oversize_class'];
                    $fieldList['vehicle_inoperable_'.$fieldList['numCorporateVehicles']]         = $row['inoperable'];
                    $fieldList['vehicle_length_'.$fieldList['numCorporateVehicles']]             = $row['length'];
                    $fieldList['vehicle_width_'.$fieldList['numCorporateVehicles']]              = $row['width'];
                    $fieldList['vehicle_height_'.$fieldList['numCorporateVehicles']]             = $row['height'];
                    $fieldList['vehicle_charge_'.$fieldList['numCorporateVehicles']]             = $row['charge'];
                    $fieldList['vehicle_shipping_count_'.$fieldList['numCorporateVehicles']]     = $row['shipping_count'];
                    $fieldList['vehicle_not_shipping_count_'.$fieldList['numCorporateVehicles']] = $row['not_shipping_count'];
                    $fieldList['vehicle_comment_'.$fieldList['numCorporateVehicles']]            = $row['comment'];
                }
            }
        }
        //Packing Items
        $sql    = "SELECT * FROM `vtiger_packing_items` WHERE `quoteid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['pack'.$row['itemid']]           = $row['pack_qty'];
                $fieldList['unpack'.$row['itemid']]         = $row['unpack_qty'];
                $fieldList['ot_pack'.$row['itemid']]        = $row['ot_pack_qty'];
                $fieldList['ot_unpack'.$row['itemid']]      = $row['ot_unpack_qty'];
                $fieldList['packCustomRate'.$row['itemid']] = $row['custom_rate'];
                $fieldList['packPackRate'.$row['itemid']] = $row['pack_rate'];
                if(getenv('INSTANCE_NAME') == 'graebel'){
                    $fieldList['containers'.$row['itemid']] = $row['containers'];
                }
            }
        }
        //Quotes Misc Accessorieals
        $sql    = "SELECT * FROM `vtiger_misc_accessorials` WHERE `quoteid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            $flat = 0;
            $qty  = 0;
            while ($row =& $result->fetchRow()) {
                if ($row['charge_type'] == 'flat') {
                    $flat++;
                    $fieldList['flatDescription'.$flat]     = $row['description'];
                    $fieldList['flatCharge'.$flat]          = $row['charge'];
                    $fieldList['flatDiscounted'.$flat]      = $row['discounted'];
                    $fieldList['flatDiscountPercent'.$flat] = $row['discount'];
                    $fieldList['flatLineItemId'.$flat]      = $row['line_item_id'];
                    $fieldList['flatEnforced'.$flat]        = $row['enforced'];
                    $fieldList['flatFromContract'.$flat]    = $row['from_contract'];
                    $fieldList['flatChargeToBeRated'.$flat] = $row['included'];
                } else {
                    $qty++;
                    $fieldList['qtyRateDescription'.$qty]  = $row['description'];
                    $fieldList['qtyRateCharge'.$qty]       = $row['charge'];
                    $fieldList['qtyRateQty'.$qty]          = $row['qty'];
                    $fieldList['qtyRateDiscounted'.$qty]   = $row['discounted'];
                    $fieldList['qtyRateLineItemId'.$qty]   = $row['line_item_id'];
                    $fieldList['qtyRateEnforced'.$qty]     = $row['enforced'];
                    $fieldList['qtyRateFromContract'.$qty] = $row['from_contract'];
                    $fieldList['qtyChargeToBeRated'.$flat] = $row['included'];
                }
            }
        }
        //Crates
        $sql    = "SELECT * FROM `vtiger_crates` WHERE `quoteid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            $fieldList['interstateNumCrates'] = 0;
            $crateNumber = 0;
            while ($row =& $result->fetchRow()) {
                $fieldList['interstateNumCrates']++;
                $crateNumber++;
                $fieldList['crateID'.$crateNumber]              = $row['crateid'];
                $fieldList['crateDescription'.$crateNumber]     = $row['description'];
                $fieldList['crateLength'.$crateNumber]          = $row['length'];
                $fieldList['crateWidth'.$crateNumber]           = $row['width'];
                $fieldList['crateHeight'.$crateNumber]          = $row['height'];
                $fieldList['cratePack'.$crateNumber]            = $row['pack'];
                $fieldList['crateUnpack'.$crateNumber]          = $row['unpack'];
                $fieldList['crateOTPack'.$crateNumber]          = $row['ot_pack'];
                $fieldList['crateOTUnpack'.$crateNumber]        = $row['ot_unpack'];
                $fieldList['crateDiscountPercent'.$crateNumber] = $row['discount'];
                $fieldList['crateApplyTariff'.$crateNumber]     = $row['apply_tariff']?:1;
                $fieldList['crateCustomRateAmount'.$crateNumber] = $row['custom_rate_amount'];
                $fieldList['crateLineItemId'.$crateNumber]      = $row['line_item_id'];
            }
        }
        //Bulky Items
        $sql    = "SELECT * FROM `vtiger_bulky_items` WHERE `quoteid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                $fieldList['bulky'.$row['bulkyid']] = $row['ship_qty'];
            }
        }
        //SIT Items
        //ensure this table exists.
        if (Vtiger_Utils::CheckTable('vtiger_quotes_sit')) {
            $sql    = "SELECT * FROM `vtiger_quotes_sit` WHERE `estimateid` =?";
            $result = $adb->pquery($sql, [$record]);
            if ($adb->num_rows($result) > 0) {
                while ($row =& $result->fetchRow()) {
                    $fieldList['Cartage'.$row['serviceid']]       = $row['cartage_cwt_rate'];
                    $fieldList['FirstDay'.$row['serviceid']]      = $row['first_day_rate'];
                    $fieldList['AdditionalDay'.$row['serviceid']] = $row['additional_day_rate'];
                }
            }
        }
        //Effective Tariff
        $sql    = "SELECT `effective_tariff` FROM `vtiger_quotes`
                    WHERE `quoteid` =?";
        $result = $adb->pquery($sql, [$record]);
        if ($adb->num_rows($result) > 0) {
            $row = $result->fetchRow();
            $fieldList['effective_tariff'] = $row['effective_tariff'];
        }

        //Service Charges
        if(getenv('INSTANCE_NAME') == 'sirva') {
            $sql    = "SELECT * FROM `vtiger_quotes_inter_servchg` WHERE quoteid=?";
            $result = $adb->pquery($sql, [$record]);
            if($adb->num_rows($result) > 0) {
                $chargeList = [];
                while($row =& $result->fetchRow()) {
                    //'[{"serviceid":"0","is_dest":"0","service_description":"","always_used":"0","charge":"0","minimum":"0","applied":"0"}]';
                    $chargeList[] = [
                        'serviceid'             => $row['serviceid'],
                        'is_dest'               => $row['is_dest'],
                        'service_description'   => $row['service_description'],
                        'always_used'           => $row['always_used'],
                        'charge'                => $row['charge'],
                        'minimum'               => $row['minimum'],
                        'service_weight'        => $row['service_weight'],
                        'applied'               => $row['applied']
                    ];
                }
            }
            $fieldList['compiledServiceCharges'] = $chargeList;
        }

        // Line Items
        $sql = 'SELECT * FROM `vtiger_detailed_lineitems` WHERE `dli_relcrmid`=?';
        $result = $adb->pquery($sql, [$record]);
        $i = 0;
        while ($row = $result->fetchRow()) {
            $i++;
            $fieldList['detaillineitemid'.$i] = $row['detaillineitemsid'];
            $fieldList['tariffitemnumber'.$i] = $row['dli_tariff_item_number'];
            $fieldList['tariffitem'.$i] = $row['dli_tariff_item_name'];
            $fieldList['tariffsection'.$i] = $row['dli_tariff_schedule_section'];
            $fieldList['section'.$i] = $row['dli_return_section_name'];
            $fieldList['description'.$i] = $row['dli_description'];
            $fieldList['role'.$i] = $row['dli_participant_role'];
            $fieldList['roleID'.$i] = $row['dli_participant_role_id'];
            $fieldList['baserate'.$i] = $row['dli_base_rate'];
            $fieldList['quantity'.$i] = $row['dli_quantity'];
            $fieldList['unitOfMeasurement'.$i] = $row['dli_unit_of_measurement'];
            $fieldList['unitrate'.$i] = $row['dli_unit_rate'];
            $fieldList['gross'.$i] = $row['dli_gross'];
            $fieldList['invoicediscountpct'.$i] = $row['dli_invoice_discount'];
            $fieldList['invoicecostnet'.$i] = $row['dli_invoice_net'];
            $fieldList['distributablediscountpct'.$i] = $row['dli_distribution_discount'];
            $fieldList['distributablecostnet'.$i] = $row['dli_distribution_net'];
            $fieldList['movepolicy'.$i] = $row['dli_tariff_move_policy'];
            $fieldList['approval'.$i] = $row['dli_approval'];
            $fieldList['invoiceable'.$i] = $row['dli_invoiceable'];
            $fieldList['distributable'.$i] = $row['dli_distributable'];
            $fieldList['invoicedone'.$i] = $row['dli_invoiced'];
            $fieldList['distributed'.$i] = $row['dli_distributed'];
            $fieldList['invoicenumber'.$i] = $row['dli_invoice_number'];
            $fieldList['invoice_phase'.$i] = $row['dli_phase'];
            $fieldList['invoice_event'.$i] = $row['dli_event'];
            $fieldList['invoice_sequence'.$i] = $row['dli_invoice_sequence'];
            $fieldList['distribution_sequence'.$i] = $row['dli_distribution_sequence'];
            $fieldList['ready_to_invoice'.$i] = $row['dli_ready_to_invoice'];
            $fieldList['ready_to_distribute'.$i] = $row['dli_ready_to_distribute'];
            $fieldList['preformed'.$i] = $row['dli_date_performed'];
            $fieldList['location'.$i] = $row['dli_location'];
            $fieldList['gcs_flag'.$i] = $row['dli_gcs_flag']?:'N';
            $fieldList['metro_flag'.$i] = $row['dli_metro_flag'];
            $fieldList['item_weight'.$i] = $row['dli_item_weight'];
            $fieldList['rate_net'.$i] = $row['dli_rate_net'];

            $sql = 'SELECT * from `dli_service_providers` WHERE dli_id=?';
            $res2 = $adb->pquery($sql, [$row['detaillineitemsid']]);
            $spCount = 0;
            while ($row2 = $res2->fetchRow()) {
                $spCount++;
                $fieldList['serviceProviderID'.$i.'_'.$spCount] = $row2['dli_service_providers_id'];
                $fieldList['serviceProvider'.$i.'_'.$spCount] = $row2['vendor_id'];
                $fieldList['serviceProviderSplit'.$i.'_'.$spCount] = $row2['split_amount'];
                $fieldList['serviceProviderMiles'.$i.'_'.$spCount] = $row2['split_miles'];
                $fieldList['serviceProviderWeight'.$i.'_'.$spCount] = $row2['split_weight'];
                $fieldList['serviceProviderPercent'.$i.'_'.$spCount] = $row2['split_percent'];
            }
        }
        if ($i > 0) {
            $fieldList['detailLineItemCount'] = $i;
        }

        if (getenv('INSTANCE_NAME') == 'graebel') {
            $sql    = "SELECT * FROM `vtiger_quotes_inter_servchg` WHERE quoteid=?";
            $result = $adb->pquery($sql, [$record]);
            if ($adb->num_rows($result) > 0) {
                $chargeList = [];
                while ($row =& $result->fetchRow()) {
                    //'[{"serviceid":"0","is_dest":"0","service_description":"","always_used":"0","charge":"0","minimum":"0","applied":"0"}]';
                    $chargeList[] = [
                        'serviceid'           => $row['serviceid'],
                        'is_dest'             => $row['is_dest'],
                        'service_description' => $row['service_description'],
                        'always_used'         => $row['always_used'],
                        'charge'              => $row['charge'],
                        'minimum'             => $row['minimum'],
                        'service_weight'      => $row['service_weight'],
                        'applied'             => $row['applied']
                    ];
                }
            }
            $fieldList['compiledServiceCharges'] = $chargeList;
        }

        if(getenv('GOOGLE_ADDRESS_MILES_CALCULATOR'))
        {
            $addresses = [];
            $miles = [];
            $time = [];
            $res = $adb->pquery('SELECT `address`, `miles`, `time` FROM vtiger_google_addresscalc WHERE quoteid=? ORDER BY vtiger_google_addresscalc_id ASC',
                                [$record]);
            while($row = $res->fetchRow())
            {
                if($row['address'] == '_Total_')
                {
                    $fieldList['googleCalcMilesTotal'] = $row['miles'];
                    $fieldList['googleCalcTimeTotal'] = $row['time'];
                    continue;
                }
                $addresses[] = $row['address'];
                $miles[] = $row['miles'];
                $time[] = $row['time'];
            }
            $fieldList['googleCalcAddress'] = $addresses;
            $fieldList['googleCalcMiles'] = $miles;
            $fieldList['googleCalcTime'] = $time;
        }

        return $fieldList;
    }

    function saveStops($fieldList, $record, $tablePrefix = '') {
        $db            = PearDatabase::getInstance();
        $totalStops    = $fieldList['numStops'];
        $requestRecord = $fieldList['record'];
        $pseudo        = $fieldList['pseudoSave'];
        for ($i = 0; $i <= $totalStops; $i++) {
            $description = $fieldList['stop_description_'.$i];
            $sequence    = $fieldList['stop_sequence_'.$i];
            if ($description && $sequence) {
                $id         = $fieldList['stop_id_'.$i];
                $weight     = $fieldList['stop_weight_'.$i];
                $isPrimary  = $fieldList['stop_isprimary_'.$i];
                $address1   = $fieldList['stop_address1_'.$i];
                $address2   = $fieldList['stop_address2_'.$i];
                $phone1     = $fieldList['stop_phone1_'.$i];
                $phone2     = $fieldList['stop_phone2_'.$i];
                $phoneType1 = $fieldList['stop_phonetype1_'.$i];
                $phoneType2 = $fieldList['stop_phonetype2_'.$i];
                $city       = $fieldList['stop_city_'.$i];
                $type       = $fieldList['stop_type_'.$i];
                $contact    = $fieldList['stop_contact_'.$i];
                $state      = $fieldList['stop_state_'.$i];
                $zip        = $fieldList['stop_zip_'.$i];
                $country    = $fieldList['stop_country_'.$i];
                $date       = $fieldList['stop_date_'.$i];
                $opp        = $fieldList['potential_id'];
                $order      = $fieldList['orders_id'];
                if (!$id || $id == 'none') {
                    $sql    = "SELECT id FROM `".$tablePrefix."vtiger_extrastops_seq`";
                    $result = $db->pquery($sql, []);
                    $row    = $result->fetchRow();
                    $id     = $row[0];
                    if (!$id) {
                        $id  = 1;
                        $sql = "INSERT INTO `".$tablePrefix."vtiger_extrastops_seq` (id) VALUES (2)";
                        $db->pquery($sql, []);
                    }
                    $sql = "UPDATE `".$tablePrefix."vtiger_extrastops_seq` SET id = ".($id + 1);
                    $db->pquery($sql, []);
                    $sql = "INSERT INTO `".
                           $tablePrefix.
                           "vtiger_extrastops` (stopid, stop_sequence, stop_description, stop_weight, stop_isprimary, stop_address1, stop_address2, stop_phone1, stop_phone2, stop_phonetype1, stop_phonetype2, stop_city, stop_state, stop_zip, stop_country, stop_date, stop_estimate, stop_type, stop_contact) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $db->pquery($sql,
                                [$id,
                                 $sequence,
                                 $description,
                                 $weight,
                                 $isPrimary,
                                 $address1,
                                 $address2,
                                 $phone1,
                                 $phone2,
                                 $phoneType1,
                                 $phoneType2,
                                 $city,
                                 $state,
                                 $zip,
                                 $country,
                                 $date,
                                 $record,
                                 $type,
                                 $contact]);
                } else {
                    $sql = "UPDATE `".
                           $tablePrefix.
                           "vtiger_extrastops` SET extrastops_sequence = ?, extrastops_description = ?, extrastops_weight = ?, extrastops_isprimary = ?, extrastops_address1 = ?, extrastops_address2 = ?, extrastops_phone1 = ?, extrastops_phone2 = ?, extrastops_phonetype1 = ?, extrastops_phonetype2 = ?, extrastops_city = ?, extrastops_state = ?, extrastops_zip = ?, extrastops_country = ?, extrastops_date = ?, extrastops_type = ?, extrastops_contact = ?, extrastops_estimate = ? WHERE extrastopsid = ? AND (extrastops_relcrmid = ? OR extrastops_relcrmid = ? OR extrastops_relcrmid = ?)";
                    $db->pquery($sql,
                                [$sequence,
                                 $description,
                                 $weight,
                                 $isPrimary,
                                 $address1,
                                 $address2,
                                 $phone1,
                                 $phone2,
                                 $phoneType1,
                                 $phoneType2,
                                 $city,
                                 $state,
                                 $zip,
                                 $country,
                                 $date,
                                 $type,
                                 $contact,
                                 $record,
                                 $id,
                                 $record,
                                 $opp,
                                 $order]
                                );
                }
            }
        }
    }

    /**    function used to get the list of sales orders which are related to the Quotes
     *
     * @param int $id - quote id
     *
     * @return array - return an array which will be returned from the function GetRelatedList
     */
    public function get_salesorder($id)
    {
        global $log, $singlepane_view;
        $log->debug("Entering get_salesorder(".$id.") method ...");
        require_once('modules/SalesOrder/SalesOrder.php');
        $focus  = new SalesOrder();
        $button = '';
        if ($singlepane_view == 'true') {
            $returnset = '&return_module=Quotes&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module=Quotes&return_action=CallRelatedList&return_id='.$id;
        }
        $userNameSql = getSqlForNameInDisplayFormat(['first_name' =>
                                                         'vtiger_users.first_name',
                                                     'last_name'  => 'vtiger_users.last_name'],
                                                    'Users');
        $query       = "select vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename
			, vtiger_account.accountname,case when (vtiger_users.user_name not like '') then
			$userNameSql else vtiger_groups.groupname end as user_name
		from vtiger_salesorder
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
		left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid
		left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid
		left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
        LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
        LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.start_period = vtiger_salesorder.salesorderid
		LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
		LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
		left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
		where vtiger_crmentity.deleted=0 and vtiger_salesorder.quoteid = ".$id;
        $log->debug("Exiting get_salesorder method ...");

        return GetRelatedList('Quotes', 'SalesOrder', $focus, $query, $button, $returnset);
    }

    /**    function used to get the list of activities which are related to the Quotes
     *
     * @param int $id - quote id
     *
     * @return array - return an array which will be returned from the function GetRelatedList
     */
    public function get_activities($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $log, $singlepane_view, $currentModule, $current_user;
        $log->debug("Entering get_activities(".$id.") method ...");
        $this_module    = $currentModule;
        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/Activity.php");
        $other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);
        $parenttab        = getParentTab();
        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }
        $button = '';
        $button .= '<input type="hidden" name="activity_mode">';
        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                if (getFieldVisibilityPermission('Calendar', $current_user->id, 'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".
                               getTranslatedString('LBL_NEW').
                               " ".
                               getTranslatedString('LBL_TODO', $related_module).
                               "' class='crmbutton small create'".
                               " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'".
                               " value='".
                               getTranslatedString('LBL_ADD_NEW').
                               " ".
                               getTranslatedString('LBL_TODO', $related_module).
                               "'>&nbsp;";
                }
            }
        }
        $userNameSql  = getSqlForNameInDisplayFormat(['first_name' =>
                                                          'vtiger_users.first_name',
                                                      'last_name'  => 'vtiger_users.last_name'],
                                                     'Users');
        $query        = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else
		vtiger_groups.groupname end as user_name, vtiger_contactdetails.contactid,
		vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_activity.*,
		vtiger_seactivityrel.crmid as parent_id,vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
		vtiger_crmentity.modifiedtime,vtiger_recurringevents.recurringtype
		from vtiger_activity
		inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=
		vtiger_activity.activityid
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
		left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid=
		vtiger_activity.activityid
		left join vtiger_contactdetails on vtiger_contactdetails.contactid =
		vtiger_cntactivityrel.contactid
		left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
		left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=
		vtiger_activity.activityid
		left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
		where vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted=0 and
			activitytype='Task' and (vtiger_activity.status is not NULL and
			vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL and
			vtiger_activity.status != 'Deferred')";
        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
        if ($return_value == null) {
            $return_value = [];
        }
        $return_value['CUSTOM_BUTTON'] = $button;
        $log->debug("Exiting get_activities method ...");

        return $return_value;
    }

    /**    function used to get the the activity history related to the quote
     *
     * @param int $id - quote id
     *
     * @return array - return an array which will be returned from the function GetHistory
     */
    public function get_history($id)
    {
        global $log;
        $log->debug("Entering get_history(".$id.") method ...");
        $userNameSql = getSqlForNameInDisplayFormat(['first_name' =>
                                                         'vtiger_users.first_name',
                                                     'last_name'  => 'vtiger_users.last_name'],
                                                    'Users');
        $query       = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
			vtiger_activity.eventstatus, vtiger_activity.activitytype,vtiger_activity.date_start,
			vtiger_activity.due_date,vtiger_activity.time_start, vtiger_activity.time_end,
			vtiger_contactdetails.contactid,
			vtiger_contactdetails.firstname,vtiger_contactdetails.lastname, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.createdtime, vtiger_crmentity.description, case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid
                                left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where vtiger_activity.activitytype='Task'
  				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred')
	 	        	and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
        //Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
        $log->debug("Exiting get_history method ...");

        return getHistory('Quotes', $query, $id);
    }

    /**    Function used to get the Quote Stage history of the Quotes
     *
     * @param $id - quote id
     *
     * @return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values
     *                      and all column values of all entries
     */
    public function get_quotestagehistory($id)
    {
        global $log;
        $log->debug("Entering get_quotestagehistory(".$id.") method ...");
        global $adb;
        global $mod_strings;
        global $app_strings;
        $query    =
            'SELECT vtiger_quotestagehistory.*, vtiger_quotes.quote_no FROM vtiger_quotestagehistory INNER JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_quotestagehistory.quoteid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_quotes.quoteid WHERE vtiger_crmentity.deleted = 0 AND vtiger_quotes.quoteid = ?';
        $result   = $adb->pquery($query, [$id]);
        $noofrows = $adb->num_rows($result);
        $header[] = $app_strings['Quote No'];
        $header[] = $app_strings['LBL_ACCOUNT_NAME'];
        $header[] = $app_strings['LBL_AMOUNT'];
        $header[] = $app_strings['Quote Stage'];
        $header[] = $app_strings['LBL_LAST_MODIFIED'];
        //Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
        //Account Name , Total are mandatory fields. So no need to do security check to these fields.
        global $current_user;
        //If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
        $quotestage_access = (getFieldVisibilityPermission('Quotes', $current_user->id, 'quotestage') != '0')?1:0;
        $picklistarray     = getAccessPickListValues('Quotes');
        $quotestage_array  = ($quotestage_access != 1)?$picklistarray['quotestage']:[];
        //- ==> picklist field is not permitted in profile
        //Not Accessible - picklist is permitted in profile but picklist value is not permitted
        $error_msg = ($quotestage_access != 1)?'Not Accessible':'-';
        while ($row = $adb->fetch_array($result)) {
            $entries = [];
            // Module Sequence Numbering
            //$entries[] = $row['quoteid'];
            $entries[] = $row['quote_no'];
            // END
            $entries[]      = $row['accountname'];
            $entries[]      = $row['total'];
            $entries[]      = (in_array($row['quotestage'], $quotestage_array))?$row['quotestage']:$error_msg;
            $date           = new DateTimeField($row['lastmodified']);
            $entries[]      = $date->getDisplayDateTimeValue();
            $entries_list[] = $entries;
        }
        $return_data = ['header' => $header, 'entries' => $entries_list];
        $log->debug("Exiting get_quotestagehistory method ...");

        return $return_data;
    }

    // Function to get column name - Overriding function of base class
    public function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype = '')
    {
        if ($columname == 'potentialid' || $columname == 'contactid') {
            if ($fldvalue == '') {
                return null;
            }
        }

        return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
    }

    /*
     * Function to get the secondary query part of a report
     * @param - $module primary module name
     * @param - $secmodule secondary module name
     * returns the query string formed on fetching the related data for report for secondary module
     */
    public function generateReportsSecQuery($module, $secmodule, $queryPlanner)
    {
        $matrix = $queryPlanner->newDependencyMatrix();
        $matrix->setDependency('vtiger_crmentityQuotes', ['vtiger_usersQuotes', 'vtiger_groupsQuotes', 'vtiger_lastModifiedByQuotes']);
        $matrix->setDependency('vtiger_inventoryproductrelQuotes', ['vtiger_productsQuotes', 'vtiger_serviceQuotes']);
        $matrix->setDependency('vtiger_quotes',
                               ['vtiger_crmentityQuotes',
                                "vtiger_currency_info$secmodule",
                                'vtiger_quotescf',
                                'vtiger_potentialRelQuotes',
                                'vtiger_quotesbillads',
                                'vtiger_quotesshipads',
                                'vtiger_inventoryproductrelQuotes',
                                'vtiger_contactdetailsQuotes',
                                'vtiger_accountQuotes',
                                'vtiger_invoice_recurring_info',
                                'vtiger_quotesQuotes',
                                'vtiger_usersRel1']);
        if (!$queryPlanner->requireTable('vtiger_quotes', $matrix)) {
            return '';
        }
        $query = $this->getRelationQuery($module, $secmodule, "vtiger_quotes", "quoteid", $queryPlanner);
        if ($queryPlanner->requireTable("vtiger_crmentityQuotes", $matrix)) {
            $query .= " left join vtiger_crmentity as vtiger_crmentityQuotes on vtiger_crmentityQuotes.crmid=vtiger_quotes.quoteid and vtiger_crmentityQuotes.deleted=0";
        }
        if ($queryPlanner->requireTable("vtiger_quotescf")) {
            $query .= " left join vtiger_quotescf on vtiger_quotes.quoteid = vtiger_quotescf.quoteid";
        }
        if ($queryPlanner->requireTable("vtiger_quotesbillads")) {
            $query .= " left join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid";
        }
        if ($queryPlanner->requireTable("vtiger_quotesshipads")) {
            $query .= " left join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid";
        }
        if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")) {
            $query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_quotes.currency_id";
        }
        if ($queryPlanner->requireTable("vtiger_inventoryproductrelQuotes", $matrix)) {
            $query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelQuotes on vtiger_quotes.quoteid = vtiger_inventoryproductrelQuotes.id";
            // To Eliminate duplicates in reports
            if (($module == 'Products' || $module == 'Services') && $secmodule == "Quotes") {
                if ($module == 'Products') {
                    $query .= " and vtiger_inventoryproductrelQuotes.productid = vtiger_products.productid ";
                } elseif ($module == 'Services') {
                    $query .= " and vtiger_inventoryproductrelQuotes.productid = vtiger_service.serviceid ";
                }
            }
        }
        if ($queryPlanner->requireTable("vtiger_productsQuotes")) {
            $query .= " left join vtiger_products as vtiger_productsQuotes on vtiger_productsQuotes.productid = vtiger_inventoryproductrelQuotes.productid";
        }
        if ($queryPlanner->requireTable("vtiger_serviceQuotes")) {
            $query .= " left join vtiger_service as vtiger_serviceQuotes on vtiger_serviceQuotes.serviceid = vtiger_inventoryproductrelQuotes.productid";
        }
        if ($queryPlanner->requireTable("vtiger_groupsQuotes")) {
            $query .= " left join vtiger_groups as vtiger_groupsQuotes on vtiger_groupsQuotes.groupid = vtiger_crmentityQuotes.smownerid";
        }
        if ($queryPlanner->requireTable("vtiger_usersQuotes")) {
            $query .= " left join vtiger_users as vtiger_usersQuotes on vtiger_usersQuotes.id = vtiger_crmentityQuotes.smownerid";
        }
        if ($queryPlanner->requireTable("vtiger_usersRel1")) {
            $query .= " left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager";
        }
        if ($queryPlanner->requireTable("vtiger_potentialRelQuotes")) {
            $query .= " left join vtiger_potential as vtiger_potentialRelQuotes on vtiger_potentialRelQuotes.potentialid = vtiger_quotes.potentialid";
        }
        if ($queryPlanner->requireTable("vtiger_contactdetailsQuotes")) {
            $query .= " left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid";
        }
        if ($queryPlanner->requireTable("vtiger_accountQuotes")) {
            $query .= " left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid";
        }
        if ($queryPlanner->requireTable("vtiger_lastModifiedByQuotes")) {
            $query .= " left join vtiger_users as vtiger_lastModifiedByQuotes on vtiger_lastModifiedByQuotes.id = vtiger_crmentityQuotes.modifiedby ";
        }
        if ($queryPlanner->requireTable("vtiger_createdbyQuotes")) {
            $query .= " left join vtiger_users as vtiger_createdbyQuotes on vtiger_createdbyQuotes.id = vtiger_crmentityQuotes.smcreatorid ";
        }

        return $query;
    }

    /*
     * Function to get the relation tables for related modules
     * @param - $secmodule secondary module name
     * returns the array with table names and fieldnames storing relations between module and this module
     */
    public function setRelationTables($secmodule)
    {
        $rel_tables = [
            "SalesOrder" => ["vtiger_salesorder" => ["quoteid", "salesorderid"], "vtiger_quotes" => "quoteid"],
            "Calendar"   => ["vtiger_seactivityrel" => ["crmid", "activityid"], "vtiger_quotes" => "quoteid"],
            "Documents"  => ["vtiger_senotesrel" => ["crmid", "notesid"], "vtiger_quotes" => "quoteid"],
            "Accounts"   => ["vtiger_quotes" => ["quoteid", "accountid"]],
            "Contacts"   => ["vtiger_quotes" => ["quoteid", "contactid"]],
            "Potentials" => ["vtiger_quotes" => ["quoteid", "potentialid"]],
        ];

        return $rel_tables[$secmodule];
    }

    // Function to unlink an entity with given Id from another entity
    public function unlinkRelationship($id, $return_module, $return_id)
    {
        global $log;
        if (empty($return_module) || empty($return_id)) {
            return;
        }
        if ($return_module == 'Accounts') {
            $this->trash('Quotes', $id);
        } elseif ($return_module == 'Potentials' || $return_module == 'Opportunities') {
            $relation_query = 'UPDATE vtiger_quotes SET potentialid=? WHERE quoteid=?';
            $this->db->pquery($relation_query, [null, $id]);
        } elseif ($return_module == 'Contacts') {
            $relation_query = 'UPDATE vtiger_quotes SET contactid=? WHERE quoteid=?';
            $this->db->pquery($relation_query, [null, $id]);
        } else {
            $sql    = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
            $params = [$id, $return_module, $return_id, $id, $return_module, $return_id];
            $this->db->pquery($sql, $params);
        }
    }

    public function insertIntoEntityTable($table_name, $module, $fileid = '', $pseudo = false)
    {
        //Ignore relation table insertions while saving of the record
        if ($table_name == 'vtiger_inventoryproductrel') {
            return;
        }
        parent::insertIntoEntityTable($table_name, $module, $fileid, $pseudo);
    }

//    /*Function to create records in current module.
//    **This function called while importing records to this module*/
//    function createRecords($obj) {
//        $createRecords = self::createRecords($obj);
//
//        return $createRecords;
//    }
//
//    /*Function returns the record information which means whether the record is imported or not
//    **This function called while importing records to this module*/
//    function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
//        $entityInfo = self::importRecord($obj, $inventoryFieldData, $lineItemDetails);
//
//        return $entityInfo;
//    }
//
//    /*Function to return the status count of imported records in current module.
//    **This function called while importing records to this module*/
//    function getImportStatusCount($obj) {
//        $statusCount = self::getImportStatusCount($obj);
//
//        return $statusCount;
//    }
//
//    function undoLastImport($obj, $user) {
//        $undoLastImport = self::undoLastImport($obj, $user);
//    }

    /** Function to export the lead records in CSV Format
     *
     * @param reference variable - where condition is passed when the query is executed
     *                  Returns Export Quotes Query.
     */
    public function create_export_query($where)
    {
        global $log;
        global $current_user;
        $log->debug("Entering create_export_query(".$where.") method ...");
        include("include/utils/ExportUtils.php");
        //To get the Permitted fields query and the permitted fields list
        $sql         = getPermittedFieldsQuery("Quotes", "detail_view");
        $fields_list = getFieldsListFromQuery($sql);
        $fields_list .= getInventoryFieldsForExport($this->table_name);
        $userNameSql = getSqlForNameInDisplayFormat(['first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'], 'Users');
        $query       = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_crmentity.crmid
				LEFT JOIN vtiger_quotescf ON vtiger_quotescf.quoteid = vtiger_quotes.quoteid
				LEFT JOIN vtiger_quotesbillads ON vtiger_quotesbillads.quotebilladdressid = vtiger_quotes.quoteid
				LEFT JOIN vtiger_quotesshipads ON vtiger_quotesshipads.quoteshipaddressid = vtiger_quotes.quoteid
				LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_quotes.quoteid
				LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_quotes.contactid
				LEFT JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_quotes.potentialid
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_quotes.accountid
				LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_quotes.currency_id
				LEFT JOIN vtiger_users AS vtiger_inventoryManager ON vtiger_inventoryManager.id = vtiger_quotes.inventorymanager
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= $this->getNonAdminAccessControlQuery('Quotes', $current_user);
        $where_auto = " vtiger_crmentity.deleted=0";
        if ($where != "") {
            $query .= " where ($where) AND ".$where_auto;
        } else {
            $query .= " where ".$where_auto;
        }
        $log->debug("Exiting create_export_query method ...");

        return $query;
    }

    /** Business logic behind primary estimates - Once the Addendum estimate is designated as primary and accepted,
     * the old estimate stage should be flipped to Non-Current and the old estimate is still locked.
     *
     * @param $request
     */
    public function setPreviousPrimarySettings($request)
    {
        $db = PearDatabase::getInstance();
        // Get all estimates associated with the potentialid that is marked as primary
        $sql    = 'SELECT quoteid FROM `vtiger_quotes` WHERE (potentialid = ? OR orders_id=?) AND is_primary = ? AND estimate_type != ?';
        $result = $db->pquery($sql, [$request['potential_id'], $request['orders_id'], 1, 'Accepted']);
        $row    = $result->fetchRow();
        if ($row) {
            // Set the old primary estimate to non-current
            $sql = 'UPDATE `vtiger_quotes` SET quotestage = ? WHERE quoteid = ? AND is_primary = ?';
            $db->pquery($sql, ['Non-Current', $row['quoteid'], 1]);
        }
    }

    public function FormatDate($inputDate, $current_user_date_format)
    {
        $rv = null;
        if ($inputDate) {
            switch ($current_user_date_format) {
                case 'mm-dd-yyyy':
                    $carbon_format = 'm-d-Y';
                    break;
                case 'dd-mm-yyyy':
                    $carbon_format = 'd-m-Y';
                    break;
                case 'yyyy-mm-dd':
                    $carbon_format = 'Y-m-d';
                    break;
                default:
                    $carbon_format = null;
            }
            if ($carbon_format) {
                try {
                    $rv = Carbon::createFromFormat($carbon_format, $inputDate)->format('Y-m-d');
                } catch (Exception $ex) {
                    //One should always catch your errors
                }
            }
        }

        return $rv;
    }

    public static function saveDetailedLineItem($fieldList, $quoteid, $checkSaveOnly = false)
    {
        $db                  =& PearDatabase::getInstance();
        $detailLineItemCount = $fieldList['detailLineItemCount'];

        if (!$detailLineItemCount) {
            return false;
        }

        // enforce security for invoiced/distributed flags
        $user             = Users_Record_Model::getCurrentUserModel();
        if (getenv('INSTANCE_NAME') == 'graebel') {
            // the current user is not GVL Integration, do not update existing line item invoiced/distributed
            // and set new line item invoiced/distributed to false
            $updateFlags = false;
            $userId = $user->getId();
            // GVL Integration
            if ($db->pquery('SELECT * FROM `gvl_integration_users` WHERE userid=?', [$userId])->fetchRow()) {
                $updateFlags = true;
            }
        } else {
            $updateFlags = true;
        }

        $lineItemServiceProviders = [];
        $len = strlen('serviceProvider');
        foreach ($fieldList as $fieldName => $fieldValue) {
            if (substr($fieldName, 0, $len) !== 'serviceProvider') {
                continue;
            }

            preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
            $valueInfo = explode('_', substr($fieldName, $m[0][1]));
            $fieldInfo = substr($fieldName, 0, $m[0][1]);
            $lineItemServiceProviders[$valueInfo[0]][$valueInfo[1]][$fieldInfo] = $fieldValue;
        }

        $idList = [];
        $date_var         = date("Y-m-d H:i:s");
        $assigned_user_id = $fieldList['assigned_user_id'];
        $agentid          = $fieldList['agentid'];
        //We still don't have these?  I swear to god.
        if (!$assigned_user_id) {
            //make it this user
            $assigned_user_id = $user->getId();
        }
        if (!$agentid) {
            //make it their base agency... why does this record need an agency?
            $assigned_user_id = $user->getAccessibleAgentsForUser()[0];
        }

        $usedDistributionSequence = [];

        for ($i = 0; $i <= $detailLineItemCount; $i++) {
            unset($lastID);

			//Check if the line item should be deleted.
			if (self::deleteLineItem($fieldList, $i)) {
				//to delete just don't insert or update it.
                continue;
            }

			//expand this out for readability and add in so we don't actually delete existing line items this way.
            if (
                !$fieldList['detaillineitemid'.$i] &&
                !$fieldList['tariffitem'.$i] &&
                !$fieldList['section'.$i] &&
                !$fieldList['gross'.$i]
			) {
                continue;
            }

            if ($fieldList['deleted'.$i]) {
                continue;
            }

            $element = [
                'dli_tariff_item_number'      => $fieldList['tariffitemnumber'.$i], //not tabled by rating to return
                'dli_tariff_item_name'        => $fieldList['tariffitem'.$i], //not tabled by rating to return
                'dli_tariff_schedule_section' => $fieldList['tariffsection'.$i],
                'dli_return_section_name'     => $fieldList['section'.$i],
                'dli_description'             => $fieldList['description'.$i],
                //'dli_provider_role'           => $fieldList['role' . $i],
                'dli_participant_role'        => $fieldList['role'.$i],
                'dli_participant_role_id'     => $fieldList['roleID'.$i],
                'dli_base_rate'               => $fieldList['baserate'.$i]?CurrencyField::convertToDBFormat($fieldList['baserate'.$i], $user, true):'',
                'dli_quantity'                => $fieldList['quantity'.$i]? :'',
                'dli_unit_of_measurement'     => $fieldList['unitOfMeasurement'.$i],
                'dli_unit_rate'               => $fieldList['unitrate'.$i]?CurrencyField::convertToDBFormat($fieldList['unitrate'.$i], $user, true, 4):'',
                'dli_gross'                   => $fieldList['gross'.$i]?CurrencyField::convertToDBFormat($fieldList['gross'.$i], $user, true):'',
                'dli_invoice_discount'        => $fieldList['invoicediscountpct'.$i],
                'dli_invoice_net'             => $fieldList['invoicecostnet'.$i]?CurrencyField::convertToDBFormat($fieldList['invoicecostnet'.$i], $user, true):'', //consider defaulting.
                'dli_distribution_discount'   => $fieldList['distributablediscountpct'.$i],
                'dli_distribution_net'        => $fieldList['distributablecostnet'.$i]?CurrencyField::convertToDBFormat($fieldList['distributablecostnet'.$i], $user, true):'', //consider defaulting.
                'dli_tariff_move_policy'      => $fieldList['movepolicy'.$i],
                'dli_approval'                => $fieldList['approval'.$i],
                //'dli_service_provider'        => $fieldList['serviceprovider'.$i]? :'',
                'dli_invoiceable'             => $fieldList['invoiceable'.$i],
                'dli_distributable'           => $fieldList['distributable'.$i],
                'dli_invoiced'                => $fieldList['invoicedone'.$i],
                'dli_distributed'             => $fieldList['distributed'.$i],
                'dli_invoice_number'          => $fieldList['invoicenumber'.$i],
                'dli_phase'                   => $fieldList['invoice_phase'.$i],
                'dli_event'                   => $fieldList['invoice_event'.$i],
                'dli_invoice_sequence'        => $fieldList['invoice_sequence'.$i],
                'dli_distribution_sequence'   => $fieldList['distribution_sequence'.$i],
                'dli_ready_to_invoice'        => $fieldList['ready_to_invoice'.$i]? :'0',
                'dli_ready_to_distribute'     => $fieldList['ready_to_distribute'.$i]? :'0',
                'dli_location'                => $fieldList['location'.$i],
                'dli_gcs_flag'                => $fieldList['gcs_flag'.$i],
                'dli_metro_flag'              => $fieldList['metro_flag'.$i],
                'dli_item_weight'             => $fieldList['item_weight'.$i],
                'dli_rate_net'                => $fieldList['rate_net'.$i],
                'dli_relcrmid'                => $quoteid,
                'dli_date_performed'          => $fieldList['preformed'.$i]?DateTimeField::convertToDBFormat($fieldList['preformed'.$i]):null,
                //normally built in the crmentity table function
                'assigned_user_id'            => $assigned_user_id,
                'agentid'                     => $agentid,
                'smownerid'                   => $assigned_user_id,
                'modifiedby'                  => $user->id,
                'modifiedtime'                => $db->formatDate($date_var, true),
            ];

            if(!$updateFlags && $element['dli_ready_to_distribute'])
            {
                // OT 3793 - validation for setting ready to distribute
                // quick check for easy stuff
                //                Distribution Amount
                //                Date the Service was performed
                //                Gross amount for invoice (if applicable)
                //                invoice Discount (if Applicaple)
                //                Invoice net (if Applicable)
                //                Discount gross
                //                Distribution discount
                //                Distribution Amount.
                if($element['dli_invoiceable'])
                {
                    if(!isset($element['dli_invoice_discount'])
                        || $element['dli_invoice_net'] === '')
                    {
                        if($checkSaveOnly)
                        {
                            return 'Invoice Net must be set';
                        }
                        $element['dli_ready_to_distribute'] = '0';
                    }
                }
                if($element['dli_gross'] === '')
                {
                    if($checkSaveOnly)
                    {
                        return 'Gross must be set';
                    }
                    $element['dli_ready_to_distribute'] = '0';
                }
                if(!isset($element['dli_distribution_discount'])
                   || $element['dli_distribution_net'] === '')
                {
                    if($checkSaveOnly)
                    {
                        return 'Distribution Net must be set';
                    }
                    $element['dli_ready_to_distribute'] = '0';
                }
                if($element['dli_date_performed'] === '')
                {
                    if($checkSaveOnly)
                    {
                        return 'Date Performed must be set';
                    }
                    $element['dli_ready_to_distribute'] = '0';
                }
                if(in_array($element['dli_distribution_sequence'], $usedDistributionSequence))
                {
                    if($checkSaveOnly)
                    {
                        return 'Distribution sequence ('.$element['dli_distribution_sequence'].') must be unique';
                    }
                    $element['dli_ready_to_distribute'] = '0';
                }
                $usedDistributionSequence[] = $element['dli_distribution_sequence'];
            }

            if($checkSaveOnly)
            {
                continue;
            }

            try {
                $params     = [];
                $insertNew = true;
                if ($fieldList['detaillineitemid'.$i]) {
                    $checkSql = 'SELECT `detaillineitemsid` FROM `vtiger_detailed_lineitems` WHERE `detaillineitemsid` = ?';
                    $result   = $db->pquery($checkSql, [$fieldList['detaillineitemid'.$i]]);
                    if ($row = $result->fetchRow()) {
                        $lastID = $fieldList['detaillineitemid'.$i];
                        $idList[]  = $lastID;
                        $insertNew = false;
                        if (!$updateFlags) {
                            $element['dli_invoiced'] = $row['dli_invoiced'];
                            $element['dli_distributed'] = $row['dli_distributed'];
                            if($element['dli_invoiced'] || $element['dli_distributed'])
                            {
                                // if we're not GVL integration, don't update this line item at all if it's invoiced or distributed
                                // this will also skip the service provider update
                                continue;
                            }
                        }
                        $tabList    = '';
                        $updateList = '';
                        foreach ($element as $key => $value) {
                            if ($_REQUEST['view'] == 'Edit') {
                            $tabList .= ($tabList?',':'').' `'.$key.'`';
                            $updateList .= ($updateList?',':'').' `'.$key.'` = ?';
                                if (isset($value)) {
                                $params[] = $value;
                            } else {
                                    $params[] = null;
                                }
                            } else {
                                if (isset($value)) {
                                    $tabList .= ($tabList?',':'').' `'.$key.'`';
                                    $updateList .= ($updateList?',':'').' `'.$key.'` = ?';
                                    $params[] = $value;
                                } else {
                                    //$params[] = NULL;
                                }
                            }
                        }
                        $updateSql = 'UPDATE `vtiger_detailed_lineitems` SET '.$updateList.' WHERE `detaillineitemsid` = ?';
                        $params[]  = $fieldList['detaillineitemid'.$i];
                        $db->pquery($updateSql, $params);
                    }
                }
                if ($insertNew) {
                    if (!$updateFlags) {
                        $element['dli_invoiced'] = '0';
                        $element['dli_distributed'] = '0';
                    }
                    $tabList    = '';
                    $updateList = '';
                    foreach ($element as $key => $value) {
                        $tabList .= ($tabList?',':'').' `'.$key.'`';
                        $updateList .= ($updateList?',':'').' `'.$key.'` = ?';
                        if ($value) {
                            $params[] = $value;
                        } else {
                            $params[] = null;
                        }
                    }
                    $tabList .= ($tabList?',':'').' `createdtime`';
                    $params[] = $db->formatDate($date_var, true);
                    $new_sql  = 'INSERT INTO `vtiger_detailed_lineitems` ('.$tabList.') VALUES ('.generateQuestionMarks($params).')';
                    $db->pquery($new_sql, $params);
                    $lastID = $db->getLastInsertID();
                    $idList[] = $lastID;
                }
            } catch (Exception $e) {
                file_put_contents('logs/devLog.log', "\n Save Exception saving detailed line items! : ".$e->getMessage()."\n line".$e->getLine()."\n", FILE_APPEND);
            }

            foreach ($lineItemServiceProviders[$i] as $serviceInfo) {
                $dli_service_provider_id = $serviceInfo['serviceProviderID'];
                $dli_vendor_id = $serviceInfo['serviceProvider'];
                $split = $serviceInfo['serviceProviderSplit'];
                $splitMiles = $serviceInfo['serviceProviderMiles'];
                $splitWeight = $serviceInfo['serviceProviderWeight'];
                $splitPercent = $serviceInfo['serviceProviderPercent'];
                $spDeleted = $serviceInfo['serviceProviderDeleted'];
                $sql = 'SELECT dli_service_providers_id FROM `dli_service_providers` WHERE dli_service_providers_id = ?';
                if ($dli_service_provider_id && $db->pquery($sql, [$dli_service_provider_id])->fetchRow()) {
                    if ($spDeleted == 'yes') {
                            $sql = 'DELETE FROM `dli_service_providers` WHERE dli_service_providers_id=?';
                            $db->pquery($sql, [$dli_service_provider_id]);
                    } else {
                        $sql = 'UPDATE `dli_service_providers` SET vendor_id=?, split_amount=?, dli_id=?, split_miles=?, split_weight=?, split_percent=? WHERE dli_service_providers_id = ?';
                        $db->pquery($sql, [$dli_vendor_id, $split, $lastID, $splitMiles, $splitWeight, $splitPercent, $dli_service_provider_id]);
                    }
                } elseif ($spDeleted != 'yes' && $lastID) {
                    $sql = 'INSERT INTO `dli_service_providers` (dli_id, vendor_id, split_amount, split_miles, split_weight, split_percent) VALUES (?,?,?,?,?,?)';
                    $db->pquery($sql, [$lastID, $dli_vendor_id, $split, $splitMiles, $splitWeight, $splitPercent, ]);
                }
            }
        }

        if($checkSaveOnly)
        {
            return false;
        }

        if (count($idList) == 0) {
            $idList = [-1];
        }
            //Delete all line items that might exist for this crmid but weren't inserted/updated.
            //maybe want to just delete all at the top and then readd new.
            // this a dumb way to do all this, but oh well
            // don't delete things that are invoiced/distributed or linked to that
                $removeStmt = 'DELETE dli_service_providers FROM dli_service_providers
                          INNER JOIN vtiger_detailed_lineitems ON (dli_service_providers.dli_id = vtiger_detailed_lineitems.detaillineitemsid)
                            WHERE vtiger_detailed_lineitems.dli_relcrmid=? AND (dli_invoiced=0 OR dli_invoiced IS NULL) AND (dli_distributed=0 OR dli_distributed IS NULL) AND
                            `detaillineitemsid` NOT IN ('.implode(',', $idList).')';
                $db->pquery($removeStmt, [$quoteid]);
            $removeStmt = 'DELETE FROM `vtiger_detailed_lineitems` WHERE dli_relcrmid = ? AND (dli_invoiced=0 OR dli_invoiced IS NULL) AND (dli_distributed=0 OR dli_distributed IS NULL) AND `detaillineitemsid` NOT IN ('.implode(',', $idList).')';
            $db->pquery($removeStmt, [$quoteid]);
        return false;
    }

    public static function updateLineItemTotals($quoteid, &$fieldList)
    {
        $db = &PearDatabase::getInstance();
        // update totals
        $res = $db->pquery('SELECT SUM(dli_gross) FROM vtiger_detailed_lineitems WHERE dli_relcrmid=?',
                           [$quoteid]);
        if($res && ($row = $res->fetchRow))
        {
            $fieldList['gross_total'] = $row[0];
        }
        $res = $db->pquery('SELECT SUM(dli_invoice_net) FROM vtiger_detailed_lineitems WHERE dli_relcrmid=?',
                           [$quoteid]);
        if($res && ($row = $res->fetchRow))
        {
            $fieldList['invoice_net_total'] = $row[0];
        }
        $res = $db->pquery('SELECT SUM(dli_distribution_net) FROM vtiger_detailed_lineitems WHERE dli_relcrmid=?',
                           [$quoteid]);
        if($res && ($row = $res->fetchRow))
        {
            $fieldList['dist_net_total'] = $row[0];
        }
        $res = $db->pquery("SELECT SUM(dli_invoice_net) FROM vtiger_detailed_lineitems WHERE dli_relcrmid=?
                                  AND dli_ready_to_invoice<>0 AND dli_ready_to_invoice<>'N' AND dli_ready_to_invoice IS NOT NULL",
                           [$quoteid]);
        if($res && ($row = $res->fetchRow))
        {
            $fieldList['total_ready_to_invoice'] = $row[0];
        }
        $res = $db->pquery("SELECT SUM(dli_distribution_net) FROM vtiger_detailed_lineitems WHERE dli_relcrmid=?
                                  AND dli_ready_to_distribute<>0 AND dli_ready_to_distribute<>'N' AND dli_ready_to_distribute IS NOT NULL",
                           [$quoteid]);
        if($res && ($row = $res->fetchRow))
        {
            $fieldList['total_ready_to_dist'] = $row[0];
        }
        $db->pquery('UPDATE `vtiger_quotes` SET gross_total=?, invoice_net_total=?, dist_net_total=?, total_ready_to_invoice=?, total_ready_to_dist=? WHERE quoteid=?',
                    [$fieldList['gross_total'], $fieldList['invoice_net_total'], $fieldList['dist_net_total'], $fieldList['total_ready_to_invoice'], $fieldList['total_ready_to_dist'], $quoteid]);
    }

    protected static function deleteLineItem(&$fieldList, $i) {
        //If the item is not set to delete return false.
        if (!\MoveCrm\InputUtils::CheckboxToBool($fieldList['lineItemRemoveOnSave'.$i])) {
            return false;
        }
        //can't be removed if the line item has been set ready to invoice
        if (\MoveCrm\InputUtils::CheckboxToBool($fieldList['ready_to_invoice'.$i])) {
            return false;
        }
        //can't be removed if the line item has been set ready to distributed
        if (\MoveCrm\InputUtils::CheckboxToBool($fieldList['ready_to_distribute'.$i])) {
            return false;
        }
        //can't be removed if the line item has been invoiced
        if (\MoveCrm\InputUtils::CheckboxToBool($fieldList['invoicedone'.$i])) {
            return false;
        }
        //can't be removed if the line item has been distributed
        if (\MoveCrm\InputUtils::CheckboxToBool($fieldList['distributed'.$i])) {
            return false;
        }

        //Otherwise remove it.
        return true;
    }

    //OT 15866 Calculating days between two dates (inclusive)
    public function getSITNumberOfDays($startDateString, $endDateString)
    {
        $formattedDays = 0;
        $startDate = new DateTime($startDateString);
        $endDate = new DateTime($endDateString);
        if ($endDate > $startDate) {
            $totalDays     = date_diff($startDate, $endDate, false);
            $formattedDays = $totalDays->format('%a');
            $formattedDays++; //to count the final day as a day.
        }
        //@TODO: we are unclear if the final day is counted as a day or not.
        // this is the total day count, additional days are sent to rating as this number - 1

        return $formattedDays;
    }

    public function allowedIntrastate($fieldList)
    {
        if ($fieldList['business_line_est'] != 'Intrastate Move') {
            return false;
        }

        if (!isset($fieldList['effective_tariff'])) {
            return false;
        }

        try {
            $tariffManagerModel = TariffManager_Record_Model::getInstanceById($fieldList['effective_tariff'], 'TariffManager');
            if (!$tariffManagerModel) {
                return false;
            }

            if ($tariffManagerModel->get('tariff_type') != 'Intrastate') {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    public function saveDetailLineItems($recordId, $key, $fieldList) {
        $db = PearDatabase::getInstance();
        foreach ($fieldList['subitemitems'.$key] as $identifier) {
            $detail = [
                'CostNet'     => $fieldList['subitem_costnet'.$identifier],
                'Quantity'    => $fieldList['subitem_quantity'.$identifier],
                'Location'    => $fieldList['subitem_location'.$identifier],
                'Schedule'    => $fieldList['subitem_schedule'.$identifier],
                'Description' => $fieldList['subitem_description'.$identifier],
                'Rate'        => $fieldList['subitem_rate'.$identifier],
                'Weight'      => $fieldList['subitem_weight'.$identifier],
                'RateNet'     => $fieldList['subrate_net'.$identifier],
                'Item_Weight' => $fieldList['subitem_weight'.$identifier],
                'Rate_Net'    => $fieldList['subrate_net'.$identifier],
                'RatingItem'  => $fieldList['subitem_ratingitem'.$identifier],
            ];

            $sql = "INSERT INTO `vtiger_rating_line_item_details` (estimate_id,amount,quantity,location,schedule,description,rate,weight,ratingitem) VALUES (?,?,?,?,?,?,?,?,?)";
            $db->pquery($sql,
                        [$recordId,
                         $detail['CostNet'],
                         $detail['Quantity'],
                         $detail['Location'],
                         $detail['Schedule'],
                         $detail['Description'],
                         $detail['Rate'],
                         $detail['Weight'],
                         $detail['RatingItem']]);
        }
    }
    public function saveLineItems($recordId, $identifier, $fieldList) {
        $db = PearDatabase::getInstance();
        // file_put_contents('logs/devLog.log', "\n Line Items : \n".print_r($lineitems, true), FILE_APPEND);
        $lineitem = [
            'Quantity'    => $fieldList['lineitem_quantity'.$identifier],
            'Location'    => $fieldList['lineitem_location'.$identifier],
            'Schedule'    => $fieldList['lineitem_schedule'.$identifier],
            'Rate'        => $fieldList['lineitem_rate'.$identifier],
            'Weight'      => $fieldList['lineitem_weight'.$identifier],
            'RateNet'     => $fieldList['linerate_net'.$identifier],
            'Item_Weight' => $fieldList['lineitem_weight'.$identifier],
            'Rate_Net'    => $fieldList['linerate_net'.$identifier],
            //'Description' => $fieldList['productName'.$identifier],
            'Description' => $fieldList['lineitem_x_description'.$identifier],
            //'Subtotal'    => $fieldList['listPrice'.$identifier],
            'Subtotal'    => $fieldList['lineitem_x_subtotal'.$identifier],
        ];
        //insert new
        $sql = "INSERT INTO `vtiger_rating_line_items` (estimate_id,line_item_id,amount,quantity,location,schedule,billing_item,rate,
                       weight) VALUES (?,?,?,?,?,?,?,?,?)";

        $db->pquery($sql,
                    [$recordId,
                     $identifier,
                     $lineitem['Subtotal'],
                     $lineitem['Quantity'],
                     $lineitem['Location'],
                     $lineitem['Schedule'],
                     $lineitem['Description'],
                     $lineitem['Rate'],
                     $lineitem['Weight']]);
    }

    public function doPrimaryEstimateLogic($fieldList, $isLocal = false) {
        $hasPrefix = strpos($fieldList['potential_id'], 'x');

        $potentialid = $hasPrefix === false ? $fieldList['potential_id'] : substr(strstr($fieldList['potential_id'], 'x'), 1);
        if(!$potentialid) {
            return false;
        }
        $oppRecord = Opportunities_Record_Model::getInstanceById($potentialid);
        // This seems to be the consistent way to see if a record of that ID exists or not.
        // If I'm wrong \_(?)_/
        if(!$oppRecord->entity) {
            return false;
        }
        $oppRecord->set('mode','edit');

        // Forecasted Amount
        $oppRecord->set('amount',$fieldList['hdnGrandTotal']);

        $miles = $fieldList['interstate_mileage'];
        if ($miles > 0) {
            if (getenv('INSTANCE_NAME') == 'mccollisters') {
                $oppRecord->set('mileage',$miles);
            }
        }

        if (getenv('INSTANCE_NAME') == 'sirva') {
            $inputDates   = [
                'pack_date',
                'pack_to_date',
                'preffered_ppdate',
                'load_date',
                'load_to_date',
                'preferred_pldate',
                'deliver_date',
                'deliver_to_date',
                'preferred_pddate',
                'followup_date',
                'decision_date',
                'survey_date',
                'days_to_move',
            ];
        } else {
            $inputDates = [
                'load_date',
            ];
        }
        $setStmt      = '';
        foreach ($inputDates as $inputDate) {
            // @NOTE: This was always being overriden anyways, and sync sends them as yyyy-mm-dd anyway. So there was no reason for the old code.
            // Leaving this in for posterity.
            // $dataFormat = $current_user->get('date_format');
            $dataFormat = 'yyyy-mm-dd';
            $SqlFormattedDate = $this->FormatDate($fieldList[$inputDate], $dataFormat);

            if ($setStmt) {
                $setStmt .= ', ';
            }
            $setStmt .= $inputDate . ' = ?';
            // $params[] = $SqlFormattedDate;
            $oppRecord->set($inputDate,$SqlFormattedDate);

            if ($inputDate == 'survey_date') {
                $setStmt .= ', survey_time = ? ';
                // $params[] = $fieldList['survey_time'];
                $oppRecord->set('survey_time', $fieldList['survey_time']);
            }
        }

        // if ($setStmt) {
        //     $sql      = 'UPDATE `vtiger_potentialscf` JOIN `vtiger_potential` USING(`potentialid`) SET '.$setStmt.' WHERE `vtiger_potentialscf`.`potentialid` = ?';
        //     $params[] = $potentialid;
        //     $result   = $db->pquery($sql, $params);
        // }
        unset($params);
        if (getenv('INSTANCE_NAME') == 'sirva') {
            if($fieldList['shipper_type'] == 'NAT') {
                // $params[] = $fieldList['parent_contract'];
                // $params[] = $fieldList['nat_account_no'];
                $oppRecord->set('agmt_id', $fieldList['parent_contract']);
                $oppRecord->set('national_account_number', $fieldList['nat_account_no']);
                $oppRecord->set('business_channel', "Corporate");

                if ($fieldList['contract'] != '' && $fieldList['contract'] != 0) {
                    $contract = Vtiger_Record_Model::getInstanceById($fieldList['contract'], 'Contracts');
                    // $params[] = $contract->get('contract_no');
                    $oppRecord->set('subagmt_nbr', $fieldList['contract_no']);
                    if ($contract->get('billing_apn') != '' && $contract->get('billing_apn') != 0){
                        // $params[] = $contract->get('billing_apn');
                        $oppRecord->set('billing_apn', $fieldList['billing_apn']);
                    }
                }
            }

            //Update opportunity agrmt, sub agrmt
            if(!$isLocal && $fieldList['shipper_type'] == 'COD' && $fieldList['effective_tariff'] != '' && $fieldList['effective_tariff'] != 0){
                $tariffType = Vtiger_Record_Model::getInstanceById($fieldList['effective_tariff'], 'TariffManager')->get('custom_tariff_type');
                $oppRecord->get('shipper_type', 'COD');
                $oppRecord->get('business_channel', 'Consumer');

                switch($tariffType){
                    case 'TPG':
                        $oppRecord->set('agrmt_cod', 'TPG');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'TPG GRR':
                        $oppRecord->set('agrmt_cod', 'GRR');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'Pricelock':
                        $oppRecord->set('agrmt_cod', 'CGP');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'Pricelock GRR':
                        $oppRecord->set('agrmt_cod', 'GRR');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'Autos Only':
                        $oppRecord->set('agrmt_cod', '204-A');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'Blue Express':
                        $oppRecord->set('agrmt_cod', 'CGP');
                        $oppRecord->set('subagrmt_cod', '002');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'Truckload Express':
                        $oppRecord->set('agrmt_cod', 'CGP');
                        $oppRecord->set('subagrmt_cod', '007');
                        $oppRecord->set('express_shipment', 1);
                        break;
                    case 'UAS':
                        $oppRecord->set('agrmt_cod', 'UAS');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                        break;
                    case 'Allied Express':
                        $oppRecord->set('agrmt_cod', 'TPG');
                        $oppRecord->set('subagrmt_cod', '005');
                        $oppRecord->set('express_shipment', 1);
                        break;
                    default:
                        $oppRecord->set('agrmt_cod', '');
                        $oppRecord->set('subagrmt_cod', '001');
                        $oppRecord->set('express_shipment', 0);
                }
            }
        }

        $oppRecord->save();
    }

    public function checkIfParentHasPrimary($module, $orderId, $oppId) {
        $db = PearDatabase::getInstance();
        if(!empty($oppId)) {
            $sql    = "SELECT COUNT(quoteid) as numRelated FROM `vtiger_quotes` JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid=`vtiger_quotes`.quoteid WHERE is_primary=1 AND deleted=0 AND setype=? AND potentialid=?";
            $result = $db->pquery($sql, [$module, $oppId]);
            if ($db->num_rows($result) > 0 && $result->fields['numRelated'] > 0) {
                return true;
            }
        }

        if(!empty($orderId)) {
            $sql    = "SELECT COUNT(quoteid) as numRelated FROM `vtiger_quotes` JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid=`vtiger_quotes`.quoteid WHERE is_primary=1 AND deleted=0 AND setype=? AND orders_id=?";
            $result = $db->pquery($sql, [$module, $orderId]);
            if ($db->num_rows($result) > 0 && $result->fields['numRelated'] > 0) {
                return true;
            }
        }

        return false;
    }


        /**
     * Customizing the Delete procedure.
     */
    public function trash($module, $recordId)
    {
        $db = PearDatabase::getInstance();
        $estimateModel = Vtiger_Record_Model::getInstanceById($recordId, $module);

        if($estimateModel->get('is_primary') == 1 && $estimateModel->get('orders_id') != ''){
            if ($orderRecordModel = Vtiger_Record_Model::getInstanceById($estimateModel->get('orders_id'), 'Orders')) {
                $sql               = "SELECT dli_invoice_net FROM `vtiger_detailed_lineitems` WHERE dli_relcrmid=? AND dli_description = ?";
                $result            = $db->pquery($sql, [$recordId, 'Linehaul']);
                $estimatedLinehaul = 0;
                while ($row =& $result->fetchRow()) {
                    $estimatedLinehaul += $row['dli_invoice_net'];
                }
              
                //set the linehaul
                $orderLineHaul = $orderRecordModel->get('orders_elinehaul');
                $orderRecordModel->set('orders_elinehaul', $orderLineHaul - $estimatedLinehaul);
                $orderRecordModel->set('mode','edit');
                //save the record.
                $orderRecordModel->save();
            }
        }

        parent::trash($module, $recordId);
    }


}
