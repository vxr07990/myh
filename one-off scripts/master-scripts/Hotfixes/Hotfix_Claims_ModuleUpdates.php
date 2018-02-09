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
            $result = $db->pquery($sql, [$moduleId, $blockId]);
            if ($result && $db->num_rows($result) > 0) {
                $maxSequence = $result->fetchRow()[0];
            }
            if ($maxSequence == null) {
                $maxSequence = 1;
            }
            echo 'Max sequence: ' . $maxSequence . '<br>';

            foreach ($fieldNames as $index => $fieldName) {
                $sequence = $maxSequence + $index;
                $sql = "UPDATE vtiger_field SET sequence=$sequence, block=$blockId WHERE tabid=$moduleId AND fieldname='$fieldName'";
                echo $sql . '<br>';
                Vtiger_Utils::ExecuteQuery($sql);
            }
            echo 'All fields updated OK<br>';
        }
    }
}

if (!function_exists('addPicklistValue')) {
    function addPicklistValue($pickListName, $moduleName, $newValue)
    {
        $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
        if(!$fieldModel)
        {
            return;
        }
        $rolesSelected = array();
        if ($fieldModel->isRoleBased()) {
            $roleRecordList = Settings_Roles_Record_Model::getAll();
            foreach ($roleRecordList as $roleRecord) {
                $rolesSelected[] = $roleRecord->getId();
            }
        }
        try {
            $id = $moduleModel->addPickListValues($fieldModel, $newValue, $rolesSelected);
        } catch (Exception $e) {
        }
    }
}


$claimsInstance = Vtiger_Module::getInstance('Claims');
if (!$claimsInstance) {
    echo 'Module Claims not found<br>';
} else {
    //update Claim Type picklist
    echo 'Deletting vtiger_claim_type</br>';
    //first: delete the existing picklist values
    $sqlquery = 'DELETE FROM vtiger_claim_type WHERE 1';
    Vtiger_Utils::ExecuteQuery($sqlquery);
    echo 'OK</br>';
    //last: add the new picklist values
    $newPicklistValues = ['Cargo', 'Automobile', 'Facility/Residence', 'Property', 'Service Recovery'];
    $pickListName = 'claim_type';
    $moduleName = 'Claims';

    echo 'Adding new values</br>';
    foreach ($newPicklistValues as $value) {
        addPicklistValue($pickListName, $moduleName, $value);
        echo $value . ', ';
    }
    echo '</br>';
    echo 'OK</br>';

    //setting claim_type mandatory
    $field = Vtiger_Field::getInstance('claim_type', $claimsInstance);
    if ($field) {
        echo 'Setting claim_type to mandatory<br>';
        $sqlquery = 'UPDATE vtiger_field SET typeofdata="V~M" WHERE fieldid=' . $field->id;
        Vtiger_Utils::ExecuteQuery($sqlquery);
        echo 'OK</br>';
    } else {
        echo 'No claim_type field found<br>';
    }

    //Update Module Identifier
    //start block1 : LBL_CLAIMS_INFORMATION
    $block1 = Vtiger_Block::getInstance('LBL_CLAIMS_INFORMATION', $claimsInstance);
    if ($block1) {
        echo "<h3> LBL_CLAIMS_INFORMATION block already exists </h3><br>";
    } else {
        $block1 = new Vtiger_Block();
        $block1->label = 'LBL_CLAIMS_INFORMATION';
        $claimsInstance->addBlock($block1);
        $claimsIsNew = true;
    }



    $field3 = Vtiger_Field::getInstance('claimssummary_id', $claimsInstance);
    if (!$field3) {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_CLAIMSSUMMARY_ID';
        $field3->name = 'claimssummary_id';
        $field3->table = 'vtiger_claims';
        $field3->column = 'claimssummary_id';
        $field3->columntype = 'INT(10)';
        $field3->uitype = 10;
        $field3->typeofdata = 'I~O';
        $block1->addField($field3);
        $field3->setRelatedModules(array('ClaimsSummary'));
    }

    $field1 = Vtiger_Field::getInstance('claims_number', $claimsInstance);
    if ($field1) {
        echo "<li>The claims_number field already exists</li><br>";

    //Lets make sure the UI type is right
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype=4, presence=2 WHERE fieldid=$field1->id");
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_CLAIMS_NUMBER';
        $field1->name = 'claims_number';
        $field1->table = 'vtiger_claims';
        $field1->column = 'claims_number';
        $field1->columntype = 'VARCHAR(255)';
        $field1->uitype = 4;
        $field1->typeofdata = 'V~M';

        $block1->addField($field1);
    }

    $db = pearDatabase::getInstance();
    $result = $db->pquery("SELECT * FROM vtiger_entityname WHERE tabid=$claimsInstance->id AND fieldname='claims_number'");
    if ($result && $db->num_rows($result) == 0) {
        $claimsInstance->unsetEntityIdentifier();
        $claimsInstance->setEntityIdentifier($field1);
    }

    global $adb;

    $result = $adb->query("SELECT * FROM vtiger_modentity_num WHERE semodule='Claims'");
    if ($result && $adb->num_rows($result) == 0) {
        $numid = $adb->getUniqueId("vtiger_modentity_num");
        $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'Claims', 'CLI', 1, 1, 1));
    }


    //create block LBL_DATES_INFORMATION
    $datesBlock = Vtiger_Block::getInstance('LBL_DATES_INFORMATION', $claimsInstance);
    if ($datesBlock) {
        echo 'Block LBL_DATES_INFORMATION alredy present';
    } else {
        $datesBlock = new Vtiger_Block();
        $datesBlock->label = 'LBL_DATES_INFORMATION';
        $claimsInstance->addBlock($datesBlock);
    }
    if ($datesBlock) {
        $field1 = Vtiger_Field::getInstance('claims_date_received', $claimsInstance);
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_CLAIMS_DATE_RECEIVED';
            $field1->name = 'claims_date_received';
            $field1->table = 'vtiger_claims';
            $field1->column = $field1->name;
            $field1->columntype = 'date';
            $field1->uitype = 5;
            $field1->typeofdata = 'D~O';
            $datesBlock->addField($field1);
        }
        $field2 = Vtiger_Field::getInstance('claims_date_closed', $claimsInstance);
        if (!$field2) {
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_CLAIMS_DATE_CLOSED';
            $field2->name = 'claims_date_closed';
            $field2->table = 'vtiger_claims';
            $field2->column = $field2->name;
            $field2->columntype = 'date';
            $field2->uitype = 5;
            $field2->typeofdata = 'D~O';
            $datesBlock->addField($field2);
        }
        $field3 = Vtiger_Field::getInstance('claims_calendar_days_settle', $claimsInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->label = 'LBL_CLAIMS_CALENDAR_DAYS_SETTLE';
            $field3->name = 'claims_calendar_days_settle';
            $field3->table = 'vtiger_claims';
            $field3->column = $field3->name;
            $field3->columntype = 'int(10)';
            $field3->uitype = 7;
            $field3->typeofdata = 'I~O';
            $datesBlock->addField($field3);
        }
        $field4 = Vtiger_Field::getInstance('claims_business_days_settle', $claimsInstance);
        if (!$field4) {
            $field4 = new Vtiger_Field();
            $field4->label = 'LBL_CLAIMS_BUSINESS_DAYS_SETTLE';
            $field4->name = 'claims_business_days_settle';
            $field4->table = 'vtiger_claims';
            $field4->column = $field4->name;
            $field4->columntype = 'int(10)';
            $field4->uitype = 7;
            $field4->typeofdata = 'I~O';
            $datesBlock->addField($field4);
        }
    }

    //-----------------------------------------------
    $statusBlock = Vtiger_Block::getInstance('LBL_STATUS_INFORMATION', $claimsInstance);
    if ($statusBlock) {
        echo 'Block LBL_STATUS_INFORMATION alredy present';
    } else {
        $statusBlock = new Vtiger_Block();
        $statusBlock->label = 'LBL_STATUS_INFORMATION';
        $claimsInstance->addBlock($statusBlock);
    }

    if ($statusBlock) {
        $field_11 = Vtiger_Field::getInstance('claims_status_statusgrid', $claimsInstance);
        if (!$field_11) {
            $field_11 = new Vtiger_Field();
            $field_11->label = 'LBL_CLAIMS_STATUS_STATUSGRID';
            $field_11->name = 'claims_status_statusgrid';
            $field_11->table = 'vtiger_claims';
            $field_11->column = $field_11->name;
            $field_11->columntype = 'varchar(255)';
            $field_11->displaytype = 1;
            $field_11->uitype = 15;
            $field_11->typeofdata = 'V~M';
            $statusBlock->addField($field_11);
            $field_11->setPicklistValues(['From Sent', 'Expedited', 'Active', 'Idle', 'Closed', 'Void']);
        }


        $field_12 = Vtiger_Field::getInstance('claims_reason_statusgrid', $claimsInstance);
        if (!$field_12) {
            $field_12 = new Vtiger_Field();
            $field_12->label = 'LBL_CLAIMS_REASON_STATUSGRID';
            $field_12->name = 'claims_reason_statusgrid';
            $field_12->table = 'vtiger_claims';
            $field_12->column = $field_12->name;
            $field_12->columntype = 'varchar(255)';
            $field_12->displaytype = 1;
            $field_12->uitype = 15;
            $field_12->typeofdata = 'V~M';
            $statusBlock->addField($field_12);
            $reasons = [
        'Form Sent',
        'Expedited Claim Request',
        'Formal Claim Received',
        'Requested Information Received From Transferee',
        'Appointment Performed',
        'In­Shop Repairs Completed',
        'Parts Received From Manufacturer',
        'Response Received to Settlement Offer',
        'New Claim Type Received',
        'Supplemental Claim Received',
        'Awaiting Information From Transferee',
        'Delayed Scheduling of Appointment',
        'In­Shop Repairs in Process',
        'Parts On Order From Manufacturer',
        'Settlement Offer Extended',
        'Claim Investigation Concluded',
        'Claim Opened Under Wrong Order Number',
        'Self­Insured Client'];
            $field_12->setPicklistValues($reasons);
        }


        $field_13 = Vtiger_Field::getInstance('claims_effective_date_statusgrid', $claimsInstance);
        if (!$field_13) {
            $field_13 = new Vtiger_Field();
            $field_13->label = 'LBL_CLAIMS_EFFECTIVE_DATE_STATUSGRID';
            $field_13->name = 'claims_effective_date_statusgrid';
            $field_13->table = 'vtiger_claims';
            $field_13->column = $field_13->name;
            $field_13->columntype = 'date';
            $field_13->displaytype = 1;
            $field_13->uitype = 5;
            $field_13->typeofdata = 'D~M';
            $statusBlock->addField($field_13);
        }
    }

    //inserting picklist dependency for claims_reason_statusgrid => claims_status_statusgrid
    $db = PearDatabase::getInstance();
    $claimsModuleId = $claimsInstance->id;

    $arr = [
    'From Sent' => '["Form Sent"]',
    'Expedited' => '["Expedited Claim Request"]',
    'Active' => '["Formal Claim Received","Requested Information Received From Transferee","Appointment Performed","In-Shop Repairs Completed","Parts Received From Manufacturer","Response Received to Settlement Offer","New Claim Type Received","Supplemental Claim Received"]',
    'Idle' => '["Awaiting Information From Transferee","Delayed Scheduling of Appointment","In-Shop Repairs in Process","Parts On Order From Manufacturer","Settlement Offer Extended"]',
    'Closed' => '["Claim Investigation Concluded"]',
    'Void' => '["Claim Opened Under Wrong Order Number","Self-Insured Client"]',
    ];
    foreach ($arr as $key => $value) {
        $result = $db->query("SELECT id FROM vtiger_picklist_dependency WHERE tabid='$claimsModuleId' AND sourcevalue='$key' AND sourcefield='claims_reason_statusgrid' AND targetfield='claims_status_statusgrid'");
        if ($result && $db->num_rows($result) < 1) {
            $numid = $db->getUniqueId("vtiger_picklist_dependency");
            $db->pquery("INSERT into vtiger_picklist_dependency values(?,?,?,?,?,?,?)", array($numid, $claimsModuleId, 'claims_status_statusgrid', 'claims_reason_statusgrid', $key, $value, null));
        }
    }


    //-----------------------------------------------
    $claimDetailBlock = Vtiger_Block::getInstance('LBL_CLAIMS_INFORMATION', $claimsInstance);
    if (!$claimDetailBlock) {
        echo 'Block LBL_CLAIMS_INFORMATION not found.<br>';
    } else {
        if ($claimDetailBlock && $statusBlock) {
            $arr = ['claims_status'];
            moveFieldsToBlock($claimsInstance, $statusBlock, $arr);
        }
    }

    //-----------------------------------------------
    $appointmentBlock = Vtiger_Block::getInstance('LBL_APPOINTMENT_INFORMATION', $claimsInstance);
    if ($appointmentBlock) {
        echo 'Block LBL_APPOINTMENT_INFORMATION alredy present';
    } else {
        $appointmentBlock = new Vtiger_Block();
        $appointmentBlock->label = 'LBL_APPOINTMENT_INFORMATION';
        $claimsInstance->addBlock($appointmentBlock);
    }
    if ($appointmentBlock) {
        $field5 = Vtiger_Field::getInstance('claims_pri_insp_company', $claimsInstance);
        if (!$field5) {
            $field5 = new Vtiger_Field();
            $field5->label = 'LBL_CLAIMS_PRI_INSP_COMPANY';
            $field5->name = 'claims_pri_insp_company';
            $field5->table = 'vtiger_claims';
            $field5->column = $field5->name;
            $field5->columntype = 'VARCHAR(100)';
            $field5->uitype = 1;
            $field5->typeofdata = 'V~O~LE~100';
            $appointmentBlock->addField($field5);
        }
        $field6 = Vtiger_Field::getInstance('claims_sec_insp_company', $claimsInstance);
        if (!$field6) {
            $field6 = new Vtiger_Field();
            $field6->label = 'LBL_CLAIMS_SEC_INSP_COMPANY';
            $field6->name = 'claims_sec_insp_company';
            $field6->table = 'vtiger_claims';
            $field6->column = $field6->name;
            $field6->columntype = 'VARCHAR(100)';
            $field6->uitype = 1;
            $field6->typeofdata = 'V~O~LE~100';
            $appointmentBlock->addField($field6);
        }
        $field7 = Vtiger_Field::getInstance('claims_pri_amount', $claimsInstance);
        if (!$field7) {
            $field7 = new Vtiger_Field();
            $field7->label = 'LBL_CLAIMS_PRI_AMOUNT';
            $field7->name = 'claims_pri_amount';
            $field7->table = 'vtiger_claims';
            $field7->column = $field7->name;
            $field7->columntype = 'decimal(13,2)';
            $field7->uitype = 71;
            $field7->typeofdata = 'N~O~10,2';
            $appointmentBlock->addField($field7);
        }
        $field_h = Vtiger_Field::getInstance('claims_sec_amount', $claimsInstance);
        if (!$field_h) {
            $field8 = new Vtiger_Field();
            $field8->label = 'LBL_CLAIMS_SEC_AMOUNT';
            $field8->name = 'claims_sec_amount';
            $field8->table = 'vtiger_claims';
            $field8->column = $field8->name;
            $field8->columntype = 'decimal(13,2)';
            $field8->uitype = 71;
            $field8->typeofdata = 'N~O~10,2';
            $appointmentBlock->addField($field8);
        }
        $field9 = Vtiger_Field::getInstance('claims_pri_insp_date', $claimsInstance);
        if (!$field9) {
            $field9 = new Vtiger_Field();
            $field9->label = 'LBL_CLAIMS_PRI_INSP_DATE';
            $field9->name = 'claims_pri_insp_date';
            $field9->table = 'vtiger_claims';
            $field9->column = $field9->name;
            $field9->columntype = 'date';
            $field9->uitype = 5;
            $field9->typeofdata = 'D~O';
            $appointmentBlock->addField($field9);
        }
        $field10 = Vtiger_Field::getInstance('claims_sec_insp_date', $claimsInstance);
        if (!$field10) {
            $field10 = new Vtiger_Field();
            $field10->label = 'LBL_CLAIMS_SEC_INSP_DATE';
            $field10->name = 'claims_sec_insp_date';
            $field10->table = 'vtiger_claims';
            $field10->column = $field10->name;
            $field10->columntype = 'date';
            $field10->uitype = 5;
            $field10->typeofdata = 'D~O';
            $appointmentBlock->addField($field10);
        }
        $field11 = Vtiger_Field::getInstance('claims_pri_2nd_appt_date', $claimsInstance);
        if (!$field11) {
            $field11 = new Vtiger_Field();
            $field11->label = 'LBL_CLAIMS_PRI_2ND_APPT_DATE';
            $field11->name = 'claims_pri_2nd_appt_date';
            $field11->table = 'vtiger_claims';
            $field11->column = $field11->name;
            $field11->columntype = 'date';
            $field11->uitype = 5;
            $field11->typeofdata = 'D~O';
            $appointmentBlock->addField($field11);
        }
        $field12 = Vtiger_Field::getInstance('claims_sec_2nd_appt_date', $claimsInstance);
        if (!$field12) {
            $field12 = new Vtiger_Field();
            $field12->label = 'LBL_CLAIMS_SEC_2ND_APPT_DATE';
            $field12->name = 'claims_sec_2nd_appt_date';
            $field12->table = 'vtiger_claims';
            $field12->column = $field12->name;
            $field12->columntype = 'date';
            $field12->uitype = 5;
            $field12->typeofdata = 'D~O';
            $appointmentBlock->addField($field12);
        }
        $field13 = Vtiger_Field::getInstance('claims_pri_3rd_appt_date', $claimsInstance);
        if (!$field13) {
            $field13 = new Vtiger_Field();
            $field13->label = 'LBL_CLAIMS_PRI_3RD_APPT_DATE';
            $field13->name = 'claims_pri_3rd_appt_date';
            $field13->table = 'vtiger_claims';
            $field13->column = $field13->name;
            $field13->columntype = 'date';
            $field13->uitype = 5;
            $field13->typeofdata = 'D~O';
            $appointmentBlock->addField($field13);
        }
        $field14 = Vtiger_Field::getInstance('claims_sec_3rd_appt_date', $claimsInstance);
        if (!$field14) {
            $field14 = new Vtiger_Field();
            $field14->label = 'LBL_CLAIMS_SEC_3RD_APPT_DATE';
            $field14->name = 'claims_sec_3rd_appt_date';
            $field14->table = 'vtiger_claims';
            $field14->column = $field14->name;
            $field14->columntype = 'date';
            $field14->uitype = 5;
            $field14->typeofdata = 'D~O';
            $appointmentBlock->addField($field14);
        }
        $field15 = Vtiger_Field::getInstance('claims_pri_report_received_date', $claimsInstance);
        if (!$field15) {
            $field15 = new Vtiger_Field();
            $field15->label = 'LBL_CLAIMS_PRI_REPORT_RECEIVED_DATE';
            $field15->name = 'claims_pri_report_received_date';
            $field15->table = 'vtiger_claims';
            $field15->column = $field15->name;
            $field15->columntype = 'date';
            $field15->uitype = 5;
            $field15->typeofdata = 'D~O';
            $appointmentBlock->addField($field15);
        }
        $field16 = Vtiger_Field::getInstance('claims_sec_report_received_date', $claimsInstance);
        if (!$field16) {
            $field16 = new Vtiger_Field();
            $field16->label = 'LBL_CLAIMS_SEC_REPORT_RECEIVED_DATE';
            $field16->name = 'claims_sec_report_received_date';
            $field16->table = 'vtiger_claims';
            $field16->column = $field16->name;
            $field16->columntype = 'date';
            $field16->uitype = 5;
            $field16->typeofdata = 'D~O';
            $appointmentBlock->addField($field16);
        }

    //Todo: create remaining fields
    }

    //Deleting fields that now will be shown under claims summary module

    $field = Vtiger_Field::getInstance('claims_order', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claims_account', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claims_valuationtype', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claims_declaredvalue', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claim_filedby', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('date_created', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('date_submitted', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('date_closed', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('total_claim', $claimsInstance);
    if ($field) {
        $field->delete();
    }


    $field = Vtiger_Field::getInstance('valuation_type', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('declared_value', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    $field = Vtiger_Field::getInstance('claims_status', $claimsInstance);
    if ($field) {
        $field->delete();
    }

    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET summaryfield=1 WHERE fieldname IN ('claim_number','claim_type','claims_status_statusgrid','claims_date_received','claims_calendar_days_settle', 'claims_date_closed','claims_business_days_settle')");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";