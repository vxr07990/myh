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


$oppsModule = Vtiger_Module::getInstance('Opportunities');
$potsModule = Vtiger_Module::getInstance('Potentials');

$infoBlockOpps = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule);
$infoBlockPots = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potsModule);

$selfHaulFieldOpps = Vtiger_Field::getInstance('self_haul', $oppsModule);
if ($selfHaulFieldOpps) {
    echo '<br>opps self haul already exists';
} else {
    $selfHaulFieldOpps             = new Vtiger_Field();
    $selfHaulFieldOpps->label      = 'LBL_OPPORTUNITY_SELF_HAUL';
    $selfHaulFieldOpps->name       = 'self_haul_opp';
    $selfHaulFieldOpps->table      = 'vtiger_potential';
    $selfHaulFieldOpps->column     = 'self_haul';
    $selfHaulFieldOpps->columntype = 'VARCHAR(3)';
    $selfHaulFieldOpps->uitype = 56;
    $selfHaulFieldOpps->typeofdata = 'V~O';
    $selfHaulFieldOpps->displaytype = 1;
    $selfHaulFieldOpps->quickcreate = 0;
    $selfHaulFieldOpps->presence = 2;
    $selfHaulFieldOpps->readonly = 0;
    $infoBlockOpps->addField($selfHaulFieldOpps);
}

$selfHaulFieldPots = Vtiger_Field::getInstance('self_haul', $potsModule);
if ($selfHaulFieldPots) {
    echo '<br>opps self haul already exists';
} else {
    $selfHaulFieldPots             = new Vtiger_Field();
    $selfHaulFieldPots->label      = 'LBL_OPPORTUNITY_SELF_HAUL';
    $selfHaulFieldPots->name       = 'self_haul_opp';
    $selfHaulFieldPots->table      = 'vtiger_potential';
    $selfHaulFieldPots->column     = 'self_haul';
    $selfHaulFieldPots->columntype = 'VARCHAR(3)';
    $selfHaulFieldPots->uitype = 56;
    $selfHaulFieldPots->typeofdata = 'V~O';
    $selfHaulFieldPots->displaytype = 1;
    $selfHaulFieldPots->quickcreate = 0;
    $selfHaulFieldPots->presence = 2;
    $selfHaulFieldPots->readonly = 0;
    $infoBlockPots->addField($selfHaulFieldPots);
}

/*$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Opportunities');
if ($module) {
    echo "<h2>Updating Opportunities Fields</h2><br>";
    $block = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $module);
    if ($block) {
        echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br>";
        $field2 = Vtiger_Field::getInstance('self_haul', $module);
        if ($field2) {
            echo "<li>The self_haul field already exists for Opportunities</li><br>";

            if ($field2->getBlockId() != $block->id) {
                echo "<li>Moving to the Information block.</li><br>";
                $stmt = 'UPDATE `vtiger_field` SET `block`= ? WHERE `fieldid` = ?';
                $db->pquery($stmt, [$block->id, $field2->id]);
                echo "<br><h1>Changed self_haul field's block in Opportunities, Reordering fields in the block </h1><br>";
                ReorderBlockForSelfHaul($db, $module, $block);
            }
        } else {
            $field2             = new Vtiger_Field();
            $field2->label      = 'LBL_OPPORTUNITY_SELF_HAUL';
            $field2->name       = 'self_haul';
            $field2->table      = 'vtiger_potential';
            $field2->column     = 'self_haul';
            $field2->columntype = 'VARCHAR(3)';
            $field2->uitype = 56;
            $field2->typeofdata = 'V~O';
            $field2->displaytype = 1;
            $field2->quickcreate = 0;
            $field2->presence = 2;
            $field2->readonly = 0;
            $block->addField($field2);
            //$block->save($module);

            echo "<br><h1>Added self_haul field to Opportunities, Reordering fields in the block </h1><br>";
            ReorderBlockForSelfHaul($db, $module, $block);
        }
    } else {
        echo "<h1>NO Opportunities Information block, failing self_haul</h1>";
    }
} else {
    echo "<h1>NO Opportunities, failing adding self_haul </h1>";
}

function ReorderBlockForSelfHaul($db, $module, $block) {
    $fieldSeq = [
        'potentialname'        => 1,
        'move_type'            => 2,
        'contact_id'           => 3,
        'potential_no'         => 4,
        'sales_stage'          => 5,
        'amount'               => 6,
        'business_line'        => 7,
        'business_channel'     => 8,
        'potentialtype'        => 9,
        'shipper_type'         => 10,
        'smownerid'            => 11,
        'sales_person'         => 12,
        'related_to'           => 13,
        'order_number'         => 14,
        'smcreatorid'          => 15,
        'createdtime'          => 16,
        'closingdate'          => 17,
        'modifiedtime'         => 18,
        'assigned_date'        => 19,
        'funded'               => 20,
        'promotion_code'       => 21,
        'agentid'              => 22,
        'program_terms'        => 22,
        'billing_type'         => 23,
        'self_haul'            => 24,
        'moving_a_vehicle'     => 25,
        'lock_military_fields' => 26,
        'special_terms'        => 27,
        'employer_comments'    => 28
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
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'. $name.
                                           '" AND fieldid = '.$field->id);
                $max++;
            }
        }
    }
}*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";