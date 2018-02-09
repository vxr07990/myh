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
 * The goal is to add the apn field to the Accounts in the LBL_ACCOUNT_INFORMATION block
 * reorder the block for APN to be second
 * set apn to display in the summary view
 *
 * put apn in Contracts in the LBL_CONTRACTS_BILLING block
 * linked to the accounts LBL_CONTRACTS_APN
 * reorder the block so the APN link is first.
 *
 * Add a field for Rate per 100 to contracts in the LBL_CONTRACTS_VALUATION block
 * labeld: LBL_CONTRACTS_RATE_PER_100
 *
 * Update nat_account_no in LBL_CONTRACTS_INFORMATION block
 * to be a uitype 10 of accounts
 *
 * add label to: languages/en_us/Accounts.php
 * add label to: languages/en_us/Contracts.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//process Accounts update
$moduleName = 'Accounts';
$blockName  = 'LBL_ACCOUNT_INFORMATION';
print "<h2>START add APN to $moduleName module. </h2>\n";
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
    } else {
        $fields   = [
            'apn' => [
                'label'        => 'LBL_ACCOUNTS_APN',
                'name'         => 'apn',
                'table'        => 'vtiger_account',
                'column'       => 'apn',
                'columntype'   => 'VARCHAR(50)',
                'uitype'       => 1,
                'typeofdata'   => 'V~O',
                'summaryfield' => 1,
                'block'        => $block,
                //'setRelatedModules' => ['LeadSourceManager'],
                //'replaceExisting'	=> false,
            ],
        ];
        $rField   = addFields_SAATA($fields, $module, true);
        $fieldSeq = [
            'accountname'         => 1,
            'apn'                 => 2,
            'account_no'          => 3,
            'phone'               => 5,
            'website'             => 4,
            'fax'                 => 7,
            'tickersymbol'        => 6,
            'otherphone'          => 9,
            'account_id'          => 8,
            'email1'              => 11,
            'employees'           => 10,
            'email2'              => 12,
            'ownership'           => 13,
            'rating'              => 15,
            'industry'            => 14,
            'siccode'             => 17,
            'accounttype'         => 16,
            'annual_revenue'      => 19,
            'emailoptout'         => 18,
            'notify_owner'        => 21,
            'assigned_user_id'    => 20,
            'createdtime'         => 23,
            'modifiedtime'        => 22,
            'modifiedby'          => 24,
            'isconvertedfromlead' => 25,
            'created_user_id'     => 26,
            'agentid'             => 27,
        ];
        echo "<li>Reordering block</li><br>";
        reorderBlockEstimatesInformation_SAATA($fieldSeq, $blockName, $moduleName);
        echo "<li>finished update to $moduleName</li><br>";
    }
}
print "<h2>END add apn field to $moduleName module. </h2>\n";
//END process Accounts update

//process Contracts update
$moduleName = 'Contracts';
$blockName  = 'LBL_CONTRACTS_BILLING';
print "<h2>START add APN to $moduleName module. </h2>\n";
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
    } else {
        $fields   = [
            'billing_apn' => [
                'label'             => 'LBL_CONTRACTS_APN',
                'name'              => 'billing_apn',
                'table'             => 'vtiger_contracts',
                'column'            => 'billing_apn',
                'columntype'        => 'VARCHAR(50)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'summaryfield'      => 1,
                'block'             => $block,
                'setRelatedModules' => ['Accounts'],
                //'replaceExisting'	=> false,
            ],
        ];
        $rField   = addFields_SAATA($fields, $module, true);
        $fieldSeq = [
            'billing_apn'      => 1,
            'billing_contact'  => 2,
            'billing_address1' => 3,
            'billing_address2' => 4,
            'billing_city'     => 5,
            'billing_state'    => 6,
            'billing_zip'      => 7,
            'billing_pobox'    => 8,
            'billing_country'  => 9,
        ];
        echo "<li>Reordering block</li><br>";
        reorderBlockEstimatesInformation_SAATA($fieldSeq, $blockName, $moduleName);
        echo "<li>finished update to $moduleName</li><br>";
    }
}
print "<h2>END add billing_apn field to $moduleName module. </h2>\n";
//END process Contracts update

//process Contracts update
$moduleName = 'Contracts';
$blockName  = 'LBL_CONTRACTS_INFORMATION';
print "<h2>START update nat_account_no in $moduleName module. </h2>\n";
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
    } else {
        $fields   = [
            'nat_account_no' => [
                'label'             => 'LBL_CONTRACTS_APN',
                'name'              => 'nat_account_no',
                'table'             => 'vtiger_contracts',
                'column'            => 'nat_account_no',
                'columntype'        => 'VARCHAR(11)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'summaryfield'      => 1,
                'block'             => $block,
                'setRelatedModules' => ['Accounts'],
                'replaceExisting'    => true,
            ],
        ];
        $rField   = addFields_SAATA($fields, $module, true);
        echo "<li>finished update to $moduleName</li><br>";
    }
}
print "<h2>END add nat_account_no field to $moduleName module. </h2>\n";
//END process Contracts update

/*
 * Add a field for Rate per 100 to contracts in the LBL_CONTRACTS_VALUATION block
 * labeld: LBL_CONTRACTS_RATE_PER_100
 */
//process Contracts update
$moduleName = 'Contracts';
$blockName  = 'LBL_CONTRACTS_VALUATION';
print "<h2>START update rate_per_100 in $moduleName module. </h2>\n";
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
    } else {
        $fields   = [
            'rate_per_100' => [
                'label'             => 'LBL_CONTRACTS_RATE_PER_100',
                'name'              => 'rate_per_100',
                'table'             => 'vtiger_contracts',
                'column'            => 'rate_per_100',
                'columntype'        => 'VARCHAR(50)',
                'uitype'            => 1,
                'typeofdata'        => 'V~O',
                'summaryfield'      => 1,
                'block'             => $block,
                //'setRelatedModules' => ['Accounts'],
                //'replaceExisting'	=> true,
            ],
        ];
        $rField   = addFields_SAATA($fields, $module, true);
        echo "<li>finished update to $moduleName</li><br>";
    }
}
print "<h2>END add rate_per_100 field to $moduleName module. </h2>\n";
//END process Contracts update


function addFields_SAATA($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
            if ($data['replaceExisting']) {
                $db   = PearDatabase::getInstance();

                if ($field0->uitype != $data['uitype']) {
                    echo "Updating $field_name to uitype=" . $data['uitype'] . " for lead source module<br />\n";
                    $db   = PearDatabase::getInstance();
                    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['uitype'], $field0->id]);
                }

                //update the typeofdata
                if ($field0->typeofdata != $data['typeofdata']) {
                    echo "Updating $field_name to be a have typeofdata = '" . $data['typeofdata'] . "'.<br />\n";
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

function reorderBlockEstimatesInformation_SAATA($fieldSeq, $blockLabel, $moduleName)
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