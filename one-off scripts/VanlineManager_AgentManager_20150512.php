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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');

$vanlineIsNew = false;
$agentIsNew = false;
$menuInstance = Vtiger_Menu::getInstance('SYSTEM_ADMIN_TAB');

echo "<h2>VanlineManager Module</h2>";

$vanlineInstance = Vtiger_Module::getInstance('VanlineManager');
if (!$vanlineInstance) {
    $vanlineInstance = new Vtiger_Module();
    $vanlineInstance->name = 'VanlineManager';
    $vanlineInstance->save();

    $vanlineInstance->initTables();
}

$vanlineBlock1 = Vtiger_Block::getInstance('LBL_VANLINEMANAGER_INFORMATION', $vanlineInstance);
if ($vanlineBlock1) {
    echo "<br> Block 'LBL_VANLINEMANAGER_INFORMATION' is already present <br>";
} else {
    $vanlineBlock1 = new Vtiger_Block();
    $vanlineBlock1->label = 'LBL_VANLINEMANAGER_INFORMATION';
    $vanlineInstance->addBlock($vanlineBlock1);
    $vanlineIsNew = true;
}

$vanlineBlock2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $vanlineInstance);
if ($vanlineBlock2) {
    echo "<br> Block 'LBL_CUSTOM_INFORMATION' is already present <br>";
} else {
    $vanlineBlock2 = new Vtiger_Block();
    $vanlineBlock2->label = 'LBL_CUSTOM_INFORMATION';
    $vanlineInstance->addBlock($vanlineBlock2);
}

$vanlineBlock3 = Vtiger_Block::getInstance('LBL_VANLINEMANAGER_ADDRESSINFORMATION', $vanlineInstance);
if ($vanlineBlock3) {
    echo "<br> Block 'LBL_VANLINEMANAGER_ADDRESSINFORMATION' is already present <br>";
} else {
    $vanlineBlock3 = new Vtiger_Block();
    $vanlineBlock3->label = 'LBL_VANLINEMANAGER_ADDRESSINFORMATION';
    $vanlineInstance->addBlock($vanlineBlock3);
}

$vanlineBlock4 = Vtiger_Block::getInstance('LBL_VANLINEMANAGER_CONTACTINFORMATION', $vanlineInstance);
if ($vanlineBlock4) {
    echo "<br> Block 'LBL_VANLINEMANAGER_CONTACTINFORMATION' is already present <br>";
} else {
    $vanlineBlock4 = new Vtiger_Block();
    $vanlineBlock4->label = 'LBL_VANLINEMANAGER_CONTACTINFORMATION';
    $vanlineInstance->addBlock($vanlineBlock4);
}

$vanlineField0 = Vtiger_Field::getInstance('vanline_no', $vanlineInstance);
if ($vanlineField0) {
    echo "<br> Field 'vanline_no' is already present <br>";
} else {
    $vanlineField0 = new Vtiger_Field();
    $vanlineField0->label = 'LBL_VANLINEMANAGER_NO';
    $vanlineField0->name = 'vanline_no';
    $vanlineField0->table = 'vtiger_vanlinemanager';
    $vanlineField0->column = 'vanline_no';
    $vanlineField0->columntype = 'VARCHAR(32)';
    $vanlineField0->uitype = 4;
    $vanlineField0->typeofdata = 'V~M';
    $vanlineField0->displaytype = 3;

    $vanlineBlock1->addField($vanlineField0);

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $vanlineInstance->name, 'VNLN', 1);
}

$vanlineField1 = Vtiger_Field::getInstance('vanline_id', $vanlineInstance);
if ($vanlineField1) {
    echo "<br> Field 'vanline_id' is already present <br>";
} else {
    $vanlineField1 = new Vtiger_Field();
    $vanlineField1->label = 'LBL_VANLINEMANAGER_ID';
    $vanlineField1->name = 'vanline_id';
    $vanlineField1->table = 'vtiger_vanlinemanager';
    $vanlineField1->column = 'vanline_id';
    $vanlineField1->columntype = 'VARCHAR(10)';
    $vanlineField1->uitype = 2;
    $vanlineField1->typeofdata = 'V~M';

    $vanlineBlock1->addField($vanlineField1);
}

$vanlineField2 = Vtiger_Field::getInstance('vanline_name', $vanlineInstance);
if ($vanlineField2) {
    echo "<br> Field 'vanline_name' is already present <br>";
} else {
    $vanlineField2 = new Vtiger_Field();
    $vanlineField2->label = 'LBL_VANLINEMANAGER_NAME';
    $vanlineField2->name = 'vanline_name';
    $vanlineField2->table = 'vtiger_vanlinemanager';
    $vanlineField2->column = 'vanline_name';
    $vanlineField2->columntype = 'VARCHAR(255)';
    $vanlineField2->uitype = 2;
    $vanlineField2->typeofdata = 'V~M';

    $vanlineBlock1->addField($vanlineField2);

    $vanlineInstance->setEntityIdentifier($vanlineField2);
}

$vanlineField3 = Vtiger_Field::getInstance('address1', $vanlineInstance);
if ($vanlineField3) {
    echo "<br> Field 'address1' is already present <br>";
} else {
    $vanlineField3 = new Vtiger_Field();
    $vanlineField3->label = 'LBL_VANLINEMANAGER_ADDRESS1';
    $vanlineField3->name = 'address1';
    $vanlineField3->table = 'vtiger_vanlinemanager';
    $vanlineField3->column = 'address1';
    $vanlineField3->columntype = 'VARCHAR(100)';
    $vanlineField3->uitype = 2;
    $vanlineField3->typeofdata = 'V~M';

    $vanlineBlock3->addField($vanlineField3);
}

$vanlineField4 = Vtiger_Field::getInstance('address2', $vanlineInstance);
if ($vanlineField4) {
    echo "<br> Field 'address2' is already present <br>";
} else {
    $vanlineField4 = new Vtiger_Field();
    $vanlineField4->label = 'LBL_VANLINEMANAGER_ADDRESS2';
    $vanlineField4->name = 'address2';
    $vanlineField4->table = 'vtiger_vanlinemanager';
    $vanlineField4->column = 'address2';
    $vanlineField4->columntype = 'VARCHAR(100)';
    $vanlineField4->uitype = 1;
    $vanlineField4->typeofdata = 'V~O';

    $vanlineBlock3->addField($vanlineField4);
}

$vanlineField5 = Vtiger_Field::getInstance('city', $vanlineInstance);
if ($vanlineField5) {
    echo "<br> Field 'city' is already present <br>";
} else {
    $vanlineField5 = new Vtiger_Field();
    $vanlineField5->label = 'LBL_VANLINEMANAGER_CITY';
    $vanlineField5->name = 'city';
    $vanlineField5->table = 'vtiger_vanlinemanager';
    $vanlineField5->column = 'city';
    $vanlineField5->columntype = 'VARCHAR(100)';
    $vanlineField5->uitype = 2;
    $vanlineField5->typeofdata = 'V~M';

    $vanlineBlock3->addField($vanlineField5);
}

$vanlineField6 = Vtiger_Field::getInstance('state', $vanlineInstance);
if ($vanlineField6) {
    echo "<br> Field 'state' is already present <br>";
} else {
    $vanlineField6 = new Vtiger_Field();
    $vanlineField6->label = 'LBL_VANLINEMANAGER_STATE';
    $vanlineField6->name = 'state';
    $vanlineField6->table = 'vtiger_vanlinemanager';
    $vanlineField6->column = 'state';
    $vanlineField6->columntype = 'VARCHAR(100)';
    $vanlineField6->uitype = 2;
    $vanlineField6->typeofdata = 'V~M';

    $vanlineBlock3->addField($vanlineField6);
}

$vanlineFieldZip = Vtiger_Field::getInstance('zip', $vanlineInstance);
if ($vanlineFieldZip) {
    echo "<br> Field 'zip' is already present <br>";
} else {
    $vanlineFieldZip = new Vtiger_Field();
    $vanlineFieldZip->label = 'LBL_VANLINEMANAGER_ZIP';
    $vanlineFieldZip->name = 'zip';
    $vanlineFieldZip->table = 'vtiger_vanlinemanager';
    $vanlineFieldZip->column = 'zip';
    $vanlineFieldZip->columntype = 'VARCHAR(10)';
    $vanlineFieldZip->uitype = 2;
    $vanlineFieldZip->typeofdata = 'V~M';

    $vanlineBlock3->addField($vanlineFieldZip);
}

$vanlineField7 = Vtiger_Field::getInstance('country', $vanlineInstance);
if ($vanlineField7) {
    echo "<br> Field 'country' is already present <br>";
} else {
    $vanlineField7 = new Vtiger_Field();
    $vanlineField7->label = 'LBL_VANLINEMANAGER_COUNTRY';
    $vanlineField7->name = 'country';
    $vanlineField7->table = 'vtiger_vanlinemanager';
    $vanlineField7->column = 'country';
    $vanlineField7->columntype = 'VARCHAR(100)';
    $vanlineField7->uitype = 2;
    $vanlineField7->typeofdata = 'V~M';

    $vanlineBlock3->addField($vanlineField7);
}

$vanlineField8 = Vtiger_Field::getInstance('phone1', $vanlineInstance);
if ($vanlineField8) {
    echo "<br> Field 'phone1' is already present <br>";
} else {
    $vanlineField8 = new Vtiger_Field();
    $vanlineField8->label = 'LBL_VANLINEMANAGER_PHONE1';
    $vanlineField8->name = 'phone1';
    $vanlineField8->table = 'vtiger_vanlinemanager';
    $vanlineField8->column = 'phone1';
    $vanlineField8->columntype = 'VARCHAR(20)';
    $vanlineField8->uitype = 11;
    $vanlineField8->typeofdata = 'V~M';

    $vanlineBlock4->addField($vanlineField8);
}

$vanlineField9 = Vtiger_Field::getInstance('phone2', $vanlineInstance);
if ($vanlineField9) {
    echo "<br> Field 'phone2' is already present <br>";
} else {
    $vanlineField9 = new Vtiger_Field();
    $vanlineField9->label = 'LBL_VANLINEMANAGER_PHONE2';
    $vanlineField9->name = 'phone2';
    $vanlineField9->table = 'vtiger_vanlinemanager';
    $vanlineField9->column = 'phone2';
    $vanlineField9->columntype = 'VARCHAR(20)';
    $vanlineField9->uitype = 11;
    $vanlineField9->typeofdata = 'V~O';

    $vanlineBlock4->addField($vanlineField9);
}

$vanlineField10 = Vtiger_Field::getInstance('fax', $vanlineInstance);
if ($vanlineField10) {
    echo "<br> Field 'fax' is already present <br>";
} else {
    $vanlineField10 = new Vtiger_Field();
    $vanlineField10->label = 'LBL_VANLINEMANAGER_FAX';
    $vanlineField10->name = 'fax';
    $vanlineField10->table = 'vtiger_vanlinemanager';
    $vanlineField10->column = 'fax';
    $vanlineField10->columntype = 'VARCHAR(20)';
    $vanlineField10->uitype = 11;
    $vanlineField10->typeofdata = 'V~O';

    $vanlineBlock4->addField($vanlineField10);
}

$vanlineField11 = Vtiger_Field::getInstance('website', $vanlineInstance);
if ($vanlineField11) {
    echo "<br> Field 'website' is already present <br>";
} else {
    $vanlineField11 = new Vtiger_Field();
    $vanlineField11->label = 'LBL_VANLINEMANAGER_WEBSITE';
    $vanlineField11->name = 'website';
    $vanlineField11->table = 'vtiger_vanlinemanager';
    $vanlineField11->column = 'website';
    $vanlineField11->columntype = 'VARCHAR(255)';
    $vanlineField11->uitype = 1;
    $vanlineField11->typeofdata = 'V~O';

    $vanlineBlock4->addField($vanlineField11);
}

$vanlineField12 = Vtiger_Field::getInstance('CreatedTime', $vanlineInstance);
if ($vanlineField12) {
    echo "<br> Field 'CreatedTime' is already present <br>";
} else {
    $vanlineField12 = new Vtiger_Field();
    $vanlineField12->label = 'Created Time';
    $vanlineField12->name = 'CreatedTime';
    $vanlineField12->table = 'vtiger_crmentity';
    $vanlineField12->column = 'createdtime';
    $vanlineField12->uitype = 70;
    $vanlineField12->typeofdata = 'T~O';
    $vanlineField12->displaytype = 2;

    $vanlineBlock1->addField($vanlineField12);
}

$vanlineField13 = Vtiger_Field::getInstance('ModifiedTime', $vanlineInstance);
if ($vanlineField13) {
    echo "<br> Field 'ModifiedTime' is already present <br>";
} else {
    $vanlineField13 = new Vtiger_Field();
    $vanlineField13->label = 'Modified Time';
    $vanlineField13->name = 'ModifiedTime';
    $vanlineField13->table = 'vtiger_crmentity';
    $vanlineField13->uitype = 70;
    $vanlineField13->typeofdata = 'T~O';
    $vanlineField13->displaytype = 2;

    $vanlineBlock1->addField($vanlineField13);
}

$vanlineField14 = Vtiger_Field::getInstance('assigned_user_id', $vanlineInstance);
if ($vanlineField14) {
    echo "<br> Field 'assigned_user_id' is already present <br>";
} else {
    $vanlineField14 = new Vtiger_Field();
    $vanlineField14->label = 'LBL_VANLINEMANAGER_ASSIGNEDTO';
    $vanlineField14->name = 'assigned_user_id';
    $vanlineField14->table = 'vtiger_crmentity';
    $vanlineField14->uitype = 53;
    $vanlineField14->typeofdata = 'V~M';
    $vanlineField14->displaytype = 1;
    $vanlineField14->quickcreate = 0;
    
    $vanlineBlock1->addField($vanlineField14);
}

if ($vanlineIsNew) {
    $filter0 = new Vtiger_Filter();
    $filter0->name = 'All';
    $filter0->isdefault = true;
    $vanlineInstance->addFilter($filter0);

    $filter0->addField($vanlineField1)->addField($vanlineField2, 1)->addField($vanlineField8, 2)->addField($vanlineField11, 3);
    
    $vanlineInstance->setDefaultSharing();
    $vanlineInstance->initWebservice();

    $menuInstance->addModule($vanlineInstance);
}


echo "<h2>AgentManager Module</h2>";

$agentInstance = Vtiger_Module::getInstance('AgentManager');
if (!$agentInstance) {
    $agentInstance = new Vtiger_Module();
    $agentInstance->name = 'AgentManager';
    $agentInstance->save();

    $agentInstance->initTables();
}

$agentBlock1 = Vtiger_Block::getInstance('LBL_AGENTMANAGER_INFORMATION', $agentInstance);
if ($agentBlock1) {
    echo "<br> Block 'LBL_AGENTMANAGER_INFORMATION' is already present <br>";
} else {
    $agentBlock1 = new Vtiger_Block();
    $agentBlock1->label = 'LBL_AGENTMANAGER_INFORMATION';
    $agentInstance->addBlock($agentBlock1);
}

$agentBlock2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $agentInstance);
if ($agentBlock2) {
    echo "<br> Block 'LBL_CUSTOM_INFORMATION' is already present <br>";
} else {
    $agentBlock2 = new Vtiger_Block();
    $agentBlock2->label = 'LBL_CUSTOM_INFORMATION';
    $agentInstance->addBlock($agentBlock2);
}

$agentBlock3 = Vtiger_Block::getInstance('LBL_AGENTMANAGER_ADDRESSINFORMATION', $agentInstance);
if ($agentBlock3) {
    echo "<br> Block 'LBL_AGENTMANAGER_ADDRESSINFORMATION' is already present <br>";
} else {
    $agentBlock3 = new Vtiger_Block();
    $agentBlock3->label = 'LBL_AGENTMANAGER_ADDRESSINFORMATION';
    $agentInstance->addBlock($agentBlock3);
}

$agentField0 = Vtiger_Field::getInstance('agency_no', $agentInstance);
if ($agentField0) {
    echo "<br> Field 'agency_no' is already present <br>";
} else {
    $agentField0 = new Vtiger_Field();
    $agentField0->label = 'LBL_AGENTMANAGER_NO';
    $agentField0->name = 'agency_no';
    $agentField0->table = 'vtiger_agentmanager';
    $agentField0->column = 'agency_no';
    $agentField0->columntype = 'VARCHAR(32)';
    $agentField0->uitype = 4;
    $agentField0->typeofdata = 'V~M';
    $agentField0->displaytype = 3;

    $agentBlock1->addField($agentField0);

    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $agentInstance->name, 'AGNT', 1);
}

$agentField1 = Vtiger_Field::getInstance('agency_code', $agentInstance);
if ($agentField1) {
    echo "<br> Field 'agency_code' is already present <br>";
} else {
    $agentField1 = new Vtiger_Field();
    $agentField1->label = 'LBL_AGENTMANAGER_CODE';
    $agentField1->name = 'agency_code';
    $agentField1->table = 'vtiger_agentmanager';
    $agentField1->column = 'agency_code';
    $agentField1->columntype = 'VARCHAR(10)';
    $agentField1->uitype = 2;
    $agentField1->typeofdata = 'V~M';

    $agentBlock1->addField($agentField1);
}

$agentField2 = Vtiger_Field::getInstance('agency_name', $agentInstance);
if ($agentField2) {
    echo "<br> Field 'agency_name' is already present <br>";
} else {
    $agentField2 = new Vtiger_Field();
    $agentField2->label = 'LBL_AGENTMANAGER_NAME';
    $agentField2->name = 'agency_name';
    $agentField2->table = 'vtiger_agentmanager';
    $agentField2->column = 'agency_name';
    $agentField2->columntype = 'VARCHAR(255)';
    $agentField2->uitype = 2;
    $agentField2->typeofdata = 'V~M';

    $agentBlock1->addField($agentField2);

    $agentInstance->setEntityIdentifier($agentField2);
}

$agentField3 = Vtiger_Field::getInstance('vanline_id', $agentInstance);
if ($agentField3) {
    echo "<br> Field 'vanline_id' is already present <br>";
} else {
    $agentField3 = new Vtiger_Field();
    $agentField3->label = 'LBL_AGENTMANAGER_VANLINEID';
    $agentField3->name = 'vanline_id';
    $agentField3->table = 'vtiger_agentmanager';
    $agentField3->column = 'vanline_id';
    $agentField3->columntype = 'VARCHAR(255)';
    $agentField3->uitype = 10;
    $agentField3->typeofdata = 'V~O';

    $agentBlock1->addField($agentField3);

    $agentField3->setRelatedModules(array('VanlineManager'));
}

$agentField4 = Vtiger_Field::getInstance('address1', $agentInstance);
if ($agentField4) {
    echo "<br> Field 'address1' is already present <br>";
} else {
    $agentField4 = new Vtiger_Field();
    $agentField4->label = 'LBL_AGENTMANAGER_ADDRESS1';
    $agentField4->name = 'address1';
    $agentField4->table = 'vtiger_agentmanager';
    $agentField4->column = 'address1';
    $agentField4->columntype = 'VARCHAR(100)';
    $agentField4->uitype = 2;
    $agentField4->typeofdata = 'V~M';

    $agentBlock3->addField($agentField4);
}

$agentField5 = Vtiger_Field::getInstance('address2', $agentInstance);
if ($agentField5) {
    echo "<br> Field 'address2' is already present <br>";
} else {
    $agentField5 = new Vtiger_Field();
    $agentField5->label = 'LBL_AGENTMANAGER_ADDRESS2';
    $agentField5->name = 'address2';
    $agentField5->table = 'vtiger_agentmanager';
    $agentField5->column = 'address2';
    $agentField5->columntype = 'VARCHAR(100)';
    $agentField5->uitype = 1;
    $agentField5->typeofdata = 'V~O';

    $agentBlock3->addField($agentField5);
}

$agentField6 = Vtiger_Field::getInstance('city', $agentInstance);
if ($agentField6) {
    echo "<br> Field 'city' is already present <br>";
} else {
    $agentField6 = new Vtiger_Field();
    $agentField6->label = 'LBL_AGENTMANAGER_CITY';
    $agentField6->name = 'city';
    $agentField6->table = 'vtiger_agentmanager';
    $agentField6->column = 'city';
    $agentField6->columntype = 'VARCHAR(100)';
    $agentField6->uitype = 2;
    $agentField6->typeofdata = 'V~M';

    $agentBlock3->addField($agentField6);
}

$agentField7 = Vtiger_Field::getInstance('state', $agentInstance);
if ($agentField7) {
    echo "<br> Field 'state' is already present <br>";
} else {
    $agentField7 = new Vtiger_Field();
    $agentField7->label = 'LBL_AGENTMANAGER_STATE';
    $agentField7->name = 'state';
    $agentField7->table = 'vtiger_agentmanager';
    $agentField7->column = 'state';
    $agentField7->columntype = 'VARCHAR(100)';
    $agentField7->uitype = 2;
    $agentField7->typeofdata = 'V~M';

    $agentBlock3->addField($agentField7);
}

$agentFieldZip = Vtiger_Field::getInstance('zip', $agentInstance);
if ($agentFieldZip) {
    echo "<br> Field 'zip' is already present <br>";
} else {
    $agentFieldZip = new Vtiger_Field();
    $agentFieldZip->label = 'LBL_AGENTMANAGER_ZIP';
    $agentFieldZip->name = 'zip';
    $agentFieldZip->table = 'vtiger_agentmanager';
    $agentFieldZip->column = 'zip';
    $agentFieldZip->columntype = 'VARCHAR(10)';
    $agentFieldZip->uitype = 2;
    $agentFieldZip->typeofdata = 'V~M';

    $agentBlock3->addField($agentFieldZip);
}

$agentField8 = Vtiger_Field::getInstance('country', $agentInstance);
if ($agentField8) {
    echo "<br> Field 'country' is already present <br>";
} else {
    $agentField8 = new Vtiger_Field();
    $agentField8->label = 'LBL_AGENTMANAGER_COUNTRY';
    $agentField8->name = 'country';
    $agentField8->table = 'vtiger_agentmanager';
    $agentField8->column = 'country';
    $agentField8->columntype = 'VARCHAR(100)';
    $agentField8->uitype = 1;
    $agentField8->typeofdata = 'V~O';

    $agentBlock3->addField($agentField8);
}

$agentField9 = Vtiger_Field::getInstance('phone1', $agentInstance);
if ($agentField9) {
    echo "<br> Field 'phone1' is already present <br>";
} else {
    $agentField9 = new Vtiger_Field();
    $agentField9->label = 'LBL_AGENTMANAGER_PHONE1';
    $agentField9->name = 'phone1';
    $agentField9->table = 'vtiger_agentmanager';
    $agentField9->column = 'phone1';
    $agentField9->columntype = 'VARCHAR(20)';
    $agentField9->uitype = 11;
    $agentField9->typeofdata = 'V~M';

    $agentBlock3->addField($agentField9);
}

$agentField10 = Vtiger_Field::getInstance('phone2', $agentInstance);
if ($agentField9) {
    echo "<br> Field 'phone2' is already present <br>";
} else {
    $agentField10 = new Vtiger_Field();
    $agentField10->label = 'LBL_AGENTMANAGER_PHONE2';
    $agentField10->name = 'phone2';
    $agentField10->table = 'vtiger_agentmanager';
    $agentField10->column = 'phone2';
    $agentField10->columntype = 'VARCHAR(20)';
    $agentField10->uitype = 11;
    $agentField10->typeofdata = 'V~O';

    $agentBlock3->addField($agentField10);
}

$agentField11 = Vtiger_Field::getInstance('fax', $agentInstance);
if ($agentField11) {
    echo "<br> Field 'fax' is already present <br>";
} else {
    $agentField11 = new Vtiger_Field();
    $agentField11->label = 'LBL_AGENTMANAGER_FAX';
    $agentField11->name = 'fax';
    $agentField11->table = 'vtiger_agentmanager';
    $agentField11->column = 'fax';
    $agentField11->columntype = 'VARCHAR(20)';
    $agentField11->uitype = 11;
    $agentField11->typeofdata = 'V~O';

    $agentBlock3->addField($agentField11);
}

$agentField12 = Vtiger_Field::getInstance('email', $agentInstance);
if ($agentField12) {
    echo "<br> Field 'email' is already present <br>";
} else {
    $agentField12 = new Vtiger_Field();
    $agentField12->label = 'LBL_AGENTMANAGER_EMAIL';
    $agentField12->name = 'email';
    $agentField12->table = 'vtiger_agentmanager';
    $agentField12->column = 'email';
    $agentField12->columntype = 'VARCHAR(200)';
    $agentField12->uitype = 2;
    $agentField12->typeofdata = 'V~M';

    $agentBlock3->addField($agentField12);
}

$agentField13 = Vtiger_Field::getInstance('CreatedTime', $agentInstance);
if ($agentField13) {
    echo "<br> Field 'CreatedTime' is already present <br>";
} else {
    $agentField13 = new Vtiger_Field();
    $agentField13->label = 'Created Time';
    $agentField13->name = 'CreatedTime';
    $agentField13->table = 'vtiger_crmentity';
    $agentField13->column = 'createdtime';
    $agentField13->uitype = 70;
    $agentField13->typeofdata = 'T~O';
    $agentField13->displaytype = 2;

    $agentBlock1->addField($agentField13);
}

$agentField14 = Vtiger_Field::getInstance('ModifiedTime', $agentInstance);
if ($agentField14) {
    echo "<br> Field 'ModifiedTime' is already present <br>";
} else {
    $agentField14 = new Vtiger_Field();
    $agentField14->label = 'Modified Time';
    $agentField14->name = 'ModifiedTime';
    $agentField14->table = 'vtiger_crmentity';
    $agentField14->uitype = 70;
    $agentField14->typeofdata = 'T~O';
    $agentField14->displaytype = 2;

    $agentBlock1->addField($agentField14);
}

$agentField15 = Vtiger_Field::getInstance('assigned_user_id', $agentInstance);
if ($agentField15) {
    echo "<br> Field 'assigned_user_id' is already present <br>";
} else {
    $agentField15 = new Vtiger_Field();
    $agentField15->label = 'LBL_AGENTMANAGER_ASSIGNEDTO';
    $agentField15->name = 'assigned_user_id';
    $agentField15->table = 'vtiger_crmentity';
    $agentField15->uitype = 53;
    $agentField15->typeofdata = 'V~M';
    $agentField15->displaytype = 1;
    $agentField15->quickcreate = 0;
    
    $agentBlock1->addField($agentField15);
}

if ($agentIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $agentInstance->addFilter($filter1);

    $filter1->addField($agentField1)->addField($agentField2, 1)->addField($agentField3, 2);

    $agentInstance->setDefaultSharing();

    $agentInstance->initWebservice();

    $menuInstance->addModule($agentInstance);

    $vanlineInstance = Vtiger_Module::getInstance('VanlineManager');
    $relationLabel = 'Agents';
    $vanlineInstance->setRelatedList($agentInstance, $relationLabel, array('Add', 'Select'));

    $usersInstance = Vtiger_Module::getInstance('Users');
    $relationLabel = 'Users';
    $agentInstance->setRelatedList($usersInstance, $relationLabel, array('Select'), 'get_users');
}

if (!Vtiger_Utils::CheckTable('vtiger_user2agency')) {
    Vtiger_Utils::CreateTable('vtiger_user2agency',
            '(userid int(19),agency_code int(19))', true);
    echo '<br> vtiger_user2agency table created <br>';
}

if (!Vtiger_Utils::CheckTable('vtiger_tariff2vanline')) {
    Vtiger_Utils::CreateTable('vtiger_tariff2vanline',
            '(vanlineid int(19),tariffid int(19),apply_to_all_agents tinyint(1))', true);
    echo '<br /> vtiger_tariff2vanline table created <br />';
}

if (!Vtiger_Utils::CheckTable('vtiger_tariff2agent')) {
    Vtiger_Utils::CreateTable('vtiger_tariff2agent',
            '(agentid int(19),tariffid int(19))', true);
    echo '<br /> vtiger_tariff2agent table created <br />';
}
