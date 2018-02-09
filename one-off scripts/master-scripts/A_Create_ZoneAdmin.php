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



// A_Create_ZoneAdmin.php

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$zoneAdminIsNew = false;
$moduleInstance = Vtiger_Module::getInstance('ZoneAdmin');
if ($moduleInstance) {
    echo "<h2> Updating fields for zone admin </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ZoneAdmin';
    $moduleInstance->save();
    echo "<h2> creating zone admin module and updating fields </h2><Br>";
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_ZONEADMIN_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3> LBL_ZONEADMIN_INFORMATION block already exists </h3><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_ZONEADMIN_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
    $zoneAdminIsNew = true;
}
echo "<ul>";
$field3= Vtiger_Field::getInstance('za_zone', $moduleInstance); //Zone Name
if ($field3) {
    echo "<li> the za_zone field already exists </li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ORIGIN_ZONE';
    $field3->name = 'za_zone';
    $field3->table = 'vtiger_zoneadmin';
    $field3->column = 'za_zone';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 2;
    $field3->typeofdata = 'V~O';

    $blockInstance->addField($field3);
}
$field1 = Vtiger_Field::getInstance('zoneadmin_id', $moduleInstance); //Zone ID
if ($field1) {
    echo "<li> the zoneadmin_id field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ZONEADMIN_ID';
    $field1->name = 'zoneadmin_id';
    $field1->table = 'vtiger_zoneadmin';
    $field1->column = 'zoneadmin_id';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 4;
    $field1->typeofdata = 'V~M';

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}
$field4= Vtiger_Field::getInstance('zip_code', $moduleInstance); //ZIP Codes
if ($field4) {
    echo "<li> the zip_code field already exists </li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ZIP_CODE';
    $field4->name = 'zip_code';
    $field4->table = 'vtiger_zoneadmin';
    $field4->column = 'zip_code';
    $field4->columntype = 'VARCHAR(100)';
    $field4->uitype = 19;
    $field4->typeofdata = 'V~O';

    $blockInstance->addField($field4);
}
$field2 = Vtiger_Field::getInstance('za_state', $moduleInstance); //States
if ($field2) {
    echo "<li> the za_state field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_STATE';
    $field2->name = 'za_state';
    $field2->table = 'vtiger_zoneadmin';
    $field2->column = 'za_state';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 33;
    $field2->typeofdata = 'V~O';

    $blockInstance->addField($field2);
    $field2->setPicklistValues(array('AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY', 'AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'NT', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT'));
}
$field36=Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);//Assigned To
if ($field36) {
    echo "<li> assigned_user_id field alreadyexist exists </li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'Assigned To';
    $field36->name = 'assigned_user_id';
    $field36->table = 'vtiger_crmentity';
    $field36->column = 'smownerid';
    $field36->uitype = 53;
    $field36->typeofdata = 'V~M';

    $blockInstance->addField($field36);
}
$field37= Vtiger_Field::getInstance('createdtime', $moduleInstance);//Created Time
if ($field37) {
    echo "<li> createdtime field already exists </li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'createdtime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $blockInstance->addField($field37);
}
$field38 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance); //Modified Time
if ($field38) {
    echo "<lil> modifiedtime field already exists </li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'modifiedtime';
    $field38->table = 'vtiger_crmentity';
    $field38->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $blockInstance->addField($field38);
}
echo "</ul>";
$blockInstance->save($moduleInstance);

$blockInstance2= Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<h3> LBL_CUSTOM_INFORMATION block already exists </h3><br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

if ($zoneAdminIsNew) {
    echo "ZoneAdmin is New<br>";
    ModTracker::enableTrackingForModule($moduleInstance->id);
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();


    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";