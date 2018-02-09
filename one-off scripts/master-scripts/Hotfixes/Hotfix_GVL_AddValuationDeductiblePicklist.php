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
 * Date: 8/23/2016
 * Time: 11:57 AM
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleEstimates = Vtiger_Module::getInstance('Estimates');
$moduleOrders = Vtiger_Module::getInstance('Orders');

$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $moduleEstimates);
$blockOrders = Vtiger_Block::getInstance('LBL_ORDERS_BLOCK_VALUATION', $moduleOrders);

if ($moduleEstimates && $moduleOrders && $blockEstimates && $blockOrders) {
    $field1E = Vtiger_Field::getInstance('valuation_deductible_amount', $moduleEstimates);
    if ($field1E) {
        echo "<li>The valuation_deductible_amount field already exists in Estimates</li><br>";
    } else {
        $field1E             = new Vtiger_Field();
        $field1E->label      = 'LBL_QUOTES_VALUATIONDEDUCTIBLEAMOUNT';
        $field1E->name       = 'valuation_deductible_amount';
        $field1E->table      = 'vtiger_quotes';
        $field1E->column     = 'valuation_deductible_amount';
        $field1E->columntype = 'VARCHAR(100)';
        $field1E->uitype     = '16';
        $field1E->typeofdata = 'V~O';
        $blockEstimates->addField($field1E);
        $field1E->setPicklistValues(['$0', '$250', '$500']);
        $seq      = 8;
        $fieldEVA = Vtiger_Field::getInstance('valuation_amount', $moduleEstimates);
        if ($fieldEVA) {
            $seq = $fieldEVA->sequence + 1;
        }
        setFieldSequenceAVDP($moduleEstimates, $field1E, $seq);
    }
    $field1O = Vtiger_Field::getInstance('valuation_deductible_amount', $moduleOrders);
    if ($field1O) {
        echo "<li>The valuation_deductible_amount field already exists in Orders</li><br>";
    } else {
        $field1O             = new Vtiger_Field();
        $field1O->label      = 'LBL_ORDERS_VALUATIONDEDUCTIBLEAMOUNT';
        $field1O->name       = 'valuation_deductible_amount';
        $field1O->table      = 'vtiger_orders';
        $field1O->column     = 'valuation_deductible_amount';
        $field1O->columntype = 'VARCHAR(100)';
        $field1O->uitype     = '16';
        $field1O->typeofdata = 'V~O';
        $blockOrders->addField($field1O);
        $field1O->setPicklistValues(['$0', '$250', '$500']);
        $seq      = 3;
        $fieldOVA = Vtiger_Field::getInstance('valuation_amount', $moduleOrders);
        if ($fieldOVA) {
            $seq = $fieldOVA->sequence + 1;
        }
        setFieldSequenceAVDP($moduleOrders, $field1O, $seq);
    }
}

function setFieldSequenceAVDP($module, $field, $seqNum)
{
    $db              = PearDatabase::getInstance();
    $fieldID         = $field->id;
    $currentSequence = $field->sequence;
    $stmt            = 'SELECT tabid FROM `vtiger_field` WHERE fieldid='.$fieldID;
    $res             = $db->pquery($stmt);
    if ($res->fetchInto($row)) {
        $tabID = $row['tabid'];
        $stmt  = 'UPDATE `vtiger_field` SET sequence = sequence - 1 WHERE sequence > '.$currentSequence.' AND tabid = '.$tabID;
        $db->pquery($stmt);
        $stmt = 'UPDATE `vtiger_field` SET sequence = sequence + 1 WHERE sequence >= '.$seqNum.' AND tabid = '.$tabID;
        $db->pquery($stmt);
        $stmt = 'UPDATE `vtiger_field` SET sequence = '.$seqNum.' WHERE fieldid = '.$fieldID;
        $db->pquery($stmt);
    } else {
        echo "<li>Failed to find table id for field</li><br>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";