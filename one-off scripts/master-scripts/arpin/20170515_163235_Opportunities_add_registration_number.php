<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = &PearDatabase::getInstance();
$moduleName = 'Opportunities';
$blockName = 'LBL_POTENTIALS_INFORMATION';

$moduleInstance = Vtiger_Module::getInstance($moduleName);

if (!$moduleInstance) {
    print "ERROR: No moduleName " . $moduleName . PHP_EOL;
    return;
}

$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);

if (!$blockInstance) {
    print "ERROR: No blockName " . $blockName . PHP_EOL;
    return;
}

//create registration number field
$newFieldName = 'registration_number';
$fieldInstance = Vtiger_Field::getInstance($newFieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance               = new Vtiger_Field();
    $fieldInstance->label        = 'LBL_'.strtoupper($newFieldName);
    $fieldInstance->name         = $newFieldName;
    $fieldInstance->table        = 'vtiger_potential';
    $fieldInstance->column       = $newFieldName;
    $fieldInstance->columntype   = 'VARCHAR(255)';
    $fieldInstance->uitype       = 1;
    $fieldInstance->typeofdata   = 'V~O';
    $fieldInstance->displaytype  = 1;
    $fieldInstance->readonly     = 0;
    $fieldInstance->presence     = 2;
    $fieldInstance->defaultvalue = '';
    $blockInstance->addField($fieldInstance);
}

$fieldSequence = [
    'potentialname',
    $newFieldName,
    'business_line',
    'contact_id',
    'related_to',
    'amount',
    'potential_no',
    'opportunity_type',
    'leadsource',
    'sales_stage',
    'assigned_user_id',
    'nextstep',
    'isconvertedfromlead',
    'closingdate',
    'probability',
    'forecast_amount',
    'sales_person',
    'converted_from',
    'billing_type',
    'modifiedtime',
    'agentid',
    'createdtime',
    'created_user_id',
];
reorderBlock_OARN($fieldSequence, $blockInstance, $moduleInstance);

function reorderBlock_OARN($fieldSeq, $block, $module)
{
    if (!$module) {
        return;
    }

    if (!is_object($block)) {
        return;
    }

    if (!is_array($fieldSeq)) {
        return;
    }

    $db = &PearDatabase::getInstance();
    $push_to_end = [];
    $seq = 1;
    foreach ($fieldSeq as $name) {
        $field = Vtiger_Field::getInstance($name, $module);
        if ($field) {
            $sql = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
            $result = $db->pquery($sql, [$seq, $block->id]);
            if ($result) {
                while ($row = $result->fetchRow()) {
                    $push_to_end[] = $row[0];
                }
            }
            $updateStmt = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
            $db->pquery($updateStmt, [$seq++, $field->id]);
        }
        unset($field);
    }

    //push anything that might have gotten added and isn't on the list to the end of the block
    $max = 1;
    $selectMax = 'SELECT MAX(sequence)+1 FROM `vtiger_field` WHERE block = ?';
    $res = $db->pquery($selectMax, [$block->id]);
    if ($res && method_exists($res, 'fetchRow')) {
        $blah = $res->fetchRow();
        $max = $blah['seq'];
    }

    foreach ($push_to_end as $name) {
        //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
        if (!in_array($name, $fieldSeq)) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                $updateStmt = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
                $db->pquery($updateStmt, [$max, $field->id]);
                $max++;
            }
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
