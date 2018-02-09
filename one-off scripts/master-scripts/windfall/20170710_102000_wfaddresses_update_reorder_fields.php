<?php
/**
 * Created by PhpStorm.
 * User: jgriffin
 * Date: 12/5/2016
 * Time: 10:55 AM
 */
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

$module = Vtiger_Module::getInstance('WFAddress');

if(!$module)
{
    return;
}

$fieldsToHide = [
    'firstname',    'lastname',
    'company',      'wfaddress_phone',
    'wfaddress_fax', 'wfaddress_email',
    'wfaddress_type'
];

$db = PearDatabase::getInstance();

foreach($fieldsToHide as $fieldname) {
    $field = Vtiger_Field::getInstance($fieldname, $module);
    if (!$field) {
        continue;
    }
    $db->pquery('UPDATE vtiger_field SET presence=? WHERE fieldid=?', [1, $field->id]);
}

$fieldSeq = [
    'address_name',
    'wfaddress_address1',
    'wfaddress_address2', 'wfaddress_city',
    'wfaddress_state', 'wfaddress_country',
    'wfaddress_zip'
];


$fields = [];
foreach ($fieldSeq as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        $fields[] = $field;
    }
}
setFieldSequenceWFURF($fields);


function setFieldSequenceWFURF($fields)
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
