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


// $Vtiger_Utils_Log = true;
// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// $tariffIsNew = false;  //flag for filters at the end
$tariffSectionsIsNew = false;
$effectiveDatesIsNew = false;
$tariffServicesIsNew = false;

//Start Tariffs Module
$module1 = Vtiger_Module::getInstance('Tariffs');
if ($module1) {
    echo "<h2>Updating Tariffs Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Tariffs';
    $module1->save();
    echo "<h2>Creating Module Tariffs and Updating Fields</h2><br>";
    $module1->initTables();
    ModTracker::enableTrackingForModule($module1->id);
    //add Local Tariffs to Menu, menu doesn't seem to work correctly and throws a fatal error
    //$menu = Vtiger_Menu::getInstance('COMPANY_ADMIN_TAB');
    //$menu->addModule($module1);
}

//start block1 : LBL_TARIFFS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_TARIFFS_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_TARIFFS_INFORMATION';
    $module1->addBlock($block1);
    $tariffIsNew = true;
}
echo "<ul>";
//start block1 fields
$field1 = Vtiger_Field::getInstance('tariff_name', $module1);
if ($field1) {
    echo "<li>The tariff_name field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'Tariff Name';
    $field1->name = 'tariff_name';
    $field1->table = 'vtiger_tariffs';
    $field1->column = 'tariff_name';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';

    $block1->addField($field1);
        
    $module1->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('tariff_state', $module1);
if ($field2) {
    echo "<li>The tariff_state field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'Tariff State';
    $field2->name = 'tariff_state';
    $field2->table = 'vtiger_tariffs';
    $field2->column = 'tariff_state';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 2;
    $field2->typeofdata = 'V~M';

    $block1->addField($field2);
}
// This field is unnecessary the assigned to field has the same functionality. Disable field if it exists
 $field3 = Vtiger_Field::getInstance('related_agent', $module1);
if ($field3) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence=1 WHERE fieldid=".$field3->id);
    // echo "<li>The related_agent field already exists</li><br> \n";
}
// else {
    // $field3 = new Vtiger_Field();
    // $field3->label = 'Agent';
    // $field3->name = 'related_agent';
    // $field3->table = 'vtiger_tariffs';
    // $field3->column = 'related_agent';
    // $field3->columntype = 'INT(19)';
    // $field3->uitype = 10;
    // $field3->typeofdata = 'V~M';

    // $block1->addField($field3);

    // $field3->setRelatedModules(Array('Agents'));
// }

//We aren't sure if this needs to exist -ACS 20150513
// $field4 = Vtiger_Field::getInstance('commodity_type',$module1);
// if ($field4) {
    // echo "<li>The commodity_type field already exists</li><br> \n";
// }
// else {
    // $field4 = new Vtiger_Field();
    // $field4->label = 'Commodity';
    // $field4->name = 'commodity_type';
    // $field4->table = 'vtiger_tariffs';
    // $field4->column = 'commodity_type';
    // $field4->columntype = 'VARCHAR(255)';
    // $field4->uitype = 16;
    // $field4->typeofdata = 'V~M';

    // $block1->addField($field4);

    // $field4->setPicklistValues( Array ('HHG', 'Comm. Goods', 'Truckload') );
// }

$field5 = Vtiger_Field::getInstance('assigned_user_id', $module1);
if ($field5) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'Assigned To';
    $field5->name = 'assigned_user_id';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'smownerid';
    $field5->uitype = 53;
    $field5->typeofdata = 'V~M';

    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('CreatedTime', $module1);
if ($field6) {
    echo "<li>The CreatedTime field already exists</li><br> \n";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'Created Time';
    $field6->name = 'CreatedTime';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'createdtime';
    $field6->uitype = 70;
    $field6->typeofdata = 'T~O';
    $field6->displaytype = 2;

    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('ModifiedTime', $module1);
if ($field7) {
    echo "<li>The ModifiedTime field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'Modified Time';
    $field7->name = 'ModifiedTime';
    $field7->table = 'vtiger_crmentity';
    $field7->column = 'modifiedtime';
    $field7->uitype = 70;
    $field7->typeofdata = 'T~O';
    $field7->displaytype = 2;

    $block1->addField($field7);
}
//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1 : LBL_TARIFFS_INFORMATION

//start block2 : LBL_CUSTOM_INFORMATION
$block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module1);
if ($block2) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_CUSTOM_INFORMATION';
    $module1->addBlock($block2);
}
$block2->save($module1);
//end block2 : LBL_CUSTOM_INFORMATION

if ($tariffIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field5, 2)->addField($field6, 3);

    $module1->setDefaultSharing();

    $module1->initWebservice();
}
//End Tariffs Module

//Start TariffSections Module2
$module2 = Vtiger_Module::getInstance('TariffSections');
if ($module2) {
    echo "<h2>Updating TariffSections Fields</h2><br>";
} else {
    $module2 = new Vtiger_Module();
    $module2->name = 'TariffSections';
    $module2->save();
    echo "<h2>Creating Module TariffSections and Updating Fields</h2><br>";
    $module2->initTables();
}

//start block3 : LBL_TARIFFSECTIONS_INFORMATION
$block3 = Vtiger_Block::getInstance('LBL_TARIFFSECTIONS_INFORMATION', $module2);
if ($block3) {
    echo "<h3>The LBL_TARIFFSECTIONS_INFORMATION block already exists</h3><br> \n";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_TARIFFSECTIONS_INFORMATION';
    $module2->addBlock($block3);
    $tariffSectionsIsNew = true;
}
echo "<ul>";

//start block3 fields
$field8 = Vtiger_Field::getInstance('section_name', $module2);
if ($field8) {
    echo "<li>The section_name field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_TARIFFSECTIONS_NAME';
    $field8->name = 'section_name';
    $field8->table = 'vtiger_tariffsections';
    $field8->column = 'section_name';
    $field8->columntype = 'VARCHAR(255)';
    $field8->uitype = 2;
    $field8->typeofdata = 'V~M';

    $block3->addField($field8);

    $module2->setEntityIdentifier($field8);
}
$field9 = Vtiger_Field::getInstance('related_tariff', $module2);
if ($field9) {
    echo "<li>The related_tariff field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_TARIFFSECTIONS_TARIFF';
    $field9->name = 'related_tariff';
    $field9->table = 'vtiger_tariffsections';
    $field9->column = 'related_tariff';
    $field9->columntype = 'INT(19)';
    $field9->uitype = 10;
    $field9->typeofdata = 'V~M';

    $block3->addField($field9);

    $field9->setRelatedModules(array('Tariffs'));
}
$field10 = Vtiger_Field::getInstance('assigned_user_id', $module2);
if ($field10) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'Assigned To';
    $field10->name = 'assigned_user_id';
    $field10->table = 'vtiger_crmentity';
    $field10->column = 'smownerid';
    $field10->uitype = 53;
    $field10->typeofdata = 'V~M';

    $block3->addField($field10);
}
$field11 = Vtiger_Field::getInstance('CreatedTime', $module2);
if ($field11) {
    echo "<li>The CreatedTime field already exists</li><br> \n";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'Created Time';
    $field11->name = 'CreatedTime';
    $field11->table = 'vtiger_crmentity';
    $field11->column = 'createdtime';
    $field11->uitype = 70;
    $field11->typeofdata = 'T~O';
    $field11->displaytype = 2;

    $block3->addField($field11);
}
$field12 = Vtiger_Field::getInstance('ModifiedTime', $module2);
if ($field12) {
    echo "<li>The ModifiedTime field already exists</li><br> \n";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'Modified Time';
    $field12->name = 'ModifiedTime';
    $field12->table = 'vtiger_crmentity';
    $field12->column = 'modifiedtime';
    $field12->uitype = 70;
    $field12->typeofdata = 'T~O';
    $field12->displaytype = 2;

    $block3->addField($field12);
}
//end block3 fields
echo "</ul>";
$block3->save($module2);
//end block3 : LBL_TARIFFSECTIONS_INFORMATION

//start block4 : LBL_CUSTOM_INFORMATION
$block4 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module2);
if ($block4) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_CUSTOM_INFORMATION';
    $module2->addBlock($block4);
}
$block4->save($module2);
//end block4 : LBL_CUSTOM_INFORMATION

if ($tariffSectionsIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module2->addFilter($filter1);

    $filter1->addField($field8)->addField($field9, 1)->addField($field10, 2);

    $module2->setDefaultSharing();

    $module2->initWebservice();

    $tariffInstance = Vtiger_Module::getInstance('Tariffs');
    $relationLabel = 'Tariff Sections';
    $tariffInstance->setRelatedList($module2, $relationLabel, array('Add'));
}
//End TariffSections Module2

//Start EffectiveDates Module3

$module3 = Vtiger_Module::getInstance('EffectiveDates');
if ($module3) {
    echo "<h2>Updating EffectiveDates Fields</h2><br>";
} else {
    $module3 = new Vtiger_Module();
    $module3->name = 'EffectiveDates';
    $module3->save();
    echo "<h2>Creating Module EffectiveDates and Updating Fields</h2><br>";
    $module3->initTables();
}

//start block5 : LBL_EFFECTIVEDATES_INFORMATION
$block5 = Vtiger_Block::getInstance('LBL_EFFECTIVEDATES_INFORMATION', $module3);
if ($block5) {
    echo "<h3>The LBL_EFFECTIVEDATES_INFORMATION block already exists</h3><br> \n";
} else {
    $block5 = new Vtiger_Block();
    $block5->label = 'LBL_EFFECTIVEDATES_INFORMATION';
    $module3->addBlock($block5);
    $effectiveDatesIsNew = true;
}
echo "<ul>";
//start block5 fields
$field13 = Vtiger_Field::getInstance('effective_date', $module3);
if ($field13) {
    echo "<li>The effective_date field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'Effective Date';
    $field13->name = 'effective_date';
    $field13->table = 'vtiger_effectivedates';
    $field13->column = 'effective_date';
    $field13->columntype = 'DATE';
    $field13->uitype = 5;
    $field13->typeofdata = 'D~M';

    $block5->addField($field13);
        
    $module3->setEntityIdentifier($field13);
}
$field14 = Vtiger_Field::getInstance('related_tariff', $module3);
if ($field14) {
    echo "<li>The related_tariff field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'Tariff';
    $field14->name = 'related_tariff';
    $field14->table = 'vtiger_effectivedates';
    $field14->column = 'related_tariff';
    $field14->columntype = 'INT(19)';
    $field14->uitype = 10;
    $field14->typeofdata = 'V~M';

    $block5->addField($field14);

    $field14->setRelatedModules(array('Tariffs'));
}
$field15 = Vtiger_Field::getInstance('assigned_user_id', $module3);
if ($field15) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'Assigned To';
    $field15->name = 'assigned_user_id';
    $field15->table = 'vtiger_crmentity';
    $field15->column = 'smownerid';
    $field15->uitype = 53;
    $field15->typeofdata = 'V~M';

    $block5->addField($field15);
}
$field16 = Vtiger_Field::getInstance('CreatedTime', $module3);
if ($field16) {
    echo "<li>The CreatedTime field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'Created Time';
    $field16->name = 'CreatedTime';
    $field16->table = 'vtiger_crmentity';
    $field16->column = 'createdtime';
    $field16->uitype = 70;
    $field16->typeofdata = 'T~O';
    $field16->displaytype = 2;

    $block5->addField($field16);
}
$field17 = Vtiger_Field::getInstance('ModifiedTime', $module3);
if ($field17) {
    echo "<li>The ModifiedTime field already exists</li><br> \n";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'Modified Time';
    $field17->name = 'ModifiedTime';
    $field17->table = 'vtiger_crmentity';
    $field17->column = 'modifiedtime';
    $field17->uitype = 70;
    $field17->typeofdata = 'T~O';
    $field17->displaytype = 2;

    $block5->addField($field17);
}
//end block5 fields

echo "</ul>";
$block5->save($module3);
//end block5 : LBL_EFFECTIVEDATES_INFORMATION

//start block6 : LBL_CUSTOM_INFORMATION
$block6 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module3);
if ($block6) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $block6 = new Vtiger_Block();
    $block6->label = 'LBL_CUSTOM_INFORMATION';
    $module3->addBlock($block6);
}
//end block6 : LBL_CUSTOM_INFORMATION

if ($effectiveDatesIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module3->addFilter($filter1);

    $filter1->addField($field13)->addField($field14, 1)->addField($field15, 2);

    $module3->setDefaultSharing();

    $module3->initWebservice();

    $tariffInstance = Vtiger_Module::getInstance('Tariffs');
    $relationLabel = 'Effective Dates';
    $tariffInstance->setRelatedList($module3, $relationLabel, array('Add'));
}
//End EffectiveDates Module3

//Start TariffServices Module4
//$tariffServicesIsNew = false;  //flag for filters at the end

$module4 = Vtiger_Module::getInstance('TariffServices');
if ($module4) {
    echo "<h2>Updating TariffServices Fields</h2><br>";
} else {
    $module4 = new Vtiger_Module();
    $module4->name = 'TariffServices';
    $module4->save();
    echo "<h2>Creating Module TariffServices and Updating Fields</h2><br>";
    $module4->initTables();
    $module4->setDefaultSharing();
    $module4->initWebservice();
}

//start block7 : LBL_TARIFFSERVICES_INFORMATION
$block7 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_INFORMATION', $module4);
if ($block7) {
    echo "<h3>The LBL_TARIFFSERVICES_INFORMATION block already exists</h3><br> \n";
} else {
    $block7 = new Vtiger_Block();
    $block7->label = 'LBL_TARIFFSERVICES_INFORMATION';
    $module4->addBlock($block7);
    $tariffServicesIsNew = true;
}

echo "<ul>";
//start block7 fields
$field18 = Vtiger_Field::getInstance('service_name', $module4);
if ($field18) {
    echo "<li>The service_name field already exists</li><br> \n";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_TARIFFSERVICES_NAME';
    $field18->name = 'service_name';
    $field18->table = 'vtiger_tariffservices';
    $field18->column = 'service_name';
    $field18->columntype = 'VARCHAR(100)';
    $field18->uitype = 2;
    $field18->typeofdata = 'V~M';

    $block7->addField($field18);

    $module4->setEntityIdentifier($field18);
}

$field19 = Vtiger_Field::getInstance('tariff_section', $module4);
if ($field19) {
    echo "<li>The tariff_section field already exists</li><br> \n";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_TARIFFSERVICES_RELATEDSECTION';
    $field19->name = 'tariff_section';
    $field19->table = 'vtiger_tariffservices';
    $field19->column = 'tariff_section';
    $field19->columntype = 'INT(19)';
    $field19->uitype = 10;
    $field19->typeofdata = 'V~M';

    $block7->addField($field19);

    $field19->setRelatedModules(array('TariffSections'));
}
$field20 = Vtiger_Field::getInstance('effective_date', $module4);
if ($field20) {
    echo "<li>The effective_date field already exists</li><br> \n";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_TARIFFSERVICES_EFFECTIVEDATE';
    $field20->name = 'effective_date';
    $field20->table = 'vtiger_tariffservices';
    $field20->column = 'effective_date';
    $field20->columntype = 'INT(19)';
    $field20->uitype = 10;
    $field20->typeofdata = 'D~M';

    $block7->addField($field20);

    $field20->setRelatedModules(array('EffectiveDates'));
}

$field21 = Vtiger_Field::getInstance('related_tariff', $module4);
if ($field21) {
    echo "<li>The related_tariff field already exists</li><br> \n";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_TARIFFSERVICES_RELATEDTARIFF';
    $field21->name = 'related_tariff';
    $field21->table = 'vtiger_tariffservices';
    $field21->column = 'related_tariff';
    $field21->columntype = 'INT(19)';
    $field21->uitype = 10;
    $field21->typeofdata = 'V~M';

    $block7->addField($field21);

    $field21->setRelatedModules(array('Tariffs'));
}

$field22 = Vtiger_Field::getInstance('rate_type', $module4);
if ($field22) {
    echo "<li>The rate_type field already exists</li><br> \n";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_TARIFFSERVICES_RATETYPE';
    $field22->name = 'rate_type';
    $field22->table = 'vtiger_tariffservices';
    $field22->column = 'rate_type';
    $field22->columntype = 'VARCHAR(255)';
    $field22->uitype = 16;
    $field22->typeofdata = 'V~M';

    $block7->addField($field22);

    $field22->setPicklistValues(array('Base Plus Trans.', 'Break Point Trans.', 'Weight/Mileage Trans.', 'Bulky List', 'Charge Per $100 (Valuation)', 'County Charge', 'Crating Item', 'Flat Charge', 'Hourly Avg Lb/Man/Hour', 'Hourly Set', 'Hourly Simple', 'Packing Items', 'Per Cu Ft/Per Day', 'Per Cu Ft/Per Month', 'Per CWT', 'Per CWT/Per Day', 'Per CWT/Per Month', 'Per Quantity', 'Per Quantity/Per Day', 'Per Quantity/Per Month', 'Tabled Valuation'));
}

$field23 = Vtiger_Field::getInstance('applicability', $module4);
if ($field23) {
    echo "<li>The applicability field already exists</li><br> \n";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_TARIFFSERVICES_APPLICABILITY';
    $field23->name = 'applicability';
    $field23->table = 'vtiger_tariffservices';
    $field23->column = 'applicability';
    $field23->columntype = 'VARCHAR(255)';
    $field23->uitype = 16;
    $field23->typeofdata = 'V~M';

    $block7->addField($field23);

    $field23->setPicklistValues(array('All Locations', 'Origin Destination Only', 'Shipment Level'));
}
$field24 = Vtiger_Field::getInstance('is_required', $module4);
if ($field24) {
    echo "<li>The is_required field already exists</li><br> \n";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_TARIFFSERVICES_REQUIRED';
    $field24->name = 'is_required';
    $field24->table = 'vtiger_tariffservices';
    $field24->column = 'is_required';
    $field24->columntype = 'VARCHAR(3)';
    $field24->uitype = 56;
    $field24->typeofdata = 'C~O';

    $block7->addField($field24);
}
/*$field25 = Vtiger_Field::getInstance('is_discountable',$module4);
if ($field25) {
    echo "<li>The is_discountable field already exists</li><br> \n";
}
else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_TARIFFSERVICES_DISCOUNTABLE';
    $field25->name = 'is_discountable';
    $field25->table = 'vtiger_tariffservices';
    $field25->column = 'is_discountable';
    $field25->columntype = 'VARCHAR(3)';
    $field25->uitype = 56;
    $field25->typeofdata = 'C~O';

    $block7->addField($field25);
}*/
$field53 = Vtiger_Field::getInstance('assigned_user_id', $module4);
if ($field53) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field53 = new Vtiger_Field();
    $field53->label = 'Assigned To';
    $field53->name = 'assigned_user_id';
    $field53->table = 'vtiger_crmentity';
    $field53->column = 'smownerid';
    $field53->uitype = 53;
    $field53->typeofdata = 'V~M';

    $block7->addField($field53);
}
$field54 = Vtiger_Field::getInstance('CreatedTime', $module4);
if ($field54) {
    echo "<li>The CreatedTime field already exists</li><br> \n";
} else {
    $field54 = new Vtiger_Field();
    $field54->label = 'Created Time';
    $field54->name = 'CreatedTime';
    $field54->table = 'vtiger_crmentity';
    $field54->column = 'createdtime';
    $field54->uitype = 70;
    $field54->typeofdata = 'T~O';
    $field54->displaytype = 2;

    $block7->addField($field54);
}
$field55 = Vtiger_Field::getInstance('ModifiedTime', $module4);
if ($field55) {
    echo "<li>The ModifiedTime field already exists</li><br> \n";
} else {
    $field55 = new Vtiger_Field();
    $field55->label = 'Modified Time';
    $field55->name = 'ModifiedTime';
    $field55->table = 'vtiger_crmentity';
    $field55->uitype = 70;
    $field55->typeofdata = 'T~O';
    $field55->displaytype = 2;

    $block7->addField($field55);
}
//end block7 fields

echo "</ul>";
$block7->save($module4);
//end block7 : LBL_TARIFFSERVICES_INFORMATION

//start block8 : LBL_CUSTOM_INFORMATION
$block8 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module4);
if ($block8) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $block8 = new Vtiger_Block();
    $block8->label = 'LBL_CUSTOM_INFORMATION';
    $module4->addBlock($block8);
}
$block8->save($module4);
//end block8 : LBL_CUSTOM_INFORMATION

//start block9 : LBL_TARIFFSERVICES_CHARGEPERHUNDRED
$block9 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CHARGEPERHUNDRED', $module4);
if ($block9) {
    echo "<h3>The LBL_TARIFFSERVICES_CHARGEPERHUNDRED block already exists</h3><br> \n";
} else {
    $block9 = new Vtiger_Block();
    $block9->label = 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED';
    $module4->addBlock($block9);
}

echo "<ul>";
//start block9 fields
//deprecated now
// $field26 = Vtiger_Field::getInstance('chargeperhundred_rate',$module4);
// if ($field26) {
    // echo "<li>The chargeperhundred_rate field already exists</li><br> \n";
// }
// else {
    // $field26 = new Vtiger_Field();
    // $field26->label = 'LBL_TARIFFSERVICES_RATE';
    // $field26->name = 'chargeperhundred_rate';
    // $field26->table = 'vtiger_tariffservices';
    // $field26->column = 'chargeperhundred_rate';
    // $field26->columntype = 'DECIMAL(10,2)';
    // $field26->uitype = 71;
    // $field26->typeofdata = 'N~O';

    // $block9->addField($field26);
// }
//end block9 fields

echo "</ul>";
$block9->save($module4);
//end block9 : LBL_TARIFFSERVICES_CHARGEPERHUNDRED

//start block10 : LBL_TARIFFSERVICES_CRATINGITEM
$block10 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CRATINGITEM', $module4);
if ($block10) {
    echo "<h3>The LBL_TARIFFSERVICES_CRATINGITEM block already exists</h3><br> \n";
} else {
    $block10 = new Vtiger_Block();
    $block10->label = 'LBL_TARIFFSERVICES_CRATINGITEM';
    $module4->addBlock($block10);
}

echo "<ul>";
//start block10 fields
$field27 = Vtiger_Field::getInstance('crate_inches', $module4);
if ($field27) {
    echo "<li>The crate_inches field already exists</li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_TARIFFSERVICES_INCHES';
    $field27->name = 'crate_inches';
    $field27->table = 'vtiger_tariffservices';
    $field27->column = 'crate_inches';
    $field27->columntype = 'INT(5)';
    $field27->uitype = 7;
    $field27->typeofdata = 'I~O';

    $block10->addField($field27);
}
$field28 = Vtiger_Field::getInstance('crate_mincube', $module4);
if ($field28) {
    echo "<li>The crate_mincube field already exists</li><br> \n";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_TARIFFSERVICES_MINCRATECUBE';
    $field28->name = 'crate_mincube';
    $field28->table = 'vtiger_tariffservices';
    $field28->column = 'crate_mincube';
    $field28->columntype = 'INT(10)';
    $field28->uitype = 7;
    $field28->typeofdata = 'I~O';

    $block10->addField($field28);
}
$field29 = Vtiger_Field::getInstance('crate_packrate', $module4);
if ($field29) {
    echo "<li>The crate_packrate field already exists</li><br> \n";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_TARIFFSERVICES_CRATINGRATE';
    $field29->name = 'crate_packrate';
    $field29->table = 'vtiger_tariffservices';
    $field29->column = 'crate_packrate';
    $field29->columntype = 'DECIMAL(10,2)';
    $field29->uitype = 71;
    $field29->typeofdata = 'N~O';

    $block10->addField($field29);
}
$field30 = Vtiger_Field::getInstance('crate_unpackrate', $module4);
if ($field30) {
    echo "<li>The crate_unpackrate field already exists</li><br> \n";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_TARIFFSERVICES_UNCRATINGRATE';
    $field30->name = 'crate_unpackrate';
    $field30->table = 'vtiger_tariffservices';
    $field30->column = 'crate_unpackrate';
    $field30->columntype = 'DECIMAL(10,2)';
    $field30->uitype = 71;
    $field30->typeofdata = 'N~O';

    $block10->addField($field30);
}
//end block10 fields

echo "</ul>";
$block10->save($module4);
//end block10 : LBL_TARIFFSERVICES_CRATINGITEM

//start block11 : LBL_TARIFFSERVICES_FLATCHARGE
$block11 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_FLATCHARGE', $module4);
if ($block11) {
    echo "<h3>The LBL_TARIFFSERVICES_FLATCHARGE block already exists</h3><br> \n";
} else {
    $block11 = new Vtiger_Block();
    $block11->label = 'LBL_TARIFFSERVICES_FLATCHARGE';
    $module4->addBlock($block11);
}

echo "<ul>";
//start block11 fields
$field31 = Vtiger_Field::getInstance('flat_rate', $module4);
if ($field31) {
    echo "<li>The flat_rate field already exists</li><br> \n";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_TARIFFSERVICES_RATE';
    $field31->name = 'flat_rate';
    $field31->table = 'vtiger_tariffservices';
    $field31->column = 'flat_rate';
    $field31->columntype = 'DECIMAL(10,2)';
    $field31->uitype = 71;
    $field31->typeofdata = 'N~O';
}

$block11->addField($field31);
//end block11 fields

echo "</ul>";
$block11->save($module4);
//end block11 : LBL_TARIFFSERVICES_FLATCHARGE

//start block12 : LBL_TARIFFSERVICES_HOURLYAVG
$block12 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_HOURLYAVG', $module4);
if ($block12) {
    echo "<h3>The LBL_TARIFFSERVICES_HOURLYAVG block already exists</h3><br> \n";
} else {
    $block12 = new Vtiger_Block();
    $block12->label = 'LBL_TARIFFSERVICES_HOURLYAVG';
    $module4->addBlock($block12);
}

echo "<ul>";
//start block12 fields
$field32 = Vtiger_Field::getInstance('hourlyavg_rate', $module4);
if ($field32) {
    echo "<li>The hourlyavg_rate field already exists</li><br> \n";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_TARIFFSERVICES_RATE';
    $field32->name = 'hourlyavg_rate';
    $field32->table = 'vtiger_tariffservices';
    $field32->column = 'hourlyavg_rate';
    $field32->columntype = 'DECIMAL(10,2)';
    $field32->uitype = 71;
    $field32->typeofdata = 'N~O';

    $block12->addField($field32);
}
//end block12 fields

echo "</ul>";
$block12->save($module4);
//end block12 : LBL_TARIFFSERVICES_HOURLYAVG

//start block13 : LBL_TARIFFSERVICES_HOURLYSET
$block13 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_HOURLYSET', $module4);
if ($block13) {
    echo "<h3>The LBL_TARIFFSERVICES_HOURLYSET block already exists</h3><br> \n";
} else {
    $block13 = new Vtiger_Block();
    $block13->label = 'LBL_TARIFFSERVICES_HOURLYSET';
    $module4->addBlock($block13);
}

echo "<ul>";
//start block13 fields
$field33 = Vtiger_Field::getInstance('hourlyset_hasvan', $module4);
if ($field33) {
    echo "<li>The hourlyset_hasvan field already exists</li><br> \n";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_TARIFFSERVICES_HASVAN';
    $field33->name = 'hourlyset_hasvan';
    $field33->table = 'vtiger_tariffservices';
    $field33->column = 'hourlyset_hasvan';
    $field33->columntype = 'VARCHAR(3)';
    $field33->uitype = 56;
    $field33->typeofdata = 'C~O';

    $block13->addField($field33);
}
$field34 = Vtiger_Field::getInstance('hourlyset_hastravel', $module4);
if ($field34) {
    echo "<li>The hourlyset_hastravel field already exists</li><br> \n";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_TARIFFSERVICES_TRAVELTIME';
    $field34->name = 'hourlyset_hastravel';
    $field34->table = 'vtiger_tariffservices';
    $field34->column = 'hourlyset_hastravel';
    $field34->columntype = 'VARCHAR(3)';
    $field34->uitype = 56;
    $field34->typeofdata = 'C~O';

    $block13->addField($field34);
}
$field35 = Vtiger_Field::getInstance('hourlyset_addmanrate', $module4);
if ($field35) {
    echo "<li>The hourlyset_addmanrate field already exists</li><br> \n";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_TARIFFSERVICES_ADDMANRATE';
    $field35->name = 'hourlyset_addmanrate';
    $field35->table = 'vtiger_tariffservices';
    $field35->column = 'hourlyset_addmanrate';
    $field35->columntype = 'DECIMAL(10,2)';
    $field35->uitype = 71;
    $field35->typeofdata = 'N~O';

    $block13->addField($field35);
}
$field36 = Vtiger_Field::getInstance('hourlyset_addvanrate', $module4);
if ($field36) {
    echo "<li>The hourlyset_addvanrate field already exists</li><br> \n";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_TARIFFSERVICES_ADDVANRATE';
    $field36->name = 'hourlyset_addvanrate';
    $field36->table = 'vtiger_tariffservices';
    $field36->column = 'hourlyset_addvanrate';
    $field36->columntype = 'DECIMAL(10,2)';
    $field36->uitype = 71;
    $field36->typeofdata = 'N~O';

    $block13->addField($field36);
}
//end block13 fields

echo "</ul>";
$block13->save($module4);
//end block13 : LBL_TARIFFSERVICES_HOURLYSET

//start block14 : LBL_TARIFFSERVICES_HOURLYSIMPLE
$block14 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_HOURLYSIMPLE', $module4);
if ($block14) {
    echo "<h3>The LBL_TARIFFSERVICES_HOURLYSIMPLE block already exists</h3><br> \n";
} else {
    $block14 = new Vtiger_Block();
    $block14->label = 'LBL_TARIFFSERVICES_HOURLYSIMPLE';
    $module4->addBlock($block14);
}

echo "<ul>";
//start block14 fields
$field37 = Vtiger_Field::getInstance('hourlysimple_rate', $module4);
if ($field37) {
    echo "<li>The hourlysimple_rate field already exists</li><br> \n";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_TARIFFSERVICES_RATE';
    $field37->name = 'hourlysimple_rate';
    $field37->table = 'vtiger_tariffservices';
    $field37->column = 'hourlysimple_rate';
    $field37->columntype = 'DECIMAL(10,2)';
    $field37->uitype = 71;
    $field37->typeofdata = 'N~O';

    $block14->addField($field37);
}
//end block14 fields

echo "</ul>";
$block14->save($module4);
//end block14 : LBL_TARIFFSERVICES_HOURLYSIMPLE

//start block15 : LBL_TARIFFSERVICES_CUFTPERDAY
$block15 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CUFTPERDAY', $module4);
if ($block15) {
    echo "<h3>The LBL_TARIFFSERVICES_CUFTPERDAY block already exists</h3><br> \n";
} else {
    $block15 = new Vtiger_Block();
    $block15->label = 'LBL_TARIFFSERVICES_CUFTPERDAY';
    $module4->addBlock($block15);
}

echo "<ul>";
//start block15 fields
$field38 = Vtiger_Field::getInstance('cuftperday_rate', $module4);
if ($field38) {
    echo "<li>The cuftperday_rate field already exists</li><br> \n";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_TARIFFSERVICES_RATE';
    $field38->name = 'cuftperday_rate';
    $field38->table = 'vtiger_tariffservices';
    $field38->column = 'cuftperday_rate';
    $field38->columntype = 'DECIMAL(10,2)';
    $field38->uitype = 71;
    $field38->typeofdata = 'N~O';

    $block15->addField($field38);
}
//end block15 fields

echo "</ul>";
$block15->save($module4);
//end block15 : LBL_TARIFFSERVICES_CUFTPERDAY

//start block16 : LBL_TARIFFSERVICES_CUFTPERMONTH
$block16 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CUFTPERMONTH', $module4);
if ($block16) {
    echo "<h3>The LBL_TARIFFSERVICES_CUFTPERMONTH block already exists</h3><br> \n";
} else {
    $block16 = new Vtiger_Block();
    $block16->label = 'LBL_TARIFFSERVICES_CUFTPERMONTH';
    $module4->addBlock($block16);
}

echo "<ul>";
//start block16 fields
$field39 = Vtiger_Field::getInstance('cuftpermonth_rate', $module4);
if ($field39) {
    echo "<li>The cuftpermonth_rate field already exists</li><br> \n";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_TARIFFSERVICES_RATE';
    $field39->name = 'cuftpermonth_rate';
    $field39->table = 'vtiger_tariffservices';
    $field39->column = 'cuftpermonth_rate';
    $field39->columntype = 'DECIMAL(10,2)';
    $field39->uitype = 71;
    $field39->typeofdata = 'N~O';

    $block16->addField($field39);
}
//end block16 fields

echo "</ul>";
$block16->save($module4);
//end block16 : LBL_TARIFFSERVICES_CUFTPERMONTH

//start block17 : LBL_TARIFFSERVICES_CWT
$block17 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWT', $module4);
if ($block17) {
    echo "<h3>The LBL_TARIFFSERVICES_CWT block already exists</h3><br> \n";
} else {
    $block17 = new Vtiger_Block();
    $block17->label = 'LBL_TARIFFSERVICES_CWT';
    $module4->addBlock($block17);
}

echo "<ul>";
//start block17 fields
$field40 = Vtiger_Field::getInstance('cwt_rate', $module4);
if ($field40) {
    echo "<li>The cwt_rate field already exists</li><br> \n";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_TARIFFSERVICES_RATE';
    $field40->name = 'cwt_rate';
    $field40->table = 'vtiger_tariffservices';
    $field40->column = 'cwt_rate';
    $field40->columntype = 'DECIMAL(10,2)';
    $field40->uitype = 71;
    $field40->typeofdata = 'N~O';

    $block17->addField($field40);
}
//end block17 fields

echo "</ul>";
$block17->save($module4);
//end block17 : LBL_TARIFFSERVICES_CWT

//start block18 : LBL_TARIFFSERVICES_CWTPERDAY
$block18 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWTPERDAY', $module4);
if ($block18) {
    echo "<h3>The LBL_TARIFFSERVICES_CWTPERDAY block already exists</h3><br> \n";
} else {
    $block18 = new Vtiger_Block();
    $block18->label = 'LBL_TARIFFSERVICES_CWTPERDAY';
    $module4->addBlock($block18);
}

echo "<ul>";
//start block18 fields
$field41 = Vtiger_Field::getInstance('cwtperday_rate', $module4);
if ($field41) {
    echo "<li>The cwtperday_rate field already exists</li><br> \n";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_TARIFFSERVICES_RATE';
    $field41->name = 'cwtperday_rate';
    $field41->table = 'vtiger_tariffservices';
    $field41->column = 'cwtperday_rate';
    $field41->columntype = 'DECIMAL(10,2)';
    $field41->uitype = 71;
    $field41->typeofdata = 'N~O';

    $block18->addField($field41);
}
//end block18 fields

echo "</ul>";
$block18->save($module4);
//end block18 : LBL_TARIFFSERVICES_CWTPERDAY

//start block19 : LBL_TARIFFSERVICES_CWTPERMONTH
$block19 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWTPERMONTH', $module4);
if ($block19) {
    echo "<h3>The LBL_TARIFFSERVICES_CWTPERMONTH block already exists</h3><br> \n";
} else {
    $block19 = new Vtiger_Block();
    $block19->label = 'LBL_TARIFFSERVICES_CWTPERMONTH';
    $module4->addBlock($block19);
}

echo "<ul>";
//start block19 fields
$field42 = Vtiger_Field::getInstance('cwtpermonth_rate', $module4);
if ($field42) {
    echo "<li>The cwtpermonth_rate field already exists</li><br> \n";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_TARIFFSERVICES_RATE';
    $field42->name = 'cwtpermonth_rate';
    $field42->table = 'vtiger_tariffservices';
    $field42->column = 'cwtpermonth_rate';
    $field42->columntype = 'DECIMAL(10,2)';
    $field42->uitype = 71;
    $field42->typeofdata = 'N~O';

    $block19->addField($field42);
}
//end block19 fields

echo "</ul>";
$block19->save($module4);
//end block19 : LBL_TARIFFSERVICES_CWTPERMONTH

//start block20 : LBL_TARIFFSERVICES_QTY
$block20 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_QTY', $module4);
if ($block20) {
    echo "<h3>The LBL_TARIFFSERVICES_QTY block already exists</h3><br> \n";
} else {
    $block20 = new Vtiger_Block();
    $block20->label = 'LBL_TARIFFSERVICES_QTY';
    $module4->addBlock($block20);
}

echo "<ul>";
//start block20 fields
$field43 = Vtiger_Field::getInstance('qty_rate', $module4);
if ($field43) {
    echo "<li>The qty_rate field already exists</li><br> \n";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_TARIFFSERVICES_RATE';
    $field43->name = 'qty_rate';
    $field43->table = 'vtiger_tariffservices';
    $field43->column = 'qty_rate';
    $field43->columntype = 'DECIMAL(10,2)';
    $field43->uitype = 71;
    $field43->typeofdata = 'N~O';

    $block20->addField($field43);
}
//end block20 fields

echo "</ul>";
$block20->save($module4);
//end block20 : LBL_TARIFFSERVICES_QTY

//start block21 : LBL_TARIFFSERVICES_QTYPERDAY
$block21 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_QTYPERDAY', $module4);
if ($block21) {
    echo "<h3>The LBL_TARIFFSERVICES_QTYPERDAY block already exists</h3><br> \n";
} else {
    $block21 = new Vtiger_Block();
    $block21->label = 'LBL_TARIFFSERVICES_QTYPERDAY';
    $module4->addBlock($block21);
}

echo "<ul>";
//start block21 fields
$field44 = Vtiger_Field::getInstance('qtyperday_rate', $module4);
if ($field44) {
    echo "<li>The qtyperday_rate field already exists</li><br> \n";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_TARIFFSERVICES_RATE';
    $field44->name = 'qtyperday_rate';
    $field44->table = 'vtiger_tariffservices';
    $field44->column = 'qtyperday_rate';
    $field44->columntype = 'DECIMAL(10,2)';
    $field44->uitype = 71;
    $field44->typeofdata = 'N~O';

    $block21->addField($field44);
}
//end block21 fields

echo "</ul>";
$block21->save($module4);
//end block21 : LBL_TARIFFSERVICES_QTYPERDAY

//start block22 : LBL_TARIFFSERVICES_QTYPERMONTH
$block22 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_QTYPERMONTH', $module4);
if ($block22) {
    echo "<h3>The LBL_TARIFFSERVICES_QTYPERMONTH block already exists</h3><br> \n";
} else {
    $block22 = new Vtiger_Block();
    $block22->label = 'LBL_TARIFFSERVICES_QTYPERMONTH';
    $module4->addBlock($block22);
}

echo "<ul>";
//start block22 fields
$field45 = Vtiger_Field::getInstance('qtypermonth_rate', $module4);
if ($field45) {
    echo "<li>The qtypermonth_rate field already exists</li><br> \n";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_TARIFFSERVICES_RATE';
    $field45->name = 'qtypermonth_rate';
    $field45->table = 'vtiger_tariffservices';
    $field45->column = 'qtypermonth_rate';
    $field45->columntype = 'DECIMAL(10,2)';
    $field45->uitype = 71;
    $field45->typeofdata = 'N~O';

    $block22->addField($field45);
}
//end block22 fields

echo "</ul>";
$block22->save($module4);
//end block22 : LBL_TARIFFSERVICES_QTYPERMONTH

//start block23 : LBL_TARIFFSERVICES_BASEPLUS
$block23 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_BASEPLUS', $module4);
if ($block23) {
    echo "<h3>The LBL_TARIFFSERVICES_BASEPLUS block already exists</h3><br> \n";
} else {
    $block23 = new Vtiger_Block();
    $block23->label = 'LBL_TARIFFSERVICES_BASEPLUS';
    $module4->addBlock($block23);
}
$block23->save($module4);
//end block23 : LBL_TARIFFSERVICES_BASEPLUS

//start block24 : LBL_TARIFFSERVICES_BREAKPOINT
$block24 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_BREAKPOINT', $module4);
if ($block24) {
    echo "<h3>The LBL_TARIFFSERVICES_BREAKPOINT block already exists</h3><br> \n";
} else {
    $block24 = new Vtiger_Block();
    $block24->label = 'LBL_TARIFFSERVICES_BREAKPOINT';
    $module4->addBlock($block24);
}
$block24->save($module4);
//end block24 : LBL_TARIFFSERVICES_BREAKPOINT

//start block25 : LBL_TARIFFSERVICES_WEIGHTMILEAGE
$block25 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_WEIGHTMILEAGE', $module4);
if ($block25) {
    echo "<h3>The LBL_TARIFFSERVICES_WEIGHTMILEAGE block already exists</h3><br> \n";
} else {
    $block25 = new Vtiger_Block();
    $block25->label = 'LBL_TARIFFSERVICES_WEIGHTMILEAGE';
    $module4->addBlock($block25);
}
$block25->save($module4);
//end block25 : LBL_TARIFFSERVICES_WEIGHTMILEAGE

//start block26 : LBL_TARIFFSERVICES_BULKY
$block26 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_BULKY', $module4);
if ($block26) {
    echo "<h3>The LBL_TARIFFSERVICES_BULKY block already exists</h3><br> \n";
} else {
    $block26 = new Vtiger_Block();
    $block26->label = 'LBL_TARIFFSERVICES_BULKY';
    $module4->addBlock($block26);
}

echo "<ul>";
//start block26 fields
$field46 = Vtiger_Field::getInstance('bulky_chargeper', $module4);
if ($field46) {
    echo "<li>The bulky_chargeper field already exists</li><br> \n";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_TARIFFSERVICES_CHARGEPER';
    $field46->name = 'bulky_chargeper';
    $field46->table = 'vtiger_tariffservices';
    $field46->column = 'bulky_chargeper';
    $field46->columntype = 'VARCHAR(255)';
    $field46->uitype = 16;
    $field46->typeofdata = 'V~O';

    $block26->addField($field46);
    
    $field46->setPicklistValues(array('Quantity', 'Hourly'));
}
//end block26 fields

echo "</ul>";
$block26->save($module4);
//end block26 : LBL_TARIFFSERVICES_BULKY

//start block27 : LBL_TARIFFSERVICES_COUNTYCHARGE
$block27 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_COUNTYCHARGE', $module4);
if ($block27) {
    echo "<h3>The LBL_TARIFFSERVICES_COUNTYCHARGE block already exists</h3><br> \n";
} else {
    $block27 = new Vtiger_Block();
    $block27->label = 'LBL_TARIFFSERVICES_COUNTYCHARGE';
    $module4->addBlock($block27);
}
$block27->save($module4);
//end block27 : LBL_TARIFFSERVICES_COUNTYCHARGE

//start block28 : LBL_TARIFFSERVICES_PACKING
$block28 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_PACKING', $module4);
if ($block28) {
    echo "<h3>The LBL_TARIFFSERVICES_PACKING block already exists</h3><br> \n";
} else {
    $block28 = new Vtiger_Block();
    $block28->label = 'LBL_TARIFFSERVICES_PACKING';
    $module4->addBlock($block28);
}

echo "<ul>";
//start block28 fields
$field47 = Vtiger_Field::getInstance('packing_containers', $module4);
if ($field47) {
    echo "<li>The packing_containers field already exists</li><br> \n";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_TARIFFSERVICES_HASCONTAINERS';
    $field47->name = 'packing_containers';
    $field47->table = 'vtiger_tariffservices';
    $field47->column = 'packing_containers';
    $field47->columntype = 'VARCHAR(3)';
    $field47->uitype = 56;
    $field47->typeofdata = 'C~O';

    $block28->addField($field47);
}
$field48 = Vtiger_Field::getInstance('packing_haspacking', $module4);
if ($field48) {
    echo "<li>The packing_haspacking field already exists</li><br> \n";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_TARIFFSERVICES_HASPACKING';
    $field48->name = 'packing_haspacking';
    $field48->table = 'vtiger_tariffservices';
    $field48->column = 'packing_haspacking';
    $field48->columntype = 'VARCHAR(3)';
    $field48->uitype = 56;
    $field48->typeofdata = 'C~O';

    $block28->addField($field48);
}
$field49 = Vtiger_Field::getInstance('packing_hasunpacking', $module4);
if ($field49) {
    echo "<li>The packing_hasunpacking field already exists</li><br> \n";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_TARIFFSERVICES_HASUNPACKING';
    $field49->name = 'packing_hasunpacking';
    $field49->table = 'vtiger_tariffservices';
    $field49->column = 'packing_hasunpacking';
    $field49->columntype = 'VARCHAR(3)';
    $field49->uitype = 56;
    $field49->typeofdata = 'C~O';

    $block28->addField($field49);
}
$field50 = Vtiger_Field::getInstance('packing_salestax', $module4);
if ($field50) {
    echo "<li>The packing_salestax field already exists</li><br> \n";
} else {
    $field50 = new Vtiger_Field();
    $field50->label = 'LBL_TARIFFSERVICES_SALESTAX';
    $field50->name = 'packing_salestax';
    $field50->table = 'vtiger_tariffservices';
    $field50->column = 'packing_salestax';
    $field50->columntype = 'DECIMAL(7,3)';
    $field50->uitype = 9;
    $field50->typeofdata = 'N~O';

    $block28->addField($field50);
}
//end block28 fields

echo "</ul>";
$block28->save($module4);
//end block28 : LBL_TARIFFSERVICES_PACKING

//start block29 : LBL_TARIFFSERVICES_VALUATION
$block29 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_VALUATION', $module4);
if ($block29) {
    echo "<h3>The LBL_TARIFFSERVICES_VALUATION block already exists</h3><br> \n";
} else {
    $block29 = new Vtiger_Block();
    $block29->label = 'LBL_TARIFFSERVICES_VALUATION';
    $module4->addBlock($block29);
}

echo "<ul>";
//start block29 fields
$field51 = Vtiger_Field::getInstance('valuation_released', $module4);
if ($field51) {
    echo "<li>The valuation_released field already exists</li><br> \n";
} else {
    $field51 = new Vtiger_Field();
    $field51->label = 'LBL_TARIFFSERVICES_RELEASEDVAL';
    $field51->name = 'valuation_released';
    $field51->table = 'vtiger_tariffservices';
    $field51->column = 'valuation_released';
    $field51->columntype = 'VARCHAR(3)';
    $field51->uitype = 56;
    $field51->typeofdata = 'C~O';

    $block29->addField($field51);
}
$field52 = Vtiger_Field::getInstance('valuation_releasedamount', $module4);
if ($field52) {
    echo "<li>The valuation_releasedamount field already exists</li><br> \n";
} else {
    $field52 = new Vtiger_Field();
    $field52->label = 'LBL_TARIFFSERVICES_RELEASEDVALAMOUNT';
    $field52->name = 'valuation_releasedamount';
    $field52->table = 'vtiger_tariffservices';
    $field52->column = 'valuation_releasedamount';
    $field52->columntype = 'DECIMAL(10,2)';
    $field52->uitype = 71;
    $field52->typeofdata = 'N~O';

    $block29->addField($field52);
}
//end block29 fields

echo "</ul>";
$block29->save($module4);
//end block29 : LBL_TARIFFSERVICES_VALUATION
//this is done in a different script now
// if($tariffServicesIsNew){
    // $filter1 = new Vtiger_Filter();
    // $filter1->name = 'All';
    // $filter1->isdefault = true;
    // $module4->addFilter($filter1);

    // $filter1->addField($field18)->addField($field19, 1)->addField($field20, 2)->addField($field21, 3)->addField($field22, 4);

    // $module4->setDefaultSharing();

    // $module4->initWebservice();

    
    // $dateInstance = Vtiger_Module::getInstance('EffectiveDates');
    // $relationLabel = 'Tariff Services';
    // $dateInstance->setRelatedList($module4, $relationLabel, Array('Add'));
// }
//End TariffServices Module4

//Start Check Tables
echo "<h2>Updating Tables for Tariffs</h2><br>";
echo "<ul>";

if (!Vtiger_Utils::CheckTable('vtiger_tariffbaseplus')) {
    Vtiger_Utils::CreateTable('vtiger_tariffbaseplus', '(
	`serviceid` int(30) NOT NULL,
	`from_miles` int(30) NOT NULL,
	`to_miles` int(30) NOT NULL,
	`from_weight` int(30) NOT NULL,
	`to_weight` int(30) NOT NULL,
	`base_rate` decimal(10,2) NOT NULL,
	`excess` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffbaseplus_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffbaseplus table created <br></li>";
} else {
    echo "<li> vtiger_tariffbaseplus table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffbaseplus_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffbaseplus_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffbaseplus_seq table created <br></li>";
    
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffbaseplus_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffbaseplus_seq id initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffbaseplus_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffbreakpoint')) {
    Vtiger_Utils::CreateTable('vtiger_tariffbreakpoint', '(
	`serviceid` int(30) NOT NULL,
	`from_miles` int(30) NOT NULL,
	`to_miles` int(30) NOT NULL,
	`from_weight` int(30) NOT NULL,
	`to_weight` int(30) NOT NULL,
	`break_point` int(30) NOT NULL,
	`base_rate` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffbreakpoint_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffbreakpoint table created <br></li>";
} else {
    echo "<li> vtiger_tariffbreakpoint table exists<br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffbreakpoint_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffbreakpoint_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffbreakpoint_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffbreakpoint_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffbreakpoint_seq id initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffbreakpoint_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffweightmileage')) {
    Vtiger_Utils::CreateTable('vtiger_tariffweightmileage', '(
	`serviceid` int(30) NOT NULL,
	`from_miles` int(30) NOT NULL,
	`to_miles` int(30) NOT NULL,
	`from_weight` int(30) NOT NULL,
	`to_weight` int(30) NOT NULL,
	`base_rate` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffweightmileage_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffweightmileage table created <br></li>";
} else {
    echo "<li> vtiger_tariffweightmileage table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffweightmileage_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffweightmileage_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffweightmileage_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffweightmileage_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffweightmileage_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffweightmileage_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffbulky')) {
    Vtiger_Utils::CreateTable('vtiger_tariffbulky', '(
	`serviceid` int(30) NOT NULL,
	`description` varchar(100) NOT NULL,
	`weight` int(30),
	`rate` decimal(10, 2),
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`), 
	UNIQUE KEY `vtiger_tariffbulky_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffbulky table created <br></li>";
} else {
    echo "<li> vtiger_tariffbulky table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffbulky_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffbulky_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffbulky_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffbulky_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffbulky_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffbulky_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffchargeperhundred')) {
    Vtiger_Utils::CreateTable('vtiger_tariffchargeperhundred', '(
	`serviceid` int(30) NOT NULL,
	`deductible` decimal(10,2) NOT NULL,
	`rate` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffchargeperhundred_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffchargeperhundred table created <br></li>";
} else {
    echo "<li> vtiger_tariffchargeperhundred table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffchargeperhundred_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffchargeperhundred_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffchargeperhundred_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffchargeperhundred_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffchargeperhundred_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffchargeperhundred_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffcountycharge')) {
    Vtiger_Utils::CreateTable('vtiger_tariffcountycharge', '(
	`serviceid` int(30) NOT NULL,
	`name` varchar(50) NOT NULL,
	`rate` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffcountycharge_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffcountycharge table created <br></li>";
} else {
    echo "<li> vtiger_tariffcountycharge table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffcountycharge_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffcountycharge_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffcountycharge_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffcountycharge_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffcountycharge_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffcountycharge_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffhourlyset')) {
    Vtiger_Utils::CreateTable('vtiger_tariffhourlyset', '(
	`serviceid` int(30) NOT NULL,
	`men` int(30) NOT NULL,
	`vans` int(30) NOT NULL,
	`rate` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffhourlyset_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffhourlyset table created <br></li>";
} else {
    echo "<li> vtiger_tariffhourlyset table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffhourlyset_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffhourlyset_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffhourlyset_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffhourlyset_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffhourlyset_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffhourlyset_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffpackingitems')) {
    Vtiger_Utils::CreateTable('vtiger_tariffpackingitems', '(
	`serviceid` int(30) NOT NULL,
	`name` varchar(50) NOT NULL,
	`container_rate` decimal(10,2),
	`packing_rate` decimal(10,2),
	`unpacking_rate` decimal(10,2),
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffpackingitems_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffpackingitems table created <br></li>";
} else {
    echo "<li> vtiger_tariffpackingitems table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffpackingitems_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffpackingitems_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffpackingitems_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffpackingitems_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffpackingitems_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffpackingitems_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffvaluations')) {
    Vtiger_Utils::CreateTable('vtiger_tariffvaluations', '(
	`serviceid` int(30) NOT NULL,
	`amount` decimal(10,2) NOT NULL,
	`deductible` decimal(10,2) NOT NULL,
	`cost` decimal(10,2) NOT NULL,
	`line_item_id` int(30) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`line_item_id`),
	UNIQUE KEY `vtiger_tariffvaluations_idx` (`line_item_id`)
	)', true);
    echo "<li> vtiger_tariffpackingitems_seq table created<br></li>";
} else {
    echo "<li> vtiger_tariffpackingitems_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_tariffvaluations_seq')) {
    Vtiger_Utils::CreateTable('vtiger_tariffvaluations_seq', '(
	`id` int(11) NOT NULL
	)', true);
    echo "<li> vtiger_tariffvaluations_seq table created <br></li>";
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffvaluations_seq` (id) VALUES (0)');
    echo "<li> vtiger_tariffvaluations_seq initialized to 0 <br></li>";
} else {
    echo "<li> vtiger_tariffvaluations_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_states')) {
    Vtiger_Utils::CreateTable('vtiger_states', '(
	`stateid` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`abbr` varchar(5) NOT NULL,
	PRIMARY KEY (`stateid`)
	)', true);
    echo "<li> vtiger_tariffvaluations_seq table created <br></li>";
} else {
    echo "<li> vtiger_tariffvaluations_seq table exists <br></li>";
}
if (!Vtiger_Utils::CheckTable('vtiger_counties')) {
    Vtiger_Utils::CreateTable('vtiger_counties', '(
	`countyid` int(11) NOT NULL,
	`stateid` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`countyid`)
	)', true);
    echo "<li> vtiger_counties table created<br></li>";
} else {
    echo "<li> vtiger_counties table exists <br></li>";
}
echo "</ul>";

$moduleInstance = Vtiger_Module::getInstance('EffectiveDates');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('TariffServices'), 'Tariff Services', array('ADD'), 'get_dependents_list');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";