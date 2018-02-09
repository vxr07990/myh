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

//Hotfix_DailyNotes_Module.php

$moduleInstance = Vtiger_Module::getInstance('DailyNotes');
$dailyNotesNew = false;

if ($moduleInstance) {
    echo "Module DailyNotes already present";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'DailyNotes';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $dailyNotesNew = true;
    
}

$block= Vtiger_Block::getInstance('LBL_DAILYNOTES_INFORMATION', $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = 'LBL_DAILYNOTES_INFORMATION';
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('dailynotes_dailynotes', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_DAILYNOTES_NUMBER';
    $field01->name = 'dailynotes_dailynotes';
    $field01->table = 'vtiger_dailynotes';
    $field01->column = 'dailynotes_dailynotes';
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    $moduleInstance->setEntityIdentifier($field01);
}

global $adb;

$result = $adb->query("SELECT * FROM vtiger_modentity_num WHERE semodule='DailyNotes'");
if ($result && $adb->num_rows($result) == 0) {
	$numid = $adb->getUniqueId("vtiger_modentity_num");
	$adb->pquery("INSERT into vtiger_modentity_num (num_id,semodule,prefix,start_id,cur_id,active) values(?,?,?,?,?,?)", array($numid, 'DailyNotes', 'DN', 1, 1, 1));
}

$field2 = Vtiger_Field::getInstance('dailynotes_date', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_DAILYNOTES_DATE';
    $field2->name = 'dailynotes_date';
    $field2->table = 'vtiger_dailynotes';
    $field2->column = 'dailynotes_date';
    $field2->columntype = 'date';
    $field2->uitype = 5;
    $field2->typeofdata = 'D~M';
	$field2->summaryfield = 1;

    $block->addField($field2);
}

$field3 = Vtiger_Field::getInstance('dailynotes_note', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_DAILYNOTES_NOTE';
    $field3->name = 'dailynotes_note';
    $field3->table = 'vtiger_dailynotes';
    $field3->column = 'dailynotes_note';
    $field3->columntype = 'text';
    $field3->uitype = 19;
    $field3->typeofdata = 'V~M';
    $field3->summaryfield = 1;
        
    $block->addField($field3);
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

if($dailyNotesNew){
    
    $block->save($module);

    
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field01)->addField($field2, 1)->addField($field3, 2)->addField($agentField, 4);

    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";