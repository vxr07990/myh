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




$moduleInstance = Vtiger_Module::getInstance('MovePolicies'); // The module1 your blocks and fields will be in.
$new_module = false;

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'MovePolicies';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $new_module = true;
}


$block1 = Vtiger_Block::getInstance('LBL_MOVEPOLICY_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if (!$block1) {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_MOVEPOLICY_INFORMATION';
    $moduleInstance->addBlock($block1);
    $vehiclesIsNew = true;
}

$field00 = Vtiger_Field::getInstance('policies_id', $moduleInstance);
if (!$field00) {
    $field00 = new Vtiger_Field();
    $field00->label = 'LBL_MOVE_POLICY_ID';
    $field00->name = 'policies_id';
    $field00->table = 'vtiger_movepolicies';
    $field00->column = 'policies_id';
    $field00->columntype = 'VARCHAR(50)';
    $field00->uitype = 4;
    $field00->typeofdata = 'V~0';

    $block1->addField($field00);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'MovePolicies', 'POL', 1, 1, 1));

    $moduleInstance->setEntityIdentifier($field00);
}


$field01 = Vtiger_Field::getInstance('policies_subject', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_MOVE_POLICY_SUBJECT';
    $field01->name = 'policies_subject';
    $field01->table = 'vtiger_movepolicies';
    $field01->column = 'policies_subject';
    $field01->columntype = 'VARCHAR(100)';
    $field01->uitype = 2;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);
}

$field02 = Vtiger_Field::getInstance('policies_accountid', $moduleInstance);
if (!$field02) {
    $field02 = new Vtiger_Field();
    $field02->label = 'LBL_MOVE_POLICY_ACCOUNT';
    $field02->name = 'policies_accountid';
    $field02->table = 'vtiger_movepolicies';
    $field02->column = 'policies_accountid';
    $field02->columntype = 'INT(11)';
    $field02->uitype = 10;
    $field02->typeofdata = 'I~M';

    $block1->addField($field02);
    $field02->setRelatedModules(array('Accounts'));
}

$field03 = Vtiger_Field::getInstance('policies_contractid', $moduleInstance);
if (!$field03) {
    $field03 = new Vtiger_Field();
    $field03->label = 'LBL_MOVE_POLICY_CONTRACT';
    $field03->name = 'policies_contractid';
    $field03->table = 'vtiger_movepolicies';
    $field03->column = 'policies_contractid';
    $field03->columntype = 'INT(11)';
    $field03->uitype = 10;
    $field03->typeofdata = 'I~O';

    $block1->addField($field03);
    $field03->setRelatedModules(array('Contracts'));
}

$field04 = Vtiger_Field::getInstance('policies_tariffid', $moduleInstance);
if (!$field04) {
    $field04 = new Vtiger_Field();
    $field04->label = 'LBL_MOVE_POLICY_TARIFF';
    $field04->name = 'policies_tariffid';
    $field04->table = 'vtiger_movepolicies';
    $field04->column = 'policies_tariffid';
    $field04->columntype = 'INT(11)';
    $field04->uitype = 10;
    $field04->typeofdata = 'I~M';

    $block1->addField($field04);
    $field04->setRelatedModules(array('TariffManager'));
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

    $block1->addField($field36);
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

    $block1->addField($field37);
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

    $block1->addField($field38);
}

$field39 = Vtiger_Field::getInstance('agentid', $moduleInstance);

if (!$field39) {
    $field39 = new Vtiger_Field();
    $field39->label = 'Owner';
    $field39->name = 'agentid';
    $field39->table = 'vtiger_crmentity';
    $field39->column = 'agentid';
    $field39->uitype = 1002;
    $field39->typeofdata = 'I~M';

    $block1->addField($field39);
}


//end block1 fields
$block1->save($moduleInstance);

$block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if (!$block2) {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($block2);
}

$block3 = Vtiger_Block::getInstance('LBL_MOVEPOLICIES_NOTES', $moduleInstance);
if (!$block3) {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_MOVEPOLICIES_NOTES';
    $moduleInstance->addBlock($block3);
}

$fieldnotes = Vtiger_Field::getInstance('movepolicies_notes', $moduleInstance);
if (!$fieldnotes) {
    $fieldnotes = new Vtiger_Field();
    $fieldnotes->label = 'LBL_MOVEPOLICIES_NOTES_FIELD';
    $fieldnotes->name = 'movepolicies_notes';
    $fieldnotes->table = 'vtiger_movepolicies';
    $fieldnotes->column = 'movepolicies_notes';
    $fieldnotes->columntype = 'TEXT';
    $fieldnotes->uitype = 19;
    $fieldnotes->typeofdata = 'V~O';

    $block3->addField($fieldnotes);
}


//if we are creating the module => Add the filters and related lists

if ($new_module) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field00)->addField($field01, 1)->addField($field02, 2)->addField($field03, 3)->addField($field04, 4);

//Adding relationship between Policies and Contracts
$moduleInstanceContracts = Vtiger_Module::getInstance('Contracts');
    $moduleInstanceContracts->setRelatedList($moduleInstance, 'Move Policies', array('ADD'), 'get_dependents_list');

//Adding relationship between Policies and Contracts
$moduleInstanceAccounts = Vtiger_Module::getInstance('Accounts');
    $moduleInstanceAccounts->setRelatedList($moduleInstance, 'Move Policies', array('ADD'), 'get_dependents_list');
}

//Move the module under right menu

Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent = 'SALES_MARKETING_TAB' WHERE name = 'MovePolicies'");


//Enable modtracker for module
ModTracker::enableTrackingForModule($moduleInstance->id);

//Add new table to save items

if (!Vtiger_Utils::CheckTable('vtiger_movepolicies_items')) {
    Vtiger_Utils::CreateTable('vtiger_movepolicies_items',
                              '(
                                                              id INT(19)  NOT NULL AUTO_INCREMENT KEY,
                                                                policies_id INT(19),
                                                                tariff_crmid INT(19),
                                                                tariff_id INT(19),
                                                                tariff_section int(5) DEFAULT NULL,
								item_id VARCHAR(19),
								item_des VARCHAR(150),
                                                                item_code VARCHAR(50) DEFAULT NULL,
								item_auth VARCHAR(25),
								item_auth_limits VARCHAR(25),
                                                                item_remarks TEXT
								)', true);
} else {
    Vtiger_Utils::AddColumn('vtiger_movepolicies_items', 'item_code', 'VARCHAR(50)  NULL  DEFAULT NULL  AFTER `item_des`');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";