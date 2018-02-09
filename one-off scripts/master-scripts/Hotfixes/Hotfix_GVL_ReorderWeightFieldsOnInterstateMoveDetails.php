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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/7/2016
 * Time: 4:52 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;

$moduleNames = ['Actuals'];
$fieldNames = ['gweight','tweight','weight'];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $fields = [];
    foreach ($fieldNames as $fieldName) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            $fields[] = $field;
        }
    }
    setFieldSequenceRWFOIMD($fields);
}

function setFieldSequenceRWFOIMD($fields)
{
    $db              = PearDatabase::getInstance();
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