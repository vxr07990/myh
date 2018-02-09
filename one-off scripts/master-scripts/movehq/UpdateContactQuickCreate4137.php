<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/24/2017
 * Time: 1:42 PM
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

$fields = [
    'contact_type' => 0,
    'firstname' => 2,
    'lastname' => 0,
    'homephone' => 2,
    'mobile' => 2,
    'phone' => 2,
    'email' => 2,
    'secondaryemail' => 2,
    'agentid' => 0,
    'assigned_user_id' => 0,
];

$module = Vtiger_Module::getInstance('Contacts');
if(!$module)
{
    return;
}

$db->pquery('UPDATE vtiger_field SET quickcreate=1,quickcreatesequence=NULL WHERE tabid=? AND quickcreate IN(0,2)',
            [$module->id]);

$seq = 1;
foreach($fields as $fieldName => $q)
{
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if(!$field)
    {
        continue;
    }
    $db->pquery('UPDATE vtiger_field SET quickcreate=?,quickcreatesequence=? WHERE fieldid=?',
                [$q, $seq++, $field->id]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";