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

/*-------------Add bulky article changes. Only used for UVLC. Will be conditionalized with rating engine----------*/
echo '<br />Checking if bulky_article_changes field exists:<br />';

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleEstimates);

$field1 = Vtiger_Field::getInstance('bulky_article_changes', $moduleQuotes);
if ($field1) {
    echo "<br /> The bulky_article_changes field already exists in Quotes/Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_BULKY_ARTICLE_CHANGES';
    $field1->name = 'bulky_article_changes';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'bulky_article_changes';
    $field1->columntype = 'FLOAT(10,4)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field2 = Vtiger_Field::getInstance('bulky_article_changes', $moduleEstimates);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_BULKY_ARTICLE_CHANGES';
    $field2->name = 'bulky_article_changes';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'bulky_article_changes';
    $field2->columntype = 'FLOAT(10,4)';
    $field2->uitype = 7;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 0;
    $field2->presence = 2;

    $blockEstimates->addField($field2);
}

/*------------Update Valuation drop-down values and create new blocks----------*/

//Change global value for valuation('Zero') to 'FVP - $0'
echo '<br />Changing valuation deductible "Zero" value to "FVP - $0"<br />';
$sql = 'SELECT `valuation_deductible` FROM `vtiger_valuation_deductible` WHERE `valuation_deductible` = "Zero"';
if ($db->getOne($sql)) {
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_valuation_deductible` SET `valuation_deductible` = "FVP - $0" WHERE `valuation_deductible` = "Zero"');
}

//Update valuation values to include 'FVP'
echo '<br />Prepend all valuation values with "FVP -"<br />';
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_valuation_deductible` SET `valuation_deductible` = CONCAT("FVP - ", `valuation_deductible`) WHERE `valuation_deductible` NOT LIKE "%FVP -%" AND `valuation_deductible` NOT LIKE "60¢ /lb."');


//Create new valuation blocks and move valuation fields into them
echo '<br />Create valuation blocks if they do not exist<br />';
$blockQuotesValuation = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $moduleQuotes);
if (!$blockQuotesValuation) {
    $blockQuotesValuation = new Vtiger_Block();
    $blockQuotesValuation->label = 'LBL_QUOTES_VALUATION';
    $moduleQuotes->addBlock($blockQuotesValuation);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockQuotesValuation->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_deductible', $moduleQuotes)->id);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockQuotesValuation->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_amount', $moduleQuotes)->id);
}

$blockEstimatesValuation = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $moduleEstimates);
if (!$blockEstimatesValuation) {
    $blockEstimatesValuation = new Vtiger_Block();
    $blockEstimatesValuation->label = 'LBL_QUOTES_VALUATION';
    $blockEstimatesValuation->sequence = 6;
    $moduleEstimates->addBlock($blockEstimatesValuation);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockEstimatesValuation->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_deductible', $moduleEstimates)->id);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockEstimatesValuation->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_amount', $moduleEstimates)->id);
}

/*------------Add Elivator, Stair and Long Carry blocks----------*/

$blocks = [
    'LBL_QUOTES_ELEVATOR' => [ 'elevator' , Vtiger_Block::getInstance('LBL_QUOTES_ELEVATOR', $moduleEstimates)],
    'LBL_QUOTES_STAIR' => [ 'stair' , Vtiger_Block::getInstance('LBL_QUOTES_STAIR', $moduleEstimates)],
    'LBL_QUOTES_LONGCARRY' => [ 'longcarry' , Vtiger_Block::getInstance('LBL_QUOTES_LONGCARRY', $moduleEstimates)]
];

$fields = [
    'origin_occurrence' => [$moduleQuotes, $moduleEstimates],
    'destination_occurrence' => [$moduleQuotes, $moduleEstimates],
    'origin_CTW' => [$moduleQuotes, $moduleEstimates],
    'destination_CTW' => [$moduleQuotes, $moduleEstimates],
];

foreach ($blocks as $key => $block) {
    echo "<br />Checking if the '$key' block exists: <br />";
    if (!$blocks[$key][1]) {
        $blocks[$key][1] = new Vtiger_Block();
        $blocks[$key][1]->label = $key;
        $blocks[$key][1]->sequence = 8;
        $moduleEstimates->addBlock($blocks[$key][1]);
    } else {
        echo "<br />'$key' block exists.<br />";
    }

    echo "<br />Add fields to '$key' block if they do not exist.<br />";
    foreach ($fields as $fieldKey => $fieldBlocks) {
        if (!Vtiger_Field::getInstance($blocks[$key][0] . '_' . $fieldKey, $fieldBlocks[0])) {
            unset($newField);
            $newField = new Vtiger_Field();
            $newField->label = $key . '_' . strtoupper($fieldKey);
            $newField->name = $blocks[$key][0] . '_' . $fieldKey;
            $newField->table = 'vtiger_quotes';
            $newField->column = $blocks[$key][0] . '_' . $fieldKey;
            $newField->columntype = 'INT(19)';
            $newField->uitype = 7;
            $newField->typeofdata = 'V~O';
            $newField->displaytype = 1;
            $newField->quickcreate = 0;
            $newField->presence = 2;

            $blockQuotes->addField($newField);
        }
        if (!Vtiger_Field::getInstance($blocks[$key][0] . '_' . $fieldKey, $fieldBlocks[1])) {
            unset($newField);
            $newField = new Vtiger_Field();
            $newField->label = $key . '_' . strtoupper($fieldKey);
            $newField->name = $blocks[$key][0] . '_' . $fieldKey;
            $newField->table = 'vtiger_quotes';
            $newField->column = $blocks[$key][0] . '_' . $fieldKey;
            $newField->columntype = 'INT(19)';
            $newField->uitype = 7;
            $newField->typeofdata = 'V~O';
            $newField->displaytype = 1;
            $newField->quickcreate = 0;
            $newField->presence = 2;

            $blocks[$key][1]->addField($newField);
        }
    }
}

/*------------Add Rush shipment fee field----------*/
$field3 = Vtiger_Field::getInstance('rush_shipment_fee', $moduleEstimates);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_RUSH_SHIPMENT_FEE';
    $field3->name = 'rush_shipment_fee';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'rush_shipment_fee';
    $field3->columntype = 'VARCHAR(3)';
    $field3->uitype = 56;
    $field3->typeofdata = 'C~O';
    $field3->displaytype = 1;
    $field3->quickcreate = 0;
    $field3->presence = 2;

    $blockEstimates->addField($field3);
}
$field4 = Vtiger_Field::getInstance('rush_shipment_fee', $moduleQuotes);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_RUSH_SHIPMENT_FEE';
    $field4->name = 'rush_shipment_fee';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'rush_shipment_fee';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'C~O';
    $field4->displaytype = 1;
    $field4->quickcreate = 0;
    $field4->presence = 2;

    $blockQuotes->addField($field4);
}

/*------------Add appliance service and reservice fields----------*/
$appliacnceBlock = Vtiger_Block::getInstance('LBL_ESTIMATES_APPLIANCE', $moduleEstimates);

echo '<br />Adding LBL_APPLIANCE block if it does not exist.<br />';
if (!$appliacnceBlock) {
    $appliacnceBlock = new Vtiger_Block();
    $appliacnceBlock->label = 'LBL_ESTIMATES_APPLIANCE';
    $appliacnceBlock->sequence = 8;
    $moduleEstimates->addBlock($appliacnceBlock);
}

$field5 = Vtiger_Field::getInstance('appliance_service', $moduleEstimates);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_APPLIANCE_SERVICE';
    $field5->name = 'appliance_service';
    $field5->table = 'vtiger_quotes';
    $field5->column = 'appliance_service';
    $field5->columntype = 'INT(10)';
    $field5->uitype = 7;
    $field5->typeofdata = 'V~O';
    $field5->displaytype = 1;
    $field5->quickcreate = 0;
    $field5->presence = 2;

    $appliacnceBlock->addField($field5);
}
$field6 = Vtiger_Field::getInstance('appliance_reservice', $moduleEstimates);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_APPLIANCE_RESERVICE';
    $field6->name = 'appliance_reservice';
    $field6->table = 'vtiger_quotes';
    $field6->column = 'appliance_reservice';
    $field6->columntype = 'INT(10)';
    $field6->uitype = 7;
    $field6->typeofdata = 'V~O';
    $field6->displaytype = 1;
    $field6->quickcreate = 0;
    $field6->presence = 2;

    $appliacnceBlock->addField($field6);
}

$field7 = Vtiger_Field::getInstance('appliance_service', $moduleQuotes);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_APPLIANCE_SERVICE';
    $field7->name = 'appliance_service';
    $field7->table = 'vtiger_quotes';
    $field7->column = 'appliance_service';
    $field7->columntype = 'INT(10)';
    $field7->uitype = 7;
    $field7->typeofdata = 'V~O';
    $field7->displaytype = 1;
    $field7->quickcreate = 0;
    $field7->presence = 2;

    $blockQuotes->addField($field7);
}
$field8 = Vtiger_Field::getInstance('appliance_reservice', $moduleQuotes);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_APPLIANCE_RESERVICE';
    $field8->name = 'appliance_reservice';
    $field8->table = 'vtiger_quotes';
    $field8->column = 'appliance_reservice';
    $field8->columntype = 'INT(10)';
    $field8->uitype = 7;
    $field8->typeofdata = 'V~O';
    $field8->displaytype = 1;
    $field8->quickcreate = 0;
    $field8->presence = 2;

    $blockQuotes->addField($field8);
}

/*------------Add new SIT table for UVLC----------*/
$newSITBlock = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS2', $moduleEstimates);

echo '<br />Adding LBL_QUOTES_SITDETAILS2 block if it does not exist.<br />';
if (!$newSITBlock) {
    $newSITBlock = new Vtiger_Block();
    $newSITBlock->label = 'LBL_QUOTES_SITDETAILS2';
    $newSITBlock->sequence = 8;
    $moduleEstimates->addBlock($newSITBlock);
}

$field9 = Vtiger_Field::getInstance('ori_sit2_date_in', $moduleEstimates);
if (!$field9) {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_SIT_DATE_IN';
    $field9->name = 'ori_sit2_date_in';
    $field9->table = 'vtiger_quotes';
    $field9->column = 'ori_sit2_date_in';
    $field9->columntype = 'DATE';
    $field9->uitype = 5;
    $field9->typeofdata = 'D~O';
    $field9->displaytype = 1;
    $field9->quickcreate = 0;
    $field9->presence = 2;

    $newSITBlock->addField($field9);
}

$field9 = Vtiger_Field::getInstance('des_sit2_date_in', $moduleEstimates);
if (!$field9) {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_DES_SIT_DATE_IN';
    $field9->name = 'des_sit2_date_in';
    $field9->table = 'vtiger_quotes';
    $field9->column = 'des_sit2_date_in';
    $field9->columntype = 'DATE';
    $field9->uitype = 5;
    $field9->typeofdata = 'D~O';
    $field9->displaytype = 1;
    $field9->quickcreate = 0;
    $field9->presence = 2;

    $newSITBlock->addField($field9);
}

$field10 = Vtiger_Field::getInstance('ori_sit2_date_in', $moduleQuotes);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_SIT_DATE_IN';
    $field10->name = 'ori_sit2_date_in';
    $field10->table = 'vtiger_quotes';
    $field10->column = 'ori_sit2_date_in';
    $field10->columntype = 'DATE';
    $field10->uitype = 5;
    $field10->typeofdata = 'D~O';
    $field10->displaytype = 1;
    $field10->quickcreate = 0;
    $field10->presence = 2;

    $blockQuotes->addField($field10);
}

$field10 = Vtiger_Field::getInstance('des_sit2_date_in', $moduleQuotes);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_DES_SIT_DATE_IN';
    $field10->name = 'des_sit2_date_in';
    $field10->table = 'vtiger_quotes';
    $field10->column = 'des_sit2_date_in';
    $field10->columntype = 'DATE';
    $field10->uitype = 5;
    $field10->typeofdata = 'D~O';
    $field10->displaytype = 1;
    $field10->quickcreate = 0;
    $field10->presence = 2;

    $blockQuotes->addField($field10);
}

$field11 = Vtiger_Field::getInstance('ori_sit2_pickup_date', $moduleEstimates);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_SIT_PICKUP_DATE';
    $field11->name = 'ori_sit2_pickup_date';
    $field11->table = 'vtiger_quotes';
    $field11->column = 'ori_sit2_pickup_date';
    $field11->columntype = 'DATE';
    $field11->uitype = 5;
    $field11->typeofdata = 'D~O';
    $field11->displaytype = 1;
    $field11->quickcreate = 0;
    $field11->presence = 2;

    $newSITBlock->addField($field11);
}

$field11 = Vtiger_Field::getInstance('des_sit2_pickup_date', $moduleEstimates);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_DES_SIT_PICKUP_DATE';
    $field11->name = 'des_sit2_pickup_date';
    $field11->table = 'vtiger_quotes';
    $field11->column = 'des_sit2_pickup_date';
    $field11->columntype = 'DATE';
    $field11->uitype = 5;
    $field11->typeofdata = 'D~O';
    $field11->displaytype = 1;
    $field11->quickcreate = 0;
    $field11->presence = 2;

    $newSITBlock->addField($field11);
}

$field12 = Vtiger_Field::getInstance('ori_sit2_pickup_date', $moduleQuotes);
if (!$field12) {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_SIT_PICKUP_DATE';
    $field12->name = 'ori_sit2_pickup_date';
    $field12->table = 'vtiger_quotes';
    $field12->column = 'ori_sit2_pickup_date';
    $field12->columntype = 'DATE';
    $field12->uitype = 5;
    $field12->typeofdata = 'D~O';
    $field12->displaytype = 1;
    $field12->quickcreate = 0;
    $field12->presence = 2;

    $blockQuotes->addField($field12);
}

$field12 = Vtiger_Field::getInstance('des_sit2_pickup_date', $moduleQuotes);
if (!$field12) {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_DES_SIT_PICKUP_DATE';
    $field12->name = 'des_sit2_pickup_date';
    $field12->table = 'vtiger_quotes';
    $field12->column = 'des_sit2_pickup_date';
    $field12->columntype = 'DATE';
    $field12->uitype = 5;
    $field12->typeofdata = 'D~O';
    $field12->displaytype = 1;
    $field12->quickcreate = 0;
    $field12->presence = 2;

    $blockQuotes->addField($field12);
}

$field13 = Vtiger_Field::getInstance('ori_sit2_number_days', $moduleEstimates);
if (!$field13) {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_SIT_NUM_DAYS';
    $field13->name = 'ori_sit2_number_days';
    $field13->table = 'vtiger_quotes';
    $field13->column = 'ori_sit2_number_days';
    $field13->columntype = 'INT(10)';
    $field13->uitype = 7;
    $field13->typeofdata = 'V~O';
    $field13->displaytype = 1;
    $field13->quickcreate = 0;
    $field13->presence = 2;

    $newSITBlock->addField($field13);
}

$field13 = Vtiger_Field::getInstance('des_sit2_number_days', $moduleEstimates);
if (!$field13) {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_DES_SIT_NUM_DAYS';
    $field13->name = 'des_sit2_number_days';
    $field13->table = 'vtiger_quotes';
    $field13->column = 'des_sit2_number_days';
    $field13->columntype = 'INT(10)';
    $field13->uitype = 7;
    $field13->typeofdata = 'V~O';
    $field13->displaytype = 1;
    $field13->quickcreate = 0;
    $field13->presence = 2;

    $newSITBlock->addField($field13);
}

$field14 = Vtiger_Field::getInstance('ori_sit2_number_days', $moduleQuotes);
if (!$field14) {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_SIT_NUM_DAYS';
    $field14->name = 'ori_sit2_number_days';
    $field14->table = 'vtiger_quotes';
    $field14->column = 'ori_sit2_number_days';
    $field14->columntype = 'INT(10)';
    $field14->uitype = 7;
    $field14->typeofdata = 'V~O';
    $field14->displaytype = 1;
    $field14->quickcreate = 0;
    $field14->presence = 2;

    $blockQuotes->addField($field14);
}

$field14 = Vtiger_Field::getInstance('des_sit2_number_days', $moduleQuotes);
if (!$field14) {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_DES_SIT_NUM_DAYS';
    $field14->name = 'des_sit2_number_days';
    $field14->table = 'vtiger_quotes';
    $field14->column = 'des_sit2_number_days';
    $field14->columntype = 'INT(10)';
    $field14->uitype = 7;
    $field14->typeofdata = 'V~O';
    $field14->displaytype = 1;
    $field14->quickcreate = 0;
    $field14->presence = 2;

    $blockQuotes->addField($field14);
}

$field15 = Vtiger_Field::getInstance('ori_sit2_weight', $moduleEstimates);
if (!$field15) {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_SIT_WEIGHT';
    $field15->name = 'ori_sit2_weight';
    $field15->table = 'vtiger_quotes';
    $field15->column = 'ori_sit2_weight';
    $field15->columntype = 'INT(10)';
    $field15->uitype = 7;
    $field15->typeofdata = 'V~O';
    $field15->displaytype = 1;
    $field15->quickcreate = 0;
    $field15->presence = 2;

    $newSITBlock->addField($field15);
}

$field15 = Vtiger_Field::getInstance('des_sit2_weight', $moduleEstimates);
if (!$field15) {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_DES_SIT_WEIGHT';
    $field15->name = 'des_sit2_weight';
    $field15->table = 'vtiger_quotes';
    $field15->column = 'des_sit2_weight';
    $field15->columntype = 'INT(10)';
    $field15->uitype = 7;
    $field15->typeofdata = 'V~O';
    $field15->displaytype = 1;
    $field15->quickcreate = 0;
    $field15->presence = 2;

    $newSITBlock->addField($field15);
}

$field16 = Vtiger_Field::getInstance('ori_sit2_weight', $moduleQuotes);
if (!$field16) {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_SIT_WEIGHT';
    $field16->name = 'ori_sit2_weight';
    $field16->table = 'vtiger_quotes';
    $field16->column = 'ori_sit2_weight';
    $field16->columntype = 'INT(10)';
    $field16->uitype = 7;
    $field16->typeofdata = 'V~O';
    $field16->displaytype = 1;
    $field16->quickcreate = 0;
    $field16->presence = 2;

    $blockQuotes->addField($field16);
}

$field16 = Vtiger_Field::getInstance('des_sit2_weight', $moduleQuotes);
if (!$field16) {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_DES_SIT_WEIGHT';
    $field16->name = 'des_sit2_weight';
    $field16->table = 'vtiger_quotes';
    $field16->column = 'des_sit2_weight';
    $field16->columntype = 'INT(10)';
    $field16->uitype = 7;
    $field16->typeofdata = 'V~O';
    $field16->displaytype = 1;
    $field16->quickcreate = 0;
    $field16->presence = 2;

    $blockQuotes->addField($field16);
}

$field17 = Vtiger_Field::getInstance('ori_sit2_container_or_warehouse', $moduleEstimates);
if (!$field17) {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_SIT_CONTAINER_WAREHOUSE';
    $field17->name = 'ori_sit2_container_or_warehouse';
    $field17->table = 'vtiger_quotes';
    $field17->column = 'ori_sit2_container_or_warehouse';
    $field17->columntype = 'VARCHAR(3)';
    $field17->uitype = 56;
    $field17->typeofdata = 'C~O';
    $field17->displaytype = 1;
    $field17->quickcreate = 0;
    $field17->presence = 2;

    $newSITBlock->addField($field17);
}

$field17 = Vtiger_Field::getInstance('des_sit2_container_or_warehouse', $moduleEstimates);
if (!$field17) {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_DES_SIT_CONTAINER_WAREHOUSE';
    $field17->name = 'des_sit2_container_or_warehouse';
    $field17->table = 'vtiger_quotes';
    $field17->column = 'des_sit2_container_or_warehouse';
    $field17->columntype = 'VARCHAR(3)';
    $field17->uitype = 56;
    $field17->typeofdata = 'C~O';
    $field17->displaytype = 1;
    $field17->quickcreate = 0;
    $field17->presence = 2;

    $newSITBlock->addField($field17);
}

$field18 = Vtiger_Field::getInstance('ori_sit2_container_or_warehouse', $moduleQuotes);
if (!$field18) {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_SIT_CONTAINER_WAREHOUSE';
    $field18->name = 'ori_sit2_container_or_warehouse';
    $field18->table = 'vtiger_quotes';
    $field18->column = 'ori_sit2_container_or_warehouse';
    $field18->columntype = 'VARCHAR(3)';
    $field18->uitype = 56;
    $field18->typeofdata = 'C~O';
    $field18->displaytype = 1;
    $field18->quickcreate = 0;
    $field18->presence = 2;

    $blockQuotes->addField($field18);
}

$field18 = Vtiger_Field::getInstance('des_sit2_container_or_warehouse', $moduleQuotes);
if (!$field18) {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_DES_SIT_CONTAINER_WAREHOUSE';
    $field18->name = 'des_sit2_container_or_warehouse';
    $field18->table = 'vtiger_quotes';
    $field18->column = 'des_sit2_container_or_warehouse';
    $field18->columntype = 'VARCHAR(3)';
    $field18->uitype = 56;
    $field18->typeofdata = 'C~O';
    $field18->displaytype = 1;
    $field18->quickcreate = 0;
    $field18->presence = 2;

    $blockQuotes->addField($field18);
}

$field19 = Vtiger_Field::getInstance('ori_sit2_container_number', $moduleEstimates);
if (!$field19) {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_SIT_CONTAINER_NUMBER';
    $field19->name = 'ori_sit2_container_number';
    $field19->table = 'vtiger_quotes';
    $field19->column = 'ori_sit2_container_number';
    $field19->columntype = 'VARCHAR(15)';
    $field19->uitype = 7;
    $field19->typeofdata = 'V~O';
    $field19->displaytype = 1;
    $field19->quickcreate = 0;
    $field19->presence = 2;

    $newSITBlock->addField($field19);
}

$field19 = Vtiger_Field::getInstance('des_sit2_container_number', $moduleEstimates);
if (!$field19) {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_DES_SIT_CONTAINER_NUMBER';
    $field19->name = 'des_sit2_container_number';
    $field19->table = 'vtiger_quotes';
    $field19->column = 'des_sit2_container_number';
    $field19->columntype = 'VARCHAR(15)';
    $field19->uitype = 7;
    $field19->typeofdata = 'V~O';
    $field19->displaytype = 1;
    $field19->quickcreate = 0;
    $field19->presence = 2;

    $newSITBlock->addField($field19);
}

$field20 = Vtiger_Field::getInstance('ori_sit2_container_number', $moduleQuotes);
if (!$field20) {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_SIT_CONTAINER_NUMBER';
    $field20->name = 'ori_sit2_container_number';
    $field20->table = 'vtiger_quotes';
    $field20->column = 'ori_sit2_container_number';
    $field20->columntype = 'VARCHAR(15)';
    $field20->uitype = 7;
    $field20->typeofdata = 'V~O';
    $field20->displaytype = 1;
    $field20->quickcreate = 0;
    $field20->presence = 2;

    $blockQuotes->addField($field20);
}

$field20 = Vtiger_Field::getInstance('des_sit2_container_number', $moduleQuotes);
if (!$field20) {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_DES_SIT_CONTAINER_NUMBER';
    $field20->name = 'des_sit2_container_number';
    $field20->table = 'vtiger_quotes';
    $field20->column = 'des_sit2_container_number';
    $field20->columntype = 'VARCHAR(15)';
    $field20->uitype = 7;
    $field20->typeofdata = 'V~O';
    $field20->displaytype = 1;
    $field20->quickcreate = 0;
    $field20->presence = 2;

    $blockQuotes->addField($field20);
}

/******************* Move Fuel surcharge to Acessorial Details block *******************/
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = " . Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleEstimates)->id . ", `sequence` = '" . (Vtiger_Field::getInstance('rush_shipment_fee', $moduleEstimates)->sequence + 1) . "' WHERE fieldid = " . Vtiger_Field::getInstance('irr_charge', $moduleEstimates)->id);


/******************* Add ot loading and ot unloading *******************/
$field21 = Vtiger_Field::getInstance('accesorial_ot_loading', $moduleQuotes);
if (!$field21) {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_ACC_OT_LOADING';
    $field21->name = 'accesorial_ot_loading';
    $field21->table = 'vtiger_quotes';
    $field21->column = 'accesorial_ot_loading';
    $field21->columntype = 'VARCHAR(15)';
    $field21->uitype = 7;
    $field21->typeofdata = 'V~O';
    $field21->displaytype = 1;
    $field21->quickcreate = 0;
    $field21->presence = 2;

    $blockQuotes->addField($field21);
}

$field21 = Vtiger_Field::getInstance('accesorial_ot_loading', $moduleEstimates);
if (!$field21) {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_ACC_OT_LOADING';
    $field21->name = 'accesorial_ot_loading';
    $field21->table = 'vtiger_quotes';
    $field21->column = 'accesorial_ot_loading';
    $field21->columntype = 'VARCHAR(15)';
    $field21->uitype = 7;
    $field21->typeofdata = 'V~O';
    $field21->displaytype = 1;
    $field21->quickcreate = 0;
    $field21->presence = 2;

    $blockEstimates->addField($field21);
}

$field22 = Vtiger_Field::getInstance('accesorial_ot_unloading', $moduleQuotes);
if (!$field22) {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_ACC_OT_UNLOADING';
    $field22->name = 'accesorial_ot_unloading';
    $field22->table = 'vtiger_quotes';
    $field22->column = 'accesorial_ot_unloading';
    $field22->columntype = 'VARCHAR(15)';
    $field22->uitype = 7;
    $field22->typeofdata = 'V~O';
    $field22->displaytype = 1;
    $field22->quickcreate = 0;
    $field22->presence = 2;

    $blockQuotes->addField($field22);
}

$field22 = Vtiger_Field::getInstance('accesorial_ot_unloading', $moduleEstimates);
if (!$field22) {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_ACC_OT_UNLOADING';
    $field22->name = 'accesorial_ot_unloading';
    $field22->table = 'vtiger_quotes';
    $field22->column = 'accesorial_ot_unloading';
    $field22->columntype = 'VARCHAR(15)';
    $field22->uitype = 7;
    $field22->typeofdata = 'V~O';
    $field22->displaytype = 1;
    $field22->quickcreate = 0;
    $field22->presence = 2;

    $blockEstimates->addField($field22);
}


/******************* Add fuel surcharge *******************/
$field23 = Vtiger_Field::getInstance('accesorial_fuel_surcharge', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_FUEL_SURCHARGE';
    $field23->name = 'accesorial_fuel_surcharge';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'accesorial_fuel_surcharge';
    $field23->columntype = 'DOUBLE(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    $blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('accesorial_fuel_surcharge', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_FUEL_SURCHARGE';
    $field23->name = 'accesorial_fuel_surcharge';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'accesorial_fuel_surcharge';
    $field23->columntype = 'DOUBLE(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    $blockEstimates->addField($field23);
}

/******************* Add space reservation block and fields *******************/
$spaceBlock = Vtiger_Block::getInstance('LBL_SPACE_RESERVATION', $moduleEstimates);

echo '<br />Adding LBL_QUOTES_SITDETAILS2 block if it does not exist.<br />';
if (!$spaceBlock) {
    $spaceBlock = new Vtiger_Block();
    $spaceBlock->label = 'LBL_SPACE_RESERVATION';
    $spaceBlock->sequence = 8;
    $moduleEstimates->addBlock($spaceBlock);
}

$field24 = Vtiger_Field::getInstance('space_reserve_bool', $moduleQuotes);
if (!$field24) {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_SPACE_RESERVE_BOOL';
    $field24->name = 'space_reserve_bool';
    $field24->table = 'vtiger_quotes';
    $field24->column = 'space_reserve_bool';
    $field24->columntype = 'VARCHAR(3)';
    $field24->uitype = 56;
    $field24->typeofdata = 'V~O';
    $field24->displaytype = 1;
    $field24->quickcreate = 0;
    $field24->presence = 2;

    $blockQuotes->addField($field24);
}

$field24 = Vtiger_Field::getInstance('space_reserve_bool', $moduleEstimates);
if (!$field24) {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_SPACE_RESERVE_BOOL';
    $field24->name = 'space_reserve_bool';
    $field24->table = 'vtiger_quotes';
    $field24->column = 'space_reserve_bool';
    $field24->columntype = 'VARCHAR(3)';
    $field24->uitype = 56;
    $field24->typeofdata = 'V~O';
    $field24->displaytype = 1;
    $field24->quickcreate = 0;
    $field24->presence = 2;

    $spaceBlock->addField($field24);
}

$field25 = Vtiger_Field::getInstance('space_reserve_cf', $moduleQuotes);
if (!$field25) {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_SPACE_RESERVE_CF';
    $field25->name = 'space_reserve_cf';
    $field25->table = 'vtiger_quotes';
    $field25->column = 'space_reserve_cf';
    $field25->columntype = 'INT(11)';
    $field25->uitype = 7;
    $field25->typeofdata = 'V~O';
    $field25->displaytype = 1;
    $field25->quickcreate = 0;
    $field25->presence = 2;

    $blockQuotes->addField($field25);
}

$field25 = Vtiger_Field::getInstance('space_reserve_cf', $moduleEstimates);
if (!$field25) {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_SPACE_RESERVE_CF';
    $field25->name = 'space_reserve_cf';
    $field25->table = 'vtiger_quotes';
    $field25->column = 'space_reserve_cf';
    $field25->columntype = 'INT(11)';
    $field25->uitype = 7;
    $field25->typeofdata = 'V~O';
    $field25->displaytype = 1;
    $field25->quickcreate = 0;
    $field25->presence = 2;

    $spaceBlock->addField($field25);
}

/******************* Add Expedited Service *******************/
$field23 = Vtiger_Field::getInstance('accesorial_expedited_service', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_EXPEDITED_SERVICE';
    $field23->name = 'accesorial_expedited_service';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'accesorial_expedited_service';
    $field23->columntype = 'VARCHAR(3)';
    $field23->uitype = 56;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    $blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('accesorial_expedited_service', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_EXPEDITED_SERVICE';
    $field23->name = 'accesorial_expedited_service';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'accesorial_expedited_service';
    $field23->columntype = 'VARCHAR(3)';
    $field23->uitype = 56;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    $blockEstimates->addField($field23);
}
/******************* TODO: Fields below need to be put into the correct blocks ************************************/
/******************* Add Valuation Discount *******************/
$field23 = Vtiger_Field::getInstance('valuation_discount', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_VALUATION_DISCOUNT';
    $field23->name = 'valuation_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'valuation_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('valuation_discount', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_VALUATION_DISCOUNT';
    $field23->name = 'valuation_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'valuation_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}

/******************* Add Storage Discount *******************/
$field23 = Vtiger_Field::getInstance('storage_discount', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STORAGE_DISCOUNT';
    $field23->name = 'storage_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'storage_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('storage_discount', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STORAGE_DISCOUNT';
    $field23->name = 'storage_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'storage_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}

/******************* Add Packing Recycling Fee *******************/
$field23 = Vtiger_Field::getInstance('storage_discount', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STORAGE_DISCOUNT';
    $field23->name = 'storage_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'storage_discount';
    $field23->columntype = 'DECIMAL(5,2)';
    $field23->uitype = 71;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('storage_discount', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STORAGE_DISCOUNT';
    $field23->name = 'storage_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'storage_discount';
    $field23->columntype = 'DECIMAL(5,2)';
    $field23->uitype = 71;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}

/******************* Add Apply packing discount to crates *******************/
/*
$field23 = Vtiger_Field::getInstance('storage_discount',$moduleQuotes);
if(!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STORAGE_DISCOUNT';
    $field23->name = 'storage_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'storage_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('storage_discount',$moduleEstimates);
if(!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_STORAGE_DISCOUNT';
    $field23->name = 'storage_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'storage_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}
*/
/******************* Add IRR Discount *******************/
$field23 = Vtiger_Field::getInstance('irr_discount', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_IRR_DISCOUNT';
    $field23->name = 'irr_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'irr_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('irr_discount', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_IRR_DISCOUNT';
    $field23->name = 'irr_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'irr_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}

/******************* Add Ferry Charge Discount *******************/
$field23 = Vtiger_Field::getInstance('ferry_charge_discount', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_FERRY_CHARGE_DISCOUNT';
    $field23->name = 'ferry_charge_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'ferry_charge_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('ferry_charge_discount', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_FERRY_CHARGE_DISCOUNT';
    $field23->name = 'ferry_charge_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'ferry_charge_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}

/******************* Add Labor Surcharge Discount *******************/
$field23 = Vtiger_Field::getInstance('labor_surcharge_discount', $moduleQuotes);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_LABOR_SURCHARGE_DISCOUNT';
    $field23->name = 'labor_surcharge_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'labor_surcharge_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockQuotes->addField($field23);
}

$field23 = Vtiger_Field::getInstance('labor_surcharge_discount', $moduleEstimates);
if (!$field23) {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_LABOR_SURCHARGE_DISCOUNT';
    $field23->name = 'labor_surcharge_discount';
    $field23->table = 'vtiger_quotes';
    $field23->column = 'labor_surcharge_discount';
    $field23->columntype = 'DECIMAL(5,1)';
    $field23->uitype = 9;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 0;
    $field23->presence = 2;

    //$blockEstimates->addField($field23);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";