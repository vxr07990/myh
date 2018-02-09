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

echo '<br />Checking if County/Country fields exists:<br />';

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockQuotes = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $moduleEstimates);

$field1 = Vtiger_Field::getInstance('estimates_origin_county', $moduleQuotes);
if ($field1) {
    echo "<br /> The estimates_origin_county field already exists in Quotes/Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_ORIGINCOUNTY';
    $field1->name = 'estimates_origin_county';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'estimates_origin_county';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;

    $blockQuotes->addField($field1);
}

$field2 = Vtiger_Field::getInstance('estimates_origin_county', $moduleEstimates);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ESTIMATES_ORIGINCOUNTY';
    $field2->name = 'estimates_origin_county';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'estimates_origin_county';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 0;

    $blockEstimates->addField($field2);
}

$field1 = Vtiger_Field::getInstance('estimates_destination_county', $moduleQuotes);
if ($field1) {
    echo "<br /> The estimates_destination_county field already exists in Quotes/Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_DESTINATIONCOUNTY';
    $field1->name = 'estimates_destination_county';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'estimates_destination_county';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;

    $blockQuotes->addField($field1);
}

$field2 = Vtiger_Field::getInstance('estimates_destination_county', $moduleEstimates);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ESTIMATES_DESTINATIONCOUNTY';
    $field2->name = 'estimates_destination_county';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'estimates_destination_county';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 0;

    $blockEstimates->addField($field2);
}

$field1 = Vtiger_Field::getInstance('estimates_origin_country', $moduleQuotes);
if ($field1) {
    echo "<br /> The estimates_origin_country field already exists in Quotes/Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_ORIGINCOUNTRY';
    $field1->name = 'estimates_origin_country';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'estimates_origin_country';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;

    $blockQuotes->addField($field1);
}

$field2 = Vtiger_Field::getInstance('estimates_origin_country', $moduleEstimates);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ESTIMATES_ORIGINCOUNTRY';
    $field2->name = 'estimates_origin_country';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'estimates_origin_country';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 0;

    $blockEstimates->addField($field2);
}

$field1 = Vtiger_Field::getInstance('estimates_destination_country', $moduleQuotes);
if ($field1) {
    echo "<br /> The estimates_destination_country field already exists in Quotes/Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_DESTINATIONCOUNTRY';
    $field1->name = 'estimates_destination_country';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'estimates_destination_country';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;

    $blockQuotes->addField($field1);
}

$field2 = Vtiger_Field::getInstance('estimates_destination_country', $moduleEstimates);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ESTIMATES_DESTINATIONCOUNTRY';
    $field2->name = 'estimates_destination_country';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'estimates_destination_country';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 0;

    $blockEstimates->addField($field2);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";