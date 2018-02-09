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



// vtiger_entity_name

//ini_set('error_reporting', E_ALL);

//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');

$quotesInstance = Vtiger_Module::getInstance('Quotes');
$quotesblock1 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $quotesInstance);
$quotesblock3 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $quotesInstance);

$field01 = Vtiger_Field::getInstance('bill_street', $quotesInstance);
$field02 = Vtiger_Field::getInstance('bill_city', $quotesInstance);
$field03 = Vtiger_Field::getInstance('bill_state', $quotesInstance);
$field04 = Vtiger_Field::getInstance('bill_code', $quotesInstance);
$field05 = Vtiger_Field::getInstance('bill_country', $quotesInstance);
$field06 = Vtiger_Field::getInstance('bill_pobox', $quotesInstance);
$field07 = Vtiger_Field::getInstance('ship_street', $quotesInstance);
$field08 = Vtiger_Field::getInstance('ship_city', $quotesInstance);
$field09 = Vtiger_Field::getInstance('ship_state', $quotesInstance);
$field010 = Vtiger_Field::getInstance('ship_code', $quotesInstance);
$field011 = Vtiger_Field::getInstance('ship_country', $quotesInstance);
$field015 = Vtiger_Field::getInstance('ship_pobox', $quotesInstance);

//adding fields to Quotes module LBL_QUOTE_INFORMATION
$field012 = Vtiger_Field::getInstance('business_line_est', $quotesInstance);
if ($field012) {
    echo "<li>The business_line_est field already exists</li><br>";
} else {
    $field012 = new Vtiger_Field();
    $field012->label = 'LBL_QUOTES_BUSINESSLINE';
    $field012->name = 'business_line_est';
    $field012->table = 'vtiger_quotescf';
    $field012->column = 'business_line_est';
    $field012->columntype='VARCHAR(200)';
    $field012->uitype = 16;
    $field012->typeofdata = 'V~O';
    $field012->displaytype = 1;
    $field012->quickcreate = 3;
    
    
    $quotesblock1->addField($field012);
    $field012->setPicklistValues(['Local Move', 'Interstate Move', 'Commercial Move', 'Intrastate Move', 'HHG - International Air', 'HHG - International Sea', 'Commercial - Distribution', 'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management', 'Commercial - Project', 'Auto Transportation']);
    
    $field_business_line = Vtiger_Field::getInstance('business_line', $moduleInstance);
    if ($field_business_line) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field_business_line->id);
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_quotescf` SET business_line_est=business_line");
    }
}
$field013 = Vtiger_Field::getInstance('orders_id', $quotesInstance);
if ($field013) {
    echo "<li>The orders_id field already exists</li><br>";
} else {
    $field013 = new Vtiger_Field();
    $field013->label = 'LBL_QUOTES_ORDERSID';
    $field013->name = 'orders_id';
    $field013->table = 'vtiger_quotes';
    $field013->column = 'orders_id';
    $field013->columntype='INT(19)';
    $field013->uitype = 10;
    $field013->typeofdata = 'V~O';
    $field013->displaytype = 1;

    $quotesblock1->addField($field013);
    $field013->setRelatedModules(array('Orders'));
}

$field014 = Vtiger_Field::getInstance('is_primary', $quotesInstance);
if ($field014) {
    echo "<li>The is_primary field already exists</li><br>";
} else {
    $field014 = new Vtiger_Field();
    $field014->label = 'LBL_QUOTES_ISPRIMARY';
    $field014->name = 'is_primary';
    $field014->table = 'vtiger_quotes';
    $field014->column = 'is_primary';
    $field014->columntype='VARCHAR(3)';
    $field014->uitype = 56;
    $field014->typeofdata = 'C~O';
    $field014->displaytype = 1;

    $quotesblock1->addField($field014);
}
//create new block LBL_QUOTES_CONTACTDETAILS
$quotesblock2 = Vtiger_Block::getInstance('LBL_QUOTES_CONTACTDETAILS', $quotesInstance);
if ($quotesblock2) {
    echo "<li>The LBL_QUOTES_CONTACTDETAILS field already exists</li><br>";
} else {
    $quotesblock2 = new Vtiger_Block();
    $quotesblock2->label = 'LBL_QUOTES_CONTACTDETAILS';
    $quotesInstance->addBlock($quotesblock2);
}

$field016 = Vtiger_Field::getInstance('LBL_QUOTES_ORIGINADDRESS1', $quotesInstance);
if ($field016) {
    echo "<li>The origin_address1 field already exists</li><br>";
} else {
    $field016 = new Vtiger_Field();
    $field016->label = 'LBL_QUOTES_ORIGINADDRESS1';
    $field016->name = 'origin_address1';
    $field016->table = 'vtiger_quotescf';
    $field016->column = 'origin_address1';
    $field016->columntype = 'VARCHAR(255)';
    $field016->uitype = 1;
    $field016->typeofdata = 'V~O~LE~50';
    $field016->displaytype = 1;

    $quotesblock3->addField($field016);
}

$field22 = Vtiger_Field::getInstance('destination_address1', $quotesInstance);
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

    $quotesblock3->addField($field22);
}
// from 51
$field23 = Vtiger_Field::getInstance('origin_address2', $quotesInstance);
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

    $quotesblock3->addField($field23);
}

$field24 = Vtiger_Field::getInstance('destination_address2', $quotesInstance);
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

    $quotesblock3->addField($field24);
}

$field25 = Vtiger_Field::getInstance('origin_city', $quotesInstance);
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

    $quotesblock3->addField($field25);
}

$field26 = Vtiger_Field::getInstance('destination_city', $quotesInstance);
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

    $quotesblock3->addField($field26);
}

$field27 = Vtiger_Field::getInstance('origin_state', $quotesInstance);
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

    $quotesblock3->addField($field27);
}

$field28 = Vtiger_Field::getInstance('destination_state', $quotesInstance);
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

    $quotesblock3->addField($field28);
}

$field29 = Vtiger_Field::getInstance('origin_zip', $quotesInstance);
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

    $quotesblock3->addField($field29);
}

$field30 = Vtiger_Field::getInstance('destination_zip', $quotesInstance);
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

    $quotesblock3->addField($field30);
}

$field31 = Vtiger_Field::getInstance('origin_phone1', $quotesInstance);
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

    $quotesblock3->addField($field31);
}

$field32 = Vtiger_Field::getInstance('destination_phone1', $quotesInstance);
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

    $quotesblock3->addField($field32);
}

$field33 = Vtiger_Field::getInstance('origin_phone2', $quotesInstance);
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

    $quotesblock3->addField($field33);
}

$field34 = Vtiger_Field::getInstance('destination_phone2', $quotesInstance);
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

    $quotesblock3->addField($field34);
}

$quotesblock4 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $quotesInstance);
if ($quotesblock4) {
    echo "<li>The LBL_QUOTES_LOCALMOVEDETAILS field already exists</li><br>";
} else {
    $quotesblock4 = new Vtiger_Block();
    $quotesblock4->label = 'LBL_QUOTES_LOCALMOVEDETAILS';
    $quotesInstance->addBlock($quotesblock4);
}

$field35 = Vtiger_Field::getInstance('cf_1003', $quotesInstance);
if ($field35) {
    echo "<li>The cf_1003 field already exists</li><br>";
} else {
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
    $field35->quickcreate = 3;

    $quotesblock4->addField($field35);
}

$quotesblock5 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $quotesInstance);
if ($quotesblock5) {
    echo "<li>The LBL_QUOTES_INTERSTATEMOVEDETAILS field already exists</li><br>";
} else {
    $quotesblock5 = new Vtiger_Block();
    $quotesblock5->label = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
    $quotesInstance->addBlock($quotesblock5);
}

$field44 = Vtiger_Field::getInstance('cf_1005', $quotesInstance);
if ($field44) {
    echo "<li>The cf_1005 field already exists</li><br>";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_QUOTES_HOLDERFIELD2';
    $field44->name = 'cf_1005';
    $field44->table = 'vtiger_quotescf';
    $field44->column = 'cf_1005';
    $field44->columntype = 'VARCHAR(3)';
    $field44->uitype = 1;
    $field44->typeofdata = 'V~O~LE~15';
    $field44->displaytype = 1;
    $field44->presence = 1;

    $quotesblock5->addField($field44);
}

$field36 = Vtiger_Field::getInstance('weight', $quotesInstance);
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

    $quotesblock5->addField($field36);
}

$field37 = Vtiger_Field::getInstance('pickup_date', $quotesInstance);
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
    $field37->quickcreate = 0;

    $quotesblock5->addField($field37);
}

$field69 = Vtiger_Field::getInstance('pickup_time', $quotesInstance);
if ($field69) {
    echo "<li>The pickup_time field already exists</li><br>";
} else {
    $field69 = new Vtiger_Field();
    $field69->label = 'LBL_QUOTES_PICKUPTIME';
    $field69->name = 'pickup_time';
    $field69->table = 'vtiger_quotes';
    $field69->column = 'pickup_time';
    $field69->columntype = 'TIME';
    $field69->uitype = 14;
    $field69->typeofdata = 'T~O';
    $field69->displaytype = 1;
    $field69->presence = 1;

    $quotesblock5->addField($field69);
}

$field70 = Vtiger_Field::getInstance('fuel_price', $quotesInstance);
if ($field70) {
    echo "<li>The fuel_price field already exists</li><br>";
} else {
    $field70 = new Vtiger_Field();
    $field70->label = 'LBL_QUOTES_FUELPRICE';
    $field70->name = 'fuel_price';
    $field70->table = 'vtiger_quotes';
    $field70->column = 'fuel_price';
    $field70->columntype = 'DECIMAL(56,8)';
    $field70->uitype = 71;
    $field70->typeofdata = 'N~O';
    $field70->displaytype = 1;
    $field70->presence =1;

    $quotesblock5->addField($field70);
}

$field42 = Vtiger_Field::getInstance('bottom_line_discount', $quotesInstance);
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

    $quotesblock5->addField($field42);
}

$field39 = Vtiger_Field::getInstance('valuation_deductible', $quotesInstance);
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
    $field39->defaultvalue = 'Zero';
    $field39->setPicklistValues(array('60¢ /lb.', 'Zero', '$250', '$500'));

    $quotesblock5->addField($field39);
}

$field41 = Vtiger_Field::getInstance('valuation_amount', $quotesInstance);
if ($field40) {
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
    $field41->quickcreate = 0;

    $quotesblock5->addField($field41);
}

$field40 = Vtiger_Field::getInstance('full_unpack', $quotesInstance);
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

    $quotesblock5->addField($field40);
}

$field38 = Vtiger_Field::getInstance('full_pack', $quotesInstance);
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

    $quotesblock5->addField($field38);
}


$field76 = Vtiger_Field::getInstance('rate_estimate', $quotesInstance);
if ($field76) {
    echo "<li>The rate_estimate field already exists</li><br>";
} else {
    $field76 = new Vtiger_Field();
    $field76->label = 'LBL_QUOTES_RATEESTIMATE';
    $field76->name = 'rate_estimate';
    $field76->table = 'vtiger_quotes';
    $field76->column = 'rate_estimate';
    $field76->columntype = 'DECIMAL(56,8)';
    $field76->uitype = 71;
    $field76->typeofdata = 'N~O';
    $field76->displaytype = 1;
    $field76->presence = 1;

    $quotesblock5->addField($field76);
}

$field43 = Vtiger_Field::getInstance('interstate_mileage', $quotesInstance);
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

    $quotesblock5->addField($field43);
}

// #6
$quotesblock6 = Vtiger_Block::getInstance('LBL_QUOTES_COMMERCIALMOVEDETAILS', $quotesInstance);
if ($quotesblock6) {
    echo "<li>The LBL_QUOTES_COMMERCIALMOVEDETAILS field already exists</li><br>";
} else {
    $quotesblock6 = new Vtiger_Block();
    $quotesblock6->label = 'LBL_QUOTES_COMMERCIALMOVEDETAILS';
    $quotesInstance->addBlock($quotesblock6);
}

$field65 = Vtiger_Field::getInstance('cf_1007', $quotesInstance);
if ($field65) {
    echo "<li>The cf_1007 field already exists</li><br>";
} else {
    $field65 = new Vtiger_Field();
    $field65->label = 'LBL_QUOTES_HOLDERFIELD3';
    $field65->name = 'cf_1007';
    $field65->table = 'vtiger_quotescf';
    $field65->column = 'cf_1007';
    $field35->columntype = 'VARCHAR(15)';
    $field65->uitype = 1;
    $field65->typeofdata = 'V~O~LE~15';
    $field65->displaytype = 1;
    $field65->quickcreate = 3;

    $quotesblock6->addField($field65);
}

$quotesblock7 = Vtiger_Block::getInstance('LBL_QUOTES_VALUATIONDETAILS', $quotesInstance);
if ($quotesblock7) {
    echo "<li>The LBL_QUOTES_VALUATIONDETAILS field already exists</li><br>";
} else {
    $quotesblock7 = new Vtiger_Block();
    $quotesblock7->label = 'LBL_QUOTES_VALUATIONDETAILS';
    $quotesInstance->addBlock($quotesblock7);
}

$quotesblock8 = Vtiger_Block::getInstance('LBL_QUOTES_PACKING', $quotesInstance);
if ($quotesblock8) {
    echo "<li>The LBL_QUOTES_PACKING field already exists</li><br>";
} else {
    $quotesblock8 = new Vtiger_Block();
    $quotesblock8->label = 'LBL_QUOTES_PACKING';
    $quotesInstance->addBlock($quotesblock8);
}

$quotesblock9 = Vtiger_Block::getInstance('LBL_QUOTES_OTPACKING', $quotesInstance);
if ($quotesblock9) {
    echo "<li>The LBL_QUOTES_OTPACKING field already exists</li><br>";
} else {
    $quotesblock9 = new Vtiger_Block();
    $quotesblock9->label = 'LBL_QUOTES_OTPACKING';
    $quotesInstance->addBlock($quotesblock9);
}

$quotesblock10 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $quotesInstance);
if ($quotesblock10) {
    echo "<li>The LBL_QUOTES_SITDETAILS field already exists</li><br>";
} else {
    $quotesblock10 = new Vtiger_Block();
    $quotesblock10->label = 'LBL_QUOTES_SITDETAILS';
    $quotesInstance->addBlock($quotesblock10);
}


$field77 = Vtiger_Field::getInstance('sit_origin_date_in', $quotesInstance);
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

    $quotesblock10->addField($field77);
}

$field78 = Vtiger_Field::getInstance('sit_dest_date_in', $quotesInstance);
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

    $quotesblock10->addField($field78);
}

$field79 = Vtiger_Field::getInstance('sit_origin_pickup_date', $quotesInstance);
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

    $quotesblock10->addField($field79);
}

$field80 = Vtiger_Field::getInstance('sit_dest_delivery_date', $quotesInstance);
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

    $quotesblock10->addField($field80);
}

$field81 = Vtiger_Field::getInstance('sit_origin_weight', $quotesInstance);
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

    $quotesblock10->addField($field81);
}

$field82 = Vtiger_Field::getInstance('sit_dest_weight', $quotesInstance);
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

    $quotesblock10->addField($field82);
}

$field83 = Vtiger_Field::getInstance('sit_origin_zip', $quotesInstance);
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

    $quotesblock10->addField($field83);
}

$field84 = Vtiger_Field::getInstance('sit_dest_zip', $quotesInstance);
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

    $quotesblock10->addField($field84);
}

$field85 = Vtiger_Field::getInstance('sit_origin_miles', $quotesInstance);
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

    $quotesblock10->addField($field85);
}

$field86 = Vtiger_Field::getInstance('sit_dest_miles', $quotesInstance);
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

    $quotesblock10->addField($field86);
}

$field87 = Vtiger_Field::getInstance('sit_origin_number_days', $quotesInstance);
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

    $quotesblock10->addField($field87);
}

$field88 = Vtiger_Field::getInstance('sit_dest_number_days', $quotesInstance);
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

    $quotesblock10->addField($field88);
}

$field89 = Vtiger_Field::getInstance('sit_origin_first_day', $quotesInstance);
if ($field89) {
    echo "<li>The sit_origin_number_days field already exists</li><br>";
} else {
    $field89 = new Vtiger_Field();
    $field89->label = 'LBL_QUOTES_SITORIGINFIRSTDAYRATE';
    $field89->name = 'sit_origin_first_day';
    $field89->table = 'vtiger_quotes';
    $field89->column = 'sit_origin_first_day';
    $field89->columntype = 'DECIMAL(10,3)';
    $field89->uitype = 71;
    $field89->typeofdata = 'N~O';
    $field89->displaytype = 1;
    $field89->presence = 1;

    $quotesblock10->addField($field89);
}

$field90 = Vtiger_Field::getInstance('sit_dest_first_day', $quotesInstance);
if ($field90) {
    echo "<li>The sit_dest_first_day field already exists</li><br>";
} else {
    $field90 = new Vtiger_Field();
    $field90->label = 'LBL_QUOTES_SITDESTINATIONFIRSTDAYRATE';
    $field90->name = 'sit_dest_first_day';
    $field90->table = 'vtiger_quotes';
    $field90->column = 'sit_dest_first_day';
    $field90->columntype = 'DECIMAL(10,3)';
    $field90->uitype = 71;
    $field90->typeofdata = 'N~O';
    $field90->displaytype = 1;
    $field90->presence = 1;

    $quotesblock10->addField($field90);
}

$field91 = Vtiger_Field::getInstance('sit_origin_first_day_cost', $quotesInstance);
if ($field91) {
    echo "<li>The sit_origin_first_day_cost field already exists</li><br>";
} else {
    $field91 = new Vtiger_Field();
    $field91->label = 'LBL_QUOTES_SITORIGINFIRSTDAYCOST';
    $field91->name = 'sit_origin_first_day_cost';
    $field91->table = 'vtiger_quotes';
    $field91->column = 'sit_origin_first_day_cost';
    $field91->columntype = 'DECIMAL(10,3)';
    $field91->uitype = 71;
    $field91->typeofdata = 'N~O';
    $field91->displaytype = 1;
    $field91->presence = 1;

    $quotesblock10->addField($field91);
}

$field92 = Vtiger_Field::getInstance('sit_dest_first_day_cost', $quotesInstance);
if ($field92) {
    echo "<li>The sit_dest_first_day_cost field already exists</li><br>";
} else {
    $field92 = new Vtiger_Field();
    $field92->label = 'LBL_QUOTES_SITDESTINATIONFIRSTDAYCOST';
    $field92->name = 'sit_dest_first_day_cost';
    $field92->table = 'vtiger_quotes';
    $field92->column = 'sit_dest_first_day_cost';
    $field92->columntype = 'DECIMAL(10,3)';
    $field92->uitype = 71;
    $field92->typeofdata = 'N~O';
    $field92->displaytype = 1;
    $field92->presence = 1;

    $quotesblock10->addField($field92);
}


$field93 = Vtiger_Field::getInstance('sit_origin_sec_day', $quotesInstance);
if ($field93) {
    echo "<li>The sit_origin_sec_day field already exists</li><br>";
} else {
    $field93 = new Vtiger_Field();
    $field93->label = 'LBL_QUOTES_SITORIGINSECONDDAYRATE';
    $field93->name = 'sit_origin_sec_day';
    $field93->table = 'vtiger_quotes';
    $field93->column = 'sit_origin_sec_day';
    $field93->columntype = 'DECIMAL(10,3)';
    $field93->uitype = 71;
    $field93->typeofdata = 'N~O';
    $field93->displaytype = 1;
    $field93->presence = 1;

    $quotesblock10->addField($field93);
}

$field94 = Vtiger_Field::getInstance('sit_dest_sec_day', $quotesInstance);
if ($field94) {
    echo "<li>The sit_dest_sec_day field already exists</li><br>";
} else {
    $field94 = new Vtiger_Field();
    $field94->label = 'LBL_QUOTES_SITDESTINATIONSECONDDAYRATE';
    $field94->name = 'sit_dest_sec_day';
    $field94->table = 'vtiger_quotes';
    $field94->column = 'sit_dest_sec_day';
    $field94->columntype = 'DECIMAL(10,3)';
    $field94->uitype = 71;
    $field94->typeofdata = 'N~O';
    $field94->displaytype = 1;
    $field94->presence = 1;

    $quotesblock10->addField($field94);
}

$field95 = Vtiger_Field::getInstance('sit_origin_sec_day_cost', $quotesInstance);
if ($field95) {
    echo "<li>The sit_origin_sec_day_cost field already exists</li><br>";
} else {
    $field95 = new Vtiger_Field();
    $field95->label = 'LBL_QUOTES_SITORIGINSECONDDAYCOST';
    $field95->name = 'sit_origin_sec_day_cost';
    $field95->table = 'vtiger_quotes';
    $field95->column = 'sit_origin_sec_day_cost';
    $field95->columntype = 'DECIMAL(10,3)';
    $field95->uitype = 71;
    $field95->typeofdata = 'N~O';
    $field95->displaytype = 1;
    $field95->presence = 1;

    $quotesblock10->addField($field95);
}

$field96 = Vtiger_Field::getInstance('sit_dest_sec_day_cost', $quotesInstance);
if ($field96) {
    echo "<li>The sit_dest_sec_day_cost field already exists</li><br>";
} else {
    $field96 = new Vtiger_Field();
    $field96->label = 'LBL_QUOTES_SITDESTINATIONSECONDDAYCOST';
    $field96->name = 'sit_dest_sec_day_cost';
    $field96->table = 'vtiger_quotes';
    $field96->column = 'sit_dest_sec_day_cost';
    $field96->columntype = 'DECIMAL(10,3)';
    $field96->uitype = 71;
    $field96->typeofdata = 'N~O';
    $field96->displaytype = 1;
    $field96->presence = 1;

    $quotesblock10->addField($field96);
}

$field97 = Vtiger_Field::getInstance('sit_origin_pickup_delivery', $quotesInstance);
if ($field97) {
    echo "<li>The sit_origin_pickup_delivery field already exists</li><br>";
} else {
    $field97 = new Vtiger_Field();
    $field97->label = 'LBL_QUOTES_SITORIGINPICKUPDELIVERY';
    $field97->name = 'sit_origin_pickup_delivery';
    $field97->table = 'vtiger_quotes';
    $field97->column = 'sit_origin_pickup_delivery';
    $field97->columntype = 'DECIMAL(10,3)';
    $field97->uitype = 71;
    $field97->typeofdata = 'N~O';
    $field97->displaytype = 1;
    $field97->presence = 1;

    $quotesblock10->addField($field97);
}

$field98 = Vtiger_Field::getInstance('sit_dest_pickup_delivery', $quotesInstance);
if ($field98) {
    echo "<li>The sit_dest_pickup_delivery field already exists</li><br>";
} else {
    $field98 = new Vtiger_Field();
    $field98->label = 'LBL_QUOTES_SITDESTINATIONPICKUPDELIVERY';
    $field98->name = 'sit_dest_pickup_delivery';
    $field98->table = 'vtiger_quotes';
    $field98->column = 'sit_dest_pickup_delivery';
    $field98->columntype = 'DECIMAL(10,3)';
    $field98->uitype = 71;
    $field98->typeofdata = 'N~O';
    $field98->displaytype = 1;
    $field98->presence = 1;

    $quotesblock10->addField($field98);
}

$field99 = Vtiger_Field::getInstance('sit_origin_fuel_percent', $quotesInstance);
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

    $quotesblock10->addField($field99);
}

$field100 = Vtiger_Field::getInstance('sit_dest_fuel_percent', $quotesInstance);
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

    $quotesblock10->addField($field100);
}

$field101 = Vtiger_Field::getInstance('sit_origin_fuel_surcharge', $quotesInstance);
if ($field101) {
    echo "<li>The sit_origin_fuel_surcharge field already exists</li><br>";
} else {
    $field101 = new Vtiger_Field();
    $field101->label = 'LBL_QUOTES_SITORIGINFUELSURCHARGE';
    $field101->name = 'sit_origin_fuel_surcharge';
    $field101->table = 'vtiger_quotes';
    $field101->column = 'sit_origin_fuel_surcharge';
    $field101->columntype = 'DECIMAL(10,3)';
    $field101->uitype = 71;
    $field101->typeofdata = 'N~O';
    $field101->displaytype = 1;
    $field101->presence =1;

    $quotesblock10->addField($field101);
}

$field102 = Vtiger_Field::getInstance('sit_dest_fuel_surcharge', $quotesInstance);
if ($field102) {
    echo "<li>The sit_dest_fuel_surcharge field already exists</li><br>";
} else {
    $field102 = new Vtiger_Field();
    $field102->label = 'LBL_QUOTES_SITDESTINATIONFUELSURCHARGE';
    $field102->name = 'sit_dest_fuel_surcharge';
    $field102->table = 'vtiger_quotes';
    $field102->column = 'sit_dest_fuel_surcharge';
    $field102->columntype = 'DECIMAL(10,3)';
    $field102->uitype = 71;
    $field102->typeofdata = 'N~O';
    $field102->displaytype = 1;
    $field102->presence = 1;

    $quotesblock10->addField($field102);
}

$field103 = Vtiger_Field::getInstance('sit_origin_irr_percent', $quotesInstance);
if ($field103) {
    echo "<li>The sit_origin_irr_percent field already exists</li><br>";
} else {
    $field103 = new Vtiger_Field();
    $field103->label = 'LBL_QUOTES_SITORIGINIRRPERCENT';
    $field103->name = 'sit_origin_irr_percent';
    $field103->table = 'vtiger_quotes';
    $field103->column = 'sit_origin_irr_percent';
    $field103->columntype = 'DECIMAL(10,3)';
    $field103->uitype = 7;
    $field103->typeofdata = 'N~O';
    $field103->displaytype = 1;
    $field103->presence = 1;

    $quotesblock10->addField($field103);
}

$field104 = Vtiger_Field::getInstance('sit_dest_irr_percent', $quotesInstance);
if ($field104) {
    echo "<li>The sit_dest_irr_percent field already exists</li><br>";
} else {
    $field104 = new Vtiger_Field();
    $field104->label = 'LBL_QUOTES_SITDESTINATIONIRRPERCENT';
    $field104->name = 'sit_dest_irr_percent';
    $field104->table = 'vtiger_quotes';
    $field104->column = 'sit_dest_irr_percent';
    $field104->columntype = 'DECIMAL(10,3)';
    $field104->uitype = 7;
    $field104->typeofdata = 'N~O';
    $field104->displaytype = 1;
    $field104->presence = 1;

    $quotesblock10->addField($field104);
}

$field105 = Vtiger_Field::getInstance('sit_origin_irr', $quotesInstance);
if ($field105) {
    echo "<li>The sit_origin_irr field already exists</li><br>";
} else {
    $field105 = new Vtiger_Field();
    $field105->label = 'LBL_QUOTES_SITORIGINIRR';
    $field105->name = 'sit_origin_irr';
    $field105->table = 'vtiger_quotes';
    $field105->column = 'sit_origin_irr';
    $field105->columntype = 'DECIMAL(10,3)';
    $field105->uitype = 71;
    $field105->typeofdata = 'N~O';
    $field105->displaytype = 1;
    $field105->presence = 1;

    $quotesblock10->addField($field105);
}

$field106 = Vtiger_Field::getInstance('sit_dest_irr', $quotesInstance);
if ($field106) {
    echo "<li>The origin_address2 field already exists</li><br>";
} else {
    $field106 = new Vtiger_Field();
    $field106->label = 'LBL_QUOTES_SITDESTINATIONIRR';
    $field106->name = 'sit_dest_irr';
    $field106->table = 'vtiger_quotes';
    $field106->column = 'sit_dest_irr';
    $field106->columntype = 'DECIMAL(10,3)';
    $field106->uitype = 71;
    $field106->typeofdata = 'N~O';
    $field106->displaytype = 1;
    $field106->presence = 1;

    $quotesblock10->addField($field106);
}

$field107 = Vtiger_Field::getInstance('sit_origin_overtime', $quotesInstance);
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

    $quotesblock10->addField($field107);
}

$field108 = Vtiger_Field::getInstance('sit_dest_overtime', $quotesInstance);
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

    $quotesblock10->addField($field108);
}


$quotesblock11 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $quotesInstance);
if ($quotesblock11) {
    echo "<li>The LBL_QUOTES_ACCESSORIALDETAILS field already exists</li><br>";
} else {
    $quotesblock11 = new Vtiger_Block();
    $quotesblock11->label = 'LBL_QUOTES_ACCESSORIALDETAILS';
    $quotesInstance->addBlock($quotesblock11);
}

$field109 = Vtiger_Field::getInstance('acc_shuttle_origin_weight', $quotesInstance);
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

    $quotesblock11->addField($field109);
}

$field110 = Vtiger_Field::getInstance('acc_shuttle_dest_weight', $quotesInstance);
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

    $quotesblock11->addField($field110);
}

$field111 = Vtiger_Field::getInstance('acc_shuttle_origin_applied', $quotesInstance);
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

    $quotesblock11->addField($field111);
}

$field112 = Vtiger_Field::getInstance('acc_shuttle_dest_applied', $quotesInstance);
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

    $quotesblock11->addField($field112);
}

$field113 = Vtiger_Field::getInstance('acc_shuttle_origin_ot', $quotesInstance);
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

    $quotesblock11->addField($field113);
}

$field114 = Vtiger_Field::getInstance('acc_shuttle_dest_ot', $quotesInstance);
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

    $quotesblock11->addField($field114);
}

$field115 = Vtiger_Field::getInstance('acc_shuttle_origin_over25', $quotesInstance);
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

    $quotesblock11->addField($field115);
}

$field116 = Vtiger_Field::getInstance('acc_shuttle_dest_over25', $quotesInstance);
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

    $quotesblock11->addField($field116);
}

$field117 = Vtiger_Field::getInstance('acc_shuttle_origin_miles', $quotesInstance);
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

    $quotesblock11->addField($field117);
}

$field118 = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $quotesInstance);
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

    $quotesblock11->addField($field118);
}

$field119 = Vtiger_Field::getInstance('acc_ot_origin_weight', $quotesInstance);
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

    $quotesblock11->addField($field119);
}

$field120 = Vtiger_Field::getInstance('acc_ot_dest_weight', $quotesInstance);
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

    $quotesblock11->addField($field120);
}

$field121 = Vtiger_Field::getInstance('acc_ot_origin_applied', $quotesInstance);
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

    $quotesblock11->addField($field121);
}

$field122 = Vtiger_Field::getInstance('acc_ot_dest_applied', $quotesInstance);
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

    $quotesblock11->addField($field122);
}

$field123 = Vtiger_Field::getInstance('acc_selfstg_origin_weight', $quotesInstance);
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

    $quotesblock11->addField($field123);
}

$field124 = Vtiger_Field::getInstance('acc_selfstg_dest_weight', $quotesInstance);
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

    $quotesblock11->addField($field124);
}

$field125 = Vtiger_Field::getInstance('acc_selfstg_origin_applied', $quotesInstance);
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

    $quotesblock11->addField($field125);
}

$field126 = Vtiger_Field::getInstance('acc_selfstg_dest_applied', $quotesInstance);
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

    $quotesblock11->addField($field126);
}

$field127 = Vtiger_Field::getInstance('acc_selfstg_origin_ot', $quotesInstance);
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

    $quotesblock11->addField($field127);
}

$field128 = Vtiger_Field::getInstance('acc_selfstg_dest_ot', $quotesInstance);
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

    $quotesblock11->addField($field128);
}

$field129 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $quotesInstance);
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

    $quotesblock11->addField($field129);
}

$field130 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $quotesInstance);
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

    $quotesblock11->addField($field130);
}

$field131 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $quotesInstance);
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

    $quotesblock11->addField($field131);
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

    $quotesblock11->addField($field132);
}

$field133 = Vtiger_Field::getInstance('acc_wait_origin_hours', $quotesInstance);
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

    $quotesblock11->addField($field133);
}

$field134 = Vtiger_Field::getInstance('acc_wait_dest_hours', $quotesInstance);
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

    $quotesblock11->addField($field134);
}

$field135 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $quotesInstance);
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

    $quotesblock11->addField($field135);
}

$field136 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $quotesInstance);
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

    $quotesblock11->addField($field136);
}

$field137 = Vtiger_Field::getInstance('effective_date', $quotesInstance);
if ($field137) {
    echo "<li>The effective_date field already exists</li><br>";
} else {
    $field137 = new Vtiger_Field();
    $field137->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field137->name = 'effective_date';
    $field137->table = 'vtiger_quotes';
    $field137->column = 'effective_date';
    $field137->columntype = 'DATE';
    $field137->uitype = 5;
    $field137->typeofdata = 'D~O';
    $field137->displaytype = 1;

    $quotesblock4->addField($field137);
}
// this is in remove-is discountable now
// $field138 = Vtiger_Field::getInstance('local_bl_discount',$quotesInstance);
// if($field138) {
    // echo "<li>The local_bl_discount field already exists</li><br>";
// }
// else {
// $field138 = new Vtiger_Field();
// $field138->label = 'LBL_QUOTES_LOCALBLDISCOUNT';
// $field138->name = 'local_bl_discount';
// $field138->table = 'vtiger_quotes';
// $field138->column = 'local_bl_discount';
// $field138->columntype = 'DECIMAL(12,3)	';
// $field138->uitype = 7;
// $field138->typeofdata = 'N~O';
// $field138->displaytype = 1;

// $quotesblock4->addField($field138);
// }


if (!Vtiger_Utils::CheckTable('vtiger_misc_accessorials')) {
    echo "<li>creating vtiger_misc_accessorials </li><br>";
    Vtiger_Utils::CreateTable('vtiger_misc_accessorials',
                              '(quoteid INT(10),
                                description TEXT,
                                charge DECIMAL(10,2),
                                qty INT(10),
                                discounted VARCHAR(3),
                                discount DECIMAL(5,3),
                                line_item_id INT(10),
                                charge_type VARCHAR(30)
                                )', true);
}

if (!Vtiger_Utils::CheckTable('vtiger_misc_accessorials_seq')) {
    echo "<li>creating vtiger_misc_accessorials_seq </li><br>";
    Vtiger_Utils::CreateTable('vtiger_misc_accessorials_seq',
                              '(id INT(11)
                                )', true);
}

Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock3->id . ', sequence = 1, presence = 1'. ' WHERE fieldid = ' . $field07->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock3->id . ', sequence = 2, presence = 1'. ' WHERE fieldid = ' . $field08->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock3->id . ', sequence = 3, presence = 1'. ' WHERE fieldid = ' . $field09->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock3->id . ', sequence = 4, presence = 1'. ' WHERE fieldid = ' . $field010->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock3->id . ', sequence = 5, presence = 1'. ' WHERE fieldid = ' . $field011->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock3->id . ', sequence = 6, presence = 1'. ' WHERE fieldid = ' . $field015->id);

//moving core fields to block in Quotes LBL_CONTACTSDETAILS
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock2->id . ', sequence = 1, presence = 2'. ' WHERE fieldid = ' . $field01->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock2->id . ', sequence = 2, presence = 2'. ' WHERE fieldid = ' . $field02->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock2->id . ', sequence = 3, presence = 2'. ' WHERE fieldid = ' . $field03->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock2->id . ', sequence = 4, presence = 2'. ' WHERE fieldid = ' . $field04->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock2->id . ', sequence = 5, presence = 2'. ' WHERE fieldid = ' . $field05->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $quotesblock2->id . ', sequence = 6, presence = 2'. ' WHERE fieldid = ' . $field06->id);

//insert row in vtiger_accessotials_seq
Vtiger_Utils::ExecuteQuery('INSERT `vtiger_misc_accessorials_seq` SET id = 0');

echo "<h1>Creating Tables for Local Services</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_quotes_baseplus')) {
    echo "<li>creating vtiger_quotes_baseplus </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_baseplus',
                              '(
							    estimateid INT(11),
							    serviceid INT(11),
								mileage INT(11),
								weight INT(11),
								rate DECIMAL(12,3),
								excess DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_breakpoint')) {
    echo "<li>creating vtiger_quotes_breakpoint </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_breakpoINT',
                              '(estimateid INT(11),
							    serviceid INT(11),
								mileage INT(11),
								weight INT(11),
								rate DECIMAL(12,3),
								breakpoint INT(11)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_weightmileage')) {
    echo "<li>creating vtiger_quotes_weightmileage </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_weightmileage',
                              '(estimateid INT(11),
							    serviceid INT(11),
								mileage INT(11),
								weight INT(11),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_countycharge')) {
    echo "<li>creating vtiger_quotes_countycharge </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_countycharge',
                              '(estimateid INT(11),
							    serviceid INT(11),
								county VARCHAR(50),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_hourlyset')) {
    echo "<li>creating vtiger_quotes_hourlyset </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_hourlyset',
                              '(estimateid INT(11),
							    serviceid INT(11),
								men INT(11),
								vans INT(11),
								hours DECIMAL(12,2),
								traveltime DECIMAL(12,2),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_perunit')) {
    echo "<li>creating vtiger_quotes_perunit </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_perunit',
                              '(estimateid INT(11),
							    serviceid INT(11),
								qty1 DECIMAL(12,3),
								qty2 DECIMAL(12,3),
								rate DECIMAL(12,3),
								ratetype VARCHAR(50)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_bulky')) {
    echo "<li>creating vtiger_quotes_bulky </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_bulky',
                              '(estimateid INT(11),
							    serviceid INT(11),
								description VARCHAR(75),
								qty INT(11),
								weight INT(11),
								rate DECIMAL(12,3),
								bulky_id INT(11)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_valuation')) {
    echo "<li>creating vtiger_quotes_valuation </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_valuation',
                              '(estimateid INT(11),
							    serviceid INT(11),
								released TINYINT(1),
								released_amount DECIMAL(4,3),
								amount INT(11),
								deductible INT(11),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_packing')) {
    echo "<li>creating vtiger_quotes_packing </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_packing',
                              '(estimateid INT(11),
							    serviceid INT(11),
								name VARCHAR(50),
								container_qty INT(11),
								container_rate DECIMAL(12,3),
								pack_qty INT(11),
								pack_rate DECIMAL(12,3),
								unpack_qty INT(11),
								unpack_rate DECIMAL(12,3),
								packing_id INT(11)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_crating')) {
    echo "<li>creating vtiger_quotes_crating </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_crating',
                              '(estimateid INT(11),
							    serviceid INT(11),
								crateid VARCHAR(50),
								description VARCHAR(50),
								crating_qty INT(11),
								crating_rate DECIMAL(12,3),
								uncrating_qty INT(11),
								uncrating_rate DECIMAL(12,3),
								length INT(11),
								width INT(11),
								height INT(11),
								inches_added INT(11),
								line_item_id INT(11)
								)', true);
}

echo "</ol>";
echo "<h1>Script Completed</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";