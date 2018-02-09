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

//Hotfix_Holiday_Module.php

$moduleInstance = Vtiger_Module::getInstance('Holiday');
$holidayModNew = false;

if ($moduleInstance) {
    echo "Module Holiday already present";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Holiday';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    
    $holidayModNew = true;
}

$block= Vtiger_Block::getInstance('LBL_HOLIDAY_INFORMATION', $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = 'LBL_HOLIDAY_INFORMATION';
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('holiday_holiday', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_HOLIDAY_NUMBER';
    $field01->name = 'holiday_holiday';
    $field01->table = 'vtiger_holiday';
    $field01->column = 'holiday_holiday';
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    $moduleInstance->setEntityIdentifier($field01);
}

global $adb;

$result = $adb->query("SELECT * FROM vtiger_modentity_num WHERE semodule='Holiday'");
if ($result && $adb->num_rows($result) == 0) {
	$numid = $adb->getUniqueId("vtiger_modentity_num");
	$adb->pquery("INSERT into vtiger_modentity_num (num_id,semodule,prefix,start_id,cur_id,active) values(?,?,?,?,?,?)", array($numid, 'Holiday', 'HD', 1, 1, 1));
}

$field2 = Vtiger_Field::getInstance('holiday_date', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_HOLIDAY_DATE';
    $field2->name = 'holiday_date';
    $field2->table = 'vtiger_holiday';
    $field2->column = 'holiday_date';
    $field2->columntype = 'date';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~M';

    $block->addField($field2);
}

$field3 = Vtiger_Field::getInstance('holiday_type', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_HOLIDAY_TYPE';
    $field3->name = 'holiday_type';
    $field3->table = 'vtiger_holiday';
    $field3->column = 'holiday_type';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~M';
	
    $block->addField($field3);
    
    $field3->setPicklistValues(array('Blocked', 'Holiday'));
}

$field4 = Vtiger_Field::getInstance('holiday_business_line', $moduleInstance);
if (!$field4) {
	$field4 = new Vtiger_Field();
	$field4->label = 'LBL_HOLIDAY_BUSINESS_LINE';
	$field4->name = 'holiday_business_line';
	$field4->table = 'vtiger_holiday';
	$field4->column = 'holiday_business_line';
	$field4->columntype = 'VARCHAR(255)';
	$field4->uitype = 33;
	$field4->typeofdata = 'V~O';
	
	$block->addField($field4);

	$field4->setPicklistValues(array("All","HHG - Interstate","HHG - Intrastate","HHG - Local","HHG - International","Electronics - Interstate","Electronics - Intrastate","Electronics - Local","Electronics - International","Displays & Exhibits - Interstate","Displays & Exhibits - Intrastate","Display & Exhibits - Local","Displays & Exhibits - International","General Commodities - Interstate","General Commodities - Intrastate","General Commodities - Local","General Commodities - International","Auto - Interstate","Auto - Intrastate","Auto - Local","Auto - International","Commercial - Interstate","Commercial - Intrastate","Commercial - Local","Commercial - International"));
}

$field36 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field36) {
    $field36 = new Vtiger_Field();
    $field36->label = 'Assigned To';
    $field36->name = 'assigned_user_id';
    $field36->table = 'vtiger_crmentity';
    $field36->column = 'smownerid';
    $field36->uitype = 53;
    $field36->typeofdata = 'V~M';

    $block->addField($field36);
}

$field37 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if (!$field37) {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'createdtime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $block->addField($field37);
}

$field38 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$field38) {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'modifiedtime';
    $field38->table = 'vtiger_crmentity';
    $field37->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $block->addField($field38);
}

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

if($holidayModNew){
    $block->save($module);

    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field01)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($agentField, 4);

    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();  
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";