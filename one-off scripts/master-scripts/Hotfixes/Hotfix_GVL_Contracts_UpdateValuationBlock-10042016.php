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


$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';


$moduleName = 'Contracts';
$blockName = 'LBL_CONTRACTS_VALUATION';
$module = Vtiger_Module::getInstance($moduleName);
$valuationBlock = Vtiger_Block::getInstance($blockName, $module);

if ($module && $valuationBlock) {
    $field1 = Vtiger_Field::getInstance('additional_valuation', $module);
    $field2 = Vtiger_Field::getInstance('total_valuation', $module);
    $field3 = Vtiger_Field::getInstance('valuation_deductible', $module);
    $field4 = Vtiger_Field::getInstance('min_val_per_lb', $module);
    $field5 = Vtiger_Field::getInstance('free_frv', $module);
    $field6 = Vtiger_Field::getInstance('free_frv_amount', $module);
    $field7 = Vtiger_Field::getInstance('maximum_rvp', $module);
    $field8 = Vtiger_Field::getInstance('rvp_flat_fee', $module);
    $field9 = Vtiger_Field::getInstance('rvp_per_1000', $module);
    $field10 = Vtiger_Field::getInstance('rvp_per_1000_sit', $module);

    $field11 = Vtiger_Field::getInstance('cargo_protection_type', $module);
    $field12 = Vtiger_Field::getInstance('free_fvp_allowed', $module);
    $field13 = Vtiger_Field::getInstance('free_fvp_amount', $module);

    $field15 = Vtiger_Field::getInstance('valuation_amount', $module);
    $field16 = Vtiger_Field::getInstance('valuation_deductible_amount', $module);
    $field17 = Vtiger_Field::getInstance('valuation_discounted', $module);
    $field18 = Vtiger_Field::getInstance('valuation_discount_amount', $module);


    hideFieldCUVB($field1);
    hideFieldCUVB($field2);
    hideFieldCUVB($field11);
    hideFieldCUVB($field12);
    hideFieldCUVB($field13);

    hideFieldCUVB($field15);
    hideFieldCUVB($field16);
    hideFieldCUVB($field17);
    hideFieldCUVB($field18);



    $db = PearDatabase::getInstance();
    $sql = 'SELECT contractsid, cargo_protection_type FROM `vtiger_contracts` WHERE cargo_protection_type IS NOT NULL';
    $result = $db->pquery($sql);
    $data = [];
    if ($result) {
        while ($row =& $result->fetchRow()) {
            $data[$row['contractsid']] = $row['cargo_protection_type'];
        }
    }

    $fields   = [
        'valuation_deductible'  => [
            'label'           => 'LBL_CONTRACTS_VALUATIONDEDUCTIBLE',
            'name'            => 'valuation_deductible',
            'table'           => 'vtiger_contracts',
            'column'          => 'valuation_deductible',
            'columntype'      => 'VARCHAR(255)',
            'uitype'          => 16,
            'typeofdata'      => 'V~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'min_val_per_lb'  => [
            'label'           => 'LBL_CONTRACTS_VAL_MINPRICE',
            'name'            => 'min_val_per_lb',
            'table'           => 'vtiger_contracts',
            'column'          => 'min_val_per_lb',
            'columntype'      => 'decimal(22,8)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'free_frv'  => [
            'label'           => 'LBL_CONTRACTS_FREEFRV',
            'name'            => 'free_frv',
            'table'           => 'vtiger_contracts',
            'column'          => 'free_frv',
            'columntype'      => 'VARCHAR(3)',
            'uitype'          => 56,
            'typeofdata'      => 'V~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'free_frv_amount'  => [
            'label'           => 'LBL_CONTRACTS_FREEFRVAMOUNT',
            'name'            => 'free_frv_amount',
            'table'           => 'vtiger_contracts',
            'column'          => 'free_frv_amount',
            'columntype'      => 'DECIMAL(10,2)',
            'uitype'          => 71,
            'typeofdata'      => 'V~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'maximum_rvp'  => [
            'label'           => 'LBL_CONTRACTS_MAXIMUMRVP',
            'name'            => 'maximum_rvp',
            'table'           => 'vtiger_contracts',
            'column'          => 'maximum_rvp',
            'columntype'      => 'decimal(22,8)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'rvp_flat_fee'  => [
            'label'           => 'LBL_CONTRACTS_RVPFLATFEE',
            'name'            => 'rvp_flat_fee',
            'table'           => 'vtiger_contracts',
            'column'          => 'rvp_flat_fee',
            'columntype'      => 'decimal(22,2)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'rvp_per_1000'  => [
            'label'           => 'LBL_CONTRACTS_RVPPER1000',
            'name'            => 'rvp_per_1000',
            'table'           => 'vtiger_contracts',
            'column'          => 'rvp_per_1000',
            'columntype'      => 'decimal(22,2)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'rvp_per_1000_sit'  => [
            'label'           => 'LBL_CONTRACTS_RVPPER1000SIT',
            'name'            => 'rvp_per_1000_sit',
            'table'           => 'vtiger_contracts',
            'column'          => 'rvp_per_1000_sit',
            'columntype'      => 'decimal(22,2)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'presence'        => 2,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
    ]
    ;

    addFields_CUVB($fields, $module);

    $orderFieldSeq = [
        'valuation_deductible',
        'min_val_per_lb',
        'free_frv',
        'free_frv_amount',
        'maximum_rvp',
        'rvp_flat_fee',
        'rvp_per_1000',
        'rvp_per_1000_sit'
    ];

    echo "<li>Reordering block $blockName</li><br>";
    reorderFieldsByBlock_CUVB($orderFieldSeq, $blockName, $moduleName);
}

function addFields_CUVB($fields, $module)
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
                    $res = $db->pquery($stmt);
                    if ($row = $res->fetchRow()) {
                        do {
                            if ($value['Field'] == $field_name) {
                                if (strtolower($value['Type']) != strtolower($data['columntype'])) {
                                    echo "Updating $field_name to be a " . $data['columntype'] . " type.<br />\n";
                                    $stmt = 'ALTER TABLE `' . $field0->table . '` MODIFY COLUMN `' . $field_name . '` ' . $data['columntype'] . ' DEFAULT NULL';
                                    $db->pquery($stmt);
                                }
                                //we're only affecting the $field_name so if we find it just break
                                break;
                            }
                        } while ($value = $res->fetchRow());
                    } else {
                        $stmt = 'ALTER TABLE `' . $field0->table . '` ADD COLUMN `' . $field_name . '` ' . $data['columntype'] . ' DEFAULT NULL';
                        $db->pquery($stmt);
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

function hideFieldCUVB($field)
{
    if (!$field) {
        return;
    }
    $db = PearDatabase::getInstance();
    $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?',
                [$field->id]);
}

function reorderFieldsByBlock_CUVB($fieldSeq, $blockLabel, $moduleName)
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


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";