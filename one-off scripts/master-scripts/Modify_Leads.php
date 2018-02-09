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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Leads');
//Check and create all nessecary blocks for Leads
$block1 = Vtiger_Block::getInstance('LBL_LEAD_INFORMATION', $module);
//Since we are going to change the name we want to see if either of the names exist;
if (!$block1) {
    $block1 = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $module);
}
    if ($block1) {
        echo "<br> block 'LBL_LEAD_INFORMATION' already exists.<br>";
    } else {
        $block1 = new Vtiger_Block();
        $block1->label = 'LBL_LEAD_INFORMATION';
        $module->addBlock($block1);
    }
$block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module);
//Since we are going to change the name we want to see if either of the names exist;
if (!$block2) {
    $block2 = Vtiger_Block::getInstance('LBL_LEADS_CUSTOMINFORMATION', $module);
}
    if ($block2) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $block2 = new Vtiger_Block();
        $block2->label = 'LBL_CUSTOM_INFORMATION';
        $module->addBlock($block2);
    }
$block3 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $module);
//Since we are going to change the name we want to see if either of the names exist;
if (!$block3) {
    $block3 = Vtiger_Block::getInstance('LBL_LEADS_ADDRESSINFORMATION', $module);
}
    if ($block3) {
        echo "<br> block 'LBL_ADDRESS_INFORMATION' already exists.<br>";
    } else {
        $block3 = new Vtiger_Block();
        $block3->label = 'LBL_ADDRESS_INFORMATION';
        $module->addBlock($block3);
    }
$block4 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $module);
//Since we are going to change the name we want to see if either of the names exist;
if (!$block4) {
    $block4 = Vtiger_Block::getInstance('LBL_LEADS_DESCRIPTIONINFORMATION', $module);
}
    if ($block4) {
        echo "<br> block 'LBL_DESCRIPTION_INFORMATION' already exists.<br>";
    } else {
        $block4 = new Vtiger_Block();
        $block4->label = 'LBL_DESCRIPTION_INFORMATION';
        $module->addBlock($block4);
    }
$block5 = Vtiger_Block::getInstance('LBL_LEADS_DATES', $module);
    if ($block5) {
        echo "<br> block 'LBL_LEADS_DATES' already exists.<br>";
    } else {
        $block5 = new Vtiger_Block();
        $block5->label = 'LBL_LEADS_DATES';
        $module->addBlock($block5);
    }
$block6 = Vtiger_Block::getInstance('LBL_LEADS_LOCALMOVEDETAILS', $module);
    if ($block6) {
        echo "<br> block 'LBL_LEADS_LOCALMOVEDETAILS' already exists.<br>";
    } else {
        $block6 = new Vtiger_Block();
        $block6->label = 'LBL_LEADS_LOCALMOVEDETAILS';
        $module->addBlock($block6);
    }
$block7 = Vtiger_Block::getInstance('LBL_LEADS_INTERSTATEMOVEDETAILS', $module);
    if ($block7) {
        echo "<br> block 'LBL_LEADS_INTERSTATEMOVEDETAILS' already exists.<br>";
    } else {
        $block7 = new Vtiger_Block();
        $block7->label = 'LBL_LEADS_INTERSTATEMOVEDETAILS';
        $module->addBlock($block7);
    }
$block8 = Vtiger_Block::getInstance('LBL_LEADS_COMMERCIALMOVEDETAILS', $module);
    if ($block8) {
        echo "<br> block 'LBL_LEADS_COMMERCIALMOVEDETAILS' already exists.<br>";
    } else {
        $block8 = new Vtiger_Block();
        $block8->label = 'LBL_LEADS_COMMERCIALMOVEDETAILS';
        $module->addBlock($block8);
    }
$block9 = Vtiger_Block::getInstance('LBL_LEADS_NATIONALACCOUNT', $module);
    if ($block9) {
        echo "<br> block 'LBL_LEADS_NATIONALACCOUNT' already exists.<br>";
    } else {
        $block9 = new Vtiger_Block();
        $block9->label = 'LBL_LEADS_NATIONALACCOUNT';
        $module->addBlock($block9);
    }
//reorder the blocks

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 1, blocklabel = 'LBL_LEADS_INFORMATION' WHERE blockid = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 9, blocklabel = 'LBL_LEADS_CUSTOMINFORMATION' WHERE blockid = ". $block2->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 2, blocklabel = 'LBL_LEADS_ADDRESSINFORMATION' WHERE blockid = ". $block3->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 5, blocklabel = 'LBL_LEADS_DESCRIPTIONINFORMATION' WHERE blockid = ". $block4->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 4 WHERE blockid = ". $block5->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 6 WHERE blockid = ". $block6->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 7 WHERE blockid = ". $block7->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 8 WHERE blockid = ". $block8->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 3 WHERE blockid = ". $block9->id);

echo "<br>UPDATE `vtiger_blocks` SET sequence = 1, blocklabel = 'LBL_LEADS_INFORMATION' WHERE blockid = ". $block1->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 9, blocklabel = 'LBL_LEADS_CUSTOMINFORMATION' WHERE blockid = ". $block2->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 2, blocklabel = 'LBL_LEADS_ADDRESSINFORMATION' WHERE blockid = ". $block3->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 5, blocklabel = 'LBL_LEADS_DESCRIPTIONINFORMATION' WHERE blockid = ". $block4->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 4 WHERE blockid = ". $block5->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 6 WHERE blockid = ". $block6->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 7 WHERE blockid = ". $block7->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 8 WHERE blockid = ". $block8->id;
echo "<br>UPDATE `vtiger_blocks` SET sequence = 3 WHERE blockid = ". $block9->id;

//add fields to LBL_LEAD_INFORMATION block1
$field1 = Vtiger_Field::getInstance('salutationtype', $module);
$field2 = Vtiger_Field::getInstance('firstname', $module);
$field3 = Vtiger_Field::getInstance('lead_no', $module);
$field4 = Vtiger_Field::getInstance('phone', $module);
$field5 = Vtiger_Field::getInstance('lastname', $module);
$field6 = Vtiger_Field::getInstance('mobile', $module);
$field7 = Vtiger_Field::getInstance('company', $module);
$field8 = Vtiger_Field::getInstance('fax', $module);
$field9 = Vtiger_Field::getInstance('designation', $module);
$field10 = Vtiger_Field::getInstance('email', $module);
$field11 = Vtiger_Field::getInstance('leadsource', $module);
$field12 = Vtiger_Field::getInstance('website', $module);
$field13 = Vtiger_Field::getInstance('industry', $module);
$field14 = Vtiger_Field::getInstance('leadstatus', $module);
$field15 = Vtiger_Field::getInstance('annualrevenue', $module);
$field16 = Vtiger_Field::getInstance('rating', $module);
$field17 = Vtiger_Field::getInstance('noofemployees', $module);
$field18 = Vtiger_Field::getInstance('assigned_user_id', $module);
$field19 = Vtiger_Field::getInstance('secondaryemail', $module);
$field20 = Vtiger_Field::getInstance('createdtime', $module);
$field21 = Vtiger_Field::getInstance('modifiedtime', $module);
$field22 = Vtiger_Field::getInstance('modifiedby', $module);
$field23 = Vtiger_Field::getInstance('emailoptout', $module);
$field24 = Vtiger_Field::getInstance('created_user_id', $module);

$field25 = Vtiger_Field::getInstance('business_line', $module);
    if ($field25) {
        echo "<br> Field 'business_line' is already present. <br>";
    } else {
        $field25 = new Vtiger_Field();
        $field25->label = 'LBL_LEADS_BUSINESSLINE';
        $field25->name = 'business_line';
        $field25->table = 'vtiger_leadscf';
        $field25->column = 'business_line';
        $field25->columntype = 'VARCHAR(255)';
        $field25->uitype = 16;
        $field25->typeofdata = 'V~O';
    
        $field25->setPicklistValues(array('Local Move', 'Interstate Move', 'Commercial Move', 'Intrastate Move', 'International Move', 'HHG - International Sea', 'National Account', 'Commercial - Distribution', 'Commercial - Record Storage', 'Commercial - Storage', 'Commercial - Asset Management', 'Commercial - Project', 'Auto Transportation'));
    
        $block1->addField($field25);
    }
//reorder the sequence and fix summary fields
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_SALUTATION', presence = 0, sequence = 1, summaryfield = 0 WHERE fieldid = ". $field1->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_FIRSTNAME', presence = 0, sequence = 1, summaryfield = 1 WHERE fieldid = ". $field2->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_LEADNUMBER', presence = 0, sequence = 2, summaryfield = 0 WHERE fieldid = ". $field3->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_PHONENUMBER', presence = 2, sequence = 5, summaryfield = 1 WHERE fieldid = ". $field4->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_LASTNAME', presence = 0, sequence = 3, summaryfield = 1 WHERE fieldid = ". $field5->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_MOBILENUMBER', presence = 2, sequence = 6, summaryfield = 0 WHERE fieldid = ". $field6->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_COMPANY', presence = 2, sequence = 4, summaryfield = 1 WHERE fieldid = ". $field7->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_FAXNUMBER', presence = 1, sequence = 5, summaryfield = 0 WHERE fieldid = ". $field8->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_DESIGNATION', presence = 1, sequence = 5, summaryfield = 0 WHERE fieldid = ". $field9->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_EMAIL', presence = 2, sequence = 7, summaryfield = 1 WHERE fieldid = ". $field10->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_LEADSOURCE', presence = 2, sequence = 10, summaryfield = 1 WHERE fieldid = ". $field11->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_WEBSITE', presence = 1, sequence = 7, summaryfield = 1 WHERE fieldid = ". $field12->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_INDUSTRY', presence = 1, sequence = 8, summaryfield = 0 WHERE fieldid = ". $field13->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_LEADSTATUS', presence = 2, sequence = 12, summaryfield = 0 WHERE fieldid = ". $field14->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_ANNUALREVENUE', presence = 1, sequence = 12, summaryfield = 0 WHERE fieldid = ". $field15->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_RATING', presence = 1, sequence = 15, summaryfield = 0 WHERE fieldid = ". $field16->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_NUMBEREMPLOYEES', presence = 1, sequence = 14, summaryfield = 0 WHERE fieldid = ". $field17->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_ASSIGNEDTO', presence = 0, sequence = 11, summaryfield = 1 WHERE fieldid = ". $field18->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_SECONDARYEMAIL', presence = 2, sequence = 8, summaryfield = 0 WHERE fieldid = ". $field19->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_CREATEDTIME', presence = 0, sequence = 13, summaryfield = 0 WHERE fieldid = ". $field20->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_MODIFIEDTIME', presence = 0, sequence = 14, summaryfield = 0 WHERE fieldid = ". $field21->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_LASTMODIFIED', presence = 0, sequence = 23, summaryfield = 0 WHERE fieldid = ". $field22->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_EMAILOPTOUT', presence = 2, sequence = 9, summaryfield = 0 WHERE fieldid = ". $field23->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_CREATEDBY', presence = 2, sequence = 16, summaryfield = 0 WHERE fieldid = ". $field24->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_BUSINESSLINE', presence = 2, sequence = 15, summaryfield = 0 WHERE fieldid = ". $field25->id ." AND block = ". $block1->id);

//LBL_LEADS_CUSTOMINFORMATION block2 is empty

//move the core fields from LBL_LEADS_ADDRESSINFORMATION block3 to where they are supposed to go and fix their labels and sequence
$field1 = Vtiger_Field::getInstance('lane', $module);
$field2 = Vtiger_Field::getInstance('code', $module);
$field3 = Vtiger_Field::getInstance('city', $module);
$field4 = Vtiger_Field::getInstance('country', $module);
$field5 = Vtiger_Field::getInstance('state', $module);
$field6 = Vtiger_Field::getInstance('pobox', $module);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 1, fieldlabel = 'LBL_LEADS_STREET', block = ". $block9->id ." WHERE fieldid = ". $field1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 5, fieldlabel = 'LBL_LEADS_POSTALCODE', block = ". $block9->id ." WHERE fieldid = ". $field2->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 3, fieldlabel = 'LBL_LEADS_CITY', block = ". $block9->id ." WHERE fieldid = ". $field3->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 6, fieldlabel = 'LBL_LEADS_COUNTRY', block = ". $block9->id ." WHERE fieldid = ". $field4->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 4, fieldlabel = 'LBL_LEADS_STATE', block = ". $block9->id ." WHERE fieldid = ". $field5->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence = 2, fieldlabel = 'LBL_LEADS_POBOX', block = ". $block9->id ." WHERE fieldid = ". $field6->id);
//add fields to LBL_LEADS_ADDRESSINFORMATION block3
$field1 = Vtiger_Field::getInstance('origin_address1', $module);
    if ($field1) {
        echo "<br> Field 'origin_address1' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_LEADS_ORIGINADDRESS1';
        $field1->name = 'origin_address1';
        $field1->table = 'vtiger_leadscf';
        $field1->column = 'origin_address1';
        $field1->columntype = 'VARCHAR(50)';
        $field1->uitype = 1;
        $field1->typeofdata = 'V~O~LE~50';
    
        $block3->addField($field1);
    }
$field2 = Vtiger_Field::getInstance('destination_address1', $module);
    if ($field2) {
        echo "<br> Field 'destination_address1' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_LEADS_DESTINATIONADDRESS1';
        $field2->name = 'destination_address1';
        $field2->table = 'vtiger_leadscf';
        $field2->column = 'destination_address1';
        $field2->columntype = 'VARCHAR(50)';
        $field2->uitype = 1;
        $field2->typeofdata = 'V~O~LE~50';
    
        $block3->addField($field2);
    }
$field3 = Vtiger_Field::getInstance('origin_address2', $module);
    if ($field3) {
        echo "<br> Field 'origin_address2' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_LEADS_ORIGINADDRESS2';
        $field3->name = 'origin_address2';
        $field3->table = 'vtiger_leadscf';
        $field3->column = 'origin_address2';
        $field3->columntype = 'VARCHAR(50)';
        $field3->uitype = 1;
        $field3->typeofdata = 'V~O~LE~50';
    
        $block3->addField($field3);
    }
$field4 = Vtiger_Field::getInstance('destination_address2', $module);
    if ($field4) {
        echo "<br> Field 'destination_address2' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_LEADS_DESTINATIONADDRESS2';
        $field4->name = 'destination_address2';
        $field4->table = 'vtiger_leadscf';
        $field4->column = 'destination_address2';
        $field4->columntype = 'VARCHAR(50)';
        $field4->uitype = 1;
        $field4->typeofdata = 'V~O~LE~50';
    
        $block3->addField($field4);
    }
$field5 = Vtiger_Field::getInstance('origin_city', $module);
    if ($field5) {
        echo "<br> Field 'origin_city' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_LEADS_ORIGINCITY';
        $field5->name = 'origin_city';
        $field5->table = 'vtiger_leadscf';
        $field5->column = 'origin_city';
        $field5->columntype = 'VARCHAR(50)';
        $field5->uitype = 1;
        $field5->typeofdata = 'V~O~LE~50';
    
        $block3->addField($field5);
    }
$field6 = Vtiger_Field::getInstance('destination_city', $module);
    if ($field6) {
        echo "<br> Field 'destination_city' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_LEADS_DESTINATIONCITY';
        $field6->name = 'destination_city';
        $field6->table = 'vtiger_leadscf';
        $field6->column = 'destination_city';
        $field6->columntype = 'VARCHAR(50)';
        $field6->uitype = 1;
        $field6->typeofdata = 'V~O~LE~50';
    
        $block3->addField($field6);
    }
$field7 = Vtiger_Field::getInstance('origin_state', $module);
    if ($field7) {
        echo "<br> Field 'origin_state' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_LEADS_ORIGINSTATE';
        $field7->name = 'origin_state';
        $field7->table = 'vtiger_leadscf';
        $field7->column = 'origin_state';
        $field7->columntype = 'VARCHAR(255)';
        $field7->uitype = 1;
        $field7->typeofdata = 'V~O';
    
        $block3->addField($field7);
    }
$field8 = Vtiger_Field::getInstance('destination_state', $module);
    if ($field8) {
        echo "<br> Field 'destination_state' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'LBL_LEADS_DESTINATIONSTATE';
        $field8->name = 'destination_state';
        $field8->table = 'vtiger_leadscf';
        $field8->column = 'destination_state';
        $field8->columntype = 'VARCHAR(255)';
        $field8->uitype = 1;
        $field8->typeofdata = 'V~O';
    
        $block3->addField($field8);
    }
$field9 = Vtiger_Field::getInstance('origin_zip', $module);
    if ($field9) {
        echo "<br> Field 'origin_zip' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'LBL_LEADS_ORIGINZIP';
        $field9->name = 'origin_zip';
        $field9->table = 'vtiger_leadscf';
        $field9->column = 'origin_zip';
        $field9->columntype = 'INT(5)';
        $field9->uitype = 7;
        $field9->typeofdata = 'I~O';
    
        $block3->addField($field9);
    }
$field10 = Vtiger_Field::getInstance('destination_zip', $module);
    if ($field10) {
        echo "<br> Field 'destination_zip' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'LBL_LEADS_DESTINATIONZIP';
        $field10->name = 'destination_zip';
        $field10->table = 'vtiger_leadscf';
        $field10->column = 'destination_zip';
        $field10->columntype = 'INT(5)';
        $field10->uitype = 7;
        $field10->typeofdata = 'I~O';
    
        $block3->addField($field10);
    }
$field11 = Vtiger_Field::getInstance('origin_country', $module);
    if ($field11) {
        echo "<br> Field 'origin_country' is already present. <br>";
    } else {
        $field11 = new Vtiger_Field();
        $field11->label = 'LBL_LEADS_ORIGINCOUNTRY';
        $field11->name = 'origin_country';
        $field11->table = 'vtiger_leadscf';
        $field11->column = 'origin_country';
        $field11->columntype = 'VARCHAR(100)';
        $field11->uitype = 1;
        $field11->typeofdata = 'V~O';
    
        $block3->addField($field11);
    }
$field12 = Vtiger_Field::getInstance('destination_country', $module);
    if ($field12) {
        echo "<br> Field 'destination_country' is already present. <br>";
    } else {
        $field12 = new Vtiger_Field();
        $field12->label = 'LBL_LEADS_DESTINATIONCOUNTRY';
        $field12->name = 'destination_country';
        $field12->table = 'vtiger_leadscf';
        $field12->column = 'destination_country';
        $field12->columntype = 'VARCHAR(100)';
        $field12->uitype = 1;
        $field12->typeofdata = 'V~O';
    
        $block3->addField($field12);
    }
$field13 = Vtiger_Field::getInstance('origin_phone1', $module);
    if ($field13) {
        echo "<br> Field 'origin_phone1' is already present. <br>";
    } else {
        $field13 = new Vtiger_Field();
        $field13->label = 'LBL_LEADS_ORIGINPHONE1';
        $field13->name = 'origin_phone1';
        $field13->table = 'vtiger_leadscf';
        $field13->column = 'origin_phone1';
        $field13->columntype = 'VARCHAR(30)';
        $field13->uitype = 1;
        $field13->typeofdata = 'V~O';
    
        $block3->addField($field13);
    }
$field14 = Vtiger_Field::getInstance('destination_phone1', $module);
    if ($field14) {
        echo "<br> Field 'destination_phone1' is already present. <br>";
    } else {
        $field14 = new Vtiger_Field();
        $field14->label = 'LBL_LEADS_DESTINATIONPHONE1';
        $field14->name = 'destination_phone1';
        $field14->table = 'vtiger_leadscf';
        $field14->column = 'destination_phone1';
        $field14->columntype = 'VARCHAR(30)';
        $field14->uitype = 1;
        $field14->typeofdata = 'V~O';
    
        $block3->addField($field14);
    }
$field15 = Vtiger_Field::getInstance('origin_phone2', $module);
    if ($field15) {
        echo "<br> Field 'origin_phone2' is already present. <br>";
    } else {
        $field15 = new Vtiger_Field();
        $field15->label = 'LBL_LEADS_ORIGINPHONE2';
        $field15->name = 'origin_phone2';
        $field15->table = 'vtiger_leadscf';
        $field15->column = 'origin_phone2';
        $field15->columntype = 'VARCHAR(30)';
        $field15->uitype = 1;
        $field15->typeofdata = 'V~O';
    
        $block3->addField($field15);
    }
$field16 = Vtiger_Field::getInstance('destination_phone2', $module);
    if ($field16) {
        echo "<br> Field 'destination_phone2' is already present. <br>";
    } else {
        $field16 = new Vtiger_Field();
        $field16->label = 'LBL_LEADS_DESTINATIONPHONE2';
        $field16->name = 'destination_phone2';
        $field16->table = 'vtiger_leadscf';
        $field16->column = 'destination_phone2';
        $field16->columntype = 'VARCHAR(30)';
        $field16->uitype = 1;
        $field16->typeofdata = 'V~O';
    
        $block3->addField($field16);
    }
$field17 = Vtiger_Field::getInstance('origin_flightsofstairs', $module);
    if ($field17) {
        echo "<br> Field 'origin_flightsofstairs' is already present. <br>";
    } else {
        $field17 = new Vtiger_Field();
        $field17->label = 'LBL_LEADS_ORIGINFLIGHTSOFSTAIRS';
        $field17->name = 'origin_flightsofstairs';
        $field17->table = 'vtiger_leadscf';
        $field17->column = 'origin_flightsofstairs';
        $field17->columntype = 'DECIMAL(2,0)';
        $field17->uitype = 7;
        $field17->typeofdata = 'N~O';
    
        $block3->addField($field17);
    }
$field18 = Vtiger_Field::getInstance('destination_flightsofstairs', $module);
    if ($field18) {
        echo "<br> Field 'destination_flightsofstairs' is already present. <br>";
    } else {
        $field18 = new Vtiger_Field();
        $field18->label = 'LBL_LEADS_DESTINATIONFLIGHTSOFSTAIRS';
        $field18->name = 'destination_flightsofstairs';
        $field18->table = 'vtiger_leadscf';
        $field18->column = 'destination_flightsofstairs';
        $field18->columntype = 'DECIMAL(2,0)';
        $field18->uitype = 7;
        $field18->typeofdata = 'N~O';
    
        $block3->addField($field18);
    }
$field19 = Vtiger_Field::getInstance('origin_description', $module);
    if ($field19) {
        echo "<br> Field 'origin_description' is already present. <br>";
    } else {
        $field19 = new Vtiger_Field();
        $field19->label = 'LBL_LEADS_ORIGINDESCRIPTION';
        $field19->name = 'origin_description';
        $field19->table = 'vtiger_leadscf';
        $field19->column = 'origin_description';
        $field19->columntype = 'VARCHAR(255)';
        $field19->uitype = 1;
        $field19->typeofdata = 'V~O';
    
        $block3->addField($field19);
    }
$field20 = Vtiger_Field::getInstance('destination_description', $module);
    if ($field20) {
        echo "<br> Field 'destination_description' is already present. <br>";
    } else {
        $field20 = new Vtiger_Field();
        $field20->label = 'LBL_LEADS_DESTINATIONDESCRIPTION';
        $field20->name = 'destination_description';
        $field20->table = 'vtiger_leadscf';
        $field20->column = 'destination_description';
        $field20->columntype = 'VARCHAR(255)';
        $field20->uitype = 1;
        $field20->typeofdata = 'V~O';
    
        $block3->addField($field20);
    }
//fields made in sequence order so no reordering is nessecary

//LBL_LEADS_DESCRIPTIONINFORMATION block4 is the same as it is in core
//LBL_LEADS_DATES block5 is new
$field1 = Vtiger_Field::getInstance('pack', $module);
    if ($field1) {
        echo "<br> Field 'pack' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_LEADS_PACK';
        $field1->name = 'pack';
        $field1->table = 'vtiger_leadscf';
        $field1->column = 'pack';
        $field1->columntype = 'DATE';
        $field1->uitype = 5;
        $field1->typeofdata = 'D~O';
    
        $block5->addField($field1);
    }
$field2 = Vtiger_Field::getInstance('pack_to', $module);
    if ($field2) {
        echo "<br> Field 'pack_to' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_LEADS_PACKTO';
        $field2->name = 'pack_to';
        $field2->table = 'vtiger_leadscf';
        $field2->column = 'pack_to';
        $field2->columntype = 'DATE';
        $field2->uitype = 5;
        $field2->typeofdata = 'D~O';
    
        $block5->addField($field2);
    }
$field02 = Vtiger_Field::getInstance('preferred_ppdate', $module);
    if ($field02) {
        echo "<br> Field 'preferred_ppdate' is already present. <br>";
    } else {
        $field02 = new Vtiger_Field();
        $field02->label = 'LBL_LEADS_PPDATE';
        $field02->name = 'preferred_ppdate';
        $field02->table = 'vtiger_leadscf';
        $field02->column = 'preferred_ppdate';
        $field02->columntype = 'DATE';
        $field02->uitype = 5;
        $field02->typeofdata = 'D~O';
    
        $block5->addField($field02);
    }

$field3 = Vtiger_Field::getInstance('load_from', $module);
    if ($field3) {
        echo "<br> Field 'load_from' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_LEADS_LOAD';
        $field3->name = 'load_from';
        $field3->table = 'vtiger_leadscf';
        $field3->column = 'load_from';
        $field3->columntype = 'DATE';
        $field3->uitype = 5;
        $field3->typeofdata = 'D~O';
    
        $block5->addField($field3);
    }
$field4 = Vtiger_Field::getInstance('load_to', $module);
    if ($field4) {
        echo "<br> Field 'load_to' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_LEADS_LOADTO';
        $field4->name = 'load_to';
        $field4->table = 'vtiger_leadscf';
        $field4->column = 'load_to';
        $field4->columntype = 'DATE';
        $field4->uitype = 5;
        $field4->typeofdata = 'D~O';
    
        $block5->addField($field4);
    }
$field04 = Vtiger_Field::getInstance('preferred_pldate', $module);
    if ($field04) {
        echo "<br> Field 'preferred_pldate' is already present. <br>";
    } else {
        $field04 = new Vtiger_Field();
        $field04->label = 'LBL_LEADS_PLDATE';
        $field04->name = 'preferred_pldate';
        $field04->table = 'vtiger_leadscf';
        $field04->column = 'preferred_pldate';
        $field04->columntype = 'DATE';
        $field04->uitype = 5;
        $field04->typeofdata = 'D~O';
    
        $block5->addField($field04);
    }

$field5 = Vtiger_Field::getInstance('deliver', $module);
    if ($field5) {
        echo "<br> Field 'deliver' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_LEADS_DELIVER';
        $field5->name = 'deliver';
        $field5->table = 'vtiger_leadscf';
        $field5->column = 'deliver';
        $field5->columntype = 'DATE';
        $field5->uitype = 5;
        $field5->typeofdata = 'D~O';
    
        $block5->addField($field5);
    }
$field6 = Vtiger_Field::getInstance('deliver_to', $module);
    if ($field6) {
        echo "<br> Field 'deliver_to' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_LEADS_DELIVERTO';
        $field6->name = 'deliver_to';
        $field6->table = 'vtiger_leadscf';
        $field6->column = 'deliver_to';
        $field6->columntype = 'DATE';
        $field6->uitype = 5;
        $field6->typeofdata = 'D~O';
    
        $block5->addField($field6);
    }
$field06 = Vtiger_Field::getInstance('preferred_pddate', $module);
    if ($field06) {
        echo "<br> Field 'preferred_pddate' is already present. <br>";
    } else {
        $field06 = new Vtiger_Field();
        $field06->label = 'LBL_LEADS_PDDATE';
        $field06->name = 'preferred_pddate';
        $field06->table = 'vtiger_leadscf';
        $field06->column = 'preferred_pddate';
        $field06->columntype = 'DATE';
        $field06->uitype = 5;
        $field06->typeofdata = 'D~O';
    
        $block5->addField($field06);
    }
/*
$field7 = Vtiger_Field::getInstance('survey_date', $module);
    if($field7) {
        echo "<br> Field 'survey_date' is already present. <br>";
    } else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_LEADS_SURVEY';
    $field7->name = 'survey_date';
    $field7->table = 'vtiger_leadscf';
    $field7->column = 'survey_date';
    $field7->columntype = 'DATE';
    $field7->uitype = 5;
    $field7->typeofdata = 'D~O';

    $block5->addField($field7);
    }
$field8 = Vtiger_Field::getInstance('survey_time', $module);
    if($field8) {
        echo "<br> Field 'survey_time' is already present. <br>";
    } else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_LEADS_SURVEYTIME';
    $field8->name = 'survey_time';
    $field8->table = 'vtiger_leadscf';
    $field8->column = 'survey_time';
    $field8->columntype = 'TIME';
    $field8->uitype = 14;
    $field8->typeofdata = 'T~O';

    $block5->addField($field8);
    }*/
$field9 = Vtiger_Field::getInstance('follow_up', $module);
    if ($field9) {
        echo "<br> Field 'follow_up' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'LBL_LEADS_FOLLOWUP';
        $field9->name = 'follow_up';
        $field9->table = 'vtiger_leadscf';
        $field9->column = 'follow_up';
        $field9->columntype = 'DATE';
        $field9->uitype = 5;
        $field9->typeofdata = 'D~O';
    
        $block5->addField($field9);
    }
$field10 = Vtiger_Field::getInstance('decision', $module);
    if ($field10) {
        echo "<br> Field 'decision' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'LBL_LEADS_DECISION';
        $field10->name = 'decision';
        $field10->table = 'vtiger_leadscf';
        $field10->column = 'decision';
        $field10->columntype = 'DATE';
        $field10->uitype = 5;
        $field10->typeofdata = 'D~O';
    
        $block5->addField($field10);
    }
//LBL_LEADS_LOCALMOVEDETAILS block6 appears to be empty
//LBL_LEADS_INTERSTATEMOVEDETAILS block7 appears to be empty
//LBL_LEADS_COMMERCIALMOVEDETAILS block8 appears to be empty
//LBL_LEADS_NATIONALACCOUNT block9 contains remapped fields from block3

//get rid of the related list to Campaigns
$module->unsetRelatedList(Vtiger_Module::getInstance('Campaigns'), 'Campaigns', 'get_campaigns');

echo "<h1><br>END OF SCRIPT</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";