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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

///////////////////////////////////////Tariffs///////////////////////////////////////////////////////////////
$moduleTariffs = Vtiger_Module::getInstance('Tariffs');
if ($moduleTariffs) {
    $blockInstance = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleTariffs);
    if ($blockInstance) {
        echo "<h3>The Record Update Information block already exists</h3><br> \n";
    } else {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'LBL_RECORD_UPDATE_INFORMATION';
        $moduleTariffs->addBlock($blockInstance);
    }
}
//Date Created
$field1 = Vtiger_Field::getInstance('CreatedTime', $moduleTariffs);
if ($field1) {
    echo "<li>The createdtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id, 2, $field1->id));
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TARIFFS_CREATEDTIME';
    $field1->name = 'CreatedTime';
    $field1->table = 'vtiger_crmentity';
    $field1->column = 'CreatedTime';
    $field1->uitype = 70;
    $field1->typeofdata = 'T~O';
    $field1->displaytype = 2;
    $blockInstance->addField($field1);
}
//Date Modified
$field2 = Vtiger_Field::getInstance('ModifiedTime', $moduleTariffs);
if ($field2) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id, 2, $field2->id));
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TARIFFS_MODIFIEDTIME';
    $field2->name = 'ModifiedTime';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'ModifiedTime';
    $field2->uitype = 70;
    $field2->typeofdata = 'T~O';
    $field2->displaytype = 2;
    $blockInstance->addField($field2);
}

//Created By
$field3 = Vtiger_Field::getInstance('createdby', $moduleTariffs);
if ($field3) {
    echo "<li>The createdby field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id, 2, $field3->id));
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TARIFFS_CREATEDBY';
    $field3->name = 'createdby';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smcreatorid';
    $field3->uitype = 52;
    $field3->typeofdata = 'V~O';
    $field3->displaytype = 2;

    $blockInstance->addField($field3);
}
//Assigned To
$field4 = Vtiger_Field::getInstance('assigned_user_id', $moduleTariffs);
if ($field4) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id, 2, $field4->id));
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TARIFFS_ASSIGNED_TO';
    $field4->name = 'assigned_user_id';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'smownerid';
    $field4->uitype = 53;
    $field4->typeofdata = 'V~M';
    $field4->displaytype = 2;

    $blockInstance->addField($field4);
}

//sequence
$blockInstanceInfo = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $moduleTariffs);
$fieldsInstance = array(
    1 => 'tariff_name',
    2 => 'tariff_state',
    3 => 'agentid',
);
foreach ($fieldsInstance as $k => $val) {
    $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleTariffs->id, $blockInstanceInfo->id));
}

//Remove tariff_type field
$adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `fieldname`=? AND `tabid`=? ", array( 'tariff_type', $moduleTariffs->id));
//Remove admin_access
$adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `fieldname`=? AND `tabid`=? ", array( 'admin_access', $moduleTariffs->id));


///////////////////////////////////////////////EffectiveDates//////////////////////////////////////////////////////
$moduleEffectiveDates = Vtiger_Module::getInstance('EffectiveDates');
if ($moduleEffectiveDates) {
    $blockEffectiveDates = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleEffectiveDates);
    if ($blockEffectiveDates) {
        echo "<h3>The Record Update Information block already exists</h3><br> \n";
    } else {
        $blockEffectiveDates = new Vtiger_Block();
        $blockEffectiveDates->label = 'LBL_RECORD_UPDATE_INFORMATION';
        $moduleEffectiveDates->addBlock($blockEffectiveDates);
    }
}
//Date Created
$field5 = Vtiger_Field::getInstance('CreatedTime', $moduleEffectiveDates);
if ($field5) {
    echo "<li>The createdtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockEffectiveDates->id, 2, $field5->id));
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_TARIFFS_CREATEDTIME';
    $field5->name = 'CreatedTime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'CreatedTime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;
    $blockEffectiveDates->addField($field5);
}
//Date Modified
$field6 = Vtiger_Field::getInstance('ModifiedTime', $moduleEffectiveDates);
if ($field6) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockEffectiveDates->id, 2, $field6->id));
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_TARIFFS_MODIFIEDTIME';
    $field6->name = 'ModifiedTime';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'ModifiedTime';
    $field6->uitype = 70;
    $field6->typeofdata = 'T~O';
    $field6->displaytype = 2;
    $blockEffectiveDates->addField($field6);
}

//Created By
$field7 = Vtiger_Field::getInstance('createdby', $moduleEffectiveDates);
if ($field7) {
    echo "<li>The createdby field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockEffectiveDates->id, 2, $field7->id));
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_TARIFFS_CREATEDBY';
    $field7->name = 'createdby';
    $field7->table = 'vtiger_crmentity';
    $field7->column = 'smcreatorid';
    $field7->uitype = 52;
    $field7->typeofdata = 'V~O';
    $field7->displaytype = 2;

    $blockEffectiveDates->addField($field7);
}
//Assigned To
$field8 = Vtiger_Field::getInstance('assigned_user_id', $moduleEffectiveDates);
if ($field8) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockEffectiveDates->id, 2, $field8->id));
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_TARIFFS_ASSIGNED_TO';
    $field8->name = 'assigned_user_id';
    $field8->table = 'vtiger_crmentity';
    $field8->column = 'smownerid';
    $field8->uitype = 53;
    $field8->typeofdata = 'V~M';
    $field8->displaytype = 2;

    $blockEffectiveDates->addField($field8);
}

//Owner Field
$blockEffectiveDatesInfo = Vtiger_Block::getInstance('LBL_EFFECTIVEDATES_INFORMATION', $moduleEffectiveDates);
$fieldOwnerEffectiveDates = Vtiger_Field::getInstance('agentid', $moduleEffectiveDates);
if ($fieldOwnerEffectiveDates) {
    echo "<br> The agentid field already exists <br>";
} else {
    $fieldOwnerEffectiveDates = new Vtiger_Field();
    $fieldOwnerEffectiveDates->label = 'Owner';
    $fieldOwnerEffectiveDates->name = 'agentid';
    $fieldOwnerEffectiveDates->table = 'vtiger_crmentity';
    $fieldOwnerEffectiveDates->column = 'agentid';
    $fieldOwnerEffectiveDates->columntype = 'INT(10)';
    $fieldOwnerEffectiveDates->uitype = 1002;
    $fieldOwnerEffectiveDates->typeofdata = 'I~M';
    $fieldOwnerEffectiveDates->quickcreate = 0;

    $blockEffectiveDatesInfo->addField($fieldOwnerEffectiveDates);
}
//sequence
$fieldsEffectiveDates = array(
    1 => 'effective_date',
    2 => 'related_tariff',
    3 => 'agentid',
);
foreach ($fieldsEffectiveDates as $k => $val) {
    $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleEffectiveDates->id, $blockEffectiveDatesInfo->id));
}





///////////////////////////////////////////////TariffSections////////////////////////////////////////////////

$moduleTariffSections = Vtiger_Module::getInstance('TariffSections');
if ($moduleTariffSections) {
    $blockTariffSections = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleTariffSections);
    if ($blockTariffSections) {
        echo "<h3>The Record Update Information block already exists</h3><br> \n";
    } else {
        $blockTariffSections = new Vtiger_Block();
        $blockTariffSections->label = 'LBL_RECORD_UPDATE_INFORMATION';
        $moduleTariffSections->addBlock($blockTariffSections);
    }
}
//Date Created
$field9 = Vtiger_Field::getInstance('CreatedTime', $moduleTariffSections);
if ($field9) {
    echo "<li>The createdtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockTariffSections->id, 2, $field9->id));
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_TARIFFS_CREATEDTIME';
    $field9->name = 'CreatedTime';
    $field9->table = 'vtiger_crmentity';
    $field9->column = 'CreatedTime';
    $field9->uitype = 70;
    $field9->typeofdata = 'T~O';
    $field9->displaytype = 2;
    $blockTariffSections->addField($field9);
}
//Date Modified
$field10 = Vtiger_Field::getInstance('ModifiedTime', $moduleTariffSections);
if ($field10) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockTariffSections->id, 2, $field10->id));
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_TARIFFS_MODIFIEDTIME';
    $field10->name = 'ModifiedTime';
    $field10->table = 'vtiger_crmentity';
    $field10->column = 'ModifiedTime';
    $field10->uitype = 70;
    $field10->typeofdata = 'T~O';
    $field10->displaytype = 2;
    $blockTariffSections->addField($field10);
}

//Created By
$field11 = Vtiger_Field::getInstance('createdby', $moduleTariffSections);
if ($field11) {
    echo "<li>The createdby field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockTariffSections->id, 2, $field11->id));
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_TARIFFS_CREATEDBY';
    $field11->name = 'createdby';
    $field11->table = 'vtiger_crmentity';
    $field11->column = 'smcreatorid';
    $field11->uitype = 52;
    $field11->typeofdata = 'V~O';
    $field11->displaytype = 2;

    $blockTariffSections->addField($field11);
}
//Assigned To
$field12 = Vtiger_Field::getInstance('assigned_user_id', $moduleTariffSections);
if ($field12) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockTariffSections->id, 2, $field12->id));
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_TARIFFS_ASSIGNED_TO';
    $field12->name = 'assigned_user_id';
    $field12->table = 'vtiger_crmentity';
    $field12->column = 'smownerid';
    $field12->uitype = 53;
    $field12->typeofdata = 'V~M';
    $field12->displaytype = 2;

    $blockTariffSections->addField($field12);
}

//Owner Field
$blockTariffSectionsInfo = Vtiger_Block::getInstance('LBL_TARIFFSECTIONS_INFORMATION', $moduleTariffSections);
$fieldOwnerTariffSections = Vtiger_Field::getInstance('agentid', $moduleTariffSections);
if ($fieldOwnerTariffSections) {
    echo "<br> The agentid field already exists <br>";
} else {
    $fieldOwnerTariffSections = new Vtiger_Field();
    $fieldOwnerTariffSections->label = 'Owner';
    $fieldOwnerTariffSections->name = 'agentid';
    $fieldOwnerTariffSections->table = 'vtiger_crmentity';
    $fieldOwnerTariffSections->column = 'agentid';
    $fieldOwnerTariffSections->columntype = 'INT(10)';
    $fieldOwnerTariffSections->uitype = 1002;
    $fieldOwnerTariffSections->typeofdata = 'I~M';
    $fieldOwnerTariffSections->quickcreate = 0;

    $blockTariffSectionsInfo->addField($fieldOwnerTariffSections);
}
//sequence
$FieldsTariffSections = array(
    1 => 'section_name',
    2 => 'related_tariff',
    3 => 'agentid',
    4 => 'is_discountable'
);
foreach ($FieldsTariffSections as $k => $val) {
    $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleTariffSections->id, $blockTariffSectionsInfo->id));
}

/////////////////////////////////////////TariffServices/////////////////////////////////////////////////////////////////
$moduleTariffServices = Vtiger_Module::getInstance('TariffServices');
$blockTariffServicesInfo = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_INFORMATION', $moduleTariffServices);
//tariffservices_discountable
$field13 = Vtiger_Field::getInstance('tariffservices_discountable', $moduleTariffServices);
if ($field13) {
    echo "<br> The tariffservices_discountable field already exists<br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_TARIFFSERVICES_DISCOUNTABLE';
    $field13->name = 'tariffservices_discountable';
    $field13->table = 'vtiger_tariffservices';
    $field13->column ='tariffservices_discountable';
    $field13->columntype = 'varchar(3)';
    $field13->uitype = 56;
    $field13->typeofdata = 'C~O';

    $blockTariffServicesInfo->addField($field13);
}

//Owner Field
$fieldOwnerTariffServices = Vtiger_Field::getInstance('agentid', $moduleTariffServices);
if ($fieldOwnerTariffServices) {
    echo "<br> The agentid field already exists <br>";
} else {
    $fieldOwnerTariffServices = new Vtiger_Field();
    $fieldOwnerTariffServices->label = 'Owner';
    $fieldOwnerTariffServices->name = 'agentid';
    $fieldOwnerTariffServices->table = 'vtiger_crmentity';
    $fieldOwnerTariffServices->column = 'agentid';
    $fieldOwnerTariffServices->columntype = 'INT(10)';
    $fieldOwnerTariffServices->uitype = 1002;
    $fieldOwnerTariffServices->typeofdata = 'I~M';
    $fieldOwnerTariffServices->quickcreate = 0;

    $blockTariffServicesInfo->addField($fieldOwnerTariffServices);
}
//Remove invoiceable field
$adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `fieldname`=? AND `tabid`=? ", array( 'invoiceable', $moduleTariffServices->id));
// Remove distributable field
$adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `fieldname`=? AND `tabid`=? ", array( 'distributable', $moduleTariffServices->id));
//sequence
$Fields = array(
    1 => 'service_name',
    2 => 'tariff_section',
    3 => 'rate_type',
    4 => 'applicability',
    5 => 'related_itemcodes',
    6 => 'is_required',
    7 => 'tariffservices_discountable',
    8 => 'agentid',
    9 => 'assigned_user_id',
    10 => 'tariffservices_assigntomodule',
    11 => 'tariffservices_assigntorecord',
    12 => 'related_tariff',
);
foreach ($Fields as $k => $val) {
    $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleTariffServices->id, $blockTariffServicesInfo->id));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";