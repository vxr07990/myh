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


/*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Pricing Level Changes</h1><br>\n";
//get both Estimates and Quotes since they are coupled.
$quotes = Vtiger_Module::getInstance('Quotes');
$est = Vtiger_Module::getInstance('Estimates');

//grab each copy of pricing color so we can disable it
$field1 = Vtiger_Field::getInstance('pricing_color', $quotes);
$field2 = Vtiger_Field::getInstance('pricing_color', $est);
$field3 = Vtiger_Field::getInstance('pricing_type', $quotes);
$field4 = Vtiger_Field::getInstance('pricing_type', $est);
echo "<br>Field1: ".$field1->id."<br>Field2: ".$field2->id."<br>Field3: ".$field3->id."<br>Field4: ".$field4->id;
//disable pricing color for both Estimates and Quotes
echo "<br>UPDATE `vtiger_field` SET presence=1 WHERE fieldid IN (".$field1->id.",".$field2->id.",".$field3->id.",".$field4->id.")<br>";
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid IN (".$field1->id.",".$field2->id.",".$field3->id.",".$field4->id.")");

//grab the blocks so we can jam the new stuff in the right place
$block1 = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $quotes);
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $est);


$field3 = Vtiger_Field::getInstance('demand_color', $quotes);
if ($field3) {
    echo "<br> The demand_color field already exists in Quotes <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_DEMAND_COLOR';
    $field3->name = 'demand_color';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'demand_color';
    $field3->columntype = 'VARCHAR(30)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~O';
    $field3->displaytype = 1;
    $field3->quickcreate = 0;
    $field3->presence = 2;
    $block1->addField($field3);
    //only do this once or we'll end up with duplicates in the picklist
    $field3->setPicklistValues(['Green', 'Yellow', 'Blue', 'Red', 'Gold']);
}
$field4 = Vtiger_Field::getInstance('demand_color', $est);
if ($field4) {
    echo "<br> The demand_color field already exists in Estimates <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_DEMAND_COLOR';
    $field4->name = 'demand_color';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'demand_color';
    $field4->columntype = 'VARCHAR(30)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~O';
    $field4->displaytype = 1;
    $field4->quickcreate = 0;
    $field4->presence = 2;

    $block2->addField($field4);
}
$field5 = Vtiger_Field::getInstance('pricing_level', $quotes);
if ($field5) {
    echo "<br> The pricing_level field already exists in Quotes <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_PRICING_LEVEL';
    $field5->name = 'pricing_level';
    $field5->table = 'vtiger_quotes';
    $field5->column = 'pricing_level';
    $field5->columntype = 'VARCHAR(30)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~O';
    $field5->displaytype = 1;
    $field5->quickcreate = 0;
    $field5->presence = 2;

    $block1->addField($field5);
    //only do this once or we'll end up with duplicates in the picklist
    $field5->setPicklistValues(['Level 1',
                                'Level 2',
                                'Level 3',
                                'Level 4',
                                'Level 5',
                                'Level 6',
                                'Level 7',
                                'Level 8',
                                'Level 9',
                                'Level 10',
                                'Level 11',
                                'Level 12',
                               ]);
}
$field6 = Vtiger_Field::getInstance('pricing_level', $est);
if ($field6) {
    echo "<br> The pricing_level field already exists in Estimates <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_PRICING_LEVEL';
    $field6->name = 'pricing_level';
    $field6->table = 'vtiger_quotes';
    $field6->column = 'pricing_level';
    $field6->columntype = 'VARCHAR(30)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';
    $field6->displaytype = 1;
    $field6->quickcreate = 0;
    $field6->presence = 2;

    $block2->addField($field6);
}

//reorder stuff so it doesn't look terrible in the UI
$fieldSeq1 = [
              'pricing_color_lock'=>1,
              'pricing_color'=>8,
              'demand_color'=>2,
              'pricing_level'=>3,
              'percent_smf'=>4,
              'flat_smf'=>5,
              'desired_total'=>6,
              'smf_type'=>7,
             ];
$fieldSeq2 = [
              'pricing_color_lock'=>1,
              'pricing_color'=>12,
              'demand_color'=>2,
              'pricing_level'=>3,
              'percent_smf'=>4,
              'flat_smf'=>5,
              'desired_total'=>6,
              'smf_type'=>7,
              'grr'=>8,
              'grr_override_amount'=>9,
              'grr_override'=>10,
              'grr_cp'=>11,
             ];
$block3 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $quotes);
$block4 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $est);
$fieldSeq3 = [
              'weight'=>1,
              'pickup_date'=>2,
              'full_pack'=>3,
              'full_unpack'=>4,
              'bottom_line_discount'=>5,
              'interstate_mileage'=>6,
              'linehaul_disc'=>7,
              'accessorial_disc'=>8,
              'packing_disc'=>9,
              'sit_disc'=>10,
              'interstate_effective_date'=>11,
              'estimate_type'=>12,
              'apply_full_pack_rate_override'=>13,
              'full_pack_rate_override'=>14,
              'effective_tariff'=>15,
              'pricing_type'=>16,
             ];
reorderBlockPricingLevel($fieldSeq1, $block1, $quotes);
reorderBlockPricingLevel($fieldSeq2, $block2, $est);
reorderBlockPricingLevel($fieldSeq3, $block3, $quotes);
reorderBlockPricingLevel($fieldSeq3, $block4, $est);
echo "<br><h1>Finished Hotfix Pricing Level Changes</h1><br>\n";

function reorderBlockPricingLevel($fieldSeq, $block, $module)
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