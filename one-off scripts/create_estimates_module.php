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


/**
 * @param The parameters are just pointers to the data for the quotes module
 * @return SQL -> CSV -> convert/ -> create_estimates.php -> Estimates module
 * @author Louis Robinson
 * @file create_estimate.php
 * @info built programatically with fgetcsv() and foreach()
 * @description take all custom functionality out of core vtiger quotes module
 * 				and put it into Estimates.
 *
 *  SELECT *
 *	FROM  `vtiger_field`
 *	JOIN  `vtiger_blocks` ON block = blockid
 *	WHERE `vtiger_field`.tabid =20
 */

// vtiger_entity_name

//ini_set('error_reporting', E_ALL);

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');

$moduleInstance = new Vtiger_Module();
$moduleInstance->name = 'Estimates';
$moduleInstance->save();

//$moduleInstance->initTables();


$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_QUOTE_INFORMATION';
$moduleInstance->addBlock($blockInstance);

$blockInstance2 = new Vtiger_Block();
$blockInstance2->label = 'LBL_QUOTES_CONTACTDETAILS';
$moduleInstance->addBlock($blockInstance2);

$blockInstance3 = new Vtiger_Block();
$blockInstance3->label = 'LBL_ADDRESS_INFORMATION';
$moduleInstance->addBlock($blockInstance3);

// #4
$blockInstance4 = new Vtiger_Block();
$blockInstance4->label = 'LBL_QUOTES_LOCALMOVEDETAILS';
$moduleInstance->addBlock($blockInstance4);

$blockInstance5 = new Vtiger_Block();
$blockInstance5->label = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
$moduleInstance->addBlock($blockInstance5);

// #6
$blockInstance6 = new Vtiger_Block();
$blockInstance6->label = 'LBL_QUOTES_COMMERCIALMOVEDETAILS';
$moduleInstance->addBlock($blockInstance6);

$blockInstance7 = new Vtiger_Block();
$blockInstance7->label = 'LBL_TERMS_INFORMATION';
$moduleInstance->addBlock($blockInstance7);

$blockInstance8 = new Vtiger_Block();
$blockInstance8->label = 'LBL_DESCRIPTION_INFORMATION';
$moduleInstance->addBlock($blockInstance8);

$blockInstance9 = new Vtiger_Block();
$blockInstance9->label = 'LBL_ITEM_DETAILS';
$moduleInstance->addBlock($blockInstance9);

$blockInstance10 = new Vtiger_Block();
$blockInstance10->label = 'LBL_QUOTES_SITDETAILS';
$moduleInstance->addBlock($blockInstance10);

$blockInstance11 = new Vtiger_Block();
$blockInstance11->label = 'LBL_QUOTES_ACCESSORIALDETAILS';
$moduleInstance->addBlock($blockInstance11);

/**
 * @param null
 * @return data for module from quotes
 */
$field1 = new Vtiger_Field();
$field1->label = 'LBL_QUOTES_SUBJECT';
$field1->name = 'subject';
$field1->table = 'vtiger_quotes';
$field1->column = 'subject';
$field1->uitype = 2;
$field1->typeofdata = 'V~M';
$field1->displaytype = 1;
$field1->quickcreate = 0;
$field1->defaultvalue = 'Estimate';

$blockInstance->addField($field1);

$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_QUOTES_POTENTIALNAME';
$field2->name = 'potential_id';
$field2->table = 'vtiger_quotes';
$field2->column = 'potentialid';
$field2->uitype = 76;
$field2->typeofdata = 'I~O';
$field2->displaytype = 1;

$blockInstance->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_QUOTES_QUOTENUMBER';
$field3->name = 'quote_no';
$field3->table = 'vtiger_quotes';
$field3->column = 'quote_no';
$field3->uitype = 4;
$field3->typeofdata = 'V~M';
$field3->displaytype = 1;

$blockInstance->addField($field3);

$entity = new CRMEntity();
$entity->setModuleSeqNumber('configure', $moduleInstance->name, 'EST', 1);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_QUOTES_QUOTESTAGE';
$field4->name = 'quotestage';
$field4->table = 'vtiger_quotes';
$field4->column = 'quotestage';
$field4->uitype = 15;
$field4->typeofdata = 'V~M';
$field4->displaytype = 1;
$field4->quickcreate = 0;
$field4->defaultvalue = 'Created';

$blockInstance->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_QUOTES_VALIDUTILL';
$field5->name = 'validtill';
$field5->table = 'vtiger_quotes';
$field5->column = 'validtill';
$field5->uitype = 5;
$field5->typeofdata = 'D~O';
$field5->displaytype = 1;

$blockInstance->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_QUOTES_CONTACTNAME';
$field6->name = 'contact_id';
$field6->table = 'vtiger_quotes';
$field6->column = 'contactid';
$field6->uitype = 57;
$field6->typeofdata = 'V~O';
$field6->displaytype = 1;

$blockInstance->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_QUOTES_SHIPPING';
$field7->name = 'shipping';
$field7->table = 'vtiger_quotes';
$field7->column = 'shipping';
$field7->uitype = 1;
$field7->typeofdata = 'V~O';
$field7->displaytype = 1;

$blockInstance->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_QUOTES_ACCOUNTNAME';
$field8->name = 'account_id';
$field8->table = 'vtiger_quotes';
$field8->column = 'accountid';
$field8->uitype = 73;
$field8->typeofdata = 'I~O';
$field8->displaytype = 1;

$blockInstance->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_QUOTES_ASSIGNEDTO';
$field9->name = 'assigned_user_id';
$field9->table = 'vtiger_crmentity';
$field9->column = 'smownerid';
$field9->uitype = 53;
$field9->typeofdata = 'V~M';
$field9->displaytype = 1;
$field9->quickcreate = 0;

$blockInstance->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_QUOTES_CREATEDTIME';
$field10->name = 'createdtime';
$field10->table = 'vtiger_crmentity';
$field10->column = 'createdtime';
$field10->uitype = 70;
$field10->typeofdata = 'DT~O';
$field10->displaytype = 2;

$blockInstance->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_QUOTES_MODIFIEDTIME';
$field11->name = 'modifiedtime';
$field11->table = 'vtiger_crmentity';
$field11->column = 'modifiedtime';
$field11->uitype = 70;
$field11->typeofdata = 'DT~O';
$field11->displaytype = 2;

$blockInstance->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_QUOTES_BUSINESSLINE';
$field12->name = 'business_line';
$field12->table = 'vtiger_quotescf';
$field12->column = 'business_line';
$field12->uitype = 16;
$field12->typeofdata = 'V~O';
$field12->displaytype = 1;

$blockInstance->addField($field12);

$field13 = new Vtiger_Field();
$field13->label = 'LBL_QUOTES_PROJECTID';
$field13->name = 'project_id';
$field13->table = 'vtiger_quotes';
$field13->column = 'project_id';
$field13->uitype = 10;
$field13->typeofdata = 'V~O';
$field13->displaytype = 1;

$blockInstance->addField($field13);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_QUOTES_ISPRIMARY';
$field14->name = 'is_primary';
$field14->table = 'vtiger_quotes';
$field14->column = 'is_primary';
$field14->uitype = 56;
$field14->typeofdata = 'C~O';
$field14->displaytype = 1;

$blockInstance->addField($field14);

$field15 = new Vtiger_Field();
$field15->label = 'LBL_QUOTES_BILLINGADDRESS';
$field15->name = 'bill_street';
$field15->table = 'vtiger_quotesbillads';
$field15->column = 'bill_street';
$field15->uitype = 24;
$field15->typeofdata = 'V~O';
$field15->displaytype = 1;

$blockInstance2->addField($field15);

$field16 = new Vtiger_Field();
$field16->label = 'LBL_QUOTES_BILLINGCITY';
$field16->name = 'bill_city';
$field16->table = 'vtiger_quotesbillads';
$field16->column = 'bill_city';
$field16->uitype = 1;
$field16->typeofdata = 'V~O';
$field16->displaytype = 1;

$blockInstance2->addField($field16);

$field17 = new Vtiger_Field();
$field17->label = 'LBL_QUOTES_BILLINGSTATE';
$field17->name = 'bill_state';
$field17->table = 'vtiger_quotesbillads';
$field17->column = 'bill_state';
$field17->uitype = 1;
$field17->typeofdata = 'V~O';
$field17->displaytype = 1;

$blockInstance2->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_QUOTES_BILLINGZIPCODE';
$field18->name = 'bill_code';
$field18->table = 'vtiger_quotesbillads';
$field18->column = 'bill_code';
$field18->uitype = 1;
$field18->typeofdata = 'V~O';
$field18->displaytype = 1;

$blockInstance2->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_QUOTES_BILLINGPOBOX';
$field19->name = 'bill_pobox';
$field19->table = 'vtiger_quotesbillads';
$field19->column = 'bill_pobox';
$field19->uitype = 1;
$field19->typeofdata = 'V~O';
$field19->displaytype = 1;

$blockInstance2->addField($field19);

// from 32
$field20 = new Vtiger_Field();
$field20->label = 'LBL_QUOTES_BILLINGCOUNTRY';
$field20->name = 'bill_country';
$field20->table = 'vtiger_quotesbillads';
$field20->column = 'bill_country';
$field20->uitype = 1;
$field20->typeofdata = 'V~O';
$field20->displaytype = 1;

$blockInstance2->addField($field20);

// from 49
$field21 = new Vtiger_Field();
$field21->label = 'LBL_QUOTES_ORIGINADDRESS1';
$field21->name = 'origin_address1';
$field21->table = 'vtiger_quotescf';
$field21->column = 'origin_address1';
$field21->uitype = 1;
$field21->typeofdata = 'V~O~LE~50';
$field21->displaytype = 1;

$blockInstance3->addField($field21);

$field22 = new Vtiger_Field();
$field22->label = 'LBL_QUOTES_DESTINATIONADDRESS1';
$field22->name = 'destination_address1';
$field22->table = 'vtiger_quotescf';
$field22->column = 'destination_address1';
$field22->uitype = 1;
$field22->typeofdata = 'V~O~LE~50';
$field22->displaytype = 1;

$blockInstance3->addField($field22);

// from 51
$field23 = new Vtiger_Field();
$field23->label = 'LBL_QUOTES_ORIGINADDRESS2';
$field23->name = 'origin_address2';
$field23->table = 'vtiger_quotescf';
$field23->column = 'origin_address2';
$field23->uitype = 1;
$field23->typeofdata = 'V~O~LE~50';
$field23->displaytype = 1;

$blockInstance3->addField($field23);

$field24 = new Vtiger_Field();
$field24->label = 'LBL_QUOTES_DESTINATIONADDRESS2';
$field24->name = 'destination_address2';
$field24->table = 'vtiger_quotescf';
$field24->column = 'destination_address2';
$field24->uitype = 1;
$field24->typeofdata = 'V~O~LE~50';
$field24->displaytype = 1;

$blockInstance3->addField($field24);

$field25 = new Vtiger_Field();
$field25->label = 'LBL_QUOTES_ORIGINCITY';
$field25->name = 'origin_city';
$field25->table = 'vtiger_quotescf';
$field25->column = 'origin_city';
$field25->uitype = 1;
$field25->typeofdata = 'V~O~LE~50';
$field25->displaytype = 1;

$blockInstance3->addField($field25);

$field26 = new Vtiger_Field();
$field26->label = 'LBL_QUOTES_DESTINATIONCITY';
$field26->name = 'destination_city';
$field26->table = 'vtiger_quotescf';
$field26->column = 'destination_city';
$field26->uitype = 1;
$field26->typeofdata = 'V~O~LE~50';
$field26->displaytype = 1;

$blockInstance3->addField($field26);

$field27 = new Vtiger_Field();
$field27->label = 'LBL_QUOTES_ORIGINSTATE';
$field27->name = 'origin_state';
$field27->table = 'vtiger_quotescf';
$field27->column = 'origin_state';
$field27->uitype = 1;
$field27->typeofdata = 'V~O';
$field27->displaytype = 1;

$blockInstance3->addField($field27);

$field28 = new Vtiger_Field();
$field28->label = 'LBL_QUOTES_DESTINATIONSTATE';
$field28->name = 'destination_state';
$field28->table = 'vtiger_quotescf';
$field28->column = 'destination_state';
$field28->uitype = 1;
$field28->typeofdata = 'V~O';
$field28->displaytype = 1;

$blockInstance3->addField($field28);

$field29 = new Vtiger_Field();
$field29->label = 'LBL_QUOTES_ORIGINZIP';
$field29->name = 'origin_zip';
$field29->table = 'vtiger_quotescf';
$field29->column = 'origin_zip';
$field29->uitype = 7;
$field29->typeofdata = 'V~O';
$field29->displaytype = 1;
$field29->quickcreate = 0;

$blockInstance3->addField($field29);

$field30 = new Vtiger_Field();
$field30->label = 'LBL_QUOTES_DESTINATIONZIP';
$field30->name = 'destination_zip';
$field30->table = 'vtiger_quotescf';
$field30->column = 'destination_zip';
$field30->uitype = 7;
$field30->typeofdata = 'V~O';
$field30->displaytype = 1;
$field30->quickcreate = 0;

$blockInstance3->addField($field30);

$field31 = new Vtiger_Field();
$field31->label = 'LBL_QUOTES_ORIGINPHONE1';
$field31->name = 'origin_phone1';
$field31->table = 'vtiger_quotescf';
$field31->column = 'origin_phone1';
$field31->uitype = 11;
$field31->typeofdata = 'V~O';
$field31->displaytype = 1;

$blockInstance3->addField($field31);

$field32 = new Vtiger_Field();
$field32->label = 'LBL_QUOTES_DESTINATIONPHONE1';
$field32->name = 'destination_phone1';
$field32->table = 'vtiger_quotescf';
$field32->column = 'destination_phone1';
$field32->uitype = 11;
$field32->typeofdata = 'V~O';
$field32->displaytype = 1;

$blockInstance3->addField($field32);

$field33 = new Vtiger_Field();
$field33->label = 'LBL_QUOTES_ORIGINPHONE2';
$field33->name = 'origin_phone2';
$field33->table = 'vtiger_quotescf';
$field33->column = 'origin_phone2';
$field33->uitype = 11;
$field33->typeofdata = 'V~O';
$field33->displaytype = 1;

$blockInstance3->addField($field33);

$field34 = new Vtiger_Field();
$field34->label = 'LBL_QUOTES_DESTINATIONPHONE2';
$field34->name = 'destination_phone2';
$field34->table = 'vtiger_quotescf';
$field34->column = 'destination_phone2';
$field34->uitype = 11;
$field34->typeofdata = 'V~O';
$field34->displaytype = 1;

$blockInstance3->addField($field34);

// local
$field35 = new Vtiger_Field();
$field35->label = 'LBL_QUOTES_HOLDERFIELD1';
$field35->name = 'cf_1003';
$field35->table = 'vtiger_quotescf';
$field35->column = 'cf_1003';
$field35->uitype = 1;
$field35->typeofdata = 'V~O~LE~15';
$field35->displaytype = 1;

$blockInstance4->addField($field35);

$field36 = new Vtiger_Field();
$field36->label = 'LBL_QUOTES_WEIGHT';
$field36->name = 'weight';
$field36->table = 'vtiger_quotes';
$field36->column = 'weight';
$field36->uitype = 7;
$field36->typeofdata = 'I~O';
$field36->displaytype = 1;
$field36->quickcreate = 0;

$blockInstance5->addField($field36);

$field37 = new Vtiger_Field();
$field37->label = 'LBL_QUOTES_PICKUPDATE';
$field37->name = 'pickup_date';
$field37->table = 'vtiger_quotes';
$field37->column = 'pickup_date';
$field37->uitype = 5;
$field37->typeofdata = 'D~O';
$field37->displaytype = 1;

$blockInstance5->addField($field37);

$field38 = new Vtiger_Field();
$field38->label = 'LBL_QUOTES_FULLPACKAPPLIED';
$field38->name = 'full_pack';
$field38->table = 'vtiger_quotes';
$field38->column = 'full_pack';
$field38->uitype = 56;
$field38->typeofdata = 'C~O';
$field38->displaytype = 1;
$field38->quickcreate = 0;

$blockInstance5->addField($field38);

$field39 = new Vtiger_Field();
$field39->label = 'LBL_QUOTES_VALUATIONDEDUCTIBLE';
$field39->name = 'valuation_deductible';
$field39->table = 'vtiger_quotes';
$field39->column = 'valuation_deductible';
$field39->uitype = 16;
$field39->typeofdata = 'V~O';
$field39->displaytype = 1;

$blockInstance5->addField($field39);

$field40 = new Vtiger_Field();
$field40->label = 'LBL_QUOTES_FULLUNPACKAPPLIED';
$field40->name = 'full_unpack';
$field40->table = 'vtiger_quotes';
$field40->column = 'full_unpack';
$field40->uitype = 56;
$field40->typeofdata = 'C~O';
$field40->displaytype = 1;
$field40->quickcreate = 0;

$blockInstance5->addField($field40);

$field41 = new Vtiger_Field();
$field41->label = 'LBL_QUOTES_VALUATIONAMOUNT';
$field41->name = 'valuation_amount';
$field41->table = 'vtiger_quotes';
$field41->column = 'valuation_amount';
$field41->uitype = 71;
$field41->typeofdata = 'N~O';
$field41->displaytype = 1;

$blockInstance5->addField($field41);

$field42 = new Vtiger_Field();
$field42->label = 'LBL_QUOTES_BOTTOMLINEDISCOUNT';
$field42->name = 'bottom_line_discount';
$field42->table = 'vtiger_quotes';
$field42->column = 'bottom_line_discount';
$field42->uitype = 7;
$field42->typeofdata = 'NN~O';
$field42->displaytype = 1;
$field42->quickcreate = 0;

$blockInstance5->addField($field42);

$field43 = new Vtiger_Field();
$field43->label = 'LBL_QUOTES_MILEAGE';
$field43->name = 'interstate_mileage';
$field43->table = 'vtiger_quotes';
$field43->column = 'interstate_mileage';
$field43->uitype = 7;
$field43->typeofdata = 'I~O';
$field43->displaytype = 1;

$blockInstance5->addField($field43);

// commercial
$field44 = new Vtiger_Field();
$field44->label = 'LBL_QUOTES_HOLDERFIELD2';
$field44->name = 'cf_1005';
$field44->table = 'vtiger_quotescf';
$field44->column = 'cf_1005';
$field44->uitype = 1;
$field44->typeofdata = 'V~O~LE~15';
$field44->displaytype = 1;

$blockInstance6->addField($field44);

// originally 5
$field45 = new Vtiger_Field();
$field45->label = 'LBL_QUOTES_TERMSANDCONDITIONS';
$field45->name = 'terms_conditions';
$field45->table = 'vtiger_quotes';
$field45->column = 'terms_conditions';
$field45->uitype = 19;
$field45->typeofdata = 'V~O';
$field45->displaytype = 1;

$blockInstance7->addField($field45);

$field46 = new Vtiger_Field();
$field46->label = 'LBL_QUOTES_DESCRIPTION';
$field46->name = 'description';
$field46->table = 'vtiger_crmentity';
$field46->column = 'description';
$field46->uitype = 19;
$field46->typeofdata = 'V~O';
$field46->displaytype = 1;

$blockInstance8->addField($field46);

$field47 = new Vtiger_Field();
$field47->label = 'LBL_QUOTES_TAX2';
$field47->name = 'tax2';
$field47->table = 'vtiger_inventoryproductrel';
$field47->column = 'tax2';
$field47->uitype = 83;
$field47->typeofdata = 'V~O';
$field47->displaytype = 5;

$blockInstance9->addField($field47);

$field48 = new Vtiger_Field();
$field48->label = 'LBL_QUOTES_TAX3';
$field48->name = 'tax3';
$field48->table = 'vtiger_inventoryproductrel';
$field48->column = 'tax3';
$field48->uitype = 83;
$field48->typeofdata = 'V~O';
$field48->displaytype = 5;

$blockInstance9->addField($field48);

$field49 = new Vtiger_Field();
$field49->label = 'LBL_QUOTES_PRETAXTOTAL';
$field49->name = 'pre_tax_total';
$field49->table = 'vtiger_quotes';
$field49->column = 'pre_tax_total';
$field49->uitype = 72;
$field49->typeofdata = 'N~O';
$field49->displaytype = 3;

$blockInstance->addField($field49);

$field50 = new Vtiger_Field();
$field50->label = 'LBL_QUOTES_HDNSHPERCENT';
$field50->name = 'hdnS_H_Percent';
$field50->table = 'vtiger_quotes';
$field50->column = 's_h_percent';
$field50->uitype = 1;
$field50->typeofdata = 'N~O';
$field50->displaytype = 5;

$blockInstance9->addField($field50);

$field51 = new Vtiger_Field();
$field51->label = 'LBL_QUOTES_LASTMODIFIEDBY';
$field51->name = 'modifiedby';
$field51->table = 'vtiger_crmentity';
$field51->column = 'modifiedby';
$field51->uitype = 52;
$field51->typeofdata = 'V~O';
$field51->displaytype = 3;

$blockInstance->addField($field51);

$field52 = new Vtiger_Field();
$field52->label = 'LBL_QUOTES_CONVERSIONRATE';
$field52->name = 'conversion_rate';
$field52->table = 'vtiger_quotes';
$field52->column = 'conversion_rate';
$field52->uitype = 1;
$field52->typeofdata = 'N~O';
$field52->displaytype = 3;

$blockInstance->addField($field52);

$field54 = new Vtiger_Field();
$field54->label = 'LBL_QUOTES_HDNDISCOUNTAMOUNT';
$field54->name = 'hdnDiscountAmount';
$field54->table = 'vtiger_quotes';
$field54->column = 'discount_amount';
$field54->uitype = 72;
$field54->typeofdata = 'N~O';
$field54->displaytype = 3;

$blockInstance->addField($field54);

$field55 = new Vtiger_Field();
$field55->label = 'LBL_QUOTES_SHIPPINGADDRESS';
$field55->name = 'ship_street';
$field55->table = 'vtiger_quotesshipads';
$field55->column = 'ship_street';
$field55->uitype = 24;
$field55->typeofdata = 'V~O';
$field55->displaytype = 1;

$blockInstance3->addField($field54);

$field56 = new Vtiger_Field();
$field56->label = 'LBL_QUOTES_HDNSHAMOUNT';
$field56->name = 'hdnS_H_Amount';
$field56->table = 'vtiger_quotes';
$field56->column = 's_h_amount';
$field56->uitype = 72;
$field56->typeofdata = 'N~O';
$field56->displaytype = 3;

$blockInstance->addField($field56);

$field57 = new Vtiger_Field();
$field57->label = 'LBL_QUOTES_SHIPPINGCITY';
$field57->name = 'ship_city';
$field57->table = 'vtiger_quotesshipads';
$field57->column = 'ship_city';
$field57->uitype = 1;
$field57->typeofdata = 'V~O';
$field57->displaytype = 1;
$field57->presense = 1;

// $blockInstance3->addField($field57);

$field58 = new Vtiger_Field();
$field58->label = 'LBL_QUOTES_HDNSUBTOTAL';
$field58->name = 'hdnSubTotal';
$field58->table = 'vtiger_quotes';
$field58->column = 'subtotal';
$field58->uitype = 72;
$field58->typeofdata = 'N~O';
$field58->displaytype = 3;

$blockInstance->addField($field58);

$field59 = new Vtiger_Field();
$field59->label = 'LBL_QUOTES_SHIPPINGSTATE';
$field59->name = 'ship_state';
$field59->table = 'vtiger_quotesshipads';
$field59->column = 'ship_state';
$field59->uitype = 1;
$field59->typeofdata = 'V~O';
$field59->displaytype = 1;

//$blockInstance->addField($field59);

/**
 * Not in Estimate Details Block
 */
$field60 = new Vtiger_Field();
$field60->label = 'LBL_QUOTES_CARRIER';
$field60->name = 'carrier';
$field60->table = 'vtiger_quotes';
$field60->column = 'carrier';
$field60->uitype = 15;
$field60->typeofdata = 'V~O';
$field60->displaytype = 1;

//$blockInstance->addField($field60);

$field61 = new Vtiger_Field();
$field61->label = 'LBL_QUOTES_SHIPPINGZIPCODE';
$field61->name = 'ship_code';
$field61->table = 'vtiger_quotesshipads';
$field61->column = 'ship_code';
$field61->uitype = 1;
$field61->typeofdata = 'V~O';
$field61->displaytype = 1;
$field61->presense = 1;

// $blockInstance->addField($field61);

$field62 = new Vtiger_Field();
$field62->label = 'LBL_QUOTES_ADJUSTMENT';
$field62->name = 'txtAdjustment';
$field62->table = 'vtiger_quotes';
$field62->column = 'adjustment';
$field62->uitype = 72;
$field62->typeofdata = 'NN~O';
$field62->displaytype = 3;

$blockInstance->addField($field62);

$field63 = new Vtiger_Field();
$field63->label = 'LBL_QUOTES_SHIPPINGCOUNTRY';
$field63->name = 'ship_country';
$field63->table = 'vtiger_quotesshipads';
$field63->column = 'ship_country';
$field63->uitype = 1;
$field63->typeofdata = 'V~O';
$field63->displaytype = 1;
$field63->presense = 1;

// $blockInstance3->addField($field63);

$field64 = new Vtiger_Field();
$field64->label = 'LBL_QUOTES_INVENTORYMANAGER';
$field64->name = 'assigned_user_id1';
$field64->table = 'vtiger_quotes';
$field64->column = 'inventorymanager';
$field64->uitype = 77;
$field64->typeofdata = 'I~O';
$field64->displaytype = 1;

// $blockInstance3->addField($field64);

$field65 = new Vtiger_Field();
$field65->label = 'LBL_QUOTES_HOLDERFIELD3';
$field65->name = 'cf_1007';
$field65->table = 'vtiger_quotescf';
$field65->column = 'cf_1007';
$field65->uitype = 1;
$field65->typeofdata = 'V~O~LE~15';
$field65->displaytype = 1;

$blockInstance6->addField($field65);

$field66 = new Vtiger_Field();
$field66->label = 'LBL_QUOTES_HDNGRANDTOTAL';
$field66->name = 'hdnGrandTotal';
$field66->table = 'vtiger_quotes';
$field66->column = 'total';
$field66->uitype = 72;
$field66->typeofdata = 'N~O';
$field66->displaytype = 3;

$blockInstance->addField($field66);

$field67 = new Vtiger_Field();
$field67->label = 'LBL_QUOTES_SHIPPINGPOBOX';
$field67->name = 'ship_pobox';
$field67->table = 'vtiger_quotesshipads';
$field67->column = 'ship_pobox';
$field67->uitype = 1;
$field67->typeofdata = 'V~O';
$field67->displaytype = 1;
$field67->presense = 1;

// $blockInstance->addField($field67);

$field68 = new Vtiger_Field();
$field68->label = 'LBL_QUOTES_TAX1';
$field68->name = 'tax1';
$field68->table = 'vtiger_inventoryproductrel';
$field68->column = 'tax1';
$field68->uitype = 83;
$field68->typeofdata = 'V~O';
$field68->displaytype = 5;

$blockInstance9->addField($field68);

$field69 = new Vtiger_Field();
$field69->label = 'LBL_QUOTES_PICKUPTIME';
$field69->name = 'pickup_time';
$field69->table = 'vtiger_quotes';
$field69->column = 'pickup_time';
$field69->uitype = 14;
$field69->typeofdata = 'T~O';
$field69->displaytype = 1;

// $blockInstance5->addField($field69);

$field70 = new Vtiger_Field();
$field70->label = 'LBL_QUOTES_FUELPRICE';
$field70->name = 'fuel_price';
$field70->table = 'vtiger_quotes';
$field70->column = 'fuel_price';
$field70->uitype = 71;
$field70->typeofdata = 'N~O';
$field70->displaytype = 1;

// $blockInstance5->addField($field70);

$field71 = new Vtiger_Field();
$field71->label = 'LBL_QUOTES_ITEMCOMMENT';
$field71->name = 'comment';
$field71->table = 'vtiger_inventoryproductrel';
$field71->column = 'comment';
$field71->uitype = 19;
$field71->typeofdata = 'V~O';
$field71->displaytype = 5;

$blockInstance9->addField($field71);

$field72 = new Vtiger_Field();
$field72->label = 'LBL_QUOTES_PRODUCTID';
$field72->name = 'productid';
$field72->table = 'vtiger_inventoryproductrel';
$field72->column = 'productid';
$field72->uitype = 10;
$field72->typeofdata = 'V~M';
$field72->displaytype = 5;

$blockInstance9->addField($field72);

$field73 = new Vtiger_Field();
$field73->label = 'LBL_QUOTES_LISTPRICE';
$field73->name = 'listprice';
$field73->table = 'vtiger_inventoryproductrel';
$field73->column = 'listprice';
$field73->uitype = 71;
$field73->typeofdata = 'N~O';
$field73->displaytype = 5;

$blockInstance9->addField($field73);

$field74 = new Vtiger_Field();
$field74->label = 'LBL_QUOTES_QUANTITY';
$field74->name = 'quantity';
$field74->table = 'vtiger_inventoryproductrel';
$field74->column = 'quantity';
$field74->uitype = 7;
$field74->typeofdata = 'N~O';
$field74->displaytype = 5;

$blockInstance9->addField($field74);

$field75 = new Vtiger_Field();
$field75->label = 'LBL_QUOTES_ITEMDISCOUNTPERCENT';
$field75->name = 'discount_percent';
$field75->table = 'vtiger_inventoryproductrel';
$field75->column = 'discount_percent';
$field75->uitype = 7;
$field75->typeofdata = 'V~O';
$field75->displaytype = 3;

$blockInstance9->addField($field75);

$field76 = new Vtiger_Field();
$field76->label = 'LBL_QUOTES_RATEESTIMATE';
$field76->name = 'rate_estimate';
$field76->table = 'vtiger_quotes';
$field76->column = 'rate_estimate';
$field76->uitype = 71;
$field76->typeofdata = 'N~O';
$field76->displaytype = 1;

// $blockInstance5->addField($field76);

$field77 = new Vtiger_Field();
$field77->label = 'LBL_QUOTES_SITORIGINDATEIN';
$field77->name = 'sit_origin_date_in';
$field77->table = 'vtiger_quotes';
$field77->column = 'sit_origin_date_in';
$field77->uitype = 5;
$field77->typeofdata = 'D~O';
$field77->displaytype = 1;

$blockInstance10->addField($field77);

$field78 = new Vtiger_Field();
$field78->label = 'LBL_QUOTES_SITDESTINATIONDATEIN';
$field78->name = 'sit_dest_date_in';
$field78->table = 'vtiger_quotes';
$field78->column = 'sit_dest_date_in';
$field78->uitype = 5;
$field78->typeofdata = 'D~O';
$field78->displaytype = 1;

$blockInstance10->addField($field78);

$field79 = new Vtiger_Field();
$field79->label = 'LBL_QUOTES_SITORIGINPICKUPDATE';
$field79->name = 'sit_origin_pickup_date';
$field79->table = 'vtiger_quotes';
$field79->column = 'sit_origin_pickup_date';
$field79->uitype = 5;
$field79->typeofdata = 'D~O';
$field79->displaytype = 1;

$blockInstance10->addField($field79);

$field80 = new Vtiger_Field();
$field80->label = 'LBL_QUOTES_SITDELIVERYDATE';
$field80->name = 'sit_dest_delivery_date';
$field80->table = 'vtiger_quotes';
$field80->column = 'sit_dest_delivery_date';
$field80->uitype = 5;
$field80->typeofdata = 'D~O';
$field80->displaytype = 1;

$blockInstance10->addField($field80);

$field81 = new Vtiger_Field();
$field81->label = 'LBL_QUOTES_SITORIGINWEIGHT';
$field81->name = 'sit_origin_weight';
$field81->table = 'vtiger_quotes';
$field81->column = 'sit_origin_weight';
$field81->uitype = 7;
$field81->typeofdata = 'I~O';
$field81->displaytype = 1;

$blockInstance10->addField($field81);

$field82 = new Vtiger_Field();
$field82->label = 'LBL_QUOTES_SITDESTINATIONWEIGHT';
$field82->name = 'sit_dest_weight';
$field82->table = 'vtiger_quotes';
$field82->column = 'sit_dest_weight';
$field82->uitype = 7;
$field82->typeofdata = 'I~O';
$field82->displaytype = 1;

$blockInstance10->addField($field82);

$field83 = new Vtiger_Field();
$field83->label = 'LBL_QUOTES_SITORIGINZIP';
$field83->name = 'sit_origin_zip';
$field83->table = 'vtiger_quotes';
$field83->column = 'sit_origin_zip';
$field83->uitype = 7;
$field83->typeofdata = 'I~O';
$field83->displaytype = 1;

$blockInstance10->addField($field83);

$field84 = new Vtiger_Field();
$field84->label = 'LBL_QUOTES_SITDESTINATIONZIP';
$field84->name = 'sit_dest_zip';
$field84->table = 'vtiger_quotes';
$field84->column = 'sit_dest_zip';
$field84->uitype = 7;
$field84->typeofdata = 'I~O';
$field84->displaytype = 1;

$blockInstance10->addField($field84);

$field85 = new Vtiger_Field();
$field85->label = 'LBL_QUOTES_SITORIGINMILES';
$field85->name = 'sit_origin_miles';
$field85->table = 'vtiger_quotes';
$field85->column = 'sit_origin_miles';
$field85->uitype = 7;
$field85->typeofdata = 'I~O';
$field85->displaytype = 1;

$blockInstance10->addField($field85);

$field86 = new Vtiger_Field();
$field86->label = 'LBL_QUOTES_SITDESTINATIONMILES';
$field86->name = 'sit_dest_miles';
$field86->table = 'vtiger_quotes';
$field86->column = 'sit_dest_miles';
$field86->uitype = 7;
$field86->typeofdata = 'I~O';
$field86->displaytype = 1;

$blockInstance10->addField($field86);

$field87 = new Vtiger_Field();
$field87->label = 'LBL_QUOTES_SITORIGINNUMBERDAYS';
$field87->name = 'sit_origin_number_days';
$field87->table = 'vtiger_quotes';
$field87->column = 'sit_origin_number_days';
$field87->uitype = 7;
$field87->typeofdata = 'I~O';
$field87->displaytype = 1;

$blockInstance10->addField($field87);

$field88 = new Vtiger_Field();
$field88->label = 'LBL_QUOTES_SITDESTINATIONNUMBERDAYS';
$field88->name = 'sit_dest_number_days';
$field88->table = 'vtiger_quotes';
$field88->column = 'sit_dest_number_days';
$field88->uitype = 7;
$field88->typeofdata = 'I~O';
$field88->displaytype = 1;

$blockInstance10->addField($field88);

$field89 = new Vtiger_Field();
$field89->label = 'LBL_QUOTES_SITORIGINFIRSTDAYRATE';
$field89->name = 'sit_origin_first_day';
$field89->table = 'vtiger_quotes';
$field89->column = 'sit_origin_first_day';
$field89->uitype = 71;
$field89->typeofdata = 'N~O';
$field89->displaytype = 1;

// $blockInstance10->addField($field89);

$field90 = new Vtiger_Field();
$field90->label = 'LBL_QUOTES_SITDESTINATIONFIRSTDAYRATE';
$field90->name = 'sit_dest_first_day';
$field90->table = 'vtiger_quotes';
$field90->column = 'sit_dest_first_day';
$field90->uitype = 71;
$field90->typeofdata = 'N~O';
$field90->displaytype = 1;

// $blockInstance10->addField($field90);

$field91 = new Vtiger_Field();
$field91->label = 'LBL_QUOTES_SITORIGINFIRSTDAYCOST';
$field91->name = 'sit_origin_first_day_cost';
$field91->table = 'vtiger_quotes';
$field91->column = 'sit_origin_first_day_cost';
$field91->uitype = 71;
$field91->typeofdata = 'N~O';
$field91->displaytype = 1;

// $blockInstance10->addField($field91);

$field92 = new Vtiger_Field();
$field92->label = 'LBL_QUOTES_SITDESTINATIONFIRSTDAYCOST';
$field92->name = 'sit_dest_first_day_cost';
$field92->table = 'vtiger_quotes';
$field92->column = 'sit_dest_first_day_cost';
$field92->uitype = 71;
$field92->typeofdata = 'N~O';
$field92->displaytype = 1;

// $blockInstance10->addField($field92);

$field93 = new Vtiger_Field();
$field93->label = 'LBL_QUOTES_SITORIGINSECONDDAYRATE';
$field93->name = 'sit_origin_sec_day';
$field93->table = 'vtiger_quotes';
$field93->column = 'sit_origin_sec_day';
$field93->uitype = 71;
$field93->typeofdata = 'N~O';
$field93->displaytype = 1;

// $blockInstance10->addField($field93);

$field94 = new Vtiger_Field();
$field94->label = 'LBL_QUOTES_SITDESTINATIONSECONDDAYRATE';
$field94->name = 'sit_dest_sec_day';
$field94->table = 'vtiger_quotes';
$field94->column = 'sit_dest_sec_day';
$field94->uitype = 71;
$field94->typeofdata = 'N~O';
$field94->displaytype = 1;

// $blockInstance10->addField($field94);

$field95 = new Vtiger_Field();
$field95->label = 'LBL_QUOTES_SITORIGINSECONDDAYCOST';
$field95->name = 'sit_origin_sec_day_cost';
$field95->table = 'vtiger_quotes';
$field95->column = 'sit_origin_sec_day_cost';
$field95->uitype = 71;
$field95->typeofdata = 'N~O';
$field95->displaytype = 1;

// $blockInstance10->addField($field95);

$field96 = new Vtiger_Field();
$field96->label = 'LBL_QUOTES_SITDESTINATIONSECONDDAYCOST';
$field96->name = 'sit_dest_sec_day_cost';
$field96->table = 'vtiger_quotes';
$field96->column = 'sit_dest_sec_day_cost';
$field96->uitype = 71;
$field96->typeofdata = 'N~O';
$field96->displaytype = 1;

// $blockInstance10->addField($field96);

$field97 = new Vtiger_Field();
$field97->label = 'LBL_QUOTES_SITORIGINPICKUPDELIVERY';
$field97->name = 'sit_origin_pickup_delivery';
$field97->table = 'vtiger_quotes';
$field97->column = 'sit_origin_pickup_delivery';
$field97->uitype = 71;
$field97->typeofdata = 'N~O';
$field97->displaytype = 1;

// $blockInstance10->addField($field97);

$field98 = new Vtiger_Field();
$field98->label = 'LBL_QUOTES_SITDESTINATIONPICKUPDELIVERY';
$field98->name = 'sit_dest_pickup_delivery';
$field98->table = 'vtiger_quotes';
$field98->column = 'sit_dest_pickup_delivery';
$field98->uitype = 71;
$field98->typeofdata = 'N~O';
$field98->displaytype = 1;

// $blockInstance10->addField($field98);

$field99 = new Vtiger_Field();
$field99->label = 'LBL_QUOTES_SITORIGINFUELPERCENT';
$field99->name = 'sit_origin_fuel_percent';
$field99->table = 'vtiger_quotes';
$field99->column = 'sit_origin_fuel_percent';
$field99->uitype = 7;
$field99->typeofdata = 'N~O';
$field99->displaytype = 1;

$blockInstance10->addField($field99);

$field100 = new Vtiger_Field();
$field100->label = 'LBL_QUOTES_SITDESTINATIONFUELPERCENT';
$field100->name = 'sit_dest_fuel_percent';
$field100->table = 'vtiger_quotes';
$field100->column = 'sit_dest_fuel_percent';
$field100->uitype = 7;
$field100->typeofdata = 'N~O';
$field100->displaytype = 1;

$blockInstance10->addField($field100);

$field101 = new Vtiger_Field();
$field101->label = 'LBL_QUOTES_SITORIGINFUELSURCHARGE';
$field101->name = 'sit_origin_fuel_surcharge';
$field101->table = 'vtiger_quotes';
$field101->column = 'sit_origin_fuel_surcharge';
$field101->uitype = 71;
$field101->typeofdata = 'N~O';
$field101->displaytype = 1;

// $blockInstance10->addField($field101);

$field102 = new Vtiger_Field();
$field102->label = 'LBL_QUOTES_SITDESTINATIONFUELSURCHARGE';
$field102->name = 'sit_dest_fuel_surcharge';
$field102->table = 'vtiger_quotes';
$field102->column = 'sit_dest_fuel_surcharge';
$field102->uitype = 71;
$field102->typeofdata = 'N~O';
$field102->displaytype = 1;

// $blockInstance10->addField($field102);

$field103 = new Vtiger_Field();
$field103->label = 'LBL_QUOTES_SITORIGINIRRPERCENT';
$field103->name = 'sit_origin_irr_percent';
$field103->table = 'vtiger_quotes';
$field103->column = 'sit_origin_irr_percent';
$field103->uitype = 7;
$field103->typeofdata = 'N~O';
$field103->displaytype = 1;

// $blockInstance10->addField($field103);

$field104 = new Vtiger_Field();
$field104->label = 'LBL_QUOTES_SITDESTINATIONIRRPERCENT';
$field104->name = 'sit_dest_irr_percent';
$field104->table = 'vtiger_quotes';
$field104->column = 'sit_dest_irr_percent';
$field104->uitype = 7;
$field104->typeofdata = 'N~O';
$field104->displaytype = 1;

// $blockInstance10->addField($field104);

$field105 = new Vtiger_Field();
$field105->label = 'LBL_QUOTES_SITORIGINIRR';
$field105->name = 'sit_origin_irr';
$field105->table = 'vtiger_quotes';
$field105->column = 'sit_origin_irr';
$field105->uitype = 71;
$field105->typeofdata = 'N~O';
$field105->displaytype = 1;

// $blockInstance10->addField($field105);

$field106 = new Vtiger_Field();
$field106->label = 'LBL_QUOTES_SITDESTINATIONIRR';
$field106->name = 'sit_dest_irr';
$field106->table = 'vtiger_quotes';
$field106->column = 'sit_dest_irr';
$field106->uitype = 71;
$field106->typeofdata = 'N~O';
$field106->displaytype = 1;

// $blockInstance10->addField($field106);

$field107 = new Vtiger_Field();
$field107->label = 'LBL_QUOTES_SITORIGINOVERTIME';
$field107->name = 'sit_origin_overtime';
$field107->table = 'vtiger_quotes';
$field107->column = 'sit_origin_overtime';
$field107->uitype = 56;
$field107->typeofdata = 'C~O';
$field107->displaytype = 1;

$blockInstance10->addField($field107);

$field108 = new Vtiger_Field();
$field108->label = 'LBL_QUOTES_SITDESTINATIONOVERTIME';
$field108->name = 'sit_dest_overtime';
$field108->table = 'vtiger_quotes';
$field108->column = 'sit_dest_overtime';
$field108->uitype = 56;
$field108->typeofdata = 'C~O';
$field108->displaytype = 1;

$blockInstance10->addField($field108);

$field109 = new Vtiger_Field();
$field109->label = 'LBL_QUOTES_ACCSHUTTLEORIGINWEIGHT';
$field109->name = 'acc_shuttle_origin_weight';
$field109->table = 'vtiger_quotes';
$field109->column = 'acc_shuttle_origin_weight';
$field109->uitype = 7;
$field109->typeofdata = 'I~O';
$field109->displaytype = 1;

$blockInstance11->addField($field109);


$field110 = new Vtiger_Field();
$field110->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONWEIGHT';
$field110->name = 'acc_shuttle_dest_weight';
$field110->table = 'vtiger_quotes';
$field110->column = 'acc_shuttle_dest_weight';
$field110->uitype = 7;
$field110->typeofdata = 'I~O';
$field110->displaytype = 1;

$blockInstance11->addField($field110);

$field111 = new Vtiger_Field();
$field111->label = 'LBL_QUOTES_ACCSHUTTLEORIGINAPPLIED';
$field111->name = 'acc_shuttle_origin_applied';
$field111->table = 'vtiger_quotes';
$field111->column = 'acc_shuttle_origin_applied';
$field111->uitype = 56;
$field111->typeofdata = 'C~O';
$field111->displaytype = 1;

$blockInstance11->addField($field111);

$field112 = new Vtiger_Field();
$field112->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONAPPLIED';
$field112->name = 'acc_shuttle_dest_applied';
$field112->table = 'vtiger_quotes';
$field112->column = 'acc_shuttle_dest_applied';
$field112->uitype = 56;
$field112->typeofdata = 'C~O';
$field112->displaytype = 1;

$blockInstance11->addField($field112);

$field113 = new Vtiger_Field();
$field113->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOT';
$field113->name = 'acc_shuttle_origin_ot';
$field113->table = 'vtiger_quotes';
$field113->column = 'acc_shuttle_origin_ot';
$field113->uitype = 56;
$field113->typeofdata = 'C~O';
$field113->displaytype = 1;

$blockInstance11->addField($field113);

$field114 = new Vtiger_Field();
$field114->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOT';
$field114->name = 'acc_shuttle_dest_ot';
$field114->table = 'vtiger_quotes';
$field114->column = 'acc_shuttle_dest_ot';
$field114->uitype = 56;
$field114->typeofdata = 'C~O';
$field114->displaytype = 1;

$blockInstance11->addField($field114);

$field115 = new Vtiger_Field();
$field115->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOVER25';
$field115->name = 'acc_shuttle_origin_over25';
$field115->table = 'vtiger_quotes';
$field115->column = 'acc_shuttle_origin_over25';
$field115->uitype = 56;
$field115->typeofdata = 'C~O';
$field115->displaytype = 1;

$blockInstance11->addField($field115);

$field116 = new Vtiger_Field();
$field116->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOVER25';
$field116->name = 'acc_shuttle_dest_over25';
$field116->table = 'vtiger_quotes';
$field116->column = 'acc_shuttle_dest_over25';
$field116->uitype = 56;
$field116->typeofdata = 'C~O';
$field116->displaytype = 1;

$blockInstance11->addField($field116);

$field117 = new Vtiger_Field();
$field117->label = 'LBL_QUOTES_ACCSHUTTLEORIGINMILES';
$field117->name = 'acc_shuttle_origin_miles';
$field117->table = 'vtiger_quotes';
$field117->column = 'acc_shuttle_origin_miles';
$field117->uitype = 7;
$field117->typeofdata = 'I~O';
$field117->displaytype = 1;

$blockInstance11->addField($field117);

$field118 = new Vtiger_Field();
$field118->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONMILES';
$field118->name = 'acc_shuttle_dest_miles';
$field118->table = 'vtiger_quotes';
$field118->column = 'acc_shuttle_dest_miles';
$field118->uitype = 7;
$field118->typeofdata = 'I~O';
$field118->displaytype = 1;

$blockInstance11->addField($field118);

$field119 = new Vtiger_Field();
$field119->label = 'LBL_QUOTES_ACCOTORIGINWEIGHT';
$field119->name = 'acc_ot_origin_weight';
$field119->table = 'vtiger_quotes';
$field119->column = 'acc_ot_origin_weight';
$field119->uitype = 7;
$field119->typeofdata = 'I~O';
$field119->displaytype = 1;

$blockInstance11->addField($field119);

$field120 = new Vtiger_Field();
$field120->label = 'LBL_QUOTES_ACCOTDESTINATIONWEIGHT';
$field120->name = 'acc_ot_dest_weight';
$field120->table = 'vtiger_quotes';
$field120->column = 'acc_ot_dest_weight';
$field120->uitype = 7;
$field120->typeofdata = 'I~O';
$field120->displaytype = 1;

$blockInstance11->addField($field120);

$field121 = new Vtiger_Field();
$field121->label = 'LBL_QUOTES_ACCOTORIGINAPPLIED';
$field121->name = 'acc_ot_origin_applied';
$field121->table = 'vtiger_quotes';
$field121->column = 'acc_ot_origin_applied';
$field121->uitype = 56;
$field121->typeofdata = 'C~O';
$field121->displaytype = 1;

$blockInstance11->addField($field121);

$field122 = new Vtiger_Field();
$field122->label = 'LBL_QUOTES_ACCOTDESTINATIONAPPLIED';
$field122->name = 'acc_ot_dest_applied';
$field122->table = 'vtiger_quotes';
$field122->column = 'acc_ot_dest_applied';
$field122->uitype = 56;
$field122->typeofdata = 'C~O';
$field122->displaytype = 1;

$blockInstance11->addField($field122);

$field123 = new Vtiger_Field();
$field123->label = 'LBL_QUOTES_ACCSELFSTGORIGINWEIGHT';
$field123->name = 'acc_selfstg_origin_weight';
$field123->table = 'vtiger_quotes';
$field123->column = 'acc_selfstg_origin_weight';
$field123->uitype = 7;
$field123->typeofdata = 'I~O';
$field123->displaytype = 1;

$blockInstance11->addField($field123);

$field124 = new Vtiger_Field();
$field124->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONWEIGHT';
$field124->name = 'acc_selfstg_dest_weight';
$field124->table = 'vtiger_quotes';
$field124->column = 'acc_selfstg_dest_weight';
$field124->uitype = 7;
$field124->typeofdata = 'I~O';
$field124->displaytype = 1;

$blockInstance11->addField($field124);

$field125 = new Vtiger_Field();
$field125->label = 'LBL_QUOTES_ACCSELFSTGORIGINAPPLIED';
$field125->name = 'acc_selfstg_origin_applied';
$field125->table = 'vtiger_quotes';
$field125->column = 'acc_selfstg_origin_applied';
$field125->uitype = 56;
$field125->typeofdata = 'C~O';
$field125->displaytype = 1;

$blockInstance11->addField($field125);

$field126 = new Vtiger_Field();
$field126->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONAPPLIED';
$field126->name = 'acc_selfstg_dest_applied';
$field126->table = 'vtiger_quotes';
$field126->column = 'acc_selfstg_dest_applied';
$field126->uitype = 56;
$field126->typeofdata = 'C~O';
$field126->displaytype = 1;

$blockInstance11->addField($field126);

$field127 = new Vtiger_Field();
$field127->label = 'LBL_QUOTES_ACCSELFSTGORIGINOT';
$field127->name = 'acc_selfstg_origin_ot';
$field127->table = 'vtiger_quotes';
$field127->column = 'acc_selfstg_origin_ot';
$field127->uitype = 56;
$field127->typeofdata = 'C~O';
$field127->displaytype = 1;

$blockInstance11->addField($field127);

$field128 = new Vtiger_Field();
$field128->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONOT';
$field128->name = 'acc_selfstg_dest_ot';
$field128->table = 'vtiger_quotes';
$field128->column = 'acc_selfstg_dest_ot';
$field128->uitype = 56;
$field128->typeofdata = 'C~O';
$field128->displaytype = 1;

$blockInstance11->addField($field128);

$field129 = new Vtiger_Field();
$field129->label = 'LBL_QUOTES_ACCEXLABORORIGINHOURS';
$field129->name = 'acc_exlabor_origin_hours';
$field129->table = 'vtiger_quotes';
$field129->column = 'acc_exlabor_origin_hours';
$field129->uitype = 7;
$field129->typeofdata = 'I~O';
$field129->displaytype = 1;

$blockInstance11->addField($field129);

$field130 = new Vtiger_Field();
$field130->label = 'LBL_QUOTES_ACCEXLABORDESTINATIONHOURS';
$field130->name = 'acc_exlabor_dest_hours';
$field130->table = 'vtiger_quotes';
$field130->column = 'acc_exlabor_dest_hours';
$field130->uitype = 7;
$field130->typeofdata = 'I~O';
$field130->displaytype = 1;

$blockInstance11->addField($field130);

$field131 = new Vtiger_Field();
$field131->label = 'LBL_QUOTES_ACCEXLABOROTORIGINHOURS';
$field131->name = 'acc_exlabor_ot_origin_hours';
$field131->table = 'vtiger_quotes';
$field131->column = 'acc_exlabor_ot_origin_hours';
$field131->uitype = 7;
$field131->typeofdata = 'I~O';
$field131->displaytype = 1;

$blockInstance11->addField($field131);

$field132 = new Vtiger_Field();
$field132->label = 'LBL_QUOTES_ACCEXLABOROTDESTINATIONHOURS';
$field132->name = 'acc_exlabor_ot_dest_hours';
$field132->table = 'vtiger_quotes';
$field132->column = 'acc_exlabor_ot_dest_hours';
$field132->uitype = 7;
$field132->typeofdata = 'I~O';
$field132->displaytype = 1;

$blockInstance11->addField($field132);

$field133 = new Vtiger_Field();
$field133->label = 'LBL_QUOTES_ACCWAITORIGINHOURS';
$field133->name = 'acc_wait_origin_hours';
$field133->table = 'vtiger_quotes';
$field133->column = 'acc_wait_origin_hours';
$field133->uitype = 7;
$field133->typeofdata = 'I~O';
$field133->displaytype = 1;

$blockInstance11->addField($field133);

$field134 = new Vtiger_Field();
$field134->label = 'LBL_QUOTES_ACCWAITDESTINATIONHOURS';
$field134->name = 'acc_wait_dest_hours';
$field134->table = 'vtiger_quotes';
$field134->column = 'acc_wait_dest_hours';
$field134->uitype = 7;
$field134->typeofdata = 'I~O';
$field134->displaytype = 1;

$blockInstance11->addField($field134);

$field135 = new Vtiger_Field();
$field135->label = 'LBL_QUOTES_ACCWAITOTORIGINHOURS';
$field135->name = 'acc_wait_ot_origin_hours';
$field135->table = 'vtiger_quotes';
$field135->column = 'acc_wait_ot_origin_hours';
$field135->uitype = 7;
$field135->typeofdata = 'I~O';
$field135->displaytype = 1;

$blockInstance11->addField($field135);

$field136 = new Vtiger_Field();
$field136->label = 'LBL_QUOTES_ACCWAITOTDESTINATIONHOURS';
$field136->name = 'acc_wait_ot_dest_hours';
$field136->table = 'vtiger_quotes';
$field136->column = 'acc_wait_ot_dest_hours';
$field136->uitype = 7;
$field136->typeofdata = 'I~O';
$field136->displaytype = 1;

$blockInstance11->addField($field136);

$field137 = new Vtiger_Field();
$field137->label = 'LBL_QUOTES_HDNTAXTYPE';
$field137->name = 'hdnTaxType';
$field137->table = 'vtiger_quotes';
$field137->column = 'taxtype';
$field137->uitype = 16;
$field137->typeofdata = 'V~O';
$field137->displaytype = 3;

$blockInstance->addField($field137);

$field138 = new Vtiger_Field();
$field138->label = 'LBL_QUOTES_DISCOUNT';
$field138->name = 'discount_amount';
$field138->table = 'vtiger_inventoryproductrel';
$field138->column = 'discount_amount';
$field138->uitype = 71;
$field138->typeofdata = 'N~O';
$field138->displaytype = 5;

$blockInstance9->addField($field138);

$field139 = new Vtiger_Field();
$field139->label = 'LBL_QUOTES_HDNDISCOUNTPERCENT';
$field139->name = 'hdnDiscountPercent';
$field139->table = 'vtiger_quotes';
$field139->column = 'discount_percent';
$field139->uitype = 1;
$field139->typeofdata = 'N~O';
$field139->displaytype = 3;

$blockInstance->addField($field139);

$field140 = new Vtiger_Field();
$field140->label = 'LBL_QUOTES_CURRENCY';
$field140->name = 'currency_id';
$field140->table = 'vtiger_quotes';
$field140->column = 'currency_id';
$field140->uitype = 117;
$field140->typeofdata = 'I~O';
$field140->displaytype = 3;

$blockInstance->addField($field140);

$filter1 = new Vtiger_Filter();
$filter1->name = 'ALL';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field4, 1)->addField($field2, 2)->addField($field8, 3)->addField($field66, 4)->addField($field9, 5);
  
$moduleInstance->setDefaultSharing();
$moduleInstance->initWebservice();

// Adds the Updates link to the vertical navigation menu on the right.
ModTracker::enableTrackingForModule($moduleInstance->id);

$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Calendar'), 'Activities', array('add'), 'get_activities');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('add', 'select'), 'get_attachments');
