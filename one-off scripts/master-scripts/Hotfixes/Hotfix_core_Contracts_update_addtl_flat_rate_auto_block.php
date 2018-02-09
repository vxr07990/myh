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
 * Updates to Contracts based on design mock up.
 *
 * create Block: Additional Flat Rate Auto Charges.
 *
 * Add:
 * Oversize Vehicle Charge
 * Vehicle Inoperable Charge
 * Oversize Vehicle Charge
 * Overtime Charge
 * Diversion Fee
 * Waiting Time Per Hour
 * Maximium Waiting Time Charge
 * SIT Per Day
 * Pickup or Delivery Charge
 * Pickup or Delivery Mileage
 * Pickup or Delivery Additional Per Mile Charge
 * Waive EAC?
 * Waive Fuel Surcharge?
 * Waive IRR?
 * Waive Origin/Destination Service Charge?
 *
 * add labels for this in: languages/en_us/Contracts.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'LBL_CONTRACTS_ADDITIONAL_FLAT_RATE_AUTO';
foreach (['Contracts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        //just jamming this in here
        if (!$block) {
            //create new block.
            $block        = new Vtiger_Block();
            $block->label = $blockName;
            $module->addBlock($block);
        }
        
        //no harm in making sure.
        if ($block) {
            /*
            //move these fields
            $moveFields = [
            ];
            moveFields_CCUDAFRAB($moveFields, $module, $block->id);
            */
            //add these fields:
            $addFields = [
            'oversize_vehicle_charge' => [
                'label'           => 'LBL_CONTRACTS_OVERSIZE_VEHICLE_CHARGE',
                'name'            => 'oversize_vehicle_charge',
                'table'           => 'vtiger_contracts',
                'column'          => 'oversize_vehicle_charge',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'vehicle_inoperable_charge' => [
                'label'           => 'LBL_CONTRACTS_VEHICLE_INOPERABLE_CHARGE',
                'name'            => 'vehicle_inoperable_charge',
                'table'           => 'vtiger_contracts',
                'column'          => 'vehicle_inoperable_charge',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            /*
             * I think this is a typo on the mock up
            'oversize_vehicle_charge_2' => [
                'label'           => 'LBL_CONTRACTS_OVERSIZE_VEHICLE_CHARGE_2',
                'name'            => 'oversize_vehicle_charge_2',
                'table'           => 'vtiger_contracts',
                'column'          => 'oversize_vehicle_charge_2',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            */
            'auto_overtime_charge' => [
                'label'           => 'LBL_CONTRACTS_AUTO_OVERTIME_CHARGE',
                'name'            => 'auto_overtime_charge',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_overtime_charge',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'auto_diversion_fee' => [
                'label'           => 'LBL_CONTRACTS_AUTO_DIVERSION_FEE',
                'name'            => 'auto_diversion_fee',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_diversion_fee',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'auto_wait_time_per_hour' => [
                'label'           => 'LBL_CONTRACTS_AUTO_WAIT_TIME_PER_HOUR',
                'name'            => 'auto_wait_time_per_hour',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_wait_time_per_hour',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'auto_max_wait_time_charge' => [
                'label'           => 'LBL_CONTRACTS_AUTO_MAX_WAIT_TIME_CHARGE',
                'name'            => 'auto_max_wait_time_charge',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_max_wait_time_charge',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'auto_sit_per_day' => [
                'label'           => 'LBL_CONTRACTS_AUTO_SIT_PER_DAY',
                'name'            => 'auto_sit_per_day',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_sit_per_day',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
            'auto_pickup_delivery_charge' => [
                'label'           => 'LBL_CONTRACTS_AUTO_PU_DEL_CHARGE',
                'name'            => 'auto_pickup_delivery_charge',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_pickup_delivery_charge',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
             'auto_pickup_delivery_mileage' => [
                'label'           => 'LBL_CONTRACTS_AUTO_PU_DEL_MILEAGE',
                'name'            => 'auto_pickup_delivery_mileage',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_pickup_delivery_mileage',
                'columntype'      => 'varchar(10)',
                'uitype'          => 7,
                'typeofdata'      => 'I~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false
            ],
            'auto_pickup_delivery_addl_mile_charge' => [
                'label'           => 'LBL_CONTRACTS_AUTO_PU_DEL_ADDL_MILE_CHARGE',
                'name'            => 'auto_pickup_delivery_addl_mile_charge',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_pickup_delivery_addl_mile_charge',
                'columntype'      => 'DECIMAL(7,2)',
                'uitype'          => 71,
                'typeofdata'      => 'N~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
                'auto_waive_eac'         => [
                'label'           => 'LBL_CONTRACTS_AUTO_WAIVE_EAC',
                'name'            => 'auto_waive_eac',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_waive_eac',
                'columntype'      => 'varchar(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
                'auto_waive_fuel_surcharge'         => [
                'label'           => 'LBL_CONTRACTS_AUTO_WAIVE_FUEL_SURCHARGE',
                'name'            => 'auto_waive_fuel_surcharge',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_waive_fuel_surcharge',
                'columntype'      => 'varchar(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
                'auto_waive_irr'         => [
                'label'           => 'LBL_CONTRACTS_AUTO_WAIVE_IRR',
                'name'            => 'auto_waive_irr',
                'table'           => 'vtiger_contracts',
                'column'          => 'auto_waive_irr',
                'columntype'      => 'varchar(3)',
                'uitype'          => 56,
                'typeofdata'      => 'C~O',
                'displaytype'     => '1',
                'block'           => $block,
                'replaceExisting' => false,
            ],
                'auto_waive_org_dest_service_charge'         => [
                    'label'           => 'LBL_CONTRACTS_AUTO_WAIVE_ORG_DEST_SERVICE_CHARGE',
                    'name'            => 'auto_waive_org_dest_service_charge',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'auto_waive_org_dest_service_charge',
                    'columntype'      => 'varchar(3)',
                    'uitype'          => 56,
                    'typeofdata'      => 'C~O',
                    'displaytype'     => '1',
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
            ];
            addFields_CCUDAFRAB($addFields, $module);
            
            //reorder fields
            $orderFieldSeq = [
                'oversize_vehicle_charge',
                'vehicle_inoperable_charge',
                'auto_overtime_charge',
                'auto_diversion_fee',
                'auto_wait_time_per_hour',
                'auto_max_wait_time_charge',
                'auto_sit_per_day',
                'auto_pickup_delivery_charge',
                'auto_pickup_delivery_mileage',
                'auto_pickup_delivery_addl_mile_charge',
                'auto_waive_eac',
                'auto_waive_fuel_surcharge',
                'auto_waive_irr',
                'auto_waive_org_dest_service_charge',
            ];

            echo "<li>unhiding fields for block $blockName</li><br>";
            unhideFields_CCUDAFRAB($orderFieldSeq, $module);
            
            echo "<li>Reordering block $blockName</li><br>";
            reorderFieldsByBlock_CCUDAFRAB($orderFieldSeq, $blockName, $moduleName);

            //Hide these
            //$hideFields = [
            //];
            //hideFields_CCUDAFRAB ($hideFields, $module);
        }
        print "<h2>finished add fields to $moduleName module. </h2>\n";
    }
}

function moveFields_CCUDAFRAB($fields, $module, $newBlockID)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->block != $newBlockID) {
                    echo "Updating $field_name to be a have blockID = $newBlockID <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `block` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$newBlockID, $field0->id]);
                }
            }
        }
    }
    return false;
}

//END process update
function hideFields_CCUDAFRAB($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 1) {
                    echo "Updating $field_name to be a have presence = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
    return false;
}

function unhideFields_CCUDAFRAB($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 2) {
                    echo "Updating $field_name to be a have presence = 2 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['2', $field0->id]);
                }
            }
        }
    }
    return false;
}

//END process update
function addFields_CCUDAFRAB($fields, $module)
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

                //update the label
                if ($data['label'] && $field0->label != $data['label']) {
                    echo "Updating $field_name to be a have label = '".$data['label']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `label` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['label'], $field0->id]);
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

function reorderFieldsByBlock_CCUDAFRAB($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end = [];
            $seq = 1;
            foreach ($fieldSeq as $name) {
                if ($name && $field = Vtiger_Field::getInstance($name, $module)) {
                    $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                    $result = $db->pquery($sql, [$seq, $block->id]);
                    if ($result) {
                        while ($row = $result->fetchRow()) {
                            $push_to_end[] = $row['fieldname'];
                        }
                    }
                    $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                    $db->pquery($updateStmt, [$seq++, $field->id, $block->id]);
                }
                unset($field);
            }
            //push anything that might have gotten added and isn't on the list to the end of the block
            $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
            foreach ($push_to_end as $name) {
                //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
                if (!in_array($name, $fieldSeq)) {
                    $field = Vtiger_Field::getInstance($name, $module);
                    if ($field) {
                        $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                        $db->pquery($updateStmt, [$max++, $field->id, $block->id]);
                        $max++;
                    }
                }
            }
        }
    }
}

function reorderBlocks_CCUDAFRAB($blockSeq, $module)
{
    $db = PearDatabase::getInstance();
    if ($module && is_array($blockSeq)) {
        $push_to_end = [];
        $sequence = 1;
        foreach ($blockSeq as $blockLabel) {
            if ($blockLabel && $block = Vtiger_Block::getInstance($blockLabel, $module)) {
                //block exists so we are good to move it.
                $sql    = 'SELECT blocklabel FROM `vtiger_blocks` WHERE sequence = ? AND blockid = ?';
                $result = $db->pquery($sql, [$sequence, $block->id]);
                if ($result) {
                    while ($row = $result->fetchRow()) {
                        $push_to_end[] = $row['blocklabel'];
                    }
                }
                $updateStmt = 'UPDATE `vtiger_blocks` SET `sequence` = ? WHERE `blockid` = ? AND `tabid` = ?';
                $db->pquery($updateStmt, [$sequence++, $block->id, $module->getId()]);
            } else {
                print "Didn't find: $blockLabel in " . $module->getName() . " to reorder<br/>\n";
            }
        }
        
        //push anything that might have gotten added and isn't on the list to the end of the block
        $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_blocks` WHERE `tabid` = ? AND `blockid` = ?', [$module->getId(), $block->id])->fetchRow()[0] + 1;
        foreach ($push_to_end as $name) {
            //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
            if (!in_array($name, $blockSeq)) {
                if ($block = Vtiger_Block::getInstance($blockLabel, $module)) {
                    $updateStmt = 'UPDATE `vtiger_blocks` SET `sequence` = ? WHERE `blockid` = ? AND `tabid` = ?';
                    $db->pquery($updateStmt, [$max++, $block->id, $module->getId()]);
                }
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";