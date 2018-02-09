<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


echo "Deleting Capacity Calendar Counter Block from OrdersTask!! <br>";

$db = PearDatabase::getInstance();
$tabid = getTabId("CapacityCalendarCounter");

$db->pquery("DELETE FROM vtiger_blocks WHERE tabid = ? AND blocklabel = 'LBL_CAPACITY_CALENDAR_COUNTER_SETUP'", array($tabid));

echo "Creating Capacity Calendar Counter default filter <br>";
	
$moduleInstance = Vtiger_Module::getInstance('CapacityCalendarCounter');
$block = Vtiger_Block::getInstance('LBL_CAPACITYCALENDARCOUNTER_INFORMATION', $moduleInstance);
$agentField = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$agentField) {
    $agentField = new Vtiger_Field();
    $agentField->label = 'Owner';
    $agentField->name = 'agentid';
    $agentField->table = 'vtiger_crmentity';
    $agentField->column = 'agentid';
    $agentField->columntype = 'INT(10)';
    $agentField->uitype = 1002;
    $agentField->typeofdata = 'I~M';

    $block->addField($agentField);
}

$block->save($module);

$result = $db->pquery("SELECT * FROM vtiger_customview WHERE viewname='All' AND entitytype='CapacityCalendarCounter'");

if($result && $db->num_rows($result) == 0){
   
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $field1 = Vtiger_Field::getInstance('calendar_code', $moduleInstance);
    $field2 = Vtiger_Field::getInstance('order_task_field', $moduleInstance);
    $field3 = Vtiger_Field::getInstance('agentid', $moduleInstance);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2); 
}



echo "Updating Calendar Code field <br>";

$db->pquery("ALTER TABLE vtiger_capacitycalendarcounter CHANGE calendar_code calendar_code VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL", array());
$db->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE columnname = 'calendar_code' AND tablename = 'vtiger_capacitycalendarcounter'", array());
	
echo "Updating Capacity Calendar Owner field <br>";
$db->pquery("UPDATE vtiger_field SET presence = '1', typeofdata = 'V~O', sequence = 4 WHERE columnname = 'capacitycalendarcounter_relcrmid' AND tablename = 'vtiger_capacitycalendarcounter'", array());

echo "Updating Counter Type field <br>";

$db->pquery("UPDATE vtiger_field SET sequence = 3, typeofdata = 'V~M', WHERE columnname = 'order_task_field' AND tablename = 'vtiger_capacitycalendarcounter'", array());

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";