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

$ordersModuleName = 'Orders';
$ordersModule = Vtiger_Module::getInstance($ordersModuleName);
$blockOrders = Vtiger_Block::getInstance('LBL_ORDERS_ORIGINADDRESS');

if ($ordersModule) {
    $ordersBlockName = 'LBL_ORDERS_ORIGINADDRESS';
    $blockOrders = Vtiger_Block::getInstance($ordersBlockName, $ordersModule);
    if ($blockOrders) {
        $field0 = Vtiger_Field::getInstance('origin_consignment', $ordersModule);
        if ($field0) {
            echo "The origin_consignment field already exists<br>\n";
        } else {
            $field0             = new Vtiger_Field();
            $field0->label      = 'LBL_ORIGIN_CONSIGNMENT';
            $field0->name       = 'origin_consignment';
            $field0->table      = 'vtiger_orders';
            $field0->column     = 'origin_consignment';
            $field0->columntype = 'VARCHAR(100)';
            $field0->uitype     = 10;
            $field0->typeofdata = 'V~O';
            $blockOrders->addField($field0);
            $field0->setRelatedModules(array('Contacts'));
            echo "The $field0->name field created.<br>\n";
        }
        $field1 = Vtiger_Field::getInstance('destination_consignment', $ordersModule);
        if ($field1) {
            echo "The destination_consignment field already exists<br>\n";
        } else {
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_DESTINATION_CONSIGNMENT';
            $field1->name       = 'destination_consignment';
            $field1->table      = 'vtiger_orders';
            $field1->column     = 'destination_consignment';
            $field1->columntype = 'VARCHAR(100)';
            $field1->uitype     = 10;
            $field1->typeofdata = 'V~O';
            $blockOrders->addField($field1);
            $field1->setRelatedModules(array('Contacts'));
            echo "The $field1->name field created.<br>\n";
        }
    } else {
        echo "Unable to find $ordersBlockName block. Skipping field adds.<br/>\n";
    }
} else {
    echo "Unable to find $ordersModuleName module. Skipping field adds.<br/>\n";
}

$extraStopsModuleName = 'ExtraStops';
$extraStopsModule = Vtiger_Module::getInstance($extraStopsModuleName);
if ($extraStopsModule) {
    $extraStopsBlockName = 'LBL_EXTRASTOPS_INFORMATION';
    $extraStopsBlock = Vtiger_Block::GetInstance($extraStopsBlockName, $extraStopsModule);
    if ($extraStopsBlock) {
        $field2 = Vtiger_Field::getInstance('extrastops_consignment', $extraStopsModule);
        if ($field2) {
            echo "The extrastops_consignment field already exists<br>\n";
        } else {
            $field2             = new Vtiger_Field();
            $field2->label      = 'LBL_EXTRASTOPS_CONSIGNMENT';
            $field2->name       = 'extrastops_consignment';
            $field2->table      = 'vtiger_extrastops';
            $field2->column     = 'extrastops_consignment';
            $field2->columntype = 'VARCHAR(100)';
            $field2->uitype     = 10;
            $field2->typeofdata = 'V~O';
            $extraStopsBlock->addField($field2);
            $field2->setRelatedModules(array('Contacts'));
            echo "The $field2->name field created.<br>\n";

            // REORDER THE FIELDS
            $fieldOrder = [
                'extrastops_name',
                'extrastops_sequence',
                'extrastops_weight',
                'extrastops_isprimary',
                'extrastops_address1',
                'extrastops_address2',
                'extrastops_phone1',
                'extrastops_phone2',
                'extrastops_phonetype1',
                'extrastops_phonetype2',
                'extrastops_city',
                'extrastops_state',
                'extrastops_zip',
                'extrastops_country',
                'extrastops_date',
                'extrastops_contact',
                'extrastops_type',
                'extrastops_description',
                'extrastops_consignment',
                'acc_svc_shuttle_weight',
                'acc_svc_shuttle_applied',
                'acc_svc_shuttle_overtime',
                'acc_svc_shuttle_miles',
                'acc_svc_selfstg_weight',
                'acc_svc_selfstg_applied',
                'acc_svc_selfstg_overtime',
                'extrastops_sirvastoptype',
                'extrastops_relcrmid',
                'assigned_user_id'
            ];
            reorderFieldsByBlock_OESACF($fieldOrder, $extraStopsBlockName, $extraStopsModuleName);
        }
    } else {
        echo "Unable to find $extraStopsBlockName block. Skipping field add.<br/>\n";
    }
} else {
    echo "Unable to find $extraStopsModuleName module. Skipping field adds.<br/>\n";
}

function reorderFieldsByBlock_OESACF($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end = [];
            $seq = 1;
            foreach ($fieldSeq as $name) {
                if ($name && $field = Vtiger_Field::getInstance($name, $module)) {
                    $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                    $result = $db->pquery($sql, [$seq, $block->id]);
                    if ($result) {
                        while ($row = $result->fetchRow()) {
                            $push_to_end[] = $row['fieldname'];
                        }
                    }
                    $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                    $db->pquery($updateStmt, [$seq++, $field->id, $block->id]);
                }
                unset($field);
            }
            //push anything that might have gotten added and isn't on the list to the end of the block
            $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
            foreach ($push_to_end as $name) {
                //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
                if (!in_array($name, $fieldSeq)) {
                    $field = Vtiger_Field::getInstance($name, $module);
                    if ($field) {
                        $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                        $db->pquery($updateStmt, [$max++, $field->id, $block->id]);
                        $max++;
                    }
                }
            }
        }
    }
}

print "DONE: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";