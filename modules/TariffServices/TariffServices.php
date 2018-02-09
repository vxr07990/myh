<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

//@TODO: Fix this because this is silly really.
require_once ('includes/runtime/BaseModel.php');
require_once ('modules/Vtiger/models/Record.php');
require_once ('modules/Inventory/models/Record.php');
require_once ('modules/Quotes/models/Record.php');
require_once ('modules/Estimates/models/Record.php');

class TariffServices extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_tariffservices';
    public $table_index= 'tariffservicesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_tariffservicescf', 'tariffservicesid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_tariffservices', 'vtiger_tariffservicescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_tariffservices' => 'tariffservicesid',
        'vtiger_tariffservicescf'=>'tariffservicesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Service Name' => array('tariffservices', 'service_name'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Service Name' => 'service_name',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'service_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Service Name' => array('tariffservices', 'service_name'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Service Name' => 'service_name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('service_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'service_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'service_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('service_name','assigned_user_id');

    public $default_order_by = 'service_name';
    public $default_sort_order='ASC';

    public function save_module()
    {
        //custom save for module
    }

    public function clearExtraItems($tablename, $serviceid)
    {
        $tablearray = array('baseplus',
                            'breakpoint',
                            'bulky',
                            'chargeperhundred',
                            'countycharge',
                            'hourlyset',
                            'packingitems',
                            'valuations',
                            'weightmileage',
                            'servicebasecharge',
                            'cwtbyweight',
                            'flatratebyweight'
                            );
        $db = &PearDatabase::getInstance();
        foreach ($tablearray as $table) {
            if ($table == $tablename) {
                continue;
            }

            $sql = "DELETE FROM `vtiger_tariff$table` WHERE serviceid=?";
            $db->pquery($sql, [$serviceid]);
        }
    }

    public function removeExcessPackingItems($allowedPacking, $serviceid) {
        if (!$serviceid) {
            return null;
        }

        $db = &PearDatabase::getInstance();
        $packItems = array_keys($allowedPacking);
        $toRemove = [];

        $sql = "SELECT line_item_id, pack_item_id FROM vtiger_tariffpackingitems WHERE serviceid=?";
        if($res = $db->pquery($sql, [$serviceid])) {
            while($row = $res->fetchRow()) {
                if(!in_array($row['pack_item_id'], $packItems)) {
                    $toRemove[] = $row['line_item_id'];
                }
            }
        }

        if ($toRemove) {
            $sql = "DELETE FROM vtiger_tariffpackingitems WHERE line_item_id IN (".generateQuestionMarks($toRemove).")";
            $db->pquery($sql, $toRemove);
        }
    }

    public function saveentity($module, $fileid = '')
    {
        /*if($_REQUEST['repeat'] === true){
            return;
        }
        //does things twice, this stops it.
        $_REQUEST['repeat'] = true;*/
        $this->column_fields['service_base_charge_applies'] = implode(' |##| ', $this->column_fields['service_base_charge_applies']);

        //Check and assign owner if tariff is not set to admin_access
        $db = PearDatabase::getInstance();
        $sql = "SELECT agentid, admin_access FROM `vtiger_tariffs` JOIN `vtiger_crmentity` ON tariffsid=crmid WHERE crmid=?";
        $result = $db->pquery($sql, [$this->column_fields['related_tariff']]);
        $row = $result->fetchRow();
        if ($row != null && $row['admin_access'] != 1) {
            $this->column_fields['agentid'] = $row['agentid'];
        }

        parent::saveentity($module, $fileid);
        $columns = array_merge($_REQUEST, $this->column_fields);
        if (empty($columns['record'])) {
            $columns['record'] = $columns['currentid'];
        }
        //file_put_contents('logs/devLog.log', "\n COLUMNS: ".print_r($columns, true), FILE_APPEND);
        $db = PearDatabase::getInstance();
        $serviceid = $columns['record'];
        $serviceType = $columns['rate_type'];
        $params = array();

        if ($serviceid == '' && $this->id) {
            //$sql = "SELECT id FROM `vtiger_crmentity_seq`";
            //$result = $db->pquery($sql, $params);
            //$row = $result->fetchRow();
            //$serviceid = $row[0]+1;
            $serviceid = $this->id;
        }

        if (!$serviceid) {
            //@TODO: we should really never get here, but if we do throw an error.
            throw new Exception(vtranslate('LBL_RECORD_NOT_FOUND'), -1);
        }

        $i = 1;
        if ($serviceType == 'Base Plus Trans.') {
            $this->clearExtraItems('baseplus', $serviceid);
            $basePlusCount = $columns['numBasePlus'];
            for ($i; $i<=$basePlusCount; $i++) {
                $fromMiles = $columns['fromMilesBasePlus'.$i];
                $toMiles = $columns['toMilesBasePlus'.$i];
                $fromWeight = $columns['fromWeightBasePlus'.$i];
                $toWeight = $columns['toWeightBasePlus'.$i];
                $baseRate = $columns['baseRateBasePlus'.$i];
                $excess = $columns['excessBasePlus'.$i];
                $lineItemId = $columns['lineItemIdBasePlus'.$i];

                if ($fromMiles == '' || $toMiles == '' || $fromWeight == '' || $toWeight == '' || $baseRate == '' || $excess == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffbaseplus');

                    $sql = "INSERT INTO `vtiger_tariffbaseplus` (serviceid, from_miles, to_miles, from_weight, to_weight, base_rate, excess) VALUES (?,?,?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffbaseplus` SET serviceid=?, from_miles=?, to_miles=?, from_weight=?, to_weight=?, base_rate=?, excess=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $fromMiles;
                $params[] = $toMiles;
                $params[] = $fromWeight;
                $params[] = $toWeight;
                $params[] = $baseRate;
                $params[] = $excess;
                $params[] = $lineItemId;

                //file_put_contents('logs/devLog.log', "\n PARAMS: ".print_r($params, true), FILE_APPEND);

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Break Point Trans.') {
            $this->clearExtraItems('breakpoint', $serviceid);
            $breakPointCount = $columns['numBreakPoint'];
            for ($i; $i<=$breakPointCount; $i++) {
                $fromMiles = $columns['fromMilesBreakPoint'.$i];
                $toMiles = $columns['toMilesBreakPoint'.$i];
                $fromWeight = $columns['fromWeightBreakPoint'.$i];
                $toWeight = $columns['toWeightBreakPoint'.$i];
                $breakPoint = $columns['breakPointBreakPoint'.$i];
                $baseRate = $columns['baseRateBreakPoint'.$i];
                $lineItemId = $columns['lineItemIdBreakPoint'.$i];

                if ($fromMiles == '' || $toMiles == '' || $fromWeight == '' || $toWeight == '' || $breakPoint == '' || $baseRate == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffbreakpoint');
                    $sql = "INSERT INTO `vtiger_tariffbreakpoint` (serviceid, from_miles, to_miles, from_weight, to_weight, break_point, base_rate) VALUES (?,?,?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffbreakpoint` SET serviceid=?, from_miles=?, to_miles=?, from_weight=?, to_weight=?, break_point=?, base_rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $fromMiles;
                $params[] = $toMiles;
                $params[] = $fromWeight;
                $params[] = $toWeight;
                $params[] = $breakPoint;
                $params[] = $baseRate;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Service Base Charge' || $serviceType == 'Storage Valuation') {
            $this->clearExtraItems('servicebasecharge', $serviceid);
            $chargeCount = $columns['numServiceCharge'];
            for ($i; $i<=$chargeCount; $i++) {
                $priceFrom = $columns['priceFromServiceBaseCharge'.$i];
                $priceTo = $columns['priceToServiceBaseCharge'.$i];
                $charge = $columns['chargeServiceBaseCharge'.$i];
                $lineItemId = $columns['lineItemIdServiceBaseCharge'.$i];

                if ($priceFrom == '' || $priceTo == '' || $charge == '') {
                    continue;
                }

                $sql = "INSERT INTO `vtiger_tariffservicebasecharge` (serviceid, price_from, price_to, factor) VALUES (?,?,?,?)";
                if ($lineItemId != null) {
                    $sql = "UPDATE `vtiger_tariffservicebasecharge` SET serviceid=?, price_from=?, price_to=?, factor=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $priceFrom;
                $params[] = $priceTo;
                $params[] = $charge;
                $params[] = $lineItemId;
                $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Weight/Mileage Trans.') {
            $this->clearExtraItems('weightmileage', $serviceid);
            $weightMileageCount = $columns['numWeightMileage'];
            for ($i; $i<=$weightMileageCount; $i++) {
                $fromMiles = $columns['fromMilesWeightMileage'.$i];
                $toMiles = $columns['toMilesWeightMileage'.$i];
                $fromWeight = $columns['fromWeightWeightMileage'.$i];
                $toWeight = $columns['toWeightWeightMileage'.$i];
                $baseRate = $columns['baseRateWeightMileage'.$i];
                $lineItemId = $columns['lineItemIdWeightMileage'.$i];

                if ($fromMiles == '' || $toMiles == '' || $fromWeight == '' || $toWeight == '' || $baseRate == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffweightmileage');
                    $sql = "INSERT INTO `vtiger_tariffweightmileage` (serviceid, from_miles, to_miles, from_weight, to_weight, base_rate) VALUES (?,?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffweightmileage` SET serviceid=?, from_miles=?, to_miles=?, from_weight=?, to_weight=?, base_rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $fromMiles;
                $params[] = $toMiles;
                $params[] = $fromWeight;
                $params[] = $toWeight;
                $params[] = $baseRate;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Bulky List') {
            $this->clearExtraItems('bulky', $serviceid);
            $defaultBulkyList = TariffServices_Record_Model::getDefaultBulkies();

            $bulkyCount = $columns['numBulky'];
            $bulkyItems = [];
            $i = 1;
            for ($i; $i<=$bulkyCount; $i++) {
                $description   = $columns['bulkyDescription'.$i];
                $weight        = $columns['bulkyWeight'.$i];
                $rate          = $columns['bulkyRate'.$i];
                $lineItemId    = $columns['bulkyLineItemId'.$i];
                $CartonBulkyId = $columns['CartonBulkyId'.$i];
                $standardItem = 0;

                if (isset($defaultBulkyList[$CartonBulkyId])) {
                    $standardItem = 1;
                    if (getenv('ENFORCE_STANDARD_LOCAL_BULKY_LIST')) {
                        $description = $defaultBulkyList[$CartonBulkyId]['description'];
                        $weight      = $this->testNumber($weight, $defaultBulkyList[$CartonBulkyId]['weight']);
                        $rate        = $this->testNumber($rate, $defaultBulkyList[$CartonBulkyId]['rate']);
                    }
                    unset($defaultBulkyList[$CartonBulkyId]); //@TODO: Maybe this unset goes in the ENFORCE.. if.
                } else {
                    if (getenv('DISALLOW_CUSTOM_LOCAL_BULKY_LIST')) {
                        //don't allow it to save custom local packing.  This is to thwart vtws creates/updates.
                        continue;
                    }
                }

                if ($description == '' || ($weight === '' && $rate === '')) {
                    //file_put_contents('logs/devLog.log', "\n CONTINUE", FILE_APPEND);
                    continue;
                }
                $bulkyItems[] = [
                    'serviceid'     => $serviceid,
                    'description'   => $description,
                    'weight'        => $weight,
                    'rate'          => $rate,
                    'lineItemId'    => $lineItemId,
                    'CartonBulkyId' => $CartonBulkyId,
                    'standardItem'  => $standardItem
                ];
            }
             if (getenv('ENFORCE_STANDARD_LOCAL_BULKY_LIST')) {
                 foreach ($defaultBulkyList as $defaultBulkyID => $defaultBulkyItem) {
                     $bulkyItems[] = [
                         'serviceid'      => $serviceid,
                         'description'    => $defaultBulkyItem['description'],
                         'weight'         => $defaultBulkyItem['weight'],
                         'rate'           => $defaultBulkyItem['rate'],
                         'CartonBulkyId'  => $defaultBulkyID,
                         'lineItemId'     => null,
                         'standardItem'   => 1
                     ];
                 }
             }

            foreach ($bulkyItems as $bulkyItem) {
                $sql = '';
                if ($bulkyItem['lineItemId'] == null) {
                    //@NOTE: unnecessary
                    $bulkyItem['lineItemId'] = $db->getUniqueID('vtiger_tariffbulky');
                    $sql = "INSERT INTO `vtiger_tariffbulky` (serviceid, description, weight, rate, CartonBulkyId, standardItem) VALUES (?,?,?,?,?,?)";
                } else {
                    //file_put_contents('logs/devLog.log', "\n UPDATE", FILE_APPEND);
                    $sql = "UPDATE `vtiger_tariffbulky` SET serviceid=?, description=?, weight=?, rate=?, CartonBulkyId=?, standardItem=? WHERE line_item_id=?";
                }

                $params = [];
                $params[] = $bulkyItem['serviceid'];
                $params[] = $bulkyItem['description'];
                $params[] = $bulkyItem['weight'];
                $params[] = $bulkyItem['rate'];
                $params[] = $bulkyItem['CartonBulkyId'];
                $params[] = $bulkyItem['standardItem'];
                $params[] = $bulkyItem['lineItemId'];
                $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Charge Per $100 (Valuation)') {
            $this->clearExtraItems('chargeperhundred', $serviceid);
            $chargePer100Count = $columns['numChargePer100'];
            $multiplier = $columns['chargePerHundredMultiplier'];
            $hasReleased = $columns['chargePerHundredHasReleased']?1:0;
            $releasedAmount = $columns['chargePerHundredDefaultReleased'];
            for ($i; $i<=$chargePer100Count; $i++) {
                $deductible = $columns['chargePerHundredDeductible'.$i];
                $rate = $columns['chargePerHundredRate'.$i];
                $lineItemId = $columns['chargePerHundredLineItemId'.$i];

                if ($deductible == '' || $rate == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffchargeperhundred');
                    $sql = "INSERT INTO `vtiger_tariffchargeperhundred` (serviceid, deductible, rate) VALUES (?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffchargeperhundred` SET serviceid=?, deductible=?, rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $deductible;
                $params[] = $rate;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
            // The gross gets grosser
            $sql = "UPDATE `vtiger_tariffchargeperhundred` SET multiplier=? WHERE serviceid=?";
            $db->pquery($sql, [$multiplier, $serviceid]);
            $sql = "UPDATE `vtiger_tariffservices` SET valuation_released=?,valuation_releasedamount=? WHERE tariffservicesid=?";
            $db->pquery($sql, [$hasReleased,$releasedAmount,$serviceid]);
        } elseif ($serviceType == 'County Charge') {
            $this->clearExtraItems('countycharge', $serviceid);
            $countyChargesCount = $columns['numCountyCharges'];
            for ($i; $i<=$countyChargesCount; $i++) {
                $name = $columns['countyName'.$i];
                $rate = $columns['countyRate'.$i];
                $lineItemId = $columns['countyLineItemId'.$i];

                if ($name == '' || $rate == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffcountycharge');
                    $sql = "INSERT INTO `vtiger_tariffcountycharge` (serviceid, name, rate) VALUES (?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffcountycharge` SET serviceid=?, name=?, rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $name;
                $params[] = $rate;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Hourly Set') {
            $this->clearExtraItems('hourlyset', $serviceid);
            $hourlyCount = $columns['numHourly'];
            for ($i; $i<=$hourlyCount; $i++) {
                $men = $columns['hourlyMen'.$i];
                $vans = $columns['hourlyVans'.$i];
                $rate = $columns['hourlyRate'.$i];
                $lineItemId = $columns['hourlyLineItemId'.$i];

                if ($men == '' || $vans == '' || $rate == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffhourlyset');
                    $sql = "INSERT INTO `vtiger_tariffhourlyset` (serviceid, men, vans, rate) VALUES (?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffhourlyset` SET serviceid=?, men=?, vans=?, rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $men;
                $params[] = $vans;
                $params[] = $rate;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Packing Items') {
            $this->clearExtraItems('packingitems', $serviceid);
            $packingCount = $columns['numPacking'];

            //OH MY.
            $defaultPacking = Estimates_Record_Model::getPackingLabelsStatic();
            if(getenv('DISALLOW_CUSTOM_LOCAL_PACKING')) {
                $this->removeExcessPackingItems($defaultPacking, $serviceid);
            }

            $packingItems = [];
            for ($i; $i<=$packingCount; $i++) {
                $name          = $columns['cartonName'.$i];
                $containerRate = $columns['cartonContainerRate'.$i];
                $packingRate   = $columns['cartonPackingRate'.$i];
                $unpackingRate = $columns['cartonUnpackingRate'.$i];
                $lineItemId    = $columns['cartonLineItemId'.$i];
                $packItemId    = $columns['packItemId'.$i];
                //$standardItem  = $columns['standardItem'.$i];
                $standardItem  = 0;

                if (isset($defaultPacking[$packItemId])) {
                    $standardItem = 1;
                    if (getenv('ENFORCE_STANDARD_LOCAL_PACKING')) {
                        $name          = $defaultPacking[$packItemId];
                        $containerRate = $containerRate? :'0.00';
                        $packingRate   = $packingRate? :'0.00';
                        $unpackingRate = $unpackingRate? :'0.00';
                    }
                    unset($defaultPacking[$packItemId]); //@TODO: Maybe this unset goes in the ENFORCE.. if.
                } else {
                    if (getenv('DISALLOW_CUSTOM_LOCAL_PACKING')) {
                        //don't allow it to save custom local packing.  This is to thwart vtws creates/updates.
                        continue;
                    }
                }

                if ($name == '' || ($containerRate == '' && $packingRate == '' && $unpackingRate == '')) {
                    continue;
                }

                $packingItems[] = [
                    'serviceid'     => $serviceid,
                    'name'          => $name,
                    'containerRate' => $containerRate,
                    'packingRate'   => $packingRate,
                    'unpackingRate' => $unpackingRate,
                    'packItemId'    => $packItemId,
                    'standardItem'  => $standardItem,
                    'lineItemId'    => $lineItemId
                ];
            }

            if (getenv('ENFORCE_STANDARD_LOCAL_PACKING')) {
                foreach ($defaultPacking as $defaultPackID => $defaultPackingItem) {
                    $packingItems[] = [
                        'serviceid'     => $serviceid,
                        'name'          => $defaultPackingItem,
                        'containerRate' => '0.00',
                        'packingRate'   => '0.00',
                        'unpackingRate' => '0.00',
                        'packItemId'    => $defaultPackID,
                        'standardItem'  => 1
                    ];
                }
            }

            foreach ($packingItems as $packItem) {
                $sql = '';
                if ($packItem['lineItemId'] == null) {
                    //@NOTE: unnecessary
                    $packItem['lineItemId'] = $db->getUniqueID('vtiger_tariffpackingitems');
                    $sql = "INSERT INTO `vtiger_tariffpackingitems` (serviceid, name, container_rate, packing_rate, unpacking_rate, pack_item_id, standardItem) VALUES (?,?,?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffpackingitems` SET serviceid=?, name=?, container_rate=?, packing_rate=?, unpacking_rate=?, pack_item_id=?, standardItem=? WHERE line_item_id=?";
                }

                $params = []; //@NOTE: because trust issues.
                $params[] = $packItem['serviceid'];
                $params[] = $packItem['name'];
                $params[] = $packItem['containerRate'];
                $params[] = $packItem['packingRate'];
                $params[] = $packItem['unpackingRate'];
                $params[] = $packItem['packItemId'];
                $params[] = $packItem['standardItem'];
                $params[] = $packItem['lineItemId'];
                $db->pquery($sql, $params);
                unset($params);
            }
        } elseif ($serviceType == 'Tabled Valuation') {
            $this->clearExtraItems('valuations', $serviceid);
            $valuationNum = $columns['valuationNum'];
            $valuationMultiplier = $columns['valuationMultipler'];
            file_put_contents('logs/devLog.log', "\n VALUATION NUM: ".$valuationNum, FILE_APPEND);
            for ($i; $i <= $valuationNum; $i++) {
                $amount = $columns['valuationAmount'.$i];
                $deductible = $columns['valuationDeductible'.$i];
                $cost = $columns['valuationCost'.$i];
                $lineItemId = $columns['valuationLineItemId'.$i];
                $amountRow = $columns['amountRow'.$i];
                $deductibleRow = $columns['deductibleRow'.$i];

                file_put_contents('logs/devLog.log', "\n i: $i , amount: $amount , deductible: $deductible, cost: $cost, lineItemId: $lineItemId, amountRow: $amountRow, deductibleRow: $deductibleRow", FILE_APPEND);

                if ($amount == '' || $deductible == '' || $cost == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null) {
                    //@NOTE: unnecessary
                    $lineItemId = $db->getUniqueID('vtiger_tariffvaluations');
                    $sql = "INSERT INTO `vtiger_tariffvaluations` (serviceid, amount, deductible, cost, amount_row, deductible_row) VALUES (?,?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffvaluations` SET serviceid=?, amount=?, deductible=?, cost=?, amount_row=?, deductible_row=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $amount;
                $params[] = $deductible;
                $params[] = $cost;
                $params[] = $amountRow;
                $params[] = $deductibleRow;
                $params[] = $lineItemId;

                //file_put_contents('logs/devLog.log', "\n PARAMS: ".print_r($params, true), FILE_APPEND);
                //file_put_contents('logs/devLog.log', "\n SQL: $sql", FILE_APPEND);

                $result = $db->pquery($sql, $params);

                unset($params);
            }
            $sql = "UPDATE `vtiger_tariffvaluations` SET multiplier=? WHERE serviceid=?";
            $db->pquery($sql, [$valuationMultiplier,$serviceid]);
        } elseif ($serviceType == 'CWT by Weight' || $serviceType == 'SIT Cartage') {
            $this->clearExtraItems('cwtbyweight', $serviceid);
            $CWTbyWeightCount = $columns['numCWTbyWeight'];
            for ($i; $i<=$CWTbyWeightCount; $i++) {
                $fromWeight = $columns['fromWeightCWTbyWeight'.$i];
                $toWeight = $columns['toWeightCWTbyWeight'.$i];
                $baseRate = $columns['baseRateCWTbyWeight'.$i];
                $lineItemId = $columns['lineItemIdCWTbyWeight'.$i];
                if ($fromWeight == '' || $toWeight == '' || $baseRate == '') {
                    continue;
                }

                $sql = '';
                if ($lineItemId == null || empty($lineItemId)) {
                    //@NOTE: NEEDED HERE
                    $lineItemId = $db->getUniqueID('vtiger_tariffcwtbyweight');
                    $sql = "INSERT INTO `vtiger_tariffcwtbyweight` (from_weight, to_weight, rate, line_item_id, serviceid) VALUES (?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffcwtbyweight` SET from_weight=?, to_weight=?, rate=? WHERE line_item_id=?";
                }


                $params[] = $fromWeight;
                $params[] = $toWeight;
                $params[] = $baseRate;
                $params[] = $lineItemId;
                $params[] = $serviceid;
                $result = $db->pquery($sql, $params);
                if (getenv('INSTANCE_NAME') == 'sirva') {
                    $sql2   = "UPDATE `vtiger_tariffservices` SET cartage_cwt_rate=? WHERE tariffservicesid=?";
                    $result = $db->pquery($sql2, [$baseRate, $serviceid]);
                }
                unset($params);
            }
        } elseif ($serviceType == 'Flat Rate By Weight') {
          $this->clearExtraItems('flatratebyweight', $serviceid);
          $flatRateByWeightCount = $columns['numRateByWeight'];

          for($i;$i<=$flatRateByWeightCount;$i++) {
            $from       = $columns['flatratebyweight_from'.$i];
            $to         = $columns['flatratebyweight_to'.$i];
            $rate       = $columns['flatratebyweight_rate'.$i];
            $lineItemId = $columns['flatratebyweight_lineitemId'.$i];
            //Yes this will set it on every row, it is expected. Why? ¯\_(ツ)_/¯
            $cwtRate    = $columns['flatratebyweight_cwtrate'];

            if($from == '' || $to == '' || $rate == '') {
              continue;
            }
            if ($lineItemId == null || empty($lineItemId)) {
                //@NOTE: NEEDED HERE
                $lineItemId = $db->getUniqueID('vtiger_tariffflatratebyweight');
                $sql = "INSERT INTO `vtiger_tariffflatratebyweight` (from_weight, to_weight, rate, cwt_rate, line_item_id, serviceid) VALUES (?,?,?,?,?,?)";
            } else {
                $sql = "UPDATE `vtiger_tariffflatratebyweight` SET from_weight=?, to_weight=?, rate=?, cwt_rate=? WHERE line_item_id=?";
            }

            $params[] = $from;
            $params[] = $to;
            $params[] = $rate;
            $params[] = $cwtRate;
            $params[] = $lineItemId;
            $params[] = $serviceid;
            $result = $db->pquery($sql, $params);
            unset($params);
          }
        }
    }

    private function testNumber($rate, $default) {
        //If $rate is set as any thing except empty string.
        if (isset($rate) && $rate !== '') {
            return $rate;
        }
        return $default;
    }

    /**
     * Function to retrieve custom fields
     * @param Int Record ID
     * @return Array fieldName=>fieldValue pairs
     */
    public function retrieve($record)
    {
        $fieldList = [];
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'TariffServices');
        $serviceType = $recordModel->get('rate_type');
        $db = PearDatabase::getInstance();
        //changed from 0 to 1 because the create starts at 1.
        $seq = 1;

        switch ($serviceType) {
            case 'Base Plus Trans.':
                $sql = "SELECT * FROM `vtiger_tariffbaseplus` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['fromMilesBasePlus'.$seq] = $row['from_miles'];
                    $fieldList['toMilesBasePlus'.$seq] = $row['to_miles'];
                    $fieldList['fromWeightBasePlus'.$seq] = $row['from_weight'];
                    $fieldList['toWeightBasePlus'.$seq] = $row['to_weight'];
                    $fieldList['baseRateBasePlus'.$seq] = $row['base_rate'];
                    $fieldList['excessBasePlus'.$seq] = $row['excess'];
                    $seq++;
                }
                $fieldList['numBasePlus'] = $seq;
                break;
            case 'Break Point Trans.':
                $sql  = "SELECT * FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['fromMilesBreakPoint'.$seq] = $row['from_miles'];
                    $fieldList['toMilesBreakPoint'.$seq] = $row['to_miles'];
                    $fieldList['fromWeightBreakPoint'.$seq] = $row['from_weight'];
                    $fieldList['toWeightBreakPoint'.$seq] = $row['to_weight'];
                    $fieldList['breakPointBreakPoint'.$seq] = $row['break_point'];
                    $fieldList['baseRateBreakPoint'.$seq] = $row['base_rate'];
                    $seq++;
                }
                $fieldList['numBreakPoint'] = $seq;
                break;
            case 'Service Base Charge':
                $sql = "SELECT * FROM `vtiger_tariffservicebasecharge` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['priceFromServiceBaseCharge'.$seq] = $row['price_from'];
                    $fieldList['priceToServiceBaseCharge'.$seq] = $row['price_to'];
                    $fieldList['chargeServiceBaseCharge'.$seq] = $row['factor'];
                    $seq++;
                }
                $fieldList['numServiceCharge'] = $seq;
                break;
            case 'Weight/Mileage Trans.':
                $sql = "SELECT * FROM `vtiger_tariffweightmileage` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['fromMilesWeightMileage'.$seq] = $row['from_miles'];
                    $fieldList['toMilesWeightMileage'.$seq] = $row['to_miles'];
                    $fieldList['fromWeightWeightMileage'.$seq] = $row['from_weight'];
                    $fieldList['toWeightWeightMileage'.$seq] = $row['to_weight'];
                    $fieldList['baseRateWeightMileage'.$seq] = $row['base_rate'];
                    $seq++;
                }
                $fieldList['numWeightMileage'] = $seq;
                break;
            case 'Bulky List':
                $sql = "SELECT * FROM `vtiger_tariffbulky` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['bulkyDescription'.$seq] = $row['description'];
                    $fieldList['bulkyWeight'.$seq] = $row['weight'];
                    $fieldList['bulkyRate'.$seq] = $row['rate'];
                    $fieldList['CartonBulkyId'.$seq] = $row['CartonBulkyId'];
                    $fieldList['standardItem'.$seq] = $row['standardItem'];
                    $fieldList['bulkyLineItemId'.$seq] = $row['line_item_id'];
                    $seq++;
                }
                $fieldList['numBulky'] = $seq;
                break;
            case 'Charge Per $100 (Valuation)':
                $sql = "SELECT * FROM `vtiger_tariffchargeperhundred` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['chargePerHundredMultiplier'] = $row['multiplier'];
                    $fieldList['chargePerHundredDeductible'.$seq] = $row['deductible'];
                    $fieldList['chargePerHundredRate'.$seq] = $row['rate'];
                    $seq++;
                }
                $fieldList['numChargePer100'] = $seq;
                break;
            case 'County Charge':
                $sql = "SELECT * FROM `vtiger_tariffcountycharge` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['countyName'.$seq] = $row['name'];
                    $fieldList['countyRate'.$seq] = $row['rate'];
                    $seq++;
                }
                $fieldList['numCountyCharges'] = $seq;
                break;
            case 'Hourly Set':
                $sql = "SELECT * FROM `vtiger_tariffhourlyset` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['hourlyMen'.$seq] = $row['men'];
                    $fieldList['hourlyVans'.$seq] = $row['vans'];
                    $fieldList['hourlyRate'.$seq] = $row['rate'];
                    $seq++;
                }
                $fieldList['numHourly'] = $seq;
                break;
            case 'Packing Items':
                $sql = "SELECT * FROM `vtiger_tariffpackingitems` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['cartonName'.$seq] = $row['name'];
                    $fieldList['cartonContainerRate'.$seq] = $row['container_rate'];
                    $fieldList['cartonPackingRate'.$seq] = $row['packing_rate'];
                    $fieldList['cartonUnpackingRate'.$seq] = $row['unpacking_rate'];
                    $fieldList['packItemId'.$seq] = $row['pack_item_id'];
                    $fieldList['standardItem'.$seq] = $row['standardItem'];
                    $fieldList['cartonLineItemId'.$seq] = $row['line_item_id'];
                    $seq++;
                }
                $fieldList['numPacking'] = $seq;
                break;
            case 'Tabled Valuation':
                $sql = "SELECT * FROM `vtiger_tariffvaluations` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['valuationAmount'.$seq] = $row['amount'];
                    $fieldList['valuationDeductible'.$seq] = $row['deductible'];
                    $fieldList['valuationCost'.$seq] = $row['cost'];
                    $fieldList['amountRow'.$seq] = $row['amount_row'];
                    $fieldList['deductibleRow'.$seq] = $row['deductible_row'];
                    $fieldList['valuationMultiplier'] = $row['multiplier'];
                    $seq++;
                }
                $fieldList['valuationNum'] = $seq;
                break;
            case 'CWT by Weight':
                $sql = "SELECT * FROM `vtiger_tariffcwtbyweight` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['fromWeightCWTbyWeight'.$seq] = $row['from_weight'];
                    $fieldList['toWeightCWTbyWeight'.$seq] = $row['to_weight'];
                    $fieldList['baseRateCWTbyWeight'.$seq] = $row['rate'];
                    $seq++;
                }
                $fieldList['numCWTbyWeight'] = $seq;
                break;
            case 'Flat Rate By Weight':
                $sql = "SELECT * FROM `vtiger_tariffcwtbyweight` WHERE serviceid=?";
                $result = $db->pquery($sql, [$record]);
                while ($row =& $result->fetchRow()) {
                    $fieldList['flatratebyweight_from'.$seq] = $row['from_weight'];
                    $fieldList['flatratebyweight_to'.$seq] = $row['to_weight'];
                    $fieldList['flatratebyweight_rate'.$seq] = $row['rate'];
                    $fieldList['flatratebyweight_lineitemId'.$seq] = $row['line_item_id'];
                    $seq++;
                }
                $fieldList['numCWTbyWeight'] = $seq;
                break;
        }

        return $fieldList;
    }

    /* function insertIntoEntityTable($table_name, $module, $fileid = '') {
        parent::insertIntoEntityTable($table_name, $module, $fileid);
        file_put_contents('logs/devLog.log', "\n request : ".print_r($_REQUEST, true), FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n post : ".print_r($_POST, true), FILE_APPEND);
        //save logic goes here

    } */

    /**
    * Invoked when special actions are performed on the module.
    * @param String Module name
    * @param String Event Type
    */
    public function vtlib_handler($moduleName, $eventType)
    {
        global $adb;
        if ($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }
}
