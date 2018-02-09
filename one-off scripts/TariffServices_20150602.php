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


echo "IN FILE";
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
echo "PAST INCLUDES";

$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('TariffServices');


if ($moduleInstance) {
    echo "<h1>Updating TariffServices Fields</h1><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'TariffServices';
    $moduleInstance->save();
    echo "<h1>Creating TariffServices and Updating Fields</h1><br>";
    $moduleInstance->initTables();
}
$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h2>LBL_TARIFFSERVICES_INFORMATION block already exists</h2><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFSERVICES_INFORMATION';
    echo "<h2>Creating LBL_TARIFFSERVICES_INFORMATION BLOCK</h2><br>";
    $moduleInstance->addBlock($blockInstance);
    $isNew = true;
}

$field1 = Vtiger_Field::getInstance('service_name', $moduleInstance);
if ($field1) {
    echo "service_name field already exists<br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TARIFFSERVICES_NAME';
    $field1->name = 'service_name';
    $field1->table = 'vtiger_tariffservices';
    $field1->column = 'service_name';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';

    $blockInstance->addField($field1);

    $moduleInstance->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('tariff_section', $moduleInstance);
if ($field2) {
    echo "tariff_section field already exists<br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TARIFFSERVICES_RELATEDSECTION';
    $field2->name = 'tariff_section';
    $field2->table = 'vtiger_tariffservices';
    $field2->column = 'tariff_section';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    
    $blockInstance->addField($field2);
    $field2->setRelatedModules(array('TariffSections'));
}
$field3 = Vtiger_Field::getInstance('effective_date', $moduleInstance);
if ($field3) {
    echo "effective_date field already exists<br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TARIFFSERVICES_EFFECTIVEDATE';
    $field3->name = 'effective_date';
    $field3->table = 'vtiger_tariffservices';
    $field3->column = 'effective_date';
    $field3->columntype = 'INT(19)';
    $field3->uitype = 10;
    $field3->typeofdata = 'D~M';

    $blockInstance->addField($field3);
    $field3->setRelatedModules(array('EffectiveDates'));
}

$field4 = Vtiger_Field::getInstance('related_tariff', $moduleInstance);
if ($field4) {
    echo "related_tariff field already exists<br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TARIFFSERVICES_RELATEDTARIFF';
    $field4->name = 'related_tariff';
    $field4->table = 'vtiger_tariffservices';
    $field4->column = 'related_tariff';
    $field4->columntype = 'INT(19)';
    $field4->uitype = 10;
    $field4->typeofdata = 'V~M';

    $blockInstance->addField($field4);

    $field4->setRelatedModules(array('Tariffs'));
}
$field5 = Vtiger_Field::getInstance('rate_type', $moduleInstance);
if ($field5) {
    echo "rate_type field already exists<br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_TARIFFSERVICES_RATETYPE';
    $field5->name = 'rate_type';
    $field5->table = 'vtiger_tariffservices';
    $field5->column = 'rate_type';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 15;
    $field5->typeofdata = 'V~M';

    $blockInstance->addField($field5);

    $field5->setPicklistValues(array('Base Plus Trans.', 'Break Point Trans.', 'Weight/Mileage Trans.', 'Bulky List', 'Charge Per $100 (Valuation)', 'County Charge', 'Crating Item', 'Flat Charge', 'Hourly Avg Lb/Man/Hour', 'Hourly Set', 'Hourly Simple', 'Packing Items', 'Per Cu Ft/Per Day', 'Per Cu Ft/Per Month', 'Per CWT', 'Per CWT/Per Day', 'Per CWT/Per Month', 'Per Quantity', 'Per Quantity/Per Day', 'Per Quantity/Per Month', 'Tabled Valuation'));
}
$field6 = Vtiger_Field::getInstance('applicability', $moduleInstance);
if ($field6) {
    echo "applicability field already exists<br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_TARIFFSERVICES_APPLICABILITY';
    $field6->name = 'applicability';
    $field6->table = 'vtiger_tariffservices';
    $field6->column = 'applicability';
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 15;
    $field6->typeofdata = 'V~M';

    $blockInstance->addField($field6);

    $field6->setPicklistValues(array('All Locations', 'Origin Destination Only', 'Shipment Level'));
}
$field7 = Vtiger_Field::getInstance('is_required', $moduleInstance);
if ($field7) {
    echo "is_required field already exists<br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_TARIFFSERVICES_REQUIRED';
    $field7->name = 'is_required';
    $field7->table = 'vtiger_tariffservices';
    $field7->column = 'is_required';
    $field7->columntype = 'VARCHAR(3)';
    $field7->uitype = 56;
    $field7->typeofdata = 'C~O';

    $blockInstance->addField($field7);
}
/* deprecated as of 6/02/2015 - ACS
$field8 = Vtiger_Field::getInstance('is_discountable', $moduleInstance);
if($field8) {
    echo "is_discountable field already exists<br>";
}
else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_TARIFFSERVICES_DISCOUNTABLE';
    $field8->name = 'is_discountable';
    $field8->table = 'vtiger_tariffservices';
    $field8->column = 'is_discountable';
    $field8->columntype = 'VARCHAR(3)';
    $field8->uitype = 56;
    $field8->typeofdata = 'C~O';

    $blockInstance->addField($field8);
}*/
$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<h2>LBL_CUSTOM_INFORMATION block alreayd exists</h2><br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    echo "<h2>Creating LBL_CUSTOM_INFORMATION block</h2><br>";
    $moduleInstance->addBlock($blockInstance2);
}

$blockInstance3 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CHARGEPERHUNDRED', $moduleInstance);
if ($blockInstance3) {
    echo "<h2>LBL_TARIFFSERVICES_CHARGEPERHUNDRED block already exists<h2><br>";
} else {
    $blockInstance3 = new Vtiger_Block();
    $blockInstance3->label = 'LBL_TARIFFSERVICES_CHARGEPERHUNDRED';
    echo "<h2>Creating LBL_TARIFFSERVICES_CHARGEPERHUNDRED block</h2><br>";
    $moduleInstance->addBlock($blockInstance3);
}
$field9 = Vtiger_Field::getInstance('chargeperhundred_rate', $moduleInstance);
if ($field9) {
    echo "chargeperhundred_rate field already exists<br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_TARIFFSERVICES_RATE';
    $field9->name = 'chargeperhundred_rate';
    $field9->table = 'vtiger_tariffservices';
    $field9->column = 'chargeperhundred_rate';
    $field9->columntype = 'DECIMAL(10,2)';
    $field9->uitype = 71;
    $field9->typeofdata = 'N~O';

    $blockInstance3->addField($field9);
}
$blockInstance4 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CRATINGITEM', $moduleInstance);
if ($blockInstance4) {
    echo "<h2>LBL_TARIFFSERVICES_CRATINGITEM block already exists</h2><br>";
} else {
    $blockInstance4 = new Vtiger_Block();
    $blockInstance4->label = 'LBL_TARIFFSERVICES_CRATINGITEM';
    echo "<h2>Creating LBL_TARIFFSERVICES_CRATINGITEM block</h2><br>";
    $moduleInstance->addBlock($blockInstance4);
}
$field10 = Vtiger_Field::getInstance('crate_inches', $moduleInstance);
if ($field10) {
    echo "crate_inches field already exists<br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_TARIFFSERVICES_INCHES';
    $field10->name = 'crate_inches';
    $field10->table = 'vtiger_tariffservices';
    $field10->column = 'crate_inches';
    $field10->columntype = 'INT(5)';
    $field10->uitype = 7;
    $field10->typeofdata = 'I~O';

    $blockInstance4->addField($field10);
}
$field11 = Vtiger_Field::getInstance('crate_mincube', $moduleInstance);
if ($field11) {
    echo "crate_mincube field already exists<br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_TARIFFSERVICES_MINCRATECUBE';
    $field11->name = 'crate_mincube';
    $field11->table = 'vtiger_tariffservices';
    $field11->column = 'crate_mincube';
    $field11->columntype = 'INT(10)';
    $field11->uitype = 7;
    $field11->typeofdata = 'I~O';

    $blockInstance4->addField($field11);
}
$field12 = Vtiger_Field::getInstance('crate_packrate', $moduleInstance);
if ($field12) {
    echo "crate_packrate field already exists<br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_TARIFFSERVICES_CRATINGRATE';
    $field12->name = 'crate_packrate';
    $field12->table = 'vtiger_tariffservices';
    $field12->column = 'crate_packrate';
    $field12->columntype = 'DECIMAL(10,2)';
    $field12->uitype = 71;
    $field12->typeofdata = 'N~O';

    $blockInstance4->addField($field12);
}
$field13 = Vtiger_Field::getInstance('crate_unpackrate', $moduleInstance);
if ($field13) {
    echo "crate_unpackrate field already exists<br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_TARIFFSERVICES_UNCRATINGRATE';
    $field13->name = 'crate_unpackrate';
    $field13->table = 'vtiger_tariffservices';
    $field13->column = 'crate_unpackrate';
    $field13->columntype = 'DECIMAL(10,2)';
    $field13->uitype = 71;
    $field13->typeofdata = 'N~O';

    $blockInstance4->addField($field13);

    $blockInstance5 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_FLATCHARGE', $moduleInstance);
}
    
if ($blockInstance5) {
    echo "<h2>LBL_TARIFFSERVICES_FLATCHARGE block already exists</h2><br>";
} else {
    $blockInstance5 = new Vtiger_Block();
    $blockInstance5->label = 'LBL_TARIFFSERVICES_FLATCHARGE';
    echo "<h2>Creating LBL_TARIFFSERVICES_FLATCHARGE block</h2><br>";
    $moduleInstance->addBlock($blockInstance5);
}
$field14 = Vtiger_Field::getInstance('flat_rate', $moduleInstance);
if ($field14) {
    echo "flat_rate field already exists<br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_TARIFFSERVICES_RATE';
    $field14->name = 'flat_rate';
    $field14->table = 'vtiger_tariffservices';
    $field14->column = 'flat_rate';
    $field14->columntype = 'DECIMAL(10,2)';
    $field14->uitype = 71;
    $field14->typeofdata = 'N~O';

    $blockInstance5->addField($field14);
}

$blockInstance6 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_HOURLYAVG', $moduleInstance);
if ($blockInstance6) {
    echo "<h2>LBL_TARIFFSERVICES_HOURLYAVG block already exists</h2><br>";
} else {
    $blockInstance6 = new Vtiger_Block();
    $blockInstance6->label = 'LBL_TARIFFSERVICES_HOURLYAVG';
    echo "<h2>Creating LBL_TARIFFSERVICES_HOURLYAVG block</h2><br>";
    $moduleInstance->addBlock($blockInstance6);
}
$field15 = Vtiger_Field::getInstance('hourlyavg_rate', $moduleInstance);
if ($field15) {
    echo "hourlyavg_rate field already exists<br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_TARIFFSERVICES_RATE';
    $field15->name = 'hourlyavg_rate';
    $field15->table = 'vtiger_tariffservices';
    $field15->column = 'hourlyavg_rate';
    $field15->columntype = 'DECIMAL(10,2)';
    $field15->uitype = 71;
    $field15->typeofdata = 'N~O';

    $blockInstance6->addField($field15);
}

$blockInstance7 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_HOURLYSET', $moduleInstance);
if ($blockInstance7) {
    echo "<h2>LBL_TARIFFSERVICES_HOURLYSET block already exists</h2><br>";
} else {
    $blockInstance7 = new Vtiger_Block();
    $blockInstance7->label = 'LBL_TARIFFSERVICES_HOURLYSET';
    echo "<h2>Creating LBL_TARIFFSERVICES_HOURLYSET block</h2><br>";
    $moduleInstance->addBlock($blockInstance7);
}
$field16 = Vtiger_Field::getInstance('hourlyset_hasvan', $moduleInstance);
if ($field16) {
    echo "hourlyset_hasvan field already exists<br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_TARIFFSERVICES_HASVAN';
    $field16->name = 'hourlyset_hasvan';
    $field16->table = 'vtiger_tariffservices';
    $field16->column = 'hourlyset_hasvan';
    $field16->columntype = 'VARCHAR(3)';
    $field16->uitype = 56;
    $field16->typeofdata = 'C~O';

    $blockInstance7->addField($field16);
}
$field17 = Vtiger_Field::getInstance('hourlyset_hastravel', $moduleInstance);
if ($field17) {
    echo "hourlyset_hastravel field already exists<br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_TARIFFSERVICES_TRAVELTIME';
    $field17->name = 'hourlyset_hastravel';
    $field17->table = 'vtiger_tariffservices';
    $field17->column = 'hourlyset_hastravel';
    $field17->columntype = 'VARCHAR(3)';
    $field17->uitype = 56;
    $field17->typeofdata = 'C~O';

    $blockInstance7->addField($field17);
}
$field18 = Vtiger_Field::getInstance('hourlyset_addmanrate', $moduleInstance);
if ($field18) {
    echo "hourlyset_addmanrate field already exists<br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_TARIFFSERVICES_ADDMANRATE';
    $field18->name = 'hourlyset_addmanrate';
    $field18->table = 'vtiger_tariffservices';
    $field18->column = 'hourlyset_addmanrate';
    $field18->columntype = 'DECIMAL(10,2)';
    $field18->uitype = 71;
    $field18->typeofdata = 'N~O';

    $blockInstance7->addField($field18);
}
$field19 = Vtiger_Field::getInstance('hourlyset_addvanrate', $moduleInstance);
if ($field19) {
    echo "hourlyset_addvanrate field already exists<br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_TARIFFSERVICES_ADDVANRATE';
    $field19->name = 'hourlyset_addvanrate';
    $field19->table = 'vtiger_tariffservices';
    $field19->column = 'hourlyset_addvanrate';
    $field19->columntype = 'DECIMAL(10,2)';
    $field19->uitype = 71;
    $field19->typeofdata = 'N~O';

    $blockInstance7->addField($field19);
}

$blockInstance8 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_HOURLYSIMPLE', $moduleInstance);
if ($blockInstance8) {
    echo "<h2>LBL_TARIFFSERVICES_HOURLYSIMPLE block already exists</h2><br>";
} else {
    $blockInstance8 = new Vtiger_Block();
    $blockInstance8->label = 'LBL_TARIFFSERVICES_HOURLYSIMPLE';
    echo "<h2>Creating LBL_TARIFFSERVICES_HOURLYSIMPLE block</h2><br>";
    $moduleInstance->addBlock($blockInstance8);
}
$field20 = Vtiger_Field::getInstance('hourlysimple_rate', $moduleInstance);
if ($field20) {
    echo "hourlysimple_rate field already exists<br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_TARIFFSERVICES_RATE';
    $field20->name = 'hourlysimple_rate';
    $field20->table = 'vtiger_tariffservices';
    $field20->column = 'hourlysimple_rate';
    $field20->columntype = 'DECIMAL(10,2)';
    $field20->uitype = 71;
    $field20->typeofdata = 'N~O';

    $blockInstance8->addField($field20);
}

$blockInstance9 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CUFTPERDAY', $moduleInstance);
if ($blockInstance9) {
    echo "<h2>LBL_TARIFFSERVICES_CUFTPERDAY block already exists</h2><br>";
} else {
    $blockInstance9 = new Vtiger_Block();
    $blockInstance9->label = 'LBL_TARIFFSERVICES_CUFTPERDAY';
    echo "<h2>Creating LBL_TARIFFSERVICES_CUFTPERDAY block</h2><br>";
    $moduleInstance->addBlock($blockInstance9);
}
$field21 = Vtiger_Field::getInstance('cuftperday_rate', $moduleInstance);
if ($field21) {
    echo "cuftperday_rate field already exists<br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_TARIFFSERVICES_RATE';
    $field21->name = 'cuftperday_rate';
    $field21->table = 'vtiger_tariffservices';
    $field21->column = 'cuftperday_rate';
    $field21->columntype = 'DECIMAL(10,2)';
    $field21->uitype = 71;
    $field21->typeofdata = 'N~O';

    $blockInstance9->addField($field21);
}

$blockInstance10 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CUFTPERMONTH', $moduleInstance);
if ($blockInstance10) {
    echo "<h2>LBL_TARIFFSERVICES_CUFTPERMONTH block already exists</h2><br>";
} else {
    $blockInstance10 = new Vtiger_Block();
    $blockInstance10->label = 'LBL_TARIFFSERVICES_CUFTPERMONTH';
    echo "<h2>Creating LBL_TARIFFSERVICES_CUFTPERMONTH block</h2><br>";
    $moduleInstance->addBlock($blockInstance10);
}
$field22 = Vtiger_Field::getInstance('cuftpermonth_rate', $moduleInstance);
if ($field22) {
    echo "cuftpermonth_rate field already exists<br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_TARIFFSERVICES_RATE';
    $field22->name = 'cuftpermonth_rate';
    $field22->table = 'vtiger_tariffservices';
    $field22->column = 'cuftpermonth_rate';
    $field22->columntype = 'DECIMAL(10,2)';
    $field22->uitype = 71;
    $field22->typeofdata = 'N~O';

    $blockInstance10->addField($field22);
}
$blockInstance11 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWT', $moduleInstance);
if ($blockInstance11) {
    echo "<h2>LBL_TARIFFSERVICES_CWT block already exists</h2><br>";
} else {
    $blockInstance11 = new Vtiger_Block();
    $blockInstance11->label = 'LBL_TARIFFSERVICES_CWT';
    echo "<h2>Creating LBL_TARIFFSERVICES_CWT block</h2><br>";
    $moduleInstance->addBlock($blockInstance11);
}
$field23 = Vtiger_Field::getInstance('cwt_rate', $moduleInstance);
if ($field23) {
    echo "cwt_rate field already exists<br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_TARIFFSERVICES_RATE';
    $field23->name = 'cwt_rate';
    $field23->table = 'vtiger_tariffservices';
    $field23->column = 'cwt_rate';
    $field23->columntype = 'DECIMAL(10,2)';
    $field23->uitype = 71;
    $field23->typeofdata = 'N~O';

    $blockInstance11->addField($field23);
}

$blockInstance12 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWTPERDAY', $moduleInstance);
if ($blockInstance12) {
    echo "<h2>LBL_TARIFFSERVICES_CWTPERDAY block already exists</h2><br>";
} else {
    $blockInstance12 = new Vtiger_Block();
    $blockInstance12->label = 'LBL_TARIFFSERVICES_CWTPERDAY';
    echo "<h2>Creating LBL_TARIFFSERVICES_CWTPERDAY block</h2><br>";
    $moduleInstance->addBlock($blockInstance12);
}
$field24 = Vtiger_Field::getInstance('cwtperday_rate', $moduleInstance);
if ($field24) {
    echo "cwtperday_rate field already exists<br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_TARIFFSERVICES_RATE';
    $field24->name = 'cwtperday_rate';
    $field24->table = 'vtiger_tariffservices';
    $field24->column = 'cwtperday_rate';
    $field24->columntype = 'DECIMAL(10,2)';
    $field24->uitype = 71;
    $field24->typeofdata = 'N~O';

    $blockInstance12->addField($field24);
}

$blockInstance13 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWTPERMONTH', $moduleInstance);
if ($blockInstance13) {
    echo "<h2>LBL_TARIFFSERVICES_CWTPERMONTH block already exists</h2><br>";
} else {
    $blockInstance13 = new Vtiger_Block();
    $blockInstance13->label = 'LBL_TARIFFSERVICES_CWTPERMONTH';
    echo "<h2>Creating LBL_TARIFFSERVICES_CWTPERMONTH block</h2><br>";
    $moduleInstance->addBlock($blockInstance13);
}
$field25 = Vtiger_Field::getInstance('cwtpermonth_rate', $moduleInstance);
if ($field25) {
    echo "cwtpermonth_rate field already exists<br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_TARIFFSERVICES_RATE';
    $field25->name = 'cwtpermonth_rate';
    $field25->table = 'vtiger_tariffservices';
    $field25->column = 'cwtpermonth_rate';
    $field25->columntype = 'DECIMAL(10,2)';
    $field25->uitype = 71;
    $field25->typeofdata = 'N~O';

    $blockInstance13->addField($field25);
}

$blockInstance14 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_QTY', $moduleInstance);
if ($blockInstance14) {
    echo "<h2>LBL_TARIFFSERVICES_QTY block already exists</h2><br>";
} else {
    $blockInstance14 = new Vtiger_Block();
    $blockInstance14->label = 'LBL_TARIFFSERVICES_QTY';
    echo "<h2>Creating LBL_TARIFFSERVICES_QTY block</h2><br>";
    $moduleInstance->addBlock($blockInstance14);
}
$field26 = Vtiger_Field::getInstance('qty_rate', $moduleInstance);
if ($field26) {
    echo "qty_rate field already exists<br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_TARIFFSERVICES_RATE';
    $field26->name = 'qty_rate';
    $field26->table = 'vtiger_tariffservices';
    $field26->column = 'qty_rate';
    $field26->columntype = 'DECIMAL(10,2)';
    $field26->uitype = 71;
    $field26->typeofdata = 'N~O';

    $blockInstance14->addField($field26);
}

$blockInstance15 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_QTYPERDAY', $moduleInstance);
if ($blockInstance15) {
    echo "<h2>LBL_TARIFFSERVICES_QTYPEPERDAY block already exists</h2><br>";
} else {
    $blockInstance15 = new Vtiger_Block();
    $blockInstance15->label = 'LBL_TARIFFSERVICES_QTYPERDAY';
    echo "<h2>Creating LBL_TARIFFSERVICES_QTYPEPERDAY block</h2><br>";
    $moduleInstance->addBlock($blockInstance15);
}
$field27 = Vtiger_Field::getInstance('qtyperday_rate', $moduleInstance);
if ($field27) {
    echo "qtyperday_rate field already exists<br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_TARIFFSERVICES_RATE';
    $field27->name = 'qtyperday_rate';
    $field27->table = 'vtiger_tariffservices';
    $field27->column = 'qtyperday_rate';
    $field27->columntype = 'DECIMAL(10,2)';
    $field27->uitype = 71;
    $field27->typeofdata = 'N~O';

    $blockInstance15->addField($field27);
}

$blockInstance16 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_QTYPERMONTH', $moduleInstance);
if ($blockInstance16) {
    echo "<h2>LBL_TARIFFSERVICES_QTYPERMONTH block already exists</h2><br>";
} else {
    $blockInstance16 = new Vtiger_Block();
    $blockInstance16->label = 'LBL_TARIFFSERVICES_QTYPERMONTH';
    echo "<h2>Creating LBL_TARIFFSERVICES_QTYPERMONTH block</h2><br>";
    $moduleInstance->addBlock($blockInstance16);
}
$field28 = Vtiger_Field::getInstance('qtypermonth_rate', $moduleInstance);
if ($field28) {
    echo "qtypermonth_rate field already exists<br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_TARIFFSERVICES_RATE';
    $field28->name = 'qtypermonth_rate';
    $field28->table = 'vtiger_tariffservices';
    $field28->column = 'qtypermonth_rate';
    $field28->columntype = 'DECIMAL(10,2)';
    $field28->uitype = 71;
    $field28->typeofdata = 'N~O';

    $blockInstance16->addField($field28);
}

$blockInstance17 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_BASEPLUS', $moduleInstance);
if ($blockInstance17) {
    echo "<h2>LBL_TARIFFSERVICES_BASEPLUS block already exists</h2><br>";
} else {
    $blockInstance17 = new Vtiger_Block();
    $blockInstance17->label = 'LBL_TARIFFSERVICES_BASEPLUS';
    echo "Creating LBL_TARIFFSERVICES_BASEPLUS block</h2><br>";
    $moduleInstance->addBlock($blockInstance17);
}

$blockInstance18 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_BREAKPOINT', $moduleInstance);
if ($blockInstance18) {
    echo "<h2>LBL_TARIFFSERVICES_BREAKPOINT block already exists</h2><br>";
} else {
    $blockInstance18 = new Vtiger_Block();
    $blockInstance18->label = 'LBL_TARIFFSERVICES_BREAKPOINT';
    echo "<h2>Creating LBL_TARIFFSERVICES_BREAKPOINT block</h2><br>";
    $moduleInstance->addBlock($blockInstance18);
}
$blockInstance19 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_WEIGHTMILEAGE', $moduleInstance);
if ($blockInstance19) {
    echo "<h2>LBL_TARIFFSERVICES_WEIGHTMILEAGE block already exists</h2><br>";
} else {
    $blockInstance19 = new Vtiger_Block();
    $blockInstance19->label = 'LBL_TARIFFSERVICES_WEIGHTMILEAGE';
    echo "<h2>Creating LBL_TARIFFSERVICES_WEIGHTMILEAGE block</h2><br>";
    $moduleInstance->addBlock($blockInstance19);
}
$blockInstance20 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_BULKY', $moduleInstance);
if ($blockInstance20) {
    echo "<h2>LBL_TARIFFSERVICES_BULKY block already exists</h2><br>";
} else {
    $blockInstance20 = new Vtiger_Block();
    $blockInstance20->label = 'LBL_TARIFFSERVICES_BULKY';
    echo "<h2>Creating LBL_TARIFFSERVICES_BULKY block</h2><br>";
    $moduleInstance->addBlock($blockInstance20);
}
$field29 = Vtiger_Field::getInstance('bulky_chargeper', $moduleInstance);
if ($field29) {
    echo "bulky_chargeper field already exists<br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_TARIFFSERVICES_CHARGEPER';
    $field29->name = 'bulky_chargeper';
    $field29->table = 'vtiger_tariffservices';
    $field29->column = 'bulky_chargeper';
    $field29->columntype = 'VARCHAR(255)';
    $field29->uitype = 15;
    $field29->typeofdata = 'V~O';

    $blockInstance20->addField($field29);

    $field29->setPicklistValues(array('Quantity', 'Hourly'));
}

$blockInstance21 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_COUNTYCHARGE', $moduleInstance);
if ($blockInstance21) {
    echo "<h2>LBL_TARIFFSERVICES_COUNTYCHARGE block already exists</h2><br>";
} else {
    $blockInstance21 = new Vtiger_Block();
    $blockInstance21->label = 'LBL_TARIFFSERVICES_COUNTYCHARGE';
    echo "<h2>LBL_TARIFFSERVICES_COUNTYCHARGE block already exists</h2><br>";
    $moduleInstance->addBlock($blockInstance21);
}

$blockInstance22 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_PACKING', $moduleInstance);
if ($blockInstance22) {
    echo "<h2>LBL_TARIFFSERVICES_PACKING block already exists</h2><br>";
} else {
    $blockInstance22 = new Vtiger_Block();
    $blockInstance22->label = 'LBL_TARIFFSERVICES_PACKING';
    echo "<h2>Creating LBL_TARIFFSERVICES_PACKING block</h2><br>";
    $moduleInstance->addBlock($blockInstance22);
}
$field30 = Vtiger_Field::getInstance('packing_containers', $moduleInstance);
if ($field30) {
    echo "packing_containers field already exists<br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_TARIFFSERVICES_HASCONTAINERS';
    $field30->name = 'packing_containers';
    $field30->table = 'vtiger_tariffservices';
    $field30->column = 'packing_containers';
    $field30->columntype = 'VARCHAR(3)';
    $field30->uitype = 56;
    $field30->typeofdata = 'C~O';

    $blockInstance22->addField($field30);
}
$field31 = Vtiger_Field::getInstance('packing_haspacking', $moduleInstance);
if ($field31) {
    echo "packing_haspacking field already exists<br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_TARIFFSERVICES_HASPACKING';
    $field31->name = 'packing_haspacking';
    $field31->table = 'vtiger_tariffservices';
    $field31->column = 'packing_haspacking';
    $field31->columntype = 'VARCHAR(3)';
    $field31->uitype = 56;
    $field31->typeofdata = 'C~O';

    $blockInstance22->addField($field31);
}
$field32 = Vtiger_Field::getInstance('packing_hasunpacking', $moduleInstance);
if ($field32) {
    echo "packing_hasunpacking field already exists<br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_TARIFFSERVICES_HASUNPACKING';
    $field32->name = 'packing_hasunpacking';
    $field32->table = 'vtiger_tariffservices';
    $field32->column = 'packing_hasunpacking';
    $field32->columntype = 'VARCHAR(3)';
    $field32->uitype = 56;
    $field32->typeofdata = 'C~O';

    $blockInstance22->addField($field32);
}
$field33 = Vtiger_Field::getInstance('packing_salestax', $moduleInstance);
if ($field33) {
    echo "packing_salestax field already exists<br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_TARIFFSERVICES_SALESTAX';
    $field33->name = 'packing_salestax';
    $field33->table = 'vtiger_tariffservices';
    $field33->column = 'packing_salestax';
    $field33->columntype = 'DECIMAL(7,3)';
    $field33->uitype = 9;
    $field33->typeofdata = 'N~O';

    $blockInstance22->addField($field33);
}

$blockInstance23 = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_VALUATION', $moduleInstance);
if ($blockInstance23) {
    echo "<h2>LBL_TARIFFSERVICES_VALUATION block already exists</h2><br>";
} else {
    $blockInstance23 = new Vtiger_Block();
    $blockInstance23->label = 'LBL_TARIFFSERVICES_VALUATION';
    echo "Creating LBL_TARIFFSERVICES_VALUATION block</h2><br>";
    $moduleInstance->addBlock($blockInstance23);
}
$field34 = Vtiger_Field::getInstance('valuation_released', $moduleInstance);
if ($field34) {
    echo "valuation_released field already exists<br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_TARIFFSERVICES_RELEASEDVAL';
    $field34->name = 'valuation_released';
    $field34->table = 'vtiger_tariffservices';
    $field34->column = 'valuation_released';
    $field34->columntype = 'VARCHAR(3)';
    $field34->uitype = 56;
    $field34->typeofdata = 'C~O';

    $blockInstance23->addField($field34);
}

$field35 = Vtiger_Field::getInstance('valuation_releasedamount', $moduleInstance);
if ($field35) {
    echo "valuation_releasedamount field already exists<br>";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_TARIFFSERVICES_RELEASEDVALAMOUNT';
    $field35->name = 'valuation_releasedamount';
    $field35->table = 'vtiger_tariffservices';
    $field35->column = 'valuation_releasedamount';
    $field35->columntype = 'DECIMAL(10,2)';
    $field35->uitype = 71;
    $field35->typeofdata = 'N~O';

    $blockInstance23->addField($field35);
}
$field36 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field36) {
    echo "assigned_user_id field already exists<br>";
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
$field37 = Vtiger_Field::getInstance('CreatedTime', $moduleInstance);
if ($field37) {
    echo "CreatedTime field already exists<br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'CreatedTime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $blockInstance->addField($field37);
}
$field38 = Vtiger_Field::getInstance('ModifiedTime', $moduleInstance);
if ($field38) {
    echo "ModifiedTime field already exists<br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'ModifiedTime';
    $field38->table = 'vtiger_crmentity';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $blockInstance->addField($field38);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4);

    $moduleInstance->setDefaultSharing();

    $moduleInstance->initWebservice();

    $dateInstance = Vtiger_Module::getInstance('EffectiveDates');
    $relationLabel = 'Tariff Services';
    $dateInstance->setRelatedList($moduleInstance, $relationLabel, array('Add'));
}
