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
$moduleInstance = Vtiger_Module::getInstance('AutoSpotQuote');

echo '<br />Checking if Auto Spot Quote module exists.<br />';

if ($moduleInstance) {
    echo '<br />Auto Spot Quote already exists.<br />';
} else {
    echo '<br />Auto Spot Quote does not exist. Creating it now:<br />';
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'AutoSpotQuote';
    $moduleInstance->save();
    $moduleInstance->initTables();
    Vtiger_Module::getInstance('Opportunities')->setRelatedList($moduleInstance, 'Auto Spot Quote', array('ADD'), 'get_related_list');
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    echo '<br />Auto Spot Quote created!<br />';
}

//Set up the block
unset($blockInstance);
$blockInstance = Vtiger_Block::getInstance('LBL_AUTOSPOTQUOTEDETAILS', $moduleInstance);

echo('<br />Checking if LBL_AUTOSPOTQUOTEDETAILS block exists.<br />');

if ($blockInstance) {
    echo('<br />LBL_AUTOSPOTQUOTEDETAILS block already exists.<br />');
} else {
    echo('<br />LBL_AUTOSPOTQUOTEDETAILS block does not exists. Creating it now:<br />');
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_AUTOSPOTQUOTEDETAILS';
    $moduleInstance->addBlock($blockInstance);
    echo('<br />LBL_AUTOSPOTQUOTEDETAILS block created!<br />');
}

//Setup the fields
unset($field1);
$field1 = Vtiger_Field::getInstance('auto_make', $moduleInstance);
echo 'Checkig if auto_make field exists.';
if ($field1) {
    echo '<br>auto_make field exists.<br>';
} else {
    echo "<br>Creating auto_make field:<br>";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_AUTOSPOTQUOTEMAKE';
    $field1->name = 'auto_make';
    $field1->table = 'vtiger_autospotquote';
    $field1->column = 'auto_make';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->summaryfield = 0;
    $field1->typeofdata = 'V~M';
        
    $blockInstance->addField($field1);

    $field1->setPicklistValues(['ATV']);
    $moduleInstance->setEntityIdentifier($field1);

    echo "<br>auto_make field created!<br>";
}

unset($field1);
$field1 = Vtiger_Field::getInstance('auto_model', $moduleInstance);
echo 'Checkig if auto_model field exists.';
if ($field1) {
    echo '<br>auto_model field exists.<br>';
} else {
    echo "<br>Creating auto_model field:<br>";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_AUTOSPOTQUOTEMODEL';
    $field1->name = 'auto_model';
    $field1->table = 'vtiger_autospotquote';
    $field1->column = 'auto_model';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->summaryfield = 0;
    $field1->typeofdata = 'V~M';
        
    $blockInstance->addField($field1);

    $field1->setPicklistValues(['100']);

    echo "<br>auto_model field created!<br>";
}

$field3 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_AUTOSPOTQUOTEASSIGNEDTO';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';
    $field3->summaryfield = 0;

    $blockInstance->addField($field3);
}

$field2 = Vtiger_Field::getInstance('potential_id', $moduleInstance);
if ($field2) {
    echo "<li>The potential_id field already exists</li><br> \n";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_AUTOSPOTQUOTEOPPORTUNITY';
    $field2->name = 'potential_id';
    $field2->table = 'vtiger_autospotquote';
    $field2->column = 'potential_id';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 0;

    $blockInstance->addField($field2);

    $field2->setRelatedModules(['Opportunities']);
}

$moduleAutoSpotQuote = Vtiger_Module::getInstance('AutoSpotQuote');

$blockAutoSpotQuote324 = Vtiger_Block::getInstance('LBL_AUTOSPOTQUOTEDETAILS', $moduleAutoSpotQuote);
if ($blockAutoSpotQuote324) {
    echo "<br> The LBL_AUTOSPOTQUOTEDETAILS block already exists in AutoSpotQuote <br>";
} else {
    $blockAutoSpotQuote324 = new Vtiger_Block();
    $blockAutoSpotQuote324->label = 'LBL_AUTOSPOTQUOTEDETAILS';
    $moduleAutoSpotQuote->addBlock($blockAutoSpotQuote324);
}

$field = Vtiger_Field::getInstance('auto_year', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_year field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_YEAR';
    $field->name = 'auto_year';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_year';
    $field->columntype = 'int(4)';
    $field->uitype = 7;
    $field->typeofdata = 'I~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAutoSpotQuote324->addField($field);
}
$field = Vtiger_Field::getInstance('auto_smf', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_smf field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_VEHICLESMF';
    $field->name = 'auto_smf';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_smf';
    $field->columntype = 'decimal(13,2)';
    $field->uitype = 71;
    $field->typeofdata = 'NN~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;
    $field->defaultvalue = 1000.00;

    $blockAutoSpotQuote324->addField($field);
}
$field = Vtiger_Field::getInstance('auto_rush_fee', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_rush_fee field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_RUSHFEE';
    $field->name = 'auto_rush_fee';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_rush_fee';
    $field->columntype = 'decimal(13,2)';
    $field->uitype = 71;
    $field->typeofdata = 'NN~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;
    $field->defaultvalue = 100.00;

    $blockAutoSpotQuote324->addField($field);
}
$field = Vtiger_Field::getInstance('auto_transport_type', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_transport_type field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_TRANSPORTTYPE';
    $field->name = 'auto_transport_type';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_transport_type';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;
    $field->defaultvalue = 'Open Trailer';

    $blockAutoSpotQuote324->addField($field);
    $field->setPicklistValues(['Open Trailer', 'Closed Trailer']);
}
$field = Vtiger_Field::getInstance('auto_condition', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_condition field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_CONDITION';
    $field->name = 'auto_condition';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_condition';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;
    $field->defaultvalue = 'Running';

    $blockAutoSpotQuote324->addField($field);
    $field->setPicklistValues(['Running', 'Not Running']);
}
$field = Vtiger_Field::getInstance('auto_load_from', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_load_from field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_LOADFROM';
    $field->name = 'auto_load_from';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_load_from';
    $field->columntype = 'date';
    $field->uitype = 5;
    $field->typeofdata = 'D~M';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAutoSpotQuote324->addField($field);
}
$field = Vtiger_Field::getInstance('auto_comment', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_comment field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_COMMENT';
    $field->name = 'auto_comment';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_comment';
    $field->columntype = 'varchar(255)';
    $field->uitype = 19;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAutoSpotQuote324->addField($field);
}

$block324 = Vtiger_Block::getInstance('LBL_AUTOSPOTQUOTEDETAILS', $moduleAutoSpotQuote);
$field2362 = Vtiger_Field::getInstance('auto_make', $moduleAutoSpotQuote);
$field2369 = Vtiger_Field::getInstance('auto_year', $moduleAutoSpotQuote);
$field2373 = Vtiger_Field::getInstance('auto_rush_fee', $moduleAutoSpotQuote);
$field2375 = Vtiger_Field::getInstance('auto_transport_type', $moduleAutoSpotQuote);
$field2377 = Vtiger_Field::getInstance('auto_condition', $moduleAutoSpotQuote);
$field2381 = Vtiger_Field::getInstance('auto_comment', $moduleAutoSpotQuote);
$field2367 = Vtiger_Field::getInstance('auto_model', $moduleAutoSpotQuote);
$field2371 = Vtiger_Field::getInstance('auto_smf', $moduleAutoSpotQuote);
$field2379 = Vtiger_Field::getInstance('auto_load_from', $moduleAutoSpotQuote);
$field2313 = Vtiger_Field::getInstance('assigned_user_id', $moduleAutoSpotQuote);
$field2363 = Vtiger_Field::getInstance('potential_id', $moduleAutoSpotQuote);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field2362->id." THEN 1 WHEN fieldid=".$field2369->id." THEN 3 WHEN fieldid=".$field2373->id." THEN 5 WHEN fieldid=".$field2375->id." THEN 7 WHEN fieldid=".$field2377->id." THEN 9 WHEN fieldid=".$field2381->id." THEN 11 WHEN fieldid=".$field2367->id." THEN 2 WHEN fieldid=".$field2371->id." THEN 4 WHEN fieldid=".$field2379->id." THEN 6 WHEN fieldid=".$field2313->id." THEN 8 WHEN fieldid=".$field2363->id." THEN 10 END, block=CASE WHEN fieldid=".$field2362->id." THEN ".$block324->id." WHEN fieldid=".$field2369->id." THEN ".$block324->id." WHEN fieldid=".$field2373->id." THEN ".$block324->id." WHEN fieldid=".$field2375->id." THEN ".$block324->id." WHEN fieldid=".$field2377->id." THEN ".$block324->id." WHEN fieldid=".$field2381->id." THEN ".$block324->id." WHEN fieldid=".$field2367->id." THEN ".$block324->id." WHEN fieldid=".$field2371->id." THEN ".$block324->id." WHEN fieldid=".$field2379->id." THEN ".$block324->id." WHEN fieldid=".$field2313->id." THEN ".$block324->id." WHEN fieldid=".$field2363->id." THEN ".$block324->id." END WHERE fieldid IN (".$field2362->id.",".$field2369->id.",".$field2373->id.",".$field2375->id.",".$field2377->id.",".$field2381->id.",".$field2367->id.",".$field2371->id.",".$field2379->id.",".$field2313->id.",".$field2363->id.")");
    
$field = Vtiger_Field::getInstance('auto_quote_info', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_quote_info field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_QUOTEINFO';
    $field->name = 'auto_quote_info';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_quote_info';
    $field->columntype = 'TEXT';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 3;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAutoSpotQuote324->addField($field);
}

$field = Vtiger_Field::getInstance('auto_quote_select', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_quote_select field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_QUOTEINFO';
    $field->name = 'auto_quote_select';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_quote_select';
    $field->columntype = 'int(2)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 3;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAutoSpotQuote324->addField($field);
}

$field = Vtiger_Field::getInstance('auto_quote_id', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The auto_quote_id field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_QUOTEINFO';
    $field->name = 'auto_quote_id';
    $field->table = 'vtiger_autospotquote';
    $field->column ='auto_quote_id';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 3;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockAutoSpotQuote324->addField($field);
}

//Make sure that field is for opps and not potentials ; Bug fix
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_fieldmodulerel` SET `relmodule` = 'Opportunities' WHERE relmodule = 'Potentials' AND `module` = 'AutoSpotQuote'");

//UPDATE `vtiger_field` SET `summaryfield` = 0 WHERE `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'AutoSpotQuote')
/*
$moduleOpportunities = Vtiger_Module::getInstance('Opportunities');
$modulePotentials = Vtiger_Module::getInstance('Potentials');

$blockOpportunities201 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleOpportunities);
if($blockOpportunities201) {
    echo "<br> The LBL_POTENTIALS_INFORMATION block already exists in Opportunities <br>";
}
else {
    $blockOpportunities201 = new Vtiger_Block();
    $blockOpportunities201->label = 'LBL_POTENTIALS_INFORMATION';
    $moduleOpportunities->addBlock($blockOpportunities201);
}

$blockPotentials201 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $modulePotentials);
if($blockPotentials201) {
    echo "<br> The LBL_OPPORTUNITY_INFORMATION block already exists in Potentials <br>";
}
else {
    $blockPotentials201 = new Vtiger_Block();
    $blockPotentials201->label = 'LBL_OPPORTUNITY_INFORMATION';
    $modulePotentials->addBlock($blockPotentials201);
}

$field = Vtiger_Field::getInstance('auto_sts_register', $moduleOpportunities);
if($field) {
    echo "<br> The auto_sts_register field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_AUTOREGISTERSTS';
    $field->name = 'auto_sts_register';
    $field->table = 'vtiger_potential';
    $field->column ='auto_sts_register';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 3;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities201->addField($field);
}
$field = Vtiger_Field::getInstance('auto_sts_register', $modulePotentials);
if($field) {
    echo "<br> The auto_sts_register field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_AUTOREGISTERSTS';
    $field->name = 'auto_sts_register';
    $field->table = 'vtiger_potential';
    $field->column ='auto_sts_register';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 3;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials201->addField($field);
}*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";