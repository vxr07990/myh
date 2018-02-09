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



//VendorsOutofService.php
include_once 'vtlib/Vtiger/Module.php';
$vendorsOutServIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VendorsOutofService'); // The module1 your blocks and fields will be in.
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VendorsOutofService';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $vendorsOutServIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VENDORS_OUT_OF_SERVICE_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VENDORS_OUT_OF_SERVICE_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VENDORS_OUT_OF_SERVICE_INFORMATION';
    $moduleInstance->addBlock($block1);
}

//start block1 fields

$field01 = Vtiger_Field::getInstance('voos_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_OUTOFSERVICE_NO';
    $field01->name = 'voos_number';
    $field01->table = 'vtiger_vendorsoutofservice';
    $field01->column = 'voos_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT INTO vtiger_modentity_num VALUES(?,?,?,?,?,?,?)", array($numid, 'VendorsOutofService', 'OUTS', 1, 1, 1, NULL));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('voos_vendorid', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_OUTOFSERVICE_VENDOR';
    $field0->name = 'voos_vendorid';
    $field0->table = 'vtiger_vendorsoutofservice';
    $field0->column = 'voos_vendorid';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);

    $field0->setRelatedModules(array('Vendors'));
}

$field1 = Vtiger_Field::getInstance('voos_status', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OUTOFSERVICE_STATUS';
    $field1->name = 'voos_status';
    $field1->table = 'vtiger_vendorsoutofservice';
    $field1->column = 'voos_status';
    $field1->columntype = 'VARCHAR(150)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~M';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Out of Service', 'On Notice'));
}

$field2 = Vtiger_Field::getInstance('voos_reason', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_OUTOFSERVICE_REASON';
    $field2->name = 'voos_reason';
    $field2->table = 'vtiger_vendorsoutofservice';
    $field2->column = 'voos_reason';
    $field2->columntype = 'VARCHAR(150)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';

    $block1->addField($field2);
    $field2->setPicklistValues(array('Insurance - All Reasons', 'Insurance - Auto Liability Expired', 'Insurance - Auto Liability Incomplete', 'Insurance - General Liability Expired', 'Insurance - General Liability Incomplete', 'Insurance - Non-trucking Liability Expired', 'Insurance - OCC/ACC Insurance Expired', 'Insurance - Physical Damage Expired', 'Insurance - Umbrella Expired', 'Insurance - Worker\'s Comp Expired', 'Insurance - Worker\'s Comp Incomplete', 'Insurance - Cancel 90 Days', 'Insurance - Cancel Over 90 Days Must Reapply', 'Contract - All Reasons', 'Contract - I/C 30 Day Notice to Cancel', 'Contract - I/C Contract Cancelled - Call Safety to Clear', 'Contract - TSC 30 Day Notice to Cancel', 'Contract - Paperwork Incomplete'));
}

$field3 = Vtiger_Field::getInstance('voos_effective_date', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_OUTOFSERVICE_EFFECTIVE_DATE';
    $field3->name = 'voos_effective_date';
    $field3->table = 'vtiger_vendorsoutofservice';
    $field3->column = 'voos_effective_date';
    $field3->columntype = 'DATE';
    $field3->uitype = 5;
    $field3->typeofdata = 'D~M';

    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('voos_reinstated_date', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_OUTOFSERVICE_REINSTATED_DATE';
    $field4->name = 'voos_reinstated_date';
    $field4->table = 'vtiger_vendorsoutofservice';
    $field4->column = 'voos_reinstated_date';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('voos_comments', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_OUTOFSERVICE_COMMENTS';
    $field5->name = 'voos_comments';
    $field5->table = 'vtiger_vendorsoutofservice';
    $field5->column = 'voos_comments';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 19;
    $field5->typeofdata = 'V~O';

    $block1->addField($field5);
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

$agentField = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$agentField) {
    $agentField = new Vtiger_Field();
    $agentField->label = 'Owner Agent';
    $agentField->name = 'agentid';
    $agentField->table = 'vtiger_crmentity';
    $agentField->column = 'agentid';
    $agentField->columntype = 'INT(10)';
    $agentField->uitype = 1002;
    $agentField->typeofdata = 'I~O';

    $block1->addField($agentField);
}

$block1->save($module);


if ($vendorsOutServIsNew) {
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();

    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field01)
            ->addField($field0, 1)
            ->addField($field1, 2)
            ->addField($field2, 3)
            ->addField($field3, 4)
            ->addField($field4, 5);
            //->addField($field5, 6);
}

if ($vendorsOutServIsNew) {
    $vendorsInstance = Vtiger_Module::getInstance('Vendors');
    $vendorsInstance->setRelatedList($moduleInstance, 'Out of Service', array('ADD'), 'get_dependents_list');
}

//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VendorsOutofService'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";