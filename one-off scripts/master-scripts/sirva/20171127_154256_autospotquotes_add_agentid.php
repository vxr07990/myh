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
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$current_user = Users::getActiveAdminUser();

if(!$db) {
    $db = PearDatabase::getInstance();
}

$map = [
    'AutoSpotQuote' => [
        'LBL_AUTOSPOTQUOTEDETAILS' => [
            'LBL_AUTOSPOTQUOTE_AGENTID' => [
                'name' => 'agentid',
                'column' => 'agentid',
                'table' => 'vtiger_crmentity',
                'columntype' => 'INT(11)',
                'uitype' => 1002
            ]
        ]
    ]
];

multicreate($map);

print "Mapping estimate agentids to new AutoSpotQuote agentid field...<br/>\n";
$sql = "SELECT autospotquoteid, estimate_id FROM vtiger_autospotquote JOIN vtiger_crmentity ON autospotquoteid=crmid WHERE deleted=0 AND estimate_id IS NOT NULL";
if($result = $db->query($sql)) {
    while($row = $result->fetchRow()) {
        $estimate = Vtiger_Record_Model::getInstanceById($row['estimate_id']);
        $autoSpot = Vtiger_Record_Model::getInstanceById($row['autospotquoteid']);

        if($autoSpot->get('agentid') != $estimate->get('agentid')) {
            print "Updating AutoSpotQuote " . $autoSpot->getId() . " agentid...<br/>\n";
            $autoSpot->set('mode', 'edit');
            $autoSpot->set('agentid', $estimate->get('agentid'));

            $autoSpot->save();
        }
        else {
            print "Skipping AutoSpotQuote " . $autoSpot->getId() . ", agentid already set...<br/>\n";
        }
    }
}
else {
    print "There was an error getting available AutoSpotQuotes for mapping, please check the mysqlFail log.<br/>\n";
    exit;
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

