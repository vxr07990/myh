<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 4/3/2017
 * Time: 10:00 AM
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

$db = &PearDatabase::getInstance();

$fieldMap = [
    'preferred_ppdate' => 'preffered_ppdate',
    'preferred_pldate' => 'preferred_pldate',
    'preferred_pddate' => 'preferred_pddate',
];

$leadsModule = Vtiger_Module::getInstance('Leads');
$oppsModule = Vtiger_Module::getInstance('Opportunities');

if(!$leadsModule || !$oppsModule)
{
    return;
}

foreach($fieldMap as $leadFieldName => $oppFieldName)
{
    $leadField = Vtiger_Field::getInstance($leadFieldName, $leadsModule);
    $oppField = Vtiger_Field::getInstance($oppFieldName, $oppsModule);
    if(!$leadField || !$oppField)
    {
        continue;
    }
    $res = $db->pquery('SELECT 1 FROM vtiger_convertleadmapping WHERE leadfid=? AND potentialfid=?',
                       [$leadField->id, $oppField->id]);
    if($db->num_rows($res))
    {
        continue;
    }
    $db->pquery('INSERT INTO vtiger_convertleadmapping (leadfid,potentialfid) VALUES (?,?)',
                [$leadField->id, $oppField->id]);
}




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
