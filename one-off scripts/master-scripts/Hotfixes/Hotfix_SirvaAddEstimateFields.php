<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/*
 *
 *Goals:
 * under pricing split by origin/destination
 * fix OT load OT unload in Estimates to be checkboxes under Pricing
 * //accesorial_ot_loading       LBL_ACC_OT_LOADING
 * //accesorial_ot_unloading     LBL_ACC_OT_UNLOADING
 *
 * //accesorial_expedited_service  LBL_EXPEDIATED_SERVICE
 * accesorial_exclusive_vehicle  LBL_ACC_EXCLUSIVE_VEHICLE
 * accesorial_space_reservation  LBL_ACC_SPACE_RESERVATION
 *
 * space_reserve_bool already exists.
 *
 * add Exclusive Use of Vehicle checkbox under pricing.
 * add Space Reservation checkbox under pricing.
 * update Rush Shipment Fee to be hidden presence => 1?
 * //rush_shipment_fee  LBL_RUSH_SHIPMENT_FEE
 *
 * add label to: languages/en_us/Estimates.php or Quotes.php both?
 *
 * add OT pack OT unpack in Estimates to be checkboxes under Pricing
 * accesorial_ot_packing       LBL_ACC_OT_PACKING
 * accesorial_ot_unpacking     LBL_ACC_OT_UNPACKING
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName  = 'LBL_QUOTES_ACCESSORIALDETAILS';
foreach (['Estimates', 'Quotes'] as $moduleName) {
    print "<h2>START add Extra checkboxes to $moduleName module. </h2>\n";
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        if (!$block) {
            echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
        } else {
            $fields   = [
            'accesorial_exclusive_vehicle' => [
                'label'           => 'LBL_ACC_EXCLUSIVE_VEHICLE',
                'name'            => 'accesorial_exclusive_vehicle',
                'table'           => 'vtiger_quotes',
                'column'          => 'accesorial_exclusive_vehicle',
                'columntype'      => 'VARCHAR(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                //'summaryfield' => 1,
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'accesorial_space_reserve_bool' => [
                'label'           => 'LBL_SPACE_RESERVE_BOOL',
                'name'            => 'accessorial_space_reserve_bool',
                'table'           => 'vtiger_quotes',
                //'column'          => 'space_reserve_bool',
                'column'          => 'accessorial_space_reserve_bool',
                'columntype'      => 'VARCHAR(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                //'summaryfield' => 1,
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'accesorial_ot_packing'        => [
                'label'           => 'LBL_ACC_OT_PACKING',
                'name'            => 'accesorial_ot_packing',
                'table'           => 'vtiger_quotes',
                'column'          => 'accesorial_ot_packing',
                'columntype'      => 'VARCHAR(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                //'summaryfield' => 1,
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'accesorial_ot_unpacking'      => [
                'label'           => 'LBL_ACC_OT_UNPACKING',
                'name'            => 'accesorial_ot_unpacking',
                'table'           => 'vtiger_quotes',
                'column'          => 'accesorial_ot_unpacking',
                'columntype'      => 'VARCHAR(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                //'summaryfield' => 1,
                'block'           => $block,
                'replaceExisting' => false,
            ],
                'accesorial_ot_loading'   => [
                    'label'               => 'LBL_ACC_OT_LOADING',
                    'name'                => 'accesorial_ot_loading',
                    'table'               => 'vtiger_quotes',
                    'column'              => 'accesorial_ot_loading',
                    'columntype'          => 'VARCHAR(3)',
                    'uitype'              => 56,
                    'typeofdata'          => 'C~O',
                    //'summaryfield' => 1,
                    'block'               => $block,
                    'replaceExisting'     => true,
                    'updateDatabaseTable' => true,
                ],
                'accesorial_ot_unloading' => [
                    'label'               => 'LBL_ACC_OT_UNLOADING',
                    'name'                => 'accesorial_ot_unloading',
                    'table'               => 'vtiger_quotes',
                    'column'              => 'accesorial_ot_unloading',
                    'columntype'          => 'VARCHAR(3)',
                    'uitype'              => 56,
                    'typeofdata'          => 'C~O',
                    //'summaryfield' => 1,
                    'block'               => $block,
                    'replaceExisting'     => true,
                    'updateDatabaseTable' => true,
                ],
                'rush_shipment_fee'       => [
                    'label'           => 'LBL_RUSH_SHIPMENT_FEE',
                    'name'            => 'rush_shipment_fee',
                    'table'           => 'vtiger_quotes',
                    'column'          => 'rush_shipment_fee',
                    'columntype'      => 'VARCHAR(3)',
                    'uitype'          => 56,
                    'typeofdata'      => 'C~O',
                    //'summaryfield' => 1,
                    'presence'        => 1,
                    'block'           => $block,
                    'replaceExisting' => true,
                ],
            ];
            $rField   = addFields_AEF($fields, $module, true);
            $fieldSeq = [
                'acc_shuttle_origin_weight'    => '1',
                'acc_shuttle_dest_weight'      => '2',
                'acc_shuttle_origin_applied'   => '3',
                'acc_shuttle_dest_applied'     => '4',
                'acc_shuttle_origin_ot'        => '5',
                'acc_shuttle_dest_ot'          => '6',
                'acc_shuttle_origin_over25'    => '7',
                'acc_shuttle_dest_over25'      => '8',
                'acc_shuttle_origin_miles'     => '9',
                'acc_shuttle_dest_miles'       => '10',
                'acc_ot_origin_weight'         => '11',
                'acc_ot_dest_weight'           => '12',
                'acc_ot_origin_applied'        => '13',
                'acc_ot_dest_applied'          => '14',
                'acc_selfstg_origin_weight'    => '15',
                'acc_selfstg_dest_weight'      => '16',
                'acc_selfstg_origin_applied'   => '17',
                'acc_selfstg_dest_applied'     => '18',
                'acc_selfstg_origin_ot'        => '19',
                'acc_selfstg_dest_ot'          => '20',
                'acc_exlabor_origin_hours'     => '21',
                'acc_exlabor_dest_hours'       => '22',
                'apply_exlabor_rate_origin'    => '23',
                'apply_exlabor_rate_dest'      => '24',
                'exlabor_rate_origin'          => '25',
                'exlabor_rate_dest'            => '26',
                'exlabor_flat_origin'          => '27',
                'exlabor_flat_dest'            => '28',
                'acc_exlabor_ot_origin_hours'  => '29',
                'acc_exlabor_ot_dest_hours'    => '30',
                'apply_exlabor_ot_rate_origin' => '31',
                'apply_exlabor_ot_rate_dest'   => '32',
                'exlabor_ot_flat_origin'       => '33',
                'exlabor_ot_flat_dest'         => '34',
                'exlabor_ot_rate_origin'       => '35',
                'exlabor_ot_rate_dest'         => '36',
                'acc_wait_origin_hours'        => '37',
                'acc_wait_dest_hours'          => '38',
                'acc_wait_ot_origin_hours'     => '39',
                'acc_wait_ot_dest_hours'       => '40',
                'bulky_article_changes'        => '41',
                'rush_shipment_fee'            => '42',
                'accesorial_ot_loading'        => '43',
                'accesorial_ot_unloading'      => '44',
'accesorial_ot_packing' => '45',
'accesorial_ot_unpacking' => '46',

                'consumption_fuel'             => '47',
                'accesorial_fuel_surcharge'    => '48',
                
                'accesorial_expedited_service' => '49',
                'express_truckload'            => '50',
'accesorial_exclusive_vehicle' => '51',
'accesorial_space_reserve_bool' => '52',
                'space_reserve_bool' => '53',
            ];

            if ($moduleName == 'Quotes') {
                $fieldSeq = [
                    'acc_shuttle_origin_weight'        => '1',
                    'acc_shuttle_dest_weight'          => '2',
                    'acc_shuttle_origin_applied'       => '3',
                    'acc_shuttle_dest_applied'         => '4',
                    'acc_shuttle_origin_ot'            => '5',
                    'acc_shuttle_dest_ot'              => '6',
                    'acc_shuttle_origin_over25'        => '7',
                    'acc_shuttle_dest_over25'          => '8',
                    'acc_shuttle_origin_miles'         => '9',
                    'acc_shuttle_dest_miles'           => '10',
                    'acc_ot_origin_weight'             => '11',
                    'acc_ot_dest_weight'               => '12',
                    'acc_ot_origin_applied'            => '13',
                    'acc_ot_dest_applied'              => '14',
                    'acc_selfstg_origin_weight'        => '15',
                    'acc_selfstg_dest_weight'          => '16',
                    'acc_selfstg_origin_applied'       => '17',
                    'acc_selfstg_dest_applied'         => '18',
                    'acc_selfstg_origin_ot'            => '19',
                    'acc_selfstg_dest_ot'              => '20',
                    'acc_exlabor_origin_hours'         => '21',
                    'acc_exlabor_dest_hours'           => '22',
                    'acc_exlabor_ot_origin_hours'      => '23',
                    'acc_exlabor_ot_dest_hours'        => '24',
                    'acc_wait_origin_hours'            => '25',
                    'acc_wait_dest_hours'              => '26',
                    'acc_wait_ot_origin_hours'         => '27',
                    'acc_wait_ot_dest_hours'           => '28',
                    'bulky_article_changes'            => '29',
                    'elevator_origin_occurrence'       => '30',
                    'elevator_destination_occurrence'  => '31',
                    'elevator_origin_CTW'              => '32',
                    'elevator_destination_CTW'         => '33',
                    'stair_origin_occurrence'          => '34',
                    'stair_destination_occurrence'     => '35',
                    'stair_origin_CTW'                 => '36',
                    'stair_destination_CTW'            => '37',
                    'longcarry_origin_occurrence'      => '38',
                    'longcarry_destination_occurrence' => '39',
                    'longcarry_origin_CTW'             => '40',
                    'longcarry_destination_CTW'        => '41',
                    'rush_shipment_fee'                => '42',
                    'appliance_service'                => '43',
                    'appliance_reservice'              => '44',
                    'ori_sit2_date_in'                 => '45',
                    'des_sit2_date_in'                 => '46',
                    'ori_sit2_pickup_date'             => '47',
                    'des_sit2_pickup_date'             => '48',
                    'ori_sit2_number_days'             => '49',
                    'des_sit2_number_days'             => '50',
                    'ori_sit2_weight'                  => '51',
                    'des_sit2_weight'                  => '52',
                    'ori_sit2_container_or_warehouse'  => '53',
                    'des_sit2_container_or_warehouse'  => '54',
                    'ori_sit2_container_number'        => '55',
                    'des_sit2_container_number'        => '56',
                    'accesorial_ot_loading'            => '57',
                    'accesorial_ot_unloading'          => '58',
'accesorial_ot_packing' => '59',
'accesorial_ot_unpacking' => '60',
                    'accesorial_fuel_surcharge'        => '61',
'accesorial_expedited_service'     => '62',
'accesorial_exclusive_vehicle' => '63',
'accesorial_space_reserve_bool' => '64',
                    'space_reserve_bool' => '65',
                    'space_reserve_cf'                 => '66',
                    'apply_exlabor_rate_origin'        => '67',
                    'exlabor_rate_origin'              => '68',
                    'exlabor_flat_origin'              => '69',
                    'apply_exlabor_ot_rate_origin'     => '70',
                    'exlabor_ot_rate_origin'           => '71',
                    'exlabor_ot_flat_origin'           => '72',
                    'apply_exlabor_rate_dest'          => '73',
                    'exlabor_rate_dest'                => '74',
                    'exlabor_flat_dest'                => '75',
                    'apply_exlabor_ot_rate_dest'       => '76',
                    'exlabor_ot_rate_dest'             => '77',
                    'exlabor_ot_flat_dest'             => '78',
                    'express_truckload'                => '79',
                    'consumption_fuel'                 => '80',
                    'grr'                              => '81',
                    'grr_override_amount'              => '82',
                    'grr_override'                     => '83',
                    'grr_cp'                           => '84',
                ];
            }
            echo "<li>Reordering block</li><br>";
            reorderBlockEstimatesInformation_AEF($fieldSeq, $blockName, $moduleName);
            echo "<li>finished update to $moduleName</li><br>";
        }
    }
    print "<h2>END add fields to $moduleName module. </h2>\n";
}

//END process update
function addFields_AEF($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $createBlock = true;
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
            if ($data['replaceExisting'] && $data['block']->id == $field0->getBlockId()) {
                $createBlock = false;
                $db          = PearDatabase::getInstance();
                if ($data['uitype'] && $field0->uitype != $data['uitype']) {
                    echo "Updating $field_name to uitype=".$data['uitype']." for lead source module<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['uitype'], $field0->id]);
                }

                //update the typeofdata
                if ($data['typeofdata'] && $field0->typeofdata != $data['typeofdata']) {
                    echo "Updating $field_name to be a have typeofdata = '".$data['typeofdata']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['typeofdata'], $field0->id]);
                }

                //update the presence
                if ($data['presence'] && $field0->presence != $data['presence']) {
                    echo "Updating $field_name to be a have presence = '".$data['presence']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['presence'], $field0->id]);
                }
                
                if (
                    array_key_exists('setRelatedModules', $data) &&
                    $data['setRelatedModules'] &&
                    count($data['setRelatedModules']) > 0
                ) {
                    echo "<li> setting relation to existing $field_name</li>";
                    $field0->setRelatedModules($data['setRelatedModules']);
                }
                if ($data['updateDatabaseTable'] && $data['columntype']) {
                    //hell you have to fix the created table!  ... sigh.
                    $stmt = 'EXPLAIN `'.$field0->table.'` `'.$field_name.'`';
                    if ($res = $db->pquery($stmt)) {
                        while ($value = $res->fetchRow()) {
                            if ($value['Field'] == $field_name) {
                                if (strtolower($value['Type']) != strtolower($data['columntype'])) {
                                    echo "Updating $field_name to be a " . $data['columntype'] . " type.<br />\n";
                                    $db   = PearDatabase::getInstance();
                                    $stmt = 'ALTER TABLE `' . $field0->table . '` MODIFY COLUMN `' . $field_name . '` ' . $data['columntype'] . ' DEFAULT NULL';
                                    $db->pquery($stmt);
                                }
                                //we're only affecting the $field_name so if we find it just break
                                break;
                            }
                        }
                    } else {
                        echo "NO $field_name column in The actual table?<br />\n";
                    }
                }
            } elseif ($data['block']->id == $field0->getBlockId()) {
                //already exists in this block
                $createBlock = false;
            } else {
                //need to add to a new block.
                $createBlock = true;  //even though it already is.
            }
        }
        
        if ($createBlock) {
            echo "<li> Attempting to add $field_name</li><br />";
            //@TODO: check data validity
            $field0 = new Vtiger_Field();
            //these are assumed to be filled.
            $field0->label        = $data['label'];
            $field0->name         = $data['name'];
            $field0->table        = $data['table'];
            $field0->column       = $data['column'];
            $field0->columntype   = $data['columntype'];
            $field0->uitype       = $data['uitype'];
            $field0->typeofdata   = $data['typeofdata'];
            $field0->summaryfield = ($data['summaryfield']?1:0);
            $field0->defaultvalue = $data['defaultvalue'];
            //these three MUST have values or it doesn't pop vtiger_field.
            $field0->displaytype = ($data['displaytype']?$data['displaytype']:1);
            $field0->readonly    = ($data['readonly']?$data['readonly']:1);
            $field0->presence    = ($data['presence']?$data['presence']:2);
            $data['block']->addField($field0);
            if ($data['setEntityIdentifier'] == 1) {
                $module->setEntityIdentifier($field0);
            }
            //just completely ensure there's stuff in the array before doing it.
            if (
                array_key_exists('setRelatedModules', $data) &&
                $data['setRelatedModules'] &&
                count($data['setRelatedModules']) > 0
            ) {
                $field0->setRelatedModules($data['setRelatedModules']);
            }
            if (
                array_key_exists('picklist', $data) &&
                $data['picklist'] &&
                count($data['picklist']) > 0
            ) {
                $field0->setPicklistValues($data['picklist']);
            }
            $returnFields[$field_name] = $field0;
        }
    }

    return $returnFields;
}

function reorderBlockEstimatesInformation_AEF($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end = [];
            foreach ($fieldSeq as $name => $seq) {
                $field = Vtiger_Field::getInstance($name, $module);
                if ($field) {
                    $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                    $result = $db->pquery($sql, [$seq, $block->id]);
                    if ($result) {
                        while ($row = $result->fetchRow()) {
                            $push_to_end[] = $row[0];
                        }
                    }
                    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.
                                               '" AND fieldid = '.$field->id);
                }
                unset($field);
            }
            //push anything that might have gotten added and isn't on the list to the end of the block
            $max =
                $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] +
                1;
            foreach ($push_to_end as $name) {
                //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
                if (!array_key_exists($name, $fieldSeq)) {
                    $field = Vtiger_Field::getInstance($name, $module);
                    if ($field) {
                        Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max
                                                   .' WHERE fieldname= "'. $name. '" AND fieldid = '.$field->id);
                        $max++;
                    }
                }
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";