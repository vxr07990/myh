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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$estimateInstance = Vtiger_Module::getInstance('Estimates');

$estimateDateBlock = Vtiger_Block::getInstance('LBL_ESTIMATES_DATES', $estimateInstance);
if ($estimateDateBlock) {
    echo "<li>The LBL_ESTIMATES_DATES field already exists</li><br>";
} else {
    $estimateDateBlock = new Vtiger_Block();
    $estimateDateBlock->label = 'LBL_ESTIMATES_DATES';
    $estimateInstance->addBlock($estimateDateBlock);
}

$field41 = Vtiger_Field::getInstance('pack_date', $estimateInstance);
if ($field41) {
    echo "<li>The pack_date field already exists</li><br>";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_ESTIMATES_PACK';
    $field41->name = 'pack_date';
    $field41->table = 'vtiger_quotes';
    $field41->column = 'pack_date';
    $field41->columntype='DATE';
    $field41->uitype = 5;
    $field41->typeofdata = 'D~O';
    $field41->displaytype = 1;
    $field41->quickcreate = 1;
    
    $estimateDateBlock->addField($field41);
}

$field42 = Vtiger_Field::getInstance('pack_to_date', $estimateInstance);
if ($field42) {
    echo "<li>The pack_to_date field already exists</li><br>";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'LBL_ESTIMATES_PACKTO';
    $field42->name = 'pack_to_date';
    $field42->table = 'vtiger_quotes';
    $field42->column = 'pack_to_date';
    $field42->columntype='DATE';
    $field42->uitype = 5;
    $field42->typeofdata = 'D~O';
    $field42->displaytype = 1;
    $field42->quickcreate = 1;
    
    $estimateDateBlock->addField($field42);
}

$field042 = Vtiger_Field::getInstance('preffered_ppdate', $estimateInstance);
if ($field042) {
    echo "<li>The preffered_ppdate field already exists</li><br>";
} else {
    $field042 = new Vtiger_Field();
    $field042->label = 'LBL_ESTIMATES_PPDATE';
    $field042->name = 'preffered_ppdate';
    $field042->table = 'vtiger_quotes';
    $field042->column = 'preffered_ppdate';
    $field042->columntype='DATE';
    $field042->uitype = 5;
    $field042->typeofdata = 'D~O';
    $field042->displaytype = 1;
    $field042->quickcreate = 1;
    
    $estimateDateBlock->addField($field042);
}
$field43 = Vtiger_Field::getInstance('load_date', $estimateInstance);
if ($field43) {
    echo "<li>The load_date field already exists</li><br>";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'LBL_ESTIMATES_LOAD';
    $field43->name = 'load_date';
    $field43->table = 'vtiger_quotes';
    $field43->column = 'load_date';
    $field43->columntype='DATE';
    $field43->uitype = 5;
    $field43->typeofdata = 'D~O';
    $field43->displaytype = 1;
    $field43->quickcreate = 1;
    
    $estimateDateBlock->addField($field43);
}

$field44 = Vtiger_Field::getInstance('load_to_date', $estimateInstance);
if ($field44) {
    echo "<li>The load_to_date field already exists</li><br>";
} else {
    $field44 = new Vtiger_Field();
    $field44->label = 'LBL_ESTIMATES_LOADTO';
    $field44->name = 'load_to_date';
    $field44->table = 'vtiger_quotes';
    $field44->column = 'load_to_date';
    $field44->columntype='DATE';
    $field44->uitype = 5;
    $field44->typeofdata = 'D~O';
    $field44->displaytype = 1;
    $field44->quickcreate = 1;
    
    $estimateDateBlock->addField($field44);
}

$field044 = Vtiger_Field::getInstance('preferred_pldate', $estimateInstance);
if ($field044) {
    echo "<li>The preferred_pldate field already exists</li><br>";
} else {
    $field044 = new Vtiger_Field();
    $field044->label = 'LBL_ESTIMATES_PLDATE';
    $field044->name = 'preferred_pldate';
    $field044->table = 'vtiger_quotes';
    $field044->column = 'preferred_pldate';
    $field044->columntype='DATE';
    $field044->uitype = 5;
    $field044->typeofdata = 'D~O';
    $field044->displaytype = 1;
    $field044->quickcreate = 1;
    
    $estimateDateBlock->addField($field044);
}

$field45 = Vtiger_Field::getInstance('deliver_date', $estimateInstance);
if ($field45) {
    echo "<li>The deliver_date field already exists</li><br>";
} else {
    $field45 = new Vtiger_Field();
    $field45->label = 'LBL_ESTIMATES_DELIVER';
    $field45->name = 'deliver_date';
    $field45->table = 'vtiger_quotes';
    $field45->column = 'deliver_date';
    $field45->columntype='DATE';
    $field45->uitype = 5;
    $field45->typeofdata = 'D~O';
    $field45->displaytype = 1;
    $field45->quickcreate = 1;
    
    $estimateDateBlock->addField($field45);
}

$field46 = Vtiger_Field::getInstance('deliver_to_date', $estimateInstance);
if ($field46) {
    echo "<li>The deliver_to_date field already exists</li><br>";
} else {
    $field46 = new Vtiger_Field();
    $field46->label = 'LBL_ESTIMATES_DELIVERTO';
    $field46->name = 'deliver_to_date';
    $field46->table = 'vtiger_quotes';
    $field46->column = 'deliver_to_date';
    $field46->columntype='DATE';
    $field46->uitype = 5;
    $field46->typeofdata = 'D~O';
    $field46->displaytype = 1;
    $field46->quickcreate = 1;
    
    $estimateDateBlock->addField($field46);
}

$field046 = Vtiger_Field::getInstance('preferred_pddate', $estimateInstance);
if ($field046) {
    echo "<li>The preferred_pddate field already exists</li><br>";
} else {
    $field046 = new Vtiger_Field();
    $field046->label = 'LBL_ESTIMATES_PDDATE';
    $field046->name = 'preferred_pddate';
    $field046->table = 'vtiger_quotes';
    $field046->column = 'preferred_pddate';
    $field046->columntype='DATE';
    $field046->uitype = 5;
    $field046->typeofdata = 'D~O';
    $field046->displaytype = 1;
    $field046->quickcreate = 1;
    
    $estimateDateBlock->addField($field046);
}

$field47 = Vtiger_Field::getInstance('survey_date', $estimateInstance);
if ($field47) {
    echo "<li>The survey_date field already exists</li><br>";
} else {
    $field47 = new Vtiger_Field();
    $field47->label = 'LBL_ESTIMATES_SURVEY';
    $field47->name = 'survey_date';
    $field47->table = 'vtiger_quotes';
    $field47->column = 'survey_date';
    $field47->columntype='DATE';
    $field47->uitype = 5;
    $field47->typeofdata = 'D~O';
    $field47->displaytype = 1;
    $field47->quickcreate = 1;
    
    $estimateDateBlock->addField($field47);
}

$field48 = Vtiger_Field::getInstance('survey_time', $estimateInstance);
if ($field48) {
    echo "<li>The survey_time field already exists</li><br>";
} else {
    $field48 = new Vtiger_Field();
    $field48->label = 'LBL_ESTIMATES_SURVEYTIME';
    $field48->name = 'survey_time';
    $field48->table = 'vtiger_quotes';
    $field48->column = 'survey_time';
    $field48->columntype='TIME';
    $field48->uitype = 14;
    $field48->typeofdata = 'T~O';
    $field48->displaytype = 1;
    $field48->quickcreate = 1;
    
    $estimateDateBlock->addField($field48);
}

$field49 = Vtiger_Field::getInstance('followup_date', $estimateInstance);
if ($field49) {
    echo "<li>The followup_date field already exists</li><br>";
} else {
    $field49 = new Vtiger_Field();
    $field49->label = 'LBL_ESTIMATES_FOLLOWUP';
    $field49->name = 'followup_date';
    $field49->table = 'vtiger_quotes';
    $field49->column = 'followup_date';
    $field49->columntype='DATE';
    $field49->uitype = 5;
    $field49->typeofdata = 'D~O';
    $field49->displaytype = 1;
    $field49->quickcreate = 1;
    
    $estimateDateBlock->addField($field49);
}

$field049 = Vtiger_Field::getInstance('decision_date', $estimateInstance);
if ($field049) {
    echo "<li>The decision_date field already exists</li><br>";
} else {
    $field049 = new Vtiger_Field();
    $field049->label = 'LBL_ESTIMATES_DECISION';
    $field049->name = 'decision_date';
    $field049->table = 'vtiger_quotes';
    $field049->column = 'decision_date';
    $field049->columntype='DATE';
    $field049->uitype = 5;
    $field049->typeofdata = 'D~O';
    $field049->displaytype = 1;
    $field049->quickcreate = 1;
    
    $estimateDateBlock->addField($field049);
}

$field050 = Vtiger_Field::getInstance('days_to_move', $estimateInstance);
if ($field050) {
    echo "<br> leads days_to_move field already exists.<br>";
} else {
    echo "<br> leads days_to_move field doesn't exist, adding it now.<br>";
    $field050 = new Vtiger_Field();
    $field050->label = 'LBL_LEADS_DAYSTOMOVE';
    $field050->name = 'days_to_move';
    $field050->table = 'vtiger_quotes';
    $field050->column = 'days_to_move';
    $field050->columntype = 'VARCHAR(255)';
    $field050->uitype = 1;
    $field050->typeofdata = 'V~O';
    $field050->displaytype = 1;
    $field050->quickcreate = 0;

    $estimateDateBlock->addField($field050);
    echo "<br> leads days_to_move field added.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";