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


include_once('vtlib/Vtiger/Module.php');

//Set up the tab/module
unset($moduleInstance);
$moduleInstance = Vtiger_Module::getInstance('TariffReportSections');

echo '<br />Checking if Tariff Report Section module exists.<br />';

if ($moduleInstance) {
    echo '<br />Tariff Report Sections already exists.<br />';
} else {
    echo '<br />Tariff Report Sections does not exist. Creating it now:<br />';
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'TariffReportSections';
    $moduleInstance->save();
    $moduleInstance->initTables();
    Vtiger_Module::getInstance('Tariffs')->setRelatedList($moduleInstance, 'Report Sections', array('ADD'), 'get_related_list');
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    echo '<br />Tariff Report Sections created!<br />';
}

//Set up the block
unset($blockInstance);
$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFORDERSECTIONS', $moduleInstance);

echo('<br />Checking if LBL_TARIFFORDERSECTIONS block exists.<br />');

if ($blockInstance) {
    echo('<br />LBL_TARIFFORDERSECTIONS block already exists.<br />');
} else {
    echo('<br />LBL_TARIFFORDERSECTIONS block does not exists. Creating it now:<br />');
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFORDERSECTIONS';
    $moduleInstance->addBlock($blockInstance);
    echo('<br />LBL_TARIFFORDERSECTIONS block created!<br />');
}

//Setup the fields
unset($field1);
$field1 = Vtiger_Field::getInstance('tariff_orders_type', $moduleInstance);
echo 'Checkig if tariff_orders_type field exists.';
if ($field1) {
    echo '<br>tariff_orders_type field exists.<br>';
} else {
    echo "<br>Creating orders_type field:<br>";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TARIFFORDERSECTIONS_TYPE';
    $field1->name = 'tariff_orders_type';
    $field1->table = 'vtiger_tariffreportsections';
    $field1->column = 'tariff_orders_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
        
    $blockInstance->addField($field1);
    $field1->setPicklistValues(array('Binding', 'Non-Binding', 'Do Not Exceed'));
    echo "<br>orders_type field created!<br>";
}

unset($field2);
$field2 = Vtiger_Field::getInstance('tariff_orders_title', $moduleInstance);
echo 'Checkig if tariff_orders_title field exists.';
if ($field2) {
    echo '<br>tariff_orders_title field exists.<br>';
} else {
    echo "<br>Creating tariff_orders_title field:<br>";
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TARIFFORDERSECTIONS_TITLE';
    $field2->name = 'tariff_orders_title';
    $field2->table = 'vtiger_tariffreportsections';
    $field2->column = 'tariff_orders_title';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
        
    $blockInstance->addField($field2);
    $moduleInstance->setEntityIdentifier($field2);
    echo "<br>tariff_orders_title field created!<br>";
}

unset($field3);
$field3 = Vtiger_Field::getInstance('tariff_orders_description', $moduleInstance);
echo 'Checkig if tariff_orders_description field exists.';
if ($field3) {
    echo '<br>tariff_orders_description field exists.<br>';
} else {
    echo "<br>Creating tariff_orders_description field:<br>";
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TARIFFORDERSECTIONS_DESCRIPTION';
    $field3->name = 'tariff_orders_description';
    $field3->table = 'vtiger_tariffreportsections';
    $field3->column = 'tariff_orders_description';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
        
    $blockInstance->addField($field3);
    echo "<br>tariff_orders_description field created!<br>";
}

unset($field4);
$field4 = Vtiger_Field::getInstance('tariff_orders_tariff', $moduleInstance);
echo 'Checkig if tariff_orders_tariff field exists.';
if ($field4) {
    echo '<br>tariff_orders_tariff field exists.<br>';
} else {
    echo "<br>Creating tariff_orders_tariff field:<br>";
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TARIFFORDERSECTIONS_TARIFF';
    $field4->name = 'tariff_orders_tariff';
    $field4->table = 'vtiger_tariffreportsections';
    $field4->column = 'tariff_orders_tariff';
    $field4->columntype = 'INT(19)';
    $field4->uitype = 10;
    $field4->typeofdata = 'V~M';
        
    $blockInstance->addField($field4);
    $field4->setRelatedModules(array('Tariffs'));
    echo "<br>tariff_orders_tariff field created!<br>";
}

unset($field6);
$field6 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
echo 'Checkig if assigned_user_id field exists.';
if ($field6) {
    echo '<br>assigned_user_id field exists.<br>';
} else {
    echo "<br>Creating assigned_user_id field:<br>";
    $field6 = new Vtiger_Field();
    $field6->label = 'Assigned To';
    $field6->name = 'assigned_user_id';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'smownerid';
    $field6->uitype = 53;
    $field6->typeofdata = 'V~M';

    $blockInstance->addField($field6);
    echo "<br>assigned_user_id field created!<br>";
}

unset($field7);
$field7 = Vtiger_Field::getInstance('tariff_orders_body', $moduleInstance);
echo '<br>Checkig if tariff_orders_body field exists.<br>';
if ($field7) {
    echo '<br>tariff_orders_body field exists.<br>';
} else {
    echo "<br>Creating tariff_orders_body field:<br>";
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_TARIFFORDERSECTIONS_BODY';
    $field7->name = 'tariff_orders_body';
    $field7->table = 'vtiger_tariffreportsections';
    $field7->column = 'tariff_orders_body';
    $field7->columntype = 'TEXT';
    $field7->uitype = 19;
    $field7->typeofdata = 'V~O';
        
    $blockInstance->addField($field7);
    echo "<br>tariff_orders_body field created.<br>";
}

unset($field8);
$field8 = Vtiger_Field::getInstance('CreatedTime', $moduleInstance);
echo 'Checkig if CreatedTime field exists.';
if ($field8) {
    echo '<br>CreatedTime field exists.<br>';
} else {
    echo "<br>Creating CreatedTime field:<br>";
    $field8 = new Vtiger_Field();
    $field8->label = 'Created Time';
    $field8->name = 'CreatedTime';
    $field8->table = 'vtiger_crmentity';
    $field8->column = 'createdtime';
    $field8->uitype = 70;
    $field8->typeofdata = 'T~O';
    $field8->displaytype = 2;

    $blockInstance->addField($field8);
    echo "<br>CreatedTime field created.<br>";
}

unset($field9);
$field9 = Vtiger_Field::getInstance('ModifiedTime', $moduleInstance);
echo 'Checkig if ModifiedTime field exists.';
if ($field9) {
    echo '<br>ModifiedTime field exists.<br>';
} else {
    echo "<br>Creating ModifiedTime field:<br>";
    $field9 = new Vtiger_Field();
    $field9->label = 'Modified Time';
    $field9->name = 'ModifiedTime';
    $field9->table = 'vtiger_crmentity';
    $field9->column = 'modifiedtime';
    $field9->uitype = 70;
    $field9->typeofdata = 'T~O';
    $field9->displaytype = 2;

    $blockInstance->addField($field9);
    echo "<br>ModifiedTime field created.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";