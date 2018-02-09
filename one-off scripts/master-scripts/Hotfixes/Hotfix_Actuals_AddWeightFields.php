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



if (!function_exists(setFieldSequenceInBlock)) {
    function setFieldSequenceInBlock($field, $seqNum)
    {
        $db = PearDatabase::getInstance();
        $fieldID = $field->id;
        $currentSequence = $field->sequence;
        $stmt = 'SELECT block FROM `vtiger_field` WHERE fieldid=' . $fieldID;
        $res = $db->pquery($stmt);
        if ($res->fetchInto($row)) {
            $block = $row['block'];
            $stmt = 'UPDATE `vtiger_field` SET sequence = sequence - 1 WHERE sequence > ' . $currentSequence . ' AND block = ' . $block;
            $db->pquery($stmt);
            $stmt = 'UPDATE `vtiger_field` SET sequence = sequence + 1 WHERE sequence >= ' . $seqNum . ' AND block = ' . $block;
            $db->pquery($stmt);
            $stmt = 'UPDATE `vtiger_field` SET sequence = ' . $seqNum . ' WHERE fieldid = ' . $fieldID;
            $db->pquery($stmt);
        } else {
            echo "<li>Failed to find block id for field</li><br>";
        }
    }
}


$actualsInstance = Vtiger_Module::getInstance('Actuals');
if ($actualsInstance) {
    echo 'Creating new weight fields in Actuals Module<br>';
    $blockInterstate = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $actualsInstance);
    if ($blockInterstate) {
        $field1 = Vtiger_Field::getInstance('tweight', $actualsInstance);
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_QUOTES_TWEIGHT';
            $field1->name = 'tweight';
            $field1->table = 'vtiger_quotes';
            $field1->column = $field1->name;
            $field1->columntype = 'INT(50)';
            $field1->uitype = 7;
            $field1->typeofdata = 'I~O';
            $blockInterstate->addField($field1);

            $field11 = Vtiger_Field::getInstance('weight', $actualsInstance);
            if ($field1->sequence != $field11->sequence + 1) {
                setFieldSequenceInBlock($field1, $field11->sequence + 1);
            }
        }



        $field2 = Vtiger_Field::getInstance('gweight', $actualsInstance);
        if (!$field2) {
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_QUOTES_GROSSWEIGHT';
            $field2->name = 'gweight';
            $field2->table = 'vtiger_quotes';
            $field2->column = $field2->name;
            $field2->columntype = 'INT(50)';
            $field2->uitype = 7;
            $field2->typeofdata = 'I~O';
            $blockInterstate->addField($field2);

            if ($field2->sequence != $field11->sequence + 2) {
                setFieldSequenceInBlock($field2, $field11->sequence + 2);
            }
        }


        echo 'End adding new weight fields in Actuals Module<br>';
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";