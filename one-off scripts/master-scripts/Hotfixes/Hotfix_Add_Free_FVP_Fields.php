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

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $moduleEstimates);

//Ensure that valuation fields are in valuation block.
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockQuotes->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_deductible', $moduleQuotes)->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockQuotes->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_amount', $moduleQuotes)->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockEstimates->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_deductible', $moduleEstimates)->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockEstimates->id WHERE fieldid = " . Vtiger_Field::getInstance('valuation_amount', $moduleEstimates)->id);

$field1 = Vtiger_Field::getInstance('apply_free_fvp', $moduleQuotes);
if ($field1) {
    echo "<br /> The apply_free_fvp field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_APPLYFREEFVP';
    $field1->name = 'apply_free_fvp';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'apply_free_fvp';
    $field1->columntype = 'INT(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('apply_free_fvp', $moduleEstimates);
if ($field1) {
    echo "<br /> The apply_free_fvp field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_APPLYFREEFVP';
    $field1->name = 'apply_free_fvp';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'apply_free_fvp';
    $field1->columntype = 'INT(3)';
    $field1->uitype = 56;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('min_declared_value_mult', $moduleQuotes);
if ($field1) {
    echo "<br /> The min_declared_value_mult field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_MINDECLAREDVALMULT';
    $field1->name = 'min_declared_value_mult';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'min_declared_value_mult';
    $field1->columntype = 'DECIMAL(10,2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('min_declared_value_mult', $moduleEstimates);
if ($field1) {
    echo "<br /> The min_declared_value_mult field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_MINDECLAREDVALMULT';
    $field1->name = 'min_declared_value_mult';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'min_declared_value_mult';
    $field1->columntype = 'DECIMAL(10,2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('free_valuation_limit', $moduleQuotes);
if ($field1) {
    echo "<br /> The free_valuation_limit field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_FREEVALUATIONLIMIT';
    $field1->name = 'free_valuation_limit';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'free_valuation_limit';
    $field1->columntype = 'INT(10)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('free_valuation_limit', $moduleEstimates);
if ($field1) {
    echo "<br /> The free_valuation_limit field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_FREEVALUATIONLIMIT';
    $field1->name = 'free_valuation_limit';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'free_valuation_limit';
    $field1->columntype = 'INT(10)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('declared_value', $moduleQuotes);
if ($field1) {
    echo "<br /> The declared_value field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_DECLAREDVALUE';
    $field1->name = 'declared_value';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'declared_value';
    $field1->columntype = 'INT(10)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('declared_value', $moduleEstimates);
if ($field1) {
    echo "<br /> The declared_value field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_DECLAREDVALUE';
    $field1->name = 'declared_value';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'declared_value';
    $field1->columntype = 'INT(10)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('valuation_flat_charge', $moduleQuotes);
if ($field1) {
    echo "<br /> The valuation_flat_charge field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_VALUATIONFLATCHARGE';
    $field1->name = 'valuation_flat_charge';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'valuation_flat_charge';
    $field1->columntype = 'DECIMAL(10, 2)';
    $field1->uitype = 71;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('valuation_flat_charge', $moduleEstimates);
if ($field1) {
    echo "<br /> The valuation_flat_charge field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_VALUATIONFLATCHARGE';
    $field1->name = 'valuation_flat_charge';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'valuation_flat_charge';
    $field1->columntype = 'DECIMAL(10, 2)';
    $field1->uitype = 71;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('rate_per_100', $moduleQuotes);
if ($field1) {
    echo "<br /> The rate_per_100 field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_RATEPER100';
    $field1->name = 'rate_per_100';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'rate_per_100';
    $field1->columntype = 'DECIMAL(10, 2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('rate_per_100', $moduleEstimates);
if ($field1) {
    echo "<br /> The rate_per_100 field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_RATEPER100';
    $field1->name = 'rate_per_100';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'rate_per_100';
    $field1->columntype = 'DECIMAL(10, 2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}

$field1 = Vtiger_Field::getInstance('free_valuation_type', $moduleQuotes);
if ($field1) {
    echo "<br /> The free_valuation_type field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_FREEVALUATIONTYPE';
    $field1->name = 'free_valuation_type';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'free_valuation_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('free_valuation_type', $moduleEstimates);
if ($field1) {
    echo "<br /> The free_valuation_type field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_FREEVALUATIONTYPE';
    $field1->name = 'free_valuation_type';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'free_valuation_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);

    $field1->setPicklistValues(['Free', 'Flat Charge', 'Increased Base Liability']);
}

$field1 = Vtiger_Field::getInstance('increased_base', $moduleQuotes);
if ($field1) {
    echo "<br /> The increased_base field already exists in Quotes <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_INCREASEBASE';
    $field1->name = 'increased_base';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'increased_base';
    $field1->columntype = 'DECIMAL(10, 2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockQuotes->addField($field1);
}

$field1 = Vtiger_Field::getInstance('increased_base', $moduleEstimates);
if ($field1) {
    echo "<br /> The increased_base field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATES_INCREASEBASE';
    $field1->name = 'increased_base';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'increased_base';
    $field1->columntype = 'DECIMAL(10, 2)';
    $field1->uitype = 7;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;

    $blockEstimates->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";