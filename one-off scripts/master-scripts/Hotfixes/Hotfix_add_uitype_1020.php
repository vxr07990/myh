<?php

if (function_exists("call_ms_function_ver")) {
	$version = 9;
	if (call_ms_function_ver(__FILE__, $version)) {
		//a		lready ran
		        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
		return;
	}
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$db = PearDatabase::getInstance();

$result = $db->pquery('SELECT * FROM vtiger_ws_fieldtype WHERE uitype="1020"');
if($result && $db->num_rows($result) == 0){
	echo 'Start: add new uitype => 1020, fieldtype => agentvanlinepicklis<br />\n';
	$sql = 'INSERT INTO vtiger_ws_fieldtype (uitype,fieldtype) VALUES ("1020","agentvanlinepicklist")';
	$result = $db->pquery($sql);
	echo 'Finish: add new uitype 1020 => agentvanlinepicklis<br />\n';
}


echo 'Start: Updating uitype of the Owner field of following modules to 1020<br />\n';
//update uitype of field agentid from this modules to 1020
$modulesToChangeOwnerUIType = [
    'ItemCodes',
    'CommissionPlans',
    'CommissionPlansFilter',
    'AgentCompensation',
    'AgentCompensationGroup',
    'Tariffs',
    'RevenueGrouping',
    'EmployeeRoles',
    'Tariffs',
    'OPList',
    'Contracts',
    'Accounts',
    'PushNotifications',
    'ZoneAdmin',
    'Carriers'
];

foreach ($modulesToChangeOwnerUIType as $moduleName) {
	$module = Vtiger_Module::getInstance($moduleName);
	$sql = "UPDATE vtiger_field SET uitype = '1020' WHERE columnname = 'agentid' AND tabid = ? AND uitype != '1020' LIMIT 1";
	if($module){
		$result = $db->pquery($sql, [$module->id]);
		echo "<li>Owner field of $module->name updated<br>\n";
	}
	else {
		echo "Module $moduleName don't exist<br>\n";
	}
}
echo 'Finish: Updating uitype of Owner field of the modules to 1020<br />\n';



echo 'Start: Adding the new Owner field uitype 1020 to modules<br />\n';
//add owner field to this modules on that block
//module => block
$modulesToAddOwnerField = [
    'Vanlines' => [
        'blockName' => 'LBL_VANLINES_INFORMATION',
        'fieldSequence' => 2
        ],
    'Agents' => [
        'blockName' => 'LBL_AGENTS_INFORMATION',
        'fieldSequence' => 4
        ]
];

foreach ($modulesToAddOwnerField as $moduleName => $data) {
	$module = Vtiger_Module::getInstance($moduleName);
	if($module){
		$block = Vtiger_Block::getInstance($data['blockName'], $module);
		if($block){
			$fieldSequence = $data['fieldSequence'];
			$agentField = Vtiger_Field::getInstance('agentid', $module);
			if (!$agentField) {
				$agentField = new Vtiger_Field();
				$agentField->label = 'Owner Agent';
				$agentField->name = 'agentid';
				$agentField->table = 'vtiger_crmentity';
				$agentField->column = 'agentid';
				$agentField->columntype = 'INT(10)';
				$agentField->uitype = 1020;
				$agentField->typeofdata = 'I~M';
				$agentField->sequence = $fieldSequence;
				
				$block->addField($agentField);
			}
		}
	}
	else {
		echo "Module $moduleName don't exist<br>\n";
	}
}

//Fix Agents layout
$module = Vtiger_Module::getInstance('Agents');
$owner = Vtiger_Field::getInstance('agent_type_picklist', $module);
$block = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $module);

$result = $db->pquery('SELECT sequence FROM vtiger_field WHERE tabid=? AND fieldid=?', [$module->id, $owner->id]);

if($result && $db->num_rows($result)>0){
	
	$agentType = Vtiger_Field::getInstance('agent_type_picklist', $module);
	$agentGrade = Vtiger_Field::getInstance('agents_grade', $module);
	
	$sequence = $db->query_result($result, 0, 'sequence');
	
	$result = $db->pquery("UPDATE vtiger_field SET sequence=$sequence+1 WHERE tabid = $module->id  AND fieldid = $agentType->id");
	$result = $db->pquery("UPDATE vtiger_field SET sequence=$sequence+2 WHERE tabid = $module->id  AND fieldid = $agentGrade->id");
	$fieldSequence = $sequence+3;
	$result = $db->pquery("UPDATE vtiger_field SET sequence=sequence+1 WHERE tabid = $module->id AND block = $block->id AND sequence >= $fieldSequence");
	
}
