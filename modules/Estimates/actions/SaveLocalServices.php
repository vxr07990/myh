<?php

class Estimates_SaveLocalServices_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/devLog.log', "\n IN PROCESS", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $params = array();
        $row = null;
        $record = $request->get('record');
        $response = new Vtiger_Response();
        //file_put_contents('logs/SaveLocalServices.log', "\n \$record : ". print_r($record,true), FILE_APPEND);
        $rate_type = $request->get('RateType');
        //file_put_contents('logs/devLog.log', "\n \$rate_type : ". print_r($rate_type,true), FILE_APPEND);
        $selectedId = $request->get('selectedId');
        //file_put_contents('logs/SaveLocalServices.log', "\n \$selectedId : ". print_r($selectedId,true), FILE_APPEND);
        if ($rate_type === 'local_tariff') {
            $selected = $request->get('selected');
            //file_put_contents('logs/SaveLocalServices.log', "\n \$selected : ". print_r($selected,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$selectedId : ". print_r($selectedId,true), FILE_APPEND);

            $sql = "UPDATE `vtiger_quotes` SET effective_tariff=? WHERE quoteid=?";
            $result = $db->pquery($sql, array($selectedId, $record));
        } elseif ($rate_type == 'section_discount') {
            $sectionid = $request->get('sectionid');
            $amount = $request->get('amount');
            //file_put_contents('logs/devLog.log', "\n sectionid : ".$sectionid."\n amount : ".$amount, FILE_APPEND);
            $sql = "SELECT * FROM `vtiger_quotes_sectiondiscount` WHERE estimateid=? AND sectionid=?";
            $result = $db->pquery($sql, array($record, $sectionid));
            $row = $result->fetchRow();
            //file_put_contents('logs/devLog.log', "\n past first sql", FILE_APPEND);
            if ($row == null) {
                //file_put_contents('logs/devLog.log', "\n in if", FILE_APPEND);
                $sql = "INSERT INTO `vtiger_quotes_sectiondiscount` (estimateid, sectionid, discount_percent) VALUES (?,?,?)";
                $result = $db->pquery($sql, array($record, $sectionid, $amount));
                //file_put_contents('logs/devLog.log', "\n past second sql", FILE_APPEND);
            } else {
                //file_put_contents('logs/devLog.log', "\n in else", FILE_APPEND);
                $sql = "UPDATE `vtiger_quotes_sectiondiscount` SET discount_percent=? WHERE estimateid=? AND sectionid=?";
                $result = $db->pquery($sql, array($amount, $record, $sectionid));
                //file_put_contents('logs/devLog.log', "\n past second sql", FILE_APPEND);
            }
        } elseif ($rate_type === 'BasePlus') {
            $rate_type = 'Base Plus Trans.';
            $miles = $request->get('miles');
            $rate = $request->get('rate');
            $weight = $request->get('weight');
            $excess = $request->get('excess');
            
            $prevMiles = $request->get('prevMiles');
            $prevWeight = $request->get('prevWeight');
            
            if ($rate === 'none' || $excess === 'none') {
                $sql = "SELECT base_rate, excess FROM `vtiger_tariffbaseplus` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
                $result = $db->pquery($sql, array($miles, $miles, $weight, $weight, $selectedId));
                $row = $result->fetchRow();
                $newRate = $row[0];
                $newExcess = $row[1];
                
                $sql = "SELECT base_rate, excess FROM `vtiger_tariffbaseplus` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
                $result = $db->pquery($sql, array($prevMiles, $prevMiles, $prevWeight, $prevWeight, $selectedId));
                $row = $result->fetchRow();
                $prevRate = $row[0];
                $prevExcess = $row[1];
            }
            $info = array();
            if ($prevRate === $newRate) {
                $info['rate'] = false;
                $info['excess'] = false;
            } else {
                $rate = $newRate;
                $excess = $newExcess;
                $info['rate'] = $rate;
                $info['excess'] = $excess;
            }
            
            $sql = "SELECT * FROM `vtiger_quotes_baseplus` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_baseplus` (estimateid, serviceid, mileage, weight, rate, excess) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $miles, $weight, $rate, $excess));
            } else {
                $sql = "UPDATE `vtiger_quotes_baseplus` SET mileage=?, weight=?, rate=?, excess=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($miles, $weight, $rate, $excess, $record, $selectedId));
            }
            $response->setResult($info);
        } elseif ($rate_type === 'WeightMile') {
            $rate_type = 'Weight/Mileage Trans.';
            $mileage = $request->get('miles');
            $rate = $request->get('rate');
            $weight = $request->get('weight');
            
            $prevMiles = $request->get('prevMiles');
            $prevWeight = $request->get('prevWeight');
            
            
            if ($rate === 'none') {
                //file_put_contents('logs/SaveLocalServices.log', "\n Rate was none", FILE_APPEND);
                $sql = "SELECT base_rate FROM `vtiger_tariffweightmileage` WHERE ? >= from_miles AND ? <= to_miles AND ? >=from_weight AND ?<=to_weight AND serviceid=?";
                $result = $db->pquery($sql, array($mileage, $mileage, $weight, $weight, $selectedId));
                $row = $result->fetchRow();
                $rate = $row[0];
                
                $sql = "SELECT base_rate FROM `vtiger_tariffweightmileage` WHERE ? >= from_miles AND ? <= to_miles AND ? >=from_weight AND ?<=to_weight AND serviceid=?";
                $result = $db->pquery($sql, array($prevMiles, $prevMiles, $prevWeight, $prevWeight, $selectedId));
                $row = $result->fetchRow();
                $prevRate = $row[0];
            }
            
            $info = array();
            if ($prevRate === $rate) {
                $info['rate'] = false;
            } else {
                $info['rate'] = $rate;
            }
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$mileage : ". print_r($mileage,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : ". print_r($rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$weight : ". print_r($weight,true), FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_weightmileage` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_weightmileage` (estimateid, serviceid, mileage, weight, rate) VALUES (?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $mileage, $weight, $rate));
            } else {
                $sql = "UPDATE `vtiger_quotes_weightmileage` SET mileage=?, weight=?, rate=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($mileage, $weight, $rate, $record, $selectedId));
            }
            
            
            $response->setResult($info);
        } elseif ($rate_type === 'CountyCharge') {
            $rate_type = 'County Charge';
            $county = $request->get('county');
            $rate = $request->get('rate');
            $info = array();
            $info['rate'] = false;
            
            if ($rate === 'none') {
                $sql = "SELECT rate FROM `vtiger_tariffcountycharge` WHERE name=? and serviceid=?";
                $result = $db->pquery($sql, array($county, $selectedId));
                
                while ($row =& $result->fetchRow()) {
                    $info['rate'] = $row[0];
                }
                if (empty($info['rate'])) {
                    $info['rate'] = 0;
                }
                $rate = $info['rate'];
            }
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$county : ". print_r($county,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : ". print_r($rate,true), FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_countycharge` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_countycharge` (estimateid, serviceid, county, rate) VALUES (?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $county, $rate));
            } else {
                $sql = "UPDATE `vtiger_quotes_countycharge` SET county=?, rate=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($county, $rate, $record, $selectedId));
            }
            $response->setResult($info);
        } elseif ($rate_type === 'HourlySet') {
            $rate_type = 'Hourly Set';
            $men = $request->get('men');
            $vans = $request->get('vans');
            $hours = $request->get('hours');
            $traveltime = $request->get('traveltime');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$men : " . $men, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$vans : " . $vans, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$hours : " . $hours, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$traveltime : " . $traveltime, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $return = array();
            $return['rate'] = false;
            
            if ($rate === 'none') {
                if (!empty($vans)) {
                    $sql = "SELECT `vtiger_tariffhourlyset`.rate, `vtiger_tariffhourlyset`.men, `vtiger_tariffhourlyset`.vans, `vtiger_tariffservices`.hourlyset_addmanrate, `vtiger_tariffservices`.hourlyset_addvanrate  
							FROM `vtiger_tariffhourlyset` JOIN `vtiger_tariffservices` ON `vtiger_tariffhourlyset`.serviceid = `vtiger_tariffservices`.tariffservicesid 
							WHERE men <=? and vans <=? and serviceid=?";

                    $result = $db->pquery($sql, array($men, $vans, $selectedId));
                    
                    $info = array();
                    while ($row =& $result->fetchRow()) {
                        $info[] = array('Rate'=>$row[0], 'Men'=>$row[1], 'Vans'=>$row[2], 'AddMan'=>$row[3], 'AddVan'=>$row[4]);
                    }
                    
                    $closest = array();
                    
                    if ($info[0]['AddVan'] >= $info[0]['AddMan']) {
                        $prevDiff = $men;
                        foreach ($info as $line) {
                            $diff = $men - $line['Men'];
                            
                            if ($diff < $prevDiff) {
                                $closest[0] = $line;
                                $prevDiff = $diff;
                            }
                        }
                    } else {
                        $prevDiff = $vans;
                        foreach ($info as $line) {
                            $diff = $vans - $line['Vans'];
                            if ($diff < $prevDiff) {
                                $closest[0] = $line;
                                $prevDiff = $diff;
                            }
                        }
                    }
                    $return['rate'] = $closest[0]['Rate'] + (($vans-$closest[0]['Vans'])*($closest[0]['AddVan'])) + (($men-$closest[0]['Men'])*($closest[0]['AddMan']));
                } else {
                    $sql = "SELECT `vtiger_tariffhourlyset`.rate, `vtiger_tariffhourlyset`.men, `vtiger_tariffservices`.hourlyset_addmanrate  
							FROM `vtiger_tariffhourlyset` JOIN `vtiger_tariffservices` ON `vtiger_tariffhourlyset`.serviceid = `vtiger_tariffservices`.tariffservicesid 
							WHERE men <=? and serviceid=?";
                    $result = $db->pquery($sql, array($men, $selectedId));
                    
                    $info = array();
                    while ($row =& $result->fetchRow()) {
                        $info[] = array('Rate'=>$row[0], 'Men'=>$row[1], 'AddMan'=>$row[2]);
                    }
                    $closest = array();
                    
                    $prevDiff = $men;
                    foreach ($info as $line) {
                        $diff = $men - $line['Men'];
                        
                        if ($diff < $prevDiff) {
                            $closest[0] = $line;
                            $prevDiff = $diff;
                        }
                    }
                    $return['rate'] = $closest[0]['Rate'] + (($men-$closest[0]['Men'])*$closest[0]['AddMan']);
                }
                $rate = $return['rate'];
            }
            $sql = "SELECT * FROM `vtiger_quotes_hourlyset` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_hourlyset` (estimateid, serviceid, men, vans, hours, traveltime, rate) VALUES (?,?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $men, $vans, $hours, $traveltime, $rate));
            } else {
                $sql = "UPDATE `vtiger_quotes_hourlyset` SET men=?, vans=?, hours=?, traveltime=?, rate=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($men, $vans, $hours, $traveltime, $rate, $record, $selectedId));
            }
            $response->setResult($return);
        } elseif ($rate_type === 'HourlySimple') {
            $rate_type = 'Hourly Simple';
            $qty1 = $request->get('quantity');
            $qty2 = $request->get('hours');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'FlatCharge') {
            $rate_type = 'Flat Charge';
            $qty1 = null;
            $qty2 = null;
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type == 'CuFt') {
            $rate_type = 'Per Cu Ft';
            $qty1 = $request->get('cuft');
            $qty2 = null;
            $rate = $request->get('rate');

            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();

            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type == 'CuFtDay') {
            $rate_type = 'Per Cu Ft/Per Day';
            $qty1 = $request->get('cuft');
            $qty2 = $request->get('days');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type == 'CuFtMonth') {
            $rate_type = 'Per Cu Ft/Per Month';
            $qty1 = $request->get('cuft');
            $qty2 = $request->get('months');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'CWT') {
            $rate_type = 'Per CWT';
            $qty1 = $request->get('weight');
            $qty2 = null;
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'CWTDay') {
            $rate_type = 'Per CWT/Per Day';
            $qty1 = $request->get('weight');
            $qty2 = $request->get('days');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'CWTMonth') {
            $rate_type = 'Per CWT/Per Month';
            $qty1 = $request->get('weight');
            $qty2 = $request->get('months');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'PerQty') {
            $rate_type = 'Per Quantity';
            $qty1 = $request->get('quantity');
            $qty2 = null;
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'PerQtyDay') {
            $rate_type = 'Per Quantity/Per Day';
            $qty1 = $request->get('quantity');
            $qty2 = $request->get('days');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'PerQtyMonth') {
            $rate_type = 'Per Quantity/Per Month';
            $qty1 = $request->get('quantity');
            $qty2 = $request->get('months');
            $rate = $request->get('rate');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
        } elseif ($rate_type === 'ChargePerHundred') {
            $rate_type = 'Charge Per $100 (Valuation)';
            $qty1 = $request->get('amount');
            $qty2 = $request->get('deductible');
            if ($qty2 === 'Select an Option') {
                $qty2 = null;
            }
            $rate = $request->get('rate');
            $info = array();
            $info['rate'] = false;
            
            if ($rate === 'none') {
                $sql = "SELECT rate FROM `vtiger_tariffchargeperhundred` WHERE deductible=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty2, $selectedId));
                
                $info = array();
                while ($row =& $result->fetchRow()) {
                    $info['rate'] = $row[0];
                }
                if (!isset($info['rate'])) {
                    $info['rate'] = 0;
                }
                $rate = $info['rate'];
            }
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty1 : " . $qty1, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty2 : " . $qty2, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : " . $rate, FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_perunit` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_perunit` (estimateid, serviceid, qty1, qty2, rate, ratetype) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $qty1, $qty2, $rate, $rate_type));
            } else {
                $sql = "UPDATE `vtiger_quotes_perunit` SET qty1=?, qty2=?, rate=?, ratetype=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($qty1, $qty2, $rate, $rate_type, $record, $selectedId));
            }
            $response->setResult($info);
        } elseif ($rate_type === 'Valuation') {
            $rate_type = 'Tabled Valuation';
            $valuationtype = $request->get('valuationtype');
            $coverage = ($valuationtype==1) ? $request->get('coverage') : null;
            $amount = ($valuationtype==0) ? $request->get('amount') : null;
            $deductible = ($valuationtype==0) ? $request->get('deductible') : null;
            $rate = ($valuationtype==0) ? $request->get('rate') : null;
            
            //file_put_contents('logs/SaveLocalServices.log', "\n PRE \$valuationtype : ". print_r($valuationtype,true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n PRE \$coverage : ". print_r($coverage,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n PRE \$rate : ". print_r($rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n PRE \$deductible : ". print_r($deductible,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n PRE \$amount : ". print_r($amount,true), FILE_APPEND);

            $info = array();
            $info['rate'] = false;
            
            if ($rate === 'none') {
                $sql = "SELECT cost FROM `vtiger_tariffvaluations` WHERE deductible=? AND amount=? AND serviceid=?";
                $result = $db->pquery($sql, array($deductible, $amount, $selectedId));
                while ($row =& $result->fetchRow()) {
                    $info['rate'] = $row[0];
                }
                if (!isset($info['rate'])) {
                    $info['rate'] = 0;
                }
                $rate = $info['rate'];
            }
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$valuationtype : ". print_r($valuationtype,true), FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n \$coverage : ". print_r($coverage, true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : ". print_r($rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$deductible : ". print_r($deductible,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$amount : ". print_r($amount,true), FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_valuation` WHERE estimateid=? and serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_valuation` (estimateid, serviceid, released, released_amount, amount, deductible, rate) VALUES (?,?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $valuationtype, $coverage, $amount, $deductible, $rate));
            } else {
                $sql = "UPDATE `vtiger_quotes_valuation` SET released=?, released_amount=?, amount=?, deductible=?, rate=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($valuationtype, $coverage, $amount, $deductible, $rate, $record, $selectedId));
            }
            
            
            $response->setResult($info);
        } elseif ($rate_type === 'Bulky') {
            $rate_type = 'Bulky List';
            $description = $request->get('description');
            $qty = $request->get('qty');
            $weight = $request->get('weight');
            $rate = $request->get('rate');
            $bulkyid = $request->get('bulkyid');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$description : ". print_r($description,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$qty : ". print_r($qty,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$weight : ". print_r($weight,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : ". print_r($rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$bulkyid : ". print_r($bulkyid,true), FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_bulky` WHERE estimateid=? AND serviceid=? AND description=? AND bulky_id=?";
            $result = $db->pquery($sql, array($record, $selectedId, $description, $bulkyid));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_bulky` (estimateid, serviceid, description, qty, weight, rate, bulky_id) VALUES (?,?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $description, $qty, $weight, $rate, $bulkyid));
            } else {
                $sql = "UPDATE `vtiger_quotes_bulky` SET qty=?, weight=?, rate=? WHERE estimateid=? AND serviceid=? AND description=? AND bulky_id=?";
                $result = $db->pquery($sql, array($qty, $weight, $rate, $record, $selectedId, $description, $bulkyid));
            }
        } elseif ($rate_type === 'Packing') {
            $rate_type = 'Packing Items';
            $name = $request->get('name');
            $container_qty = $request->get('containerQty');
            $container_rate = $request->get('containerRate');
            $pack_qty = $request->get('packQty');
            $pack_rate = $request->get('packRate');
            $unpack_qty = $request->get('unpackQty');
            $unpack_rate = $request->get('unpackRate');
            $packing_id = $request->get('PackID');
            
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$name : ". print_r($name,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$container_qty : ". print_r($container_qty,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$container_rate : ". print_r($container_rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$pack_qty : ". print_r($pack_qty,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$pack_rate : ". print_r($pack_rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$unpack_qty : ". print_r($unpack_qty,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$unpack_rate : ". print_r($unpack_rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$packing_id : ". print_r($packing_id,true), FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_packing` WHERE estimateid=? AND serviceid=? AND name=? AND packing_id=?";
            $result = $db->pquery($sql, array($record, $selectedId, $name, $packing_id));
            $row = $result->fetchRow();
            //file_put_contents('logs/SaveLocalServices.log', "\n \$row : ". print_r($row,true), FILE_APPEND);

            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_packing` (estimateid, serviceid, name, container_qty, container_rate, pack_qty, pack_rate, unpack_qty, unpack_rate, packing_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $name, $container_qty, $container_rate, $pack_qty, $pack_rate, $unpack_qty, $unpack_rate, $packing_id));
            } else {
                $sql = "UPDATE `vtiger_quotes_packing` SET container_qty=?, container_rate=?, pack_qty=?, pack_rate=?, unpack_qty=?, unpack_rate=? WHERE estimateid=? AND serviceid=? AND name=? AND packing_id=?";
                $result = $db->pquery($sql, array($container_qty, $container_rate, $pack_qty, $pack_rate, $unpack_qty, $unpack_rate, $record, $selectedId, $name, $packing_id));
            }
        } elseif ($rate_type === 'Crating') {
            $rate_type = 'Crating Item';
            $crateid = $request->get('crateID');
            $description = $request->get('Description');
            $crating_qty = $request->get('CratingQty');
            $crating_rate = $request->get('CratingRate');
            $uncrating_qty = $request->get('UncratingQty');
            $uncrating_rate = $request->get('UncratingRate');
            $length = $request->get('Length');
            $width = $request->get('Width');
            $height = $request->get('Height');
            $inches_added = $request->get('InchesAdded');
            $line_item_id = $request->get('line_item_id');
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$crateid : " . $crateid, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$description : " . $description, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$crating_qty : " . $crating_qty, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$crating_rate : " . $crating_rate, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$uncrating_qty : " . $uncrating_qty, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$uncrating_rate : " . $uncrating_rate, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$length : " . $length, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$width : " . $width, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$height : " . $height, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$inches_added : " . $inches_added, FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$line_item_id : " . $line_item_id, FILE_APPEND);

            $info = array();
            $info['blank'] = false;
            
            if (empty($crateid)) {
                $info['blank'] = true;
            }
            if (empty($description)) {
                $info['blank'] = true;
            }
            if (empty($crating_qty)) {
                $info['blank'] = true;
            }
            if (empty($crating_rate)) {
                $info['blank'] = true;
            }
            if (empty($uncrating_qty)) {
                $info['blank'] = true;
            }
            if (empty($uncrating_rate)) {
                $info['blank'] = true;
            }
            if (empty($length)) {
                $info['blank'] = true;
            }
            if (empty($width)) {
                $info['blank'] = true;
            }
            if (empty($height)) {
                $info['blank'] = true;
            }
            if (empty($inches_added)) {
                $info['blank'] = true;
            }

            //file_put_contents('logs/SaveLocalServices.log', "\n \$info['blank'] : ", FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', $info['blank'] ? $info['blank']."\n" : "False\n", FILE_APPEND);

            if ($info['blank'] === false) {
                $sql = "SELECT * FROM `vtiger_quotes_crating` WHERE estimateid=? AND serviceid=? AND line_item_id=?";
                $result = $db->pquery($sql, array($record, $selectedId, $line_item_id));
                $row = $result->fetchRow();
                
                if ($row == null) {
                    $newRow[] = 'newRow'. $id . '-' . $rowNum;
                    $sql = "INSERT INTO `vtiger_quotes_crating` (estimateid, serviceid, crateid, description, crating_qty, crating_rate, uncrating_qty, uncrating_rate, length, width, height, inches_added, line_item_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $result = $db->pquery($sql, array($record, $selectedId, $crateid, $description, $crating_qty, $crating_rate, $uncrating_qty, $uncrating_rate, $length, $width, $height, $inches_added, $line_item_id));
                    //file_put_contents('logs/loadsave.log', "\n Adding : ".$fieldName, FILE_APPEND);
                    //file_put_contents('logs/loadsave.log', "\n \$newRow : ".print_r($newRow, true), FILE_APPEND);
                } else {
                    $sql = "UPDATE `vtiger_quotes_crating` SET crateid=?, description=?, crating_qty=?, crating_rate=?, uncrating_qty=?, uncrating_rate=?, length=?, width=?, height=?, inches_added=? WHERE line_item_id=? AND estimateid=? AND serviceid=?";
                    $result = $db->pquery($sql, array($crateid, $description, $crating_qty, $crating_rate, $uncrating_qty, $uncrating_rate, $length, $width, $height, $inches_added, $line_item_id, $record, $selectedId));
                    //file_put_contents('logs/loadsave.log', "\n Updating : ".$fieldName, FILE_APPEND);
                    //file_put_contents('logs/loadsave.log', "\n \$newRow : ".print_r($newRow, true), FILE_APPEND);
                }
            }
            
            $response->setResult($info);
        } elseif ($rate_type === 'BreakPoint') {
            $rate_type = 'Break Point Trans.';
            $mileage = $request->get('miles');
            $rate = $request->get('rate');
            $weight = $request->get('weight');
            
            $tempBracketMax;
            $tempMileage;
            $tempRate;
            $tempBreakpoint;
            $tempBracketMax;
            $tempWeight;
            
            $checkBreakPoint = true;
            
            $info = array();
            $info['rate'] = false;
            $info['calcWeight'] = false;
            
            $sql = "SELECT base_rate, break_point, to_weight FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
            $result = $db->pquery($sql, array($mileage, $mileage, $weight, $weight, $selectedId));
            if ($result->numRows() > 0) { //Check if record exists for weight and mileage
                $row = $result->fetchRow();
                $tempRate = $row[0];
                $tempBreakpoint = $row[1];
                $tempBracketMax = $row[2];
                $tempWeight = $weight;
                $tempMileage = $mileage;
            } else { //If record does not exist
                $sql = "SELECT to_weight FROM `vtiger_tariffbreakpoint` WHERE ? >= from_weight and ? <= to_weight and serviceid=?";
                $result = $db->pquery($sql, array($weight, $weight, $selectedId));
                if ($result->numRows() > 0) { //Check if weight is withing a record
                    $tempWeight = $weight;
                } else { //Check if weight is higher than max
                    $sql = "SELECT MAX(to_weight), from_weight FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                    $result = $db->pquery($sql, array($selectedId));
                    $row = $result->fetchRow();
                    if ($weight > $row[0]) { //Weight is higher than max, set to max weight bracket
                        $tempWeight = $row[0];
                        $checkBreakPoint  = false; //We know we are in the max weight bracket, no need to check break point
                    } else { //Weight is lower than lowest, set to lowest weight bracket
                        $sql = "SELECT MIN(from_weight) FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                        $result = $db->pquery($sql, array($selectedId));
                        $row = $result->fetchRow();
                        $tempWeight = $row[0];
                        $checkBreakPoint  = false; //We know we are lower than the lowest weight bracket, no need to check break point
                    }
                }
                
                $sql = "SELECT base_rate FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and serviceid=?";
                $result = $db->pquery($sql, array($mileage, $mileage, $selectedId));
                if ($result->numRows() > 0) { //Check if mileage is withing a record
                    $tempMileage = $mileage;
                } else { //Mileage is higher than max
                    $sql = "SELECT MAX(to_miles) FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                    $result = $db->pquery($sql, array($selectedId));
                    $row = $result->fetchRow();
                    $tempMileage = $row[0];
                }
                
                $sql = "SELECT base_rate, break_point, to_weight FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
                $result = $db->pquery($sql, array($tempMileage, $tempMileage, $tempWeight, $tempWeight, $selectedId));
                $row = $result->fetchRow();
                $tempRate = $row[0];
                $tempBreakpoint = $row[1];
                $tempBracketMax = $row[2];
            }

            if ($checkBreakPoint && ($tempBreakpoint < $tempWeight)) { //If breakpoint is greater than our weight
                $sql = "SELECT base_rate, from_weight, break_point FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
                $result = $db->pquery($sql, array($tempMileage, $tempMileage, $tempBracketMax + 1, $tempBracketMax + 1, $selectedId));
                if ($result->numRows() > 0) { //If record exists, set params
                    $row = $result->fetchRow();
                    $info['rate'] = $row[0];
                    $info['calcWeight'] = $row[1];
                    $tempBreakpoint = $row[2];
                } else { //We already have the highest bracket
                    $info['rate'] = $tempRate;
                    $info['calcWeight'] = $tempweight;
                }
            } else { //Set params
                $info['rate'] = $tempRate;
                $info['calcWeight'] = $weight;
            }
            $response->setResult($info);
                    
            $sql = "SELECT * FROM `vtiger_quotes_breakpoint` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_breakpoint` (estimateid, serviceid, mileage, weight, rate, breakpoint) VALUES (?,?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $mileage, $weight, $info['rate'], $info['calcWeight']));
            } else {
                $sql = "UPDATE `vtiger_quotes_breakpoint` SET mileage=?, weight=?, rate=?, breakpoint=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($mileage, $weight, $info['rate'], $info['calcWeight'], $record, $selectedId));
            }
        } elseif ($rate_type == 'CWTbyWeight') {
            $rate_type = 'CWT by Weight';
            $weight = $request->get('weight');
            $rate = $request->get('rate');
            
            $prevWeight = $request->get('prevWeight');
            
            if ($rate === 'none') {
                $sql = "SELECT rate FROM `vtiger_tariffcwtbyweight` WHERE ? >=from_weight AND ?<=to_weight AND serviceid=?";
                $result = $db->pquery($sql, array($weight, $weight, $selectedId));
                $row = $result->fetchRow();
                $rate = $row[0];
                
                $sql = "SELECT rate FROM `vtiger_tariffcwtbyweight` WHERE ? >=from_weight AND ?<=to_weight AND serviceid=?";
                $result = $db->pquery($sql, array($prevWeight, $prevWeight, $selectedId));
                $row = $result->fetchRow();
                $prevRate = $row[0];
            }
            
            $info = array();
            if ($prevRate === $rate) {
                $info['rate'] = false;
            } else {
                $info['rate'] = $rate;
            }
            
            //file_put_contents('logs/SaveLocalServices.log', "\n \$mileage : ". print_r($mileage,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$rate : ". print_r($rate,true), FILE_APPEND);
            //file_put_contents('logs/SaveLocalServices.log', "\n \$weight : ". print_r($weight,true), FILE_APPEND);

            $sql = "SELECT * FROM `vtiger_quotes_cwtbyweight` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, array($record, $selectedId));
            $row = $result->fetchRow();
            
            if ($row == null) {
                $sql = "INSERT INTO `vtiger_quotes_cwtbyweight` (estimateid, serviceid, weight, rate) VALUES (?,?,?,?,?)";
                $result = $db->pquery($sql, array($record, $selectedId, $weight, $rate));
            } else {
                $sql = "UPDATE `vtiger_quotes_cwtbyweight` SET weight=?, rate=? WHERE estimateid=? AND serviceid=?";
                $result = $db->pquery($sql, array($weight, $rate, $record, $selectedId));
            }
            
            $response->setResult($info);
        }
        $response->emit();
    }
}
