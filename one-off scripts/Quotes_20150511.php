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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Quotes');
echo "<h2>Updating Quotes Fields</h2><br>";

//start block1 : LBL_QUOTES_ACCESSORIALDETAILS
$block1 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $module);
if ($block1) {
    echo "<h3>The LBL_QUOTES_ACCESSORIALDETAILS block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'Accessorial Details';
    $module->addBlock($block1);
}

echo "<ul>";

$field1 = Vtiger_Field::getInstance('acc_shuttle_origin_weight', $module);
if ($field1) {
    echo "<li>The acc_shuttle_origin_weight field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_QUOTES_ACCSHUTTLEORIGINWEIGHT';
    $field1->name = 'acc_shuttle_origin_weight';
    $field1->tablename = 'vtiger_quotes';
    $field1->column = 'acc_shuttle_origin_weight';
    $field1->columntype = 'INT(10)';
    $field1->uitype = 7;
    $field1->typeofdata = 'I~O';

    $block1->addField($field1);
}

$field2 = Vtiger_Field::getInstance('acc_shuttle_dest_weight', $module);
if ($field2) {
    echo "<li>The acc_shuttle_dest_weight field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONWEIGHT';
    $field2->name = 'acc_shuttle_dest_weight';
    $field2->tablename = 'vtiger_quotes';
    $field2->column = 'acc_shuttle_dest_weight';
    $field2->columntype = 'INT(10)';
    $field2->uitype = 7;
    $field2->typeofdata = 'I~O';

    $block1->addField($field2);
}

$field3 = Vtiger_Field::getInstance('acc_shuttle_origin_applied', $module);
if ($field3) {
    echo "<li>The acc_shuttle_origin_applied field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_ACCSHUTTLEORIGINAPPLIED';
    $field3->name = 'acc_shuttle_origin_applied';
    $field3->tablename = 'vtiger_quotes';
    $field3->column = 'acc_shuttle_origin_applied';
    $field3->columntype = 'VARCHAR(3)';
    $field3->uitype = 56;
    $field3->typeofdata = 'C~O';

    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('acc_shuttle_dest_applied', $module);
if ($field4) {
    echo "<li>The acc_shuttle_dest_applied field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONAPPLIED';
    $field4->name = 'acc_shuttle_dest_applied';
    $field4->tablename = 'vtiger_quotes';
    $field4->column = 'acc_shuttle_dest_applied';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'C~O';

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('acc_shuttle_origin_ot', $module);
if ($field5) {
    echo "<li>The acc_shuttle_origin_ot field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOT';
    $field5->name = 'acc_shuttle_origin_ot';
    $field5->tablename = 'vtiger_quotes';
    $field5->column = 'acc_shuttle_origin_ot';
    $field5->columntype = 'VARCHAR(3)';
    $field5->uitype = 56;
    $field5->typeofdata = 'C~O';

    $block1->addField($field5);
}

$field6 = Vtiger_Field::getInstance('acc_shuttle_dest_ot', $module);
if ($field6) {
    echo "<li>The acc_shuttle_dest_ot field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOT';
    $field6->name = 'acc_shuttle_dest_ot';
    $field6->tablename = 'vtiger_quotes';
    $field6->column = 'acc_shuttle_dest_ot';
    $field6->columntype = 'VARCHAR(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'C~O';

    $block1->addField($field6);
}

$field7 = Vtiger_Field::getInstance('acc_shuttle_origin_over25', $module);
if ($field7) {
    echo "<li>The acc_shuttle_origin_over25 field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOVER25';
    $field7->name = 'acc_shuttle_origin_over25';
    $field7->tablename = 'vtiger_quotes';
    $field7->column = 'acc_shuttle_origin_over25';
    $field7->columntype = 'VARCHAR(3)';
    $field7->uitype = 56;
    $field7->typeofdata = 'C~O';

    $block1->addField($field7);
}

$field8 = Vtiger_Field::getInstance('acc_shuttle_dest_over25', $module);
if ($field8) {
    echo "<li>The acc_shuttle_dest_over25 field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOVER25';
    $field8->name = 'acc_shuttle_dest_over25';
    $field8->tablename = 'vtiger_quotes';
    $field8->column = 'acc_shuttle_dest_over25';
    $field8->columntype = 'VARCHAR(3)';
    $field8->uitype = 56;
    $field8->typeofdata = 'C~O';

    $block1->addField($field8);
}

$field9 = Vtiger_Field::getInstance('acc_shuttle_origin_miles', $module);
if ($field9) {
    echo "<li>The acc_shuttle_origin_miles field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_QUOTES_ACCSHUTTLEORIGINMILES';
    $field9->name = 'acc_shuttle_origin_miles';
    $field9->tablename = 'vtiger_quotes';
    $field9->column = 'acc_shuttle_origin_miles';
    $field9->columntype = 'INT(10)';
    $field9->uitype = 7;
    $field9->typeofdata = 'I~O';

    $block1->addField($field9);
}

$field10 = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $module);
if ($field10) {
    echo "<li>The acc_shuttle_dest_miles field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONMILES';
    $field10->name = 'acc_shuttle_dest_miles';
    $field10->tablename = 'vtiger_quotes';
    $field10->column = 'acc_shuttle_dest_miles';
    $field10->columntype = 'INT(10)';
    $field10->uitype = 7;
    $field10->typeofdata = 'I~O';

    $block1->addField($field10);
}

$field11 = Vtiger_Field::getInstance('acc_ot_origin_weight', $module);
if ($field11) {
    echo "<li>The acc_ot_origin_weight field already exists</li><br> \n";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_QUOTES_ACCOTORIGINWEIGHT';
    $field11->name = 'acc_ot_origin_weight';
    $field11->tablename = 'vtiger_quotes';
    $field11->column = 'acc_ot_origin_weight';
    $field11->columntype = 'INT(10)';
    $field11->uitype = 7;
    $field11->typeofdata = 'I~O';

    $block1->addField($field11);
}

$field12 = Vtiger_Field::getInstance('acc_ot_dest_weight', $module);
if ($field12) {
    echo "<li>The acc_ot_dest_weight field already exists</li><br> \n";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_QUOTES_ACCOTDESTINATIONWEIGHT';
    $field12->name = 'acc_ot_dest_weight';
    $field12->tablename = 'vtiger_quotes';
    $field12->column = 'acc_ot_dest_weight';
    $field12->columntype = 'INT(10)';
    $field12->uitype = 7;
    $field12->typeofdata = 'I~O';

    $block1->addField($field12);
}

$field13 = Vtiger_Field::getInstance('acc_ot_origin_applied', $module);
if ($field13) {
    echo "<li>The acc_ot_origin_applied field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_QUOTES_ACCOTORIGINAPPLIED';
    $field13->name = 'acc_ot_origin_applied';
    $field13->tablename = 'vtiger_quotes';
    $field13->column = 'acc_ot_origin_applied';
    $field13->columntype = 'VARCHAR(3)';
    $field13->uitype = 56;
    $field13->typeofdata = 'C~O';

    $block1->addField($field13);
}
$field14 = Vtiger_Field::getInstance('acc_ot_dest_applied', $module);
if ($field14) {
    echo "<li>The acc_ot_dest_applied field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_QUOTES_ACCOTDESTINATIONAPPLIED';
    $field14->name = 'acc_ot_dest_applied';
    $field14->tablename = 'vtiger_quotes';
    $field14->column = 'acc_ot_dest_applied';
    $field14->columntype = 'VARCHAR(3)';
    $field14->uitype = 56;
    $field14->typeofdata = 'C~O';

    $block1->addField($field14);
}

$field15 = Vtiger_Field::getInstance('acc_selfstg_origin_weight', $module);
if ($field15) {
    echo "<li>The acc_selfstg_origin_weight field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_QUOTES_ACCSELFSTGORIGINWEIGHT';
    $field15->name = 'acc_selfstg_origin_weight';
    $field15->tablename = 'vtiger_quotes';
    $field15->column = 'acc_selfstg_origin_weight';
    $field15->columntype = 'INT(10)';
    $field15->uitype = 7;
    $field15->typeofdata = 'I~O';

    $block1->addField($field15);
}

$field16 = Vtiger_Field::getInstance('acc_selfstg_dest_weight', $module);
if ($field16) {
    echo "<li>The acc_selfstg_dest_weight field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONWEIGHT';
    $field16->name = 'acc_selfstg_dest_weight';
    $field16->tablename = 'vtiger_quotes';
    $field16->column = 'acc_selfstg_dest_weight';
    $field16->columntype = 'INT(10)';
    $field16->uitype = 7;
    $field16->typeofdata = 'I~O';

    $block1->addField($field16);
}

$field17 = Vtiger_Field::getInstance('acc_selfstg_origin_applied', $module);
if ($field17) {
    echo "<li>The acc_selfstg_origin_applied field already exists</li><br> \n";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_QUOTES_ACCSELFSTGORIGINAPPLIED';
    $field17->name = 'acc_selfstg_origin_applied';
    $field17->tablename = 'vtiger_quotes';
    $field17->column = 'acc_selfstg_origin_applied';
    $field17->columntype = 'VARCHAR(3)';
    $field17->uitype = 56;
    $field17->typeofdata = 'C~O';

    $block1->addField($field17);
}

$field18 = Vtiger_Field::getInstance('acc_selfstg_dest_applied', $module);
if ($field18) {
    echo "<li>The acc_selfstg_dest_applied field already exists</li><br> \n";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONAPPLIED';
    $field18->name = 'acc_selfstg_dest_applied';
    $field18->tablename = 'vtiger_quotes';
    $field18->column = 'acc_selfstg_dest_applied';
    $field18->columntype = 'VARCHAR(3)';
    $field18->uitype = 56;
    $field18->typeofdata = 'C~O';

    $block1->addField($field18);
}

$field19 = Vtiger_Field::getInstance('acc_selfstg_origin_ot', $module);
if ($field19) {
    echo "<li>The acc_selfstg_origin_ot field already exists</li><br> \n";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_QUOTES_ACCSELFSTGORIGINOT';
    $field19->name = 'acc_selfstg_origin_ot';
    $field19->tablename = 'vtiger_quotes';
    $field19->column = 'acc_selfstg_origin_ot';
    $field19->columntype = 'VARCHAR(3)';
    $field19->uitype = 56;
    $field19->typeofdata = 'C~O';

    $block1->addField($field19);
}

$field20 = Vtiger_Field::getInstance('acc_selfstg_dest_ot', $module);
if ($field20) {
    echo "<li>The acc_selfstg_dest_ot field already exists</li><br> \n";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONOT';
    $field20->name = 'acc_selfstg_dest_ot';
    $field20->tablename = 'vtiger_quotes';
    $field20->column = 'acc_selfstg_dest_ot';
    $field20->columntype = 'VARCHAR(3)';
    $field20->uitype = 56;
    $field20->typeofdata = 'C~O';

    $block1->addField($field20);
}
//start fields from Acc_Add_Other_Fields.php
$field21 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $module);
if ($field21) {
    echo "<li>The acc_exlabor_origin_hours field already exists</li><br> \n";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_QUOTES_ACCEXLABORORIGINHOURS';
    $field21->name = 'acc_exlabor_origin_hours';
    $field21->tablename = 'vtiger_quotes';
    $field21->column = 'acc_exlabor_origin_hours';
    $field21->columntype = 'DECIMAL(4,2)';
    $field21->uitype = 7;
    $field21->typeofdata = 'N~O';

    $block1->addField($field21);
}

$field22 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $module);
if ($field22) {
    echo "<li>The acc_exlabor_dest_hours field already exists</li><br> \n";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_QUOTES_ACCEXLABORDESTINATIONHOURS';
    $field22->name = 'acc_exlabor_dest_hours';
    $field22->tablename = 'vtiger_quotes';
    $field22->column = 'acc_exlabor_dest_hours';
    $field22->columntype = 'DECIMAL(4,2)';
    $field22->uitype = 7;
    $field22->typeofdata = 'N~O';

    $block1->addField($field22);
}

$field23 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $module);
if ($field23) {
    echo "<li>The acc_exlabor_ot_origin_hours field already exists</li><br> \n";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_QUOTES_ACCEXLABOROTORIGINHOURS';
    $field23->name = 'acc_exlabor_ot_origin_hours';
    $field23->tablename = 'vtiger_quotes';
    $field23->column = 'acc_exlabor_ot_origin_hours';
    $field23->columntype = 'DECIMAL(4,2)';
    $field23->uitype = 7;
    $field23->typeofdata = 'N~O';

    $block1->addField($field23);
}

$field24 = Vtiger_Field::getInstance('acc_exlabor_ot_dest_hours', $module);
if ($field24) {
    echo "<li>The acc_exlabor_ot_dest_hours field already exists</li><br> \n";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_QUOTES_ACCEXLABOROTDESTINATIONHOURS';
    $field24->name = 'acc_exlabor_ot_dest_hours';
    $field24->tablename = 'vtiger_quotes';
    $field24->column = 'acc_exlabor_ot_dest_hours';
    $field24->columntype = 'DECIMAL(4,2)';
    $field24->uitype = 7;
    $field24->typeofdata = 'N~O';

    $block1->addField($field24);
}

$field25 = Vtiger_Field::getInstance('acc_wait_origin_hours', $module);
if ($field25) {
    echo "<li>The acc_wait_origin_hours field already exists</li><br> \n";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_QUOTES_ACCWAITORIGINHOURS';
    $field25->name = 'acc_wait_origin_hours';
    $field25->tablename = 'vtiger_quotes';
    $field25->column = 'acc_wait_origin_hours';
    $field25->columntype = 'DECIMAL(4,2)';
    $field25->uitype = 7;
    $field25->typeofdata = 'N~O';

    $block1->addField($field25);
}

$field26 = Vtiger_Field::getInstance('acc_wait_dest_hours', $module);
if ($field26) {
    echo "<li>The acc_wait_dest_hours field already exists</li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_QUOTES_ACCWAITDESTINATIONHOURS';
    $field26->name = 'acc_wait_dest_hours';
    $field26->tablename = 'vtiger_quotes';
    $field26->column = 'acc_wait_dest_hours';
    $field26->columntype = 'DECIMAL(4,2)';
    $field26->uitype = 7;
    $field26->typeofdata = 'N~O';

    $block1->addField($field26);
}

$field27 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $module);
if ($field27) {
    echo "<li>The acc_wait_ot_origin_hours field already exists</li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_QUOTES_ACCWAITOTORIGINHOURS';
    $field27->name = 'acc_wait_ot_origin_hours';
    $field27->tablename = 'vtiger_quotes';
    $field27->column = 'acc_wait_ot_origin_hours';
    $field27->columntype = 'DECIMAL(4,2)';
    $field27->uitype = 7;
    $field27->typeofdata = 'N~O';

    $block1->addField($field27);
}

$field28 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $module);
if ($field28) {
    echo "<li>The acc_wait_ot_dest_hours field already exists</li><br> \n";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_QUOTES_ACCWAITOTDESTINATIONHOURS';
    $field28->name = 'acc_wait_ot_dest_hours';
    $field28->tablename = 'vtiger_quotes';
    $field28->column = 'acc_wait_ot_dest_hours';
    $field28->columntype = 'DECIMAL(4,2)';
    $field28->uitype = 7;
    $field28->typeofdata = 'N~O';

    $block1->addField($field28);
}
echo "</ul>";
$block1->save($module);
//end block1 : LBL_QUOTES_ACCESSORIALDETAILS

//start block2 : LBL_QUOTES_INTERSTATEMOVEDETAILS
//originally from Add_Milage.php
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
if ($block2) {
    echo "<h3>The LBL_QUOTES_INTERSTATEMOVEDETAILS block already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
    $module->addBlock($block2);
}
echo "<ul>";

$field29 = Vtiger_Field::getInstance('interstate_mileage', $module);
if ($field29) {
    echo "<li>The interstate_mileage field already exists</li><br> \n";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_QUOTES_MILEAGE';
    $field29->name = 'interstate_mileage';
    $field29->tablename = 'vtiger_quotes';
    $field29->column = 'interstate_mileage';
    $field29->columntype = 'INT(19)';
    $field29->uitype = 7;
    $field29->typeofdata = 'I~O';

    $block2->addField($field29);
}

//from create_estimates_module.php
$field30 = Vtiger_Field::getInstance('weight', $module);
if ($field30) {
    echo "<li>The weight field already exists</li><br> \n";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_QUOTES_WEIGHT';
    $field30->name = 'weight';
    $field30->tablename = 'vtiger_quotes';
    $field30->column = 'weight';
    $field30->uitype = 7;
    $field30->typeofdata = 'I~O';
    $field30->displaytype = 1;
    $field30->quickcreate = 0;

    $block2->addField($field30);
}

$field31 = Vtiger_Field::getInstance('pickup_date', $module);
if ($field31) {
    echo "<li>The pickup_date field already exists</li><br> \n";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_QUOTES_PICKUPDATE';
    $field31->name = 'pickup_date';
    $field31->tablename = 'vtiger_quotes';
    $field31->column = 'pickup_date';
    $field31->uitype = 5;
    $field31->typeofdata = 'D~O';
    $field31->displaytype = 1;

    $block2->addField($field31);
}

$field32 = Vtiger_Field::getInstance('full_pack', $module);
if ($field32) {
    echo "<li>The full_pack field already exists</li><br> \n";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_QUOTES_FULLPACKAPPLIED';
    $field32->name = 'full_pack';
    $field32->tablename = 'vtiger_quotes';
    $field32->column = 'full_pack';
    $field32->uitype = 56;
    $field32->typeofdata = 'C~O';
    $field32->displaytype = 1;
    $field32->quickcreate = 0;

    $block2->addField($field32);
}

$field33 = Vtiger_Field::getInstance('valuation_deductible', $module);
if ($field33) {
    echo "<li>The valuation_deductible field already exists</li><br> \n";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_QUOTES_VALUATIONDEDUCTIBLE';
    $field33->name = 'valuation_deductible';
    $field33->tablename = 'vtiger_quotes';
    $field33->column = 'valuation_deductible';
    $field33->uitype = 16;
    $field33->typeofdata = 'V~O';
    $field33->displaytype = 1;

    $block2->addField($field33);
}

$field34 = Vtiger_Field::getInstance('full_unpack', $module);
if ($field34) {
    echo "<li>The full_unpack field already exists</li><br> \n";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_QUOTES_FULLUNPACKAPPLIED';
    $field34->name = 'full_unpack';
    $field34->tablename = 'vtiger_quotes';
    $field34->column = 'full_unpack';
    $field34->uitype = 56;
    $field34->typeofdata = 'C~O';
    $field34->displaytype = 1;
    $field34->quickcreate = 0;

    $block2->addField($field34);
}

$field35 = Vtiger_Field::getInstance('valuation_amount', $module);
if ($field35) {
    echo "<li>The valuation_amount field already exists</li><br> \n";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_QUOTES_VALUATIONAMOUNT';
    $field35->name = 'valuation_amount';
    $field35->tablename = 'vtiger_quotes';
    $field35->column = 'valuation_amount';
    $field35->uitype = 71;
    $field35->typeofdata = 'N~O';
    $field35->displaytype = 1;

    $block2->addField($field35);
}

$field36 = Vtiger_Field::getInstance('bottom_line_discount', $module);
if ($field36) {
    echo "<li>The bottom_line_discount field already exists</li><br> \n";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_QUOTES_BOTTOMLINEDISCOUNT';
    $field36->name = 'bottom_line_discount';
    $field36->tablename = 'vtiger_quotes';
    $field36->column = 'bottom_line_discount';
    $field36->uitype = 7;
    $field36->typeofdata = 'NN~O';
    $field36->displaytype = 1;
    $field36->quickcreate = 0;

    $block2->addField($field36);
}

$field37 = Vtiger_Field::getInstance('interstate_mileage', $module);
if ($field37) {
    echo "<li>The interstate_mileage field already exists</li><br> \n";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_QUOTES_MILEAGE';
    $field37->name = 'interstate_mileage';
    $field37->tablename = 'vtiger_quotes';
    $field37->column = 'interstate_mileage';
    $field37->uitype = 7;
    $field37->typeofdata = 'I~O';
    $field37->displaytype = 1;

    $block2->addField($field37);
}

$field38 = Vtiger_Field::getInstance('pickup_time', $module);
if ($field38) {
    echo "<li>The pickup_time field already exists</li><br> \n";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_QUOTES_PICKUPTIME';
    $field38->name = 'pickup_time';
    $field38->tablename = 'vtiger_quotes';
    $field38->column = 'pickup_time';
    $field38->uitype = 14;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 1;

    $block2->addField($field38);
}

$field39 = Vtiger_Field::getInstance('fuel_price', $module);
if ($field39) {
    echo "<li>The fuel_price field already exists</li><br> \n";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_QUOTES_FUELPRICE';
    $field39->name = 'fuel_price';
    $field39->tablename = 'vtiger_quotes';
    $field39->column = 'fuel_price';
    $field39->uitype = 71;
    $field39->typeofdata = 'N~O';
    $field39->displaytype = 1;

    $block2->addField($field39);
}

$field40 = Vtiger_Field::getInstance('rate_estimate', $module);
if ($field40) {
    echo "<li>The rate_estimate field already exists</li><br> \n";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_QUOTES_RATEESTIMATE';
    $field40->name = 'rate_estimate';
    $field40->tablename = 'vtiger_quotes';
    $field40->column = 'rate_estimate';
    $field40->uitype = 71;
    $field40->typeofdata = 'N~O';
    $field40->displaytype = 1;

    $block2->addField($field40);
}
echo "</ul>";
$block2->save($module);
//end block2 : LBL_QUOTES_INTERSTATEMOVEDETAILS

//start block3 : LBL_QUOTES_SITDETAILS
//from SIT_Details_Add_Fields.php
$block3 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $module);
if ($block3) {
    echo "<h3>The LBL_QUOTES_SITDETAILS block already exists</h3><br> \n";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_QUOTES_SITDETAILS';
    $module->addBlock($block3);
}
echo "<ul>";

$field41 = Vtiger_Field::getInstance('sit_origin_date_in', $module);
if ($field41) {
    echo "<li>The sit_origin_date_in field already exists</li><br> \n";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_QUOTES_SITORIGINDATEIN';
    $field41->name = 'sit_origin_date_in';
    $field41->tablename = 'vtiger_quotes';
    $field41->column = 'sit_origin_date_in';
    $field41->columntype = 'DATE';
    $field41->uitype = 5;
    $field41->typeofdata = 'D~O';

    $block3->addField($field41);
}

$field42 = Vtiger_Field::getInstance('sit_dest_date_in', $module);
if ($field42) {
    echo "<li>The sit_dest_date_in field already exists</li><br> \n";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_QUOTES_SITDESTINATIONDATEIN';
    $field42->name = 'sit_dest_date_in';
    $field42->tablename = 'vtiger_quotes';
    $field42->column = 'sit_dest_date_in';
    $field42->columntype = 'DATE';
    $field42->uitype = 5;
    $field42->typeofdata = 'D~O';

    $block3->addField($field42);
}

$field43 = Vtiger_Field::getInstance('sit_origin_pickup_date', $module);
if ($field43) {
    echo "<li>The sit_origin_pickup_date field already exists</li><br> \n";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_QUOTES_SITORIGINPICKUPDATE';
    $field43->name = 'sit_origin_pickup_date';
    $field43->tablename = 'vtiger_quotes';
    $field43->column = 'sit_origin_pickup_date';
    $field43->columntype = 'DATE';
    $field43->uitype = 5;
    $field43->typeofdata = 'D~O';

    $block3->addField($field43);
}

$field44 = Vtiger_Field::getInstance('sit_dest_delivery_date', $module);
if ($field44) {
    echo "<li>The sit_dest_delivery_date field already exists</li><br> \n";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_QUOTES_SITDELIVERYDATE';
    $field44->name = 'sit_dest_delivery_date';
    $field44->tablename = 'vtiger_quotes';
    $field44->column = 'sit_dest_delivery_date';
    $field44->columntype = 'DATE';
    $field44->uitype = 5;
    $field44->typeofdata = 'D~O';

    $block3->addField($field44);
}

$field45 = Vtiger_Field::getInstance('sit_origin_weight', $module);
if ($field45) {
    echo "<li>The sit_origin_weight field already exists</li><br> \n";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_QUOTES_SITORIGINWEIGHT';
    $field45->name = 'sit_origin_weight';
    $field45->tablename = 'vtiger_quotes';
    $field45->column = 'sit_origin_weight';
    $field45->columntype = 'INT(10)';
    $field45->uitype = 7;
    $field45->typeofdata = 'I~O';

    $block3->addField($field45);
}

$field46 = Vtiger_Field::getInstance('sit_dest_weight', $module);
if ($field46) {
    echo "<li>The sit_dest_weight field already exists</li><br> \n";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_QUOTES_SITDESTINATIONWEIGHT';
    $field46->name = 'sit_dest_weight';
    $field46->tablename = 'vtiger_quotes';
    $field46->column = 'sit_dest_weight';
    $field46->columntype = 'INT(10)';
    $field46->uitype = 7;
    $field46->typeofdata = 'I~O';

    $block3->addField($field46);
}

$field47 = Vtiger_Field::getInstance('sit_origin_zip', $module);
if ($field47) {
    echo "<li>The sit_origin_zip field already exists</li><br> \n";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_QUOTES_SITORIGINZIP';
    $field47->name = 'sit_origin_zip';
    $field47->tablename = 'vtiger_quotes';
    $field47->column = 'sit_origin_zip';
    $field47->columntype = 'INT(10)';
    $field47->uitype = 7;
    $field47->typeofdata = 'I~O';

    $block3->addField($field47);
}

$field48 = Vtiger_Field::getInstance('sit_dest_zip', $module);
if ($field48) {
    echo "<li>The sit_dest_zip field already exists</li><br> \n";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_QUOTES_SITDESTINATIONZIP';
    $field48->name = 'sit_dest_zip';
    $field48->tablename = 'vtiger_quotes';
    $field48->column = 'sit_dest_zip';
    $field48->columntype = 'INT(10)';
    $field48->uitype = 7;
    $field48->typeofdata = 'I~O';

    $block3->addField($field48);
}

$field49 = Vtiger_Field::getInstance('sit_origin_miles', $module);
if ($field49) {
    echo "<li>The sit_origin_miles field already exists</li><br> \n";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_QUOTES_SITORIGINMILES';
    $field49->name = 'sit_origin_miles';
    $field49->tablename = 'vtiger_quotes';
    $field49->column = 'sit_origin_miles';
    $field49->columntype = 'INT(10)';
    $field49->uitype = 7;
    $field49->typeofdata = 'I~O';

    $block3->addField($field49);
}

$field50 = Vtiger_Field::getInstance('sit_dest_miles', $module);
if ($field50) {
    echo "<li>The sit_dest_miles field already exists</li><br> \n";
} else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_QUOTES_SITDESTINATIONMILES';
    $field50->name = 'sit_dest_miles';
    $field50->tablename = 'vtiger_quotes';
    $field50->column = 'sit_dest_miles';
    $field50->columntype = 'INT(10)';
    $field50->uitype = 7;
    $field50->typeofdata = 'I~O';

    $block3->addField($field50);
}

$field51 = Vtiger_Field::getInstance('sit_origin_number_days', $module);
if ($field51) {
    echo "<li>The sit_origin_number_days field already exists</li><br> \n";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_QUOTES_SITORIGINNUMBERDAYS';
    $field51->name = 'sit_origin_number_days';
    $field51->tablename = 'vtiger_quotes';
    $field51->column = 'sit_origin_number_days';
    $field51->columntype = 'INT(10)';
    $field51->uitype = 7;
    $field51->typeofdata = 'I~O';

    $block3->addField($field51);
}

$field52 = Vtiger_Field::getInstance('sit_dest_number_days', $module);
if ($field52) {
    echo "<li>The sit_dest_number_days field already exists</li><br> \n";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_QUOTES_SITDESTINATIONNUMBERDAYS';
    $field52->name = 'sit_dest_number_days';
    $field52->tablename = 'vtiger_quotes';
    $field52->column = 'sit_dest_number_days';
    $field52->columntype = 'INT(10)';
    $field52->uitype = 7;
    $field52->typeofdata = 'I~O';

    $block3->addField($field52);
}

$field53 = Vtiger_Field::getInstance('sit_origin_first_day', $module);
if ($field53) {
    echo "<li>The sit_origin_first_day field already exists</li><br> \n";
} else {
    $field53 = new Vtiger_Field();
    $field53->label = 'LBL_QUOTES_SITORIGINFIRSTDAYRATE';
    $field53->name = 'sit_origin_first_day';
    $field53->tablename = 'vtiger_quotes';
    $field53->column = 'sit_origin_first_day';
    $field53->columntype = 'DECIMAL(10,3)';
    $field53->uitype = 71;
    $field53->typeofdata = 'N~O';

    $block3->addField($field53);
}

$field54 = Vtiger_Field::getInstance('sit_dest_first_day', $module);
if ($field54) {
    echo "<li>The sit_dest_first_day field already exists</li><br> \n";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'LBL_QUOTES_SITDESTINATIONFIRSTDAYRATE';
    $field54->name = 'sit_dest_first_day';
    $field54->tablename = 'vtiger_quotes';
    $field54->column = 'sit_dest_first_day';
    $field54->columntype = 'DECIMAL(10,3)';
    $field54->uitype = 71;
    $field54->typeofdata = 'N~O';

    $block3->addField($field54);
}

$field55 = Vtiger_Field::getInstance('sit_origin_first_day_cost', $module);
if ($field55) {
    echo "<li>The sit_origin_first_day_cost field already exists</li><br> \n";
} else {
    $field55 = new Vtiger_Field();
    $field55->label = 'LBL_QUOTES_SITORIGINFIRSTDAYCOST';
    $field55->name = 'sit_origin_first_day_cost';
    $field55->tablename = 'vtiger_quotes';
    $field55->column = 'sit_origin_first_day_cost';
    $field55->columntype = 'DECIMAL(10,3)';
    $field55->uitype = 71;
    $field55->typeofdata = 'N~O';

    $block3->addField($field55);
}

$field56 = Vtiger_Field::getInstance('sit_dest_first_day_cost', $module);
if ($field56) {
    echo "<li>The sit_dest_first_day_cost field already exists</li><br> \n";
} else {
    $field56 = new Vtiger_Field();
    $field56->label = 'LBL_QUOTES_SITDESTINATIONFIRSTDAYCOST';
    $field56->name = 'sit_dest_first_day_cost';
    $field56->tablename = 'vtiger_quotes';
    $field56->column = 'sit_dest_first_day_cost';
    $field56->columntype = 'DECIMAL(10,3)';
    $field56->uitype = 71;
    $field56->typeofdata = 'N~O';

    $block3->addField($field56);
}

$field57 = Vtiger_Field::getInstance('sit_origin_sec_day', $module);
if ($field57) {
    echo "<li>The sit_origin_sec_day field already exists</li><br> \n";
} else {
    $field57 = new Vtiger_Field();
    $field57->label = 'LBL_QUOTES_SITORIGINSECONDDAYRATE';
    $field57->name = 'sit_origin_sec_day';
    $field57->tablename = 'vtiger_quotes';
    $field57->column = 'sit_origin_sec_day';
    $field57->columntype = 'DECIMAL(10,3)';
    $field57->uitype = 71;
    $field57->typeofdata = 'N~O';

    $block3->addField($field57);
}

$field58 = Vtiger_Field::getInstance('sit_dest_sec_day', $module);
if ($field58) {
    echo "<li>The sit_dest_sec_day field already exists</li><br> \n";
} else {
    $field58 = new Vtiger_Field();
    $field58->label = 'LBL_QUOTES_SITDESTINATIONSECONDDAYRATE';
    $field58->name = 'sit_dest_sec_day';
    $field58->tablename = 'vtiger_quotes';
    $field58->column = 'sit_dest_sec_day';
    $field58->columntype = 'DECIMAL(10,3)';
    $field58->uitype = 71;
    $field58->typeofdata = 'N~O';

    $block3->addField($field58);
}

$field59 = Vtiger_Field::getInstance('sit_origin_sec_day_cost', $module);
if ($field59) {
    echo "<li>The sit_origin_sec_day_cost field already exists</li><br> \n";
} else {
    $field59 = new Vtiger_Field();
    $field59->label = 'LBL_QUOTES_SITORIGINSECONDDAYCOST';
    $field59->name = 'sit_origin_sec_day_cost';
    $field59->tablename = 'vtiger_quotes';
    $field59->column = 'sit_origin_sec_day_cost';
    $field59->columntype = 'DECIMAL(10,3)';
    $field59->uitype = 71;
    $field59->typeofdata = 'N~O';

    $block3->addField($field59);
}

$field60 = Vtiger_Field::getInstance('sit_dest_sec_day_cost', $module);
if ($field60) {
    echo "<li>The sit_dest_sec_day_cost field already exists</li><br> \n";
} else {
    $field60 = new Vtiger_Field();
    $field60->label = 'LBL_QUOTES_SITDESTINATIONSECONDDAYCOST';
    $field60->name = 'sit_dest_sec_day_cost';
    $field60->tablename = 'vtiger_quotes';
    $field60->column = 'sit_dest_sec_day_cost';
    $field60->columntype = 'DECIMAL(10,3)';
    $field60->uitype = 71;
    $field60->typeofdata = 'N~O';

    $block3->addField($field60);
}

$field61 = Vtiger_Field::getInstance('sit_origin_pickup_delivery', $module);
if ($field61) {
    echo "<li>The sit_origin_pickup_delivery field already exists</li><br> \n";
} else {
    $field61 = new Vtiger_Field();
    $field61->label = 'LBL_QUOTES_SITORIGINPICKUPDELIVERY';
    $field61->name = 'sit_origin_pickup_delivery';
    $field61->tablename = 'vtiger_quotes';
    $field61->column = 'sit_origin_pickup_delivery';
    $field61->columntype = 'DECIMAL(10,3)';
    $field61->uitype = 71;
    $field61->typeofdata = 'N~O';

    $block3->addField($field61);
}

$field62 = Vtiger_Field::getInstance('sit_dest_pickup_delivery', $module);
if ($field62) {
    echo "<li>The sit_dest_pickup_delivery field already exists</li><br> \n";
} else {
    $field62 = new Vtiger_Field();
    $field62->label = 'LBL_QUOTES_SITDESTINATIONPICKUPDELIVERY';
    $field62->name = 'sit_dest_pickup_delivery';
    $field62->tablename = 'vtiger_quotes';
    $field62->column = 'sit_dest_pickup_delivery';
    $field62->columntype = 'DECIMAL(10,3)';
    $field62->uitype = 71;
    $field62->typeofdata = 'N~O';

    $block3->addField($field62);
}

$field63 = Vtiger_Field::getInstance('sit_origin_fuel_percent', $module);
if ($field63) {
    echo "<li>The sit_origin_fuel_percent field already exists</li><br> \n";
} else {
    $field63 = new Vtiger_Field();
    $field63->label = 'LBL_QUOTES_SITORIGINFUELPERCENT';
    $field63->name = 'sit_origin_fuel_percent';
    $field63->tablename = 'vtiger_quotes';
    $field63->column = 'sit_origin_fuel_percent';
    $field63->columntype = 'DECIMAL(10,3)';
    $field63->uitype = 7;
    $field63->typeofdata = 'N~O';

    $block3->addField($field63);
}

$field64 = Vtiger_Field::getInstance('sit_dest_fuel_percent', $module);
if ($field64) {
    echo "<li>The sit_dest_fuel_percent field already exists</li><br> \n";
} else {
    $field64 = new Vtiger_Field();
    $field64->label = 'LBL_QUOTES_SITDESTINATIONFUELPERCENT';
    $field64->name = 'sit_dest_fuel_percent';
    $field64->tablename = 'vtiger_quotes';
    $field64->column = 'sit_dest_fuel_percent';
    $field64->columntype = 'DECIMAL(10,3)';
    $field64->uitype = 7;
    $field64->typeofdata = 'N~O';

    $block3->addField($field64);
}

$field65 = Vtiger_Field::getInstance('sit_origin_fuel_surcharge', $module);
if ($field65) {
    echo "<li>The sit_origin_fuel_surcharge field already exists</li><br> \n";
} else {
    $field65 = new Vtiger_Field();
    $field65->label = 'LBL_QUOTES_SITORIGINFUELSURCHARGE';
    $field65->name = 'sit_origin_fuel_surcharge';
    $field65->tablename = 'vtiger_quotes';
    $field65->column = 'sit_origin_fuel_surcharge';
    $field65->columntype = 'DECIMAL(10,3)';
    $field65->uitype = 71;
    $field65->typeofdata = 'N~O';

    $block3->addField($field65);
}

$field66 = Vtiger_Field::getInstance('sit_dest_fuel_surcharge', $module);
if ($field66) {
    echo "<li>The sit_dest_fuel_surcharge field already exists</li><br> \n";
} else {
    $field66 = new Vtiger_Field();
    $field66->label = 'LBL_QUOTES_SITDESTINATIONFUELSURCHARGE';
    $field66->name = 'sit_dest_fuel_surcharge';
    $field66->tablename = 'vtiger_quotes';
    $field66->column = 'sit_dest_fuel_surcharge';
    $field66->columntype = 'DECIMAL(10,3)';
    $field66->uitype = 71;
    $field66->typeofdata = 'N~O';

    $block3->addField($field66);
}

$field67 = Vtiger_Field::getInstance('sit_origin_irr_percent', $module);
if ($field67) {
    echo "<li>The sit_origin_irr_percent field already exists</li><br> \n";
} else {
    $field67 = new Vtiger_Field();
    $field67->label = 'LBL_QUOTES_SITORIGINIRRPERCENT';
    $field67->name = 'sit_origin_irr_percent';
    $field67->tablename = 'vtiger_quotes';
    $field67->column = 'sit_origin_irr_percent';
    $field67->columntype = 'DECIMAL(10,3)';
    $field67->uitype = 7;
    $field67->typeofdata = 'N~O';

    $block3->addField($field67);
}

$field68 = Vtiger_Field::getInstance('sit_dest_irr_percent', $module);
if ($field68) {
    echo "<li>The sit_dest_irr_percent field already exists</li><br> \n";
} else {
    $field68 = new Vtiger_Field();
    $field68->label = 'LBL_QUOTES_SITDESTINATIONIRRPERCENT';
    $field68->name = 'sit_dest_irr_percent';
    $field68->tablename = 'vtiger_quotes';
    $field68->column = 'sit_dest_irr_percent';
    $field68->columntype = 'DECIMAL(10,3)';
    $field68->uitype = 7;
    $field68->typeofdata = 'N~O';

    $block3->addField($field68);
}

$field69 = Vtiger_Field::getInstance('sit_origin_irr', $module);
if ($field69) {
    echo "<li>The sit_origin_irr field already exists</li><br> \n";
} else {
    $field69 = new Vtiger_Field();
    $field69->label = 'LBL_QUOTES_SITORIGINIRR';
    $field69->name = 'sit_origin_irr';
    $field69->tablename = 'vtiger_quotes';
    $field69->column = 'sit_origin_irr';
    $field69->columntype = 'DECIMAL(10,3)';
    $field69->uitype = 71;
    $field69->typeofdata = 'N~O';

    $block3->addField($field69);
}

$field70 = Vtiger_Field::getInstance('sit_dest_irr', $module);
if ($field70) {
    echo "<li>The sit_dest_irr field already exists</li><br> \n";
} else {
    $field70 = new Vtiger_Field();
    $field70->label = 'LBL_QUOTES_SITDESTINATIONIRR';
    $field70->name = 'sit_dest_irr';
    $field70->tablename = 'vtiger_quotes';
    $field70->column = 'sit_dest_irr';
    $field70->columntype = 'DECIMAL(10,3)';
    $field70->uitype = 71;
    $field70->typeofdata = 'N~O';

    $block3->addField($field70);
}

$field71 = Vtiger_Field::getInstance('sit_origin_overtime', $module);
if ($field71) {
    echo "<li>The sit_origin_overtime field already exists</li><br> \n";
} else {
    $field71 = new Vtiger_Field();
    $field71->label = 'LBL_QUOTES_SITORIGINOVERTIME';
    $field71->name = 'sit_origin_overtime';
    $field71->tablename = 'vtiger_quotes';
    $field71->column = 'sit_origin_overtime';
    $field71->columntype = 'VARCHAR(3)';
    $field71->uitype = 56;
    $field71->typeofdata = 'C~O';

    $block3->addField($field71);
}

$field72 = Vtiger_Field::getInstance('sit_dest_overtime', $module);
if ($field72) {
    echo "<li>The sit_dest_overtime field already exists</li><br> \n";
} else {
    $field72 = new Vtiger_Field();
    $field72->label = 'LBL_QUOTES_SITDESTINATIONOVERTIME';
    $field72->name = 'sit_dest_overtime';
    $field72->tablename = 'vtiger_quotes';
    $field72->column = 'sit_dest_overtime';
    $field72->columntype = 'VARCHAR(3)';
    $field72->uitype = 56;
    $field72->typeofdata = 'C~O';

    $block3->addField($field72);
}

echo "</ul>";
$block3->save($module);
//End block3 : LBL_QUOTES_SITDETAILS

//Start block4 : LBL_QUOTE_INFORMATION
//from create_estimates_module.php
$block4 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
if ($block4) {
    echo "<h3>The LBL_QUOTE_INFORMATION block already exists</h3><br> \n";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_QUOTE_INFORMATION';
    $module->addBlock($block4);
}
echo "<ul>";

$field73 = Vtiger_Field::getInstance('business_line', $module);
if ($field73) {
    echo "<li>The business_line field already exists</li><br> \n";
} else {
    $field73 = new Vtiger_Field();
    $field73->label = 'LBL_QUOTES_BUSINESSLINE';
    $field73->name = 'business_line';
    $field73->tablename = 'vtiger_quotescf';
    $field73->column = 'business_line';
    $field73->uitype = 16;
    $field73->typeofdata = 'V~O';
    $field73->displaytype = 1;

    $block4->addField($field73);
}

$field88 = Vtiger_Field::getInstance('is_primary', $module);
if ($field88) {
    echo "<li>The is_primary field already exists</li><br> \n";
} else {
    $field88 = new Vtiger_Field();
    $field88->label = 'LBL_QUOTES_ISPRIMARY';
    $field88->name = 'is_primary';
    $field88->table = 'vtiger_quotes';
    $field88->column = 'is_primary';
    $ffield88ield1->columntype = 'VARCHAR(3)';
    $field88->uitype = 56;
    $field88->typeofdata = 'C~O';
    $field88->summaryfield = 1;

    $block4->addField($field88);
}
$field89 = Vtiger_Field::getInstance('potentialid', $module);
if ($field89) {
    echo "<li>The potentialid field already exists</li><br> \n";
} else {
    $field89 = new Vtiger_Field();
    $field89->label = 'LBL_QUOTES_POTENTIALNAME';
    $field89->name = 'potentialid';
    $field89->tablename = 'vtiger_quotes';
    $field89->column = 'potentialid';
    $field89->columntype = 'VARCHAR(100)';
    $field89->uitype = 10;
    $field89->typeofdata = 'V~O';
    $field89->displaytype = 1;

    $field89->setRelatedModules(array('Opportunities'));

    $block4->addField($field89);
}
echo "</ul>";
$block4->save($module);
//End block4 : LBL_QUOTE_INFORMATION

//Start block5 : LBL_QUOTES_CONTACTDETAILS
//from create_estimates_module.php
$block5 = Vtiger_Block::getInstance('LBL_QUOTES_CONTACTDETAILS', $module);
if ($block5) {
    echo "<h3>The LBL_QUOTES_CONTACTDETAILS block already exists</h3><br> \n";
} else {
    $block5 = new Vtiger_Block();
    $block5->label = 'LBL_QUOTES_CONTACTDETAILS';
    $module->addBlock($block5);
}
echo "<ul>";
echo "</ul>";
$block5->save($module);
//End block5 : LBL_QUOTES_CONTACTDETAILS

//Start block6 : LBL_ADDRESS_INFORMATION
//from create_estimates_module.php
$block6 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $module);
if ($block6) {
    echo "<h3>The LBL_ADDRESS_INFORMATION block already exists</h3><br> \n";
} else {
    $block6 = new Vtiger_Block();
    $block6->label = 'LBL_ADDRESS_INFORMATION';
    $module->addBlock($block6);
}

echo "<ul>";
$field74 = Vtiger_Field::getInstance('origin_address1', $module);
if ($field74) {
    echo "<li>The origin_address1 field already exists</li><br> \n";
} else {
    $field74 = new Vtiger_Field();
    $field74->label = 'LBL_QUOTES_ORIGINADDRESS1';
    $field74->name = 'origin_address1';
    $field74->tablename = 'vtiger_quotescf';
    $field74->column = 'origin_address1';
    $field74->uitype = 1;
    $field74->typeofdata = 'V~O~LE~50';
    $field74->displaytype = 1;

    $block6->addField($field74);
}

$field75 = Vtiger_Field::getInstance('destination_address1', $module);
if ($field75) {
    echo "<li>The destination_address1 field already exists</li><br> \n";
} else {
    $field75 = new Vtiger_Field();
    $field75->label = 'LBL_QUOTES_DESTINATIONADDRESS1';
    $field75->name = 'destination_address1';
    $field75->tablename = 'vtiger_quotescf';
    $field75->column = 'destination_address1';
    $field75->uitype = 1;
    $field75->typeofdata = 'V~O~LE~50';
    $field75->displaytype = 1;

    $block6->addField($field75);
}

$field76 = Vtiger_Field::getInstance('origin_address2', $module);
if ($field76) {
    echo "<li>The origin_address2 field already exists</li><br> \n";
} else {
    $field76 = new Vtiger_Field();
    $field76->label = 'LBL_QUOTES_ORIGINADDRESS2';
    $field76->name = 'origin_address2';
    $field76->tablename = 'vtiger_quotescf';
    $field76->column = 'origin_address2';
    $field76->uitype = 1;
    $field76->typeofdata = 'V~O~LE~50';
    $field76->displaytype = 1;

    $block6->addField($field76);
}

$field77 = Vtiger_Field::getInstance('destination_address2', $module);
if ($field77) {
    echo "<li>The destination_address2 field already exists</li><br> \n";
} else {
    $field77 = new Vtiger_Field();
    $field77->label = 'LBL_QUOTES_DESTINATIONADDRESS2';
    $field77->name = 'destination_address2';
    $field77->tablename = 'vtiger_quotescf';
    $field77->column = 'destination_address2';
    $field77->uitype = 1;
    $field77->typeofdata = 'V~O~LE~50';
    $field77->displaytype = 1;

    $block6->addField($field77);
}

$field78 = Vtiger_Field::getInstance('origin_city', $module);
if ($field78) {
    echo "<li>The origin_city field already exists</li><br> \n";
} else {
    $field78 = new Vtiger_Field();
    $field78->label = 'LBL_QUOTES_ORIGINCITY';
    $field78->name = 'origin_city';
    $field78->tablename = 'vtiger_quotescf';
    $field78->column = 'origin_city';
    $field78->uitype = 1;
    $field78->typeofdata = 'V~O~LE~50';
    $field78->displaytype = 1;

    $block6->addField($field78);
}

$field79 = Vtiger_Field::getInstance('destination_city', $module);
if ($field79) {
    echo "<li>The destination_city field already exists</li><br> \n";
} else {
    $field79 = new Vtiger_Field();
    $field79->label = 'LBL_QUOTES_DESTINATIONCITY';
    $field79->name = 'destination_city';
    $field79->tablename = 'vtiger_quotescf';
    $field79->column = 'destination_city';
    $field79->uitype = 1;
    $field79->typeofdata = 'V~O~LE~50';
    $field79->displaytype = 1;

    $block6->addField($field79);
}

$field80 = Vtiger_Field::getInstance('origin_state', $module);
if ($field80) {
    echo "<li>The origin_state field already exists</li><br> \n";
} else {
    $field80 = new Vtiger_Field();
    $field80->label = 'LBL_QUOTES_ORIGINSTATE';
    $field80->name = 'origin_state';
    $field80->tablename = 'vtiger_quotescf';
    $field80->column = 'origin_state';
    $field80->uitype = 1;
    $field80->typeofdata = 'V~O';
    $field80->displaytype = 1;

    $block6->addField($field80);
}

$field81 = Vtiger_Field::getInstance('destination_state', $module);
if ($field81) {
    echo "<li>The destination_state field already exists</li><br> \n";
} else {
    $field81 = new Vtiger_Field();
    $field81->label = 'LBL_QUOTES_DESTINATIONSTATE';
    $field81->name = 'destination_state';
    $field81->tablename = 'vtiger_quotescf';
    $field81->column = 'destination_state';
    $field81->uitype = 1;
    $field81->typeofdata = 'V~O';
    $field81->displaytype = 1;

    $block6->addField($field81);
}

$field82 = Vtiger_Field::getInstance('origin_zip', $module);
if ($field82) {
    echo "<li>The origin_zip field already exists</li><br> \n";
} else {
    $field82 = new Vtiger_Field();
    $field82->label = 'LBL_QUOTES_ORIGINZIP';
    $field82->name = 'origin_zip';
    $field82->tablename = 'vtiger_quotescf';
    $field82->column = 'origin_zip';
    $field82->uitype = 7;
    $field82->typeofdata = 'I~O';
    $field82->displaytype = 1;
    $field82->quickcreate = 0;

    $block6->addField($field82);
}

$field83 = Vtiger_Field::getInstance('destination_zip', $module);
if ($field83) {
    echo "<li>The destination_zip field already exists</li><br> \n";
} else {
    $field83 = new Vtiger_Field();
    $field83->label = 'LBL_QUOTES_DESTINATIONZIP';
    $field83->name = 'destination_zip';
    $field83->tablename = 'vtiger_quotescf';
    $field83->column = 'destination_zip';
    $field83->uitype = 7;
    $field83->typeofdata = 'I~O';
    $field83->displaytype = 1;
    $field83->quickcreate = 0;

    $block6->addField($field83);
}

$field84 = Vtiger_Field::getInstance('origin_phone1', $module);
if ($field84) {
    echo "<li>The origin_phone1 field already exists</li><br> \n";
} else {
    $field84 = new Vtiger_Field();
    $field84->label = 'LBL_QUOTES_ORIGINPHONE1';
    $field84->name = 'origin_phone1';
    $field84->tablename = 'vtiger_quotescf';
    $field84->column = 'origin_phone1';
    $field84->uitype = 11;
    $field84->typeofdata = 'V~O';
    $field84->displaytype = 1;

    $block6->addField($field84);
}

$field85 = Vtiger_Field::getInstance('destination_phone1', $module);
if ($field85) {
    echo "<li>The destination_phone1 field already exists</li><br> \n";
} else {
    $field85 = new Vtiger_Field();
    $field85->label = 'LBL_QUOTES_DESTINATIONPHONE1';
    $field85->name = 'destination_phone1';
    $field85->tablename = 'vtiger_quotescf';
    $field85->column = 'destination_phone1';
    $field85->uitype = 11;
    $field85->typeofdata = 'V~O';
    $field85->displaytype = 1;

    $block6->addField($field85);
}

$field86 = Vtiger_Field::getInstance('origin_phone2', $module);
if ($field86) {
    echo "<li>The origin_phone2 field already exists</li><br> \n";
} else {
    $field86 = new Vtiger_Field();
    $field86->label = 'LBL_QUOTES_ORIGINPHONE2';
    $field86->name = 'origin_phone2';
    $field86->tablename = 'vtiger_quotescf';
    $field86->column = 'origin_phone2';
    $field86->uitype = 11;
    $field86->typeofdata = 'V~O';
    $field86->displaytype = 1;

    $block6->addField($field86);
}

$field87 = Vtiger_Field::getInstance('destination_phone2', $module);
if ($field87) {
    echo "<li>The destination_phone2 field already exists</li><br> \n";
} else {
    $field87 = new Vtiger_Field();
    $field87->label = 'LBL_QUOTES_DESTINATIONPHONE2';
    $field87->name = 'destination_phone2';
    $field87->tablename = 'vtiger_quotescf';
    $field87->column = 'destination_phone2';
    $field87->uitype = 11;
    $field87->typeofdata = 'V~O';
    $field87->displaytype = 1;

    $block6->addField($field87);
}

echo "</ul>";
$block6->save($module);
//End block6 : LBL_ADDRESS_INFORMATION

//End Quotes

if (!Vtiger_Utils::CheckTable('vtiger_misc_accessorials')) {
    Vtiger_Utils::CreateTable('vtiger_misc_accessorials',
        '(quoteid int(10) PRIMARY KEY,
		description text NOT NULL,
		charge decimal(10,2) NOT NULL,
		qty int(10) NOT NULL,
		discounted varchar(3) NOT NULL,
		discount decimal(5,3) NOT NULL,
		line_item_id int(10) NOT NULL AUTO_INCREMENT,
		charge_type varchar(30) NOT NULL)', true);
    echo "<br> vtiger_misc_accessorials table created <br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_misc_accessorials_seq')) {
    Vtiger_Utils::CreateTable('vtiger_misc_accessorials_seq',
            '(id int(19) NOT NULL)', true);
    echo "<br> vtiger_misc_accessorials_seq table created <br>";
    
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_misc_accessorials_seq` (id) VALUES (0)');
    echo "<br> vtiger_misc_accessorials_seq id initialized to 0 <br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_packing_items')) {
    Vtiger_Utils::CreateTable('vtiger_packing_items',
        '(quoteid INT(10) NOT NULL,
		itemid INT(10) NOT NULL,
		pack_qty INT(10) NOT NULL,
		unpack_qty INT(10) NOT NULL,
		ot_pack_qty INT(10) NOT NULL,
		ot_unpack_qty INT(10) NOT NULL,
		PRIMARY KEY(quoteid, itemid))', true);
    echo "<br> vtiger_packing_items table created <br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_bulky_items')) {
    Vtiger_Utils::CreateTable('vtiger_bulky_items',
        '(quoteid INT(10) NOT NULL,
		bulkyid INT(10) NOT NULL,
		ship_qty INT(10) NOT NULL,
		PRIMARY KEY(quoteid, bulkyid))', true);
    echo "<br> vtiger_bulky_items table created <br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_crates')) {
    Vtiger_Utils::CreateTable('vtiger_crates',
        '(`quoteid` int(10) NOT NULL,
		`crateid` varchar(10) NOT NULL,
		`description` text NOT NULL,
		`length` int(10) NOT NULL,
		`width` int(10) NOT NULL,
		`height` int(10) NOT NULL,
		`pack` varchar(3) NOT NULL,
		`unpack` varchar(3) NOT NULL,
		`ot_pack` varchar(3) NOT NULL,
		`ot_unpack` varchar(3) NOT NULL,
		`discount` decimal(5,3) DEFAULT "0",
		`cube` int(10) NOT NULL,
		`line_item_id` int(10) NOT NULL AUTO_INCREMENT,
		PRIMARY KEY (`line_item_id`))', true);
    echo "<br> vtiger_crates table created <br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_crates_seq')) {
    Vtiger_Utils::CreateTable('vtiger_crates_seq',
        '(id int(19) NOT NULL)', true);
    echo "<br> vtiger_crates_seq table created <br>";
    
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_crates_seq` (id) VALUES (0)');
    echo "<br> vtiger_crates_seq id initialized to 0 <br>";
}
