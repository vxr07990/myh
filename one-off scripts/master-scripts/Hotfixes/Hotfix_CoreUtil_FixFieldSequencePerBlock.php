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
 * Date: 10/18/2016
 * Time: 8:30 AM
 */

// this script will push duplicate field sequences within a block to be all distinct, so that ordering can be properly

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

function ms_FixFieldSequenceInBlock($block)
{
    $db = &PearDatabase::getInstance();
    $blockRes = $db->pquery('SELECT fieldid,sequence FROM `vtiger_field` WHERE block=? ORDER BY sequence ASC', [$block]);
    $currentSeq = 1;
    while ($row2 = $blockRes->fetchRow()) {
        if ($currentSeq > $row2['sequence']) {
            $db->pquery('UPDATE `vtiger_field` SET sequence=? WHERE fieldid=?', [$currentSeq, $row2['fieldid']]);
        } elseif ($row2['sequence'] > $currentSeq) {
            $currentSeq = $row2['sequence'];
        }
        $currentSeq++;
    }
}


function ms_FixFieldSequenceInAllBlocks()
{
    $db = &PearDatabase::getInstance();
    $result = $db->pquery('SELECT blockid FROM `vtiger_blocks`');
    while ($row = $result->fetchRow()) {
        ms_FixFieldSequenceInBlock($row['blockid']);
    }
}

function ms_SetFieldSequence($fields, $db)
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