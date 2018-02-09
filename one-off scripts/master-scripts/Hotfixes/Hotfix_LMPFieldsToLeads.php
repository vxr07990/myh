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


//include this stuff to run independent of master script
// $Vtiger_Utils_Log = true;
// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';
echo "<br><h1> Adding LMP fields to Leads</h1><br>";
$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Leads');

//Above Comments in LBL_LEADS_INFORMATION
//LMPLeadId uneditable string field
$block1 = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $module);

$field1 = Vtiger_Field::getInstance('lmp_lead_id', $module);
if ($field1) {
    echo "<br> The lmp_lead_id field already exists";
} else {
    echo "<br> The lmp_lead_id field doesn't exist creating it now.";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_LEADS_LMPLEADID';
    $field1->name = 'lmp_lead_id';
    $field1->table = 'vtiger_leaddetails';
    $field1->column = 'lmp_lead_id';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';

    $block1->addField($field1);
}
//CCDisposition uneditable string
$field2 = Vtiger_Field::getInstance('cc_disposition', $module);
if ($field2) {
    echo "<br> The cc_disposition field already exists";
} else {
    echo "<br> The cc_disposition field doesn't exist creating it now.";
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_LEADS_CCDISPOSITION';
    $field2->name = 'cc_disposition';
    $field2->table = 'vtiger_leaddetails';
    $field2->column = 'cc_disposition';
    $field2->columntype = 'VARCHAR(50)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';

    $block1->addField($field2);
}
//Brand string
$field3 = Vtiger_Field::getInstance('brand', $module);
if ($field3) {
    echo "<br> The brand field already exists";
} else {
    echo "<br> The brand field doesn't exist creating it now.";
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_LEADS_BRAND';
    $field3->name = 'brand';
    $field3->table = 'vtiger_leaddetails';
    $field3->column = 'brand';
    $field3->columntype = 'VARCHAR(50)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';

    $block1->addField($field3);
}
//Organization string
$field4 = Vtiger_Field::getInstance('organization', $module);
if ($field4) {
    echo "<br> The organization field already exists";
} else {
    echo "<br> The organization field doesn't exist creating it now.";
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_LEADS_ORGANIZATION';
    $field4->name = 'organization';
    $field4->table = 'vtiger_leaddetails';
    $field4->column = 'organization';
    $field4->columntype = 'VARCHAR(50)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
//LeadReceiveDate Date
$field5 = Vtiger_Field::getInstance('lead_receive_date', $module);
if ($field5) {
    echo "<br> The lead_receive_date field already exists";
} else {
    echo "<br> The lead_receive_date field doesn't exist creating it now.";
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_LEADS_LEADRECEIVEDATE';
    $field5->name = 'lead_receive_date';
    $field5->table = 'vtiger_leaddetails';
    $field5->column = 'lead_receive_date';
    $field5->columntype = 'DATE';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~O';

    $block1->addField($field5);
}

//In LBL_LEADS_INFORMATION after Contact Info
//PreferTime Picklist of AM, PM, Either
$field6 = Vtiger_Field::getInstance('prefer_time', $module);
if ($field6) {
    echo "<br> The prefer_time field already exists";
} else {
    echo "<br> The prefer_time field doesn't exist creating it now.";
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_LEADS_PREFERTIME';
    $field6->name = 'prefer_time';
    $field6->table = 'vtiger_leaddetails';
    $field6->column = 'prefer_time';
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $block1->addField($field6);
    $field6->setPicklistValues([
        'AM',
        'PM',
        'Either',
        ]);
}
//TimeZone Picklist of ADT, AST, AKDT, AKST, CDT, CST, EDT, EST, HADT, HAST, MDT, MST, NDT, NST, PDT, or PST
$field7 = Vtiger_Field::getInstance('timezone', $module);
if ($field7) {
    echo "<br> The timezone field already exists";
} else {
    echo "<br> The timezone field doesn't exist creating it now.";
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_LEADS_TIMEZONE';
    $field7->name = 'timezone';
    $field7->table = 'vtiger_leaddetails';
    $field7->column = 'timezone';
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 16;
    $field7->typeofdata = 'V~O';

    $block1->addField($field7);
    $field7->setPicklistValues([
        'ADT',
        'AST',
        'AKDT',
        'AKST',
        'CDT',
        'CST',
        'EDT',
        'EST',
        'HADT',
        'HAST',
        'MDT',
        'MST',
        'NDT',
        'NST',
        'PDT',
        'PST',
        ]);
}
//Language Picklist of English, French, Spanish, Others
$field8 = Vtiger_Field::getInstance('languages', $module);
if ($field8) {
    echo "<br> The languages field already exists";
} else {
    echo "<br> The languages field doesn't exist creating it now.";
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_LEADS_LANGUAGE';
    $field8->name = 'languages';
    $field8->table = 'vtiger_leaddetails';
    $field8->column = 'languages';
    $field8->columntype = 'VARCHAR(255)';
    $field8->uitype = 16;
    $field8->typeofdata = 'V~O';

    $block1->addField($field8);
    $field8->setPicklistValues([
        'English',
        'French',
        'Spanish',
        'Others',
        ]);
}

//Immediately after Funded in  LBL_LEADS_INFORMATION
//ProgramName String
$field9 = Vtiger_Field::getInstance('program_name', $module);
if ($field9) {
    echo "<br> The program_name field already exists";
} else {
    echo "<br> The program_name field doesn't exist creating it now.";
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_LEADS_PROGRAMNAME';
    $field9->name = 'program_name';
    $field9->table = 'vtiger_leaddetails';
    $field9->column = 'program_name';
    $field9->columntype = 'VARCHAR(50)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';

    $block1->addField($field9);
}
//SourceName String
$field10 = Vtiger_Field::getInstance('source_name', $module);
if ($field10) {
    echo "<br> The source_name field already exists";
} else {
    echo "<br> The source_name field doesn't exist creating it now.";
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_LEADS_SOURCENAME';
    $field10->name = 'source_name';
    $field10->table = 'vtiger_leaddetails';
    $field10->column = 'source_name';
    $field10->columntype = 'VARCHAR(50)';
    $field10->uitype = 1;
    $field10->typeofdata = 'V~O';

    $block1->addField($field10);
}
//OfferNumber String
$field11 = Vtiger_Field::getInstance('offer_number', $module);
if ($field11) {
    echo "<br> The offer_number field already exists";
} else {
    echo "<br> The offer_number field doesn't exist creating it now.";
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_LEADS_OFFERNUMBER';
    $field11->name = 'offer_number';
    $field11->table = 'vtiger_leaddetails';
    $field11->column = 'offer_number';
    $field11->columntype = 'VARCHAR(50)';
    $field11->uitype = 1;
    $field11->typeofdata = 'V~O';

    $block1->addField($field11);
}
//PromotionTerms textbox
$field12 = Vtiger_Field::getInstance('promotion_terms', $module);
if ($field12) {
    echo "<br> The promotion_terms field already exists";
} else {
    echo "<br> The promotion_terms field doesn't exist creating it now.";
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_LEADS_PROMOTIONTERMS';
    $field12->name = 'promotion_terms';
    $field12->table = 'vtiger_leaddetails';
    $field12->column = 'promotion_terms';
    $field12->columntype = 'VARCHAR(255)';
    $field12->uitype = 19;
    $field12->typeofdata = 'V~O';

    $block1->addField($field12);
}

$block2 = Vtiger_Block::getInstance('LBL_LEADS_ADDRESSINFORMATION', $module);
//In LBL_LEADS_ADDRESSINFORMATION after Origin stuff
//OwnCurrent Picklist of Yes, No, Not Sure, Refused
$field13 = Vtiger_Field::getInstance('own_current', $module);
if ($field13) {
    echo "<br> The own_current field already exists";
} else {
    echo "<br> The own_current field doesn't exist creating it now.";
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_LEADS_OWNCURRENT';
    $field13->name = 'own_current';
    $field13->table = 'vtiger_leaddetails';
    $field13->column = 'own_current';
    $field13->columntype = 'VARCHAR(255)';
    $field13->uitype = 16;
    $field13->typeofdata = 'V~O';

    $block2->addField($field13);
    $field13->setPicklistValues([
        'Yes',
        'No',
        'Not Sure',
        'Refused',
        ]);
}
//DwellingType Picklist of 1 Bedroom Apt., 1 Bedroom House, 2 Bedroom Apt., 2 Bedroom House, 3 Bedroom House, or 3+ Bedroom Apt.
$field14 = Vtiger_Field::getInstance('dwelling_type', $module);
if ($field14) {
    echo "<br> The dwelling_type field already exists";
} else {
    echo "<br> The dwelling_type field doesn't exist creating it now.";
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_LEADS_DWELLINGTYPE';
    $field14->name = 'dwelling_type';
    $field14->table = 'vtiger_leaddetails';
    $field14->column = 'dwelling_type';
    $field14->columntype = 'VARCHAR(255)';
    $field14->uitype = 16;
    $field14->typeofdata = 'V~O';

    $block2->addField($field14);
    $field14->setPicklistValues([
        '1 Bedroom Apt.',
        '1 Bedroom House',
        '2 Bedroom Apt.',
        '2 Bedroom House',
        '3 Bedroom House',
        '3+ Bedroom Apt.',
        ]);
}
//FurnishLevel Picklist Light, Medium, Heavy
$field15 = Vtiger_Field::getInstance('furnish_level', $module);
if ($field15) {
    echo "<br> The furnish_level field already exists";
} else {
    echo "<br> The furnish_level field doesn't exist creating it now.";
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_LEADS_FURNISHLEVEL';
    $field15->name = 'furnish_level';
    $field15->table = 'vtiger_leaddetails';
    $field15->column = 'furnish_level';
    $field15->columntype = 'VARCHAR(255)';
    $field15->uitype = 16;
    $field15->typeofdata = 'V~O';

    $block2->addField($field15);
    $field15->setPicklistValues([
        'Light',
        'Medium',
        'Heavy',
        ]);
}

//In LBL_LEADS_ADDRESSINFORMATION after Dest stuff
//OwnNew Picklist of Yes, No, Not Sure, Refused
$field16 = Vtiger_Field::getInstance('own_new', $module);
if ($field16) {
    echo "<br> The own_new field already exists";
} else {
    echo "<br> The own_new field doesn't exist creating it now.";
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_LEADS_OWNNEW';
    $field16->name = 'own_new';
    $field16->table = 'vtiger_leaddetails';
    $field16->column = 'own_new';
    $field16->columntype = 'VARCHAR(255)';
    $field16->uitype = 16;
    $field16->typeofdata = 'V~O';

    $block2->addField($field16);
    $field16->setPicklistValues([
        'Yes',
        'No',
        'Not Sure',
        'Refused',
        ]);
}
$block3 = Vtiger_Block::getInstance('LBL_LEADS_DATES', $module);
//At start LBL_LEADS_DATES
//FlexibleOnDays Checkbox
$field17 = Vtiger_Field::getInstance('flexible_on_days', $module);
if ($field17) {
    echo "<br> The flexible_on_days field already exists";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_LEADS_FLEXIBLEONDAYS';
    $field17->name = 'flexible_on_days';
    $field17->table = 'vtiger_leaddetails';
    $field17->column = 'flexible_on_days';
    $field17->columntype = 'VARCHAR(3)';
    $field17->uitype = 56;
    $field17->typeofdata = 'C~O';

    $block3->addField($field17);
}
//FulfillmentDate Date
$field18 = Vtiger_Field::getInstance('fulfillment_date', $module);
if ($field18) {
    echo "<br> The fulfillment_date field already exists";
} else {
    echo "<br> The fulfillment_date field doesn't exist creating it now.";
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_LEADS_FULFILLMENTDATE';
    $field18->name = 'fulfillment_date';
    $field18->table = 'vtiger_leaddetails';
    $field18->column = 'fulfillment_date';
    $field18->columntype = 'DATE';
    $field18->uitype = 5;
    $field18->typeofdata = 'D~O';

    $block3->addField($field18);
}

//add vehicle block with after LBL_LEADS_DATES
$block4 = Vtiger_Block::getInstance('LBL_LEADS_VEHICLES', $module);
if ($block4) {
    echo "<br> The LBL_LEADS_VEHICLES block already exists";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_LEADS_VEHICLES';
    $module->addBlock($block4);
}
//MovingVehicle checkbox
$field19 = Vtiger_Field::getInstance('moving_vehicle', $module);
if ($field19) {
    echo "<br> The moving_vehicle field already exists";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_LEADS_MOVINGVEHICLE';
    $field19->name = 'moving_vehicle';
    $field19->table = 'vtiger_leaddetails';
    $field19->column = 'moving_vehicle';
    $field19->columntype = 'VARCHAR(3)';
    $field19->uitype = 56;
    $field19->typeofdata = 'C~O';

    $block4->addField($field19);
}
//NumberOfVehicles integer
$field20 = Vtiger_Field::getInstance('number_of_vehicles', $module);
if ($field20) {
    echo "<br> The number_of_vehicles field already exists";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_LEADS_NUMBEROFVEHICLES';
    $field20->name = 'number_of_vehicles';
    $field20->table = 'vtiger_leaddetails';
    $field20->column = 'number_of_vehicles';
    $field20->columntype = 'INT(19)';
    $field20->uitype = 7;
    $field20->typeofdata = 'I~O';

    $block4->addField($field20);
}
//VehicleYear integer
$field21 = Vtiger_Field::getInstance('vehicle_year', $module);
if ($field21) {
    echo "<br> The vehicle_year field already exists";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_LEADS_VEHICLEYEAR';
    $field21->name = 'vehicle_year';
    $field21->table = 'vtiger_leaddetails';
    $field21->column = 'vehicle_year';
    $field21->columntype = 'INT(19)';
    $field21->uitype = 7;
    $field21->typeofdata = 'I~O';

    $block4->addField($field21);
}
//VehicleMake string
$field22 = Vtiger_Field::getInstance('vehicle_make', $module);
if ($field22) {
    echo "<br> The vehicle_make field already exists";
} else {
    echo "<br> The vehicle_make field doesn't exist creating it now.";
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_LEADS_VEHICLEMAKE';
    $field22->name = 'vehicle_make';
    $field22->table = 'vtiger_leaddetails';
    $field22->column = 'vehicle_make';
    $field22->columntype = 'VARCHAR(50)';
    $field22->uitype = 1;
    $field22->typeofdata = 'V~O';

    $block4->addField($field22);
}
//VehicleModel string
$field23 = Vtiger_Field::getInstance('vehicle_model', $module);
if ($field23) {
    echo "<br> The vehicle_model field already exists";
} else {
    echo "<br> The vehicle_model field doesn't exist creating it now.";
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_LEADS_VEHICLEMODEL';
    $field23->name = 'vehicle_model';
    $field23->table = 'vtiger_leaddetails';
    $field23->column = 'vehicle_model';
    $field23->columntype = 'VARCHAR(50)';
    $field23->uitype = 1;
    $field23->typeofdata = 'V~O';

    $block4->addField($field23);
}

//OfferValuationFlg Checkbox end of Information Block
$field24 = Vtiger_Field::getInstance('offer_valuation', $module);
if ($field24) {
    echo "<br> The offer_valuation field already exists";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_LEADS_OFFERVALUATION';
    $field24->name = 'offer_valuation';
    $field24->table = 'vtiger_leaddetails';
    $field24->column = 'offer_valuation';
    $field24->columntype = 'VARCHAR(3)';
    $field24->uitype = 56;
    $field24->typeofdata = 'C~O';

    $block1->addField($field24);
}
//OutofTimeFlg Checkbox in non-conforming
$field25 = Vtiger_Field::getInstance('out_of_time', $module);
if ($field25) {
    echo "<br> The out_of_time field already exists";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_LEADS_OUTOFTIME';
    $field25->name = 'out_of_time';
    $field25->table = 'vtiger_leaddetails';
    $field25->column = 'out_of_time';
    $field25->columntype = 'VARCHAR(3)';
    $field25->uitype = 56;
    $field25->typeofdata = 'C~O';

    $block1->addField($field25);
}

$fieldSeq1 = [
    'lmp_lead_id'=> 0,
    'cc_disposition'=> 1,
    'brand'=> 2,
    'organization'=> 3,
    'lead_receive_date'=> 4,
    'salutationtype'=> 5,
    'firstname'=> 6,
    'lead_no'=> 7,
    'lastname'=> 8,
    'phone'=> 9,
    'fax'=> 10,
    'primary_phone_type'=> 11,
    'designation'=> 12,
    'mobile'=> 13,
    'website'=> 14,
    'industry'=> 15,
    'email'=> 16,
    'emailoptout'=> 17,
    'secondaryemail'=> 18,
    'prefer_time'=> 19,
    'timezone'=> 20,
    'languages'=> 21,
    'leadsource'=> 22,
    'assigned_user_id'=> 23,
    'leadstatus'=> 24,
    'annualrevenue'=> 25,
    'noofemployees'=> 26,
    'rating'=> 27,
    'business_line'=> 28,
    'disposition_lost_reasons'=> 29,
    'include_packing'=> 30,
    'comm_res'=> 31,
    'modifiedby'=> 32,
    'sales_person'=> 33,
    'shipper_type'=> 34,
    'lead_type'=> 35,
    'move_type'=> 36,
    'business_channel'=> 37,
    'funded'=> 38,
    'program_name'=>39,
    'source_name'=>40,
    'offer_number'=>41,
    'offer_valuation'=>42,
    'promotion_terms'=>43,
    'out_of_time'=>44,
    'out_of_area'=> 45,
    'out_of_origin'=> 46,
    'small_move'=> 47,
    'phone_estimate'=> 48,
    'primary_phone_ext'=> 49,
    'createdtime'=> 50,
    'modifiedtime'=> 51,
    'created_user_id'=> 5,
];
$fieldSeq2 = [
    'origin_address1' => 0,
    'destination_address1' => 1,
    'origin_address2' => 2,
    'destination_address2' => 3,
    'origin_city' => 4,
    'destination_city' => 5,
    'origin_state' => 6,
    'destination_state' => 7,
    'origin_zip' => 8,
    'destination_zip' => 9,
    'origin_country' => 10,
    'destination_country' => 11,
    'origin_phone1' => 12,
    'destination_phone1' => 13,
    'origin_phone1_type' => 14,
    'destination_phone1_type' => 15,
    'origin_phone2' => 16,
    'destination_phone2' => 17,
    'origin_phone2_type' => 18,
    'destination_phone2_type' => 19,
    'origin_fax' => 20,
    'destination_fax' => 21,
    'origin_flightsofstairs' => 22,
    'destination_flightsofstairs' => 23,
    'origin_description' => 24,
    'destination_description' => 25,
    'own_current' => 26,
    'own_new' => 27,
    'dwelling_type' => 28,
    'furnish_level' => 29,
    'origin_phone1_ext' => 30,
    'origin_phone2_ext' => 31,
    'destination_phone1_ext' => 32,
    'destination_phone2_ext' => 33,
];

$fieldSeq3 = [
    'flexible_on_days' => 1,
    'fulfillment_date' => 2,
    'pack' => 3,
    'pack_to' => 4,
    'preferred_ppdate' => 5,
    'load_from' => 6,
    'load_to' => 7,
    'preferred_pldate' => 8,
    'deliver' => 9,
    'deliver_to' => 10,
    'preferred_pddate' => 11,
    'follow_up' => 12,
    'decision' => 13,
    'days_to_move' => 14,
];
echo "<br><h1> Reording Block1</h1><br>";
reorderBlocks($fieldSeq1, $block1, $module);
echo "<br><h1> Reording Block2</h1><br>";
reorderBlocks($fieldSeq2, $block2, $module);
echo "<br><h1> Reording Block3</h1><br>";
reorderBlocks($fieldSeq3, $block3, $module);
echo "<br><h1> Finished Hotfix_LMPFieldsToLeads.php</h1><br>";

function reorderBlocks($fieldSeq, $block, $module)
{
    $db = PearDatabase::getInstance();
    $push_to_end = [];
    foreach ($fieldSeq as $name=>$seq) {
        $field = Vtiger_Field::getInstance($name, $module);
        if ($field) {
            $sql = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
            $result = $db->pquery($sql, [$seq, $block->id]);
            if ($result) {
                while ($row = $result->fetchRow()) {
                    $push_to_end[] = $row[0];
                }
            }
            Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
        }
        unset($field);
    }
    //push anything that might have gotten added and isn't on the list to the end of the block
    $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0]+1;
    foreach ($push_to_end as $name) {
        //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
        if (!array_key_exists($name, $fieldSeq)) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
                $max++;
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";