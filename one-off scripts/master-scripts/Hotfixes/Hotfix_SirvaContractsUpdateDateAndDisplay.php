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
 * The goal is to make the end_date field non-mandatory
 * set parent_contract to display in the summaryfield
 * move parent_contract to position one of the list view.
 *
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


print "<h2>Begin modifications to Contracts module. </h2>\n";
RemoveMandatoryContractsDate('Contracts', 'end_date');
ChangeDisplayOfParentContract('Contracts', 'parent_contract');
updateCVColumnListForContracts('Contracts', 'parent_contract', 0);
addExtraBlockToEstimates('Estimates', 'LBL_QUOTE_INFORMATION', 'parent_contract', 'LBL_QUOTES_PARENT_CONTRACT', 'vtiger_quotes');
addExtraBlockToEstimates('Estimates', 'LBL_QUOTE_INFORMATION', 'nat_account_no', 'LBL_QUOTES_NAT_ACCOUNT_NO', 'vtiger_quotes');
$fieldSeq = [
                'subject' => 1,
                'potential_id' => 2,
                'quote_no' => 3,
                'quotestage' => 4,
                'validtill' => 5,
                'contact_id' => 6,
                'account_id' => 7,
                'assigned_user_id' => 8,
                'createdtime' => 9,
                'modifiedtime' => 10,
                'business_line_est' => 11,
                'is_primary' => 12,
                'orders_id' => 13,
                'pre_tax_total' => 14,
                'modifiedby' => 15,
                'conversion_rate' => 16,
                'hdnDiscountAmount' => 17,
                'hdnS_H_Amount' => 18,
                'hdnSubTotal' => 19,
                'txtAdjustment' => 20,
                'hdnGrandTotal' => 21,
                'hdnTaxType' => 22,
                'hdnDiscountPercent' => 23,
                'currency_id' => 24,
                'load_date' => 25,
                'contract' => 26,
                'parent_contract' => 27,
                'nat_account_no' => 28,
                'billing_type' => 29,
                'agentid' => 30,
                'shipper_type' => 31,
                'move_type' => 32,
                'lead_type' => 33,
            ];
print "Reording Block in Estimates<br />";
reorderBlockEstimatesInformation($fieldSeq, 'LBL_QUOTE_INFORMATION', 'Estimates');

addExtraBlockToEstimates('Quotes', 'LBL_QUOTE_INFORMATION', 'parent_contract', 'LBL_QUOTES_PARENT_CONTRACT', 'vtiger_quotes');
addExtraBlockToEstimates('Quotes', 'LBL_QUOTE_INFORMATION', 'nat_account_no', 'LBL_QUOTES_NAT_ACCOUNT_NO', 'vtiger_quotes');
$fieldSeq = [
                'quote_no' => '3',
                'subject' => '1',
                'potential_id' => '2',
                'quotestage' => '4',
                'validtill' => '5',
                'contact_id' => '6',
                'carrier' => '8',
                'hdnSubTotal' => '9',
                'assigned_user_id1' => '11',
                'txtAdjustment' => '20',
                'hdnGrandTotal' => '14',
                'hdnTaxType' => '14',
                'hdnDiscountPercent' => '14',
                'hdnDiscountAmount' => '14',
                'hdnS_H_Amount' => '14',
                'account_id' => '16',
                'assigned_user_id' => '17',
                'createdtime' => '18',
                'modifiedtime' => '19',
                'modifiedby' => '22',
                'currency_id' => '20',
                'conversion_rate' => '21',
                'pre_tax_total' => '23',
                'business_line_est' => '24',
                'orders_id' => '25',
                'is_primary' => '26',
                'load_date' => '27',
                'contract' => '28',
                'parent_contract' => 29,
                'nat_account_no' => 30,
                'billing_type' => '31',
                'agentid' => '32',
                'shipper_type' => '33',
                'move_type' => '34',
                'lead_type' => '35',
            ];
print "Reording Block in Quotes<br />";
reorderBlockEstimatesInformation($fieldSeq, 'LBL_QUOTE_INFORMATION', 'Quotes');

print "<h2>END modifications to Contracts module. </h2>\n";

function RemoveMandatoryContractsDate($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $workingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($workingField) {
            $typeOfData = $workingField->typeofdata;
            $isMatch = preg_match('/~M/', $typeOfData);
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/~M/', '~O', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to NOT mandatory<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        ." WHERE `fieldid` = ? LIMIT 1";
                //print "$stmt\n";
                //print "$typeOfData, " . $workingField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $workingField->id]);
                print "<br>$moduleName $fieldName is converted to NOT mandatory<br>\n";
            } else {
                print "<br>$moduleName $fieldName is Already not mandatory<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}

function ChangeDisplayOfParentContract($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $workingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($workingField) {
            $summaryField = $workingField->summaryfield;
            if ($summaryField == 1) {
                print "Already set to 1 (which means display apparently)<br />\n";
            } else {
                print "<br>$moduleName $fieldName needs converting to display<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `summaryfield` = ?"
                        ." WHERE `fieldid` = ? LIMIT 1";
                $db->pquery($stmt, ['1', $workingField->id]);
                print "<br>$moduleName $fieldName is Set to display on the summary<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}

/*
 * function to update the column index for a particular fieldname to $position
 * by reordering the list and adding/moving the field information into it's place.
 *
 * @String $moduleName  module we're working with
 * @String $fieldName   field to move
 * @Int $position		place it should be. 0-96 apparently.
 */
function updateCVColumnListForContracts($moduleName, $fieldName, $position)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        if ($workingField = Vtiger_Field::getInstance($fieldName, $module)) {
            $cvID = false;
            $selectCV = 'SELECT * FROM `vtiger_customview` WHERE `viewname`= "All" AND `entitytype`=?';
            if ($result = $db->pquery($selectCV, [$moduleName])) {
                if ($row = $result->fetchRow()) {
                    $cvID = $row['cvid'];
                }
            }
            if ($cvID) {
                list($typeOfData) = explode('~', $workingField->typeofdata);
                $columnname = $workingField->table . ':'
                              . $fieldName . ':'
                              . $fieldName . ':'
                              . $moduleName . '_' . $workingField->label . ':'
                              . $typeOfData;
                $selectCVCol = 'SELECT * FROM `vtiger_cvcolumnlist` WHERE `cvid`=? ORDER BY `columnindex` DESC';
                $doNothing = false;
                $existing = [];
                if ($res = $db->pquery($selectCVCol, [$cvID, $columnname])) {
                    while ($colRow = $res->fetchRow()) {
                        //print '0: ' . $colRow['columnname'] . ' -- ' . $columnname . "<br />";
                        if ($colRow['columnname'] == $columnname) {
                            //print '0: ' . $colRow['columnindex'] . ' -- ' . $position . "<br />";
                            if ($colRow['columnindex'] == $position) {
                                $doNothing = true;
                                break;
                            } else {
                                $deleteExisting = 'DELETE FROM `vtiger_cvcolumnlist` WHERE `cvid` = ? AND `columnname` = ?';
                                //print "1: SQL: $updateRow;<br />";
                                //print "1: CVID: $cvID<br />CN : " . $columnname . "<br />";
                                $db->pquery($deleteExisting, [$cvID, $columnname]);
                            }
                        } else {
                            //not it...
                            $existing[$colRow['columnindex']] = $colRow['columnname'];
                        }
                    }

                    if (!$doNothing) {
                        foreach ($existing as $old_columnindex => $old_columnname) {
                            if ($old_columnindex >= $position) {
                                $updateRow =
                                    'UPDATE `vtiger_cvcolumnlist` SET `columnindex` = ? WHERE'
                                    . '`cvid` = ?'
                                    . ' AND `columnindex`=?'
                                    . ' AND `columnname`=?';
                                //print "2: $updateRow;<br />";
                                //print '2: pos: ' . ($old_columnindex + 1) . "<br /> CVID: " . $cvID . "<br /> CI: " . $old_columnindex . "<br />CN: " .$old_columnname . "<br />";
                                $db->pquery($updateRow, [$old_columnindex + 1, $cvID, $old_columnindex, $old_columnname]);
                            }
                        }
                        //[re]add the target row.
                        $updateRow = 'INSERT INTO `vtiger_cvcolumnlist` SET `columnindex` = ?, `cvid` = ?, `columnname` = ?';
                        //print "1: SQL: $updateRow;<br />";
                        //print '1: pos: ' . $position . "<br />CVID: " . $cvID . "<br />CN : " . $columnname . "<br />";
                        $db->pquery($updateRow, [$position, $cvID, $columnname]);
                        print "<br />Custom View is UPDATED for $moduleName<br />\n";
                    } else {
                        print "<br />Custom View is already CORRECT for $moduleName<br />\n";
                    }
                }
            } else {
                print "<br />failed to find a Custom View for $moduleName<br />\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}

function addExtraBlockToEstimates($moduleName, $blockLabel, $fieldName, $fieldLabel, $fieldTable)
{
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $workingField = Vtiger_Field::getInstance($fieldName, $module);
            if ($workingField) {
                //do nothing!
                print "<br />Field: $fieldName already exists in $moduleName<br />\n";
            } else {
                print "<br />adding field: $fieldName to $moduleName :: $blockLabel<br />\n";
                $field1 = new Vtiger_Field();
                $field1->label = $fieldLabel;
                $field1->name = $fieldName;
                $field1->table = $fieldTable;
                $field1->column = $fieldName;
                $field1->columntype = 'varchar(11)';
                $field1->uitype = 1;
                $field1->typeofdata = 'V~O';
                $field1->displaytype = 1;
                $field1->quickcreate = 0;
                $field1->presence = 2;
                $block->addField($field1);
            }
        } else {
            print "<br />failed to find BLOCK: $blockLabel in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}

function reorderBlockEstimatesInformation($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end  = [];
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