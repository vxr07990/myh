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

if (!$db) {
    $db = PearDatabase::getInstance();
}

echo '<br />Checking if GRR Estimate field exists:<br />';

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleQuotes);
if ($moduleQuotes&&$blockQuotes) {
    $field1 = Vtiger_Field::getInstance('grr_estimate', $moduleQuotes);
    if ($field1) {
        echo "<br /> The GRR Estimate field already exists in Quotes <br />";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_GRR_ESTIMATE_VAL';
        $field1->name = 'grr_estimate';
        $field1->table = 'vtiger_quotes';
        $field1->column = 'grr_estimate';
        $field1->columntype = 'DECIMAL(56,2)';
        $field1->uitype = 71;
        $field1->typeofdata = 'N~O';
        $field1->displaytype = 1;
        $field1->quickcreate = 0;
        $field1->presence = 2;

        $blockQuotes->addField($field1);
        echo "<br /> The GRR Estimate field created in Quotes <br />";
    }
}


$moduleEstimates = Vtiger_Module::getInstance('Estimates');
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $moduleEstimates);
if ($moduleEstimates && $blockEstimates) {
    $field1 = Vtiger_Field::getInstance('grr_estimate', $moduleEstimates);
    if ($field1) {
        echo "<br /> The GRR field already exists in Estimates <br />";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_GRR_ESTIMATE_VAL';
        $field1->name = 'grr_estimate';
        $field1->table = 'vtiger_quotes';
        $field1->column = 'grr_estimate';
        $field1->columntype = 'DECIMAL(56,2)';
        $field1->uitype = 71;
        $field1->typeofdata = 'N~O';
        $field1->displaytype = 1;
        $field1->quickcreate = 0;
        $field1->presence = 2;

        $blockEstimates->addField($field1);
        echo "<br /> The GRR Estimate field created in Estimates <br />";
    }

    $estimate_field = Vtiger_Field::getInstance('grr_estimate', $moduleEstimates);
    $override_field = Vtiger_Field::getInstance('grr_override_amount', $moduleEstimates);
    if ($estimate_field&&$override_field) {
        echo "<br/>Updating sequence<br/>";
        try {
            $esti_seq = $estimate_field->sequence;
            $override_seq = $override_field->sequence;
            $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$override_seq, $estimate_field->id]);
            $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$esti_seq, $override_field->id]);
            echo "Updating sequence passed ";
        } catch (Exception $e) {
            echo "Updating sequence failed ";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";