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


include_once('vtlib/Vtiger/Module.php');

//Set up the tab/module
$moduleName = 'Employees';
$module = Vtiger_Module::getInstance('Employees');
$db = PearDatabase::getInstance();
$conditionColumn = "employee_type";
$conditionVals = ['IC Transportation Contractor', 'I/C Co-driver', 'TSC Employee', 'Terminal Service Contractor', 'I/C and TSC',
                  'Contractor Surveyor', 'I/C Shuttle', 'IC Labor'];

if (!$module) {
    echo "<br />$moduleName not found. Exiting.<br />";
    return;
}

$terminationBlockName = 'LBL_TERMINATION_INFO';

$terminationBlock = Vtiger_Block::getInstance($terminationBlockName, $module);

if ($terminationBlock) {
    echo '<p>LBL_TERMINATION_INFO Block exists</p>';
} else {
    $terminationBlock = new Vtiger_Block();
    $terminationBlock->label = 'LBL_TERMINATION_INFO';
    $module->addBlock($terminationBlock);
    echo '<p>LBL_TERMINATION_INFO Block Added</p>';
}

$blockSeq = [
    'LBL_CUSTOM_INFORMATION',
    'LBL_EMPLOYEES_INFORMATION',
    'LBL_TERMINATION_INFO',
    'LBL_EMPLOYEES_DETAILINFO',
    'LBL_CONTRACTORS_DETAILINFO',
    'LBL_EMPLOYEES_EMERGINFO',
    'LBL_EMPLOYEES_LICENSEINFO',
    'LBL_EMPLOYEES_SAFETYDETAILS',
    'LBL_EMPLOYEES_AVAILABILITY',
    'LBL_EMPLOYEES_PHOTO',
    'LBL_DRIVER_INFORMATION'
];

reorderBlocksGEATB($blockSeq, $module);

$empTermDateField = Vtiger_Field::getInstance('employee_tdate', $module);

if ($empTermDateField){
    echo "The employee_tdate field exists<br>\n";
    moveFieldToBlockGEATB($empTermDateField, $terminationBlock);
    AddMandatoryGEATB($module, $empTermDateField);
} else {
    echo "The employee_tdate field was not found<br>\n";
}

//Hiding redundant contractor fields
$hideFields = ['contractor_tdate', 'contractor_status'];
$syncFields = ['employee_tdate', 'employee_status'];

$i = 0;

foreach ($hideFields as $fieldName) {
    $hideField = Vtiger_Field::getInstance($fieldName, $module);
    $syncField = Vtiger_Field::getInstance($syncFields[$i], $module);
    if (!$hideField || !$syncField) {
        echo "Unable to update $fieldName or $syncFields[$i]<br>\n";
        continue;
    }
    $db->pquery('UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?', [$hideField->id]);
    $i++;
    synchronizeColumnsGEATB($hideField->column, $syncField->column, $moduleName, $conditionColumn, $conditionVals);
}
$fieldRehire = Vtiger_Field::getInstance('rehire_eligibility', $module);
if ($fieldRehire) {
    echo "The rehire_eligibility field already exists<br>\n";
} else {
    $fieldRehire             = new Vtiger_Field();
    $fieldRehire->label      = 'LBL_REHIRE_ELIGIBILITY';
    $fieldRehire->name       = 'rehire_eligibility';
    $fieldRehire->table      = 'vtiger_employees';
    $fieldRehire->column     = 'rehire_eligibility';
    $fieldRehire->columntype = 'VARCHAR(3)';
    $fieldRehire->uitype     = 16;
    $fieldRehire->typeofdata = 'V~M';
    $terminationBlock->addField($fieldRehire);
    $fieldRehire->setPicklistValues(array('Yes', 'No'));
}
$fieldEligibilityDate = Vtiger_Field::getInstance('rehire_eligibility_date', $module);
if ($fieldEligibilityDate) {
    echo "The rehire_eligibility_date field already exists<br>\n";
} else {
    $fieldEligibilityDate             = new Vtiger_Field();
    $fieldEligibilityDate->label      = 'LBL_REHIRE_ELIGIBILITY_DATE';
    $fieldEligibilityDate->name       = 'rehire_eligibility_date';
    $fieldEligibilityDate->table      = 'vtiger_employees';
    $fieldEligibilityDate->column     = 'rehire_eligibility_date';
    $fieldEligibilityDate->columntype = 'DATE';
    $fieldEligibilityDate->uitype     = 5;
    $fieldEligibilityDate->typeofdata = 'D~M';
    $terminationBlock->addField($fieldEligibilityDate);
}


$reasonField = Vtiger_Field::getInstance('associate_termination_reason', $module);
if ($reasonField) {
    moveFieldToBlockGEATB($reasonField, $terminationBlock);
    echo "The associate_termination_reason field already exists<br>\n";
} else {
    $reasonField             = new Vtiger_Field();
    $reasonField->label      = 'LBL_ASSOCIATE_TERMINATION_REASON';
    $reasonField->name       = 'associate_termination_reason';
    $reasonField->table      = 'vtiger_employees';
    $reasonField->column     = 'associate_termination_reason';
    $reasonField->columntype = 'TEXT';
    $reasonField->uitype     = 19;
    $reasonField->typeofdata = 'V~M';
    $terminationBlock->addField($reasonField);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

function moveFieldToBlockGEATB($field, $block){
    if($field->getBlockID() != $block->id){
        $db = PearDatabase::getInstance();
        $db->pquery('UPDATE `vtiger_field` SET block = ? WHERE fieldid =?', [$block->id, $field->id]);
        echo "$field->name moved to $block->label block";
    } else {
        echo "$field->name already in $block->label block";
    }
}

function AddMandatoryGEATB($module, $field)
{
    $db = PearDatabase::getInstance();
    $typeOfData = $field->typeofdata;
    $isMatch = preg_match('/~O/', $typeOfData);
    if ($isMatch === false) {
        print "ERROR: couldn't preg_match?";
    } elseif ($isMatch) {
        $typeOfData = preg_replace('/~O/', '~M', $typeOfData);
        print "<br>$module->name $field->name needs converting to mandatory<br>\n";
        $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                //. " `quickcreate` = 1"
                ." WHERE `fieldid` = ? LIMIT 1";
        print "$stmt\n";
        print "$typeOfData, " . $field->id  ."<br />\n";
        $db->pquery($stmt, [$typeOfData, $field->id]);
        print "<br>$module->name $field->name is converted to mandatory<br>\n";
    } else {
        print "<br>$module->name $field->name is already mandatory<br>\n";
    }
}

function synchronizeColumnsGEATB($sourceColumn, $targetColumn, $moduleName, $conditionColumn = NULL, $conditionVals = ''){
    echo "Updating $targetColumn with $sourceColumn values<br/>\n";
    $db = PearDatabase::getInstance();
    $tableName = $db->escapeDbName("vtiger_".strtolower($moduleName));
    if($conditionColumn) {
        $sql = 'UPDATE '.$tableName.' SET '.$targetColumn.' = '.$sourceColumn.' WHERE '.$sourceColumn.' IS NOT NULL AND '.$conditionColumn.' IN ('.generateQuestionMarks($conditionVals).')';
    } else if($conditionVals) {
        echo "Unable to determine a column name for the provided conditions. Skipping column update.<br>/n";
    } else {
        $sql = 'UPDATE '.$tableName.' SET '.$targetColumn.' = '.$sourceColumn.' WHERE '.$sourceColumn.' IS NOT NULL';
    }
    $result = $db->pquery($sql,[$conditionVals]);
    $affectedRows = $db->getAffectedRowCount($result);
    echo "Updated $affectedRows row(s) of $tableName <br/>\n";
}

function reorderBlocksGEATB($blockSeq, $module) {
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
