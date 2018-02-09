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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 8/29/2016
 * Time: 2:55 PM
 */


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

$picklistOrder = [
    'Full Value Protection',
    'Vehicle Coverage',
    'Carrier Base Liability',
    'Vehicle Transport',
    'Full Replacement Value',
];

$module = Vtiger_Module::getInstance('Contracts');
$valuationBlock = Vtiger_Block::getInstance('LBL_CONTRACTS_VALUATION', $module);

if ($module && $valuationBlock) {
    $field1 = Vtiger_Field::getInstance('additional_valuation', $module);
    $field2 = Vtiger_Field::getInstance('cargo_protection_type', $module);
    $field3 = Vtiger_Field::getInstance('free_fvp_allowed', $module);
    $field4 = Vtiger_Field::getInstance('free_fvp_amount', $module);
    $field5 = Vtiger_Field::getInstance('maximum_rvp', $module);
    $field6 = Vtiger_Field::getInstance('min_val_per_lb', $module);
    $field7 = Vtiger_Field::getInstance('rvp_flat_fee', $module);
    $field8 = Vtiger_Field::getInstance('rvp_per_1000', $module);
    $field9 = Vtiger_Field::getInstance('rvp_per_1000_sit', $module);
    $field10 = Vtiger_Field::getInstance('valuation_deductible', $module);

    hideFieldUVBIC($field2);
    hideFieldUVBIC($field3);
    hideFieldUVBIC($field4);
    hideFieldUVBIC($field5);
    hideFieldUVBIC($field6);
    hideFieldUVBIC($field7);
    hideFieldUVBIC($field8);
    hideFieldUVBIC($field9);

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
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
            ],
        'valuation_amount'  => [
            'label'           => 'LBL_CONTRACTS_VALUATIONAMOUNT',
            'name'            => 'valuation_amount',
            'table'           => 'vtiger_contracts',
            'column'          => 'valuation_amount',
            'columntype'      => 'decimal(22,8)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'valuation_deductible_amount'  => [
            'label'           => 'LBL_CONTRACTS_VALUATIONDEDUCTIBLEAMOUNT',
            'name'            => 'valuation_deductible_amount',
            'table'           => 'vtiger_contracts',
            'column'          => 'valuation_deductible_amount',
            'columntype'      => 'VARCHAR(100)',
            'uitype'          => 16,
            'typeofdata'      => 'V~O',
            'displaytype'     => 1,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'valuation_discounted'  => [
            'label'           => 'LBL_CONTRACTS_VALUATIONDISCOUNTED',
            'name'            => 'valuation_discounted',
            'table'           => 'vtiger_contracts',
            'column'          => 'valuation_discounted',
            'columntype'      => 'VARCHAR(3)',
            'uitype'          => 56,
            'typeofdata'      => 'V~O',
            'displaytype'     => 1,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'valuation_discount_amount'  => [
            'label'           => 'LBL_CONTRACTS_VALUATIONDISCOUNTAMOUNT',
            'name'            => 'valuation_discount_amount',
            'table'           => 'vtiger_contracts',
            'column'          => 'valuation_discount_amount',
            'columntype'      => 'DECIMAL(10,2)',
            'uitype'          => 71,
            'typeofdata'      => 'V~O',
            'displaytype'     => 1,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'additional_valuation'  => [
            'label'           => 'LBL_CONTRACTS_ADDITIONALVALUATION',
            'name'            => 'additional_valuation',
            'table'           => 'vtiger_contracts',
            'column'          => 'additional_valuation',
            'columntype'      => 'decimal(22,8)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        'total_valuation'  => [
            'label'           => 'LBL_CONTRACTS_TOTALVALUATION',
            'name'            => 'total_valuation',
            'table'           => 'vtiger_contracts',
            'column'          => 'total_valuation',
            'columntype'      => 'decimal(22,2)',
            'uitype'          => 71,
            'typeofdata'      => 'N~O',
            'displaytype'     => 1,
            'block'           => $valuationBlock,
            'replaceExisting' => true,
            'updateDatabaseTable' => true,
        ],
        ]
        ;

    addFields_UVBIC($fields, $module);

    foreach ($data as $id => $type) {
        if (in_array($type, $picklistOrder)) {
            continue;
        }

        $sql = 'UPDATE `vtiger_contracts` SET valuation_deductible=? WHERE contractsid=?';
        if ($type == 'CBL') {
            $params = ['Carrier Base Liability', $id];
            $db->pquery($sql, $params);
        } elseif (strpos($type, 'FVP') !== false) {
            $params = ['Full Value Protection', $id];
            $db->pquery($sql, $params);
        } elseif ($type == 'RVP') {
            $params = ['Replacement Value Protection', $id];
            $db->pquery($sql, $params);
        }
    }
}

function addFields_UVBIC($fields, $module)
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

function hideFieldUVBIC($field)
{
    if (!$field) {
        return;
    }
    $db = PearDatabase::getInstance();
    $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?',
                [$field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";