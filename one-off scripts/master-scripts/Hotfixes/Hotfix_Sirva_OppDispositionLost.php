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



//add an opportunities disposition lost block, field, and picklist
//add a disposition-lost-other text input.

$Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

echo "<br><h1>Adding Disposition Lost Reasons to Opportunities</h1><br>";
Hotfix_Sirva_OppDispositionLost();
echo "<br><h1>Finished Disposition Lost Reasons Hotfix</h1><br>";

echo "<br><h1>Adding Disposition Lost Reasons OTHER to Leads</h1><br>";
Hotfix_Sirva_LeadDispositionLostOther();
echo "<br><h1>Finished Disposition Lost Reasons Hotfix</h1><br>";

function Hotfix_Sirva_LeadDispositionLostOther()
{
    $db = PearDatabase::getInstance();
    $module = Vtiger_Module::getInstance('Leads');
    if ($module) {
        $block = Vtiger_Block::getInstance('LBL_LEADS_BLOCK_LMPDETAILS', $module);
        if (!$block) {
            echo "<br /> HERE I AM CREATING A BLOCK <br />";
            $block        = new Vtiger_Block();
            $block->label = 'LBL_LEADS_BLOCK_LMPDETAILS';
            $module->addBlock($block);
        }
        $field0 = Vtiger_Field::getInstance('disposition_lost_reasons_other', $module);
        if ($field0) {
            echo "<br> The disposition_lost_reasons_other field already exists";
        } else {
            echo "<br> The disposition_lost_reasons_other field doesn't exist creating it now.";
            $field0             = new Vtiger_Field();
            $field0->label      = 'LBL_LEAD_DISPOSITIONLOSTOTHER';
            $field0->name       = 'disposition_lost_reasons_other';
            $field0->table      = 'vtiger_leaddetails';
            $field0->column     = 'disposition_lost_reasons_other';
            $field0->columntype = 'VARCHAR(255)';
            $field0->uitype     = 1;
            $field0->typeofdata = 'V~O';
            $block->addField($field0);
        }
        echo "<br><h1>Reording fields in the block</h1><br>";
        $fieldSeq = [
                    'lmp_lead_id'                    =>0,
                    'cc_disposition'                 =>1,
                    'brand'                          =>2,
                    'lead_no'                        =>3,
                    'emailoptout'                    =>4,
                    'timezone'                       =>5,
                    'languages'                      =>6,
                    'leadsource'                     =>7,
                    'disposition_lost_reasons'       =>8,
                    'disposition_lost_reasons_other' =>9,
                    'lead_type'                      =>10,
                    'business_channel'               =>11,
                    'funded'                         =>12,
                    'program_name'                   =>13,
                    'source_name'                    =>14,
                    'offer_number'                   =>15,
                    'offer_valuation'                =>16,
                    'promotion_terms'                =>17,
                    'out_of_time'                    =>18,
                    'out_of_area'                    =>19,
                    'out_of_origin'                  =>20,
                    'small_move'                     =>21,
                    'phone_estimate'                 =>22,
                    //if you need to add new fields add them to this and set the sequence values appropriately
                    //set up as 'fieldname'=> sequence,
                ];
        $push_to_end = [];
        foreach ($fieldSeq as $name => $seq) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                $result = $db->pquery($sql, [$seq, $block->id]);
                if ($result) {
                    while ($row = $result->fetchRow()) {
                        $push_to_end[] = $row[0];
                    }
                }
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.
                                           '" AND fieldid = '.$field->id);
                print 'UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '
                      .$field->id."<br />\n";
            }
            unset($field);
        }
        //@TODO: something is weird here I would expect it to use the sequnce from above, but it doesn't unless I run twice.
        //have to check when I've some time.
        //push anything that might have gotten added and isn't on the list to the end of the block
        $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
        foreach ($push_to_end as $name) {
            //foreach(reverse_array($push_to_end) as $name){
            //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
            if (!array_key_exists($name, $fieldSeq)) {
                $field = Vtiger_Field::getInstance($name, $module);
                if ($field) {
                    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.
                                               '" AND fieldid = '.$field->id);
                    $max++;
                }
            }
        }
    }
}

function Hotfix_Sirva_OppDispositionLost()
{
    $db = PearDatabase::getInstance();
    $module = Vtiger_Module::getInstance('Opportunities');
    if ($module) {
        $block = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $module);
        $picklistValues = [
            'Appointment Cancelled',
            'Capacity/Scheduling',
            'Incomplete Customer Info',
            'Move Date Has Passed',
            'Move too Small',
            'Moving Themselves',
            'National Account Move',
            'No Contact',
            'No Longer Moving',
            'Not Serviceable',
            'Other',
            'Out of Time',
            'Past Experience',
            'Pricing'
        ];
        if (!$block) {
            $block        = new Vtiger_Block();
            $block->label = 'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS';
            $module->addBlock($block);
        }
        $field0 = Vtiger_Field::getInstance('disposition_lost_reasons_other', $module);
        if ($field0) {
            echo "<br> The disposition_lost_reasons_other field already exists";
        } else {
            echo "<br> The disposition_lost_reasons_other field doesn't exist creating it now.";
            $field0             = new Vtiger_Field();
            $field0->label      = 'LBL_OPPORTUNITY_OPPORTUNITYDETAILDISPOSITIONOTHER';
            $field0->name       = 'disposition_lost_reasons_other';
            $field0->table      = 'vtiger_potential';
            $field0->column     = 'disposition_lost_reasons_other';
            $field0->columntype = 'VARCHAR(255)';
            $field0->uitype     = 1;
            $field0->typeofdata = 'V~O';
            $block->addField($field0);
        }
        $field1 = Vtiger_Field::getInstance('disposition_lost_reasons', $module);
        if ($field1) {
            echo "<br> The disposition_lost_reasons field already exists";
        } else {
            echo "<br> The disposition_lost_reasons field doesn't exist creating it now.";
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_OPPORTUNITY_OPPORTUNITYDETAILDISPOSITIONLOST';
            $field1->name       = 'disposition_lost_reasons';
            $field1->table      = 'vtiger_potential';
            $field1->column     = 'disposition_lost_reasons';
            $field1->columntype = 'VARCHAR(255)';
            $field1->uitype     = 16;
            $field1->typeofdata = 'V~O';
            $block->addField($field1);
            $field1->setPicklistValues($picklistValues);
        }
        echo "<br><h1>Reording fields in the block</h1><br>";
        $fieldSeq = [
            'opp_type'                       => 1,
            'opportunity_disposition'        => 2,
            'leadsource'                     => 3,
            'phone_estimate'                 => 4,
            'receive_date'                   => 5,
            'out_of_origin'                  => 6,
            'converted_from'                 => 7,
            'small_move'                     => 8,
            'isconvertedfromlead'            => 9,
            'out_of_area'                    => 10,
            'opportunity_detail_disposition' => 11,
            'disposition_lost_reasons'       => 12,
            'disposition_lost_reasons_other' => 13,
            //if you need to add new fields add them to this and set the sequence values appropriately
            //set up as 'fieldname'=> sequence,
        ];
        $push_to_end = [];
        foreach ($fieldSeq as $name => $seq) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                $result = $db->pquery($sql, [$seq, $block->id]);
                if ($result) {
                    while ($row = $result->fetchRow()) {
                        $push_to_end[] = $row[0];
                    }
                }
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.
                                           '" AND fieldid = '.$field->id);
                print 'UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '
                      .$field->id."<br />\n";
            }
            unset($field);
        }
        //@TODO: something is weird here I would expect it to use the sequnce from above, but it doesn't unless I run twice.
        //have to check when I've some time.
        //push anything that might have gotten added and isn't on the list to the end of the block
        $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
        foreach ($push_to_end as $name) {
            //foreach(reverse_array($push_to_end) as $name){
            //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
            if (!array_key_exists($name, $fieldSeq)) {
                $field = Vtiger_Field::getInstance($name, $module);
                if ($field) {
                    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.
                                               '" AND fieldid = '.$field->id);
                    $max++;
                }
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";