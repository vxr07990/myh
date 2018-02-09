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

// OT4800 - Estimate Module - Update "Move Details" block for Local Tariffs

$module = Vtiger_Module::getInstance('Estimates');
$block1 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module);

$fieldName1 = 'local_weight';
$field1 = Vtiger_Field::getInstance($fieldName1, $module);
if ($field1) {
    echo "<li>The $fieldName1 field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_'. strtoupper($fieldName1);
    $field1->name = $fieldName1;
    $field1->table = 'vtiger_quotes';
    $field1->column = $fieldName1;
    $field1->columntype = 'int(11)';
    $field1->uitype = 7;
    $field1->typeofdata = 'I~O~MIN=0~STEP=100';
    $field1->displaytype = 1;
    $field1->sequence = 1;

    $block1->addField($field1);
}

$fieldName2 = 'local_billed_weight';
$field2 = Vtiger_Field::getInstance($fieldName2, $module);
if ($field2) {
    echo "<li>The $fieldName2 field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_'. strtoupper($fieldName2);
    $field2->name = $fieldName2;
    $field2->table = 'vtiger_quotes';
    $field2->column = $fieldName2;
    $field2->columntype = 'int(11)';
    $field2->uitype = 7;
    $field2->typeofdata = 'I~O~MIN=0~STEP=100';
    $field2->displaytype = 1;
    $field2->sequence = 2;

    $block1->addField($field2);
}

$fieldName3 = 'local_bl_discount';
$field3 = Vtiger_Field::getInstance($fieldName3, $module);
if ($field3) {
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET sequence = 3,typeofdata = 'I~O~MIN=0~STEP=1' WHERE fieldid = ".$field3->id);
    echo "<li>The $fieldName3 field sequence UPDATED</li><br>";
} else {
    echo "<li>The $fieldName3 field DONT exists</li><br>";
}

$fieldName4 = 'effective_date';
$field4 = Vtiger_Field::getInstance($fieldName4, $module);
if ($field4) {
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET sequence = 4 WHERE fieldid = ".$field4->id);
    echo "<li>The $fieldName4 field sequence UPDATED</li><br>";
} else {
    echo "<li>The $fieldName4 field DONT exists</li><br>";
}

$fieldName5 = 'local_mileage';
$field5 = Vtiger_Field::getInstance($fieldName5, $module);
if ($field5) {
    echo "<li>The $fieldName5 field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_'. strtoupper($fieldName5);
    $field5->name = $fieldName5;
    $field5->table = 'vtiger_quotes';
    $field5->column = $fieldName5;
    $field5->columntype = 'int(11)';
    $field5->uitype = 7;
    $field5->typeofdata = 'I~O~MIN=0~STEP=1';
    $field5->displaytype = 1;
    $field5->sequence = 5;

    $block1->addField($field5);
}

$fieldName6 = 'local_cubes';
$field6 = Vtiger_Field::getInstance($fieldName6, $module);
if ($field6) {
    echo "<li>The $fieldName6 field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_'. strtoupper($fieldName6);
    $field6->name = $fieldName6;
    $field6->table = 'vtiger_quotes';
    $field6->column = $fieldName6;
    $field6->columntype = 'int(11)';
    $field6->uitype = 7;
    $field6->typeofdata = 'I~O~MIN=0~STEP=100';
    $field6->displaytype = 1;
    $field6->sequence = 6;

    $block1->addField($field6);
}

$fieldName7 = 'local_piece_count';
$field7 = Vtiger_Field::getInstance($fieldName7, $module);
if ($field7) {
    echo "<li>The $fieldName7 field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_'. strtoupper($fieldName7);
    $field7->name = $fieldName7;
    $field7->table = 'vtiger_quotes';
    $field7->column = $fieldName7;
    $field7->columntype = 'int(11)';
    $field7->uitype = 7;
    $field7->typeofdata = 'I~O~MIN=0~STEP=1';
    $field7->displaytype = 1;
    $field7->sequence = 7;

    $block1->addField($field7);
}

$fieldName8 = 'local_pack_count';
$field8 = Vtiger_Field::getInstance($fieldName8, $module);
if ($field8) {
    echo "<li>The $fieldName8 field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_'. strtoupper($fieldName8);
    $field8->name = $fieldName8;
    $field8->table = 'vtiger_quotes';
    $field8->column = $fieldName8;
    $field8->columntype = 'int(11)';
    $field8->uitype = 7;
    $field8->typeofdata = 'I~O~MIN=0~STEP=1';
    $field8->displaytype = 1;
    $field8->sequence = 8;

    $block1->addField($field8);
}
