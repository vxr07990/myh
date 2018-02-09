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



if (!function_exists('moveFieldsToBlock')) {
    function moveFieldsToBlock($module, $block, $fieldNames)
    {
        if ($module == null || $block == null) {
            echo 'Module or Block are null<br>';
        } else {
            $db = PearDatabase::getInstance();
            $moduleId = $module->id;
            $blockId = $block->id;
            $sql = 'SELECT MAX(sequence) FROM vtiger_field WHERE tabid=? AND block=?';
            $result = $db->pquery($sql, array($moduleId, $blockId));
            if ($result && $db->num_rows($result) > 0) {
                $maxSequence = $result->fetchRow()[0];
            }
            if ($maxSequence == null) {
                $maxSequence = 0;
            }
            echo 'Max sequence: ' . $maxSequence . '<br>';

            foreach ($fieldNames as $index => $fieldName) {
                $sequence = $maxSequence + $index + 1;
                $sql = "UPDATE vtiger_field SET sequence=$sequence, block=$blockId WHERE tabid=$moduleId AND fieldname='$fieldName'";
                echo $sql . '<br>';
                Vtiger_Utils::ExecuteQuery($sql);
            }
            echo 'All updated OK<br>';
        }
    }
}

if (!function_exists('reorderBlock')) {
    function reorderBlock($fieldSeq, $block, $module)
    {
        $db = PearDatabase::getInstance();
        $push_to_end = [];
        foreach ($fieldSeq as $name => $seq) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                $sql = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                $result = $db->pquery($sql, [$seq, $block->id]);
                if ($result) {
                    while ($row = $result->fetchRow()) {
                        $push_to_end[] = $row[0];
                    }
                }
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = ' . $seq . ' WHERE fieldname= "' . $name . '" AND fieldid = ' . $field->id);
            }
            unset($field);
        }
        //push anything that might have gotten added and isn't on the list to the end of the block
        $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
        foreach ($push_to_end as $name) {
            //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
            if (!array_key_exists($name, $fieldSeq)) {
                $field = Vtiger_Field::getInstance($name, $module);
                if ($field) {
                    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = ' . $max . ' WHERE fieldname= "' . $name . '" AND fieldid = ' . $field->id);
                    $max++;
                }
            }
        }
    }
}

if (!function_exists('pushDown')) {
    function pushDown($fromSequence, $sourceModuleTabId)
    {
        $db = PearDatabase::getInstance();
        $query = 'UPDATE vtiger_blocks SET sequence=sequence+1 WHERE sequence > ? and tabid=?';
        $result = $db->pquery($query, array($fromSequence, $sourceModuleTabId));
        return $result;
    }
}

$employeesInstance = Vtiger_Module::getInstance('Employees');
if (!$employeesInstance) {
    echo 'Module Employees not found<br>';
} else {
    $contractorBlock = Vtiger_Block::getInstance('LBL_CONTRACTORS_DETAILINFO', $employeesInstance);
    if (!$contractorBlock) {
        echo 'Block LBL_CONTRACTORS_DETAILINFO not found.<br>';
    } else {
        $field_z = Vtiger_Field::getInstance('employees_contractor_owner', $employeesInstance);
        if (!$field_z) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_EMPLOYEES_CONTRACTOR_OWNER';
            $field1->name = 'employees_contractor_owner';
            $field1->table = 'vtiger_employees';
            $field1->column = $field1->name;
            $field1->columntype = 'int(11)';
            $field1->uitype = 10;
            $field1->typeofdata = 'V~O';
            $contractorBlock->addField($field1);
            $field1->setRelatedModules(array('Vendors'));
        }
    }
    echo 'OK<br>';
}

//Fix Related List Between employees and Vendors






$vendorsInstance = Vtiger_Module::getInstance('Vendors');
if (!$vendorsInstance) {
    echo 'Module Vendors not found<br>';
} else {
    $contractorInfoBlock = Vtiger_Block::getInstance('LBL_CONTRACTORS_INFORMATION', $vendorsInstance);
    if ($contractorInfoBlock) {
        echo 'Block LBL_CONTRACTORS_INFORMATION alredy present.<br>';
    } else {
        pushDown(1, $vendorsInstance->id);
        $contractorInfoBlock = new Vtiger_Block();
        $contractorInfoBlock->label = 'LBL_CONTRACTORS_INFORMATION';
        $contractorInfoBlock->sequence = 2;
        $vendorsInstance->addBlock($contractorInfoBlock);
    }

    if ($contractorInfoBlock) {
        $field_a = Vtiger_Field::getInstance('vendors_business_name', $vendorsInstance);
        if (!$field_a) {
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_VENDORS_BUSINESS_NAME';
            $field2->name = 'vendors_business_name';
            $field2->table = 'vtiger_vendor';
            $field2->column = $field2->name;
            $field2->columntype = 'varchar(255)';
            $field2->uitype = 15;
            $field2->typeofdata = 'V~O';
            $contractorInfoBlock->addField($field2);
        }

        $field_b = Vtiger_Field::getInstance('vendors_contractor_type', $vendorsInstance);
        if (!$field_b) {
            $field3 = new Vtiger_Field();
            $field3->label = 'LBL_VENDORS_CONTRACTOR_TYPE';
            $field3->name = 'vendors_contractor_type';
            $field3->table = 'vtiger_vendor';
            $field3->column = $field3->name;
            $field3->columntype = 'varchar(255)';
            $field3->uitype = 15;
            $field3->typeofdata = 'V~O';
            $contractorInfoBlock->addField($field3);
            $field3->setPicklistValues(array('IC', 'TSC', 'IT'));
        }

        $field_c = Vtiger_Field::getInstance('vendors_in_service_date', $vendorsInstance);
        if (!$field_c) {
            $field4 = new Vtiger_Field();
            $field4->label = 'LBL_VENDORS_IN_SERVICE_DATE';
            $field4->name = 'vendors_in_service_date';
            $field4->table = 'vtiger_vendor';
            $field4->column = $field4->name;
            $field4->columntype = 'date';
            $field4->uitype = 5;
            $field4->typeofdata = 'D~O';
            $contractorInfoBlock->addField($field4);
        }

        $field_d = Vtiger_Field::getInstance('vendors_cancellation_date', $vendorsInstance);
        if (!$field_d) {
            $field5 = new Vtiger_Field();
            $field5->label = 'LBL_VENDORS_CANCELLATION_DATE';
            $field5->name = 'vendors_cancellation_date';
            $field5->table = 'vtiger_vendor';
            $field5->column = $field5->name;
            $field5->columntype = 'date';
            $field5->uitype = 5;
            $field5->typeofdata = 'D~O';
            $contractorInfoBlock->addField($field5);
        }

        $field_e = Vtiger_Field::getInstance('vendors_owner_ssn', $vendorsInstance);
        if (!$field_e) {
            $field6 = new Vtiger_Field();
            $field6->label = 'LBL_VENDORS_OWNER_SSN';
            $field6->name = 'vendors_owner_ssn';
            $field6->table = 'vtiger_vendor';
            $field6->column = $field6->name;
            $field6->columntype = 'VARCHAR(25)';
            $field6->uitype = 1;
            $field6->typeofdata = 'V~O~LE~25';
            $contractorInfoBlock->addField($field6);
        }

        $field_f = Vtiger_Field::getInstance('vendors_owner_birthdate', $vendorsInstance);
        if (!$field_f) {
            $field7 = new Vtiger_Field();
            $field7->label = 'LBL_VENDORS_OWNER_BIRTHDATE';
            $field7->name = 'vendors_owner_birthdate';
            $field7->table = 'vtiger_vendor';
            $field7->column = $field7->name;
            $field7->columntype = 'date';
            $field7->uitype = 5;
            $field7->typeofdata = 'D~O';
            $contractorInfoBlock->addField($field7);
        }

        //Relate to Employees
        $result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($vendorsInstance->id, $employeesInstance->id));

        if ($result && $adb->num_rows($result) == 0) {
            $vendorsInstance->setRelatedList($employeesInstance, 'Employees', array('ADD'), 'get_dependents_list');
        }

        $fieldsToMove = [
            'agentid',
            'vendor_status',
            'fein',
            'icode'
        ];
        moveFieldsToBlock($vendorsInstance, $contractorInfoBlock, $fieldsToMove);

        $fieldsNewOrder = [
            'vendors_business_name' => 1,
            'agentid' => 2,
            'vendors_contractor_type' => 3,
            'vendor_status' => 4,
            'vendors_owner_ssn' => 5,
            'vendors_owner_birthdate' => 6,
            'fein' => 7,
            'icode' => 8,
            'vendors_in_service_date' => 9,
            'vendors_cancellation_date' => 10,
        ];
        reorderBlock($fieldsNewOrder, $contractorInfoBlock, $vendorsInstance);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";