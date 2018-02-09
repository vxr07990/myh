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
 * Add Billing address to orders in: LBL_ORDERS_INVOICE
 *
 * add labels for this in: languages/en_us/Orders.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//create the dang table!
if (!Vtiger_Utils::CheckTable('vtiger_ordersbillads')) {
    echo "<li>creating vtiger_ordersbillads </li><br>";
    Vtiger_Utils::CreateTable('vtiger_ordersbillads',
'(
orderaddressid int(19) NOT NULL DEFAULT "0",
bill_city varchar(30) DEFAULT NULL,
bill_code varchar(30) DEFAULT NULL,
bill_country varchar(30) DEFAULT NULL,
bill_state varchar(30) DEFAULT NULL,
bill_street varchar(250) DEFAULT NULL,
bill_pobox varchar(30) DEFAULT NULL,
PRIMARY KEY (`orderaddressid`)
)', true);
}

$blockName  = 'LBL_ORDERS_INVOICE';
foreach (['Orders'] as $moduleName) {
    print "<h2>START add Billing address fields to $moduleName module. </h2>\n";
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        if (!$block) {
            echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
        } else {
            $fields   = [
                'bill_street'  => [
                    'label'           => 'LBL_ORDERS_BILLINGADDRESS',
                    'name'            => 'bill_street',
                    'table'           => 'vtiger_ordersbillads',
                    'column'          => 'bill_street',
                    'columntype'      => 'VARCHAR(250)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'bill_city'    => [
                    'label'           => 'LBL_ORDERS_BILLINGCITY',
                    'name'            => 'bill_city',
                    'table'           => 'vtiger_ordersbillads',
                    'column'          => 'bill_city',
                    'columntype'      => 'VARCHAR(30)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'bill_state'   => [
                    'label'           => 'LBL_ORDERS_BILLINGSTATE',
                    'name'            => 'bill_state',
                    'table'           => 'vtiger_ordersbillads',
                    'column'          => 'bill_state',
                    'columntype'      => 'VARCHAR(30)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'bill_code'    => [
                    'label'           => 'LBL_ORDERS_BILLINGZIPCODE',
                    'name'            => 'bill_code',
                    'table'           => 'vtiger_ordersbillads',
                    'column'          => 'bill_code',
                    'columntype'      => 'VARCHAR(30)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'bill_pobox'   => [
                    'label'           => 'LBL_ORDERS_BILLINGPOBOX',
                    'name'            => 'bill_pobox',
                    'table'           => 'vtiger_ordersbillads',
                    'column'          => 'bill_pobox',
                    'columntype'      => 'VARCHAR(30)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'bill_country' => [
                    'label'           => 'LBL_ORDERS_BILLINGCOUNTRY',
                    'name'            => 'bill_country',
                    'table'           => 'vtiger_ordersbillads',
                    'column'          => 'bill_country',
                    'columntype'      => 'VARCHAR(30)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
            ];
            $rField   = addFields_ABAO($fields, $module, true);
            $fieldSeq = [
                'bill_street'    => '1',
                'bill_city'      => '2',
                'bill_state'     => '3',
                'bill_code'      => '4',
                'bill_pobox'     => '5',
                'bill_country'   => '6',
                'pricing_type'   => '7',
                'bill_weight'    => '8',
                'pricing_mode'   => '10',
                'payment_type'   => '11',
                'invoice_status' => '12',
            ];
            echo "<li>Reordering block</li><br>";
            reorderBlockEstimatesInformation_ABAO($fieldSeq, $blockName, $moduleName);
            echo "<li>finished update to $moduleName</li><br>";
        }
    }
    print "<h2>END add fields to $moduleName module. </h2>\n";
}

//END process update
function addFields_ABAO($fields, $module)
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

function reorderBlockEstimatesInformation_ABAO($fieldSeq, $blockLabel, $moduleName)
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