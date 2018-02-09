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


echo "<br><h1>Adding Disposition Lost Reasons</h1><br>";

$Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

$db = PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('Leads');
$block = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $module);

$field1 = Vtiger_Field::getInstance('disposition_lost_reasons', $module);
if ($field1) {
    echo "<br> The disposition_lost_reasons field already exists";
} else {
    echo "<br> The disposition_lost_reasons field doesn't exist creating it now.";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_LEADS_DISPOSITIONLOSTREASONS';
    $field1->name = 'disposition_lost_reasons';
    $field1->table = 'vtiger_leaddetails';
    $field1->column = 'disposition_lost_reasons';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block->addField($field1);
    $field1->setPicklistValues([
        'Move Date has passed',
        'Capacity/Scheduling',
        'Pricing',
        'No Longer Moving',
        'Moving Themselves',
        'No Contact',
        'Past Experience',
        'National Account Move',
        'Incomplete Customer Info',
        'Out of Time',
        'Appointment Cancelled',
        'Not Serviceable',
        'Move too small',
        'Other',
        ]);
}
echo "<br><h1>Reording fields in the block</h1><br>";
$fieldSeq = [
    'salutationtype'=> 0,
    'firstname'=> 1,
    'lead_no'=> 2,
    'lastname'=> 3,
    'phone'=> 4,
    'fax'=> 5,
    'primary_phone_type'=> 6,
    'designation'=> 7,
    'mobile'=> 8,
    'website'=> 9,
    'industry'=> 10,
    'emailoptout'=> 11,
    'email'=> 12,
    'secondaryemail'=> 13,
    'leadsource'=> 14,
    'assigned_user_id'=> 15,
    'leadstatus'=> 16,
    'annualrevenue'=> 17,
    'noofemployees'=> 18,
    'rating'=> 19,
    'business_line'=> 20,
    'disposition_lost_reasons'=> 21,
    'include_packing'=> 22,
    'comm_res'=> 23,
    'modifiedby'=> 24,
    'sales_person'=> 25,
    'shipper_type'=> 26,
    'lead_type'=> 27,
    'move_type'=> 28,
    'business_channel'=> 29,
    'funded'=> 30,
    'out_of_area'=> 31,
    'out_of_origin'=> 32,
    'small_move'=> 33,
    'phone_estimate'=> 34,
    'primary_phone_ext'=> 35,
    'createdtime'=> 36,
    'modifiedtime'=> 37,
    'created_user_id'=> 38,
    //if you need to add new fields add them to this and set the sequence values appropriately
    //set up as 'fieldname'=> sequence,
];
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

if (!Vtiger_Utils::CheckTable('vtiger_sirva_pricing_comp')) {
    echo "<li>creating vtiger_sirva_pricing_comp </li><br>";
    Vtiger_Utils::CreateTable('vtiger_sirva_pricing_comp',
                              '(
								leadid INT(19),
							    allied TINYINT(1),
								atlas TINYINT(1),
								mayflower TINYINT(1),
								north_american TINYINT(1),
								united TINYINT(1),
								independent TINYINT(1),
								other TINYINT(1)
								)', true);
}
echo "<br><h1>Finished Disposition Lost Reasons Hotfix</h1><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";