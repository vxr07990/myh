<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/12/2016
 * Time: 5:17 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 7;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('Hotfix_CoreUtil_FixFieldSequencePerBlock.php');

$moduleNames = ['Estimates','Actuals'];
$db = &PearDatabase::getInstance();

$hideFields = ['rush_shipment_fee', 'pshipping_booker_commission', 'pshipping_origin_miles',
'pshipping_destination_miles', 'bulky_article_changes'];

$serviceDescPicklist =[
    'Assemble/Disassemble Bookcase',
    'Assemble/Disassemble Computer',
    'Assemble/Disassemble Pool Table',
    'Assemble/Disassemble Stereo',
    'Assemble/Disassemble Swingset/Play',
    'Assemble/Disassemble Wall Unit',
    'Assemble/Disassemble Waterbed',
    'Assemble/Disassemble Weight Bench',
    'Attic Placement',
    'Attic Removal',
    'Labor Services',
    'Other Service',
    'PBO Unpack Only - Full Service Pack',
    'PBO Unpack/Repack - Custom Serivce Pack',
    'Repack/Check PBOs',
    'Service GF/GM Clock',
    'Unwrap Fine Finish Wrapped Items',
    'Unwrap Upholstery Wrapped Items',
];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    foreach ($hideFields as $hideFieldName) {
        $hideField = Vtiger_Field::getInstance($hideFieldName, $module);
        if ($hideField) {
            $db->pquery('UPDATE `vtiger_field` SET presence=1 WHERE fieldid=?', [$hideField->id]);
        }
    }

    $order = [];
    $expeditedService = Vtiger_Field::getInstance('accesorial_expedited_service', $module);
    $overtimePack = Vtiger_Field::getInstance('overtime_pack', $module);
    $block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
    ms_FixFieldSequenceInBlock($block->id);

    if ($overtimePack) {
        $order[] = $overtimePack;
    }
    if ($expeditedService) {
        $order[] = $expeditedService;
    }
    $exclusiveUse = Vtiger_Field::getInstance('exclusive_use', $module);
    if ($exclusiveUse) {
        echo "The exclusive_use field already exists<br>\n";
    } else {
        if ($block) {
            $exclusiveUse             = new Vtiger_Field();
            $exclusiveUse->label      = 'LBL_QUOTES_EXCLUSIVE_USE';
            $exclusiveUse->name       = 'exclusive_use';
            $exclusiveUse->table      = 'vtiger_quotes';
            $exclusiveUse->column     = 'exclusive_use';
            $exclusiveUse->columntype = 'VARCHAR(3)';
            $exclusiveUse->uitype     = 56;
            $exclusiveUse->typeofdata = 'V~O';
            $block->addField($exclusiveUse);
        }
    }
    if ($exclusiveUse) {
        $order[] = $exclusiveUse;
    }
    if ($block && $expeditedService) {
        $db->pquery('UPDATE `vtiger_field` SET block=? WHERE fieldid=?', [$block->id, $expeditedService->id]);
    }
    setFieldSequenceGFESFL($order, $db);

    $order = [];
    $block = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $module);
    ms_FixFieldSequenceInBlock($block->id);
    $firstField = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $module);
    if ($firstField) {
        $order[] = $firstField;
    }
    $fieldName = 'acc_shuttle_origin_reason';
    $field     = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_ESTIMATES_'.strtoupper($fieldName);
        $field->name       = $fieldName;
        $field->table      = 'vtiger_quotes';
        $field->column     = $fieldName;
        $field->columntype = 'VARCHAR(255)';
        $field->uitype     = '1';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
    if ($field) {
        $order[] = $field;
    }
    //**************** Shuttle Destination Reason *******************//
    $fieldName = 'acc_shuttle_destination_reason';
    $field     = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_ESTIMATES_'.strtoupper($fieldName);
        $field->name       = $fieldName;
        $field->table      = 'vtiger_quotes';
        $field->column     = $fieldName;
        $field->columntype = 'VARCHAR(255)';
        $field->uitype     = '1';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        echo "<p>Added $fieldName Field</p>\n";
    }
    if ($field) {
        $order[] = $field;
    }
    setFieldSequenceGFESFL($order, $db);

    $order = [];
    $field0 = Vtiger_Field::getInstance('acc_exlabor_origin_desc', $module);
    if ($field0) {
        echo '<p>acc_exlabor_orign_desc field exists</p>';
    } else {
        $db  = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_acc_exlabor_origin_desc`";
        $db->pquery($sql, []);
        $field0             = new Vtiger_Field();
        $field0->label      = 'LBL_QUOTES_ACCEXLABORORIGINDESC';
        $field0->name       = 'acc_exlabor_origin_desc';
        $field0->table      = 'vtiger_quotes';
        $field0->column     = 'acc_exlabor_origin_desc';
        $field0->columntype = 'VARCHAR(200)';
        $field0->uitype     = '16';
        $field0->typeofdata = 'V~O';
        $block->addField($field0);
        $field0->setPicklistValues($serviceDescPicklist);
        echo '<p>Added acc_exlabor_origin_desc picklist</p>';
    }

    $field1 = Vtiger_Field::getInstance('acc_exlabor_dest_desc', $module);
    if ($field1) {
        echo '<p>acc_exlabor_dest_desc field exists</p>';
    } else {
        $db  = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_acc_exlabor_dest_desc`";
        $db->pquery($sql, []);
        $field1             = new Vtiger_Field();
        $field1->label      = 'LBL_QUOTES_ACCEXLABORDESTDESC';
        $field1->name       = 'acc_exlabor_dest_desc';
        $field1->table      = 'vtiger_quotes';
        $field1->column     = 'acc_exlabor_dest_desc';
        $field1->columntype = 'VARCHAR(200)';
        $field1->uitype     = '16';
        $field1->typeofdata = 'V~O';
        $block->addField($field1);
        $field1->setPicklistValues($serviceDescPicklist);
        echo '<p>Added acc_exlabor_dest_desc picklist</p>';
    }

    $order = [];
    $orderFields = [
        'acc_exlabor_origin_hours',
        'acc_exlabor_dest_hours',
        'acc_exlabor_ot_origin_hours',
        'acc_exlabor_ot_dest_hours',
        'acc_exlabor_origin_desc',
        'acc_exlabor_dest_desc',
        'acc_wait_origin_hours',
        'acc_wait_dest_hours',
        'acc_wait_ot_origin_hours',
        'acc_wait_ot_dest_hours',
    ];
    foreach ($orderFields as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            $order[] = $field;
        }
    }

    setFieldSequenceGFESFL($order, $db);
}



function setFieldSequenceGFESFL($fields, $db)
{
    for ($i = 1;$i< count($fields); $i++) {
        $stmt            = 'SELECT tabid,block,sequence FROM `vtiger_field` WHERE fieldid='.$fields[$i]->id;
        $res             = $db->pquery($stmt);
        $row = $res->fetchRow();
        if (!$row) {
            continue;
        }
        $tabid = $row['tabid'];
        $block = $row['block'];
        $currentSeq = $row['sequence'];
        $stmt            = 'SELECT tabid,block,sequence FROM `vtiger_field` WHERE fieldid='.$fields[$i-1]->id;
        $res             = $db->pquery($stmt);
        $row = $res->fetchRow();
        if (!$row || $row['tabid'] != $tabid || $row['block'] != $block) {
            continue;
        }
        $targetSeq = $row['sequence'] + 1;
        if ($targetSeq == $currentSeq) {
            continue;
        }
        if ($targetSeq < $currentSeq) {
            $db->pquery('UPDATE `vtiger_field` SET sequence=sequence+1 WHERE tabid=? AND block=? AND sequence >= ? AND sequence <= ?',
                        [$tabid, $block, $targetSeq, $currentSeq]);
        } else {
            $db->pquery('UPDATE `vtiger_field` SET sequence=sequence-1 WHERE tabid=? AND block=? AND sequence >= ? AND sequence <= ?',
                        [$tabid, $block, $currentSeq, $targetSeq]);
        }
        $db->pquery('UPDATE `vtiger_field` SET sequence=? WHERE fieldid=?',
                    [$targetSeq, $fields[$i]->id]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";