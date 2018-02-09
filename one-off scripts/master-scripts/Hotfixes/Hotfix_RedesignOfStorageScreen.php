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

if (!function_exists('pushDown')) {
    /**
    * Function to push all blocks down after sequence number
    * @param type $fromSequence
    */
    function pushDown($fromSequence, $sourceModuleTabId)
    {
    }
}

if (!function_exists('updateDBValues')) {
    /**
    * Function to push all blocks down after sequence number
    * @param type $fromSequence
    */
    function updateDBValues($table, $columns)
    {
        $db = PearDatabase::getInstance();
        foreach ($columns as $newColumn => $oldColumn) {
            $query = 'UPDATE '.$table.' SET '.$newColumn.'='.$oldColumn;
            $result = $db->query($query);
        }
    }
}
global $db;
if (!$db) {
    $db = PearDatabase::getInstance();
}
$moduleStorage = Vtiger_Module::getInstance('Storage');
if ($moduleStorage) {
    echo "<h2>Updating Storage Fields</h2><br>";
    
    //deleting obsolete fields
    $field_a = Vtiger_Field::getInstance('storage_days', $moduleStorage);
    if ($field_a) {
        $field_a->delete();
    }
    $field_b = Vtiger_Field::getInstance('storage_datein', $moduleStorage);
    if ($field_b) {
        $field_b->delete();
    }
    $field_c = Vtiger_Field::getInstance('storage_dateout', $moduleStorage);
    if ($field_c) {
        $field_c->delete();
    }
    $field_d = Vtiger_Field::getInstance('storage_cpsdate', $moduleStorage);
    if ($field_d) {
        $field_d->delete();
    }
    $field_dd = Vtiger_Field::getInstance('storage_authorization', $moduleStorage);
    if ($field_dd) {
        $field_dd->delete();
    }
    
    $field_dd = Vtiger_Field::getInstance('storage_dateinbilled', $moduleStorage);
    if ($field_dd) {
        $field_dd->delete();
    }
    
    echo 'Finish deleting fields<br>';
    
    //updating picklist storage_location if necesary
    $field_e = Vtiger_Field::getInstance('storage_location', $moduleStorage);
    if ($field_e) {
        $newValues = ['Origin','Destination','Transit'];
        $result = $db->query('SELECT storage_location FROM vtiger_storage_location');
        $updated = true;
        if ($result && $db->num_rows($result) > 0) {
            while ($row = $result->fetchRow()) {
                $arr[] = $row[0];
            }
        }
        foreach ($newValues as $value) {
            if (!in_array($value, $arr)) {
                $updated = false;
            }
        }
        if (!$updated) {
            Vtiger_Utils::ExecuteQuery('DELETE FROM vtiger_storage_location');
            $field_e->setPicklistValues($newValues);
        }
    }
    echo 'Finish updating storage_location picklist<br>';
    
    //entry for sit and perm authorization numbers
    $result = $db->pquery('SELECT * FROM vtiger_modentity_num WHERE semodule=? and prefix=?', ['Storage', 'SITAUTH']);
    if ($result && $db->num_rows($result) == 0) {
        $numid = $db->getUniqueId("vtiger_modentity_num");
        $db->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'Storage', 'SITAUTH', 1, 1, 1));
    }
    
    $result2 = $db->pquery('SELECT * FROM vtiger_modentity_num WHERE semodule=? and prefix=?', ['Storage', 'PERMAUTH']);
    if ($result2 && $db->num_rows($result2) == 0) {
        $numid2 = $db->getUniqueId("vtiger_modentity_num");
        $db->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid2, 'Storage', 'PERMAUTH', 1, 1, 1));
    }
    echo 'Finish inserting in vtiger_modentity_num<br>';
    
    $blockInfo = Vtiger_Block::getInstance('LBL_STORAGE_INFORMATION', $moduleStorage);
    if ($blockInfo) {
        $field1 = Vtiger_Field::getInstance('storage_option', $moduleStorage);
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_STORAGE_OPTION';
            $field1->name = 'storage_option';
            $field1->table = 'vtiger_storage';
            $field1->column = $field1->name;
            $field1->columntype = 'VARCHAR(255)';
            $field1->uitype = 15;
            $field1->typeofdata = 'V~M';
            $field1->summaryfield = 1;
            $blockInfo->addField($field1);
            $field1->setPicklistValues(['SIT', 'Perm']);
        }
        
        //reorder the fields in the block
        $fieldSeq = [
            'storage_option' => 1,
            'storage_location' => 2,
            'storage_agent' => 3,
            'storage_orders' => 4,
            'assigned_user_id' => 5,
            'agentid' => 6];
        reorderBlock($fieldSeq, $blockInfo, $moduleStorage);
    }
        

    $blockAuthorization = Vtiger_Block::getInstance('LBL_STORAGE_AUTHORIZATION', $moduleStorage);
    if ($blockAuthorization) {
        $field5 = Vtiger_Field::getInstance('storage_sit_authorization', $moduleStorage);
        if (!$field5) {
            $field5 = new Vtiger_Field();
            $field5->label = 'LBL_STORAGE_SIT_AUTHORIZATION';
            $field5->name = 'storage_sit_authorization';
            $field5->table = 'vtiger_storage';
            $field5->column = $field5->name;
            $field5->columntype = 'VARCHAR(255)';
            $field5->uitype = 1;
            $field5->typeofdata = 'V~O';
            $field5->summaryfield = 1;
            $blockAuthorization->addField($field5);
        }
        
        
        $field6 = Vtiger_Field::getInstance('storage_perm_authorization', $moduleStorage);
        if (!$field6) {
            $field6 = new Vtiger_Field();
            $field6->label = 'LBL_STORAGE_PERM_AUTHORIZATION';
            $field6->name = 'storage_perm_authorization';
            $field6->table = 'vtiger_storage';
            $field6->column = $field6->name;
            $field6->columntype = 'VARCHAR(255)';
            $field6->uitype = 1;
            $field6->typeofdata = 'V~O';
            $field6->summaryfield = 1;
            $blockAuthorization->addField($field6);
        }

        
        $field7 = Vtiger_Field::getInstance('storage_military_control', $moduleStorage);
        if (!$field7) {
            $field7 = new Vtiger_Field();
            $field7->label = 'LBL_STORAGE_MILITARY_CONTROL';
            $field7->name = 'storage_military_control';
            $field7->table = 'vtiger_storage';
            $field7->column = $field7->name;
            $field7->columntype = 'VARCHAR(255)';
            $field7->uitype = 1;
            $field7->typeofdata = 'V~O';
            $blockAuthorization->addField($field7);
        }
    }
    
    
    $blockSIT = Vtiger_Block::getInstance('LBL_STORAGE_SITDETAILS', $moduleStorage);
    if ($blockSIT) {
        $field8 = Vtiger_Field::getInstance('storage_sit_datein', $moduleStorage);
        if (!$field8) {
            $field8 = new Vtiger_Field();
            $field8->label = 'LBL_STORAGE_SIT_DATEIN';
            $field8->name = 'storage_sit_datein';
            $field8->table = 'vtiger_storage';
            $field8->column = $field8->name;
            $field8->columntype = 'DATE';
            $field8->uitype = 5;
            $field8->typeofdata = 'D~O';
            $blockSIT->addField($field8);
        }


        $field10 = Vtiger_Field::getInstance('storage_sit_approved_datein', $moduleStorage);
        if (!$field10) {
            $field10 = new Vtiger_Field();
            $field10->label = 'LBL_STORAGE_SIT_APPROVED_DATEIN';
            $field10->name = 'storage_sit_approved_datein';
            $field10->table = 'vtiger_storage';
            $field10->column = $field10->name;
            $field10->columntype = 'DATE';
            $field10->uitype = 5;
            $field10->typeofdata = 'D~O';
            $blockSIT->addField($field10);
        }


        $field9 = Vtiger_Field::getInstance('storage_sit_dateout', $moduleStorage);
        if (!$field9) {
            $field9 = new Vtiger_Field();
            $field9->label = 'LBL_STORAGE_SIT_DATEOUT';
            $field9->name = 'storage_sit_dateout';
            $field9->table = 'vtiger_storage';
            $field9->column = $field9->name;
            $field9->columntype = 'DATE';
            $field9->uitype = 5;
            $field9->typeofdata = 'D~O';
            $blockSIT->addField($field9);
        }


        $field12 = Vtiger_Field::getInstance('storage_sit_weight', $moduleStorage);
        if (!$field12) {
            $field12 = new Vtiger_Field();
            $field12->label = 'LBL_STORAGE_SIT_WEIGHT';
            $field12->name = 'storage_sit_weight';
            $field12->table = 'vtiger_storage';
            $field12->column = $field12->name;
            $field12->columntype = 'VARCHAR(255)';
            $field12->uitype = 1;
            $field12->typeofdata = 'V~O';
            $blockSIT->addField($field12);
        }

        $field13 = Vtiger_Field::getInstance('storage_sit_days_in_storage', $moduleStorage);
        if (!$field13) {
            $field13 = new Vtiger_Field();
            $field13->label = 'LBL_STORAGE_SIT_DAYS_IN_STORAGE';
            $field13->name = 'storage_sit_days_in_storage';
            $field13->table = 'vtiger_storage';
            $field13->column = $field13->name;
            $field13->columntype = 'VARCHAR(255)';
            $field13->uitype = 1;
            $field13->typeofdata = 'V~O';
            $blockSIT->addField($field13);
        }
        

        $field141 = Vtiger_Field::getInstance('storage_sit_conv_perm_storage', $moduleStorage);
        if (!$field141) {
            $field141 = new Vtiger_Field();
            $field141->label = 'LBL_STORAGE_SIT_CONV_PERM_STORAGE';
            $field141->name = 'storage_sit_conv_perm_storage';
            $field141->table = 'vtiger_storage';
            $field141->column = $field141->name;
            $field141->columntype = 'varchar(3)';
            $field141->uitype = 56;
            $field141->typeofdata = 'C~O';
            $blockSIT->addField($field141);
        }
        
        $field14 = Vtiger_Field::getInstance('storage_sit_date_perm_storage', $moduleStorage);
        if (!$field14) {
            $field14 = new Vtiger_Field();
            $field14->label = 'LBL_STORAGE_SIT_DATE_PERM_STORAGE';
            $field14->name = 'storage_sit_date_perm_storage';
            $field14->table = 'vtiger_storage';
            $field14->column = $field14->name;
            $field14->columntype = 'DATE';
            $field14->uitype = 5;
            $field14->typeofdata = 'D~O';
            $blockSIT->addField($field14);
        }
        
        
        $field15 = Vtiger_Field::getInstance('storage_sit_vaults', $moduleStorage);
        if (!$field15) {
            $field15 = new Vtiger_Field();
            $field15->label = 'LBL_STORAGE_SIT_VAULTS';
            $field15->name = 'storage_sit_vaults';
            $field15->table = 'vtiger_storage';
            $field15->column = $field15->name;
            $field15->columntype = 'VARCHAR(255)';
            $field15->uitype = 1;
            $field15->typeofdata = 'V~O';
            $blockSIT->addField($field15);
        }
        

        $field16 = Vtiger_Field::getInstance('storage_sit_os', $moduleStorage);
        if (!$field16) {
            $field16 = new Vtiger_Field();
            $field16->label = 'LBL_STORAGE_SIT_OS';
            $field16->name = 'storage_sit_os';
            $field16->table = 'vtiger_storage';
            $field16->column = $field16->name;
            $field16->columntype = 'VARCHAR(255)';
            $field16->uitype = 1;
            $field16->typeofdata = 'V~O';
            $blockSIT->addField($field16);
        }
    }


    $blockPERM = Vtiger_Block::getInstance('LBL_STORAGE_PERMDETAILS', $moduleStorage);
    if (!$blockPERM) {
        $blockPERM = new Vtiger_Block();
        $blockPERM->label = 'LBL_STORAGE_PERMDETAILS';
        $blockPERM->sequence = 3;
        $moduleStorage->addBlock($blockPERM);
    }
    


    if ($blockPERM) {
        $field8 = Vtiger_Field::getInstance('storage_perm_datein', $moduleStorage);
        if (!$field8) {
            $field8 = new Vtiger_Field();
            $field8->label = 'LBL_STORAGE_PERM_DATEIN';
            $field8->name = 'storage_perm_datein';
            $field8->table = 'vtiger_storage';
            $field8->column = $field8->name;
            $field8->columntype = 'DATE';
            $field8->uitype = 5;
            $field8->typeofdata = 'D~O';
            $blockPERM->addField($field8);
        }


        $field10 = Vtiger_Field::getInstance('storage_perm_approved_datein', $moduleStorage);
        if (!$field10) {
            $field10 = new Vtiger_Field();
            $field10->label = 'LBL_STORAGE_PERM_APPROVED_DATEIN';
            $field10->name = 'storage_perm_approved_datein';
            $field10->table = 'vtiger_storage';
            $field10->column = $field10->name;
            $field10->columntype = 'DATE';
            $field10->uitype = 5;
            $field10->typeofdata = 'D~O';
            $blockPERM->addField($field10);
        }


        $field9 = Vtiger_Field::getInstance('storage_perm_dateout', $moduleStorage);
        if (!$field9) {
            $field9 = new Vtiger_Field();
            $field9->label = 'LBL_STORAGE_PERM_DATEOUT';
            $field9->name = 'storage_perm_dateout';
            $field9->table = 'vtiger_storage';
            $field9->column = $field9->name;
            $field9->columntype = 'DATE';
            $field9->uitype = 5;
            $field9->typeofdata = 'D~O';
            $blockPERM->addField($field9);
        }


        $field12 = Vtiger_Field::getInstance('storage_perm_weight', $moduleStorage);
        if (!$field12) {
            $field12 = new Vtiger_Field();
            $field12->label = 'LBL_STORAGE_PERM_WEIGHT';
            $field12->name = 'storage_perm_weight';
            $field12->table = 'vtiger_storage';
            $field12->column = $field12->name;
            $field12->columntype = 'VARCHAR(255)';
            $field12->uitype = 1;
            $field12->typeofdata = 'V~O';
            $blockPERM->addField($field12);
        }

        $field13 = Vtiger_Field::getInstance('storage_perm_days_in_storage', $moduleStorage);
        if (!$field13) {
            $field13 = new Vtiger_Field();
            $field13->label = 'LBL_STORAGE_PERM_DAYS_IN_STORAGE';
            $field13->name = 'storage_perm_days_in_storage';
            $field13->table = 'vtiger_storage';
            $field13->column = $field13->name;
            $field13->columntype = 'VARCHAR(255)';
            $field13->uitype = 1;
            $field13->typeofdata = 'V~O';
            $blockPERM->addField($field13);
        }
        

        $field15 = Vtiger_Field::getInstance('storage_perm_vaults', $moduleStorage);
        if (!$field15) {
            $field15 = new Vtiger_Field();
            $field15->label = 'LBL_STORAGE_PERM_VAULTS';
            $field15->name = 'storage_perm_vaults';
            $field15->table = 'vtiger_storage';
            $field15->column = $field15->name;
            $field15->columntype = 'VARCHAR(255)';
            $field15->summaryfield = 1;
            $field15->uitype = 1;
            $field15->typeofdata = 'V~O';
            $blockPERM->addField($field15);
        }
        

        $field16 = Vtiger_Field::getInstance('storage_perm_os', $moduleStorage);
        if (!$field16) {
            $field16 = new Vtiger_Field();
            $field16->label = 'LBL_STORAGE_PERM_OS';
            $field16->name = 'storage_perm_os';
            $field16->table = 'vtiger_storage';
            $field16->column = $field16->name;
            $field16->columntype = 'VARCHAR(255)';
            $field16->uitype = 1;
            $field16->summaryfield = 1;
            $field16->typeofdata = 'V~O';
            $blockPERM->addField($field16);
        }
    }
    
    //copying field values from deleted fields to new fields
    $columns = [
//        'new_column' => 'old_column',
        'storage_sit_datein' => 'storage_datein',
        'storage_sit_dateout' => 'storage_dateout',
        'storage_sit_authorization' => 'storage_authorization',
        'storage_sit_days_in_storage' => 'storage_days',
        'storage_sit_date_perm_storage' => 'storage_cpsdate',
    ];
    updateDBValues('vtiger_storage', $columns);
    echo "<h2>Finish Updating Storage Fields</h2><br>";
    
    $query = 'UPDATE vtiger_blocks SET sequence=? WHERE  blocklabel=? and tabid=?';
    $db->pquery($query, array(1, 'LBL_STORAGE_INFORMATION', $moduleStorage->id));
    $query = 'UPDATE vtiger_blocks SET sequence=? WHERE  blocklabel=? and tabid=?';
    $db->pquery($query, array(2, 'LBL_STORAGE_AUTHORIZATION', $moduleStorage->id));
    $query = 'UPDATE vtiger_blocks SET sequence=? WHERE  blocklabel=? and tabid=?';
    $db->pquery($query, array(3, 'LBL_STORAGE_SITDETAILS', $moduleStorage->id));
    $query = 'UPDATE vtiger_blocks SET sequence=? WHERE  blocklabel=? and tabid=?';
    $db->pquery($query, array(4, 'LBL_STORAGE_PERMDETAILS', $moduleStorage->id));
    $query = 'UPDATE vtiger_blocks SET sequence=? WHERE  blocklabel=? and tabid=?';
    $db->pquery($query, array(5, 'LBL_STORAGE_RECORDUPDATE', $moduleStorage->id));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";