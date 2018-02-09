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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$fieldOrder = [
    'firstname',
    'lastname',
    'mailingstreet',
    'mailingcity',
    'mailingstate',
    'mailingzip',
    'mailingcountry',
    'primary_phone_type',
    'phone',
    'primary_phone_ext',
    'email',
    'contact_type',
    'agentid',
    'assigned_user_id',
    'account_id'
];
$sql = "SELECT tabid FROM `vtiger_tab` WHERE name = 'Contacts'";
if($res = $db->query($sql)) {
    $tabid = $res->fetchRow()['tabid'];
    print "TABID OF {$tabid} GATHERED\n\n";
}else {
    print "FAILED TO GATHER CONTACTS TABID\n";
    return;
}

for($i = 0; $i < count($fieldOrder); $i++) {
    print "-- BEGIN {$fieldOrder[$i]} --\n";
    $sql = "SELECT fieldid FROM vtiger_field WHERE fieldname = ? AND tabid = ?";
    if($res = $db->pquery($sql, [$fieldOrder[$i], $tabid])) {
        $id = $res->fetchRow()['fieldid'];
        print "FIELD ID: {$id}\n";
        print "NEW QUICKCREATE SEQUENCE: {$i}\n";

        $sql = "UPDATE vtiger_field SET quickcreatesequence = ? WHERE fieldid = ?";
        if($db->pquery($sql, [$i, $id])) {
            print "SUCCESSFUL\n";
        }else {
            print "FAILURE\n";
        }
    }else {
        print "ERR: " . print_r($res, true) . "\n";
    }
    print "\n";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
