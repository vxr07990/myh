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



echo "<h2>Starting LocalDispatch update on Detail fields</h2> ";


$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
if (!$moduleInstance) {
    echo "<h3>The OrdersTask module DONT exists </h3>";
} else {
    $block = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION', $moduleInstance);
    if (!$block) {
        echo "<h3>The LBL_OPERATIVE_TASK_INFORMATION block DONT exists </h3> ";
    } else {
        $field_optask = Vtiger_Field::getInstance('operations_task', $moduleInstance);
        if (!$field_optask) {
            $field = new Vtiger_Field();
            $field->label = 'Operations Task';
            $field->name = 'operations_task';
            $field->table = 'vtiger_orderstask';
            $field->column = 'operations_task';
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = '16';
            $field->typeofdata = 'V~O';

            $block->addField($field);

            $orderTypes = array(
                'APU',
                'ASO - Receiving',
                'ASO - Delivery',
                'Carton Delivery',
                'Carton Pickup - Warehouse',
                'Claims Salvage Pickup',
                'Claims Overage Pickup',
                'Courier Service',
                'Debris Pickup',
                'Delivery',
                'Destination Services - Airbox',
                'Destination Services - Liftvan',
                'Destination Services - Sea Container',
                'Driver Help Loading',
                'Driver Help Unloading',
                'Driver Pack',
                'Flatbed Service',
                'Unpack - Full',
                'ATS Intermodal - Destination Direct',
                'ATS Intermodal - Destination SIT',
                'ATS Intermodal - Origin Blanket Wrap',
                'ATS Intermodal- Origin Intl Wrap',
                '3PL Carrier - Destination Direct',
                '3PL Carrier - Destination SIT',
                '3PL Carrier - Origin Blanket Wrap',
                '3PL Carrier - Origin Intl Wrap',
                'Internal Move HHG',
                'Self Haul',
                'ATS LTL Crated - Destination Direct',
                'ATS LTL Crated - Destination SIT',
                'ATS LTL Crated - Origin Blanket Wrap',
                'ATS LTL Crated - Origin Intl Wrap',
                '3PL LTL Crated - Destination Direct',
                '3PL LTL Crated - Destination SIT',
                '3PL LTL Crated - Origin Blanket Wrap',
                '3PL LTL Crated - Origin Intl Wrap',
                'Non-Revenue',
                'Origin Services - Airbox',
                'Origin Services - Liftvan',
                'Origin Services - Sea Container',
                'Pack',
                'Pack & APU - for Billing Type of DPS (military only)',
                'Pack & Crate - for Billing Type of DPS (military only)',
                'Pack and Load',
                'Pack and Shuttle',
                'Storage Delivery - Partial',
                'Storage Pickup - Partial',
                'Unpack - Partial',
                'Recovery Delivery Direct',
                'Recovery Delivery SIT',
                'Recovery into Warehouse',
                'Recovery Load Direct ',
                'Release to Driver - Full',
                'Release to Driver - Partial',
                'Same Day Load/Deliver',
                'Same Day Pack/Load',
                'Same Day Pack/Load/Deliver',
                'Sea Container - Whse Live Load',
                'Sea Container - Whse Live Unload',
                'Sea Container - Whse Load',
                'Sea Container - Whse Unload',
                'Shuttle Destination',
                'Shuttle Origin',
                'Small Shipment/One Day Load',
                'Storage Access',
                'Storage Delivery',
                'Storage In',
                'Storage Out',
                'Storage Pickup',
                'Temporary Living Delivery',
                'Warehouse Handling',
                'Claims Salvage Pickup',
                'Claims Overage Pickup',
                'Courier Service',
                'Cross Dock',
                'Equipment/Material Delivery',
                'Equipment/Material Pickup',
                'Unpack - Full',
                'Internal',
                'Self Haul',
                'Load and Go',
                'Non-Revenue',
                'Pack',
                'Unpack - Partial',
                'Release to Driver - Full',
                'Release to Driver - Partial',
                'Spec Comm Load',
                'Spec Comm Unload',
                'Stop',
                'Storage Access',
                'Storage In',
                'Storage Out',
                'Survey',
                'Warehouse Handling',
            );

            $field->setPicklistValues($orderTypes);
        }

        $field_bl = Vtiger_Field::getInstance('business_line', $moduleInstance);
        if (!$field_bl) {
            echo "<h3>START adding agent_type to LocalDispatch module. </h3>";

            $field1 = new Vtiger_Field();
            $field1->name = 'business_line';
            $field1->label = 'Business Line';
            $field1->uitype = 16;
            $field1->table = 'vtiger_orderstask';
            $field1->column = $field1->name;
            $field1->columntype = 'VARCHAR(255)';
            $field1->typeofdata = 'V~O';
            $block->addField($field1);

            echo "<h3>END add business_line to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field business_line already present.  </h3>";
        }


        $field_z = Vtiger_Field::getInstance('date_spread', $moduleInstance);
        if (!$field_z) {
            echo "<h3>START adding date_spread to LocalDispatch module.  </h3>";

            $field99 = new Vtiger_Field();
            $field99->name = 'date_spread';
            $field99->label = 'Date Spread';
            $field99->uitype = 56;
            $field99->table = 'vtiger_orderstask';
            $field99->column = $field99->name;
            $field99->columntype = 'varchar(3)';
            $field99->typeofdata = 'C~O';
            $block->addField($field99);

            echo "<h3>END add date_spread to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field date_spread already present.  </h3>";
        }

        $field_y = Vtiger_Field::getInstance('multiservice_date', $moduleInstance);
        if (!$field_y) {
            echo "<h3>START adding multiservice_date to LocalDispatch module.  </h3>";

            $field98 = new Vtiger_Field();
            $field98->name = 'multiservice_date';
            $field98->label = 'Multi-Service Date';
            $field98->uitype = 56;
            $field98->table = 'vtiger_orderstask';
            $field98->column = $field98->name;
            $field98->columntype = 'varchar(3)';
            $field98->typeofdata = 'C~O';
            $block->addField($field98);

            echo "<h3>END add multiservice_date to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field multiservice_date already present.  </h3>";
        }

        $field_x = Vtiger_Field::getInstance('include_saturday', $moduleInstance);
        if (!$field_x) {
            echo "<h3>START adding include_saturday to LocalDispatch module.  </h3>";

            $field97 = new Vtiger_Field();
            $field97->name = 'include_saturday';
            $field97->label = 'Include Saturday';
            $field97->uitype = 56;
            $field97->table = 'vtiger_orderstask';
            $field97->column = $field97->name;
            $field97->columntype = 'varchar(3)';
            $field97->typeofdata = 'C~O';
            $block->addField($field97);

            echo "<h3>END add include_saturday to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field include_saturday already present.  </h3>";
        }

        $field_w = Vtiger_Field::getInstance('include_sunday', $moduleInstance);
        if (!$field_w) {
            echo "<h3>START adding include_sunday to LocalDispatch module.  </h3>";

            $field96 = new Vtiger_Field();
            $field96->name = 'include_sunday';
            $field96->label = 'Include Sunday';
            $field96->uitype = 56;
            $field96->table = 'vtiger_orderstask';
            $field96->column = $field96->name;
            $field96->columntype = 'varchar(3)';
            $field96->typeofdata = 'C~O';
            $block->addField($field96);

            echo "<h3>END add include_sunday to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field include_sunday already present.  </h3>";
        }

        $field_c = Vtiger_Field::getInstance('cod_amount', $moduleInstance);
        if (!$field_c) {
            echo "<h3>START adding cod_amount to LocalDispatch module.  </h3>";

            $field3 = new Vtiger_Field();
            $field3->name = 'cod_amount';
            $field3->label = 'COD Amount';
            $field3->uitype = 71;
            $field3->table = 'vtiger_orderstask';
            $field3->column = $field3->name;
            $field3->columntype = 'decimal(13,2)';
            $field3->typeofdata = 'N~O~10,2';
            $block->addField($field3);

            echo "<h3>END add cod_amount to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field cod_amount already present.  </h3>";
        }

        $field_d = Vtiger_Field::getInstance('service_provider_notes', $moduleInstance);
        if (!$field_d) {
            echo "<h3>START adding service_provider_notes to LocalDispatch module.  </h3>";

            $field4 = new Vtiger_Field();
            $field4->name = 'service_provider_notes';
            $field4->label = 'Service Provider Notes';
            $field4->uitype = 19;
            $field4->table = 'vtiger_orderstask';
            $field4->column = $field4->name;
            $field4->columntype = 'text';
            $field4->typeofdata = 'V~O';
            $block->addField($field4);

            echo "<h3>END add service_provider_notes to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field service_provider_notes already present.  </h3>";
        }

        $field_e = Vtiger_Field::getInstance('cancel_task', $moduleInstance);
        if (!$field_e) {
            echo "<h3>START adding cancel_task to LocalDispatch module.  </h3>";

            $field5 = new Vtiger_Field();
            $field5->name = 'cancel_task';
            $field5->label = 'Cancel Task';
            $field5->uitype = 56;
            $field5->table = 'vtiger_orderstask';
            $field5->column = $field5->name;
            $field5->columntype = 'varchar(3)';
            $field5->typeofdata = 'C~O';
            $block->addField($field5);

            echo "<h3>END add cancel_task to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field cancel_task already present.  </h3>";
        }


        $field_f = Vtiger_Field::getInstance('reason_cancelled', $moduleInstance);
        if (!$field_f) {
            echo "<h3>START adding reason_cancelled to LocalDispatch module.  </h3>";

            $field6 = new Vtiger_Field();
            $field6->name = 'reason_cancelled';
            $field6->label = 'Reason Cancelled';
            $field6->uitype = 1;
            $field6->table = 'vtiger_orderstask';
            $field6->column = $field6->name;
            $field6->columntype = 'varchar(100)';
            $field6->typeofdata = 'V~O~LE~100';
            $block->addField($field6);

            echo "<h3>END add reason_cancelled to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field reason_cancelled already present, updating it to optional.  </h3>";
            $sql = "UPDATE vtiger_field SET typeofdata='V~O~LE~100' WHERE fieldname='reason_cancelled'";
            Vtiger_Utils::ExecuteQuery($sql);
        }

        $field_g = Vtiger_Field::getInstance('task_start', $moduleInstance);
        if (!$field_g) {
            echo "<h3>START adding task_start to LocalDispatch module.  </h3>";

            $field7 = new Vtiger_Field();
            $field7->name = 'task_start';
            $field7->label = 'Task Start';
            $field7->uitype = 15;
            $field7->table = 'vtiger_orderstask';
            $field7->column = $field7->name;
            $field7->columntype = 'VARCHAR(255)';
            $field7->typeofdata = 'V~O';
            $block->addField($field7);
            $field7->setPicklistValues(array('AM', 'PM', 'After Hours'));
            echo "<h3>END add task_start to LocalDispatch module.  </h3>";
        } else {
            echo "<h3>Field task_start already present.  </h3>";
        }

        $field_a = Vtiger_Field::getInstance('orderstask_agent', $moduleInstance);
        if ($field_a) {
            $field_a->delete();
        } else {
            echo "Field agent NOT present.<br>";
        }

        $field_at = Vtiger_Field::getInstance('agent_type', $moduleInstance);
        if ($field_at) {
            $field_at->delete();
        } else {
            echo "Field agent_type NOT present.<br>";
        }

        $field_note = Vtiger_Field::getInstance('notes_to_dispatcher', $moduleInstance);
        if ($field_note) {
            echo "Field notes_to_dispatcher already present.<br>";
        } else {
            $field_note = new Vtiger_Field();
            $field_note->name = 'notes_to_dispatcher';
            $field_note->label = 'LBL_NOTES_TO_DISPATCHER';
            $field_note->uitype = 21;
            $field_note->table = 'vtiger_orderstask';
            $field_note->column = $field_note->name;
            $field_note->columntype = 'text';
            $field_note->typeofdata = 'V~O';
            $block->addField($field_note);
        }

        $field_pa = Vtiger_Field::getInstance('participating_agent', $moduleInstance);
        if ($field_pa) {
            echo "Field participating_agent already present.";
        } else {
            $field_pa = new Vtiger_Field();
            $field_pa->name = 'participating_agent';
            $field_pa->label = 'LBL_PARTICIPATING_AGENT';
            $field_pa->uitype = 16;
            $field_pa->table = 'vtiger_orderstask';
            $field_pa->column = $field_pa->name;
            $field_pa->columntype = 'VARCHAR(255)';
            $field_pa->typeofdata = 'V~O';
            $block->addField($field_pa);
        }
    }

    echo "<h2>Ending OrdersTask update on Detail fields</h2> ";

//Make sure Operations tasks block is ok
    //Making orderstask_no the entity field name

    $field_orno = Vtiger_Field::getInstance('orderstask_no', $moduleInstance);
    if (!$field_orno) {
        $moduleInstance->setEntityIdentifier($field_orno);
    }


    $operationsTaskFields = array('operations_task', 'business_line', 'date_spread', 'multiservice_date', 'include_saturday', 'include_sunday', 'participating_agent', 'service_date_from',
        'service_date_to', 'pref_date_service', 'task_start', 'notes_to_dispatcher', 'cod_amount', 'estimated_hours', 'crew_number', 'est_vehicle_number', 'service_provider_notes', 'cancel_task', 'reason_cancelled', 'ordersid',
        'assigned_user_id', 'createdtime', 'modifiedtime', 'agentid', 'orderstask_no');

    $sequence = 1;
    $blockId = $block->id;
    $mandatoryFields = array('assigned_user_id', 'createdtime', 'modifiedtime', 'agentid');
    foreach ($operationsTaskFields as $fieldName) {
        $presence = 2;
        if (in_array($fieldName, $mandatoryFields)) {
            $presence = 0;
        }
        $sql = "UPDATE vtiger_field SET sequence=$sequence, presence=$presence, block=$blockId WHERE tabid=$moduleInstance->id AND fieldname='$fieldName'";
        Vtiger_Utils::ExecuteQuery($sql);
        $sequence = $sequence + 1;
    }


//Local Dispatch Information Block


    $block2 = Vtiger_Block::getInstance('LBL_DISPATCH_UPDATES', $moduleInstance);
    if (!$block2) {
        echo "<h3>The LBL_DISPATCH_UPDATES block DONT exists </h3> ";
    } else {

        //Fix Dispatch status picklist and sequence


        $db = PearDatabase::getInstance();
        $newStatuses = array(
            'Requested',
            'Accepted',
            'Rejected',
            'Assigned',
            'Completed',
            'Cancelled',
            'Escalate'
        );

        $statusPresent = array();

        $result = $db->pquery("SELECT * FROM vtiger_dispatch_status");
        if ($result && $db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                if (!in_array($row['dispatch_status'], $newStatuses)) {
                    $db->pquery("DELETE FROM vtiger_dispatch_status WHERE dispatch_status=?", array($row['dispatch_status']));
                } else {
                    array_push($statusPresent, $row['dispatch_status']);
                }
            }
        }




        echo 'Adding new values</br>';
        foreach ($newStatuses as $value) {
            if (!in_array($value, $statusPresent)) {
                addPicklistValue('dispatch_status', 'OrdersTask', $value);
            }
        }

        echo 'Adding Requested as default value for dispatch_status picklist<br>';
        $sqlquery = "UPDATE vtiger_field SET defaultvalue = 'Requested' WHERE fieldname = 'dispatch_status'";
        Vtiger_Utils::ExecuteQuery($sqlquery);



        $field_e = Vtiger_Field::getInstance('assigned_employee', $moduleInstance);
        if (!$field_e) {
            $field0 = new Vtiger_Field();
            $field0->label = 'Crew';
            $field0->name = 'assigned_employee';
            $field0->table = 'vtiger_orderstask';
            $field0->column = 'assigned_employee';
            $field0->columntype = 'VARCHAR(250)';
            $field0->uitype = 1008;
            $field0->typeofdata = 'V~O';
            $field0->summaryfield = 0;
            $field0->presence = 2;
            $block2->addField($field0);
        }

        print "<h2>END add assigned_employee to OrdersTask module. </h2>\n";

        print "<h2>START add assigned_vehicles to OrdersTask module. </h2>\n";

        $field_v = Vtiger_Field::getInstance('assigned_vehicles', $moduleInstance);
        if (!$field_v) {
            $field1 = new Vtiger_Field();
            $field1->label = 'Assigned Vehicles';
            $field1->name = 'assigned_vehicles';
            $field1->table = 'vtiger_orderstask';
            $field1->column = 'assigned_vehicles';
            $field1->columntype = 'VARCHAR(250)';
            $field1->uitype = 1009;
            $field1->typeofdata = 'V~O';
            $field1->summaryfield = 0;
            $field1->presence = 2;
            $block2->addField($field1);
        }

        print "<h2>END add assigned_vehicles to OrdersTask module. </h2>\n";

        print "<h2>START add disp_actualend to OrdersTask module. </h2>\n";

        $field85 = Vtiger_Field::getInstance('disp_actualend', $moduleInstance);
        if (!$field85) {
            $field85 = new Vtiger_Field();
            $field85->label = 'Actual End Time';
            $field85->name = 'disp_actualend';
            $field85->table = 'vtiger_orderstask';
            $field85->column = 'disp_actualend';
            $field85->columntype = 'TIME';
            $field85->uitype = 14;
            $field85->typeofdata = 'T~O';

            $block2->addField($field85);
        };

        print "<h2>END add disp_actualend to OrdersTask module. </h2>\n";

        print "<h2>START add check_call to OrdersTask module. </h2>\n";

        $field999 = Vtiger_Field::getInstance('check_call', $moduleInstance);
        if ($field999) {
            echo '<p> check_call Field already present</p>';
        } else {
            $field999 = new Vtiger_Field();
            $field999->label = 'LBL_ORDERSTASK_CHECK_CALL';
            $field999->name = 'check_call';
            $field999->table = 'vtiger_orderstask';
            $field999->column = 'check_call';
            $field999->columntype = 'VARCHAR(150)';
            $field999->uitype = '16';
            $field999->typeofdata = 'V~O';
            $field999->setPicklistValues(array('Attempted - All Numbers, No Contact', 'Attempted - No Answer', 'Attempted - No Phone Nbr in P3', 'Attempted - Phone Busy', 'Attempted - Wrong Number', 'Contacted - Cancelled', 'Contacted - Confirmed', 'Contacted - Date Change', 'Contacted - Left Message with child', 'Contacted - Left Message with relative', 'Contacted - Left Message with secretary'));

            $block2->addField($field999);
        }

        print "<h2>END add check_call to OrdersTask module. </h2>\n";

        print "<h2>START add assigned_vendor to OrdersTask module. </h2>\n";

        $fieldv0 = Vtiger_Field::getInstance('assigned_vendor', $moduleInstance);
        if (!$fieldv0) {
            $fieldv0 = new Vtiger_Field();
            $fieldv0->label = 'Service Provider';
            $fieldv0->name = 'assigned_vendor';
            $fieldv0->table = 'vtiger_orderstask';
            $fieldv0->column = 'assigned_vendor';
            $fieldv0->columntype = 'INT(19)';
            $fieldv0->uitype = 10;
            $fieldv0->typeofdata = 'V~O';
            $fieldv0->summaryfield = 0;
            $fieldv0->presence = 2;
            $block2->addField($fieldv0);

            $fieldv0->setRelatedModules(array('Vendors'));
        }


        $dispatchFields = array('dispatch_status', 'disp_assigneddate', 'disp_assignedstart', 'disp_actualdate', 'disp_actualhours', 'assigned_employee', 'assigned_vehicles', 'disp_actualend', 'check_call', 'assigned_vendor');

        $sequence = 1;
        $blockId = $block2->id;
        $presence = 2;

        foreach ($dispatchFields as $fieldName) {
            $sql = "UPDATE vtiger_field SET sequence=$sequence, presence=$presence, block=$blockId WHERE tabid=$moduleInstance->id AND fieldname='$fieldName'";
            Vtiger_Utils::ExecuteQuery($sql);
            $sequence = $sequence + 1;
        }
    }

//Let's hide all the fields we are not using

    $fieldsToLeft = array_merge($operationsTaskFields, $dispatchFields);
    $db = PearDatabase::getInstance();

    $sql = 'UPDATE vtiger_field SET presence=1 WHERE tabid=' . $moduleInstance->id . ' AND  fieldname NOT IN (' . generateQuestionMarks($fieldsToLeft) . ')';
    $db->pquery($sql, $fieldsToLeft);


//Fix Date Spread field of the existing orderstasks

    $sql = "UPDATE vtiger_orderstask SET date_spread = 0 WHERE date_spread IS NULL";
    $db->pquery($sql, array());
    
    $sql = "UPDATE vtiger_field SET fieldname='orderstask_business_line' WHERE tabid=' . $moduleInstance->id . ' AND  fieldname='business_line'";
    $db->pquery($sql, array());
    
    
    //Fix Participating Agent UI Type

    $field_pa = Vtiger_Field::getInstance('participating_agent', $moduleInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype=10 WHERE fieldid=$field_pa->id");
    
    $result = $db->pquery("SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND relmodule=?", array($field_pa->id, 'AgentManager'));
    if ($result && $db->num_rows($result) == 0) {
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_fieldmodulerel` (`fieldid`, `module`, `relmodule`, `status`, `sequence`)
			    VALUES
				    ($field_pa->id, 'OrdersTask', 'AgentManager', NULL, NULL);
			    ");
    }
    $result = $db->pquery("SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND relmodule=?", array($field_pa->id, 'Agents'));
    if ($result && $db->num_rows($result) == 0) {
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_fieldmodulerel` (`fieldid`, `module`, `relmodule`, `status`, `sequence`)
			    VALUES
				    ($field_pa->id, 'OrdersTask', 'Agents', NULL, NULL);
			    ");
    }

    echo '</br>';
    echo 'OK</br>';
}

function addPicklistValue($pickListName, $moduleName, $newValue)
{
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
    $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
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


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";