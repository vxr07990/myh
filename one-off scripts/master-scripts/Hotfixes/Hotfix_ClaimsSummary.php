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

$moduleInstance = Vtiger_Module::getInstance('ClaimsSummary');
$ClaimsSummaryIsNew = false;
if ($moduleInstance) {
    echo "Module ClaimsSummary already present - Updating Fields";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ClaimsSummary';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $ClaimsSummaryIsNew = true;
}

$block= Vtiger_Block::getInstance('LBL_CLAIMSSUMMARY_INFORMATION', $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = 'LBL_CLAIMSSUMMARY_INFORMATION';
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('claimssummary_claimssummary', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_CLAIMSSUMMARY_NUMBER';
    $field01->name = 'claimssummary_claimssummary';
    $field01->table = 'vtiger_claimssummary';
    $field01->column = 'claimssummary_claimssummary';
    $field01->columntype = 'VARCHAR(10)';
    $field01->summaryfield = 1;
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    $moduleInstance->setEntityIdentifier($field01);
}

    global $adb;

    $result = $adb->query("SELECT * FROM vtiger_modentity_num WHERE semodule='ClaimsSummary'");
    if ($result && $adb->num_rows($result) == 0) {
        $numid = $adb->getUniqueId("vtiger_modentity_num");
        $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'ClaimsSummary', 'CS', 1, 1, 1));
    }
    

$field1 = Vtiger_Field::getInstance('claimssummary_preferred', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CLAIMSSUMMARY_PREFERRED';
    $field1->name = 'claimssummary_preferred';
    $field1->table = 'vtiger_claimssummary';
    $field1->column = 'claimssummary_preferred';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 1;
    $field1->setPicklistValues(array('Transferee', 'Co-Transferee', 'Claimant'));
    $block->addField($field1);
}

$field001 = Vtiger_Field::getInstance('claimssummary_contactid', $moduleInstance);
if (!$field001) {
    $field001 = new Vtiger_Field();
    $field001->label = 'LBL_CLAIMSSUMMARY_PREFERRED_CONTACT';
    $field001->name = 'claimssummary_contactid';
    $field001->table = 'vtiger_claimssummary';
    $field001->column = 'claimssummary_contactid';
    $field001->columntype = 'VARCHAR(100)';
    $field001->uitype = 10;
    $field001->typeofdata = 'V~O';
    $field001->summaryfield = 1;
    $block->addField($field001);
    
    $field001->setRelatedModules(array('Contacts'));
}

$field2 = Vtiger_Field::getInstance('claimssummary_valuationtype', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CLAIMSSUMMARY_VALUATIONTYPE';
    $field2->name = 'claimssummary_valuationtype';
    $field2->table = 'vtiger_claimssummary';
    $field2->column = 'claimssummary_valuationtype';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->summaryfield = 1;
    $block->addField($field2);
}


$field3 = Vtiger_Field::getInstance('claimssummary_representative', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CLAIMSSUMMARY_REPRESENTATIVE';
    $field3->name = 'claimssummary_representative';
    $field3->table = 'vtiger_claimssummary';
    $field3->column = 'claimssummary_representative';
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~O';
    $field3->summaryfield = 1;
    $block->addField($field3);
}


$field5 = Vtiger_Field::getInstance('claimssummary_orderid', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CLAIMSSUMMARY_ORDERNUMBER';
    $field5->name = 'claimssummary_orderid';
    $field5->table = 'vtiger_claimssummary';
    $field5->column = 'claimssummary_orderid';
    $field5->columntype = 'INT(20)';
    $field5->uitype = 10;
    $field5->typeofdata = 'I~O';
    $block->addField($field5);
    $field5->setRelatedModules(array('Orders'));
}

$field6 = Vtiger_Field::getInstance('claimssummary_accountid', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CLAIMSSUMMARY_ACCOUNT';
    $field6->name = 'claimssummary_accountid';
    $field6->table = 'vtiger_claimssummary';
    $field6->column = 'claimssummary_accountid';
    $field6->columntype = 'INT(10)';
    $field6->uitype = 10;
    $field6->typeofdata = 'I~O';
    $block->addField($field6);
    $field6->setRelatedModules(array('Accounts'));
}

$field7 = Vtiger_Field::getInstance('claimssummary_declaredvalue', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CLAIMSSUMMARY_DECLAREDVALUE';
    $field7->name = 'claimssummary_declaredvalue';
    $field7->table = 'vtiger_claimssummary';
    $field7->column = 'claimssummary_declaredvalue';
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    $field7->summaryfield = 1;
    $block->addField($field7);
}

$field8 = Vtiger_Field::getInstance('item_status', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CLAIMSSUMMARY_STATUS';
    $field8->name = 'item_status';//claimssummary_status';
    $field8->table = 'vtiger_claimssummary';
    $field8->column = 'item_status';//claimssummary_status';
    $field8->columntype = 'VARCHAR(100)';
    $field8->uitype = 16;
    $field8->typeofdata = 'V~O';
    $field8->summaryfield = 1;
    $field8->setPicklistValues(array('Pending', 'Closed', 'Allocated'));
    $block->addField($field8);
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
    $agentField->typeofdata = 'I~O';

    $block->addField($agentField);
}

$block->save($module);

if ($ClaimsSummaryIsNew) {

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field01)
            ->addField($field1, 2)
            ->addField($field2, 3)
            ->addField($field3, 4)
            ->addField($field5, 6)
            ->addField($field6, 7)
            ->addField($field7, 8)
            ->addField($field8, 9);

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Relate to Claims
    $claimsInstance = Vtiger_Module::getInstance('Claims');
    $moduleInstance->setRelatedList($claimsInstance, 'Claims Types', array('ADD'), 'get_dependents_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";