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



$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

$opportunitiesInstance = Vtiger_Module::getInstance('Opportunities');
if ($opportunitiesInstance) {
    echo "Module exists";
} else {
    $opportunitiesInstance = new Vtiger_Module();
    $opportunitiesInstance->name = 'Opportunities';
    $opportunitiesInstance->save();
}

$opportunitiesblock1 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $opportunitiesInstance);
if ($opportunitiesblock1) {
    echo "<li>The LBL_OPPORTUNITY_INFORMATION field already exists</li><br>";
} else {
    $opportunitiesblock1 = new Vtiger_Block();
    $opportunitiesblock1->label = 'LBL_OPPORTUNITY_INFORMATION';
    $opportunitiesInstance->addBlock($opportunitiesblock1);
}

$field1 = Vtiger_Field::getInstance('potentialname', $opportunitiesInstance);
if ($field1) {
    echo "<li>The subject field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'Potential Name';
    $field1->name = 'potentialname';
    $field1->table = 'vtiger_potential';
    $field1->column = 'potentialname';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;
    $field1->summaryfield = 1;

    $opportunitiesblock1->addField($field1);

    $opportunitiesInstance->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('potential_no', $opportunitiesInstance);
if ($field2) {
    echo "<li>The potential_no field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'Potential No';
    $field2->name = 'potential_no';
    $field2->table = 'vtiger_potential';
    $field2->column = 'potential_no';
    $field2->uitype = 4;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 3;
    $field2->presence = 2;

    $opportunitiesblock1->addField($field2);
}

$field3 = Vtiger_Field::getInstance('contact_id', $opportunitiesInstance);
if ($field3) {
    echo "<li>The contact_id field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Contact Name';
    $field3->name = 'contact_id';
    $field3->table = 'vtiger_potential';
    $field3->column = 'contact_id';
    $field3->uitype = 10;
    $field3->typeofdata = 'V~O';
    $field3->displaytype = 1;
    $field3->quickcreate = 1;
    $field3->presence = 2;
    $field3->summaryfield = 1;

    $opportunitiesblock1->addField($field3);
    $field3->setRelatedModules(array('Contacts'));
}

$field4 = Vtiger_Field::getInstance('related_to', $opportunitiesInstance);
if ($field4) {
    echo "<li>The contact_id field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'Related To';
    $field4->name = 'related_to';
    $field4->table = 'vtiger_potential';
    $field4->column = 'related_to';
    $field4->uitype = 10;
    $field4->typeofdata = 'V~O';
    $field4->displaytype = 1;
    $field4->quickcreate = 0;
    $field4->presence = 2;
    $field4->summaryfield = 1;

    $opportunitiesblock1->addField($field4);
    $field4->setRelatedModules(array('Accounts'));
}

$field5 = Vtiger_Field::getInstance('opportunity_type', $opportunitiesInstance);
if ($field5) {
    echo "<li>The contact_id field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'Type';
    $field5->name = 'opportunity_type';
    $field5->table = 'vtiger_potential';
    $field5->column = 'potentialtype';
    $field5->uitype = 15;
    $field5->typeofdata = 'V~O';
    $field5->displaytype = 1;
    $field5->quickcreate = 1;
    $field5->presence = 2;

    $opportunitiesblock1->addField($field5);
}

$field6 = Vtiger_Field::getInstance('estimate_type', $opportunitiesInstance);
if ($field6) {
    echo "<li>The estimate_type field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_POTENTIALS_ESTIMATETYPE';
    $field6->name = 'estimate_type';
    $field6->table = 'vtiger_potentialscf';
    $field6->column = 'estimate_type';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';
    $field6->displaytype = 1;
    $field6->quickcreate = 1;
    $field6->presence = 2;

    $opportunitiesblock1->addField($field6);
}

$field7 = Vtiger_Field::getInstance('business_line', $opportunitiesInstance);
if ($field7) {
    echo "<li>The business_line field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_POTENTIALS_BUSINESSLINE';
    $field7->name = 'business_line';
    $field7->table = 'vtiger_potentialscf';
    $field7->column = 'business_line';
    $field7->uitype = 16;
    $field7->typeofdata = 'V~O';
    $field7->displaytype = 1;
    $field7->quickcreate = 2;
    $field7->presence = 2;

    $opportunitiesblock1->addField($field7);
}

$field8 = Vtiger_Field::getInstance('sales_stage', $opportunitiesInstance);
if ($field8) {
    echo "<li>The sales_stage field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'Sales Stage';
    $field8->name = 'sales_stage';
    $field8->table = 'vtiger_potential';
    $field8->column = 'sales_stage';
    $field8->uitype = 15;
    $field8->typeofdata = 'V~M';
    $field8->displaytype = 1;
    $field8->quickcreate = 2;
    $field8->presence = 2;
    $field8->summaryfield = 1;

    $opportunitiesblock1->addField($field8);
}

$field9 = Vtiger_Field::getInstance('leadsource', $opportunitiesInstance);
if ($field9) {
    echo "<li>The leadsource field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'Lead Source';
    $field9->name = 'leadsource';
    $field9->table = 'vtiger_potential';
    $field9->column = 'leadsource';
    $field9->uitype = 15;
    $field9->typeofdata = 'V~O';
    $field9->displaytype = 1;
    $field9->quickcreate = 1;
    $field9->presence = 2;

    $opportunitiesblock1->addField($field9);
}

$field10 = Vtiger_Field::getInstance('amount', $opportunitiesInstance);
if ($field10) {
    echo "<li>The amount field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'Amount';
    $field10->name = 'amount';
    $field10->table = 'vtiger_potential';
    $field10->column = 'amount';
    $field10->uitype = 71;
    $field10->typeofdata = 'N~O';
    $field10->displaytype = 1;
    $field10->quickcreate = 3;
    $field10->presence = 2;
    $field10->summaryfield = 1;

    $opportunitiesblock1->addField($field10);
}

$field11 = Vtiger_Field::getInstance('assigned_user_id', $opportunitiesInstance);
if ($field11) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'Assigned To';
    $field11->name = 'assigned_user_id';
    $field11->table = 'vtiger_crmentity';
    $field11->column = 'smownerid';
    $field11->uitype = 53;
    $field11->typeofdata = 'V~M';
    $field11->displaytype = 1;
    $field11->quickcreate = 3;
    $field11->presence = 2;
    $field11->summaryfield = 1;

    $opportunitiesblock1->addField($field11);
}

$field12 = Vtiger_Field::getInstance('nextstep', $opportunitiesInstance);
if ($field12) {
    echo "<li>The nextstep field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'Next Step';
    $field12->name = 'nextstep';
    $field12->table = 'vtiger_potential';
    $field12->column = 'nextstep';
    $field12->uitype = 1;
    $field12->typeofdata = 'V~O';
    $field12->displaytype = 1;
    $field12->quickcreate = 1;
    $field12->presence = 2;

    $opportunitiesblock1->addField($field12);
}

$field13 = Vtiger_Field::getInstance('pricing_type', $opportunitiesInstance);
if ($field13) {
    echo "<li>The pricing_type field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_POTENTIALS_PRICING';
    $field13->name = 'pricing_type';
    $field13->table = 'vtiger_potentialscf';
    $field13->column = 'pricing_type';
    $field13->uitype = 16;
    $field13->typeofdata = 'V~O';
    $field13->displaytype = 1;
    $field13->quickcreate = 2;
    $field13->presence = 2;

    $opportunitiesblock1->addField($field13);
}

$field14 = Vtiger_Field::getInstance('probability', $opportunitiesInstance);
if ($field14) {
    echo "<li>The probability field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'Probability';
    $field14->name = 'probability';
    $field14->table = 'vtiger_potential';
    $field14->column = 'probability';
    $field14->uitype = 9;
    $field14->typeofdata = 'N~O';
    $field14->displaytype = 1;
    $field14->quickcreate = 1;
    $field14->presence = 2;

    $opportunitiesblock1->addField($field14);
}

$field15 = Vtiger_Field::getInstance('forecast_amount', $opportunitiesInstance);
if ($field15) {
    echo "<li>The forecast_amount field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'Forecast Amount';
    $field15->name = 'forecast_amount';
    $field15->table = 'vtiger_potential';
    $field15->column = 'forecast_amount';
    $field15->uitype = 71;
    $field15->typeofdata = 'N~O';
    $field15->displaytype = 1;
    $field15->quickcreate = 1;
    $field15->presence = 2;

    $opportunitiesblock1->addField($field15);
}

$field16 = Vtiger_Field::getInstance('created_user_id', $opportunitiesInstance);
if ($field16) {
    echo "<li>The created_user_id field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'Created By';
    $field16->name = 'created_user_id';
    $field16->table = 'vtiger_crmentity';
    $field16->column = 'smcreatorid';
    $field16->uitype = 52;
    $field16->typeofdata = 'V~O';
    $field16->displaytype = 2;
    $field16->quickcreate = 3;
    $field16->presence = 2;

    $opportunitiesblock1->addField($field16);
}

$field17 = Vtiger_Field::getInstance('createdtime', $opportunitiesInstance);
if ($field17) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'Created Time';
    $field17->name = 'createdtime';
    $field17->table = 'vtiger_crmentity';
    $field17->column = 'createdtime';
    $field17->uitype = 70;
    $field17->typeofdata = 'DT~O';
    $field17->displaytype = 2;
    $field17->quickcreate = 3;
    $field17->presence = 2;

    $opportunitiesblock1->addField($field17);
}

$field18 = Vtiger_Field::getInstance('modifiedtime', $opportunitiesInstance);
if ($field18) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'Modified Time';
    $field18->name = 'modifiedtime';
    $field18->table = 'vtiger_crmentity';
    $field18->column = 'modifiedtime';
    $field18->uitype = 70;
    $field18->typeofdata = 'DT~O';
    $field18->displaytype = 2;
    $field18->quickcreate = 3;
    $field18->presence = 2;

    $opportunitiesblock1->addField($field18);
}

$field19 = Vtiger_Field::getInstance('isconvertedfromlead', $opportunitiesInstance);
if ($field19) {
    echo "<li>The isconvertedfromlead field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'Is Converted From Lead';
    $field19->name = 'isconvertedfromlead';
    $field19->table = 'vtiger_potential';
    $field19->column = 'isconvertedfromlead';
    $field19->uitype = 56;
    $field19->typeofdata = 'C~O';
    $field19->displaytype = 2;
    $field19->quickcreate = 1;
    $field19->presence = 2;

    $opportunitiesblock1->addField($field19);
}

$field20 = Vtiger_Field::getInstance('closingdate', $opportunitiesInstance);
if ($field20) {
    echo "<li>The closingdate field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'Expected Close Date';
    $field20->name = 'closingdate';
    $field20->table = 'vtiger_potential';
    $field20->column = 'closingdate';
    $field20->uitype = 23;
    $field20->typeofdata = 'D~M';
    $field20->displaytype = 1;
    $field20->quickcreate = 2;
    $field20->presence = 2;
    $field20->summaryfield = 1;

    $opportunitiesblock1->addField($field20);
}

$opportunitiesblock2 = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $opportunitiesInstance);
if ($opportunitiesblock2) {
    echo "<li>The LBL_POTENTIALS_ADDRESSDETAILS field already exists</li><br>";
} else {
    $opportunitiesblock2 = new Vtiger_Block();
    $opportunitiesblock2->label = 'LBL_POTENTIALS_ADDRESSDETAILS';
    $opportunitiesInstance->addBlock($opportunitiesblock2);
}

//ADD FIELDS TO opportunitiesblock2
$field21 = Vtiger_Field::getInstance('origin_address1', $opportunitiesInstance);
if ($field21) {
    echo "<li>The origin_address1 field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_POTENTIALS_ORIGINADDRESS1';
    $field21->name = 'origin_address1';
    $field21->table = 'vtiger_potentialscf';
    $field21->column = 'origin_address1';
    $field21->columntype='VARCHAR(200)';
    $field21->uitype = 1;
    $field21->typeofdata = 'V~O';
    $field21->displaytype = 1;
    $field21->quickcreate = 1;
    
    $opportunitiesblock2->addField($field21);
}

$field22 = Vtiger_Field::getInstance('destination_address1', $opportunitiesInstance);
if ($field22) {
    echo "<li>The destination_address1 field already exists</li><br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_POTENTIALS_DESTINATIONADDRESS1';
    $field22->name = 'destination_address1';
    $field22->table = 'vtiger_potentialscf';
    $field22->column = 'destination_address1';
    $field22->columntype='VARCHAR(200)';
    $field22->uitype = 1;
    $field22->typeofdata = 'V~O';
    $field22->displaytype = 1;
    $field22->quickcreate = 1;
    
    $opportunitiesblock2->addField($field22);
}

$field23 = Vtiger_Field::getInstance('origin_address2', $opportunitiesInstance);
if ($field23) {
    echo "<li>The origin_address2 field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_POTENTIALS_ORIGINADDRESS2';
    $field23->name = 'origin_address2';
    $field23->table = 'vtiger_potentialscf';
    $field23->column = 'origin_address2';
    $field23->columntype='VARCHAR(200)';
    $field23->uitype = 1;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 1;
    
    $opportunitiesblock2->addField($field23);
}

$field24 = Vtiger_Field::getInstance('destination_address2', $opportunitiesInstance);
if ($field24) {
    echo "<li>The destination_address2 field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_POTENTIALS_DESTINATIONADDRESS2';
    $field24->name = 'destination_address2';
    $field24->table = 'vtiger_potentialscf';
    $field24->column = 'destination_address2';
    $field24->columntype='VARCHAR(200)';
    $field24->uitype = 1;
    $field24->typeofdata = 'V~O';
    $field24->displaytype = 1;
    $field24->quickcreate = 1;
    
    $opportunitiesblock2->addField($field24);
}

$field25 = Vtiger_Field::getInstance('origin_city', $opportunitiesInstance);
if ($field25) {
    echo "<li>The origin_city field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_POTENTIALS_ORIGINCITY';
    $field25->name = 'origin_city';
    $field25->table = 'vtiger_potentialscf';
    $field25->column = 'origin_city';
    $field25->columntype='VARCHAR(200)';
    $field25->uitype = 1;
    $field25->typeofdata = 'V~O';
    $field25->displaytype = 1;
    $field25->quickcreate = 1;
    
    $opportunitiesblock2->addField($field25);
}


$field26 = Vtiger_Field::getInstance('destination_city', $opportunitiesInstance);
if ($field26) {
    echo "<li>The destiantion_city field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_POTENTIALS_DESTINATIONCITY';
    $field26->name = 'destination_city';
    $field26->table = 'vtiger_potentialscf';
    $field26->column = 'destination_city';
    $field26->columntype='VARCHAR(200)';
    $field26->uitype = 1;
    $field26->typeofdata = 'V~O';
    $field26->displaytype = 1;
    $field26->quickcreate = 1;
    
    $opportunitiesblock2->addField($field26);
}

$field27 = Vtiger_Field::getInstance('origin_state', $opportunitiesInstance);
if ($field27) {
    echo "<li>The origin_state field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_POTENTIALS_ORIGINSTATE';
    $field27->name = 'origin_state';
    $field27->table = 'vtiger_potentialscf';
    $field27->column = 'origin_state';
    $field27->columntype='VARCHAR(200)';
    $field27->uitype = 1;
    $field27->typeofdata = 'V~O';
    $field27->displaytype = 1;
    $field27->quickcreate = 1;
    
    $opportunitiesblock2->addField($field27);
}

$field28 = Vtiger_Field::getInstance('destiantion_state', $opportunitiesInstance);
if ($field28) {
    echo "<li>The destiantion_state field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_POTENTIALS_DESTINATIONSTATE';
    $field28->name = 'destination_state';
    $field28->table = 'vtiger_potentialscf';
    $field28->column = 'destination_state';
    $field28->columntype='VARCHAR(200)';
    $field28->uitype = 1;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 1;
    $field28->quickcreate = 1;
    
    $opportunitiesblock2->addField($field28);
}

$field29 = Vtiger_Field::getInstance('origin_zip', $opportunitiesInstance);
if ($field29) {
    echo "<li>The origin_zip field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_POTENTIALS_ORIGINZIP';
    $field29->name = 'origin_zip';
    $field29->table = 'vtiger_potentialscf';
    $field29->column = 'origin_zip';
    $field29->columntype='VARCHAR(200)';
    $field29->uitype = 1;
    $field29->typeofdata = 'V~O';
    $field29->displaytype = 1;
    $field29->quickcreate = 1;
    
    $opportunitiesblock2->addField($field29);
}

$field30 = Vtiger_Field::getInstance('destiantion_zip', $opportunitiesInstance);
if ($field30) {
    echo "<li>The destiantion_zip field already exists</li><br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_POTENTIALS_DESTINATIONZIP';
    $field30->name = 'destination_zip';
    $field30->table = 'vtiger_potentialscf';
    $field30->column = 'destination_zip';
    $field30->columntype='VARCHAR(200)';
    $field30->uitype = 1;
    $field30->typeofdata = 'V~O';
    $field30->displaytype = 1;
    $field30->quickcreate = 1;
    
    $opportunitiesblock2->addField($field30);
}


$field31 = Vtiger_Field::getInstance('origin_phone1', $opportunitiesInstance);
if ($field31) {
    echo "<li>The origin_phone1 field already exists</li><br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_POTENTIALS_ORIGINPHONE1';
    $field31->name = 'origin_phone1';
    $field31->table = 'vtiger_potentialscf';
    $field31->column = 'origin_phone1';
    $field31->columntype='VARCHAR(200)';
    $field31->uitype = 11;
    $field31->typeofdata = 'V~O';
    $field31->displaytype = 1;
    $field31->quickcreate = 1;
    
    $opportunitiesblock2->addField($field31);
}

$field32 = Vtiger_Field::getInstance('destination_phone1', $opportunitiesInstance);
if ($field32) {
    echo "<li>The destination_phone1 field already exists</li><br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_POTENTIALS_DESTINATIONPHONE1';
    $field32->name = 'destination_phone1';
    $field32->table = 'vtiger_potentialscf';
    $field32->column = 'destination_phone1';
    $field32->columntype='VARCHAR(200)';
    $field32->uitype = 11;
    $field32->typeofdata = 'V~O';
    $field32->displaytype = 1;
    $field32->quickcreate = 1;
    
    $opportunitiesblock2->addField($field32);
}

$field33 = Vtiger_Field::getInstance('origin_phone2', $opportunitiesInstance);
if ($field33) {
    echo "<li>The origin_phone2 field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_POTENTIALS_ORIGINPHONE2';
    $field33->name = 'origin_phone2';
    $field33->table = 'vtiger_potentialscf';
    $field33->column = 'origin_phone2';
    $field33->columntype='VARCHAR(200)';
    $field33->uitype = 11;
    $field33->typeofdata = 'V~O';
    $field33->displaytype = 1;
    $field33->quickcreate = 1;
    
    $opportunitiesblock2->addField($field33);
}

$field34 = Vtiger_Field::getInstance('destination_phone2', $opportunitiesInstance);
if ($field34) {
    echo "<li>The destination_phone2 field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_POTENTIALS_DESTINATIONPHONE2';
    $field34->name = 'destination_phone2';
    $field34->table = 'vtiger_potentialscf';
    $field34->column = 'destination_phone2';
    $field34->columntype='VARCHAR(200)';
    $field34->uitype = 11;
    $field34->typeofdata = 'V~O';
    $field34->displaytype = 1;
    $field34->quickcreate = 1;
    
    $opportunitiesblock2->addField($field34);
}

$field35 = Vtiger_Field::getInstance('origin_country', $opportunitiesInstance);
if ($field35) {
    echo "<li>The origin_country field already exists</li><br>";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY';
    $field35->name = 'origin_country';
    $field35->table = 'vtiger_potential';
    $field35->column = 'origin_country';
    $field35->columntype='VARCHAR(200)';
    $field35->uitype = 1;
    $field35->typeofdata = 'V~O';
    $field35->displaytype = 1;
    $field35->quickcreate = 1;
    
    $opportunitiesblock2->addField($field35);
}

$field36 = Vtiger_Field::getInstance('destination_country', $opportunitiesInstance);
if ($field36) {
    echo "<li>The destination_country field already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY';
    $field36->name = 'destination_country';
    $field36->table = 'vtiger_potential';
    $field36->column = 'destination_country';
    $field36->columntype='VARCHAR(200)';
    $field36->uitype = 1;
    $field36->typeofdata = 'V~O';
    $field36->displaytype = 1;
    $field36->quickcreate = 1;
    
    $opportunitiesblock2->addField($field36);
}

$field37 = Vtiger_Field::getInstance('origin_description', $opportunitiesInstance);
if ($field37) {
    echo "<li>The origin_description field already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_POTENTIALS_ORIGINADDRESSDESCRIPTION';
    $field37->name = 'origin_description';
    $field37->table = 'vtiger_potential';
    $field37->column = 'origin_description';
    $field37->columntype='VARCHAR(200)';
    $field37->uitype = 1;
    $field37->typeofdata = 'V~O';
    $field37->displaytype = 1;
    $field37->quickcreate = 1;
    
    $opportunitiesblock2->addField($field37);
}

$field38 = Vtiger_Field::getInstance('destination_description', $opportunitiesInstance);
if ($field38) {
    echo "<li>The destination_description field already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDESCRIPTION';
    $field38->name = 'destination_description';
    $field38->table = 'vtiger_potential';
    $field38->column = 'destination_description';
    $field38->columntype='VARCHAR(200)';
    $field38->uitype = 1;
    $field38->typeofdata = 'V~O';
    $field38->displaytype = 1;
    $field38->quickcreate = 1;
    
    $opportunitiesblock2->addField($field38);
}


$field39 = Vtiger_Field::getInstance('origin_flightsofstairs', $opportunitiesInstance);
if ($field39) {
    echo "<li>The origin_flightsofstairs field already exists</li><br>";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_POTENTIALS_ORIGINADDRESSFLIGHTSOFSTAIRS';
    $field39->name = 'origin_flightsofstairs';
    $field39->table = 'vtiger_potential';
    $field39->column = 'origin_flightsofstairs';
    $field39->columntype='INT(2)';
    $field39->uitype = 7;
    $field39->typeofdata = 'N~O';
    $field39->displaytype = 1;
    $field39->quickcreate = 1;
    
    $opportunitiesblock2->addField($field39);
}


$field40 = Vtiger_Field::getInstance('destination_flightsofstairs', $opportunitiesInstance);
if ($field40) {
    echo "<li>The destination_flightsofstairs field already exists</li><br>";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_POTENTIALS_DESTINATIONADDRESSFLIGHTSOFSTAIRS';
    $field40->name = 'destination_flightsofstairs';
    $field40->table = 'vtiger_potential';
    $field40->column = 'destination_flightsofstairs';
    $field40->columntype='INT(2)';
    $field40->uitype = 7;
    $field40->typeofdata = 'N~O';
    $field40->displaytype = 1;
    $field40->quickcreate = 1;
    
    $opportunitiesblock2->addField($field40);
}

$opportunitiesblock3 = Vtiger_Block::getInstance('LBL_POTENTIALS_DATES', $opportunitiesInstance);
if ($opportunitiesblock3) {
    echo "<li>The LBL_POTENTIALS_DATES field already exists</li><br>";
} else {
    $opportunitiesblock3 = new Vtiger_Block();
    $opportunitiesblock3->label = 'LBL_POTENTIALS_DATES';
    $opportunitiesInstance->addBlock($opportunitiesblock3);
}

$field41 = Vtiger_Field::getInstance('pack_date', $opportunitiesInstance);
if ($field41) {
    echo "<li>The pack_date field already exists</li><br>";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_POTENTIALS_PACK';
    $field41->name = 'pack_date';
    $field41->table = 'vtiger_potentialscf';
    $field41->column = 'pack_date';
    $field41->columntype='DATE';
    $field41->uitype = 5;
    $field41->typeofdata = 'D~O';
    $field41->displaytype = 1;
    $field41->quickcreate = 1;
    
    $opportunitiesblock3->addField($field41);
}

$field42 = Vtiger_Field::getInstance('pack_to_date', $opportunitiesInstance);
if ($field42) {
    echo "<li>The pack_to_date field already exists</li><br>";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_POTENTIALS_PACKTO';
    $field42->name = 'pack_to_date';
    $field42->table = 'vtiger_potentialscf';
    $field42->column = 'pack_to_date';
    $field42->columntype='DATE';
    $field42->uitype = 5;
    $field42->typeofdata = 'D~O';
    $field42->displaytype = 1;
    $field42->quickcreate = 1;
    
    $opportunitiesblock3->addField($field42);
}

$field042 = Vtiger_Field::getInstance('preffered_ppdate', $opportunitiesInstance);
if ($field042) {
    echo "<li>The preffered_ppdate field already exists</li><br>";
} else {
    $field042 = new Vtiger_Field();
    $field042->label = 'LBL_POTENTIAL_PPDATE';
    $field042->name = 'preffered_ppdate';
    $field042->table = 'vtiger_potentialscf';
    $field042->column = 'preffered_ppdate';
    $field042->columntype='DATE';
    $field042->uitype = 5;
    $field042->typeofdata = 'D~O';
    $field042->displaytype = 1;
    $field042->quickcreate = 1;
    
    $opportunitiesblock3->addField($field042);
}
$field43 = Vtiger_Field::getInstance('load_date', $opportunitiesInstance);
if ($field43) {
    echo "<li>The load_date field already exists</li><br>";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_POTENTIALS_LOAD';
    $field43->name = 'load_date';
    $field43->table = 'vtiger_potentialscf';
    $field43->column = 'load_date';
    $field43->columntype='DATE';
    $field43->uitype = 5;
    $field43->typeofdata = 'D~O';
    $field43->displaytype = 1;
    $field43->quickcreate = 1;
    
    $opportunitiesblock3->addField($field43);
}

$field44 = Vtiger_Field::getInstance('load_to_date', $opportunitiesInstance);
if ($field44) {
    echo "<li>The load_to_date field already exists</li><br>";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_POTENTIALS_LOADTO';
    $field44->name = 'load_to_date';
    $field44->table = 'vtiger_potentialscf';
    $field44->column = 'load_to_date';
    $field44->columntype='DATE';
    $field44->uitype = 5;
    $field44->typeofdata = 'D~O';
    $field44->displaytype = 1;
    $field44->quickcreate = 1;
    
    $opportunitiesblock3->addField($field44);
}

$field044 = Vtiger_Field::getInstance('preferred_pldate', $opportunitiesInstance);
if ($field044) {
    echo "<li>The preferred_pldate field already exists</li><br>";
} else {
    $field044 = new Vtiger_Field();
    $field044->label = 'LBL_POTENTIAL_PLDATE';
    $field044->name = 'preferred_pldate';
    $field044->table = 'vtiger_potentialscf';
    $field044->column = 'preferred_pldate';
    $field044->columntype='DATE';
    $field044->uitype = 5;
    $field044->typeofdata = 'D~O';
    $field044->displaytype = 1;
    $field044->quickcreate = 1;
    
    $opportunitiesblock3->addField($field044);
}

$field45 = Vtiger_Field::getInstance('deliver_date', $opportunitiesInstance);
if ($field45) {
    echo "<li>The deliver_date field already exists</li><br>";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_POTENTIALS_DELIVER';
    $field45->name = 'deliver_date';
    $field45->table = 'vtiger_potentialscf';
    $field45->column = 'deliver_date';
    $field45->columntype='DATE';
    $field45->uitype = 5;
    $field45->typeofdata = 'D~O';
    $field45->displaytype = 1;
    $field45->quickcreate = 1;
    
    $opportunitiesblock3->addField($field45);
}

$field46 = Vtiger_Field::getInstance('deliver_to_date', $opportunitiesInstance);
if ($field46) {
    echo "<li>The deliver_to_date field already exists</li><br>";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_POTENTIALS_DELIVERTO';
    $field46->name = 'deliver_to_date';
    $field46->table = 'vtiger_potentialscf';
    $field46->column = 'deliver_to_date';
    $field46->columntype='DATE';
    $field46->uitype = 5;
    $field46->typeofdata = 'D~O';
    $field46->displaytype = 1;
    $field46->quickcreate = 1;
    
    $opportunitiesblock3->addField($field46);
}

$field046 = Vtiger_Field::getInstance('preferred_pddate', $opportunitiesInstance);
if ($field046) {
    echo "<li>The preferred_pddate field already exists</li><br>";
} else {
    $field046 = new Vtiger_Field();
    $field046->label = 'LBL_POTENTIAL_DELIVER';
    $field046->name = 'preferred_pddate';
    $field046->table = 'vtiger_potentialscf';
    $field046->column = 'preferred_pddate';
    $field046->columntype='DATE';
    $field046->uitype = 5;
    $field046->typeofdata = 'D~O';
    $field046->displaytype = 1;
    $field046->quickcreate = 1;
    
    $opportunitiesblock3->addField($field046);
}

$field47 = Vtiger_Field::getInstance('survey_date', $opportunitiesInstance);
if ($field47) {
    echo "<li>The survey_date field already exists</li><br>";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_POTENTIALS_SURVEY';
    $field47->name = 'survey_date';
    $field47->table = 'vtiger_potentialscf';
    $field47->column = 'survey_date';
    $field47->columntype='DATE';
    $field47->uitype = 5;
    $field47->typeofdata = 'D~O';
    $field47->displaytype = 1;
    $field47->quickcreate = 1;
    
    $opportunitiesblock3->addField($field47);
}

$field48 = Vtiger_Field::getInstance('survey_time', $opportunitiesInstance);
if ($field48) {
    echo "<li>The survey_time field already exists</li><br>";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_POTENTIALS_SURVEYTIME';
    $field48->name = 'survey_time';
    $field48->table = 'vtiger_potentialscf';
    $field48->column = 'survey_time';
    $field48->columntype='TIME';
    $field48->uitype = 14;
    $field48->typeofdata = 'T~O';
    $field48->displaytype = 1;
    $field48->quickcreate = 1;
    
    $opportunitiesblock3->addField($field48);
}

$field49 = Vtiger_Field::getInstance('followup_date', $opportunitiesInstance);
if ($field49) {
    echo "<li>The followup_date field already exists</li><br>";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_POTENTIALS_FOLLOWUP';
    $field49->name = 'followup_date';
    $field49->table = 'vtiger_potentialscf';
    $field49->column = 'followup_date';
    $field49->columntype='DATE';
    $field49->uitype = 5;
    $field49->typeofdata = 'D~O';
    $field49->displaytype = 1;
    $field49->quickcreate = 1;
    
    $opportunitiesblock3->addField($field49);
}

$field049 = Vtiger_Field::getInstance('decision_date', $opportunitiesInstance);
if ($field049) {
    echo "<li>The decision_date field already exists</li><br>";
} else {
    $field049 = new Vtiger_Field();
    $field049->label = 'LBL_POTENTIALS_DECISION';
    $field049->name = 'decision_date';
    $field049->table = 'vtiger_potentialscf';
    $field049->column = 'decision_date';
    $field049->columntype='DATE';
    $field049->uitype = 5;
    $field049->typeofdata = 'D~O';
    $field049->displaytype = 1;
    $field049->quickcreate = 1;
    
    $opportunitiesblock3->addField($field049);
}

$opportunitiesblock4 = Vtiger_Block::getInstance('LBL_POTENTIALS_NATIONALACCOUNT', $opportunitiesInstance);
if ($opportunitiesblock4) {
    echo "<li>The LBL_POTENTIALS_NATIONALACCOUNT field already exists</li><br>";
} else {
    $opportunitiesblock4 = new Vtiger_Block();
    $opportunitiesblock4->label = 'LBL_POTENTIALS_NATIONALACCOUNT';
    $opportunitiesInstance->addBlock($opportunitiesblock4);
}

$field50 = Vtiger_Field::getInstance('street', $opportunitiesInstance);
if ($field50) {
    echo "<li>The street field already exists</li><br>";
} else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_POTENTIALS_STREET';
    $field50->name = 'street';
    $field50->table = 'vtiger_potential';
    $field50->column = 'street';
    $field50->columntype='VARCHAR(200)';
    $field50->uitype = 21;
    $field50->typeofdata = 'V~O';
    $field50->displaytype = 1;
    $field50->quickcreate = 1;

    $opportunitiesblock4->addField($field50);
}

$field51 = Vtiger_Field::getInstance('pobox', $opportunitiesInstance);
if ($field51) {
    echo "<li>The pobox field already exists</li><br>";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_POTENTIALS_POBOX';
    $field51->name = 'pobox';
    $field51->table = 'vtiger_potential';
    $field51->column = 'pobox';
    $field51->columntype='VARCHAR(200)';
    $field51->uitype = 1;
    $field51->typeofdata = 'V~O';
    $field51->displaytype = 1;
    $field51->quickcreate = 1;

    $opportunitiesblock4->addField($field51);
}

$field52 = Vtiger_Field::getInstance('city', $opportunitiesInstance);
if ($field52) {
    echo "<li>The city field already exists</li><br>";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_POTENTIALS_CITY';
    $field52->name = 'city';
    $field52->table = 'vtiger_potential';
    $field52->column = 'city';
    $field52->columntype='VARCHAR(200)';
    $field52->uitype = 1;
    $field52->typeofdata = 'V~O';
    $field52->displaytype = 1;
    $field52->quickcreate = 1;

    $opportunitiesblock4->addField($field52);
}

$field53 = Vtiger_Field::getInstance('zip', $opportunitiesInstance);
if ($field53) {
    echo "<li>The zip field already exists</li><br>";
} else {
    $field53 = new Vtiger_Field();
    $field53->label = 'LBL_POTENTIALS_ZIP';
    $field53->name = 'zip';
    $field53->table = 'vtiger_potential';
    $field53->column = 'zip';
    $field53->columntype='VARCHAR(200)';
    $field53->uitype = 1;
    $field53->typeofdata = 'V~O';
    $field53->displaytype = 1;
    $field53->quickcreate = 1;

    $opportunitiesblock4->addField($field53);
}

$field54 = Vtiger_Field::getInstance('state', $opportunitiesInstance);
if ($field54) {
    echo "<li>The state field already exists</li><br>";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'LBL_POTENTIALS_STATE';
    $field54->name = 'state';
    $field54->table = 'vtiger_potential';
    $field54->column = 'state';
    $field54->columntype='VARCHAR(200)';
    $field54->uitype = 1;
    $field54->typeofdata = 'V~O';
    $field54->displaytype = 1;
    $field54->quickcreate = 1;

    $opportunitiesblock4->addField($field54);
}

$field55 = Vtiger_Field::getInstance('country', $opportunitiesInstance);
if ($field55) {
    echo "<li>The country field already exists</li><br>";
} else {
    $field55 = new Vtiger_Field();
    $field55->label = 'LBL_POTENTIALS_COUNTRY';
    $field55->name = 'country';
    $field55->table = 'vtiger_potential';
    $field55->column = 'country';
    $field55->columntype='VARCHAR(200)';
    $field55->uitype = 1;
    $field55->typeofdata = 'V~O';
    $field55->displaytype = 1;
    $field55->quickcreate = 1;

    $opportunitiesblock4->addField($field55);
}


$filter1 = new Vtiger_Filter();
$filter1->name = 'ALL';
$filter1->isdefault = true;
$opportunitiesInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5);
  
$opportunitiesInstance->setDefaultSharing();
$opportunitiesInstance->initWebservice();

// Adds the Updates link to the vertical navigation menu on the right.
ModTracker::enableTrackingForModule($opportunitiesInstance->id);

$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Calendar'), 'Activities', array('add'), 'get_activities');
$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('add', 'select'), 'get_attachments');
$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Contacts'), 'Contacts', array('add'), 'get_contacts');
/*$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Estimates'), 'Estimates',Array('add','select'),'get_quotes');
$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Orders',Array('add'),'get_dependents_list');
$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Stops'), 'Stops',Array('add'),'get_dependents_list');*/
$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Surveys'), 'Survey Appointments', array('add'), 'get_dependents_list');
//$opportunitiesInstance->setRelatedList(Vtiger_Module::getInstance('Cubesheets'), 'Surveys',Array('add'),'get_related_list');


require_once 'vtlib/Vtiger/Module.php';
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Opportunities'));

require_once 'modules/ModComments/ModComments.php';
$detailviewblock = ModComments::addWidgetTo('Opportunities');
