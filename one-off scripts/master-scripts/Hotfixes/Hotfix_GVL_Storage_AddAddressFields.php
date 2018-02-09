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

if (!function_exists('reorderBlockGSAAF')) {
    /**
     * Function to reorder all fields in a block
     * @param array $fieldSeq : an array of field names in the new order wanted to show
     * @param instance $block
     * @param instance $module
     */
    function reorderBlockGSAAF($fieldSeq, $block, $module)
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

//if(!function_exists('pushDown')){
//    /**
//     * Function to push all blocks down after sequence number
//     * @param type $fromSequence
//     */
//    function pushDown($fromSequence, $sourceModuleTabId) {
//
//    }
//}

//if(!function_exists('updateDBValues')){
//    /**
//     * Function to push all blocks down after sequence number
//     * @param type $fromSequence
//     */
//    function updateDBValues($table, $columns) {
//        $db = PearDatabase::getInstance();
//        foreach ($columns as $newColumn => $oldColumn) {
//            $query = 'UPDATE '.$table.' SET '.$newColumn.'='.$oldColumn;
//            $result = $db->query($query);
//        }
//    }
//}
global $db;
if (!$db) {
    $db = PearDatabase::getInstance();
}
$moduleStorage = Vtiger_Module::getInstance('Storage');
if ($moduleStorage) {
    echo "<h2>Updating Storage Fields</h2><br>";
    $blockInfo = Vtiger_Block::getInstance('LBL_STORAGE_INFORMATION', $moduleStorage);
    if ($blockInfo) {
        $field1 = Vtiger_Field::getInstance('storage_option', $moduleStorage);
        if (!$field1) {
            $field1               = new Vtiger_Field();
            $field1->label        = 'LBL_STORAGE_OPTION';
            $field1->name         = 'storage_option';
            $field1->table        = 'vtiger_storage';
            $field1->column       = $field1->name;
            $field1->columntype   = 'VARCHAR(255)';
            $field1->uitype       = 15;
            $field1->typeofdata   = 'V~M';
            $field1->summaryfield = 1;
            $blockInfo->addField($field1);
            $field1->setPicklistValues(['SIT', 'Perm']);
        }
        $field2 = Vtiger_Field::getInstance('storage_address_1', $moduleStorage);
        if ($field2) {
            echo "The storage_address_1 field already exists<br>\n";
        } else {
            $field2             = new Vtiger_Field();
            $field2->label      = 'LBL_STORAGE_ADDRESS_1';
            $field2->name       = 'storage_address_1';
            $field2->table      = 'vtiger_storage';
            $field2->column     = 'storage_address_1';
            $field2->columntype = 'VARCHAR(50)';
            $field2->uitype     = 1;
            $field2->typeofdata = 'V~O';
            $blockInfo->addField($field2);
        }
        $field3 = Vtiger_Field::getInstance('storage_address_2', $moduleStorage);
        if ($field3) {
            echo "The storage_address_2 field already exists<br>\n";
        } else {
            $field3             = new Vtiger_Field();
            $field3->label      = 'LBL_STORAGE_ADDRESS_2';
            $field3->name       = 'storage_address_2';
            $field3->table      = 'vtiger_storage';
            $field3->column     = 'storage_address_2';
            $field3->columntype = 'VARCHAR(50)';
            $field3->uitype     = 1;
            $field3->typeofdata = 'V~O';
            $blockInfo->addField($field3);
        }
        $field4 = Vtiger_Field::getInstance('storage_city', $moduleStorage);
        if ($field4) {
            echo "The storage_city field already exists<br>\n";
        } else {
            $field4             = new Vtiger_Field();
            $field4->label      = 'LBL_STORAGE_CITY';
            $field4->name       = 'storage_city';
            $field4->table      = 'vtiger_storage';
            $field4->column     = 'storage_city';
            $field4->columntype = 'VARCHAR(50)';
            $field4->uitype     = 1;
            $field4->typeofdata = 'V~O';
            $blockInfo->addField($field4);
        }
        $field5 = Vtiger_Field::getInstance('storage_state', $moduleStorage);
        if ($field5) {
            echo "The storage_state field already exists<br>\n";
        } else {
            $field5             = new Vtiger_Field();
            $field5->label      = 'LBL_STORAGE_STATE';
            $field5->name       = 'storage_state';
            $field5->table      = 'vtiger_storage';
            $field5->column     = 'storage_state';
            $field5->columntype = 'VARCHAR(50)';
            $field5->uitype     = 1;
            $field5->typeofdata = 'V~O';
            $blockInfo->addField($field5);
        }
        $field6 = Vtiger_Field::getInstance('storage_zip', $moduleStorage);
        if ($field6) {
            echo "The storage_zip field already exists<br>\n";
        } else {
            $field6             = new Vtiger_Field();
            $field6->label      = 'LBL_STORAGE_ZIP';
            $field6->name       = 'storage_zip';
            $field6->table      = 'vtiger_storage';
            $field6->column     = 'storage_zip';
            $field6->columntype = 'VARCHAR(50)';
            $field6->uitype     = 1;
            $field6->typeofdata = 'V~O';
            $blockInfo->addField($field6);
        }
        $field7 = Vtiger_Field::getInstance('storage_phone', $moduleStorage);
        if ($field7) {
            echo "The storage_phone field already exists<br>\n";
        } else {
            $field7             = new Vtiger_Field();
            $field7->label      = 'LBL_STORAGE_PHONE';
            $field7->name       = 'storage_phone';
            $field7->table      = 'vtiger_storage';
            $field7->column     = 'storage_phone';
            $field7->columntype = 'VARCHAR(100)';
            $field7->uitype     = 11;
            $field7->typeofdata = 'V~O';
            $blockInfo->addField($field7);
        }
        $field8 = Vtiger_Field::getInstance('storage_comment', $moduleStorage);
        if ($field8) {
            echo "The storage_comment field already exists<br>\n";
        } else {
            $field8             = new Vtiger_Field();
            $field8->label      = 'LBL_STORAGE_COMMENT';
            $field8->name       = 'storage_comment';
            $field8->table      = 'vtiger_storage';
            $field8->column     = 'storage_comment';
            $field8->columntype = 'TEXT';
            $field8->uitype     = 19;
            $field8->typeofdata = 'V~O';
            $blockInfo->addField($field8);
        }
        //reorder the fields in the block
        $fieldSeq = [
            'storage_option'    => 1,
            'storage_location'  => 2,
            'storage_agent'     => 3,
            'storage_orders'    => 4,
            'assigned_user_id'  => 5,
            'agentid'           => 6,
            'storage_address_1' => 7,
            'storage_address_2' => 8,
            'storage_city'      => 9,
            'storage_state'     => 10,
            'storage_zip'       => 11,
            'storage_phone'     => 12,
            'storage_comment'   => 13
        ];
        reorderBlockGSAAF($fieldSeq, $blockInfo, $moduleStorage);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";