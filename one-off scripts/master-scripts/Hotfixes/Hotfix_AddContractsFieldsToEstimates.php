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


$module = Vtiger_Module::getInstance('Estimates');
$block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);

//Add our load date field.
$field1 = Vtiger_Field::getInstance('load_date', $module);
if ($field1) {
    echo "<li>The load_date field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_QUOTES_LOAD_DATE';
    $field1->name = 'load_date';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'load_date';
    $field1->columntype = 'DATE';
    $field1->uitype = 5;
    $field1->typeofdata = 'D~O';
    $field1->displaytype = 1;

    $block1->addField($field1);
}
//Add our UI type 10 to Contracts, this will be hidden by the TPL files unless business line is Interstate Move
$field2 = Vtiger_Field::getInstance('contract', $module);
if ($field2) {
    echo "<li>The contract field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_CONTRACT';
    $field2->name = 'contract';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'contract';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;

    $block1->addField($field2);
    $field2->setRelatedModules(array('Contracts'));
}
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
//add our discounts to the interstate details block
$field3 = Vtiger_Field::getInstance('irr_charge', $module);
if ($field3) {
    echo "<li>The irr_charge field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_IRR';
    $field3->name = 'irr_charge';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'irr_charge';
    $field3->columntype = 'DECIMAL(7,2)';
    $field3->uitype = 9;
    $field3->typeofdata = 'N~O';
    
    $block2->addField($field3);
}
$field4 = Vtiger_Field::getInstance('linehaul_disc', $module);
if ($field4) {
    echo "<li>The linehaul_disc field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_LINEHAUL_DISC';
    $field4->name = 'linehaul_disc';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'linehaul_disc';
    $field4->columntype = 'DECIMAL(7,2)';
    $field4->uitype = 9;
    $field4->typeofdata = 'N~O';
    
    $block2->addField($field4);
}
$field5 = Vtiger_Field::getInstance('accessorial_disc', $module);
if ($field5) {
    echo "<li>The accessorial_disc field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_ACCESSORIAL_DISC';
    $field5->name = 'accessorial_disc';
    $field5->table = 'vtiger_quotes';
    $field5->column = 'accessorial_disc';
    $field5->columntype = 'DECIMAL(7,2)';
    $field5->uitype = 9;
    $field5->typeofdata = 'N~O';
    
    $block2->addField($field5);
}
$field6 = Vtiger_Field::getInstance('packing_disc', $module);
if ($field6) {
    echo "<li>The packing_disc field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_PACKING_DISC';
    $field6->name = 'packing_disc';
    $field6->table = 'vtiger_quotes';
    $field6->column = 'packing_disc';
    $field6->columntype = 'DECIMAL(7,2)';
    $field6->uitype = 9;
    $field6->typeofdata = 'N~O';
    
    $block2->addField($field6);
}
$field7 = Vtiger_Field::getInstance('sit_disc', $module);
if ($field7) {
    echo "<li>The sit_disc field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_QUOTES_SIT_DISC';
    $field7->name = 'sit_disc';
    $field7->table = 'vtiger_quotes';
    $field7->column = 'sit_disc';
    $field7->columntype = 'DECIMAL(7,2)';
    $field7->uitype = 9;
    $field7->typeofdata = 'N~O';
    
    $block2->addField($field7);
}
//repeat this on quotes so decoupling doesn't stop things from working
$module = Vtiger_Module::getInstance('Quotes');
$block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);

//Add our load date field.
$field1 = Vtiger_Field::getInstance('load_date', $module);
if ($field1) {
    echo "<li>The load_date field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_QUOTES_LOAD_DATE';
    $field1->name = 'load_date';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'load_date';
    $field1->columntype = 'DATE';
    $field1->uitype = 5;
    $field1->typeofdata = 'D~O';
    $field1->displaytype = 1;

    $block1->addField($field1);
}
//Add our UI type 10 to Contracts, this will be hidden by the TPL files unless business line is Interstate Move
$field2 = Vtiger_Field::getInstance('contract', $module);
if ($field2) {
    echo "<li>The contract field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_CONTRACT';
    $field2->name = 'contract';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'contract';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;

    $block1->addField($field2);
    $field2->setRelatedModules(array('Contracts'));
}
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
//add our discounts to the interstate details block
$field3 = Vtiger_Field::getInstance('irr_charge', $module);
if ($field3) {
    echo "<li>The irr_charge field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_IRR';
    $field3->name = 'irr_charge';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'irr_charge';
    $field3->columntype = 'DECIMAL(7,2)';
    $field3->uitype = 9;
    $field3->typeofdata = 'N~O';
    
    $block2->addField($field3);
}
$field4 = Vtiger_Field::getInstance('linehaul_disc', $module);
if ($field4) {
    echo "<li>The linehaul_disc field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_LINEHAUL_DISC';
    $field4->name = 'linehaul_disc';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'linehaul_disc';
    $field4->columntype = 'DECIMAL(7,2)';
    $field4->uitype = 9;
    $field4->typeofdata = 'N~O';
    
    $block2->addField($field4);
}
$field5 = Vtiger_Field::getInstance('accessorial_disc', $module);
if ($field5) {
    echo "<li>The accessorial_disc field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_ACCESSORIAL_DISC';
    $field5->name = 'accessorial_disc';
    $field5->table = 'vtiger_quotes';
    $field5->column = 'accessorial_disc';
    $field5->columntype = 'DECIMAL(7,2)';
    $field5->uitype = 9;
    $field5->typeofdata = 'N~O';
    
    $block2->addField($field5);
}
$field6 = Vtiger_Field::getInstance('packing_disc', $module);
if ($field6) {
    echo "<li>The packing_disc field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_PACKING_DISC';
    $field6->name = 'packing_disc';
    $field6->table = 'vtiger_quotes';
    $field6->column = 'packing_disc';
    $field6->columntype = 'DECIMAL(7,2)';
    $field6->uitype = 9;
    $field6->typeofdata = 'N~O';
    
    $block2->addField($field6);
}
$field7 = Vtiger_Field::getInstance('sit_disc', $module);
if ($field7) {
    echo "<li>The sit_disc field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_QUOTES_SIT_DISC';
    $field7->name = 'sit_disc';
    $field7->table = 'vtiger_quotes';
    $field7->column = 'sit_disc';
    $field7->columntype = 'DECIMAL(7,2)';
    $field7->uitype = 9;
    $field7->typeofdata = 'N~O';
    
    $block2->addField($field7);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";