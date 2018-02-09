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
 * Update Block: Tariff Information
 *
 * Discount Type picklist:
 *  bottom line discount, peak/non­peak discount, variable discount, line item discount.
 * bottom line discount
 * peak discount
 * non-peak discount
 * variable discount from
 * variable discount to
 * linehaul discount
 * packing discount
 * accessorial discount
 * bottom line distribution discount
 * fixed effective date
 * effective date
 * fixed irr
 * irr
 * fixed fuel surcharge
 * fuel surcharge
 * fixed eac
 * eac
 * waive peak rates
 * waive eac
 * contract min weight
 *
 * move to this block
 * min_weight
 *
 * add labels for this in: languages/en_us/Contracts.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'LBL_CONTRACTS_TARIFF';
foreach (['Contracts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        if ($block) {
            
            //move these fields
            $moveFields = [
                'min_weight',
            ];
            moveFields_CCUTI($moveFields, $module, $block->id);

            //add these fields:
            $addFields = [
                'discount_type' => [
                    'label'           => 'LBL_CONTRACTS_DISCOUNT_TYPE',
                    'name'            => 'discount_type',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'discount_type',
                    'columntype'      => 'varchar(100)',
                    'uitype'          => 16,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => '1',
                    'block'           => $block,
                    'replaceExisting' => false,
                    'picklist'     => [
                                    'Bottom Line Discount',
                                    'Peak/Non­Peak Discount',
                                    'Variable Discount',
                                    'Line Item Discount',
                                    ],
                ],
                'variable_discount_from' => [
                    'label'           => 'LBL_CONTRACTS_VARIABLE_DISCOUNT_FROM',
                    'name'            => 'variable_discount_from',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'variable_discount_from',
                    'columntype'      => 'varchar(15)',
                    'uitype'          => 9,
                    'typeofdata'      => 'N~O',
                    'displaytype'     => '1',
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'variable_discount_to' => [
                    'label'           => 'LBL_CONTRACTS_VARIABLE_DISCOUNT_TO',
                    'name'            => 'variable_discount_to',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'variable_discount_to',
                    'columntype'      => 'varchar(15)',
                    'uitype'          => 9,
                    'typeofdata'      => 'N~O',
                    'displaytype'     => '1',
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
            ];
            addFields_CCUTI($addFields, $module);
            
            //reorder fields
            $orderFieldSeq = [
            'discount_type',
            'bottom_line_disc',
            'peak_discount',
            'non_peak_discount',
            'variable_discount_from',
            'variable_discount_to',
            'linehaul_disc',
            'packing_disc',
            'accessorial_disc',
            'bottom_line_distribution_discount',
            'fixed_eff_date',
            'effective_date',
            'fixed_irr',
            'irr_charge',
            'fuel_surcharge_type',
            'fuel_charge',
            'fixed_eac',
            'fixed_eac_percent',
            'waive_peak_rates',
            'waive_eac_rates',
            'min_weight',
            ];

            echo "<li>unhiding fields for block $blockName</li><br>";
            unhideFields_CCUTI($orderFieldSeq, $module);
            
            echo "<li>Reordering block $blockName</li><br>";
            reorderFieldsByBlock_CCUTI($orderFieldSeq, $blockName, $moduleName);

            $hideFields = [
                'fixed_fuel',
                'sit_disc',
                'fuel_disc',
                'sit_distribution_discount',
            ];
            hideFields_CCUTI($hideFields, $module);
        }
        print "<h2>finished add fields to $moduleName module. </h2>\n";
    }
}

function moveFields_CCUTI($fields, $module, $newBlockID)
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
function hideFields_CCUTI($fields, $module)
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

function unhideFields_CCUTI($fields, $module)
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
function addFields_CCUTI($fields, $module)
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

function reorderFieldsByBlock_CCUTI($fieldSeq, $blockLabel, $moduleName)
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

function reorderBlocks_CCUTI($blockSeq, $module)
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