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



/* $Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php'); */

$potentialsInstance = Vtiger_Module::getInstance('Potentials');
$potentialsblock1 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potentialsInstance);
$potentialsblock2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $potentialsInstance);
$potentialsblock3 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $potentialsInstance);

//adding fields to Quotes module LBL_OPPORTUNITY_INFORMATION
$field1 = Vtiger_Field::getInstance('business_line', $potentialsInstance);
if ($field1) {
    echo "<li>The business_line field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_POTENTIALS_BUSINESSLINE';
    $field1->name = 'business_line';
    $field1->table = 'vtiger_potentialscf';
    $field1->column = 'business_line';
    $field1->columntype='VARCHAR(200)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~M';
    $field1->displaytype = 1;
    $field1->quickcreate = 2;

    $potentialsblock1->addField($field1);
}

$potentialsblock4 = Vtiger_Block::getInstance('LBL_POTENTIALS_DATES', $potentialsInstance);
if ($potentialsblock4) {
    echo "<li>The LBL_POTENTIALS_DATES field already exists</li><br>";
} else {
    $potentialsblock4 = new Vtiger_Block();
    $potentialsblock4->label = 'LBL_POTENTIALS_DATES';
    $potentialsInstance->addBlock($potentialsblock4);
}


$field4 = Vtiger_Field::getInstance('pack_date', $potentialsInstance);
if ($field4) {
    echo "<li>The pack_date field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_POTENTIALS_PACK';
    $field4->name = 'pack_date';
    $field4->table = 'vtiger_potentialscf';
    $field4->column = 'pack_date';
    $field4->columntype='DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';
    $field4->displaytype = 1;
    $field4->quickcreate = 1;
    
    $potentialsblock4->addField($field4);
}

$field5 = Vtiger_Field::getInstance('pack_to_date', $potentialsInstance);
if ($field5) {
    echo "<li>The pack_to_date field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_POTENTIALS_PACKTO';
    $field5->name = 'pack_to_date';
    $field5->table = 'vtiger_potentialscf';
    $field5->column = 'pack_to_date';
    $field5->columntype='DATE';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';
    $field5->displaytype = 1;
    $field5->quickcreate = 1;
    
    $potentialsblock4->addField($field5);
}
$field042 = Vtiger_Field::getInstance('preffered_ppdate', $potentialsInstance);
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
    
    $potentialsblock4->addField($field042);
}

$field06 = Vtiger_Field::getInstance('load_date', $potentialsInstance);
if ($field06) {
    echo "<li>The load_date field already exists</li><br>";
} else {
    $field06 = new Vtiger_Field();
    $field06->label = 'LBL_POTENTIALS_LOAD';
    $field06->name = 'load_date';
    $field06->table = 'vtiger_potentialscf';
    $field06->column = 'load_date';
    $field06->columntype='DATE';
    $field06->uitype = 5;
    $field06->typeofdata = 'D~O';
    $field06->displaytype = 1;
    $field06->quickcreate = 1;
    
    $potentialsblock4->addField($field06);
}

$field6 = Vtiger_Field::getInstance('load_to_date', $potentialsInstance);
if ($field6) {
    echo "<li>The load_to_date field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_POTENTIALS_LOADTO';
    $field6->name = 'load_to_date';
    $field6->table = 'vtiger_potentialscf';
    $field6->column = 'load_to_date';
    $field6->columntype='DATE';
    $field6->uitype = 5;
    $field6->typeofdata = 'D~O';
    $field6->displaytype = 1;
    $field6->quickcreate = 1;
    
    $potentialsblock4->addField($field6);
}
$field044 = Vtiger_Field::getInstance('preferred_pldate', $potentialsInstance);
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
    
    $potentialsblock4->addField($field044);
}

$field7 = Vtiger_Field::getInstance('deliver_date', $potentialsInstance);
if ($field7) {
    echo "<li>The deliver_date field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_POTENTIALS_DELIVER';
    $field7->name = 'deliver_date';
    $field7->table = 'vtiger_potentialscf';
    $field7->column = 'deliver_date';
    $field7->columntype='DATE';
    $field7->uitype = 5;
    $field7->typeofdata = 'D~O';
    $field7->displaytype = 1;
    $field7->quickcreate = 1;
    
    $potentialsblock4->addField($field7);
}

$field8 = Vtiger_Field::getInstance('deliver_to_date', $potentialsInstance);
if ($field8) {
    echo "<li>The deliver_to_date field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_POTENTIALS_DELIVERTO';
    $field8->name = 'deliver_to_date';
    $field8->table = 'vtiger_potentialscf';
    $field8->column = 'deliver_to_date';
    $field8->columntype='DATE';
    $field8->uitype = 5;
    $field8->typeofdata = 'D~O';
    $field8->displaytype = 1;
    $field8->quickcreate = 1;
    
    $potentialsblock4->addField($field8);
}

$field046 = Vtiger_Field::getInstance('preferred_pddate', $potentialsInstance);
if ($field046) {
    echo "<li>The preferred_pddate field already exists</li><br>";
} else {
    $field046 = new Vtiger_Field();
    $field046->label = 'LBL_POTENTIAL_PDDATE';
    $field046->name = 'preferred_pddate';
    $field046->table = 'vtiger_potentialscf';
    $field046->column = 'preferred_pddate';
    $field046->columntype='DATE';
    $field046->uitype = 5;
    $field046->typeofdata = 'D~O';
    $field046->displaytype = 1;
    $field046->quickcreate = 1;
    
    $potentialsblock4->addField($field046);
}

/*
$field9 = Vtiger_Field::getInstance('survey_date',$potentialsInstance);
if($field9) {
    echo "<li>The survey_date field already exists</li><br>";
}
else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_POTENTIALS_SURVEY';
    $field9->name = 'survey_date';
    $field9->table = 'vtiger_potentialscf';
    $field9->column = 'survey_date';
    $field9->columntype='DATE';
    $field9->uitype = 5;
    $field9->typeofdata = 'D~O';
    $field9->displaytype = 1;
    $field9->quickcreate = 1;

    $potentialsblock4->addField($field9);
}

$field10 = Vtiger_Field::getInstance('survey_time',$potentialsInstance);
if($field10) {
    echo "<li>The survey_time field already exists</li><br>";
}
else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_POTENTIALS_SURVEYTIME';
    $field10->name = 'survey_time';
    $field10->table = 'vtiger_potentialscf';
    $field10->column = 'survey_time';
    $field10->columntype='TIME';
    $field10->uitype = 14;
    $field10->typeofdata = 'T~O';
    $field10->displaytype = 1;
    $field10->quickcreate = 1;

    $potentialsblock4->addField($field10);
}*/

$field11 = Vtiger_Field::getInstance('followup_date', $potentialsInstance);
if ($field11) {
    echo "<li>The followup_date field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_POTENTIALS_FOLLOWUP';
    $field11->name = 'followup_date';
    $field11->table = 'vtiger_potentialscf';
    $field11->column = 'followup_date';
    $field11->columntype='DATE';
    $field11->uitype = 5;
    $field11->typeofdata = 'D~O';
    $field11->displaytype = 1;
    $field11->quickcreate = 1;
    
    $potentialsblock4->addField($field11);
}

$field12 = Vtiger_Field::getInstance('decision_date', $potentialsInstance);
if ($field12) {
    echo "<li>The decision_date field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_POTENTIALS_DECISION';
    $field12->name = 'decision_date';
    $field12->table = 'vtiger_potentialscf';
    $field12->column = 'decision_date';
    $field12->columntype='DATE';
    $field12->uitype = 5;
    $field12->typeofdata = 'D~O';
    $field12->displaytype = 1;
    $field12->quickcreate = 1;
    
    $potentialsblock4->addField($field12);
}


$potentialsblock5 = Vtiger_Block::getInstance('LBL_POTENTIALS_AGENTS', $potentialsInstance);
if ($potentialsblock5) {
    echo "<li>The LBL_POTENTIALS_AGENTS field already exists</li><br>";
} else {
    $potentialsblock5 = new Vtiger_Block();
    $potentialsblock5->label = 'LBL_POTENTIALS_AGENTS';
    $potentialsInstance->addBlock($potentialsblock5);
}


$potentialsblock6 = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $potentialsInstance);
if ($potentialsblock6) {
    echo "<li>The LBL_POTENTIALS_ADDRESSDETAILS field already exists</li><br>";
} else {
    $potentialsblock6 = new Vtiger_Block();
    $potentialsblock6->label = 'LBL_POTENTIALS_ADDRESSDETAILS';
    $potentialsInstance->addBlock($potentialsblock6);
}

//ADD FIELDS TO potentialsblock6
$field13 = Vtiger_Field::getInstance('origin_address1', $potentialsInstance);
if ($field13) {
    echo "<li>The origin_address1 field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_POTENTIALS_ORIGINADDRESS1';
    $field13->name = 'origin_address1';
    $field13->table = 'vtiger_potentialscf';
    $field13->column = 'origin_address1';
    $field13->columntype='VARCHAR(200)';
    $field13->uitype = 1;
    $field13->typeofdata = 'V~O';
    $field13->displaytype = 1;
    $field13->quickcreate = 1;
    
    $potentialsblock6->addField($field13);
}

$field14 = Vtiger_Field::getInstance('destination_address1', $potentialsInstance);
if ($field14) {
    echo "<li>The destination_address1 field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_POTENTIALS_DESTINATIONADDRESS1';
    $field14->name = 'destination_address1';
    $field14->table = 'vtiger_potentialscf';
    $field14->column = 'destination_address1';
    $field14->columntype='VARCHAR(200)';
    $field14->uitype = 1;
    $field14->typeofdata = 'V~O';
    $field14->displaytype = 1;
    $field14->quickcreate = 1;
    
    $potentialsblock6->addField($field14);
}

$field15 = Vtiger_Field::getInstance('origin_address2', $potentialsInstance);
if ($field15) {
    echo "<li>The origin_address2 field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_POTENTIALS_ORIGINADDRESS2';
    $field15->name = 'origin_address2';
    $field15->table = 'vtiger_potentialscf';
    $field15->column = 'origin_address2';
    $field15->columntype='VARCHAR(200)';
    $field15->uitype = 1;
    $field15->typeofdata = 'V~O';
    $field15->displaytype = 1;
    $field15->quickcreate = 1;
    
    $potentialsblock6->addField($field15);
}

$field16 = Vtiger_Field::getInstance('destination_address2', $potentialsInstance);
if ($field16) {
    echo "<li>The destination_address2 field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_POTENTIALS_DESTINATIONADDRESS2';
    $field16->name = 'destination_address2';
    $field16->table = 'vtiger_potentialscf';
    $field16->column = 'destination_address2';
    $field16->columntype='VARCHAR(200)';
    $field16->uitype = 1;
    $field16->typeofdata = 'V~O';
    $field16->displaytype = 1;
    $field16->quickcreate = 1;
    
    $potentialsblock6->addField($field16);
}

$field17 = Vtiger_Field::getInstance('origin_city', $potentialsInstance);
if ($field17) {
    echo "<li>The origin_city field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_POTENTIALS_ORIGINCITY';
    $field17->name = 'origin_city';
    $field17->table = 'vtiger_potentialscf';
    $field17->column = 'origin_city';
    $field17->columntype='VARCHAR(200)';
    $field17->uitype = 1;
    $field17->typeofdata = 'V~O';
    $field17->displaytype = 1;
    $field17->quickcreate = 1;
    
    $potentialsblock6->addField($field17);
}


$field18 = Vtiger_Field::getInstance('destination_city', $potentialsInstance);
if ($field18) {
    echo "<li>The destination_city field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_POTENTIALS_DESTINATIONCITY';
    $field18->name = 'destination_city';
    $field18->table = 'vtiger_potentialscf';
    $field18->column = 'destination_city';
    $field18->columntype='VARCHAR(200)';
    $field18->uitype = 1;
    $field18->typeofdata = 'V~O';
    $field18->displaytype = 1;
    $field18->quickcreate = 1;
    
    $potentialsblock6->addField($field18);
}

$field19 = Vtiger_Field::getInstance('origin_state', $potentialsInstance);
if ($field19) {
    echo "<li>The origin_state field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_POTENTIALS_ORIGINSTATE';
    $field19->name = 'origin_state';
    $field19->table = 'vtiger_potentialscf';
    $field19->column = 'origin_state';
    $field19->columntype='VARCHAR(200)';
    $field19->uitype = 1;
    $field19->typeofdata = 'V~O';
    $field19->displaytype = 1;
    $field19->quickcreate = 1;
    
    $potentialsblock6->addField($field19);
}

$field20 = Vtiger_Field::getInstance('destination_state', $potentialsInstance);
if ($field20) {
    echo "<li>The destination_state field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_POTENTIALS_DESTINATIONSTATE';
    $field20->name = 'destination_state';
    $field20->table = 'vtiger_potentialscf';
    $field20->column = 'destination_state';
    $field20->columntype='VARCHAR(200)';
    $field20->uitype = 1;
    $field20->typeofdata = 'V~O';
    $field20->displaytype = 1;
    $field20->quickcreate = 1;
    
    $potentialsblock6->addField($field20);
}

$field21 = Vtiger_Field::getInstance('origin_zip', $potentialsInstance);
if ($field21) {
    echo "<li>The origin_zip field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_POTENTIALS_ORIGINZIP';
    $field21->name = 'origin_zip';
    $field21->table = 'vtiger_potentialscf';
    $field21->column = 'origin_zip';
    $field21->columntype='VARCHAR(200)';
    $field21->uitype = 1;
    $field21->typeofdata = 'V~O';
    $field21->displaytype = 1;
    $field21->quickcreate = 1;
    
    $potentialsblock6->addField($field21);
}

$field22 = Vtiger_Field::getInstance('destination_zip', $potentialsInstance);
if ($field22) {
    echo "<li>The destiantion_zip field already exists</li><br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_POTENTIALS_DESTINATIONZIP';
    $field22->name = 'destination_zip';
    $field22->table = 'vtiger_potentialscf';
    $field22->column = 'destination_zip';
    $field22->columntype='VARCHAR(200)';
    $field22->uitype = 1;
    $field22->typeofdata = 'V~O';
    $field22->displaytype = 1;
    $field22->quickcreate = 1;
    
    $potentialsblock6->addField($field22);
}


$field23 = Vtiger_Field::getInstance('origin_phone1', $potentialsInstance);
if ($field23) {
    echo "<li>The origin_phone1 field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_POTENTIALS_ORIGINPHONE1';
    $field23->name = 'origin_phone1';
    $field23->table = 'vtiger_potentialscf';
    $field23->column = 'origin_phone1';
    $field23->columntype='VARCHAR(200)';
    $field23->uitype = 11;
    $field23->typeofdata = 'V~O';
    $field23->displaytype = 1;
    $field23->quickcreate = 1;
    
    $potentialsblock6->addField($field23);
}

$field24 = Vtiger_Field::getInstance('destination_phone1', $potentialsInstance);
if ($field24) {
    echo "<li>The destination_phone1 field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_POTENTIALS_DESTINATIONPHONE1';
    $field24->name = 'destination_phone1';
    $field24->table = 'vtiger_potentialscf';
    $field24->column = 'destination_phone1';
    $field24->columntype='VARCHAR(200)';
    $field24->uitype = 11;
    $field24->typeofdata = 'V~O';
    $field24->displaytype = 1;
    $field24->quickcreate = 1;
    
    $potentialsblock6->addField($field24);
}

$field25 = Vtiger_Field::getInstance('origin_phone2', $potentialsInstance);
if ($field25) {
    echo "<li>The origin_phone2 field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_POTENTIALS_ORIGINPHONE2';
    $field25->name = 'origin_phone2';
    $field25->table = 'vtiger_potentialscf';
    $field25->column = 'origin_phone2';
    $field25->columntype='VARCHAR(200)';
    $field25->uitype = 11;
    $field25->typeofdata = 'V~O';
    $field25->displaytype = 1;
    $field25->quickcreate = 1;
    
    $potentialsblock6->addField($field25);
}

$field26 = Vtiger_Field::getInstance('destination_phone2', $potentialsInstance);
if ($field26) {
    echo "<li>The destination_phone2 field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_POTENTIALS_DESTINATIONPHONE2';
    $field26->name = 'destination_phone2';
    $field26->table = 'vtiger_potentialscf';
    $field26->column = 'destination_phone2';
    $field26->columntype='VARCHAR(200)';
    $field26->uitype = 11;
    $field26->typeofdata = 'V~O';
    $field26->displaytype = 1;
    $field26->quickcreate = 1;
    
    $potentialsblock6->addField($field26);
}

$field27 = Vtiger_Field::getInstance('origin_country', $potentialsInstance);
if ($field27) {
    echo "<li>The origin_country field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY';
    $field27->name = 'origin_country';
    $field27->table = 'vtiger_potential';
    $field27->column = 'origin_country';
    $field27->columntype='VARCHAR(200)';
    $field27->uitype = 1;
    $field27->typeofdata = 'V~O';
    $field27->displaytype = 1;
    $field27->quickcreate = 1;
    
    $potentialsblock6->addField($field27);
}

$field30 = Vtiger_Field::getInstance('destination_country', $potentialsInstance);
if ($field30) {
    echo "<li>The destination_country field already exists</li><br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY';
    $field30->name = 'destination_country';
    $field30->table = 'vtiger_potential';
    $field30->column = 'destination_country';
    $field30->columntype='VARCHAR(200)';
    $field30->uitype = 1;
    $field30->typeofdata = 'V~O';
    $field30->displaytype = 1;
    $field30->quickcreate = 1;
    
    $potentialsblock6->addField($field30);
}

$field28 = Vtiger_Field::getInstance('origin_description', $potentialsInstance);
if ($field28) {
    echo "<li>The origin_description field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_POTENTIALS_ORIGINADDRESSDESCRIPTION';
    $field28->name = 'origin_description';
    $field28->table = 'vtiger_potential';
    $field28->column = 'origin_description';
    $field28->columntype='VARCHAR(200)';
    $field28->uitype = 1;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 1;
    $field28->quickcreate = 1;
    
    $potentialsblock6->addField($field28);
}

$field31 = Vtiger_Field::getInstance('destination_description', $potentialsInstance);
if ($field31) {
    echo "<li>The destination_description field already exists</li><br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDESCRIPTION';
    $field31->name = 'destination_description';
    $field31->table = 'vtiger_potential';
    $field31->column = 'destination_description';
    $field31->columntype='VARCHAR(200)';
    $field31->uitype = 1;
    $field31->typeofdata = 'V~O';
    $field31->displaytype = 1;
    $field31->quickcreate = 1;
    
    $potentialsblock6->addField($field31);
}


$field29 = Vtiger_Field::getInstance('origin_flightsofstairs', $potentialsInstance);
if ($field29) {
    echo "<li>The origin_flightsofstairs field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_POTENTIALS_ORIGINADDRESSFLIGHTSOFSTAIRS';
    $field29->name = 'origin_flightsofstairs';
    $field29->table = 'vtiger_potential';
    $field29->column = 'origin_flightsofstairs';
    $field29->columntype='INT(2)';
    $field29->uitype = 7;
    $field29->typeofdata = 'N~O';
    $field29->displaytype = 1;
    $field29->quickcreate = 1;
    
    $potentialsblock6->addField($field29);
}


$field32 = Vtiger_Field::getInstance('destination_flightsofstairs', $potentialsInstance);
if ($field32) {
    echo "<li>The destination_flightsofstairs field already exists</li><br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_POTENTIALS_DESTINATIONADDRESSFLIGHTSOFSTAIRS';
    $field32->name = 'destination_flightsofstairs';
    $field32->table = 'vtiger_potential';
    $field32->column = 'destination_flightsofstairs';
    $field32->columntype='INT(2)';
    $field32->uitype = 7;
    $field32->typeofdata = 'N~O';
    $field32->displaytype = 1;
    $field32->quickcreate = 1;
    
    $potentialsblock6->addField($field32);
}

$potentialsblock7 = Vtiger_Block::getInstance('LBL_POTENTIALS_LOCALMOVEDETAILS', $potentialsInstance);
if ($potentialsblock7) {
    echo "<li>The LBL_POTENTIALS_LOCALMOVEDETAILS field already exists</li><br>";
} else {
    $potentialsblock7 = new Vtiger_Block();
    $potentialsblock7->label = 'LBL_POTENTIALS_LOCALMOVEDETAILS';
    $potentialsInstance->addBlock($potentialsblock7);
}

$potentialsblock8 = Vtiger_Block::getInstance('LBL_POTENTIALS_INTERSTATEMOVEDETAILS', $potentialsInstance);
if ($potentialsblock8) {
    echo "<li>The LBL_POTENTIALS_INTERSTATEMOVEDETAILS field already exists</li><br>";
} else {
    $potentialsblock8 = new Vtiger_Block();
    $potentialsblock8->label = 'LBL_POTENTIALS_INTERSTATEMOVEDETAILS';
    $potentialsInstance->addBlock($potentialsblock8);
}

$potentialsblock9 = Vtiger_Block::getInstance('LBL_POTENTIALS_COMMERCIALMOVEDETAILS', $potentialsInstance);
if ($potentialsblock9) {
    echo "<li>The LBL_POTENTIALS_COMMERCIALMOVEDETAILS field already exists</li><br>";
} else {
    $potentialsblock9 = new Vtiger_Block();
    $potentialsblock9->label = 'LBL_POTENTIALS_COMMERCIALMOVEDETAILS';
    $potentialsInstance->addBlock($potentialsblock9);
}


$potentialsblock10 = Vtiger_Block::getInstance('LBL_POTENTIALS_DESTINATIONADDRESSDETAILS', $potentialsInstance);
if ($potentialsblock10) {
    echo "<li>The LBL_POTENTIALS_DESTINATIONADDRESSDETAILS field already exists</li><br>";
} else {
    $potentialsblock10 = new Vtiger_Block();
    $potentialsblock10->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDETAILS';
    $potentialsInstance->addBlock($potentialsblock10);
}

$potentialsblock11 = Vtiger_Block::getInstance('LBL_POTENTIALS_NATIONALACCOUNT', $potentialsInstance);
if ($potentialsblock11) {
    echo "<li>The LBL_POTENTIALS_NATIONALACCOUNT field already exists</li><br>";
} else {
    $potentialsblock11 = new Vtiger_Block();
    $potentialsblock11->label = 'LBL_POTENTIALS_NATIONALACCOUNT';
    $potentialsInstance->addBlock($potentialsblock11);
}

$field33 = Vtiger_Field::getInstance('street', $potentialsInstance);
if ($field33) {
    echo "<li>The street field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_POTENTIALS_STREET';
    $field33->name = 'street';
    $field33->table = 'vtiger_potential';
    $field33->column = 'street';
    $field33->columntype='VARCHAR(200)';
    $field33->uitype = 21;
    $field33->typeofdata = 'V~O';
    $field33->displaytype = 1;
    $field33->quickcreate = 1;

    $potentialsblock11->addField($field33);
}

$field34 = Vtiger_Field::getInstance('pobox', $potentialsInstance);
if ($field34) {
    echo "<li>The pobox field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_POTENTIALS_POBOX';
    $field34->name = 'pobox';
    $field34->table = 'vtiger_potential';
    $field34->column = 'pobox';
    $field34->columntype='VARCHAR(200)';
    $field34->uitype = 1;
    $field34->typeofdata = 'V~O';
    $field34->displaytype = 1;
    $field34->quickcreate = 1;

    $potentialsblock11->addField($field34);
}

$field35 = Vtiger_Field::getInstance('city', $potentialsInstance);
if ($field35) {
    echo "<li>The city field already exists</li><br>";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_POTENTIALS_CITY';
    $field35->name = 'city';
    $field35->table = 'vtiger_potential';
    $field35->column = 'city';
    $field35->columntype='VARCHAR(200)';
    $field35->uitype = 1;
    $field35->typeofdata = 'V~O';
    $field35->displaytype = 1;
    $field35->quickcreate = 1;

    $potentialsblock11->addField($field35);
}

$field36 = Vtiger_Field::getInstance('zip', $potentialsInstance);
if ($field36) {
    echo "<li>The zip field already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_POTENTIALS_ZIP';
    $field36->name = 'zip';
    $field36->table = 'vtiger_potential';
    $field36->column = 'zip';
    $field36->columntype='VARCHAR(200)';
    $field36->uitype = 1;
    $field36->typeofdata = 'V~O';
    $field36->displaytype = 1;
    $field36->quickcreate = 1;

    $potentialsblock11->addField($field36);
}

$field38 = Vtiger_Field::getInstance('state', $potentialsInstance);
if ($field38) {
    echo "<li>The state field already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_POTENTIALS_STATE';
    $field38->name = 'state';
    $field38->table = 'vtiger_potential';
    $field38->column = 'state';
    $field38->columntype='VARCHAR(200)';
    $field38->uitype = 1;
    $field38->typeofdata = 'V~O';
    $field38->displaytype = 1;
    $field38->quickcreate = 1;

    $potentialsblock11->addField($field38);
}

$field37 = Vtiger_Field::getInstance('country', $potentialsInstance);
if ($field37) {
    echo "<li>The country field already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_POTENTIALS_COUNTRY';
    $field37->name = 'country';
    $field37->table = 'vtiger_potential';
    $field37->column = 'country';
    $field37->columntype='VARCHAR(200)';
    $field37->uitype = 1;
    $field37->typeofdata = 'V~O';
    $field37->displaytype = 1;
    $field37->quickcreate = 1;

    $potentialsblock11->addField($field37);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";