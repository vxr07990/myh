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
 *
 * The goal is to add the contracts_allowed field to the TariffManger in the LBL_TARIFFMANAGER_INFORMATION block
 * reorder the block for contracts_allowed to be second
 *
 * The goal is to add the contracts_only field to the TariffManager in the LBL_TARIFFMANAGER_INFORMATION block
 * reorder the block for contracts_only to be second
 *
 * add label to: languages/en_us/TarrifManager.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//process TariffManager update
$moduleName = 'TariffManager';
$blockName  = 'LBL_TARIFFMANAGER_INFORMATION';
print "<h2>START add contracts_allowed and contracts_only to $moduleName module. </h2>\n";
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
    } else {
        $fields   = [
            'contracts_allowed' => [
                'label'      => 'LBL_TARIFFMANAGER_CONTRACT_ALLOWED',
                'name'       => 'contracts_allowed',
                'table'      => 'vtiger_tariffmanager',
                'column'     => 'contracts_allowed',
                'columntype' => 'VARCHAR(3)',
                'uitype'     => 56,
                'typeofdata' => 'C~O',
                //'summaryfield' => 1,
                'block'      => $block,
            ],
            'contracts_only'    => [
                'label'      => 'LBL_TARIFFMANAGER_CONTRACT_ONLY',
                'name'       => 'contracts_only',
                'table'      => 'vtiger_tariffmanager',
                'column'     => 'contracts_only',
                'columntype' => 'VARCHAR(3)',
                'uitype'     => 56,
                'typeofdata' => 'C~O',
                //'summaryfield' => 1,
                'block'      => $block,
            ],
        ];
        $rField   = addFields_SACFTT($fields, $module, true);
        $fieldSeq = [
            'tariffmanagername' => 1,
            'tariff_type'       => 2,
            'contracts_allowed' => 3,
            'contracts_only'    => 4,
        ];
        echo "<li>Reordering block</li><br>";
        reorderBlockEstimatesInformation_SACFTT($fieldSeq, $blockName, $moduleName);
        echo "<li>finished update to $moduleName</li><br>";
    }
}
print "<h2>END add contracts_allowed and contracts_only field to $moduleName module. </h2>\n";
/*
 * from EMAIL:
Estimate: EST348 = National Account (NAT) (UI shows Effective Tariff-this has been reported in IGC ID 13512 the field label should be Pricing Tariff) the ONLY valid 'PRICING TARIFFS' for NAT are:  AVL:400N, 400N/104G, AVL-2A,TX Max3,CA Max4,Intra-400N **** and for NVL: 400N, 400N/104G, NAVL-12A,TX Max3,CA Max4,Intra-400N
*/
$allowed = [
    '400N Base'    => 1,
    '400N/104G'    => 1,
    '400NG'        => 1,
    'ALLV-2A'      => 1,
    'Intra - 400N' => 1,
    'NAVL-12A'     => 1,
    'Max 3'        => 1,
    'Max 4'        => 1,
    'Allied Express' => 0,
    'Blue Express'   => 0,
    'Local/Intra'    => 0,
    'Pricelock'      => 0,
    'GRR'            => 0,
    'TPG'            => 0,
    'GRR'            => 0,
    'UAS'            => 0,
];
echo "<li>Verify Contracts_allowed is set/unset for tariffs</li><br>";
$db   = PearDatabase::getInstance();
$stmt = 'SELECT tariffmanagerid,tariffmanagername,custom_tariff_type,contracts_allowed FROM `vtiger_tariffmanager`';
$result = $db->pquery($stmt);
if ($result) {
    while ($row = $result->fetchRow()) {
        if ($row['contracts_allowed'] != $allowed[$row['custom_tariff_type']]) {
            //They don't match the rules.
            $stmt = 'UPDATE `vtiger_tariffmanager` SET '
                  . ' `contracts_allowed` = ?'
                  . ' WHERE `tariffmanagerid` = ?'
                  . ' LIMIT 1';
            $db->pquery($stmt, [$allowed[$row['custom_tariff_type']], $row['tariffmanagerid']]);
            print "<li>Updating: " . $row['tariffmanagername'] . " to have contracts_allowed = " . $allowed[$row['custom_tariff_type']] . "</li><br />";
        }
    }
}
//END process TariffManager update
function addFields_SACFTT($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
            if ($data['replaceExisting']) {
                $db = PearDatabase::getInstance();
                if ($field0->uitype != $data['uitype']) {
                    echo "Updating $field_name to uitype=".$data['uitype']." for lead source module<br />\n";
                    $db   = PearDatabase::getInstance();
                    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['uitype'], $field0->id]);
                }
                //update the typeofdata
                if ($field0->typeofdata != $data['typeofdata']) {
                    echo "Updating $field_name to be a have typeofdata = '".$data['typeofdata']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['typeofdata'], $field0->id]);
                }
                if (
                    array_key_exists('setRelatedModules', $data) &&
                    $data['setRelatedModules'] &&
                    count($data['setRelatedModules']) > 0
                ) {
                    echo "<li> setting relation to existing $field_name</li>";
                    $field0->setRelatedModules($data['setRelatedModules']);
                }
            }
        } else {
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

function reorderBlockEstimatesInformation_SACFTT($fieldSeq, $blockLabel, $moduleName)
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
                        Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.
                                                   $name.
                                                   '" AND fieldid = '.$field->id);
                        $max++;
                    }
                }
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";