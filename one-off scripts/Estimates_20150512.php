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
include_once('modules/ModTracker/ModTracker.php');

$isNew = false;  //flag for filters at the end

$module = Vtiger_Module::getInstance('Estimates');
if ($module) {
    echo "<h2>Updating Estimates Fields</h2><br>";
} else {
    $module = new Vtiger_Module();
    $module->name = 'Estimates';
    $module->save();
    echo "<h2>Creating Module Estimates and Updating Fields</h2><br>";
}

//start block1 : LBL_QUOTE_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
if ($block1) {
    echo "<h3>The LBL_QUOTE_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_QUOTE_INFORMATION';
    $module->addBlock($block1);
    $isNew = true;
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('subject', $module);
if ($field1) {
    echo "<li>The subject field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_QUOTES_SUBJECT';
    $field1->name = 'subject';
    $field1->tablename = 'vtiger_quotes';
    $field1->column = 'subject';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->defaultvalue = 'Estimate';
    $field1->summaryfield = 1;

    $block1->addField($field1);
    $module->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('potentialid', $module);
if ($field2) {
    echo "<li>The potentialid field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_POTENTIALNAME';
    $field2->name = 'potentialid';
    $field2->tablename = 'vtiger_quotes';
    $field2->column = 'potentialid';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;

    $field2->setRelatedModules(array('Opportunities'));

    $block1->addField($field2);
}

$field3 = Vtiger_Field::getInstance('quote_no', $module);
if ($field3) {
    echo "<li>The quote_no field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_QUOTENUMBER';
    $field3->name = 'quote_no';
    $field3->tablename = 'vtiger_quotes';
    $field3->column = 'quote_no';
    $field3->uitype = 4;
    $field3->typeofdata = 'V~M';
    $field3->displaytype = 1;

    $block1->addField($field3);
    
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $module->name, 'EST', 1);
}

$field4 = Vtiger_Field::getInstance('quotestage', $module);
if ($field4) {
    echo "<li>The quotestage field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_QUOTESTAGE';
    $field4->name = 'quotestage';
    $field4->tablename = 'vtiger_quotes';
    $field4->column = 'quotestage';
    $field4->uitype = 15;
    $field4->typeofdata = 'V~M';
    $field4->displaytype = 1;
    $field4->quickcreate = 0;
    $field4->defaultvalue = 'Created';
    $field4->summaryfield = 1;

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('validtill', $module);
if ($field5) {
    echo "<li>The validtill field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_VALIDUTILL';
    $field5->name = 'validtill';
    $field5->tablename = 'vtiger_quotes';
    $field5->column = 'validtill';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';
    $field5->displaytype = 1;

    $block1->addField($field5);
}

$field6 = Vtiger_Field::getInstance('contact_id', $module);
if ($field6) {
    echo "<li>The contact_id field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_CONTACTNAME';
    $field6->name = 'contact_id';
    $field6->tablename = 'vtiger_quotes';
    $field6->column = 'contactid';
    $field6->uitype = 57;
    $field6->typeofdata = 'V~O';
    $field6->displaytype = 1;

    $block1->addField($field6);
}

$field7 = Vtiger_Field::getInstance('shipping', $module);
if ($field7) {
    echo "<li>The shipping field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_QUOTES_SHIPPING';
    $field7->name = 'shipping';
    $field7->tablename = 'vtiger_quotes';
    $field7->column = 'shipping';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    $field7->displaytype = 1;

    $block1->addField($field7);
}

$field8 = Vtiger_Field::getInstance('account_id', $module);
if ($field8) {
    echo "<li>The account_id field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_QUOTES_ACCOUNTNAME';
    $field8->name = 'account_id';
    $field8->tablename = 'vtiger_quotes';
    $field8->column = 'accountid';
    $field8->uitype = 73;
    $field8->typeofdata = 'I~O';
    $field8->displaytype = 1;
    $field8->summaryfield = 1;

    $block1->addField($field8);
}

$field9 = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($field9) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_QUOTES_ASSIGNEDTO';
    $field9->name = 'assigned_user_id';
    $field9->tablename = 'vtiger_crmentity';
    $field9->column = 'smownerid';
    $field9->uitype = 53;
    $field9->typeofdata = 'V~M';
    $field9->displaytype = 1;
    $field9->quickcreate = 0;
    $field9->summaryfield = 1;

    $block1->addField($field9);
}

$field10 = Vtiger_Field::getInstance('createdtime', $module);
if ($field10) {
    echo "<li>The createdtime field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_QUOTES_CREATEDTIME';
    $field10->name = 'createdtime';
    $field10->tablename = 'vtiger_crmentity';
    $field10->column = 'createdtime';
    $field10->uitype = 70;
    $field10->typeofdata = 'DT~O';
    $field10->displaytype = 2;

    $block1->addField($field10);
}

$field11 = Vtiger_Field::getInstance('modifiedtime', $module);
if ($field11) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_QUOTES_MODIFIEDTIME';
    $field11->name = 'modifiedtime';
    $field11->tablename = 'vtiger_crmentity';
    $field11->column = 'modifiedtime';
    $field11->uitype = 70;
    $field11->typeofdata = 'DT~O';
    $field11->displaytype = 2;

    $block1->addField($field11);
}

$field12 = Vtiger_Field::getInstance('business_line', $module);
if ($field12) {
    echo "<li>The business_line field already exists</li><br> \n";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_QUOTES_BUSINESSLINE';
    $field12->name = 'business_line';
    $field12->tablename = 'vtiger_quotescf';
    $field12->column = 'business_line';
    $field12->uitype = 16;
    $field12->typeofdata = 'V~O';
    $field12->displaytype = 1;
    $field12->summaryfield = 1;

    $block1->addField($field12);
}

$field13 = Vtiger_Field::getInstance('project_id', $module);
if ($field13) {
    echo "<li>The project_id field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_QUOTES_PROJECTID';
    $field13->name = 'project_id';
    $field13->tablename = 'vtiger_quotes';
    $field13->column = 'project_id';
    $field13->uitype = 10;
    $field13->typeofdata = 'V~O';
    $field13->displaytype = 1;

    $block1->addField($field13);
}

$field14 = Vtiger_Field::getInstance('is_primary', $module);
if ($field14) {
    echo "<li>The is_primary field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_QUOTES_ISPRIMARY';
    $field14->name = 'is_primary';
    $field14->tablename = 'vtiger_quotes';
    $field14->column = 'is_primary';
    $field14->uitype = 56;
    $field14->typeofdata = 'C~O';
    $field14->displaytype = 1;

    $block1->addField($field14);
}

$field49 = Vtiger_Field::getInstance('pre_tax_total', $module);
if ($field49) {
    echo "<li>The pre_tax_total field already exists</li><br> \n";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_QUOTES_PRETAXTOTAL';
    $field49->name = 'pre_tax_total';
    $field49->tablename = 'vtiger_quotes';
    $field49->column = 'pre_tax_total';
    $field49->uitype = 72;
    $field49->typeofdata = 'N~O';
    $field49->displaytype = 3;

    $block1->addField($field49);
}

$field51 = Vtiger_Field::getInstance('modifiedby', $module);
if ($field51) {
    echo "<li>The modifiedby field already exists</li><br> \n";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_QUOTES_LASTMODIFIEDBY';
    $field51->name = 'modifiedby';
    $field51->tablename = 'vtiger_crmentity';
    $field51->column = 'modifiedby';
    $field51->uitype = 52;
    $field51->typeofdata = 'V~O';
    $field51->displaytype = 3;

    $block1->addField($field51);
}

$field52 = Vtiger_Field::getInstance('conversion_rate', $module);
if ($field52) {
    echo "<li>The conversion_rate field already exists</li><br> \n";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_QUOTES_CONVERSIONRATE';
    $field52->name = 'conversion_rate';
    $field52->tablename = 'vtiger_quotes';
    $field52->column = 'conversion_rate';
    $field52->uitype = 1;
    $field52->typeofdata = 'N~O';
    $field52->displaytype = 3;

    $block1->addField($field52);
}

$field56 = Vtiger_Field::getInstance('hdnS_H_Amount', $module);
if ($field56) {
    echo "<li>The hdnS_H_Amount field already exists</li><br> \n";
} else {
    $field56 = new Vtiger_Field();
    $field56->label = 'LBL_QUOTES_HDNSHAMOUNT';
    $field56->name = 'hdnS_H_Amount';
    $field56->tablename = 'vtiger_quotes';
    $field56->column = 's_h_amount';
    $field56->uitype = 72;
    $field56->typeofdata = 'N~O';
    $field56->displaytype = 3;

    $block1->addField($field56);
}

$field58 = Vtiger_Field::getInstance('hdnSubTotal', $module);
if ($field58) {
    echo "<li>The hdnSubTotal field already exists</li><br> \n";
} else {
    $field58 = new Vtiger_Field();
    $field58->label = 'LBL_QUOTES_HDNSUBTOTAL';
    $field58->name = 'hdnSubTotal';
    $field58->tablename = 'vtiger_quotes';
    $field58->column = 'subtotal';
    $field58->uitype = 72;
    $field58->typeofdata = 'N~O';
    $field58->displaytype = 3;

    $block1->addField($field58);
}
$field66 = Vtiger_Field::getInstance('hdnGrandTotal', $module);
if ($field66) {
    echo "<li>The hdnGrandTotal field already exists</li><br> \n";
} else {
    $field66 = new Vtiger_Field();
    $field66->label = 'LBL_QUOTES_HDNGRANDTOTAL';
    $field66->name = 'hdnGrandTotal';
    $field66->tablename = 'vtiger_quotes';
    $field66->column = 'total';
    $field66->uitype = 72;
    $field66->typeofdata = 'N~O';
    $field66->displaytype = 3;

    $block1->addField($field66);
}

$field137 = Vtiger_Field::getInstance('hdnTaxType', $module);
if ($field137) {
    echo "<li>The hdnTaxType field already exists</li><br> \n";
} else {
    $field137 = new Vtiger_Field();
    $field137->label = 'LBL_QUOTES_HDNTAXTYPE';
    $field137->name = 'hdnTaxType';
    $field137->tablename = 'vtiger_quotes';
    $field137->column = 'taxtype';
    $field137->uitype = 16;
    $field137->typeofdata = 'V~O';
    $field137->displaytype = 3;

    $block1->addField($field137);
}

$field139 = Vtiger_Field::getInstance('hdnDiscountPercent', $module);
if ($field139) {
    echo "<li>The hdnDiscountPercent field already exists</li><br> \n";
} else {
    $field139 = new Vtiger_Field();
    $field139->label = 'LBL_QUOTES_HDNDISCOUNTPERCENT';
    $field139->name = 'hdnDiscountPercent';
    $field139->tablename = 'vtiger_quotes';
    $field139->column = 'discount_percent';
    $field139->uitype = 1;
    $field139->typeofdata = 'N~O';
    $field139->displaytype = 3;

    $block1->addField($field139);
}

$field140 = Vtiger_Field::getInstance('currency_id', $module);
if ($field140) {
    echo "<li>The currency_id field already exists</li><br> \n";
} else {
    $field140 = new Vtiger_Field();
    $field140->label = 'LBL_QUOTES_CURRENCY';
    $field140->name = 'currency_id';
    $field140->tablename = 'vtiger_quotes';
    $field140->column = 'currency_id';
    $field140->uitype = 117;
    $field140->typeofdata = 'I~O';
    $field140->displaytype = 3;

    $block1->addField($field140);
}
echo "</ul>";
$block1->save($module);
//end block1 : LBL_QUOTE_INFORMATION

//start block2 : LBL_QUOTES_CONTACTDETAILS
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_CONTACTDETAILS', $module);
if ($block2) {
    echo "<h3>The LBL_QUOTES_CONTACTDETAILS block already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_QUOTES_CONTACTDETAILS';
    $module->addBlock($block2);
}

echo "<ul>";
//start block2 fields
$field15 = Vtiger_Field::getInstance('bill_street', $module);
if ($field15) {
    echo "<li>The bill_street field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_QUOTES_BILLINGADDRESS';
    $field15->name = 'bill_street';
    $field15->tablename = 'vtiger_quotesbillads';
    $field15->column = 'bill_street';
    $field15->uitype = 24;
    $field15->typeofdata = 'V~O';
    $field15->displaytype = 1;

    $block2->addField($field15);
}

$field16 = Vtiger_Field::getInstance('bill_city', $module);
if ($field16) {
    echo "<li>The bill_city field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_QUOTES_BILLINGCITY';
    $field16->name = 'bill_city';
    $field16->tablename = 'vtiger_quotesbillads';
    $field16->column = 'bill_city';
    $field16->uitype = 1;
    $field16->typeofdata = 'V~O';
    $field16->displaytype = 1;

    $block2->addField($field16);
}

$field17 = Vtiger_Field::getInstance('bill_state', $module);
if ($field17) {
    echo "<li>The bill_state field already exists</li><br> \n";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_QUOTES_BILLINGSTATE';
    $field17->name = 'bill_state';
    $field17->tablename = 'vtiger_quotesbillads';
    $field17->column = 'bill_state';
    $field17->uitype = 1;
    $field17->typeofdata = 'V~O';
    $field17->displaytype = 1;

    $block2->addField($field17);
}

$field18 = Vtiger_Field::getInstance('bill_code', $module);
if ($field18) {
    echo "<li>The bill_code field already exists</li><br> \n";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_QUOTES_BILLINGZIPCODE';
    $field18->name = 'bill_code';
    $field18->tablename = 'vtiger_quotesbillads';
    $field18->column = 'bill_code';
    $field18->uitype = 1;
    $field18->typeofdata = 'V~O';
    $field18->displaytype = 1;

    $block2->addField($field18);
}

$field19 = Vtiger_Field::getInstance('bill_pobox', $module);
if ($field19) {
    echo "<li>The bill_pobox field already exists</li><br> \n";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_QUOTES_BILLINGPOBOX';
    $field19->name = 'bill_pobox';
    $field19->tablename = 'vtiger_quotesbillads';
    $field19->column = 'bill_pobox';
    $field19->uitype = 1;
    $field19->typeofdata = 'V~O';
    $field19->displaytype = 1;

    $block2->addField($field19);
}

$field20 = Vtiger_Field::getInstance('bill_country', $module);
if ($field20) {
    echo "<li>The bill_country field already exists</li><br> \n";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_QUOTES_BILLINGCOUNTRY';
    $field20->name = 'bill_country';
    $field20->tablename = 'vtiger_quotesbillads';
    $field20->column = 'bill_country';
    $field20->uitype = 1;
    $field20->typeofdata = 'V~O';
    $field20->displaytype = 1;

    $block2->addField($field20);
}
echo "</ul>";
$block2->save($module);
//end block2 : LBL_QUOTES_CONTACTDETAILS

//start block3 : LBL_ADDRESS_INFORMATION
$block3 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $module);
if ($block3) {
    echo "<h3>The LBL_ADDRESS_INFORMATION block already exists</h3><br> \n";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_ADDRESS_INFORMATION';
    $module->addBlock($block3);
}
echo "<ul>";
//start block2 fields

$field21 = Vtiger_Field::getInstance('origin_address1', $module);
if ($field21) {
    echo "<li>The origin_address1 field already exists</li><br> \n";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_QUOTES_ORIGINADDRESS1';
    $field21->name = 'origin_address1';
    $field21->tablename = 'vtiger_quotescf';
    $field21->column = 'origin_address1';
    $field21->uitype = 1;
    $field21->typeofdata = 'V~O~LE~50';
    $field21->displaytype = 1;

    $block3->addField($field21);
}

$field22 = Vtiger_Field::getInstance('destination_address1', $module);
if ($field22) {
    echo "<li>The destination_address1 field already exists</li><br> \n";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_QUOTES_DESTINATIONADDRESS1';
    $field22->name = 'destination_address1';
    $field22->tablename = 'vtiger_quotescf';
    $field22->column = 'destination_address1';
    $field22->uitype = 1;
    $field22->typeofdata = 'V~O~LE~50';
    $field22->displaytype = 1;

    $block3->addField($field22);
}

$field23 = Vtiger_Field::getInstance('origin_address2', $module);
if ($field23) {
    echo "<li>The origin_address2 field already exists</li><br> \n";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_QUOTES_ORIGINADDRESS2';
    $field23->name = 'origin_address2';
    $field23->tablename = 'vtiger_quotescf';
    $field23->column = 'origin_address2';
    $field23->uitype = 1;
    $field23->typeofdata = 'V~O~LE~50';
    $field23->displaytype = 1;

    $block3->addField($field23);
}

$field24 = Vtiger_Field::getInstance('destination_address2', $module);
if ($field24) {
    echo "<li>The destination_address2 field already exists</li><br> \n";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_QUOTES_DESTINATIONADDRESS2';
    $field24->name = 'destination_address2';
    $field24->tablename = 'vtiger_quotescf';
    $field24->column = 'destination_address2';
    $field24->uitype = 1;
    $field24->typeofdata = 'V~O~LE~50';
    $field24->displaytype = 1;

    $block3->addField($field24);
}

$field25 = Vtiger_Field::getInstance('origin_city', $module);
if ($field25) {
    echo "<li>The origin_city field already exists</li><br> \n";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_QUOTES_ORIGINCITY';
    $field25->name = 'origin_city';
    $field25->tablename = 'vtiger_quotescf';
    $field25->column = 'origin_city';
    $field25->uitype = 1;
    $field25->typeofdata = 'V~O~LE~50';
    $field25->displaytype = 1;

    $block3->addField($field25);
}

$field26 = Vtiger_Field::getInstance('destination_city', $module);
if ($field26) {
    echo "<li>The destination_city field already exists</li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_QUOTES_DESTINATIONCITY';
    $field26->name = 'destination_city';
    $field26->tablename = 'vtiger_quotescf';
    $field26->column = 'destination_city';
    $field26->uitype = 1;
    $field26->typeofdata = 'V~O~LE~50';
    $field26->displaytype = 1;

    $block3->addField($field26);
}

$field27 = Vtiger_Field::getInstance('origin_state', $module);
if ($field27) {
    echo "<li>The origin_state field already exists</li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_QUOTES_ORIGINSTATE';
    $field27->name = 'origin_state';
    $field27->tablename = 'vtiger_quotescf';
    $field27->column = 'origin_state';
    $field27->uitype = 1;
    $field27->typeofdata = 'V~O';
    $field27->displaytype = 1;

    $block3->addField($field27);
}

$field28 = Vtiger_Field::getInstance('destination_state', $module);
if ($field28) {
    echo "<li>The destination_state field already exists</li><br> \n";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_QUOTES_DESTINATIONSTATE';
    $field28->name = 'destination_state';
    $field28->tablename = 'vtiger_quotescf';
    $field28->column = 'destination_state';
    $field28->uitype = 1;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 1;

    $block3->addField($field28);
}

$field29 = Vtiger_Field::getInstance('origin_zip', $module);
if ($field29) {
    echo "<li>The origin_zip field already exists</li><br> \n";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_QUOTES_ORIGINZIP';
    $field29->name = 'origin_zip';
    $field29->tablename = 'vtiger_quotescf';
    $field29->column = 'origin_zip';
    $field29->uitype = 7;
    $field29->typeofdata = 'I~O';
    $field29->displaytype = 1;
    $field29->quickcreate = 0;

    $block3->addField($field29);
}

$field30 = Vtiger_Field::getInstance('destination_zip', $module);
if ($field30) {
    echo "<li>The destination_zip field already exists</li><br> \n";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_QUOTES_DESTINATIONZIP';
    $field30->name = 'destination_zip';
    $field30->tablename = 'vtiger_quotescf';
    $field30->column = 'destination_zip';
    $field30->uitype = 7;
    $field30->typeofdata = 'I~O';
    $field30->displaytype = 1;
    $field30->quickcreate = 0;

    $block3->addField($field30);
}

$field31 = Vtiger_Field::getInstance('origin_phone1', $module);
if ($field31) {
    echo "<li>The origin_phone1 field already exists</li><br> \n";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_QUOTES_ORIGINPHONE1';
    $field31->name = 'origin_phone1';
    $field31->tablename = 'vtiger_quotescf';
    $field31->column = 'origin_phone1';
    $field31->uitype = 11;
    $field31->typeofdata = 'V~O';
    $field31->displaytype = 1;

    $block3->addField($field31);
}

$field32 = Vtiger_Field::getInstance('destination_phone1', $module);
if ($field32) {
    echo "<li>The destination_phone1 field already exists</li><br> \n";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_QUOTES_DESTINATIONPHONE1';
    $field32->name = 'destination_phone1';
    $field32->tablename = 'vtiger_quotescf';
    $field32->column = 'destination_phone1';
    $field32->uitype = 11;
    $field32->typeofdata = 'V~O';
    $field32->displaytype = 1;

    $block3->addField($field32);
}

$field33 = Vtiger_Field::getInstance('origin_phone2', $module);
if ($field33) {
    echo "<li>The origin_phone2 field already exists</li><br> \n";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_QUOTES_ORIGINPHONE2';
    $field33->name = 'origin_phone2';
    $field33->tablename = 'vtiger_quotescf';
    $field33->column = 'origin_phone2';
    $field33->uitype = 11;
    $field33->typeofdata = 'V~O';
    $field33->displaytype = 1;

    $block3->addField($field33);
}

$field34 = Vtiger_Field::getInstance('destination_phone2', $module);
if ($field34) {
    echo "<li>The destination_phone2 field already exists</li><br> \n";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_QUOTES_DESTINATIONPHONE2';
    $field34->name = 'destination_phone2';
    $field34->tablename = 'vtiger_quotescf';
    $field34->column = 'destination_phone2';
    $field34->uitype = 11;
    $field34->typeofdata = 'V~O';
    $field34->displaytype = 1;

    $block3->addField($field34);
}
echo "</ul>";
$block3->save($module);
//end block3 : LBL_ADDRESS_INFORMATION

//start block4 : LBL_QUOTES_LOCALMOVEDETAILS
$block4 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module);
if ($block4) {
    echo "<h3>The LBL_QUOTES_LOCALMOVEDETAILS block already exists</h3><br> \n";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_QUOTES_LOCALMOVEDETAILS';
    $module->addBlock($block4);
}
echo "<ul>";
//start block4 fields
$field35 = Vtiger_Field::getInstance('cf_1003', $module);
if ($field35) {
    echo "<li>The cf_1003 field already exists</li><br> \n";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_QUOTES_HOLDERFIELD1';
    $field35->name = 'cf_1003';
    $field35->tablename = 'vtiger_quotescf';
    $field35->column = 'cf_1003';
    $field35->uitype = 1;
    $field35->typeofdata = 'V~O~LE~15';
    $field35->displaytype = 1;

    $block4->addField($field35);
}

echo "</ul>";
$block4->save($module);
//end block4 : LBL_QUOTES_LOCALMOVEDETAILS

//start block5 : LBL_QUOTES_INTERSTATEMOVEDETAILS
$block5 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
if ($block5) {
    echo "<h3>The LBL_QUOTES_INTERSTATEMOVEDETAILS block already exists</h3><br> \n";
} else {
    $block5 = new Vtiger_Block();
    $block5->label = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
    $module->addBlock($block5);
}
echo "<ul>";
//start block5 fields
$field36 = Vtiger_Field::getInstance('weight', $module);
if ($field36) {
    echo "<li>The weight field already exists</li><br> \n";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_QUOTES_WEIGHT';
    $field36->name = 'weight';
    $field36->tablename = 'vtiger_quotes';
    $field36->column = 'weight';
    $field36->uitype = 7;
    $field36->typeofdata = 'I~O';
    $field36->displaytype = 1;
    $field36->quickcreate = 0;

    $block5->addField($field36);
}
$field37 = Vtiger_Field::getInstance('pickup_date', $module);
if ($field37) {
    echo "<li>The pickup_date field already exists</li><br> \n";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_QUOTES_PICKUPDATE';
    $field37->name = 'pickup_date';
    $field37->tablename = 'vtiger_quotes';
    $field37->column = 'pickup_date';
    $field37->uitype = 5;
    $field37->typeofdata = 'D~O';
    $field37->displaytype = 1;

    $block5->addField($field37);
}
$field38 = Vtiger_Field::getInstance('full_pack', $module);
if ($field38) {
    echo "<li>The full_pack field already exists</li><br> \n";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_QUOTES_FULLPACKAPPLIED';
    $field38->name = 'full_pack';
    $field38->tablename = 'vtiger_quotes';
    $field38->column = 'full_pack';
    $field38->uitype = 56;
    $field38->typeofdata = 'C~O';
    $field38->displaytype = 1;
    $field38->quickcreate = 0;

    $block5->addField($field38);
}
$field39 = Vtiger_Field::getInstance('valuation_deductible', $module);
if ($field39) {
    echo "<li>The valuation_deductible field already exists</li><br> \n";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_QUOTES_VALUATIONDEDUCTIBLE';
    $field39->name = 'valuation_deductible';
    $field39->tablename = 'vtiger_quotes';
    $field39->column = 'valuation_deductible';
    $field39->uitype = 16;
    $field39->typeofdata = 'V~O';
    $field39->displaytype = 1;

    $block5->addField($field39);
}
$field40 = Vtiger_Field::getInstance('full_unpack', $module);
if ($field40) {
    echo "<li>The full_unpack field already exists</li><br> \n";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_QUOTES_FULLUNPACKAPPLIED';
    $field40->name = 'full_unpack';
    $field40->tablename = 'vtiger_quotes';
    $field40->column = 'full_unpack';
    $field40->uitype = 56;
    $field40->typeofdata = 'C~O';
    $field40->displaytype = 1;
    $field40->quickcreate = 0;

    $block5->addField($field40);
}
$field41 = Vtiger_Field::getInstance('valuation_amount', $module);
if ($field41) {
    echo "<li>The valuation_amount field already exists</li><br> \n";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_QUOTES_VALUATIONAMOUNT';
    $field41->name = 'valuation_amount';
    $field41->tablename = 'vtiger_quotes';
    $field41->column = 'valuation_amount';
    $field41->uitype = 71;
    $field41->typeofdata = 'N~O';
    $field41->displaytype = 1;

    $block5->addField($field41);
}
$field42 = Vtiger_Field::getInstance('bottom_line_discount', $module);
if ($field42) {
    echo "<li>The bottom_line_discount field already exists</li><br> \n";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_QUOTES_BOTTOMLINEDISCOUNT';
    $field42->name = 'bottom_line_discount';
    $field42->tablename = 'vtiger_quotes';
    $field42->column = 'bottom_line_discount';
    $field42->uitype = 7;
    $field42->typeofdata = 'NN~O';
    $field42->displaytype = 1;
    $field42->quickcreate = 0;

    $block5->addField($field42);
}
$field43 = Vtiger_Field::getInstance('interstate_mileage', $module);
if ($field43) {
    echo "<li>The interstate_mileage field already exists</li><br> \n";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_QUOTES_MILEAGE';
    $field43->name = 'interstate_mileage';
    $field43->tablename = 'vtiger_quotes';
    $field43->column = 'interstate_mileage';
    $field43->uitype = 7;
    $field43->typeofdata = 'I~O';
    $field43->displaytype = 1;

    $block5->addField($field43);
}
echo "</ul>";
$block5->save($module);
//end block5 : LBL_QUOTES_INTERSTATEMOVEDETAILS

//start block6 : LBL_QUOTES_COMMERCIALMOVEDETAILS
$block6 = Vtiger_Block::getInstance('LBL_QUOTES_COMMERCIALMOVEDETAILS', $module);
if ($block6) {
    echo "<h3>The LBL_QUOTES_COMMERCIALMOVEDETAILS block already exists</h3><br> \n";
} else {
    $block6 = new Vtiger_Block();
    $block6->label = 'LBL_QUOTES_COMMERCIALMOVEDETAILS';
    $module->addBlock($block6);
}
echo "<ul>";
//start block6 fields
$field65 = Vtiger_Field::getInstance('cf_1007', $module);
if ($field65) {
    echo "<li>The cf_1007 field already exists</li><br> \n";
} else {
    $field65 = new Vtiger_Field();
    $field65->label = 'LBL_QUOTES_HOLDERFIELD3';
    $field65->name = 'cf_1007';
    $field65->tablename = 'vtiger_quotescf';
    $field65->column = 'cf_1007';
    $field65->uitype = 1;
    $field65->typeofdata = 'V~O~LE~15';
    $field65->displaytype = 1;

    $block6->addField($field65);
}

echo "</ul>";
$block6->save($module);
//end block6 : LBL_QUOTES_COMMERCIALMOVEDETAILS

//start block7 : LBL_TERMS_INFORMATION
$block7 = Vtiger_Block::getInstance('LBL_TERMS_INFORMATION', $module);
if ($block7) {
    echo "<h3>The LBL_TERMS_INFORMATION block already exists</h3><br> \n";
} else {
    $block7 = new Vtiger_Block();
    $block7->label = 'LBL_TERMS_INFORMATION';
    $module->addBlock($block7);
}
echo "<ul>";
//start block7 fields
$field45 = Vtiger_Field::getInstance('terms_conditions', $module);
if ($field45) {
    echo "<li>The terms_conditions field already exists</li><br> \n";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_QUOTES_TERMSANDCONDITIONS';
    $field45->name = 'terms_conditions';
    $field45->tablename = 'vtiger_quotes';
    $field45->column = 'terms_conditions';
    $field45->uitype = 19;
    $field45->typeofdata = 'V~O';
    $field45->displaytype = 1;

    $block7->addField($field45);
}

echo "</ul>";
$block7->save($module);
//end block7 : LBL_TERMS_INFORMATION

//start block8 : LBL_DESCRIPTION_INFORMATION
$block8 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $module);
if ($block8) {
    echo "<h3>The LBL_DESCRIPTION_INFORMATION block already exists</h3><br> \n";
} else {
    $block8 = new Vtiger_Block();
    $block8->label = 'LBL_DESCRIPTION_INFORMATION';
    $module->addBlock($block8);
}
echo "<ul>";
//start block8 fields
$field46 = Vtiger_Field::getInstance('description', $module);
if ($field46) {
    echo "<li>The description field already exists</li><br> \n";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_QUOTES_DESCRIPTION';
    $field46->name = 'description';
    $field46->tablename = 'vtiger_crmentity';
    $field46->column = 'description';
    $field46->uitype = 19;
    $field46->typeofdata = 'V~O';
    $field46->displaytype = 1;

    $block8->addField($field46);
}

echo "</ul>";
$block8->save($module);
//end block8 : LBL_DESCRIPTION_INFORMATION

//start block9 : LBL_ITEM_DETAILS
$block9 = Vtiger_Block::getInstance('LBL_ITEM_DETAILS', $module);
if ($block9) {
    echo "<h3>The LBL_ITEM_DETAILS block already exists</h3><br> \n";
} else {
    $block9 = new Vtiger_Block();
    $block9->label = 'LBL_ITEM_DETAILS';
    $module->addBlock($block9);
}
echo "<ul>";
//start block9 fields
$field47 = Vtiger_Field::getInstance('tax2', $module);
if ($field47) {
    echo "<li>The tax2 field already exists</li><br> \n";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_QUOTES_TAX2';
    $field47->name = 'tax2';
    $field47->tablename = 'vtiger_inventoryproductrel';
    $field47->column = 'tax2';
    $field47->uitype = 83;
    $field47->typeofdata = 'V~O';
    $field47->displaytype = 5;

    $block9->addField($field47);
}
$field48 = Vtiger_Field::getInstance('tax3', $module);
if ($field48) {
    echo "<li>The tax3 field already exists</li><br> \n";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_QUOTES_TAX3';
    $field48->name = 'tax3';
    $field48->tablename = 'vtiger_inventoryproductrel';
    $field48->column = 'tax3';
    $field48->uitype = 83;
    $field48->typeofdata = 'V~O';
    $field48->displaytype = 5;

    $block9->addField($field48);
}
$field68 = Vtiger_Field::getInstance('tax1', $module);
if ($field68) {
    echo "<li>The tax1 field already exists</li><br> \n";
} else {
    $field68 = new Vtiger_Field();
    $field68->label = 'LBL_QUOTES_TAX1';
    $field68->name = 'tax1';
    $field68->tablename = 'vtiger_inventoryproductrel';
    $field68->column = 'tax1';
    $field68->uitype = 83;
    $field68->typeofdata = 'V~O';
    $field68->displaytype = 5;

    $block9->addField($field68);
}
$field71 = Vtiger_Field::getInstance('comment', $module);
if ($field71) {
    echo "<li>The comment field already exists</li><br> \n";
} else {
    $field71 = new Vtiger_Field();
    $field71->label = 'LBL_QUOTES_ITEMCOMMENT';
    $field71->name = 'comment';
    $field71->tablename = 'vtiger_inventoryproductrel';
    $field71->column = 'comment';
    $field71->uitype = 19;
    $field71->typeofdata = 'V~O';
    $field71->displaytype = 5;

    $block9->addField($field71);
}
$field72 = Vtiger_Field::getInstance('productid', $module);
if ($field72) {
    echo "<li>The productid field already exists</li><br> \n";
} else {
    $field72 = new Vtiger_Field();
    $field72->label = 'LBL_QUOTES_PRODUCTID';
    $field72->name = 'productid';
    $field72->tablename = 'vtiger_inventoryproductrel';
    $field72->column = 'productid';
    $field72->uitype = 10;
    $field72->typeofdata = 'V~M';
    $field72->displaytype = 5;

    $block9->addField($field72);
}
$field73 = Vtiger_Field::getInstance('listprice', $module);
if ($field73) {
    echo "<li>The listprice field already exists</li><br> \n";
} else {
    $field73 = new Vtiger_Field();
    $field73->label = 'LBL_QUOTES_LISTPRICE';
    $field73->name = 'listprice';
    $field73->tablename = 'vtiger_inventoryproductrel';
    $field73->column = 'listprice';
    $field73->uitype = 71;
    $field73->typeofdata = 'N~O';
    $field73->displaytype = 5;

    $block9->addField($field73);
}
$field74 = Vtiger_Field::getInstance('quantity', $module);
if ($field74) {
    echo "<li>The quantity field already exists</li><br> \n";
} else {
    $field74 = new Vtiger_Field();
    $field74->label = 'LBL_QUOTES_QUANTITY';
    $field74->name = 'quantity';
    $field74->tablename = 'vtiger_inventoryproductrel';
    $field74->column = 'quantity';
    $field74->uitype = 7;
    $field74->typeofdata = 'N~O';
    $field74->displaytype = 5;

    $block9->addField($field74);
}
$field75 = Vtiger_Field::getInstance('discount_percent', $module);
if ($field75) {
    echo "<li>The discount_percent field already exists</li><br> \n";
} else {
    $field75 = new Vtiger_Field();
    $field75->label = 'LBL_QUOTES_ITEMDISCOUNTPERCENT';
    $field75->name = 'discount_percent';
    $field75->tablename = 'vtiger_inventoryproductrel';
    $field75->column = 'discount_percent';
    $field75->uitype = 7;
    $field75->typeofdata = 'V~O';
    $field75->displaytype = 3;

    $block9->addField($field75);
}

echo "</ul>";
$block9->save($module);
//end block9 : LBL_ITEM_DETAILS

//start block10 : LBL_QUOTES_SITDETAILS
$block10 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $module);
if ($block10) {
    echo "<h3>The LBL_QUOTES_SITDETAILS block already exists</h3><br> \n";
} else {
    $block10 = new Vtiger_Block();
    $block10->label = 'LBL_QUOTES_SITDETAILS';
    $module->addBlock($block10);
}
echo "<ul>";
//start block10 fields
$field77 = Vtiger_Field::getInstance('sit_origin_date_in', $module);
if ($field77) {
    echo "<li>The sit_origin_date_in field already exists</li><br> \n";
} else {
    $field77 = new Vtiger_Field();
    $field77->label = 'LBL_QUOTES_SITORIGINDATEIN';
    $field77->name = 'sit_origin_date_in';
    $field77->tablename = 'vtiger_quotes';
    $field77->column = 'sit_origin_date_in';
    $field77->uitype = 5;
    $field77->typeofdata = 'D~O';
    $field77->displaytype = 1;

    $block10->addField($field77);
}
$field78 = Vtiger_Field::getInstance('sit_dest_date_in', $module);
if ($field78) {
    echo "<li>The sit_dest_date_in field already exists</li><br> \n";
} else {
    $field78 = new Vtiger_Field();
    $field78->label = 'LBL_QUOTES_SITDESTINATIONDATEIN';
    $field78->name = 'sit_dest_date_in';
    $field78->tablename = 'vtiger_quotes';
    $field78->column = 'sit_dest_date_in';
    $field78->uitype = 5;
    $field78->typeofdata = 'D~O';
    $field78->displaytype = 1;

    $block10->addField($field78);
}
$field79 = Vtiger_Field::getInstance('sit_origin_pickup_date', $module);
if ($field79) {
    echo "<li>The sit_origin_pickup_date field already exists</li><br> \n";
} else {
    $field79 = new Vtiger_Field();
    $field79->label = 'LBL_QUOTES_SITORIGINPICKUPDATE';
    $field79->name = 'sit_origin_pickup_date';
    $field79->tablename = 'vtiger_quotes';
    $field79->column = 'sit_origin_pickup_date';
    $field79->uitype = 5;
    $field79->typeofdata = 'D~O';
    $field79->displaytype = 1;

    $block10->addField($field79);
}
$field80 = Vtiger_Field::getInstance('sit_dest_delivery_date', $module);
if ($field80) {
    echo "<li>The sit_dest_delivery_date field already exists</li><br> \n";
} else {
    $field80 = new Vtiger_Field();
    $field80->label = 'LBL_QUOTES_SITDELIVERYDATE';
    $field80->name = 'sit_dest_delivery_date';
    $field80->tablename = 'vtiger_quotes';
    $field80->column = 'sit_dest_delivery_date';
    $field80->uitype = 5;
    $field80->typeofdata = 'D~O';
    $field80->displaytype = 1;

    $block10->addField($field80);
}
$field81 = Vtiger_Field::getInstance('sit_origin_weight', $module);
if ($field81) {
    echo "<li>The sit_origin_weight field already exists</li><br> \n";
} else {
    $field81 = new Vtiger_Field();
    $field81->label = 'LBL_QUOTES_SITORIGINWEIGHT';
    $field81->name = 'sit_origin_weight';
    $field81->tablename = 'vtiger_quotes';
    $field81->column = 'sit_origin_weight';
    $field81->uitype = 7;
    $field81->typeofdata = 'I~O';
    $field81->displaytype = 1;

    $block10->addField($field81);
}
$field82 = Vtiger_Field::getInstance('sit_dest_weight', $module);
if ($field82) {
    echo "<li>The sit_dest_weight field already exists</li><br> \n";
} else {
    $field82 = new Vtiger_Field();
    $field82->label = 'LBL_QUOTES_SITDESTINATIONWEIGHT';
    $field82->name = 'sit_dest_weight';
    $field82->tablename = 'vtiger_quotes';
    $field82->column = 'sit_dest_weight';
    $field82->uitype = 7;
    $field82->typeofdata = 'I~O';
    $field82->displaytype = 1;

    $block10->addField($field82);
}
$field83 = Vtiger_Field::getInstance('sit_origin_zip', $module);
if ($field83) {
    echo "<li>The sit_origin_zip field already exists</li><br> \n";
} else {
    $field83 = new Vtiger_Field();
    $field83->label = 'LBL_QUOTES_SITORIGINZIP';
    $field83->name = 'sit_origin_zip';
    $field83->tablename = 'vtiger_quotes';
    $field83->column = 'sit_origin_zip';
    $field83->uitype = 7;
    $field83->typeofdata = 'I~O';
    $field83->displaytype = 1;

    $block10->addField($field83);
}
$field84 = Vtiger_Field::getInstance('sit_dest_zip', $module);
if ($field84) {
    echo "<li>The sit_dest_zip field already exists</li><br> \n";
} else {
    $field84 = new Vtiger_Field();
    $field84->label = 'LBL_QUOTES_SITDESTINATIONZIP';
    $field84->name = 'sit_dest_zip';
    $field84->tablename = 'vtiger_quotes';
    $field84->column = 'sit_dest_zip';
    $field84->uitype = 7;
    $field84->typeofdata = 'I~O';
    $field84->displaytype = 1;

    $block10->addField($field84);
}
$field85 = Vtiger_Field::getInstance('sit_origin_miles', $module);
if ($field85) {
    echo "<li>The sit_origin_miles field already exists</li><br> \n";
} else {
    $field85 = new Vtiger_Field();
    $field85->label = 'LBL_QUOTES_SITORIGINMILES';
    $field85->name = 'sit_origin_miles';
    $field85->tablename = 'vtiger_quotes';
    $field85->column = 'sit_origin_miles';
    $field85->uitype = 7;
    $field85->typeofdata = 'I~O';
    $field85->displaytype = 1;

    $block10->addField($field85);
}
$field86 = Vtiger_Field::getInstance('sit_dest_miles', $module);
if ($field86) {
    echo "<li>The sit_dest_miles field already exists</li><br> \n";
} else {
    $field86 = new Vtiger_Field();
    $field86->label = 'LBL_QUOTES_SITDESTINATIONMILES';
    $field86->name = 'sit_dest_miles';
    $field86->tablename = 'vtiger_quotes';
    $field86->column = 'sit_dest_miles';
    $field86->uitype = 7;
    $field86->typeofdata = 'I~O';
    $field86->displaytype = 1;

    $block10->addField($field86);
}
$field87 = Vtiger_Field::getInstance('sit_origin_number_days', $module);
if ($field87) {
    echo "<li>The sit_origin_number_days field already exists</li><br> \n";
} else {
    $field87 = new Vtiger_Field();
    $field87->label = 'LBL_QUOTES_SITORIGINNUMBERDAYS';
    $field87->name = 'sit_origin_number_days';
    $field87->tablename = 'vtiger_quotes';
    $field87->column = 'sit_origin_number_days';
    $field87->uitype = 7;
    $field87->typeofdata = 'I~O';
    $field87->displaytype = 1;

    $block10->addField($field87);
}
$field88 = Vtiger_Field::getInstance('sit_dest_number_days', $module);
if ($field88) {
    echo "<li>The sit_dest_number_days field already exists</li><br> \n";
} else {
    $field88 = new Vtiger_Field();
    $field88->label = 'LBL_QUOTES_SITDESTINATIONNUMBERDAYS';
    $field88->name = 'sit_dest_number_days';
    $field88->tablename = 'vtiger_quotes';
    $field88->column = 'sit_dest_number_days';
    $field88->uitype = 7;
    $field88->typeofdata = 'I~O';
    $field88->displaytype = 1;

    $block10->addField($field88);
}
$field99 = Vtiger_Field::getInstance('sit_origin_fuel_percent', $module);
if ($field99) {
    echo "<li>The sit_origin_fuel_percent field already exists</li><br> \n";
} else {
    $field99 = new Vtiger_Field();
    $field99->label = 'LBL_QUOTES_SITORIGINFUELPERCENT';
    $field99->name = 'sit_origin_fuel_percent';
    $field99->tablename = 'vtiger_quotes';
    $field99->column = 'sit_origin_fuel_percent';
    $field99->uitype = 7;
    $field99->typeofdata = 'N~O';
    $field99->displaytype = 1;

    $block10->addField($field99);
}
$field100 = Vtiger_Field::getInstance('sit_dest_fuel_percent', $module);
if ($field100) {
    echo "<li>The sit_dest_fuel_percent field already exists</li><br> \n";
} else {
    $field100 = new Vtiger_Field();
    $field100->label = 'LBL_QUOTES_SITDESTINATIONFUELPERCENT';
    $field100->name = 'sit_dest_fuel_percent';
    $field100->tablename = 'vtiger_quotes';
    $field100->column = 'sit_dest_fuel_percent';
    $field100->uitype = 7;
    $field100->typeofdata = 'N~O';
    $field100->displaytype = 1;

    $block10->addField($field100);
}
$field107 = Vtiger_Field::getInstance('sit_origin_overtime', $module);
if ($field107) {
    echo "<li>The sit_origin_overtime field already exists</li><br> \n";
} else {
    $field107 = new Vtiger_Field();
    $field107->label = 'LBL_QUOTES_SITORIGINOVERTIME';
    $field107->name = 'sit_origin_overtime';
    $field107->tablename = 'vtiger_quotes';
    $field107->column = 'sit_origin_overtime';
    $field107->uitype = 56;
    $field107->typeofdata = 'C~O';
    $field107->displaytype = 1;

    $block10->addField($field107);
}
$field108 = Vtiger_Field::getInstance('sit_dest_overtime', $module);
if ($field108) {
    echo "<li>The sit_dest_overtime field already exists</li><br> \n";
} else {
    $field108 = new Vtiger_Field();
    $field108->label = 'LBL_QUOTES_SITDESTINATIONOVERTIME';
    $field108->name = 'sit_dest_overtime';
    $field108->tablename = 'vtiger_quotes';
    $field108->column = 'sit_dest_overtime';
    $field108->uitype = 56;
    $field108->typeofdata = 'C~O';
    $field108->displaytype = 1;

    $block10->addField($field108);
}
$field138 = Vtiger_Field::getInstance('discount_amount', $module);
if ($field138) {
    echo "<li>The discount_amount field already exists</li><br> \n";
} else {
    $field138 = new Vtiger_Field();
    $field138->label = 'LBL_QUOTES_DISCOUNT';
    $field138->name = 'discount_amount';
    $field138->tablename = 'vtiger_inventoryproductrel';
    $field138->column = 'discount_amount';
    $field138->uitype = 71;
    $field138->typeofdata = 'N~O';
    $field138->displaytype = 5;

    $block9->addField($field138);
}
    
echo "</ul>";
$block10->save($module);
//end block10 : LBL_QUOTES_SITDETAILS

//start block11 : LBL_QUOTES_ACCESSORIALDETAILS
$block11 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $module);
if ($block11) {
    echo "<h3>The LBL_QUOTES_ACCESSORIALDETAILS block already exists</h3><br> \n";
} else {
    $block11 = new Vtiger_Block();
    $block11->label = 'LBL_QUOTES_ACCESSORIALDETAILS';
    $module->addBlock($block11);
}
echo "<ul>";
//start block11 fields
$field109 = Vtiger_Field::getInstance('acc_shuttle_origin_weight', $module);
if ($field109) {
    echo "<li>The acc_shuttle_origin_weight field already exists</li><br> \n";
} else {
    $field109 = new Vtiger_Field();
    $field109->label = 'LBL_QUOTES_ACCSHUTTLEORIGINWEIGHT';
    $field109->name = 'acc_shuttle_origin_weight';
    $field109->tablename = 'vtiger_quotes';
    $field109->column = 'acc_shuttle_origin_weight';
    $field109->uitype = 7;
    $field109->typeofdata = 'I~O';
    $field109->displaytype = 1;

    $block11->addField($field109);
}
$field110 = Vtiger_Field::getInstance('acc_shuttle_dest_weight', $module);
if ($field110) {
    echo "<li>The acc_shuttle_dest_weight field already exists</li><br> \n";
} else {
    $field110 = new Vtiger_Field();
    $field110->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONWEIGHT';
    $field110->name = 'acc_shuttle_dest_weight';
    $field110->tablename = 'vtiger_quotes';
    $field110->column = 'acc_shuttle_dest_weight';
    $field110->uitype = 7;
    $field110->typeofdata = 'I~O';
    $field110->displaytype = 1;

    $block11->addField($field110);
}
$field111 = Vtiger_Field::getInstance('acc_shuttle_origin_applied', $module);
if ($field111) {
    echo "<li>The acc_shuttle_origin_applied field already exists</li><br> \n";
} else {
    $field111 = new Vtiger_Field();
    $field111->label = 'LBL_QUOTES_ACCSHUTTLEORIGINAPPLIED';
    $field111->name = 'acc_shuttle_origin_applied';
    $field111->tablename = 'vtiger_quotes';
    $field111->column = 'acc_shuttle_origin_applied';
    $field111->uitype = 56;
    $field111->typeofdata = 'C~O';
    $field111->displaytype = 1;

    $block11->addField($field111);
}
$field112 = Vtiger_Field::getInstance('acc_shuttle_dest_applied', $module);
if ($field112) {
    echo "<li>The acc_shuttle_dest_applied field already exists</li><br> \n";
} else {
    $field112 = new Vtiger_Field();
    $field112->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONAPPLIED';
    $field112->name = 'acc_shuttle_dest_applied';
    $field112->tablename = 'vtiger_quotes';
    $field112->column = 'acc_shuttle_dest_applied';
    $field112->uitype = 56;
    $field112->typeofdata = 'C~O';
    $field112->displaytype = 1;

    $block11->addField($field112);
}
$field113 = Vtiger_Field::getInstance('acc_shuttle_origin_ot', $module);
if ($field113) {
    echo "<li>The acc_shuttle_origin_ot field already exists</li><br> \n";
} else {
    $field113 = new Vtiger_Field();
    $field113->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOT';
    $field113->name = 'acc_shuttle_origin_ot';
    $field113->tablename = 'vtiger_quotes';
    $field113->column = 'acc_shuttle_origin_ot';
    $field113->uitype = 56;
    $field113->typeofdata = 'C~O';
    $field113->displaytype = 1;

    $block11->addField($field113);
}
$field114 = Vtiger_Field::getInstance('acc_shuttle_dest_ot', $module);
if ($field114) {
    echo "<li>The acc_shuttle_dest_ot field already exists</li><br> \n";
} else {
    $field114 = new Vtiger_Field();
    $field114->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOT';
    $field114->name = 'acc_shuttle_dest_ot';
    $field114->tablename = 'vtiger_quotes';
    $field114->column = 'acc_shuttle_dest_ot';
    $field114->uitype = 56;
    $field114->typeofdata = 'C~O';
    $field114->displaytype = 1;

    $block11->addField($field114);
}
$field115 = Vtiger_Field::getInstance('acc_shuttle_origin_over25', $module);
if ($field115) {
    echo "<li>The acc_shuttle_origin_over25 field already exists</li><br> \n";
} else {
    $field115 = new Vtiger_Field();
    $field115->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOVER25';
    $field115->name = 'acc_shuttle_origin_over25';
    $field115->tablename = 'vtiger_quotes';
    $field115->column = 'acc_shuttle_origin_over25';
    $field115->uitype = 56;
    $field115->typeofdata = 'C~O';
    $field115->displaytype = 1;

    $block11->addField($field115);
}
$field116 = Vtiger_Field::getInstance('acc_shuttle_dest_over25', $module);
if ($field116) {
    echo "<li>The acc_shuttle_dest_over25 field already exists</li><br> \n";
} else {
    $field116 = new Vtiger_Field();
    $field116->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOVER25';
    $field116->name = 'acc_shuttle_dest_over25';
    $field116->tablename = 'vtiger_quotes';
    $field116->column = 'acc_shuttle_dest_over25';
    $field116->uitype = 56;
    $field116->typeofdata = 'C~O';
    $field116->displaytype = 1;

    $block11->addField($field116);
}
$field117 = Vtiger_Field::getInstance('acc_shuttle_origin_miles', $module);
if ($field117) {
    echo "<li>The acc_shuttle_origin_miles field already exists</li><br> \n";
} else {
    $field117 = new Vtiger_Field();
    $field117->label = 'LBL_QUOTES_ACCSHUTTLEORIGINMILES';
    $field117->name = 'acc_shuttle_origin_miles';
    $field117->tablename = 'vtiger_quotes';
    $field117->column = 'acc_shuttle_origin_miles';
    $field117->uitype = 7;
    $field117->typeofdata = 'I~O';
    $field117->displaytype = 1;

    $block11->addField($field117);
}
$field118 = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $module);
if ($field118) {
    echo "<li>The acc_shuttle_dest_miles field already exists</li><br> \n";
} else {
    $field118 = new Vtiger_Field();
    $field118->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONMILES';
    $field118->name = 'acc_shuttle_dest_miles';
    $field118->tablename = 'vtiger_quotes';
    $field118->column = 'acc_shuttle_dest_miles';
    $field118->uitype = 7;
    $field118->typeofdata = 'I~O';
    $field118->displaytype = 1;

    $block11->addField($field118);
}
$field119 = Vtiger_Field::getInstance('acc_ot_origin_weight', $module);
if ($field119) {
    echo "<li>The acc_ot_origin_weight field already exists</li><br> \n";
} else {
    $field119 = new Vtiger_Field();
    $field119->label = 'LBL_QUOTES_ACCOTORIGINWEIGHT';
    $field119->name = 'acc_ot_origin_weight';
    $field119->tablename = 'vtiger_quotes';
    $field119->column = 'acc_ot_origin_weight';
    $field119->uitype = 7;
    $field119->typeofdata = 'I~O';
    $field119->displaytype = 1;

    $block11->addField($field119);
}
$field120 = Vtiger_Field::getInstance('acc_ot_dest_weight', $module);
if ($field120) {
    echo "<li>The acc_ot_dest_weight field already exists</li><br> \n";
} else {
    $field120 = new Vtiger_Field();
    $field120->label = 'LBL_QUOTES_ACCOTDESTINATIONWEIGHT';
    $field120->name = 'acc_ot_dest_weight';
    $field120->tablename = 'vtiger_quotes';
    $field120->column = 'acc_ot_dest_weight';
    $field120->uitype = 7;
    $field120->typeofdata = 'I~O';
    $field120->displaytype = 1;

    $block11->addField($field120);
}
$field121 = Vtiger_Field::getInstance('acc_ot_origin_applied', $module);
if ($field121) {
    echo "<li>The acc_ot_origin_applied field already exists</li><br> \n";
} else {
    $field121 = new Vtiger_Field();
    $field121->label = 'LBL_QUOTES_ACCOTORIGINAPPLIED';
    $field121->name = 'acc_ot_origin_applied';
    $field121->tablename = 'vtiger_quotes';
    $field121->column = 'acc_ot_origin_applied';
    $field121->uitype = 56;
    $field121->typeofdata = 'C~O';
    $field121->displaytype = 1;

    $block11->addField($field121);
}
$field122 = Vtiger_Field::getInstance('acc_ot_dest_applied', $module);
if ($field122) {
    echo "<li>The acc_ot_dest_applied field already exists</li><br> \n";
} else {
    $field122 = new Vtiger_Field();
    $field122->label = 'LBL_QUOTES_ACCOTDESTINATIONAPPLIED';
    $field122->name = 'acc_ot_dest_applied';
    $field122->tablename = 'vtiger_quotes';
    $field122->column = 'acc_ot_dest_applied';
    $field122->uitype = 56;
    $field122->typeofdata = 'C~O';
    $field122->displaytype = 1;

    $block11->addField($field122);
}
$field123 = Vtiger_Field::getInstance('acc_selfstg_origin_weight', $module);
if ($field123) {
    echo "<li>The acc_selfstg_origin_weight field already exists</li><br> \n";
} else {
    $field123 = new Vtiger_Field();
    $field123->label = 'LBL_QUOTES_ACCSELFSTGORIGINWEIGHT';
    $field123->name = 'acc_selfstg_origin_weight';
    $field123->tablename = 'vtiger_quotes';
    $field123->column = 'acc_selfstg_origin_weight';
    $field123->uitype = 7;
    $field123->typeofdata = 'I~O';
    $field123->displaytype = 1;

    $block11->addField($field123);
}
$field124 = Vtiger_Field::getInstance('acc_selfstg_dest_weight', $module);
if ($field124) {
    echo "<li>The acc_selfstg_dest_weight field already exists</li><br> \n";
} else {
    $field124 = new Vtiger_Field();
    $field124->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONWEIGHT';
    $field124->name = 'acc_selfstg_dest_weight';
    $field124->tablename = 'vtiger_quotes';
    $field124->column = 'acc_selfstg_dest_weight';
    $field124->uitype = 7;
    $field124->typeofdata = 'I~O';
    $field124->displaytype = 1;

    $block11->addField($field124);
}
$field125 = Vtiger_Field::getInstance('acc_selfstg_origin_applied', $module);
if ($field125) {
    echo "<li>The acc_selfstg_origin_applied field already exists</li><br> \n";
} else {
    $field125 = new Vtiger_Field();
    $field125->label = 'LBL_QUOTES_ACCSELFSTGORIGINAPPLIED';
    $field125->name = 'acc_selfstg_origin_applied';
    $field125->tablename = 'vtiger_quotes';
    $field125->column = 'acc_selfstg_origin_applied';
    $field125->uitype = 56;
    $field125->typeofdata = 'C~O';
    $field125->displaytype = 1;

    $block11->addField($field125);
}
$field126 = Vtiger_Field::getInstance('acc_selfstg_dest_applied', $module);
if ($field126) {
    echo "<li>The acc_selfstg_dest_applied field already exists</li><br> \n";
} else {
    $field126 = new Vtiger_Field();
    $field126->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONAPPLIED';
    $field126->name = 'acc_selfstg_dest_applied';
    $field126->tablename = 'vtiger_quotes';
    $field126->column = 'acc_selfstg_dest_applied';
    $field126->uitype = 56;
    $field126->typeofdata = 'C~O';
    $field126->displaytype = 1;

    $block11->addField($field126);
}
$field127 = Vtiger_Field::getInstance('acc_selfstg_origin_ot', $module);
if ($field127) {
    echo "<li>The acc_selfstg_origin_ot field already exists</li><br> \n";
} else {
    $field127 = new Vtiger_Field();
    $field127->label = 'LBL_QUOTES_ACCSELFSTGORIGINOT';
    $field127->name = 'acc_selfstg_origin_ot';
    $field127->tablename = 'vtiger_quotes';
    $field127->column = 'acc_selfstg_origin_ot';
    $field127->uitype = 56;
    $field127->typeofdata = 'C~O';
    $field127->displaytype = 1;

    $block11->addField($field127);
}
$field128 = Vtiger_Field::getInstance('acc_selfstg_dest_ot', $module);
if ($field128) {
    echo "<li>The acc_selfstg_dest_ot field already exists</li><br> \n";
} else {
    $field128 = new Vtiger_Field();
    $field128->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONOT';
    $field128->name = 'acc_selfstg_dest_ot';
    $field128->tablename = 'vtiger_quotes';
    $field128->column = 'acc_selfstg_dest_ot';
    $field128->uitype = 56;
    $field128->typeofdata = 'C~O';
    $field128->displaytype = 1;

    $block11->addField($field128);
}
$field129 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $module);
if ($field129) {
    echo "<li>The acc_exlabor_origin_hours field already exists</li><br> \n";
} else {
    $field129 = new Vtiger_Field();
    $field129->label = 'LBL_QUOTES_ACCEXLABORORIGINHOURS';
    $field129->name = 'acc_exlabor_origin_hours';
    $field129->tablename = 'vtiger_quotes';
    $field129->column = 'acc_exlabor_origin_hours';
    $field129->uitype = 7;
    $field129->typeofdata = 'I~O';
    $field129->displaytype = 1;

    $block11->addField($field129);
}
$field130 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $module);
if ($field130) {
    echo "<li>The acc_exlabor_dest_hours field already exists</li><br> \n";
} else {
    $field130 = new Vtiger_Field();
    $field130->label = 'LBL_QUOTES_ACCEXLABORDESTINATIONHOURS';
    $field130->name = 'acc_exlabor_dest_hours';
    $field130->tablename = 'vtiger_quotes';
    $field130->column = 'acc_exlabor_dest_hours';
    $field130->uitype = 7;
    $field130->typeofdata = 'I~O';
    $field130->displaytype = 1;

    $block11->addField($field130);
}
$field131 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $module);
if ($field131) {
    echo "<li>The acc_exlabor_ot_origin_hours field already exists</li><br> \n";
} else {
    $field131 = new Vtiger_Field();
    $field131->label = 'LBL_QUOTES_ACCEXLABOROTORIGINHOURS';
    $field131->name = 'acc_exlabor_ot_origin_hours';
    $field131->tablename = 'vtiger_quotes';
    $field131->column = 'acc_exlabor_ot_origin_hours';
    $field131->uitype = 7;
    $field131->typeofdata = 'I~O';
    $field131->displaytype = 1;

    $block11->addField($field131);
}
$field132 = Vtiger_Field::getInstance('acc_exlabor_ot_dest_hours', $module);
if ($field132) {
    echo "<li>The acc_exlabor_ot_dest_hours field already exists</li><br> \n";
} else {
    $field132 = new Vtiger_Field();
    $field132->label = 'LBL_QUOTES_ACCEXLABOROTDESTINATIONHOURS';
    $field132->name = 'acc_exlabor_ot_dest_hours';
    $field132->tablename = 'vtiger_quotes';
    $field132->column = 'acc_exlabor_ot_dest_hours';
    $field132->uitype = 7;
    $field132->typeofdata = 'I~O';
    $field132->displaytype = 1;

    $block11->addField($field132);
}
$field133 = Vtiger_Field::getInstance('acc_wait_origin_hours', $module);
if ($field133) {
    echo "<li>The acc_wait_origin_hours field already exists</li><br> \n";
} else {
    $field133 = new Vtiger_Field();
    $field133->label = 'LBL_QUOTES_ACCWAITORIGINHOURS';
    $field133->name = 'acc_wait_origin_hours';
    $field133->tablename = 'vtiger_quotes';
    $field133->column = 'acc_wait_origin_hours';
    $field133->uitype = 7;
    $field133->typeofdata = 'I~O';
    $field133->displaytype = 1;

    $block11->addField($field133);
}
$field134 = Vtiger_Field::getInstance('acc_wait_dest_hours', $module);
if ($field134) {
    echo "<li>The acc_wait_dest_hours field already exists</li><br> \n";
} else {
    $field134 = new Vtiger_Field();
    $field134->label = 'LBL_QUOTES_ACCWAITDESTINATIONHOURS';
    $field134->name = 'acc_wait_dest_hours';
    $field134->tablename = 'vtiger_quotes';
    $field134->column = 'acc_wait_dest_hours';
    $field134->uitype = 7;
    $field134->typeofdata = 'I~O';
    $field134->displaytype = 1;

    $block11->addField($field134);
}
$field135 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $module);
if ($field135) {
    echo "<li>The acc_wait_ot_origin_hours field already exists</li><br> \n";
} else {
    $field135 = new Vtiger_Field();
    $field135->label = 'LBL_QUOTES_ACCWAITOTORIGINHOURS';
    $field135->name = 'acc_wait_ot_origin_hours';
    $field135->tablename = 'vtiger_quotes';
    $field135->column = 'acc_wait_ot_origin_hours';
    $field135->uitype = 7;
    $field135->typeofdata = 'I~O';
    $field135->displaytype = 1;

    $block11->addField($field135);
}
$field136 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $module);
if ($field136) {
    echo "<li>The acc_wait_ot_dest_hours field already exists</li><br> \n";
} else {
    $field136 = new Vtiger_Field();
    $field136->label = 'LBL_QUOTES_ACCWAITOTDESTINATIONHOURS';
    $field136->name = 'acc_wait_ot_dest_hours';
    $field136->tablename = 'vtiger_quotes';
    $field136->column = 'acc_wait_ot_dest_hours';
    $field136->uitype = 7;
    $field136->typeofdata = 'I~O';
    $field136->displaytype = 1;

    $block11->addField($field136);
}
    
echo "</ul>";
$block11->save($module);
//end block11 : LBL_QUOTES_ACCESSORIALDETAILS

if ($isNew) {
    $module->setDefaultSharing();
    $module->initWebservice();
    
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'ALL';
    $filter1->isdefault = true;
    $module->addFilter($filter1);
    
    $filter1->addField($field1)->addField($field4, 1)->addField($field2, 2)->addField($field8, 3)->addField($field66, 4)->addField($field9, 5);
    // Adds the Updates link to the vertical navigation menu on the right.
    ModTracker::enableTrackingForModule($module->id);

    $module->setRelatedList(Vtiger_Module::getInstance('Calendar'), 'Activities', array('add'), 'get_activities');
    $module->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('add', 'select'), 'get_attachments');
}
