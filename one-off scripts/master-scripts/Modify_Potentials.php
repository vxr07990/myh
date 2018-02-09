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
include_once('vtlib/Vtiger/Module.php'); */

$module = Vtiger_Module::getInstance('Potentials');

//Check and create all nessecary blocks for Potentials
$block1 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $module);
//Since we are going to change the name we want to see if either of the names exist;
if (!$block1) {
    $block1 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $module);
}
    if ($block1) {
        echo "<br> block 'LBL_POTENTIALS_INFORMATION' already exists.<br>";
    } else {
        $block1 = new Vtiger_Block();
        $block1->label = 'LBL_POTENTIALS_INFORMATION';
        $module->addBlock($block1);
    }
$block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module);
    if ($block2) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $block2 = new Vtiger_Block();
        $block2->label = 'LBL_CUSTOM_INFORMATION';
        $module->addBlock($block2);
    }
$block3 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $module);
    if ($block3) {
        echo "<br> block 'LBL_DESCRIPTION_INFORMATION' already exists.<br>";
    } else {
        $block3 = new Vtiger_Block();
        $block3->label = 'LBL_DESCRIPTION_INFORMATION';
        $module->addBlock($block3);
    }
$block4 = Vtiger_Block::getInstance('LBL_POTENTIALS_DATES', $module);
    if ($block4) {
        echo "<br> block 'LBL_POTENTIALS_DATES' already exists.<br>";
    } else {
        $block4 = new Vtiger_Block();
        $block4->label = 'LBL_POTENTIALS_DATES';
        $module->addBlock($block4);
    }
$block5 = Vtiger_Block::getInstance('LBL_POTENTIALS_AGENTS', $module);
    if ($block5) {
        echo "<br> block 'LBL_POTENTIALS_AGENTS' already exists.<br>";
    } else {
        $block5 = new Vtiger_Block();
        $block5->label = 'LBL_POTENTIALS_AGENTS';
        $module->addBlock($block5);
    }
$block6 = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $module);
    if ($block6) {
        echo "<br> block 'LBL_POTENTIALS_ADDRESSDETAILS' already exists.<br>";
    } else {
        $block6 = new Vtiger_Block();
        $block6->label = 'LBL_POTENTIALS_ADDRESSDETAILS';
        $block6->addBlock($block6);
    }
echo "makes it here";
$block7 = Vtiger_Block::getInstance('LBL_POTENTIALS_LOCALMOVEDETAILS', $module);
echo "makes it here too";
    if ($block7) {
        echo "<br> block 'LBL_POTENTIALS_LOCALMOVEDETAILS' already exists.<br>";
    } else {
        $block7 = new Vtiger_Block();
        $block7->label = 'LBL_POTENTIALS_LOCALMOVEDETAILS';
        $block7->addBlock($block7);
    }
$block8 = Vtiger_Block::getInstance('LBL_POTENTIALS_INTERSTATEMOVEDETAILS', $module);
    if ($block8) {
        echo "<br> block 'LBL_POTENTIALS_INTERSTATEMOVEDETAILS' already exists.<br>";
    } else {
        $block8 = new Vtiger_Block();
        $block8->label = 'LBL_POTENTIALS_INTERSTATEMOVEDETAILS';
        $block8->addBlock($block8);
    }
$block9 = Vtiger_Block::getInstance('LBL_POTENTIALS_COMMERCIALMOVEDETAILS', $module);
    if ($block9) {
        echo "<br> block 'LBL_POTENTIALS_COMMERCIALMOVEDETAILS' already exists.<br>";
    } else {
        $block9 = new Vtiger_Block();
        $block9->label = 'LBL_POTENTIALS_COMMERCIALMOVEDETAILS';
        $block9->addBlock($block9);
    }
$block10 = Vtiger_Block::getInstance('LBL_POTENTIALS_DESTINATIONADDRESSDETAILS', $module);
    if ($block10) {
        echo "<br> block 'LBL_POTENTIALS_DESTINATIONADDRESSDETAILS' already exists.<br>";
    } else {
        $block10 = new Vtiger_Block();
        $block10->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDETAILS';
        $block10->addBlock($block10);
    }
$block11 = Vtiger_Block::getInstance('LBL_POTENTIALS_NATIONALACCOUNT', $module);
    if ($block11) {
        echo "<br> block 'LBL_POTENTIALS_NATIONALACCOUNT' already exists.<br>";
    } else {
        $block11 = new Vtiger_Block();
        $block11->label = 'LBL_POTENTIALS_NATIONALACCOUNT';
        $block11->addBlock($block11);
    }
    
//reorder the blocks
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 1, blocklabel = 'LBL_POTENTIALS_INFORMATION' WHERE blockid = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 9 WHERE blockid = ". $block2->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 10 WHERE blockid = ". $block3->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 7 WHERE blockid = ". $block4->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 8 WHERE blockid = ". $block5->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 2 WHERE blockid = ". $block6->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 3 WHERE blockid = ". $block7->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 4 WHERE blockid = ". $block8->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 5 WHERE blockid = ". $block9->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 6 WHERE blockid = ". $block10->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = 11 WHERE blockid = ". $block11->id);

//add fields to LBL_LEAD_INFORMATION block1
$field1 = Vtiger_Field::getInstance('potentialname', $module);
$field2 = Vtiger_Field::getInstance('potential_no', $module);
$field3 = Vtiger_Field::getInstance('amount', $module);
$field4 = Vtiger_Field::getInstance('related_to', $module);
$field5 = Vtiger_Field::getInstance('closingdate', $module);
$field6 = Vtiger_Field::getInstance('opportunity_type', $module);
$field7 = Vtiger_Field::getInstance('nextstep', $module);
$field8 = Vtiger_Field::getInstance('leadsource', $module);
$field9 = Vtiger_Field::getInstance('sales_stage', $module);
$field10 = Vtiger_Field::getInstance('assigned_user_id', $module);
$field11 = Vtiger_Field::getInstance('probability', $module);
$field12 = Vtiger_Field::getInstance('campaignid', $module);
$field13 = Vtiger_Field::getInstance('createdtime', $module);
$field14 = Vtiger_Field::getInstance('modifiedtime', $module);
$field15 = Vtiger_Field::getInstance('modifiedby', $module);
$field16 = Vtiger_Field::getInstance('forecast_amount', $module);
$field17 = Vtiger_Field::getInstance('isconvertedfromlead', $module);
$field18 = Vtiger_Field::getInstance('contact_id', $module);
$field19 = Vtiger_Field::getInstance('created_user_id', $module);
$field20 = Vtiger_Field::getInstance('comm_res', $module);
if (!$field20) {
    $field20 = Vtiger_Field::getInstance('business_line', $module);
}

//reorder the sequence, fix summary fields, labels, and other misc things that are wrong with the core fields
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_POTENTIALNAME', presence = 0, sequence = 1, summaryfield = 1 WHERE fieldid = ". $field1->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_POTENTIALNUMBER', presence = 0, sequence = 2, summaryfield = 0 WHERE fieldid = ". $field2->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_AMOUNT', presence = 2, sequence = 10, summaryfield = 1 WHERE fieldid = ". $field3->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_RELATEDTO', presence = 0, sequence = 4, summaryfield = 1 WHERE fieldid = ". $field4->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_EXPECTEDCLOSE', presence = 2, sequence = 20, summaryfield = 1 WHERE fieldid = ". $field5->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_BUSINESSTYPE', presence = 2, sequence = 5, summaryfield = 0 WHERE fieldid = ". $field6->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_NEXTSTEP', presence = 2, sequence = 12, summaryfield = 0 WHERE fieldid = ". $field7->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_LEADSOURCE', presence = 2, sequence = 9, summaryfield = 0 WHERE fieldid = ". $field8->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_SALESSTAGE', presence = 2, sequence = 8, summaryfield = 1 WHERE fieldid = ". $field9->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_ASSIGNEDTO', presence = 2, sequence = 11, summaryfield = 1 WHERE fieldid = ". $field10->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_PROBABILITY', presence = 2, sequence = 14, summaryfield = 0 WHERE fieldid = ". $field11->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_CAMPAIGNSOURCE', presence = 1, sequence = 19, summaryfield = 0 WHERE fieldid = ". $field12->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_CREATEDTIME', presence = 0, sequence = 17, summaryfield = 0 WHERE fieldid = ". $field13->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_MODIFIEDTIME', presence = 0, sequence = 18, summaryfield = 0 WHERE fieldid = ". $field14->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_LASTMODIFIEDBY', presence = 0, sequence = 17, summaryfield = 0 WHERE fieldid = ". $field15->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_FORECASTAMOUNT', presence = 0, sequence = 15, summaryfield = 0 WHERE fieldid = ". $field16->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_ISCONVERTEDFROMLEAD', presence = 2, sequence = 19, summaryfield = 0 WHERE fieldid = ". $field17->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_CONTACTNAME', presence = 2, sequence = 3, summaryfield = 1 WHERE fieldid = ". $field18->id ." AND block = ". $block1->id);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_CREATEDBY', presence = 2, sequence = 16, summaryfield = 0 WHERE fieldid = ". $field19->id ." AND block = ". $block1->id);
//this one is goofy because apparently we took the comm_res field and turned it into business line, the SQL call will replicate that change
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET columnname = 'business_line', tablename = 'vtiger_potentialscf', uitype = 16, fieldname = 'business_line', fieldlabel = 'LBL_POTENTIALS_BUSINESSLINE', presence = 2, sequence = 7, summaryfield = 0 WHERE fieldid = ". $field20->id ." AND block = ". $block1->id);

//LBL_CUSTOM_INFORMATION block2 is empty

//LBL_DESCRIPTION_INFORMATION block3 has only a description field that we've turned off and relabeled.
$field1 = Vtiger_Field::getInstance('description', $module);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_POTENTIALS_DESCRIPTION', presence = 1 WHERE fieldid = ". $field1->id);

//LBL_POTENTIALS_DATES block4 is new
$field1 = Vtiger_Field::getInstance('pack_date', $module);
    if ($field1) {
        echo "<br> Field 'pack_date' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_POTENTIALS_PACK';
        $field1->name = 'pack_date';
        $field1->table = 'vtiger_potentialscf';
        $field1->column = 'pack_date';
        $field1->columntype = 'DATE';
        $field1->uitype = 5;
        $field1->typeofdata = 'D~O';
    
        $block4->addField($field1);
    }
$field2 = Vtiger_Field::getInstance('pack_to_date', $module);
    if ($field2) {
        echo "<br> Field 'pack_to_date' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_POTENTIALS_PACKTO';
        $field2->name = 'pack_to_date';
        $field2->table = 'vtiger_potentialscf';
        $field2->column = 'pack_to_date';
        $field2->columntype = 'DATE';
        $field2->uitype = 5;
        $field2->typeofdata = 'D~O';
    
        $block4->addField($field2);
    }
$field3 = Vtiger_Field::getInstance('load_date', $module);
    if ($field3) {
        echo "<br> Field 'load_date' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_POTENTIALS_LOAD';
        $field3->name = 'load_date';
        $field3->table = 'vtiger_potentialscf';
        $field3->column = 'load_date';
        $field3->columntype = 'DATE';
        $field3->uitype = 5;
        $field3->typeofdata = 'D~O';
    
        $block4->addField($field3);
    }
$field4 = Vtiger_Field::getInstance('load_to_date', $module);
    if ($field4) {
        echo "<br> Field 'load_to_date' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_POTENTIALS_LOADTO';
        $field4->name = 'load_to_date';
        $field4->table = 'vtiger_potentialscf';
        $field4->column = 'load_to_date';
        $field4->columntype = 'DATE';
        $field4->uitype = 5;
        $field4->typeofdata = 'D~O';
    
        $block4->addField($field4);
    }
$field5 = Vtiger_Field::getInstance('deliver_date', $module);
    if ($field5) {
        echo "<br> Field 'deliver_date' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_POTENTIALS_DELIVER';
        $field5->name = 'deliver_date';
        $field5->table = 'vtiger_potentialscf';
        $field5->column = 'deliver_date';
        $field5->columntype = 'DATE';
        $field5->uitype = 5;
        $field5->typeofdata = 'D~O';
    
        $block4->addField($field5);
    }
$field6 = Vtiger_Field::getInstance('deliver_to_date', $module);
    if ($field6) {
        echo "<br> Field 'deliver_to_date' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_POTENTIALS_DELIVERTO';
        $field6->name = 'deliver_to_date';
        $field6->table = 'vtiger_potentialscf';
        $field6->column = 'deliver_to_date';
        $field6->columntype = 'DATE';
        $field6->uitype = 5;
        $field6->typeofdata = 'D~O';
    
        $block4->addField($field6);
    }
$field7 = Vtiger_Field::getInstance('survey_date', $module);
    if ($field7) {
        echo "<br> Field 'survey_date' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_POTENTIALS_SURVEY';
        $field7->name = 'survey_date';
        $field7->table = 'vtiger_potentialscf';
        $field7->column = 'survey_date';
        $field7->columntype = 'DATE';
        $field7->uitype = 5;
        $field7->typeofdata = 'D~O';
    
        $block4->addField($field7);
    }
$field8 = Vtiger_Field::getInstance('survey_time', $module);
    if ($field8) {
        echo "<br> Field 'survey_time' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'LBL_POTENTIALS_SURVEYTIME';
        $field8->name = 'survey_time';
        $field8->table = 'vtiger_potentialscf';
        $field8->column = 'survey_time';
        $field8->columntype = 'TIME';
        $field8->uitype = 14;
        $field8->typeofdata = 'T~O';
    
        $block4->addField($field8);
    }
$field9 = Vtiger_Field::getInstance('followup_date', $module);
    if ($field9) {
        echo "<br> Field 'followup_date' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'LBL_POTENTIALS_FOLLOWUP';
        $field9->name = 'followup_date';
        $field9->table = 'vtiger_potentialscf';
        $field9->column = 'followup_date';
        $field9->columntype = 'DATE';
        $field9->uitype = 5;
        $field9->typeofdata = 'D~O';
    
        $block4->addField($field9);
    }
$field10 = Vtiger_Field::getInstance('decision_date', $module);
    if ($field10) {
        echo "<br> Field 'decision_date' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'LBL_POTENTIALS_DECISION';
        $field10->name = 'decision_date';
        $field10->table = 'vtiger_potentialscf';
        $field10->column = 'decision_date';
        $field10->columntype = 'DATE';
        $field10->uitype = 5;
        $field10->typeofdata = 'D~O';
    
        $block4->addField($field10);
    }
//fields were made in correct order so no need to change sequence and none are summary

//LBL_POTENTIALS_AGENTS block5 appears to be empty
//LBL_POTENTIALS_ADDRESSDETAILS block6 is new
$field1 = Vtiger_Field::getInstance('origin_address1', $module);
    if ($field1) {
        echo "<br> Field 'origin_address1' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_POTENTIALS_ORIGINADDRESS1';
        $field1->name = 'origin_address1';
        $field1->table = 'vtiger_potentialscf';
        $field1->column = 'origin_address1';
        $field1->columntype = 'VARCHAR(50)';
        $field1->uitype = 1;
        $field1->typeofdata = 'V~O~LE~50';
    
        $block6->addField($field1);
    }
$field2 = Vtiger_Field::getInstance('destination_address1', $module);
    if ($field2) {
        echo "<br> Field 'destination_address1' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_POTENTIALS_DESTINATIONADDRESS1';
        $field2->name = 'destination_address1';
        $field2->table = 'vtiger_potentialscf';
        $field2->column = 'destination_address1';
        $field2->columntype = 'VARCHAR(50)';
        $field2->uitype = 1;
        $field2->typeofdata = 'V~O~LE~50';
    
        $block6->addField($field2);
    }
$field3 = Vtiger_Field::getInstance('origin_address2', $module);
    if ($field3) {
        echo "<br> Field 'origin_address2' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_POTENTIALS_ORIGINADDRESS2';
        $field3->name = 'origin_address2';
        $field3->table = 'vtiger_potentialscf';
        $field3->column = 'origin_address2';
        $field3->columntype = 'VARCHAR(50)';
        $field3->uitype = 1;
        $field3->typeofdata = 'V~O~LE~50';
    
        $block6->addField($field3);
    }
$field4 = Vtiger_Field::getInstance('destination_address2', $module);
    if ($field4) {
        echo "<br> Field 'destination_address2' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_POTENTIALS_DESTINATIONADDRESS2';
        $field4->name = 'destination_address2';
        $field4->table = 'vtiger_potentialscf';
        $field4->column = 'destination_address2';
        $field4->columntype = 'VARCHAR(50)';
        $field4->uitype = 1;
        $field4->typeofdata = 'V~O~LE~50';
    
        $block6->addField($field4);
    }
$field5 = Vtiger_Field::getInstance('origin_city', $module);
    if ($field5) {
        echo "<br> Field 'origin_city' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_POTENTIALS_ORIGINCITY';
        $field5->name = 'origin_city';
        $field5->table = 'vtiger_potentialscf';
        $field5->column = 'origin_city';
        $field5->columntype = 'VARCHAR(50)';
        $field5->uitype = 1;
        $field5->typeofdata = 'V~O~LE~50';
    
        $block6->addField($field5);
    }
$field6 = Vtiger_Field::getInstance('destination_city', $module);
    if ($field6) {
        echo "<br> Field 'destination_city' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_POTENTIALS_DESTINATIONCITY';
        $field6->name = 'destination_city';
        $field6->table = 'vtiger_potentialscf';
        $field6->column = 'destination_city';
        $field6->columntype = 'VARCHAR(50)';
        $field6->uitype = 1;
        $field6->typeofdata = 'V~O~LE~50';
    
        $block6->addField($field6);
    }
$field7 = Vtiger_Field::getInstance('origin_state', $module);
    if ($field7) {
        echo "<br> Field 'origin_state' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_POTENTIALS_ORIGINSTATE';
        $field7->name = 'origin_state';
        $field7->table = 'vtiger_potentialscf';
        $field7->column = 'origin_state';
        $field7->columntype = 'VARCHAR(255)';
        $field7->uitype = 1;
        $field7->typeofdata = 'V~O';
    
        $block6->addField($field7);
    }
$field8 = Vtiger_Field::getInstance('destination_state', $module);
    if ($field8) {
        echo "<br> Field 'destination_state' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'LBL_POTENTIALS_DESTINATIONSTATE';
        $field8->name = 'destination_state';
        $field8->table = 'vtiger_potentialscf';
        $field8->column = 'destination_state';
        $field8->columntype = 'VARCHAR(255)';
        $field8->uitype = 1;
        $field8->typeofdata = 'V~O';
    
        $block6->addField($field8);
    }
$field9 = Vtiger_Field::getInstance('origin_zip', $module);
    if ($field9) {
        echo "<br> Field 'origin_zip' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'LBL_POTENTIALS_ORIGINZIP';
        $field9->name = 'origin_zip';
        $field9->table = 'vtiger_potentialscf';
        $field9->column = 'origin_zip';
        $field9->columntype = 'VARCHAR(10)';
        $field9->uitype = 7;
        $field9->typeofdata = 'I~O';
    
        $block6->addField($field9);
    }
$field10 = Vtiger_Field::getInstance('destination_zip', $module);
    if ($field10) {
        echo "<br> Field 'destination_zip' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'LBL_POTENTIALS_DESTINATIONZIP';
        $field10->name = 'destination_zip';
        $field10->table = 'vtiger_potentialscf';
        $field10->column = 'destination_zip';
        $field10->columntype = 'VARCHAR(10)';
        $field10->uitype = 7;
        $field10->typeofdata = 'I~O';
    
        $block6->addField($field10);
    }
$field11 = Vtiger_Field::getInstance('origin_country', $module);
    if ($field11) {
        echo "<br> Field 'origin_country' is already present. <br>";
    } else {
        $field11 = new Vtiger_Field();
        $field11->label = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY';
        $field11->name = 'origin_country';
        $field11->table = 'vtiger_potential';
        $field11->column = 'origin_country';
        $field11->columntype = 'VARCHAR(100)';
        $field11->uitype = 1;
        $field11->typeofdata = 'V~O';
    
        $block6->addField($field11);
    }
$field12 = Vtiger_Field::getInstance('destination_country', $module);
    if ($field12) {
        echo "<br> Field 'destination_country' is already present. <br>";
    } else {
        $field12 = new Vtiger_Field();
        $field12->label = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY';
        $field12->name = 'destination_country';
        $field12->table = 'vtiger_potential';
        $field12->column = 'destination_country';
        $field12->columntype = 'VARCHAR(100)';
        $field12->uitype = 1;
        $field12->typeofdata = 'V~O';
    
        $block6->addField($field12);
    }
$field13 = Vtiger_Field::getInstance('origin_phone1', $module);
    if ($field13) {
        echo "<br> Field 'origin_phone1' is already present. <br>";
    } else {
        $field13 = new Vtiger_Field();
        $field13->label = 'LBL_POTENTIALS_ORIGINPHONE1';
        $field13->name = 'origin_phone1';
        $field13->table = 'vtiger_potentialscf';
        $field13->column = 'origin_phone1';
        $field13->columntype = 'VARCHAR(30)';
        $field13->uitype = 11;
        $field13->typeofdata = 'V~O';
    
        $block6->addField($field13);
    }
$field14 = Vtiger_Field::getInstance('destination_phone1', $module);
    if ($field14) {
        echo "<br> Field 'destination_phone1' is already present. <br>";
    } else {
        $field14 = new Vtiger_Field();
        $field14->label = 'LBL_POTENTIALS_DESTINATIONPHONE1';
        $field14->name = 'destination_phone1';
        $field14->table = 'vtiger_potentialscf';
        $field14->column = 'destination_phone1';
        $field14->columntype = 'VARCHAR(30)';
        $field14->uitype = 11;
        $field14->typeofdata = 'V~O';
    
        $block6->addField($field14);
    }
$field15 = Vtiger_Field::getInstance('origin_phone2', $module);
    if ($field15) {
        echo "<br> Field 'origin_phone2' is already present. <br>";
    } else {
        $field15 = new Vtiger_Field();
        $field15->label = 'LBL_POTENTIALS_ORIGINPHONE2';
        $field15->name = 'origin_phone2';
        $field15->table = 'vtiger_potentialscf';
        $field15->column = 'origin_phone2';
        $field15->columntype = 'VARCHAR(30)';
        $field15->uitype = 11;
        $field15->typeofdata = 'V~O';
    
        $block6->addField($field15);
    }
$field16 = Vtiger_Field::getInstance('destination_phone2', $module);
    if ($field16) {
        echo "<br> Field 'destination_phone2' is already present. <br>";
    } else {
        $field16 = new Vtiger_Field();
        $field16->label = 'LBL_POTENTIALS_DESTINATIONPHONE2';
        $field16->name = 'destination_phone2';
        $field16->table = 'vtiger_potentialscf';
        $field16->column = 'destination_phone2';
        $field16->columntype = 'VARCHAR(30)';
        $field16->uitype = 11;
        $field16->typeofdata = 'V~O';
    
        $block6->addField($field16);
    }
$field17 = Vtiger_Field::getInstance('origin_flightsofstairs', $module);
    if ($field17) {
        echo "<br> Field 'origin_flightsofstairs' is already present. <br>";
    } else {
        $field17 = new Vtiger_Field();
        $field17->label = 'LBL_POTENTIALS_ORIGINADDRESSFLIGHTSOFSTAIRS';
        $field17->name = 'origin_flightsofstairs';
        $field17->table = 'vtiger_potential';
        $field17->column = 'origin_flightsofstairs';
        $field17->columntype = 'INT(2)';
        $field17->uitype = 7;
        $field17->typeofdata = 'N~O';
    
        $block6->addField($field17);
    }
$field18 = Vtiger_Field::getInstance('destination_flightsofstairs', $module);
    if ($field18) {
        echo "<br> Field 'destination_flightsofstairs' is already present. <br>";
    } else {
        $field18 = new Vtiger_Field();
        $field18->label = 'LBL_POTENTIALS_DESTINATIONADDRESSFLIGHTSOFSTAIRS';
        $field18->name = 'destination_flightsofstairs';
        $field18->table = 'vtiger_potential';
        $field18->column = 'destination_flightsofstairs';
        $field18->columntype = 'INT(2)';
        $field18->uitype = 7;
        $field18->typeofdata = 'N~O';
    
        $block6->addField($field18);
    }
$field19 = Vtiger_Field::getInstance('origin_description', $module);
    if ($field19) {
        echo "<br> Field 'origin_description' is already present. <br>";
    } else {
        $field19 = new Vtiger_Field();
        $field19->label = 'LBL_POTENTIALS_ORIGINADDRESSDESCRIPTION';
        $field19->name = 'origin_description';
        $field19->table = 'vtiger_potential';
        $field19->column = 'origin_description';
        $field19->columntype = 'VARCHAR(255)';
        $field19->uitype = 1;
        $field19->typeofdata = 'V~O';
    
        $block6->addField($field19);
    }
$field20 = Vtiger_Field::getInstance('destination_description', $module);
    if ($field20) {
        echo "<br> Field 'destination_description' is already present. <br>";
    } else {
        $field20 = new Vtiger_Field();
        $field20->label = 'LBL_POTENTIALS_DESTINATIONADDRESSDESCRIPTION';
        $field20->name = 'destination_description';
        $field20->table = 'vtiger_potential';
        $field20->column = 'destination_description';
        $field20->columntype = 'VARCHAR(255)';
        $field20->uitype = 1;
        $field20->typeofdata = 'V~O';
    
        $block6->addField($field20);
    }
//fields made in order of sequence no need to reorder

//LBL_POTENTIALS_LOCALMOVEDETAILS block7 is empty
//LBL_POTENTIALS_INTERSTATEMOVEDETAILS block8 is empty
//LBL_POTENTIALS_COMMERCIALMOVEDETAILS block9 is empty
//LBL_POTENTIALS_DESTINATIONADDRESSDETAILS block10 is empty

//LBL_POTENTIALS_NATIONALACCOUNT block11 is new
$field1 = Vtiger_Field::getInstance('street', $module);
    if ($field1) {
        echo "<br> Field 'street' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_POTENTIALS_STREET';
        $field1->name = 'street';
        $field1->table = 'vtiger_potential';
        $field1->column = 'street';
        $field1->columntype = 'VARCHAR(250)';
        $field1->uitype = 21;
        $field1->typeofdata = 'V~O';
    
        $block11->addField($field1);
    }
$field2 = Vtiger_Field::getInstance('pobox', $module);
    if ($field2) {
        echo "<br> Field 'pobox' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_POTENTIALS_POBOX';
        $field2->name = 'pobox';
        $field2->table = 'vtiger_potential';
        $field2->column = 'pobox';
        $field2->columntype = 'VARCHAR(30)';
        $field2->uitype = 1;
        $field2->typeofdata = 'V~O';
    
        $block11->addField($field2);
    }
$field3 = Vtiger_Field::getInstance('city', $module);
    if ($field3) {
        echo "<br> Field 'city' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_POTENTIALS_CITY';
        $field3->name = 'city';
        $field3->table = 'vtiger_potential';
        $field3->column = 'city';
        $field3->columntype = 'VARCHAR(50)';
        $field3->uitype = 1;
        $field3->typeofdata = 'V~O';
    
        $block11->addField($field3);
    }
$field4 = Vtiger_Field::getInstance('state', $module);
    if ($field4) {
        echo "<br> Field 'state' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_POTENTIALS_STATE';
        $field4->name = 'state';
        $field4->table = 'vtiger_potential';
        $field4->column = 'state';
        $field4->columntype = 'VARCHAR(50)';
        $field4->uitype = 1;
        $field4->typeofdata = 'V~O';
    
        $block11->addField($field4);
    }
$field5 = Vtiger_Field::getInstance('zip', $module);
    if ($field5) {
        echo "<br> Field 'zip' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_POTENTIALS_ZIP';
        $field5->name = 'zip';
        $field5->table = 'vtiger_potential';
        $field5->column = 'zip';
        $field5->columntype = 'VARCHAR(30)';
        $field5->uitype = 1;
        $field5->typeofdata = 'V~O';
    
        $block11->addField($field5);
    }
$field6 = Vtiger_Field::getInstance('country', $module);
    if ($field6) {
        echo "<br> Field 'country' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_POTENTIALS_COUNTRY';
        $field6->name = 'country';
        $field6->table = 'vtiger_potential';
        $field6->column = 'country';
        $field6->columntype = 'VARCHAR(50)';
        $field6->uitype = 1;
        $field6->typeofdata = 'V~O';
    
        $block11->addField($field6);
    }

//get rid of the related list to Campaigns
$module->unsetRelatedList(Vtiger_Module::getInstance('Campaigns'), 'Campaigns', 'get_campaigns');

//Update database_version table to show that we have finished updating the database to version 0.2
Vtiger_Utils::ExecuteQuery("INSERT INTO `database_version` (`movehq`, `db_version`) VALUES ('0', '0.2')");

echo "<h1><br>END OF SCRIPT</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";