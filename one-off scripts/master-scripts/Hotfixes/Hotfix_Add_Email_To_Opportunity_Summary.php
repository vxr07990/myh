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


$db = PearDatabase::getInstance();
$sql    = "SELECT * FROM `vtiger_relatedlists` WHERE `tabid` = 60 ORDER BY `sequence` ASC";
$result = $db->pquery($sql);
$emailSeq  = 0;
$oppLabels = [];
while ($row = $result->fetchRow()) {
    if ($row['label'] == "Activities") {
        $activitySeq = $emailSeq;
    }
    array_push($oppLabels, $row['label']);
    $emailSeq++;
}
if ($oppLabels[$activitySeq + 1] != "Emails") {
    $updateEmails = true;
    if (in_array("Emails", $oppLabels)) {
        $sqlEmail = "UPDATE `vtiger_relatedlists` SET `sequence` = ? WHERE `tabid` = 60 AND `label`  = 'Emails'";
    } else {
        $sql    = "SELECT * FROM `vtiger_relatedlists_seq`";
        $result = $db->pquery($sql);
        $row    = $result->fetchRow();
        $newSeqId = $row['id'] + 1;
        $sql = "UPDATE `vtiger_relatedlists_seq` SET id = ?";
        $db->pquery($sql, [$newSeqId]);
        $sqlEmail     =
            "INSERT INTO `vtiger_relatedlists` (`relation_id`, `tabid`, `related_tabid`, `name`, `sequence`, `label`, `presence`, `actions`) VALUES (?, '60', '10', 'get_emails', ?,'Emails', '0', 'ADD')";
        $updateEmails = false;
    }
    $newSeq        = 1;
    $activityFound = false;
    foreach ($oppLabels as $value) {
        if (($value == "Activities") && (!$activityFound)) {
            $activityFound = true;
            $newSeq++;
            if ($updateEmails) {
                $db->pquery($sqlEmail, [$newSeq]);
            } else {
                $db->pquery($sqlEmail, [$newSeqId, $newSeq]);
            }
        } elseif ($activityFound) {
            $sql = "UPDATE `vtiger_relatedlists` SET `sequence` = ".$newSeq." WHERE `tabid` = 60 AND `label` = '".$value."' AND `label` != 'Emails'";
            $db->pquery($sql);
        }
        if ($value != "Emails") {
            $newSeq++;
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";