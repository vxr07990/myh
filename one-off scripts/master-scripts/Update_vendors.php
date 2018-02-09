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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');
//include_once('modules/ModComments/ModComments.php');

$module6 = Vtiger_Module::getInstance('Vendors');
$block1 = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $module6);
$block2 = Vtiger_Block::getInstance('LBL_VENDOR_ADDRESS_INFORMATION', $module6);
$block3 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module6);
$block4= Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $module6);
$field01 = Vtiger_Field::getInstance('street', $module6);
$field02 = Vtiger_Field::getInstance('pobox', $module6);
$field03 = Vtiger_Field::getInstance('city', $module6);
$field04 = Vtiger_Field::getInstance('state', $module6);
$field05 = Vtiger_Field::getInstance('postalcode', $module6);
$field06 = Vtiger_Field::getInstance('country', $module6);
$field07 = Vtiger_Field::getInstance('category', $module6);
$field08 = Vtiger_Field::getInstance('description', $module6);
$field09 = Vtiger_Field::getInstance('glacct', $module6);
$field010 = Vtiger_Field::getInstance('website', $module6);
$field011 = Vtiger_Field::getInstance('createdtime', $module6);
$field012 = Vtiger_Field::getInstance('modifiedtime', $module6);
$field013 = Vtiger_Field::getInstance('phone', $module6);
$field014 = Vtiger_Field::getInstance('email', $module6);

$field12 = Vtiger_Field::getInstance('claimitems_type', $module6);
if ($field12) {
    echo "<li>The claimitems_type field already exists</li><br> \n";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_VENDORS_STATUS';
    $field12->name = 'vendor_status';    // Must be the same as column.
    $field12->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field12->column = 'vendor_status'; //  This will be the columnname in your database for the new field.
    $field12->columntype = 'VARCHAR(100)';
    $field12->uitype = 16;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field12->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field12->summaryfield = 1;

    $block1->addField($field12);        // Use only if this field is being added to relate to another module.
    $field12->setPicklistValues(array('Active', 'Inactive'));
}

$field13 = Vtiger_Field::getInstance('origin_address1', $module6);
if ($field13) {
    echo "<li>The origin_address1 field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_VENDORS_OADDRESS1';
    $field13->name = 'origin_address1';    // Must be the same as column.
    $field13->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field13->column = 'origin_address1'; //  This will be the columnname in your database for the new field.
    $field13->columntype = 'VARCHAR(100)';
    $field13->uitype = 1;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field13->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field13);        // Use only if this field is being added to relate to another module.
}

$field14 = Vtiger_Field::getInstance('origin_address2', $module6);
if ($field14) {
    echo "<li>The origin_address2 field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_VENDORS_OADDRESS2';
    $field14->name = 'origin_address2';    // Must be the same as column.
    $field14->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field14->column = 'origin_address2'; //  This will be the columnname in your database for the new field.
    $field14->columntype = 'VARCHAR(100)';
    $field14->uitype = 1;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field14->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field14);        // Use only if this field is being added to relate to another module.
}

$field15 = Vtiger_Field::getInstance('origin_city', $module6);
if ($field15) {
    echo "<li>The origin_city field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_VENDORS_OCITY';
    $field15->name = 'origin_city';    // Must be the same as column.
    $field15->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field15->column = 'origin_city'; //  This will be the columnname in your database for the new field.
    $field15->columntype = 'VARCHAR(100)';
    $field15->uitype = 1;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field15->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field15);        // Use only if this field is being added to relate to another module.
}

$field16 = Vtiger_Field::getInstance('origin_state', $module6);
if ($field16) {
    echo "<li>The origin_state field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_VENDORS_OSTATE';
    $field16->name = 'origin_state';    // Must be the same as column.
    $field16->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field16->column = 'origin_state'; //  This will be the columnname in your database for the new field.
    $field16->columntype = 'VARCHAR(100)';
    $field16->uitype = 1;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field16->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field16);        // Use only if this field is being added to relate to another module.
}

$field17 = Vtiger_Field::getInstance('origin_zip', $module6);
if ($field17) {
    echo "<li>The origin_zip field already exists</li><br> \n";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_VENDORS_OZIP';
    $field17->name = 'origin_zip';    // Must be the same as column.
    $field17->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field17->column = 'origin_zip'; //  This will be the columnname in your database for the new field.
    $field17->columntype = 'VARCHAR(100)';
    $field17->uitype = 1;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field17->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field17);        // Use only if this field is being added to relate to another module.
}

$field18 = Vtiger_Field::getInstance('origin_country', $module6);
if ($field18) {
    echo "<li>The origin_country field already exists</li><br> \n";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_VENDORS_OCOUNTRY';
    $field18->name = 'origin_country';    // Must be the same as column.
    $field18->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field18->column = 'origin_country'; //  This will be the columnname in your database for the new field.
    $field18->columntype = 'VARCHAR(100)';
    $field18->uitype = 1;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field18->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field18);        // Use only if this field is being added to relate to another module.
}

$field19 = Vtiger_Field::getInstance('phone2', $module6);
if ($field19) {
    echo "<li>The phone2 field already exists</li><br> \n";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_VENDORS_PHONE2';
    $field19->name = 'phone2';    // Must be the same as column.
    $field19->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field19->column = 'phone2'; //  This will be the columnname in your database for the new field.
    $field19->columntype = 'VARCHAR(100)';
    $field19->uitype = 11;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field19->typeofdata = 'V~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field19);        // Use only if this field is being added to relate to another module.
}

$field20 = Vtiger_Field::getInstance('email2', $module6);
if ($field20) {
    echo "<li>The email2 field already exists</li><br> \n";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_VENDORS_EMAIL2';
    $field20->name = 'email2';    // Must be the same as column.
    $field20->table = 'vtiger_vendor';    // This is the tablename from your database that the new field will be added to.
    $field20->column = 'email2'; //  This will be the columnname in your database for the new field.
    $field20->columntype = 'VARCHAR(100)';
    $field20->uitype = 13;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field20->typeofdata = 'E~O';        // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field20);        // Use only if this field is being added to relate to another module.
}
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', presence = 1'. ' WHERE fieldid = ' . $field01->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', presence = 1'. ' WHERE fieldid = ' . $field02->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', presence = 1'. ' WHERE fieldid = ' . $field03->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', presence = 1'. ' WHERE fieldid = ' . $field04->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', presence = 1'. ' WHERE fieldid = ' . $field05->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', presence = 1'. ' WHERE fieldid = ' . $field06->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', presence = 1'. ' WHERE fieldid = ' . $field07->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', presence = 1'. ' WHERE fieldid = ' . $field08->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', presence = 1'. ' WHERE fieldid = ' . $field09->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 10'. ' WHERE fieldid = ' . $field010->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 1'. ' WHERE fieldid = ' . $field011->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 2'. ' WHERE fieldid = ' . $field012->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 1'. ' WHERE fieldid = ' . $field013->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 2'. ' WHERE fieldid = ' . $field014->id);

    ModTracker::enableTrackingForModule($module6->id);
    //Set related list in Documents
    $module6 = Vtiger_Module::getInstance('Vendors');

    $module6->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('ADD'), 'get_attachments');

    //require_once 'vtlib/Vtiger/Module.php';
    $commentsModule = Vtiger_Module::getInstance('ModComments');
    $fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
    $fieldInstance->setRelatedModules(array('Vendors'));

    //require_once 'modules/ModComments/ModComments.php';
    $detailviewblock = ModComments::addWidgetTo('Vendors');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";