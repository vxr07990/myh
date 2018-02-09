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
include_once('vtlib/Vtiger/Field.php');

$db = PearDatabase::getInstance();

$fieldName = "source_name";

$leads = Vtiger_Module::getInstance('Leads');
$opps  = Vtiger_Module::getInstance('Opportunities');

if(!$leads || !$opps) {
    print "\e[33mNo leads and/or opportunities module...\e[0m";
    return;
}

$lField = Vtiger_Field::getInstance($fieldName, $leads);
$oField = Vtiger_Field::getInstance($fieldName, $opps);

if($lField && $oField) {
    $res = $db->pquery('SELECT 1 FROM vtiger_convertleadmapping WHERE leadfid=? AND potentialfid=?',
        [$lField->id, $oField->id]);
    if($db->num_rows($res)) {
        return;
    }

    $db->pquery('INSERT INTO vtiger_convertleadmapping (leadfid,potentialfid) VALUES (?,?)',
        [$lField->id, $oField->id]);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";