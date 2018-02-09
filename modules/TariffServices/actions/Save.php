<?php

class TariffServices_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        parent::process($request);
        
        /*$db = PearDatabase::getInstance();
        $serviceid = $request->get('record');
        $serviceType = $request->get('rate_type');
        $params = array();

        if($serviceid == '') {
            $sql = "SELECT id FROM `vtiger_crmentity_seq`";
            $result = $db->pquery($sql, $params);
            $row = $result->fetchRow();
            $serviceid = $row[0]+1;
        }

        $i = 1;
        if($serviceType == 'Base Plus Trans.') {
            $this->clearExtraItems('baseplus', $serviceid);
            $basePlusCount = $request->get('numBasePlus');
            for($i; $i<=$basePlusCount; $i++) {
                $fromMiles = $request->get('fromMilesBasePlus'.$i);
                $toMiles = $request->get('toMilesBasePlus'.$i);
                $fromWeight = $request->get('fromWeightBasePlus'.$i);
                $toWeight = $request->get('toWeightBasePlus'.$i);
                $baseRate = $request->get('baseRateBasePlus'.$i);
                $excess = $request->get('excessBasePlus'.$i);
                $lineItemId = $request->get('lineItemIdBasePlus'.$i);

                if($fromMiles == '' || $toMiles == '' || $fromWeight == '' || $toWeight == '' || $baseRate == '' || $excess == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffbaseplus_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffbaseplus_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

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

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } else if($serviceType == 'Break Point Trans.') {
            $this->clearExtraItems('breakpoint', $serviceid);
            $breakPointCount = $request->get('numBreakPoint');
            for($i; $i<=$breakPointCount; $i++) {
                $fromMiles = $request->get('fromMilesBreakPoint'.$i);
                $toMiles = $request->get('toMilesBreakPoint'.$i);
                $fromWeight = $request->get('fromWeightBreakPoint'.$i);
                $toWeight = $request->get('toWeightBreakPoint'.$i);
                $breakPoint = $request->get('breakPointBreakPoint'.$i);
                $baseRate = $request->get('baseRateBreakPoint'.$i);
                $lineItemId = $request->get('lineItemIdBreakPoint'.$i);

                if($fromMiles == '' || $toMiles == '' || $fromWeight == '' || $toWeight == '' || $breakPoint == '' || $baseRate == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffbreakpoint_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffbreakpoint_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

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
        } else if($serviceType == 'Weight/Mileage Trans.') {
            $this->clearExtraItems('weightmileage', $serviceid);
            $weightMileageCount = $request->get('numWeightMileage');
            for($i; $i<=$weightMileageCount; $i++) {
                $fromMiles = $request->get('fromMilesWeightMileage'.$i);
                $toMiles = $request->get('toMilesWeightMileage'.$i);
                $fromWeight = $request->get('fromWeightWeightMileage'.$i);
                $toWeight = $request->get('toWeightWeightMileage'.$i);
                $baseRate = $request->get('baseRateWeightMileage'.$i);
                $lineItemId = $request->get('lineItemIdWeightMileage'.$i);

                if($fromMiles == '' || $toMiles == '' || $fromWeight == '' || $toWeight == '' || $baseRate == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffweightmileage_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffweightmileage_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

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
        } else if($serviceType == 'Bulky List') {
            $this->clearExtraItems('bulky', $serviceid);
            $bulkyCount = $request->get('numBulky');
            for($i; $i<=$bulkyCount; $i++) {
                $description = $request->get('bulkyDescription'.$i);
                $weight = $request->get('bulkyWeight'.$i);
                $rate = $request->get('bulkyRate'.$i);
                $lineItemId = $request->get('bulkyLineItemId'.$i);

                if($description == '' || ($weight == '' && rate == '')) {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffbulky_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffbulky_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

                    $sql = "INSERT INTO `vtiger_tariffbulky` (serviceid, description, weight, rate) VALUES (?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffbulky` SET serviceid=?, description=?, weight=?, rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $description;
                $params[] = $weight;
                $params[] = $rate;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } else if($serviceType == 'Charge Per $100 (Valuation)') {
            $this->clearExtraItems('chargeperhundred', $serviceid);
            $chargePer100Count = $request->get('numChargePer100');
            for($i; $i<=$chargePer100Count; $i++) {
                $deductible = $request->get('chargePerHundredDeductible'.$i);
                $rate = $request->get('chargePerHundredRate'.$i);
                $lineItemId = $request->get('chargePerHundredLineItemId'.$i);

                if($deductible == '' || $rate == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffchargeperhundred_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffchargeperhundred_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

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
        } else if($serviceType == 'County Charge') {
            $this->clearExtraItems('countycharge', $serviceid);
            $countyChargesCount = $request->get('numCountyCharges');
            for($i; $i<=$countyChargesCount; $i++) {
                $name = $request->get('countyName'.$i);
                $rate = $request->get('countyRate'.$i);
                $lineItemId = $request->get('countyLineItemId'.$i);

                if($name == '' || $rate == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffcountycharge_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffcountycharge_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

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
        } else if($serviceType == 'Hourly Set') {
            $this->clearExtraItems('hourlyset', $serviceid);
            $hourlyCount = $request->get('numHourly');
            for($i; $i<=$hourlyCount; $i++) {
                $men = $request->get('hourlyMen'.$i);
                $vans = $request->get('hourlyVans'.$i);
                $rate = $request->get('hourlyRate'.$i);
                $lineItemId = $request->get('hourlyLineItemId'.$i);

                if($men == '' || $vans == '' || $rate == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffhourlyset_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffhourlyset_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

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
        } else if($serviceType == 'Packing Items') {
            $this->clearExtraItems('packingitems', $serviceid);
            $packingCount = $request->get('numPacking');
            for($i; $i<=$packingCount; $i++) {
                $name = $request->get('cartonName'.$i);
                $containerRate = $request->get('cartonContainerRate'.$i);
                $packingRate = $request->get('cartonPackingRate'.$i);
                $unpackingRate = $request->get('cartonUnpackingRate'.$i);
                $lineItemId = $request->get('cartonLineItemId'.$i);
                $packItemId = $request->get('packItemId'.$i);

                if($name == '' || ($containerRate == '' && $packingRate == '' && $unpackingRate == '')) {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffpackingitems_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffpackingitems_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

                    $sql = "INSERT INTO `vtiger_tariffpackingitems` (serviceid, name, container_rate, packing_rate, unpacking_rate, pack_item_id) VALUES (?,?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffpackingitems` SET serviceid=?, name=?, container_rate=?, packing_rate=?, unpacking_rate=?, pack_item_id=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $name;
                $params[] = $containerRate;
                $params[] = $packingRate;
                $params[] = $unpackingRate;
                $params[] = $packItemId;
                $params[] = $lineItemId;


                $result = $db->pquery($sql, $params);
                unset($params);
            }
        } else if($serviceType == 'Tabled Valuation') {
            $this->clearExtraItems('valuations', $serviceid);

            while($request->get('valuationAmount'.$i) != NULL) {
                $amount = $request->get('valuationAmount'.$i);
                $deductible = $request->get('valuationDeductible'.$i);
                $cost = $request->get('valuationCost'.$i);
                $lineItemId = $request->get('valuationLineItemId'.$i);

                if($amount == '' || $deductible == '' || $cost == '') {
                    $i++;
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL) {
                    $sql = "UPDATE `vtiger_tariffvaluations_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffvaluations_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

                    $sql = "INSERT INTO `vtiger_tariffvaluations` (serviceid, amount, deductible, cost) VALUES (?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffvaluations` SET serviceid=?, amount=?, deductible=?, cost=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $amount;
                $params[] = $deductible;
                $params[] = $cost;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);

                unset($params);
                $i++;
            }
        } elseif($serviceType == 'CWT by Weight'){
            $this->clearExtraItems('cwtbyweight', $serviceid);
            $CWTbyWeightCount = $request->get('numCWTbyWeight');
            for($i; $i<=$CWTbyWeightCount; $i++) {
                $fromWeight = $request->get('fromWeightCWTbyWeight'.$i);
                $toWeight = $request->get('toWeightCWTbyWeight'.$i);
                $baseRate = $request->get('baseRateCWTbyWeight'.$i);
                $lineItemId = $request->get('lineItemIdCWTbyWeight'.$i);

                if($fromWeight == '' || $toWeight == '' || $baseRate == '') {
                    continue;
                }

                $sql = '';
                if($lineItemId == NULL || empty($lineItemId)) {
                    $sql = "UPDATE `vtiger_tariffcwtbyweight_seq` SET id=id+1";
                    $result = $db->pquery($sql, $params);

                    $sql = "SELECT id FROM `vtiger_tariffcwtbyweight_seq`";
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    $lineItemId = $row[0];

                    $sql = "INSERT INTO `vtiger_tariffcwtbyweight` (serviceid, from_weight, to_weight, rate, line_item_id) VALUES (?,?,?,?,?)";
                } else {
                    $sql = "UPDATE `vtiger_tariffcwtbyweight` SET from_weight=?, to_weight=?, rate=? WHERE line_item_id=?";
                }

                $params[] = $serviceid;
                $params[] = $fromWeight;
                $params[] = $toWeight;
                $params[] = $baseRate;
                $params[] = $lineItemId;

                $result = $db->pquery($sql, $params);
                unset($params);
            }
        }
        */
        //this is what caused the saveentity repeat
        //$recordModel = $this->saveRecord($request);
        $recordId = $request->get('record');
        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentRecordId = $request->get('sourceRecord');
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
            //TODO : Url should load the related list instead of detail view of record
            $loadUrl = $parentRecordModel->getDetailViewUrl();
            $loadUrl = $loadUrl.'&relatedModule=TariffServices&mode=showRelatedList&tab_label=Tariff%20Services';
        } else {
            //@TODO: recordModel was not being set, we should be able to get it now, but I'm unsure if/how we need to handle error cases.
            if ($recordId) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
                if ($recordModel) {
                    if ($request->get('returnToList')) {
                        $loadUrl = $recordModel->getModule()->getListViewUrl();
                    } else {
                        $loadUrl = $recordModel->getDetailViewUrl();
                    }
                } else {
                    //no recordModel error case.
                }
            } else {
                //no recordID error case.
            }
        }
        header("Location: $loadUrl");
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
                            'cwtbyweight');
        $db = PearDatabase::getInstance();
        $params = array();
        $params[] = $serviceid;
        
        foreach ($tablearray as $table) {
            if ($table == $tablename) {
                continue;
            }
            
            $sql = "DELETE FROM `vtiger_tariff$table` WHERE serviceid=?";
            $params[] = $serviceid;
            
            $result = $db->pquery($sql, $params);
        }
    }
}
