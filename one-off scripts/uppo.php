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
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

$potentialsInstance = Vtiger_Module::getInstance('Potentials');


$opportunitiesblock3 = Vtiger_Block::getInstance('LBL_POTENTIALS_DATES', $opportunitiesInstance);
if ($opportunitiesblock3) {
    echo "<li>The LBL_POTENTIALS_DATES field already exists</li><br>";
} else {
    $opportunitiesblock3 = new Vtiger_Block();
    $opportunitiesblock3->label = 'LBL_POTENTIALS_DATES';
    $opportunitiesInstance->addBlock($opportunitiesblock3);
}

$field042 = Vtiger_Field::getInstance('preffered_ppdate', $opportunitiesInstance);
if ($field042) {
    echo "<li>The preffered_ppdate field already exists</li><br>";
} else {
    $field042 = new Vtiger_Field();
    $field042->label = 'LBL_POTENTIAL_PPDATE';
    $field042->name = 'preffered_ppdate';
    $field042->table = 'vtiger_potentialscf';
    $field042->column = 'preffered_ppdate';
    $field042->columntype='DATE';
    $field042->uitype = 5;
    $field042->typeofdata = 'D~O';
    $field042->displaytype = 1;
    $field042->quickcreate = 1;
    
    $opportunitiesblock3->addField($field042);
}

$field044 = Vtiger_Field::getInstance('preferred_pldate', $opportunitiesInstance);
if ($field044) {
    echo "<li>The preferred_pldate field already exists</li><br>";
} else {
    $field044 = new Vtiger_Field();
    $field044->label = 'LBL_POTENTIAL_PLDATE';
    $field044->name = 'preferred_pldate';
    $field044->table = 'vtiger_potentialscf';
    $field044->column = 'preferred_pldate';
    $field044->columntype='DATE';
    $field044->uitype = 5;
    $field044->typeofdata = 'D~O';
    $field044->displaytype = 1;
    $field044->quickcreate = 1;
    
    $opportunitiesblock3->addField($field044);
}

$field046 = Vtiger_Field::getInstance('preferred_pddate', $opportunitiesInstance);
if ($field046) {
    echo "<li>The preferred_pddate field already exists</li><br>";
} else {
    $field046 = new Vtiger_Field();
    $field046->label = 'LBL_POTENTIAL_DELIVER';
    $field046->name = 'preferred_pddate';
    $field046->table = 'vtiger_potentialscf';
    $field046->column = 'preferred_pddate';
    $field046->columntype='DATE';
    $field046->uitype = 5;
    $field046->typeofdata = 'D~O';
    $field046->displaytype = 1;
    $field046->quickcreate = 1;
    
    $opportunitiesblock3->addField($field046);
}


$potentialsblock11 = Vtiger_Block::getInstance('LBL_POTENTIALS_NATIONALACCOUNT', $potentialsInstance);
if ($potentialsblock11) {
    echo "<li>The LBL_POTENTIALS_NATIONALACCOUNT field already exists</li><br>";
} else {
    $potentialsblock11 = new Vtiger_Block();
    $potentialsblock11->label = 'LBL_POTENTIALS_NATIONALACCOUNT';
    $potentialsInstance->addBlock($potentialsblock11);
}

$field33 = Vtiger_Field::getInstance('street', $potentialsInstance);
if ($field33) {
    echo "<li>The street field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'LBL_POTENTIALS_STREET';
    $field33->name = 'street';
    $field33->table = 'vtiger_potential';
    $field33->column = 'street';
    $field33->columntype='VARCHAR(200)';
    $field33->uitype = 21;
    $field33->typeofdata = 'V~O';
    $field33->displaytype = 1;
    $field33->quickcreate = 1;

    $potentialsblock11->addField($field33);
}

$field34 = Vtiger_Field::getInstance('pobox', $potentialsInstance);
if ($field34) {
    echo "<li>The pobox field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_POTENTIALS_POBOX';
    $field34->name = 'pobox';
    $field34->table = 'vtiger_potential';
    $field34->column = 'pobox';
    $field34->columntype='VARCHAR(200)';
    $field34->uitype = 1;
    $field34->typeofdata = 'V~O';
    $field34->displaytype = 1;
    $field34->quickcreate = 1;

    $potentialsblock11->addField($field34);
}

$field35 = Vtiger_Field::getInstance('city', $potentialsInstance);
if ($field35) {
    echo "<li>The city field already exists</li><br>";
} else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_POTENTIALS_CITY';
    $field35->name = 'city';
    $field35->table = 'vtiger_potential';
    $field35->column = 'city';
    $field35->columntype='VARCHAR(200)';
    $field35->uitype = 1;
    $field35->typeofdata = 'V~O';
    $field35->displaytype = 1;
    $field35->quickcreate = 1;

    $potentialsblock11->addField($field35);
}

$field36 = Vtiger_Field::getInstance('zip', $potentialsInstance);
if ($field36) {
    echo "<li>The zip field already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_POTENTIALS_ZIP';
    $field36->name = 'zip';
    $field36->table = 'vtiger_potential';
    $field36->column = 'zip';
    $field36->columntype='VARCHAR(200)';
    $field36->uitype = 1;
    $field36->typeofdata = 'V~O';
    $field36->displaytype = 1;
    $field36->quickcreate = 1;

    $potentialsblock11->addField($field36);
}

$field38 = Vtiger_Field::getInstance('state', $potentialsInstance);
if ($field38) {
    echo "<li>The state field already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_POTENTIALS_STATE';
    $field38->name = 'state';
    $field38->table = 'vtiger_potential';
    $field38->column = 'state';
    $field38->columntype='VARCHAR(200)';
    $field38->uitype = 1;
    $field38->typeofdata = 'V~O';
    $field38->displaytype = 1;
    $field38->quickcreate = 1;

    $potentialsblock11->addField($field38);
}

$field37 = Vtiger_Field::getInstance('country', $potentialsInstance);
if ($field37) {
    echo "<li>The country field already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_POTENTIALS_COUNTRY';
    $field37->name = 'country';
    $field37->table = 'vtiger_potential';
    $field37->column = 'country';
    $field37->columntype='VARCHAR(200)';
    $field37->uitype = 1;
    $field37->typeofdata = 'V~O';
    $field37->displaytype = 1;
    $field37->quickcreate = 1;

    $potentialsblock11->addField($field37);
}
