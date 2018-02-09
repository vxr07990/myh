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
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';


// Create LongCarries module
$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('LongCarries');

if($moduleInstance)
{
    echo "<h2>LongCarries already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'LongCarries';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew = true;
}

$blockInstance = Vtiger_Block::getInstance('LBL_LONGCARRY_INFORMATION',$moduleInstance);

if($blockInstance)
{
    echo "<h3>The LBL_LONGCARRY_INFORMATION block already exists</h3><br> \n";
}
else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_LONGCARRY_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//Long Carry Up To (Ft)
$field2 = Vtiger_Field::getInstance('longcarries_uptoft', $moduleInstance);
if($field2) {
    echo "<br> The longcarries_uptoft field already exists in LongCarries <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_LONG_CARRY_UP_TO_FT';
    $field2->name       = 'longcarries_uptoft';
    $field2->table      = 'vtiger_longcarries';
    $field2->column     = 'longcarries_uptoft';
    $field2->columntype = 'INT(20)';
    $field2->uitype     = 7;
    $field2->typeofdata = 'I~O';
    $blockInstance->addField($field2);
    $moduleInstance->setEntityIdentifier($field2);
}
//Percent
$field3 = Vtiger_Field::getInstance('longcarries_percent', $moduleInstance);
if($field3) {
    echo "<br> The longcarries_percent field already exists in LongCarries <br>";
} else {
    $field3             = new Vtiger_Field();
    $field3->label      = 'LBL_LONG_CARRY_PERCENT';
    $field3->name       = 'longcarries_percent';
    $field3->table      = 'vtiger_longcarries';
    $field3->column     = 'longcarries_percent';
    $field3->columntype = 'decimal(5,2)';
    $field3->uitype     = 9;
    $field3->typeofdata = 'I~O';

    $blockInstance->addField($field3);
}


//Related To Time Calculator module
$field4 = Vtiger_Field::getInstance('longcarries_timecalc', $moduleInstance);
if($field4) {
    echo "<br> The longcarries_timecalc field already exists in LongCarries <br>";
} else {
    $field4             = new Vtiger_Field();
    $field4->label      = 'Related To';
    $field4->name       = 'longcarries_timecalc';
    $field4->table      = 'vtiger_longcarries';
    $field4->column     = 'longcarries_timecalc';
    $field4->columntype = 'INT(19)';
    $field4->uitype     = 10;
    $field4->typeofdata = 'V~O';
    $blockInstance->addField($field4);
    $field4->setRelatedModules(array('TimeCalculator'));
}

if($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field2)->addField($field3, 1);

}


// Create Flights module
$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('Flights');

if($moduleInstance)
{
    echo "<h2>Flights already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Flights';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew = true;
}

$blockInstance = Vtiger_Block::getInstance('LBL_FLIGHT_INFORMATION',$moduleInstance);

if($blockInstance)
{
    echo "<h3>The LBL_FLIGHT_INFORMATION block already exists</h3><br> \n";
}
else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_FLIGHT_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//Number of Flights Up To
$field2 = Vtiger_Field::getInstance('flights_number', $moduleInstance);
if($field2) {
    echo "<br> The flights_number field already exists in Flights <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_NUMBER_OF_FLIGHTS_UP_TO';
    $field2->name       = 'flights_number';
    $field2->table      = 'vtiger_flights';
    $field2->column     = 'flights_number';
    $field2->columntype = 'INT(20)';
    $field2->uitype     = 7;
    $field2->typeofdata = 'I~O';
    $blockInstance->addField($field2);
    $moduleInstance->setEntityIdentifier($field2);
}
//Percent
$field3 = Vtiger_Field::getInstance('flights_percent', $moduleInstance);
if($field3) {
    echo "<br> The flights_percent field already exists in Flights <br>";
} else {
    $field3             = new Vtiger_Field();
    $field3->label      = 'LBL_NUMBER_OF_FLIGHTS_PERCENT';
    $field3->name       = 'flights_percent';
    $field3->table      = 'vtiger_flights';
    $field3->column     = 'flights_percent';
    $field3->columntype = 'decimal(5,2)';
    $field3->uitype     = 9;
    $field3->typeofdata = 'I~O';

    $blockInstance->addField($field3);
}


//Related To Time Calculator module
$field4 = Vtiger_Field::getInstance('flights_timecalc', $moduleInstance);
if($field4) {
    echo "<br> The flights_timecalc field already exists in Flights <br>";
} else {
    $field4             = new Vtiger_Field();
    $field4->label      = 'Related To';
    $field4->name       = 'flights_timecalc';
    $field4->table      = 'vtiger_flights';
    $field4->column     = 'flights_timecalc';
    $field4->columntype = 'INT(19)';
    $field4->uitype     = 10;
    $field4->typeofdata = 'V~O';
    $blockInstance->addField($field4);
    $field4->setRelatedModules(array('TimeCalculator'));
}

if($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field2)->addField($field3, 1);

}



// Create Elevators module
$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('Elevators');

if($moduleInstance)
{
    echo "<h2>Elevators already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Elevators';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew = true;
}

$blockInstance = Vtiger_Block::getInstance('LBL_ELEVATOR_INFORMATION',$moduleInstance);

if($blockInstance)
{
    echo "<h3>The LBL_ELEVATOR_INFORMATION block already exists</h3><br> \n";
}
else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_ELEVATOR_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//Number of Elevators Up To
$field2 = Vtiger_Field::getInstance('elevators_number', $moduleInstance);
if($field2) {
    echo "<br> The elevators_number field already exists in Elevators <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_NUMBER_OF_ELEVATORS_UP_TO';
    $field2->name       = 'elevators_number';
    $field2->table      = 'vtiger_elevators';
    $field2->column     = 'elevators_number';
    $field2->columntype = 'INT(20)';
    $field2->uitype     = 7;
    $field2->typeofdata = 'I~O';
    $blockInstance->addField($field2);
    $moduleInstance->setEntityIdentifier($field2);
}
//Percent
$field3 = Vtiger_Field::getInstance('elevators_percent', $moduleInstance);
if($field3) {
    echo "<br> The elevators_percent field already exists in Elevators <br>";
} else {
    $field3             = new Vtiger_Field();
    $field3->label      = 'LBL_NUMBER_OF_ELEVATORS_PERCENT';
    $field3->name       = 'elevators_percent';
    $field3->table      = 'vtiger_elevators';
    $field3->column     = 'elevators_percent';
    $field3->columntype = 'decimal(5,2)';
    $field3->uitype     = 9;
    $field3->typeofdata = 'I~O';

    $blockInstance->addField($field3);
}


//Related To Time Calculator module
$field4 = Vtiger_Field::getInstance('elevators_timecalc', $moduleInstance);
if($field4) {
    echo "<br> The elevators_timecalc field already exists in Elevators <br>";
} else {
    $field4             = new Vtiger_Field();
    $field4->label      = 'Related To';
    $field4->name       = 'elevators_timecalc';
    $field4->table      = 'vtiger_elevators';
    $field4->column     = 'elevators_timecalc';
    $field4->columntype = 'INT(19)';
    $field4->uitype     = 10;
    $field4->typeofdata = 'V~O';
    $blockInstance->addField($field4);
    $field4->setRelatedModules(array('TimeCalculator'));
}

if($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field2)->addField($field3, 1);

}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";