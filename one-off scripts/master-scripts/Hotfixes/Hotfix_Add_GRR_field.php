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

echo '<br />Checking if GRR field exists:<br />';

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $moduleEstimates);

$field1 = Vtiger_Field::getInstance('grr', $moduleQuotes);
if ($field1) {
    echo "<br /> The GRR field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRR';
    $field1->name = 'grr';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr';
    $field1->columntype = 'DECIMAL(12,2)';
    $field1->uitype = 9;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr', $moduleEstimates);
if ($field1) {
    echo "<br /> The GRR field already exists in Estimates <br />";
    updateColumnType('vtiger_quotes', 'grr', 'DECIMAL(12,2)');
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRR';
    $field1->name = 'grr';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr';
    $field1->columntype = 'DECIMAL(12,2)';
    $field1->uitype = 9;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr_override_amount', $moduleQuotes);
if ($field1) {
    echo "<br /> The GRR override amount field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRROVERIDEAMOUNT';
    $field1->name = 'grr_override_amount';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr_override_amount';
    $field1->columntype = 'DECIMAL(12,2)';
    $field1->uitype = 9;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr_override_amount', $moduleEstimates);
if ($field1) {
    echo "<br /> The GRR field override amount already exists in Estimates <br />";
    updateColumnType('vtiger_quotes', 'grr_override_amount', 'DECIMAL(12,2)');
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRROVERIDEAMOUNT';
    $field1->name = 'grr_override_amount';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr_override_amount';
    $field1->columntype = 'DECIMAL(12,2)';
    $field1->uitype = 9;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr_override', $moduleQuotes);
if ($field1) {
    echo "<br /> The GRR field override already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRROVERIDE';
    $field1->name = 'grr_override';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr_override';
    $field1->columntype = 'INT(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr_override', $moduleEstimates);
if ($field1) {
    echo "<br /> The GRR field override already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRROVERIDE';
    $field1->name = 'grr_override';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr_override';
    $field1->columntype = 'INT(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr_cp', $moduleQuotes);
if ($field1) {
    echo "<br /> The GRR field cp already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRRCP';
    $field1->name = 'grr_cp';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr_cp';
    $field1->columntype = 'DECIMAL(12,2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('grr_cp', $moduleEstimates);
if ($field1) {
    echo "<br /> The GRR field cp already exists in Estimates <br />";
    updateColumnType('vtiger_quotes', 'grr_cp', 'DECIMAL(12,2)');
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_GRRCP';
    $field1->name = 'grr_cp';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'grr_cp';
    $field1->columntype = 'DECIMAL(12,2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

function updateColumnType($tableName, $fieldName, $columnType)
{
    $db = PearDatabase::getInstance();
    $sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME=? AND COLUMN_NAME=? AND TABLE_SCHEMA=?";
    $result = $db->pquery($sql, [$tableName, $fieldName, getenv('DB_NAME')]);

    $type = $result->fields['COLUMN_TYPE'];

    if (strtolower($type) == strtolower($columnType)) {
        echo "<br />";
        echo "The column_type is correct for $fieldName";
        echo "<br />";
        return;
    }

    $sql = "ALTER TABLE $tableName CHANGE COLUMN $fieldName $fieldName $columnType";
    echo "<br />";
    echo 'Running query '.$sql;
    echo "<br />";
    $db->query($sql);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";