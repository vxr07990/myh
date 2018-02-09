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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');



$moduleName = 'WFAccounts';
$module = Vtiger_Module_Model::getInstance($moduleName);

if(!$module){
    return;
}

$blockLabel = 'LBL_WFACCOUNTS_DETAIL';
$block = Vtiger_Block_Model::getInstance($blockLabel, $module);

if(!$block){
    return;
}
$field1 = Vtiger_Field::getInstance('fax', $module);
if ($field1) {
    echo "The fax field already exists<br>\n";
} else {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_WFACCOUNTS_FAX';
    $field1->name       = 'fax';
    $field1->table      = 'vtiger_wfaccounts';
    $field1->column     = 'fax';
    $field1->columntype = 'VARCHAR(20)';
    $field1->uitype     = 11;
    $field1->typeofdata = 'V~O';
    $block->addField($field1);
}
$field2 = Vtiger_Field::getInstance('website', $module);
if ($field2) {
    echo "The website field already exists<br>\n";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_WFACCOUNTS_WEBSITE';
    $field2->name       = 'website';
    $field2->table      = 'vtiger_wfaccounts';
    $field2->column     = 'website';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype     = 1;
    $field2->typeofdata = 'V~O';
    $block->addField($field2);
}


$fieldsToHide = [
    'description'
];

$db = PearDatabase::getInstance();

foreach($fieldsToHide as $fieldname) {
    $field = Vtiger_Field::getInstance($fieldname, $module);
    if (!$field) {
        continue;
    }
    $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?', [1, $field->id]);
}

$fieldNames = [
    'wfaccounts_type','account_status',
    'name', 'customer_number',
    'national_account',
    'primary_phone', 'fax',
    'website', 'primary_email',
    'download_to_device', 'logo',
    'agentid', 'assigned_user_id'

];

foreach ($fieldNames as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        $fields[] = $field;
    }
}
setFieldSequenceRWFOIMD($fields);

function setFieldSequenceRWFOIMD($fields)
{
    $db              = PearDatabase::getInstance();
    for ($i = 1;$i< count($fields); $i++) {
        $stmt            = 'SELECT tabid,block,sequence FROM `vtiger_field` WHERE fieldid=?';
        $res             = $db->pquery($stmt, [$fields[$i]->id]);
        $row = $res->fetchRow();
        if (!$row) {
            continue;
        }
        $tabid = $row['tabid'];
        $block = $row['block'];
        $currentSeq = $row['sequence'];
        $stmt            = 'SELECT tabid,block,sequence FROM `vtiger_field` WHERE fieldid=?';
        $res             = $db->pquery($stmt, [$fields[$i-1]->id]);
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
