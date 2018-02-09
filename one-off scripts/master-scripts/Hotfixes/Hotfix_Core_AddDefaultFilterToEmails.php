<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/27/2017
 * Time: 4:31 PM
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

$module = Vtiger_Module::getInstance('Emails');

if(!$module)
{
    return;
}

$filter = Vtiger_Filter::getInstance('All', $module);
if($filter){
    //replacing existing default filter
    $filter->delete();
}

$filter = new Vtiger_Filter();
$filter->name = 'All';
$filter->isdefault = true;
$module->addFilter($filter);

$i = 0;
foreach(['subject','parent_id','date_start', 'time_start', 'assigned_user_id', 'access_count' ] as $fieldName)
{
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if(!$field)
    {
        continue;
    }
    $filter->addField($field, $i);
    print "Added field: $fieldName <br />\n";
    if($fieldName == 'date_start'){
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_customview` SET sort_field = '$fieldName', sort_order = 'DESC' where cvid = $filter->id");
    }
    $i++;
}

