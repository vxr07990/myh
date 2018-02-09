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

if (!function_exists('reorderBlock')) {
    /**
    * Function to reorder all fields in a block
    * @param array $fieldSeq : an array of field names in the new order wanted to show
    * @param instance $block
    * @param instance $module
    */
    function reorderBlock($fieldSeq, $block, $module)
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
}

$db = PearDatabase::getInstance();


$claimItemsInstance = Vtiger_Module::getInstance('ClaimItems');
if ($claimItemsInstance) {
    echo "<h2>Updating Module Fields</h2><br>";
    
    $field2 = Vtiger_Field::getInstance('claimitemsdetails_existingfloortype', $claimItemsInstance);
    if ($field2) {
        //Fix picklist values
        Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_claimitemsdetails_existingfloortype WHERE 1");

        $field2->setPicklistValues(array('Carpet', 'Walls', 'Floors', 'Trim', 'Stairway/Railings', 'Doors', 'Ceiling', 'Elevator', 'Lobby', 'Dock Area', 'Driveway/Yard'));
        
        echo 'claimitemsdetails_existingfloortype Picklist Updated<br>';
    }
    
    $field2 = Vtiger_Field::getInstance('item_status', $claimItemsInstance);
    if ($field2) {
        //Fix picklist values

        $field2->setPicklistValues(array('Pending', 'Allocated'));
        
        echo 'item_status Picklist Updated<br>';
    }
    
    $field2 = Vtiger_Field::getInstance('claimitemsdetails_losscode', $claimItemsInstance);
    if ($field2) {
        //Fix picklist values

        $field2->setPicklistValues(array('Undercarriage', 'Environmental', 'Water Damage', 'Yard/Grounds', 'Driveway/Parking Lot', 'Gate', 'Other Physical Structure', 'Automobile', 'Reassembly', 'Inconvenience', 'HHG Daily Allowance', 'HHG Expense Reimbursement',
                            'HHG Furniture Rental', 'Auto Daily Allowance', 'Auto Arrangement of Rental'));
        
        echo 'claimitemsdetails_existingfloortype Picklist Updated<br>';
    }
    
    $block = Vtiger_Block::getInstance('LBL_CLAIMITEMS_INFORMATION', $claimItemsInstance);
    if ($block) {
        $field3 = Vtiger_Field::getInstance('claimitemsdetails_facresitem', $claimItemsInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->name = 'claimitemsdetails_facresitem';
            $field3->label = 'LBL_CLAIMITEMSDETAILS_FACRESITEM';
            $field3->uitype = 15;
            $field3->table = 'vtiger_claimitems';
            $field3->column = $field3->name;
            $field3->columntype = 'varchar(255)';
            $field3->typeofdata = 'V~O';
            $block->addField($field3);
            $field3->setPicklistValues(array('Carpet', 'Walls', 'Floors', 'Trim', 'Stairway/Railings', 'Doors', 'Ceiling', 'Elevator', 'Lobby', 'Dock Area'));
        }
        
        
        //reorder the fields in the block
        $fieldSeq = [
            'claimitemsdetails_location' => 1,
            'item_status' => 2,
            'claimitemsdetails_dateofincident' => 3,
            'claimitemsdetails_losscode' => 4,
            'claimitemsdetails_facresitem' => 5,
            'claimitemsdetails_documented' => 6,
            'claimitemsdetails_natureofclaim' => 7,
            'linked_claim' => 8,
            'claimitemsdetails_contactname' => 9,
            'claimitemsdetails_contactphone' => 10,
            'claimitemsdetails_contactcelltphone' => 11,
            'claimitemsdetails_contactemail' => 12,
            'claimitemsdetails_claimantrequest' => 13,
            'claimitemsdetails_amount' => 14,
            'agentid' => 15,];
        reorderBlock($fieldSeq, $block, $claimItemsInstance);
    }
    
    
    $block = Vtiger_Block::getInstance('LBL_ORIGINAL_COND_INFORMATION', $claimItemsInstance);
    if ($block) {
        $field3 = Vtiger_Field::getInstance('claimitemsdetails_proproomoriginal', $claimItemsInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->name = 'claimitemsdetails_proproomoriginal';
            $field3->label = 'LBL_CLAIMITEMSDETAILS_PROP_ROOM_ORIGINAL';
            $field3->uitype = 2;
            $field3->table = 'vtiger_claimitems';
            $field3->column = $field3->name;
            $field3->columntype = 'varchar(255)';
            $field3->typeofdata = 'V~O';
            $block->addField($field3);
        }
    
        $fieldSeq = [
            'claimitemsdetails_existingfloortype' => 1,
            'claimitemsdetails_existingroom' => 2,
            'claimitemsdetails_proproomoriginal' => 3,
            'claimitemsdetails_existingnotes' => 4,
            ];
        reorderBlock($fieldSeq, $block, $claimItemsInstance);
    }

    $block = Vtiger_Block::getInstance('LBL_ORIGINAL_FINAL_WALKTHROUGH', $claimItemsInstance);
    if ($block) {
        $field3 = Vtiger_Field::getInstance('claimitemsdetails_proproomfinal', $claimItemsInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->name = 'claimitemsdetails_proproomfinal';
            $field3->label = 'LBL_CLAIMITEMSDETAILS_PROP_ROOM_FINAL';
            $field3->uitype = 2;
            $field3->table = 'vtiger_claimitems';
            $field3->column = $field3->name;
            $field3->columntype = 'varchar(255)';
            $field3->typeofdata = 'V~O';
            $block->addField($field3);
        }
    
        $fieldSeq = [
            'claimitemsdetails_finalfloortype' => 1,
            'claimitemsdetails_finalroom' => 2,
            'claimitemsdetails_proproomfinal' => 3,
            'claimitemsdetails_finalnotes' => 4,
            ];
        reorderBlock($fieldSeq, $block, $claimItemsInstance);
    }
}
echo 'Done<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";