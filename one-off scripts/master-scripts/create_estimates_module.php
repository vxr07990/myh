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


/*
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');*/

$isNew = false;

//creating estimates module
$moduleInstance = Vtiger_Module::getInstance('Estimates');
if ($moduleInstance) {
    echo "Module exists";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "Estimates";
    $moduleInstance->save();
}
//$moduleInstance->initTables();
$blockInstance = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_QUOTE_INFORMATION block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_QUOTE_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
    $isNew = true;
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_QUOTES_CONTACTDETAILS', $moduleInstance);
if ($blockInstance2) {
    echo "<li>The LBL_QUOTES_CONTACTDETAILS block already exists</li><br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_QUOTES_CONTACTDETAILS';
    $moduleInstance->addBlock($blockInstance2);
}

$blockInstance3 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $moduleInstance);
if ($blockInstance3) {
    echo "<li>The LBL_ADDRESS_INFORMATION block already exists</li><br>";
} else {
    $blockInstance3 = new Vtiger_Block();
    $blockInstance3->label = 'LBL_ADDRESS_INFORMATION';
    $moduleInstance->addBlock($blockInstance3);
}
// #4
$blockInstance4 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $moduleInstance);
if ($blockInstance4) {
    echo "<li>The LBL_QUOTES_LOCALMOVEDETAILS block already exists</li><br>";
} else {
    $blockInstance4 = new Vtiger_Block();
    $blockInstance4->label = 'LBL_QUOTES_LOCALMOVEDETAILS';
    $moduleInstance->addBlock($blockInstance4);
}

$blockInstance5 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleInstance);
if ($blockInstance5) {
    echo "<li>The LBL_QUOTES_INTERSTATEMOVEDETAILS block already exists</li><br>";
} else {
    $blockInstance5 = new Vtiger_Block();
    $blockInstance5->label = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
    $moduleInstance->addBlock($blockInstance5);
}
// #6
$blockInstance6 = Vtiger_Block::getInstance('LBL_QUOTES_COMMERCIALMOVEDETAILS', $moduleInstance);
if ($blockInstance6) {
    echo "<li>The LBL_QUOTES_COMMERCIALMOVEDETAILS block already exists</li><br>";
} else {
    $blockInstance6 = new Vtiger_Block();
    $blockInstance6->label = 'LBL_QUOTES_COMMERCIALMOVEDETAILS';
    $moduleInstance->addBlock($blockInstance6);
}

$blockInstance10 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $moduleInstance);
if ($blockInstance10) {
    echo "<li>The LBL_QUOTES_SITDETAILS block already exists</li><br>";
} else {
    $blockInstance10 = new Vtiger_Block();
    $blockInstance10->label = 'LBL_QUOTES_SITDETAILS';
    $moduleInstance->addBlock($blockInstance10);
}

$blockInstance11 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleInstance);
if ($blockInstance11) {
    echo "<li>The LBL_QUOTES_ACCESSORIALDETAILS block already exists</li><br>";
} else {
    $blockInstance11 = new Vtiger_Block();
    $blockInstance11->label = 'LBL_QUOTES_ACCESSORIALDETAILS';
    $moduleInstance->addBlock($blockInstance11);
}

$blockInstance7 = Vtiger_Block::getInstance('LBL_TERMS_INFORMATION', $moduleInstance);
if ($blockInstance7) {
    echo "<li>The LBL_TERMS_INFORMATION block already exists</li><br>";
} else {
    $blockInstance7 = new Vtiger_Block();
    $blockInstance7->label = 'LBL_TERMS_INFORMATION';
    $moduleInstance->addBlock($blockInstance7);
}

$blockInstance8 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $moduleInstance);
if ($blockInstance8) {
    echo "<li>The LBL_DESCRIPTION_INFORMATION block already exists</li><br>";
} else {
    $blockInstance8 = new Vtiger_Block();
    $blockInstance8->label = 'LBL_DESCRIPTION_INFORMATION';
    $moduleInstance->addBlock($blockInstance8);
}

$blockInstance9 = Vtiger_Block::getInstance('LBL_ITEM_DETAILS', $moduleInstance);
if ($blockInstance9) {
    echo "<li>The LBL_ITEM_DETAILS block already exists</li><br>";
} else {
    $blockInstance9 = new Vtiger_Block();
    $blockInstance9->label = 'LBL_ITEM_DETAILS';
    $moduleInstance->addBlock($blockInstance9);
}
/**
 * @param null
 * @return data for module from quotes
 */
$field1 = Vtiger_Field::getInstance('subject', $moduleInstance);
if ($field1) {
    echo "<li>The subject field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_QUOTES_SUBJECT';
    $field1->name = 'subject';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'subject';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 1;
    $field1->defaultvalue = 'Estimate';
    $field1->presence = 2;

    $blockInstance->addField($field1);

//Removing call because it incorrectly sets values in vtiger_entityname.
//This kills the crab.
//$moduleInstance->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('potential_id', $moduleInstance);
if ($field2) {
    echo "<li>The potential_id field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_POTENTIALNAME';
    $field2->name = 'potential_id';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'potentialid';
    $field2->uitype = 10;
    $field2->typeofdata = 'I~O';
    $field2->displaytype = 1;

    $blockInstance->addField($field2);

    $field2->setRelatedModules(array('Opportunities'));
}

$field3 = Vtiger_Field::getInstance('quote_no', $moduleInstance);
if ($field3) {
    echo "<li>The quote_no field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_QUOTENUMBER';
    $field3->name = 'quote_no';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'quote_no';
    $field3->uitype = 4;
    $field3->typeofdata = 'V~M';
    $field3->displaytype = 1;
    $field3->presence = 2;
    $field3->quickcreate = 1;

    $blockInstance->addField($field3);

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'EST', 1);
}

$field4 = Vtiger_Field::getInstance('quotestage', $moduleInstance);
if ($field4) {
    echo "<li>The quotestage field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_QUOTESTAGE';
    $field4->name = 'quotestage';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'quotestage';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~M';
    $field4->displaytype = 1;
    $field4->quickcreate = 0;
    $field4->defaultvalue = 'Created';

    $blockInstance->addField($field4);
}

$field5 = Vtiger_Field::getInstance('validtill', $moduleInstance);
if ($field5) {
    echo "<li>The validtill field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_VALIDUTILL';
    $field5->name = 'validtill';
    $field5->table = 'vtiger_quotes';
    $field5->column = 'validtill';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';
    $field5->displaytype = 1;

    $blockInstance->addField($field5);
}

$field6 = Vtiger_Field::getInstance('contact_id', $moduleInstance);
if ($field6) {
    echo "<li>The contact_id field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_CONTACTNAME';
    $field6->name = 'contact_id';
    $field6->table = 'vtiger_quotes';
    $field6->column = 'contactid';
    $field6->uitype = 57;
    $field6->typeofdata = 'V~O';
    $field6->displaytype = 1;

    $blockInstance->addField($field6);
}

$field8 = Vtiger_Field::getInstance('account_id', $moduleInstance);
if ($field8) {
    echo "<li>The account_id field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_QUOTES_ACCOUNTNAME';
    $field8->name = 'account_id';
    $field8->table = 'vtiger_quotes';
    $field8->column = 'accountid';
    $field8->uitype = 73;
    $field8->typeofdata = 'I~O';
    $field8->displaytype = 1;
    $field8->presence = 2;

    $blockInstance->addField($field8);
}

$field9 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field9) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_QUOTES_ASSIGNEDTO';
    $field9->name = 'assigned_user_id';
    $field9->table = 'vtiger_crmentity';
    $field9->column = 'smownerid';
    $field9->uitype = 53;
    $field9->typeofdata = 'V~M';
    $field9->displaytype = 1;
    $field9->quickcreate = 0;
    $field9->presence = 2;

    $blockInstance->addField($field9);
}

$field10 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field10) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_QUOTES_CREATEDTIME';
    $field10->name = 'createdtime';
    $field10->table = 'vtiger_crmentity';
    $field10->column = 'createdtime';
    $field10->uitype = 70;
    $field10->typeofdata = 'DT~O';
    $field10->displaytype = 2;
    $field10->presence = 2;
    $field10->quickcreate = 1;

    $blockInstance->addField($field10);
}
$field11 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field11) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_QUOTES_MODIFIEDTIME';
    $field11->name = 'modifiedtime';
    $field11->table = 'vtiger_crmentity';
    $field11->column = 'modifiedtime';
    $field11->uitype = 70;
    $field11->typeofdata = 'DT~O';
    $field11->displaytype = 2;
    $field11->presence = 2;
    $field11->quickcreate= 1;

    $blockInstance->addField($field11);
}

$field12 = Vtiger_Field::getInstance('business_line_est', $moduleInstance);
if ($field12) {
    echo "<li>The business_line_est field already exists in Estimates</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_QUOTES_BUSINESSLINE';
    $field12->name = 'business_line_est';
    $field12->table = 'vtiger_quotescf';
    $field12->column = 'business_line_est';
    $field12->columntype='VARCHAR(200)';
    $field12->uitype = 16;
    $field12->typeofdata = 'V~O';
    $field12->displaytype = 1;
    $field12->quickcreate = 0;

    $blockInstance->addField($field12);
    
    $field_business_line = Vtiger_Field::getInstance('business_line', $moduleInstance);
    if ($field_business_line) {
        echo "<li>Updating existing business_line field to be business_line_est : ".$field_business_line->id."</li><br>";
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field_business_line->id);
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_quotescf` SET business_line_est=business_line");
    }
}

$field14 = Vtiger_Field::getInstance('is_primary', $moduleInstance);
if ($field14) {
    echo "<li>The is_primary field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_QUOTES_ISPRIMARY';
    $field14->name = 'is_primary';
    $field14->table = 'vtiger_quotes';
    $field14->column = 'is_primary';
    $field14->columntype='VARCHAR(3)';
    $field14->uitype = 56;
    $field14->typeofdata = 'C~O';
    $field14->displaytype = 1;

    $blockInstance->addField($field14);
//$quotesblock1->addField($field14);
}

$field13 = Vtiger_Field::getInstance('orders_id', $moduleInstance);
if ($field13) {
    echo "<li>The orders_id field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_QUOTES_ORDERSID';
    $field13->name = 'orders_id';
    $field13->table = 'vtiger_quotes';
    $field13->column = 'orders_id';
//$field13->columntype='INT(19)';
$field13->uitype = 10;
    $field13->typeofdata = 'V~O';
    $field13->displaytype = 1;

    $blockInstance->addField($field13);
//$quotesblock1->addField($field13);
$field13->setRelatedModules(array('Orders'));
}

//vtiger quotes field core
$field49 = Vtiger_Field::getInstance('pre_tax_total', $moduleInstance);
if ($field49) {
    echo "<li>The pre_tax_total field already exists</li><br>";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_QUOTES_PRETAXTOTAL';
    $field49->name = 'pre_tax_total';
    $field49->table = 'vtiger_quotes';
    $field49->column = 'pre_tax_total';
    $field49->uitype = 72;
    $field49->typeofdata = 'N~O';
    $field49->displaytype = 3;

    $blockInstance->addField($field49);
}

//vtiger quotes ore field
$field51 = Vtiger_Field::getInstance('modifiedby', $moduleInstance);
if ($field51) {
    echo "<li>The modifiedby field already exists</li><br>";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_QUOTES_LASTMODIFIEDBY';
    $field51->name = 'modifiedby';
    $field51->table = 'vtiger_crmentity';
    $field51->column = 'modifiedby';
    $field51->uitype = 52;
    $field51->typeofdata = 'V~O';
    $field51->displaytype = 3;
    $field51->presence = 2;
    $field51->quickcreate = 1;

    $blockInstance->addField($field51);
}
//vtiger quotes core field
$field52 = Vtiger_Field::getInstance('conversion_rate', $moduleInstance);
if ($field52) {
    echo "<li>The conversion_rate field already exists</li><br>";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_QUOTES_CONVERSIONRATE';
    $field52->name = 'conversion_rate';
    $field52->table = 'vtiger_quotes';
    $field52->column = 'conversion_rate';
    $field52->uitype = 1;
    $field52->typeofdata = 'N~O';
    $field52->displaytype = 3;
    $field52->defaultvalue = 1;
    $field52->quickcreate =3;

    $blockInstance->addField($field52);
}
//vtiger quotes core field
$field54 = Vtiger_Field::getInstance('hdnDiscountAmount', $moduleInstance);
if ($field54) {
    echo "<li>The hdnDiscountAmount field already exists</li><br>";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'LBL_QUOTES_HDNDISCOUNTAMOUNT';
    $field54->name = 'hdnDiscountAmount';
    $field54->table = 'vtiger_quotes';
    $field54->column = 'discount_amount';
    $field54->uitype = 72;
    $field54->typeofdata = 'N~O';
    $field54->displaytype = 3;

    $blockInstance->addField($field54);
}

//vtiger quotes core field
$field56 = Vtiger_Field::getInstance('hdnS_H_Amount', $moduleInstance);
if ($field56) {
    echo "<li>The hdnS_H_Amount field already exists</li><br>";
} else {
    $field56 = new Vtiger_Field();
    $field56->label = 'LBL_QUOTES_HDNSHAMOUNT';
    $field56->name = 'hdnS_H_Amount';
    $field56->table = 'vtiger_quotes';
    $field56->column = 's_h_amount';
    $field56->uitype = 72;
    $field56->typeofdata = 'N~O';
    $field56->displaytype = 3;

    $blockInstance->addField($field56);
}

//vtiger quotes core field
$field58 = Vtiger_Field::getInstance('hdnSubTotal', $moduleInstance);
if ($field58) {
    echo "<li>The hdnSubTotal field already exists</li><br>";
} else {
    $field58 = new Vtiger_Field();
    $field58->label = 'LBL_QUOTES_HDNSUBTOTAL';
    $field58->name = 'hdnSubTotal';
    $field58->table = 'vtiger_quotes';
    $field58->column = 'subtotal';
    $field58->uitype = 72;
    $field58->typeofdata = 'N~O';
    $field58->displaytype = 3;

    $blockInstance->addField($field58);
}

//vtiger quotes core field
$field62 = Vtiger_Field::getInstance('txtAdjustment', $moduleInstance);
if ($field62) {
    echo "<li>The txtAdjustment field already exists</li><br>";
} else {
    $field62 = new Vtiger_Field();
    $field62->label = 'LBL_QUOTES_ADJUSTMENT';
    $field62->name = 'txtAdjustment';
    $field62->table = 'vtiger_quotes';
    $field62->column = 'adjustment';
    $field62->uitype = 72;
    $field62->typeofdata = 'N~O';
    $field62->displaytype = 3;

    $blockInstance->addField($field62);
}

$field66 = Vtiger_Field::getInstance('hdnGrandTotal', $moduleInstance);
if ($field66) {
    echo "<li>The hdnGrandTotal field already exists</li><br>";
} else {
    $field66 = new Vtiger_Field();
    $field66->label = 'LBL_QUOTES_HDNGRANDTOTAL';
    $field66->name = 'hdnGrandTotal';
    $field66->table = 'vtiger_quotes';
    $field66->column = 'total';
    $field66->uitype = 72;
    $field66->typeofdata = 'N~O';
    $field66->displaytype = 3;

    $blockInstance->addField($field66);
}

//vtiger quotes core field
$field137 = Vtiger_Field::getInstance('hdnTaxType', $moduleInstance);
if ($field137) {
    echo "<li>The hdnTaxType field already exists</li><br>";
} else {
    $field137 = new Vtiger_Field();
    $field137->label = 'LBL_QUOTES_HDNTAXTYPE';
    $field137->name = 'hdnTaxType';
    $field137->table = 'vtiger_quotes';
    $field137->column = 'taxtype';
    $field137->uitype = 16;
    $field137->typeofdata = 'V~O';
    $field137->displaytype = 3;

    $blockInstance->addField($field137);
}

//vtiger quotes core field
$field139 = Vtiger_Field::getInstance('hdnDiscountPercent', $moduleInstance);
if ($field139) {
    echo "<li>The hdnDiscountPercent field already exists</li><br>";
} else {
    $field139 = new Vtiger_Field();
    $field139->label = 'LBL_QUOTES_HDNDISCOUNTPERCENT';
    $field139->name = 'hdnDiscountPercent';
    $field139->table = 'vtiger_quotes';
    $field139->column = 'discount_percent';
    $field139->uitype = 1;
    $field139->typeofdata = 'N~O';
    $field139->displaytype = 3;

    $blockInstance->addField($field139);
}

//vtiger quotes core field
$field140 = Vtiger_Field::getInstance('currency_id', $moduleInstance);
if ($field140) {
    echo "<li>The currency_id field already exists</li><br>";
} else {
    $field140 = new Vtiger_Field();
    $field140->label = 'LBL_QUOTES_CURRENCY';
    $field140->name = 'currency_id';
    $field140->table = 'vtiger_quotes';
    $field140->column = 'currency_id';
    $field140->uitype = 117;
    $field140->typeofdata = 'I~O';
    $field140->displaytype = 3;
    $field140->quickcreate=3;

    $blockInstance->addField($field140);
}

//fields for blockInstance2
//vtiger quotes core field
$field15 = Vtiger_Field::getInstance('bill_street', $moduleInstance);
if ($field15) {
    echo "<li>The bill_street field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_QUOTES_BILLINGADDRESS';
    $field15->name = 'bill_street';
    $field15->table = 'vtiger_quotesbillads';
    $field15->column = 'bill_street';
    $field15->uitype = 1;// Changed from 24 to match other address fields.
$field15->typeofdata = 'V~O';
    $field15->displaytype = 1;

    $blockInstance2->addField($field15);
}

//vtiger quotes core field
$field16 = Vtiger_Field::getInstance('bill_city', $moduleInstance);
if ($field16) {
    echo "<li>The bill_city field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_QUOTES_BILLINGCITY';
    $field16->name = 'bill_city';
    $field16->table = 'vtiger_quotesbillads';
    $field16->column = 'bill_city';
    $field16->uitype = 1;
    $field16->typeofdata = 'V~O';
    $field16->displaytype = 1;

    $blockInstance2->addField($field16);
}
//vtiger quotes core field
$field17 = Vtiger_Field::getInstance('bill_state', $moduleInstance);
if ($field17) {
    echo "<li>The bill_state field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_QUOTES_BILLINGSTATE';
    $field17->name = 'bill_state';
    $field17->table = 'vtiger_quotesbillads';
    $field17->column = 'bill_state';
    $field17->uitype = 1;
    $field17->typeofdata = 'V~O';
    $field17->displaytype = 1;

    $blockInstance2->addField($field17);
}
//vtiger quotes core field
$field18 = Vtiger_Field::getInstance('bill_code', $moduleInstance);
if ($field18) {
    echo "<li>The bill_code field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_QUOTES_BILLINGZIPCODE';
    $field18->name = 'bill_code';
    $field18->table = 'vtiger_quotesbillads';
    $field18->column = 'bill_code';
    $field18->uitype = 1;
    $field18->typeofdata = 'V~O';
    $field18->displaytype = 1;

    $blockInstance2->addField($field18);
}
//vtiger quotes core field
$field19 = Vtiger_Field::getInstance('bill_pobox', $moduleInstance);
if ($field19) {
    echo "<li>The bill_pobox field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_QUOTES_BILLINGPOBOX';
    $field19->name = 'bill_pobox';
    $field19->table = 'vtiger_quotesbillads';
    $field19->column = 'bill_pobox';
    $field19->uitype = 1;
    $field19->typeofdata = 'V~O';
    $field19->displaytype = 1;

    $blockInstance2->addField($field19);
}
// from 32
//vtiger quotes core field
$field20 = Vtiger_Field::getInstance('bill_country', $moduleInstance);
if ($field20) {
    echo "<li>The bill_country field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_QUOTES_BILLINGCOUNTRY';
    $field20->name = 'bill_country';
    $field20->table = 'vtiger_quotesbillads';
    $field20->column = 'bill_country';
    $field20->uitype = 1;
    $field20->typeofdata = 'V~O';
    $field20->displaytype = 1;

    $blockInstance2->addField($field20);
}

//fields for blockInstance3
// start custom fields
$field21 = Vtiger_Field::getInstance('origin_address1', $moduleInstance);
if ($field21) {
    echo "<li>The origin_address1 field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_QUOTES_ORIGINADDRESS1';
    $field21->name = 'origin_address1';
    $field21->table = 'vtiger_quotescf';
    $field21->column = 'origin_address1';
    $field21->columntype = 'VARCHAR(255)';
    $field21->uitype = 1;
    $field21->typeofdata = 'V~O~LE~50';
    $field21->displaytype = 1;

    $blockInstance3->addField($field21);
}

$field22 = Vtiger_Field::getInstance('destination_address1', $moduleInstance);
if ($field22) {
    echo "<li>The destination_address1 field already exists</li><br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_QUOTES_DESTINATIONADDRESS1';
    $field22->name = 'destination_address1';
    $field22->table = 'vtiger_quotescf';
    $field22->column = 'destination_address1';
    $field22->columntype = 'VARCHAR(255)';
    $field22->uitype = 1;
    $field22->typeofdata = 'V~O~LE~50';
    $field22->displaytype = 1;

    $blockInstance3->addField($field22);
}
// from 51
$field23 = Vtiger_Field::getInstance('origin_address2', $moduleInstance);
if ($field23) {
    echo "<li>The origin_address2 field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_QUOTES_ORIGINADDRESS2';
    $field23->name = 'origin_address2';
    $field23->table = 'vtiger_quotescf';
    $field23->column = 'origin_address2';
    $field23->columntype = 'VARCHAR(255)';
    $field23->uitype = 1;
    $field23->typeofdata = 'V~O~LE~50';
    $field23->displaytype = 1;

    $blockInstance3->addField($field23);
}

$field24 = Vtiger_Field::getInstance('destination_address2', $moduleInstance);
if ($field24) {
    echo "<li>The destination_address2 field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_QUOTES_DESTINATIONADDRESS2';
    $field24->name = 'destination_address2';
    $field24->table = 'vtiger_quotescf';
    $field24->column = 'destination_address2';
    $field24->columntype = 'VARCHAR(255)';
    $field24->uitype = 1;
    $field24->typeofdata = 'V~O~LE~50';
    $field24->displaytype = 1;

    $blockInstance3->addField($field24);
}

$field25 = Vtiger_Field::getInstance('origin_city', $moduleInstance);
if ($field25) {
    echo "<li>The origin_city field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_QUOTES_ORIGINCITY';
    $field25->name = 'origin_city';
    $field25->table = 'vtiger_quotescf';
    $field25->column = 'origin_city';
    $field25->columntype = 'VARCHAR(255)';
    $field25->uitype = 1;
    $field25->typeofdata = 'V~O~LE~50';
    $field25->displaytype = 1;

    $blockInstance3->addField($field25);
}

$field26 = Vtiger_Field::getInstance('destination_city', $moduleInstance);
if ($field26) {
    echo "<li>The destination_city field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_QUOTES_DESTINATIONCITY';
    $field26->name = 'destination_city';
    $field26->table = 'vtiger_quotescf';
    $field26->column = 'destination_city';
    $field26->columntype = 'VARCHAR(255)';
    $field26->uitype = 1;
    $field26->typeofdata = 'V~O~LE~50';
    $field26->displaytype = 1;

    $blockInstance3->addField($field26);
}

$field27 = Vtiger_Field::getInstance('origin_state', $moduleInstance);
if ($field27) {
    echo "<li>The origin_state field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_QUOTES_ORIGINSTATE';
    $field27->name = 'origin_state';
    $field27->table = 'vtiger_quotescf';
    $field27->column = 'origin_state';
    $field27->columntype = 'VARCHAR(255)';
    $field27->uitype = 1;
    $field27->typeofdata = 'V~O';
    $field27->displaytype = 1;

    $blockInstance3->addField($field27);
}

$field28 = Vtiger_Field::getInstance('destination_state', $moduleInstance);
if ($field28) {
    echo "<li>The destination_state field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_QUOTES_DESTINATIONSTATE';
    $field28->name = 'destination_state';
    $field28->table = 'vtiger_quotescf';
    $field28->column = 'destination_state';
    $field28->columntype = 'VARCHAR(255)';
    $field28->uitype = 1;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 1;

    $blockInstance3->addField($field28);
}

$field29 = Vtiger_Field::getInstance('origin_zip', $moduleInstance);
if ($field29) {
    echo "<li>The origin_zip field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_QUOTES_ORIGINZIP';
    $field29->name = 'origin_zip';
    $field29->table = 'vtiger_quotescf';
    $field29->column = 'origin_zip';
    $field29->columntype = 'VARCHAR(30)';
    $field29->uitype = 1;
    $field29->typeofdata = 'V~O';
    $field29->displaytype = 1;
    $field29->quickcreate = 0;

    $blockInstance3->addField($field29);
}

$field30 = Vtiger_Field::getInstance('destination_zip', $moduleInstance);
if ($field30) {
    echo "<li>The destiantion_zip field already exists</li><br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_QUOTES_DESTINATIONZIP';
    $field30->name = 'destination_zip';
    $field30->table = 'vtiger_quotescf';
    $field30->column = 'destination_zip';
    $field30->columntype = 'VARCHAR(30)';
    $field30->uitype = 1;
    $field30->typeofdata = 'V~O';
    $field30->displaytype = 1;
    $field30->quickcreate = 0;

    $blockInstance3->addField($field30);
}

$field31 = Vtiger_Field::getInstance('origin_phone1', $moduleInstance);
if ($field31) {
    echo "<li>The origin_phone1 field already exists</li><br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_QUOTES_ORIGINPHONE1';
    $field31->name = 'origin_phone1';
    $field31->table = 'vtiger_quotescf';
    $field31->column = 'origin_phone1';
    $field31->columntype = 'VARCHAR(255)';
    $field31->uitype = 11;
    $field31->typeofdata = 'V~O';
    $field31->displaytype = 1;

    $blockInstance3->addField($field31);
}

$field32 = Vtiger_Field::getInstance('destination_phone1', $moduleInstance);
if ($field32) {
    echo "<li>The destination_phone1 field already exists</li><br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_QUOTES_DESTINATIONPHONE1';
    $field32->name = 'destination_phone1';
    $field32->table = 'vtiger_quotescf';
    $field32->column = 'destination_phone1';
    $field32->columntype = 'VARCHAR(255)';
    $field32->uitype = 11;
    $field32->typeofdata = 'V~O';
    $field32->displaytype = 1;

    $blockInstance3->addField($field32);
}

$field33 = Vtiger_Field::getInstance('origin_phone2', $moduleInstance);
if ($field33) {
    echo "<li>The origin_phone2 field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_QUOTES_ORIGINPHONE2';
    $field33->name = 'origin_phone2';
    $field33->table = 'vtiger_quotescf';
    $field33->column = 'origin_phone2';
    $field33->columntype = 'VARCHAR(255)';
    $field33->uitype = 11;
    $field33->typeofdata = 'V~O';
    $field33->displaytype = 1;

    $blockInstance3->addField($field33);
}

$field34 = Vtiger_Field::getInstance('destination_phone2', $moduleInstance);
if ($field34) {
    echo "<li>The destination_phone2 field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_QUOTES_DESTINATIONPHONE2';
    $field34->name = 'destination_phone2';
    $field34->table = 'vtiger_quotescf';
    $field34->column = 'destination_phone2';
    $field34->columntype = 'VARCHAR(255)';
    $field34->uitype = 11;
    $field34->typeofdata = 'V~O';
    $field34->displaytype = 1;

    $blockInstance3->addField($field34);
}

//field for blockInstance4
// local
/*
$field35 = Vtiger_Field::getInstance('cf_1003',$moduleInstance);
if($field35) {
    echo "<li>The cf_1003 field already exists</li><br>";
}
else {
$field35 = new Vtiger_Field();
$field35->label = 'LBL_QUOTES_HOLDERFIELD1';
$field35->name = 'cf_1003';
$field35->table = 'vtiger_quotescf';
$field35->column = 'cf_1003';
$field35->columntype = 'VARCHAR(15)';
$field35->uitype = 1;
$field35->typeofdata = 'V~O~LE~15';
$field35->displaytype = 1;
$field35->presence = 1;

$blockInstance4->addField($field35);
}
*/
$field200 = Vtiger_Field::getInstance('effective_date', $moduleInstance);
if ($field200) {
    echo "<li>The effective_date field already exists</li><br>";
} else {
    $field200 = new Vtiger_Field();
    $field200->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field200->name = 'effective_date';
    $field200->table = 'vtiger_quotes';
    $field200->column = 'effective_date';
    $field200->columntype = 'DATE';
    $field200->uitype = 5;
    $field200->typeofdata = 'D~O';
    $field200->displaytype = 1;

    $blockInstance4->addField($field200);
}
// This is getting made twice not entirely sure why, commenting out for now
// $field201 = Vtiger_Field::getInstance('local_bl_discount',$moduleInstance);
// if($field201) {
    // echo "<li>The local_bl_discount field already exists</li><br>";
// }
// else {
// $field201 = new Vtiger_Field();
// $field201->label = 'LBL_QUOTES_LOCALBLDISCOUNT';
// $field201->name = 'local_bl_discount';
// $field201->table = 'vtiger_quotes';
// $field201->column = 'local_bl_discount';
// $field201->columntype = 'DECIMAL(12,3)	';
// $field201->uitype = 7;
// $field201->typeofdata = 'N~O';
// $field201->displaytype = 1;

// $blockInstance4->addField($field201);
// }

//fields for blockInstance5
$field36 = Vtiger_Field::getInstance('weight', $moduleInstance);
if ($field36) {
    echo "<li>The weight field already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_QUOTES_WEIGHT';
    $field36->name = 'weight';
    $field36->table = 'vtiger_quotes';
    $field36->column = 'weight';
    $field36->columntype = 'INT(10)';
    $field36->uitype = 7;
    $field36->typeofdata = 'I~O';
    $field36->displaytype = 1;
    $field36->quickcreate = 0;

    $blockInstance5->addField($field36);
}

$field37 = Vtiger_Field::getInstance('pickup_date', $moduleInstance);
if ($field37) {
    echo "<li>The pickup_date field already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_QUOTES_PICKUPDATE';
    $field37->name = 'pickup_date';
    $field37->table = 'vtiger_quotes';
    $field37->column = 'pickup_date';
    $field37->columntype = 'DATE';
    $field37->uitype = 5;
    $field37->typeofdata = 'D~O';
    $field37->displaytype = 1;

    $blockInstance5->addField($field37);
}

$field38 = Vtiger_Field::getInstance('full_pack', $moduleInstance);
if ($field38) {
    echo "<li>The full_pack field already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_QUOTES_FULLPACKAPPLIED';
    $field38->name = 'full_pack';
    $field38->table = 'vtiger_quotes';
    $field38->column = 'full_pack';
    $field38->columntype = 'VARCHAR(3)';
    $field38->uitype = 56;
    $field38->typeofdata = 'C~O';
    $field38->displaytype = 1;
    $field38->quickcreate = 0;

    $blockInstance5->addField($field38);
}

$field39 = Vtiger_Field::getInstance('valuation_deductible', $moduleInstance);
if ($field39) {
    echo "<li>The valuation_deductible field already exists</li><br>";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_QUOTES_VALUATIONDEDUCTIBLE';
    $field39->name = 'valuation_deductible';
    $field39->table = 'vtiger_quotes';
    $field39->column = 'valuation_deductible';
    $field39->columntype = 'VARCHAR(250)';
    $field39->uitype = 16;
    $field39->typeofdata = 'V~O';
    $field39->displaytype = 1;

    $blockInstance5->addField($field39);
}

$field40 = Vtiger_Field::getInstance('full_unpack', $moduleInstance);
if ($field40) {
    echo "<li>The full_unpack field already exists</li><br>";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_QUOTES_FULLUNPACKAPPLIED';
    $field40->name = 'full_unpack';
    $field40->table = 'vtiger_quotes';
    $field40->column = 'full_unpack';
    $field40->columntype = 'VARCHAR(3)';
    $field40->uitype = 56;
    $field40->typeofdata = 'C~O';
    $field40->displaytype = 1;
    $field40->quickcreate = 0;

    $blockInstance5->addField($field40);
}

$field41 = Vtiger_Field::getInstance('valuation_amount', $moduleInstance);
if ($field41) {
    echo "<li>The valuation_amount field already exists</li><br>";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_QUOTES_VALUATIONAMOUNT';
    $field41->name = 'valuation_amount';
    $field41->table = 'vtiger_quotes';
    $field41->column = 'valuation_amount';
    $field41->columntype = 'DECIMAL(56,8)';
    $field41->uitype = 71;
    $field41->typeofdata = 'N~O';
    $field41->displaytype = 1;

    $blockInstance5->addField($field41);
}

$field42 = Vtiger_Field::getInstance('bottom_line_discount', $moduleInstance);
if ($field42) {
    echo "<li>The bottom_line_discount field already exists</li><br>";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_QUOTES_BOTTOMLINEDISCOUNT';
    $field42->name = 'bottom_line_discount';
    $field42->table = 'vtiger_quotes';
    $field42->column = 'bottom_line_discount';
    $field42->columntype = 'DECIMAL(19,2)';
    $field42->uitype = 7;
    $field42->typeofdata = 'N~O';
    $field42->displaytype = 1;
    $field42->quickcreate = 0;

    $blockInstance5->addField($field42);
}

$field43 = Vtiger_Field::getInstance('interstate_mileage', $moduleInstance);
if ($field43) {
    echo "<li>The interstate_mileage field already exists</li><br>";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_QUOTES_MILEAGE';
    $field43->name = 'interstate_mileage';
    $field43->table = 'vtiger_quotes';
    $field43->column = 'interstate_mileage';
    $field43->columntype = 'INT(19)';
    $field43->uitype = 7;
    $field43->typeofdata = 'I~O';
    $field43->displaytype = 1;

    $blockInstance5->addField($field43);
}



/*$field43 = Vtiger_Field::getInstance('effective_tariff',$moduleInstance);
if($field43) {
    echo "<li>The effective_tariff field already exists</li><br>";
}
else {
$field43 = new Vtiger_Field();
$field43->label = 'LBL_QUOTES_EFFECTIVETARIFF';
$field43->name = 'effective_tariff';
$field43->table = 'vtiger_quotes';
$field43->column = 'effective_tariff';
$field43->columntype = 'INT(11)';
$field43->uitype = 16;
$field43->typeofdata = 'I~O';
$field43->displaytype = 1;

$blockInstance5->addField($field43);
}*/

//create effective_tariff column

echo "Creating effective_tariff column";

Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_quotes` ADD `effective_tariff` int(11)');

//all this stuff is unnecessary the above query has no negative ffect if it is run multiple times

/*echo "<br>checking effective tariff column<br>";

$db = PearDatabase::getInstance();
$query = "SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE
    TABLE_SCHEMA =?
AND TABLE_NAME = `vtiger_quotes`
AND COLUMN_NAME = ‘effective_tariff’";
$result = $db->pquery($query, array($dbconfig['db_name']));

file_put_contents('logs/devLog.log', 'RESULT: '.$result, FILE_APPEND);

echo "<br>check query complete<br>";

if(empty($result)){
    Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_quotes` ADD `effective_tariff` int(11)');
    echo "<br>effective_tariff column added<br>";
} else{echo "<br>effective_tariff column already exists<br>";}*/

//field for blockInstance6
/*
$field65 = Vtiger_Field::getInstance('cf_1007',$moduleInstance);
if($field65) {
    echo "<li>The cf_1007 field already exists</li><br>";
}
else {
$field65 = new Vtiger_Field();
$field65->label = 'LBL_QUOTES_HOLDERFIELD3';
$field65->name = 'cf_1007';
$field65->table = 'vtiger_quotescf';
$field65->column = 'cf_1007';
$field35->columntype = 'VARCHAR(15)';
$field65->uitype = 1;
$field65->typeofdata = 'V~O~LE~15';
$field65->displaytype = 1;
$field65->presence = 1;

$blockInstance6->addField($field65);
}
*/

//field for blockInstance7

$field45 = Vtiger_Field::getInstance('terms_conditions', $moduleInstance);
if ($field45) {
    echo "<li>The terms_conditions field already exists</li><br>";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_QUOTES_TERMSANDCONDITIONS';
    $field45->name = 'terms_conditions';
    $field45->table = 'vtiger_quotes';
    $field45->column = 'terms_conditions';
    $field45->columntype = 'TEXT';
    $field45->uitype = 19;
    $field45->typeofdata = 'V~O';
    $field45->displaytype = 1;

    $blockInstance7->addField($field45);
}

//field for blockInstance8
//vtiger quotes core field
$field46 = Vtiger_Field::getInstance('description', $moduleInstance);
if ($field46) {
    echo "<li>The description field already exists</li><br>";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_QUOTES_DESCRIPTION';
    $field46->name = 'description';
    $field46->table = 'vtiger_crmentity';
    $field46->column = 'description';
    $field46->uitype = 19;
    $field46->typeofdata = 'V~O';
    $field46->displaytype = 1;

    $blockInstance8->addField($field46);
}

//fields for blockInstance9
//vtiger quotes core fields
$field47 = Vtiger_Field::getInstance('tax2', $moduleInstance);
if ($field47) {
    echo "<li>The tax2 field already exists</li><br>";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_QUOTES_TAX2';
    $field47->name = 'tax2';
    $field47->table = 'vtiger_inventoryproductrel';
    $field47->column = 'tax2';
    $field47->uitype = 83;
    $field47->typeofdata = 'V~O';
    $field47->displaytype = 5;

    $blockInstance9->addField($field47);
}
//vtiger core quotes field
$field48 = Vtiger_Field::getInstance('tax3', $moduleInstance);
if ($field48) {
    echo "<li>The tax3 field already exists</li><br>";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_QUOTES_TAX3';
    $field48->name = 'tax3';
    $field48->table = 'vtiger_inventoryproductrel';
    $field48->column = 'tax3';
    $field48->uitype = 83;
    $field48->typeofdata = 'V~O';
    $field48->displaytype = 5;

    $blockInstance9->addField($field48);
}

//vtiger quotes field core
$field50 = Vtiger_Field::getInstance('hdnS_H_Percent', $moduleInstance);
if ($field50) {
    echo "<li>The hdnS_H_Percent field already exists</li><br>";
} else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_QUOTES_HDNSHPERCENT';
    $field50->name = 'hdnS_H_Percent';
    $field50->table = 'vtiger_quotes';
    $field50->column = 's_h_percent';
    $field50->uitype = 1;
    $field50->typeofdata = 'N~O';
    $field50->displaytype = 5;

    $blockInstance9->addField($field50);
}

//vtiger quotes core field
$field68 = Vtiger_Field::getInstance('tax1', $moduleInstance);
if ($field68) {
    echo "<li>The tax1 field already exists</li><br>";
} else {
    $field68 = new Vtiger_Field();
    $field68->label = 'LBL_QUOTES_TAX1';
    $field68->name = 'tax1';
    $field68->table = 'vtiger_inventoryproductrel';
    $field68->column = 'tax1';
    $field68->uitype = 83;
    $field68->typeofdata = 'V~O';
    $field68->displaytype = 5;

    $blockInstance9->addField($field68);
}

//vtiger quotes core field
$field71 = Vtiger_Field::getInstance('comment', $moduleInstance);
if ($field71) {
    echo "<li>The comment field already exists</li><br>";
} else {
    $field71 = new Vtiger_Field();
    $field71->label = 'LBL_QUOTES_ITEMCOMMENT';
    $field71->name = 'comment';
    $field71->table = 'vtiger_inventoryproductrel';
    $field71->column = 'comment';
    $field71->uitype = 19;
    $field71->typeofdata = 'V~O';
    $field71->displaytype = 5;

    $blockInstance9->addField($field71);
}
//vtiger quote core field
$field72 = Vtiger_Field::getInstance('productid', $moduleInstance);
if ($field72) {
    echo "<li>The productid field already exists</li><br>";
} else {
    $field72 = new Vtiger_Field();
    $field72->label = 'LBL_QUOTES_PRODUCTID';
    $field72->name = 'productid';
    $field72->table = 'vtiger_inventoryproductrel';
    $field72->column = 'productid';
    $field72->uitype = 10;
    $field72->typeofdata = 'V~O';//Change to Optional to allow for creating estimates through WS
$field72->displaytype = 5;

    $blockInstance9->addField($field72);
}
//vtiger quotes core field
$field73 = Vtiger_Field::getInstance('listprice', $moduleInstance);
if ($field73) {
    echo "<li>The listprice field already exists</li><br>";
} else {
    $field73 = new Vtiger_Field();
    $field73->label = 'LBL_QUOTES_LISTPRICE';
    $field73->name = 'listprice';
    $field73->table = 'vtiger_inventoryproductrel';
    $field73->column = 'listprice';
    $field73->uitype = 71;
    $field73->typeofdata = 'N~O';
    $field73->displaytype = 5;

    $blockInstance9->addField($field73);
}
//vtiger quotes core field
$field74 = Vtiger_Field::getInstance('quantity', $moduleInstance);
if ($field74) {
    echo "<li>The quantity field already exists</li><br>";
} else {
    $field74 = new Vtiger_Field();
    $field74->label = 'LBL_QUOTES_QUANTITY';
    $field74->name = 'quantity';
    $field74->table = 'vtiger_inventoryproductrel';
    $field74->column = 'quantity';
    $field74->uitype = 7;
    $field74->typeofdata = 'N~O';
    $field74->displaytype = 5;

    $blockInstance9->addField($field74);
}
//vtiger quotes discount percent
$field75 = Vtiger_Field::getInstance('discount_percent', $moduleInstance);
if ($field75) {
    echo "<li>The discount_percent field already exists</li><br>";
} else {
    $field75 = new Vtiger_Field();
    $field75->label = 'LBL_QUOTES_ITEMDISCOUNTPERCENT';
    $field75->name = 'discount_percent';
    $field75->table = 'vtiger_inventoryproductrel';
    $field75->column = 'discount_percent';
    $field75->uitype = 7;
    $field75->typeofdata = 'V~O';
    $field75->displaytype = 3;

    $blockInstance9->addField($field75);
}

//vtiger quotes core field
$field138 = Vtiger_Field::getInstance('discount_percent', $moduleInstance);
if ($field138) {
    echo "<li>The discount_percent field already exists</li><br>";
} else {
    $field138 = new Vtiger_Field();
    $field138->label = 'LBL_QUOTES_DISCOUNT';
    $field138->name = 'discount_amount';
    $field138->table = 'vtiger_inventoryproductrel';
    $field138->column = 'discount_amount';
    $field138->uitype = 71;
    $field138->typeofdata = 'N~O';
    $field138->displaytype = 5;

    $blockInstance9->addField($field138);
}


//add fields for blockInstance10

$field77 = Vtiger_Field::getInstance('sit_origin_date_in', $moduleInstance);
if ($field77) {
    echo "<li>The sit_origin_date_in field already exists</li><br>";
} else {
    $field77 = new Vtiger_Field();
    $field77->label = 'LBL_QUOTES_SITORIGINDATEIN';
    $field77->name = 'sit_origin_date_in';
    $field77->table = 'vtiger_quotes';
    $field77->column = 'sit_origin_date_in';
    $field77->columntype = 'DATE';
    $field77->uitype = 5;
    $field77->typeofdata = 'D~O';
    $field77->displaytype = 1;

    $blockInstance10->addField($field77);
}

$field78 = Vtiger_Field::getInstance('sit_dest_date_in', $moduleInstance);
if ($field78) {
    echo "<li>The sit_dest_date_in field already exists</li><br>";
} else {
    $field78 = new Vtiger_Field();
    $field78->label = 'LBL_QUOTES_SITDESTINATIONDATEIN';
    $field78->name = 'sit_dest_date_in';
    $field78->table = 'vtiger_quotes';
    $field78->column = 'sit_dest_date_in';
    $field78->columntype = 'DATE';
    $field78->uitype = 5;
    $field78->typeofdata = 'D~O';
    $field78->displaytype = 1;

    $blockInstance10->addField($field78);
}

$field79 = Vtiger_Field::getInstance('sit_origin_pickup_date', $moduleInstance);
if ($field79) {
    echo "<li>The sit_origin_pickup_date field already exists</li><br>";
} else {
    $field79 = new Vtiger_Field();
    $field79->label = 'LBL_QUOTES_SITORIGINPICKUPDATE';
    $field79->name = 'sit_origin_pickup_date';
    $field79->table = 'vtiger_quotes';
    $field79->column = 'sit_origin_pickup_date';
    $field79->columntype = 'DATE';
    $field79->uitype = 5;
    $field79->typeofdata = 'D~O';
    $field79->displaytype = 1;

    $blockInstance10->addField($field79);
}

$field80 = Vtiger_Field::getInstance('sit_dest_delivery_date', $moduleInstance);
if ($field80) {
    echo "<li>The sit_dest_delivery_date field already exists</li><br>";
} else {
    $field80 = new Vtiger_Field();
    $field80->label = 'LBL_QUOTES_SITDELIVERYDATE';
    $field80->name = 'sit_dest_delivery_date';
    $field80->table = 'vtiger_quotes';
    $field80->column = 'sit_dest_delivery_date';
    $field80->columntype = 'DATE';
    $field80->uitype = 5;
    $field80->typeofdata = 'D~O';
    $field80->displaytype = 1;

    $blockInstance10->addField($field80);
}

$field81 = Vtiger_Field::getInstance('sit_origin_weight', $moduleInstance);
if ($field81) {
    echo "<li>The sit_origin_weight field already exists</li><br>";
} else {
    $field81 = new Vtiger_Field();
    $field81->label = 'LBL_QUOTES_SITORIGINWEIGHT';
    $field81->name = 'sit_origin_weight';
    $field81->table = 'vtiger_quotes';
    $field81->column = 'sit_origin_weight';
    $field81->columntype = 'INT(10)';
    $field81->uitype = 7;
    $field81->typeofdata = 'I~O';
    $field81->displaytype = 1;

    $blockInstance10->addField($field81);
}

$field82 = Vtiger_Field::getInstance('sit_dest_weight', $moduleInstance);
if ($field82) {
    echo "<li>The sit_dest_weight field already exists</li><br>";
} else {
    $field82 = new Vtiger_Field();
    $field82->label = 'LBL_QUOTES_SITDESTINATIONWEIGHT';
    $field82->name = 'sit_dest_weight';
    $field82->table = 'vtiger_quotes';
    $field82->column = 'sit_dest_weight';
    $field82->columntype = 'INT(10)';
    $field82->uitype = 7;
    $field82->typeofdata = 'I~O';
    $field82->displaytype = 1;

    $blockInstance10->addField($field82);
}

$field83 = Vtiger_Field::getInstance('sit_origin_zip', $moduleInstance);
if ($field83) {
    echo "<li>The sit_origin_zip field already exists</li><br>";
} else {
    $field83 = new Vtiger_Field();
    $field83->label = 'LBL_QUOTES_SITORIGINZIP';
    $field83->name = 'sit_origin_zip';
    $field83->table = 'vtiger_quotes';
    $field83->column = 'sit_origin_zip';
    $field83->columntype = 'INT(10)';
    $field83->uitype = 7;
    $field83->typeofdata = 'I~O';
    $field83->displaytype = 1;

    $blockInstance10->addField($field83);
}

$field84 = Vtiger_Field::getInstance('sit_dest_zip', $moduleInstance);
if ($field84) {
    echo "<li>The sit_dest_zip field already exists</li><br>";
} else {
    $field84 = new Vtiger_Field();
    $field84->label = 'LBL_QUOTES_SITDESTINATIONZIP';
    $field84->name = 'sit_dest_zip';
    $field84->table = 'vtiger_quotes';
    $field84->column = 'sit_dest_zip';
    $field84->columntype = 'INT(10)';
    $field84->uitype = 7;
    $field84->typeofdata = 'I~O';
    $field84->displaytype = 1;

    $blockInstance10->addField($field84);
}

$field85 = Vtiger_Field::getInstance('sit_origin_miles', $moduleInstance);
if ($field85) {
    echo "<li>The sit_origin_miles field already exists</li><br>";
} else {
    $field85 = new Vtiger_Field();
    $field85->label = 'LBL_QUOTES_SITORIGINMILES';
    $field85->name = 'sit_origin_miles';
    $field85->table = 'vtiger_quotes';
    $field85->column = 'sit_origin_miles';
    $field85->columntype = 'INT(10)';
    $field85->uitype = 7;
    $field85->typeofdata = 'I~O';
    $field85->displaytype = 1;

    $blockInstance10->addField($field85);
}

$field86 = Vtiger_Field::getInstance('sit_dest_miles', $moduleInstance);
if ($field86) {
    echo "<li>The sit_dest_miles field already exists</li><br>";
} else {
    $field86 = new Vtiger_Field();
    $field86->label = 'LBL_QUOTES_SITDESTINATIONMILES';
    $field86->name = 'sit_dest_miles';
    $field86->table = 'vtiger_quotes';
    $field86->column = 'sit_dest_miles';
    $field86->columntype = 'INT(10)';
    $field86->uitype = 7;
    $field86->typeofdata = 'I~O';
    $field86->displaytype = 1;

    $blockInstance10->addField($field86);
}

$field87 = Vtiger_Field::getInstance('sit_origin_number_days', $moduleInstance);
if ($field87) {
    echo "<li>The sit_origin_number_days field already exists</li><br>";
} else {
    $field87 = new Vtiger_Field();
    $field87->label = 'LBL_QUOTES_SITORIGINNUMBERDAYS';
    $field87->name = 'sit_origin_number_days';
    $field87->table = 'vtiger_quotes';
    $field87->column = 'sit_origin_number_days';
    $field87->columntype = 'INT(10)';
    $field87->uitype = 7;
    $field87->typeofdata = 'I~O';
    $field87->displaytype = 1;

    $blockInstance10->addField($field87);
}

$field88 = Vtiger_Field::getInstance('sit_dest_number_days', $moduleInstance);
if ($field88) {
    echo "<li>The sit_dest_number_days field already exists</li><br>";
} else {
    $field88 = new Vtiger_Field();
    $field88->label = 'LBL_QUOTES_SITDESTINATIONNUMBERDAYS';
    $field88->name = 'sit_dest_number_days';
    $field88->table = 'vtiger_quotes';
    $field88->column = 'sit_dest_number_days';
    $field88->columntype = 'INT(10)';
    $field88->uitype = 7;
    $field88->typeofdata = 'I~O';
    $field88->displaytype = 1;

    $blockInstance10->addField($field88);
}

$field99 = Vtiger_Field::getInstance('sit_origin_fuel_percent', $moduleInstance);
if ($field99) {
    echo "<li>The sit_origin_fuel_percent field already exists</li><br>";
} else {
    $field99 = new Vtiger_Field();
    $field99->label = 'LBL_QUOTES_SITORIGINFUELPERCENT';
    $field99->name = 'sit_origin_fuel_percent';
    $field99->table = 'vtiger_quotes';
    $field99->column = 'sit_origin_fuel_percent';
    $field99->columntype = 'DECIMAL(10,3)';
    $field99->uitype = 7;
    $field99->typeofdata = 'N~O';
    $field99->displaytype = 1;

    $blockInstance10->addField($field99);
}

$field100 = Vtiger_Field::getInstance('sit_dest_fuel_percent', $moduleInstance);
if ($field100) {
    echo "<li>The sit_dest_fuel_percent field already exists</li><br>";
} else {
    $field100 = new Vtiger_Field();
    $field100->label = 'LBL_QUOTES_SITDESTINATIONFUELPERCENT';
    $field100->name = 'sit_dest_fuel_percent';
    $field100->table = 'vtiger_quotes';
    $field100->column = 'sit_dest_fuel_percent';
    $field100->columntype = 'DECIMAL(10,3)';
    $field100->uitype = 7;
    $field100->typeofdata = 'N~O';
    $field100->displaytype = 1;

    $blockInstance10->addField($field100);
}

$field107 = Vtiger_Field::getInstance('sit_origin_overtime', $moduleInstance);
if ($field107) {
    echo "<li>The sit_origin_overtime field already exists</li><br>";
} else {
    $field107 = new Vtiger_Field();
    $field107->label = 'LBL_QUOTES_SITORIGINOVERTIME';
    $field107->name = 'sit_origin_overtime';
    $field107->table = 'vtiger_quotes';
    $field107->column = 'sit_origin_overtime';
    $field107->uitype = 56;
    $field107->columntype = 'VARCHAR(3)';
    $field107->typeofdata = 'C~O';
    $field107->displaytype = 1;

    $blockInstance10->addField($field107);
}

$field108 = Vtiger_Field::getInstance('sit_dest_overtime', $moduleInstance);
if ($field108) {
    echo "<li>The sit_dest_overtime field already exists</li><br>";
} else {
    $field108 = new Vtiger_Field();
    $field108->label = 'LBL_QUOTES_SITDESTINATIONOVERTIME';
    $field108->name = 'sit_dest_overtime';
    $field108->table = 'vtiger_quotes';
    $field108->column = 'sit_dest_overtime';
    $field108->columntype = 'VARCHAR(3)';
    $field108->uitype = 56;
    $field108->typeofdata = 'C~O';
    $field108->displaytype = 1;

    $blockInstance10->addField($field108);
}


//add fields blockInstance11
$field109 = Vtiger_Field::getInstance('acc_shuttle_origin_weight', $moduleInstance);
if ($field109) {
    echo "<li>The acc_shuttle_origin_weight field already exists</li><br>";
} else {
    $field109 = new Vtiger_Field();
    $field109->label = 'LBL_QUOTES_ACCSHUTTLEORIGINWEIGHT';
    $field109->name = 'acc_shuttle_origin_weight';
    $field109->table = 'vtiger_quotes';
    $field109->column = 'acc_shuttle_origin_weight';
    $field109->columntype = 'INT(10)';
    $field109->uitype = 7;
    $field109->typeofdata = 'I~O';
    $field109->displaytype = 1;

    $blockInstance11->addField($field109);
}

$field110 = Vtiger_Field::getInstance('acc_shuttle_dest_weight', $moduleInstance);
if ($field110) {
    echo "<li>The acc-shuttle_dest_weight field already exists</li><br>";
} else {
    $field110 = new Vtiger_Field();
    $field110->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONWEIGHT';
    $field110->name = 'acc_shuttle_dest_weight';
    $field110->table = 'vtiger_quotes';
    $field110->column = 'acc_shuttle_dest_weight';
    $field110->columntype = 'INT(10)';
    $field110->uitype = 7;
    $field110->typeofdata = 'I~O';
    $field110->displaytype = 1;

    $blockInstance11->addField($field110);
}

$field111 = Vtiger_Field::getInstance('acc_shuttle_origin_applied', $moduleInstance);
if ($field111) {
    echo "<li>The acc_shuttle_origin_applied field already exists</li><br>";
} else {
    $field111 = new Vtiger_Field();
    $field111->label = 'LBL_QUOTES_ACCSHUTTLEORIGINAPPLIED';
    $field111->name = 'acc_shuttle_origin_applied';
    $field111->table = 'vtiger_quotes';
    $field111->column = 'acc_shuttle_origin_applied';
    $field111->columntype = 'VARCHAR(3)';
    $field111->uitype = 56;
    $field111->typeofdata = 'C~O';
    $field111->displaytype = 1;

    $blockInstance11->addField($field111);
}

$field112 = Vtiger_Field::getInstance('acc_shuttle_dest_applied', $moduleInstance);
if ($field112) {
    echo "<li>The acc_shuttle_dest_applied field already exists</li><br>";
} else {
    $field112 = new Vtiger_Field();
    $field112->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONAPPLIED';
    $field112->name = 'acc_shuttle_dest_applied';
    $field112->table = 'vtiger_quotes';
    $field112->column = 'acc_shuttle_dest_applied';
    $field112->columntype = 'VARCHAR(3)';
    $field112->uitype = 56;
    $field112->typeofdata = 'C~O';
    $field112->displaytype = 1;

    $blockInstance11->addField($field112);
}

$field113 = Vtiger_Field::getInstance('acc_shuttle_origin_ot', $moduleInstance);
if ($field113) {
    echo "<li>The acc_shuttle_origin_ot field already exists</li><br>";
} else {
    $field113 = new Vtiger_Field();
    $field113->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOT';
    $field113->name = 'acc_shuttle_origin_ot';
    $field113->table = 'vtiger_quotes';
    $field113->column = 'acc_shuttle_origin_ot';
    $field113->columntype = 'VARCHAR(3)';
    $field113->uitype = 56;
    $field113->typeofdata = 'C~O';
    $field113->displaytype = 1;

    $blockInstance11->addField($field113);
}

$field114 = Vtiger_Field::getInstance('acc_shuttle_dest_ot', $moduleInstance);
if ($field114) {
    echo "<li>The acc_shuttle_dest_ot field already exists</li><br>";
} else {
    $field114 = new Vtiger_Field();
    $field114->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOT';
    $field114->name = 'acc_shuttle_dest_ot';
    $field114->table = 'vtiger_quotes';
    $field114->column = 'acc_shuttle_dest_ot';
    $field114->columntype = 'VARCHAR(3)';
    $field114->uitype = 56;
    $field114->typeofdata = 'C~O';
    $field114->displaytype = 1;

    $blockInstance11->addField($field114);
}

$field115 = Vtiger_Field::getInstance('acc_shuttle_origin_over25', $moduleInstance);
if ($field115) {
    echo "<li>The acc_shuttle_origin_over25 field already exists</li><br>";
} else {
    $field115 = new Vtiger_Field();
    $field115->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOVER25';
    $field115->name = 'acc_shuttle_origin_over25';
    $field115->table = 'vtiger_quotes';
    $field115->column = 'acc_shuttle_origin_over25';
    $field115->columntype = 'VARCHAR(3)';
    $field115->uitype = 56;
    $field115->typeofdata = 'C~O';
    $field115->displaytype = 1;

    $blockInstance11->addField($field115);
}

$field116 = Vtiger_Field::getInstance('acc_shuttle_dest_over25', $moduleInstance);
if ($field116) {
    echo "<li>The acc_shuttle_dest_over25 field already exists</li><br>";
} else {
    $field116 = new Vtiger_Field();
    $field116->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOVER25';
    $field116->name = 'acc_shuttle_dest_over25';
    $field116->table = 'vtiger_quotes';
    $field116->column = 'acc_shuttle_dest_over25';
    $field116->columntype = 'VARCHAR(3)';
    $field116->uitype = 56;
    $field116->typeofdata = 'C~O';
    $field116->displaytype = 1;

    $blockInstance11->addField($field116);
}

$field117 = Vtiger_Field::getInstance('acc_shuttle_origin_miles', $moduleInstance);
if ($field117) {
    echo "<li>The acc_shuttle_origin_miles field already exists</li><br>";
} else {
    $field117 = new Vtiger_Field();
    $field117->label = 'LBL_QUOTES_ACCSHUTTLEORIGINMILES';
    $field117->name = 'acc_shuttle_origin_miles';
    $field117->table = 'vtiger_quotes';
    $field117->column = 'acc_shuttle_origin_miles';
    $field117->columntype = 'INT(10)';
    $field117->uitype = 7;
    $field117->typeofdata = 'I~O';
    $field117->displaytype = 1;

    $blockInstance11->addField($field117);
}

$field118 = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $moduleInstance);
if ($field118) {
    echo "<li>The acc_shuttle_dest_miles field already exists</li><br>";
} else {
    $field118 = new Vtiger_Field();
    $field118->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONMILES';
    $field118->name = 'acc_shuttle_dest_miles';
    $field118->table = 'vtiger_quotes';
    $field118->column = 'acc_shuttle_dest_miles';
    $field118->columntype = 'INT(10)';
    $field118->uitype = 7;
    $field118->typeofdata = 'I~O';
    $field118->displaytype = 1;

    $blockInstance11->addField($field118);
}

$field119 = Vtiger_Field::getInstance('acc_ot_origin_weight', $moduleInstance);
if ($field119) {
    echo "<li>The acc_ot_origin_weight field already exists</li><br>";
} else {
    $field119 = new Vtiger_Field();
    $field119->label = 'LBL_QUOTES_ACCOTORIGINWEIGHT';
    $field119->name = 'acc_ot_origin_weight';
    $field119->table = 'vtiger_quotes';
    $field119->column = 'acc_ot_origin_weight';
    $field119->columntype = 'INT(10)';
    $field119->uitype = 7;
    $field119->typeofdata = 'I~O';
    $field119->displaytype = 1;

    $blockInstance11->addField($field119);
}

$field120 = Vtiger_Field::getInstance('acc_ot_dest_weight', $moduleInstance);
if ($field120) {
    echo "<li>The acc_ot_dest_weight field already exists</li><br>";
} else {
    $field120 = new Vtiger_Field();
    $field120->label = 'LBL_QUOTES_ACCOTDESTINATIONWEIGHT';
    $field120->name = 'acc_ot_dest_weight';
    $field120->table = 'vtiger_quotes';
    $field120->column = 'acc_ot_dest_weight';
    $field120->columntype = 'INT(10)';
    $field120->uitype = 7;
    $field120->typeofdata = 'I~O';
    $field120->displaytype = 1;

    $blockInstance11->addField($field120);
}

$field121 = Vtiger_Field::getInstance('acc_ot_origin_applied', $moduleInstance);
if ($field121) {
    echo "<li>The acc_ot_origin_applied field already exists</li><br>";
} else {
    $field121 = new Vtiger_Field();
    $field121->label = 'LBL_QUOTES_ACCOTORIGINAPPLIED';
    $field121->name = 'acc_ot_origin_applied';
    $field121->table = 'vtiger_quotes';
    $field121->column = 'acc_ot_origin_applied';
    $field121->columntype = 'VARCHAR(3)';
    $field121->uitype = 56;
    $field121->typeofdata = 'C~O';
    $field121->displaytype = 1;

    $blockInstance11->addField($field121);
}

$field122 = Vtiger_Field::getInstance('acc_ot_dest_applied', $moduleInstance);
if ($field122) {
    echo "<li>The acc_ot_dest_applied field already exists</li><br>";
} else {
    $field122 = new Vtiger_Field();
    $field122->label = 'LBL_QUOTES_ACCOTDESTINATIONAPPLIED';
    $field122->name = 'acc_ot_dest_applied';
    $field122->table = 'vtiger_quotes';
    $field122->column = 'acc_ot_dest_applied';
    $field122->columntype = 'VARCHAR(3)';
    $field122->uitype = 56;
    $field122->typeofdata = 'C~O';
    $field122->displaytype = 1;

    $blockInstance11->addField($field122);
}

$field123 = Vtiger_Field::getInstance('acc_selfstg_origin_weight', $moduleInstance);
if ($field123) {
    echo "<li>The acc_selfstg_origin_weight field already exists</li><br>";
} else {
    $field123 = new Vtiger_Field();
    $field123->label = 'LBL_QUOTES_ACCSELFSTGORIGINWEIGHT';
    $field123->name = 'acc_selfstg_origin_weight';
    $field123->table = 'vtiger_quotes';
    $field123->column = 'acc_selfstg_origin_weight';
    $field123->columntype = 'INT(10)';
    $field123->uitype = 7;
    $field123->typeofdata = 'I~O';
    $field123->displaytype = 1;

    $blockInstance11->addField($field123);
}

$field124 = Vtiger_Field::getInstance('acc_selfstg_dest_weight', $moduleInstance);
if ($field124) {
    echo "<li>The acc_selfstg_dest_weight field already exists</li><br>";
} else {
    $field124 = new Vtiger_Field();
    $field124->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONWEIGHT';
    $field124->name = 'acc_selfstg_dest_weight';
    $field124->table = 'vtiger_quotes';
    $field124->column = 'acc_selfstg_dest_weight';
    $field124->columntype = 'INT(10)';
    $field124->uitype = 7;
    $field124->typeofdata = 'I~O';
    $field124->displaytype = 1;

    $blockInstance11->addField($field124);
}

$field125 = Vtiger_Field::getInstance('acc_selfstg_origin_applied', $moduleInstance);
if ($field125) {
    echo "<li>The acc_selfstg_origin_applied field already exists</li><br>";
} else {
    $field125 = new Vtiger_Field();
    $field125->label = 'LBL_QUOTES_ACCSELFSTGORIGINAPPLIED';
    $field125->name = 'acc_selfstg_origin_applied';
    $field125->table = 'vtiger_quotes';
    $field125->column = 'acc_selfstg_origin_applied';
    $field125->columntype = 'VARCHAR(3)';
    $field125->uitype = 56;
    $field125->typeofdata = 'C~O';
    $field125->displaytype = 1;

    $blockInstance11->addField($field125);
}

$field126 = Vtiger_Field::getInstance('acc_selfstg_dest_applied', $moduleInstance);
if ($field126) {
    echo "<li>The acc_selfstg_dest_applied field already exists</li><br>";
} else {
    $field126 = new Vtiger_Field();
    $field126->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONAPPLIED';
    $field126->name = 'acc_selfstg_dest_applied';
    $field126->table = 'vtiger_quotes';
    $field126->column = 'acc_selfstg_dest_applied';
    $field126->columntype = 'VARCHAR(3)';
    $field126->uitype = 56;
    $field126->typeofdata = 'C~O';
    $field126->displaytype = 1;

    $blockInstance11->addField($field126);
}

$field127 = Vtiger_Field::getInstance('acc_selfstg_origin_ot', $moduleInstance);
if ($field127) {
    echo "<li>The acc_selfstg_origin_ot field already exists</li><br>";
} else {
    $field127 = new Vtiger_Field();
    $field127->label = 'LBL_QUOTES_ACCSELFSTGORIGINOT';
    $field127->name = 'acc_selfstg_origin_ot';
    $field127->table = 'vtiger_quotes';
    $field127->column = 'acc_selfstg_origin_ot';
    $field127->columntype = 'VARCHAR(3)';
    $field127->uitype = 56;
    $field127->typeofdata = 'C~O';
    $field127->displaytype = 1;

    $blockInstance11->addField($field127);
}

$field128 = Vtiger_Field::getInstance('acc_selfstg_dest_ot', $moduleInstance);
if ($field128) {
    echo "<li>The acc_selfstg_dest_ot field already exists</li><br>";
} else {
    $field128 = new Vtiger_Field();
    $field128->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONOT';
    $field128->name = 'acc_selfstg_dest_ot';
    $field128->table = 'vtiger_quotes';
    $field128->column = 'acc_selfstg_dest_ot';
    $field128->columntype = 'VARCHAR(3)';
    $field128->uitype = 56;
    $field128->typeofdata = 'C~O';
    $field128->displaytype = 1;

    $blockInstance11->addField($field128);
}

$field129 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $moduleInstance);
if ($field129) {
    echo "<li>The acc_exlabor_origin_hours field already exists</li><br>";
} else {
    $field129 = new Vtiger_Field();
    $field129->label = 'LBL_QUOTES_ACCEXLABORORIGINHOURS';
    $field129->name = 'acc_exlabor_origin_hours';
    $field129->table = 'vtiger_quotes';
    $field129->column = 'acc_exlabor_origin_hours';
    $field129->columntype = 'INT(5)';
    $field129->uitype = 7;
    $field129->typeofdata = 'I~O';
    $field129->displaytype = 1;

    $blockInstance11->addField($field129);
}

$field130 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $moduleInstance);
if ($field130) {
    echo "<li>The acc_exlabor_dest_hours field already exists</li><br>";
} else {
    $field130 = new Vtiger_Field();
    $field130->label = 'LBL_QUOTES_ACCEXLABORDESTINATIONHOURS';
    $field130->name = 'acc_exlabor_dest_hours';
    $field130->table = 'vtiger_quotes';
    $field130->column = 'acc_exlabor_dest_hours';
    $field130->columntype = 'INT(5)';
    $field130->uitype = 7;
    $field130->typeofdata = 'I~O';
    $field130->displaytype = 1;

    $blockInstance11->addField($field130);
}

$field131 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $moduleInstance);
if ($field131) {
    echo "<li>The acc_exlabor_ot_origin_hours field already exists</li><br>";
} else {
    $field131 = new Vtiger_Field();
    $field131->label = 'LBL_QUOTES_ACCEXLABOROTORIGINHOURS';
    $field131->name = 'acc_exlabor_ot_origin_hours';
    $field131->table = 'vtiger_quotes';
    $field131->column = 'acc_exlabor_ot_origin_hours';
    $field131->columntype = 'INT(5)';
    $field131->uitype = 7;
    $field131->typeofdata = 'I~O';
    $field131->displaytype = 1;

    $blockInstance11->addField($field131);
}

$field131 = Vtiger_Field::getInstance('acc_exlabor_ot_dest_hours', $moduleInstance);
if ($field131) {
    echo "<li>The acc_exlabor_ot_dest_hours field already exists</li><br>";
} else {
    $field132 = new Vtiger_Field();
    $field132->label = 'LBL_QUOTES_ACCEXLABOROTDESTINATIONHOURS';
    $field132->name = 'acc_exlabor_ot_dest_hours';
    $field132->table = 'vtiger_quotes';
    $field132->column = 'acc_exlabor_ot_dest_hours';
    $field132->columntype = 'INT(5)';
    $field132->uitype = 7;
    $field132->typeofdata = 'I~O';
    $field132->displaytype = 1;

    $blockInstance11->addField($field132);
}

$field133 = Vtiger_Field::getInstance('acc_wait_origin_hours', $moduleInstance);
if ($field133) {
    echo "<li>The acc_wait_origin_hours field already exists</li><br>";
} else {
    $field133 = new Vtiger_Field();
    $field133->label = 'LBL_QUOTES_ACCWAITORIGINHOURS';
    $field133->name = 'acc_wait_origin_hours';
    $field133->table = 'vtiger_quotes';
    $field133->column = 'acc_wait_origin_hours';
    $field133->columntype = 'INT(5)';
    $field133->uitype = 7;
    $field133->typeofdata = 'I~O';
    $field133->displaytype = 1;

    $blockInstance11->addField($field133);
}

$field134 = Vtiger_Field::getInstance('acc_wait_dest_hours', $moduleInstance);
if ($field134) {
    echo "<li>The acc_wait_dest_hours field already exists</li><br>";
} else {
    $field134 = new Vtiger_Field();
    $field134->label = 'LBL_QUOTES_ACCWAITDESTINATIONHOURS';
    $field134->name = 'acc_wait_dest_hours';
    $field134->table = 'vtiger_quotes';
    $field134->column = 'acc_wait_dest_hours';
    $field134->columntype = 'INT(5)';
    $field134->uitype = 7;
    $field134->typeofdata = 'I~O';
    $field134->displaytype = 1;

    $blockInstance11->addField($field134);
}

$field135 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $moduleInstance);
if ($field135) {
    echo "<li>The acc_wait_ot_origin_hours field already exists</li><br>";
} else {
    $field135 = new Vtiger_Field();
    $field135->label = 'LBL_QUOTES_ACCWAITOTORIGINHOURS';
    $field135->name = 'acc_wait_ot_origin_hours';
    $field135->table = 'vtiger_quotes';
    $field135->column = 'acc_wait_ot_origin_hours';
    $field135->columntype = 'INT(5)';
    $field135->uitype = 7;
    $field135->typeofdata = 'I~O';
    $field135->displaytype = 1;

    $blockInstance11->addField($field135);
}

$field136 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $moduleInstance);
if ($field136) {
    echo "<li>The acc_wait_ot_dest_hours field already exists</li><br>";
} else {
    $field136 = new Vtiger_Field();
    $field136->label = 'LBL_QUOTES_ACCWAITOTDESTINATIONHOURS';
    $field136->name = 'acc_wait_ot_dest_hours';
    $field136->table = 'vtiger_quotes';
    $field136->column = 'acc_wait_ot_dest_hours';
    $field136->columntype = 'INT(5)';
    $field136->uitype = 7;
    $field136->typeofdata = 'I~O';
    $field136->displaytype = 1;

    $blockInstance11->addField($field136);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field4, 1)->addField($field2, 2)->addField($field8, 3)->addField($field66, 4)->addField($field9, 5);
      
    //$moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();

    // Adds the Updates link to the vertical navigation menu on the right.
    ModTracker::enableTrackingForModule($moduleInstance->id);

    $moduleInstance->setRelatedList(Vtiger_Module::getInstance('Calendar'), 'Activities', array('add'), 'get_activities');
    $moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('add', 'select'), 'get_attachments');
}

if (!Vtiger_Utils::CheckTable('vtiger_bulky_items')) {
    echo "<li>creating vtiger_bulky_items </li><br>";
    Vtiger_Utils::CreateTable('vtiger_bulky_items',
                              '(quoteid INT(10),
                                bulkyid INT(10),
                                ship_qty INT(10)
                               )', true);
}

if (!Vtiger_Utils::CheckTable('vtiger_packing_items')) {
    echo "<li>creating vtiger_packing_items </li><br>";
    Vtiger_Utils::CreateTable('vtiger_packing_items',
                              '(quoteid INT(10),
                                itemid INT(10),
                                pack_qty INT(10),
                                unpack_qty INT(10),
                                ot_pack_qty INT(10),
                                ot_unpack_qty INT(10)
                               )', true);
}

if (!Vtiger_Utils::CheckTable('vtiger_crates')) {
    echo "<li>creating vtiger_crates </li><br>";
    Vtiger_Utils::CreateTable('vtiger_crates',
                              '(quoteid INT(10),
                              	crateid VARCHAR(10),
                                description TEXT,
                                length INT(10),
                                width INT(10),
                                height INT(10),
                                pack VARCHAR(3),
                                unpack VARCHAR(3),
                                ot_pack VARCHAR(3),
                                ot_unpack VARCHAR(3),
                                discount DECIMAL(5,3),
                                cube INT(10),
                                line_item_id INT(10)
                               )', true);
}

if (!Vtiger_Utils::CheckTable('vtiger_crates_seq')) {
    echo "<li>creating vtiger_crates_seq </li><br>";
    Vtiger_Utils::CreateTable('vtiger_crates_seq',
                              '(id INT(11)
                                )', true);
}

Vtiger_Utils::ExecuteQuery('REPLACE INTO `vtiger_crates_seq` SET id = 0');
//file_put_contents('logs/devLog.log', "\n moduleInstance : ".print_r($moduleInstance,true), FILE_APPEND);
;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";