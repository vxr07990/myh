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



//vanlineGroupHotfix.php
//adds grouptype column to vtiger_groups so that vanline groups can be distinguished from agent groups.

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin salesPerson hotfix...<br>";

$potentialsModule = Vtiger_Module::getInstance('Potentials');
$opportunitiesModule = Vtiger_Module::getInstance('Opportunities');
$ordersModule = Vtiger_Module::getInstance('Orders');
$leadsModule = Vtiger_Module::getInstance('Leads');

$potentialsInfo = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $potentialsModule);
if ($potentialsInfo) {
    echo "<br> block 'LBL_POTENTIALS_INFORMATION' exists, attempting to add salesPerson field<br>";
    $potentialsSalesPerson = Vtiger_Field::getInstance('sales_person', $potentialsModule);
    if ($potentialsSalesPerson) {
        echo "<br> potentials sales person field already exists.<br>";
    } else {
        echo "<br> potentials sales person field doesn't exist, adding it now.<br>";
        $potentialsSalesPerson = new Vtiger_Field();
        $potentialsSalesPerson->label = 'Sales Person';
        $potentialsSalesPerson->name = 'sales_person';
        $potentialsSalesPerson->table = 'vtiger_potential';
        $potentialsSalesPerson->column = 'sales_person';
        $potentialsSalesPerson->columntype = 'int(19)';
        $potentialsSalesPerson->uitype = 16;
        $potentialsSalesPerson->typeofdata = 'V~O';
        $potentialsSalesPerson->displaytype = 1;
        $potentialsSalesPerson->quickcreate = 0;

        $potentialsInfo->addField($potentialsSalesPerson);
        echo "<br> potentials sales person field added.<br>";
    }
} else {
    echo "<br> block 'LBL_POTENTIALS_INFORMATION' doesn't exist, no action taken<br>";
}

$opportunitiesInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $opportunitiesModule);
if ($opportunitiesInfo) {
    echo "<br> block 'LBL_OPPORTUNITY_INFORMATION' exists, attempting to add salesPerson field<br>";
    $opportunitiesSalesPerson = Vtiger_Field::getInstance('sales_person', $opportunitiesModule);
    if ($opportunitiesSalesPerson) {
        echo "<br> opporunities sales person field already exists.<br>";
    } else {
        echo "<br> opporunities sales person field doesn't exist, adding it now.<br>";
        $opportunitiesSalesPerson = new Vtiger_Field();
        $opportunitiesSalesPerson->label = 'Sales Person';
        $opportunitiesSalesPerson->name = 'sales_person';
        $opportunitiesSalesPerson->table = 'vtiger_potential';
        $opportunitiesSalesPerson->column = 'sales_person';
        $opportunitiesSalesPerson->columntype = 'int(19)';
        $opportunitiesSalesPerson->uitype = 16;
        $opportunitiesSalesPerson->typeofdata = 'V~O';
        $opportunitiesSalesPerson->displaytype = 1;
        $opportunitiesSalesPerson->quickcreate = 0;

        $opportunitiesInfo->addField($opportunitiesSalesPerson);
        echo "<br> opportunities sales person field added.<br>";
    }
} else {
    echo "<br> block 'LBL_OPPORTUNITY_INFORMATION' doesn't exist, no action taken<br>";
}

$ordersInfo = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordersModule);
if ($ordersInfo) {
    echo "<br> block 'LBL_ORDERS_INFORMATION' exists, attempting to add salesPerson field<br>";
    $ordersSalesPerson = Vtiger_Field::getInstance('sales_person', $ordersModule);
    if ($ordersSalesPerson) {
        echo "<br> orders sales person field already exists.<br>";
    } else {
        echo "<br> orders sales person field doesn't exist, adding it now.<br>";
        $ordersSalesPerson = new Vtiger_Field();
        $ordersSalesPerson->label = 'Sales Person';
        $ordersSalesPerson->name = 'sales_person';
        $ordersSalesPerson->table = 'vtiger_orders';
        $ordersSalesPerson->column = 'sales_person';
        $ordersSalesPerson->columntype = 'int(19)';
        $ordersSalesPerson->uitype = 16;
        $ordersSalesPerson->typeofdata = 'V~O';
        $ordersSalesPerson->displaytype = 1;
        $ordersSalesPerson->quickcreate = 0;

        $ordersInfo->addField($ordersSalesPerson);
        echo "<br> orders sales person field added.<br>";
    }
} else {
    echo "<br> block 'LBL_ORDERS_INFORMATION' doesn't exist, no action taken<br>";
}

$leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);
if ($leadsInfo) {
    echo "<br> block 'LBL_LEADS_INFORMATION' exists, attempting to add salesPerson field<br>";
    $leadsSalesPerson = Vtiger_Field::getInstance('sales_person', $leadsModule);
    if ($leadsSalesPerson) {
        echo "<br> leads sales person field already exists.<br>";
    } else {
        echo "<br> leads sales person field doesn't exist, adding it now.<br>";
        $leadsSalesPerson = new Vtiger_Field();
        $leadsSalesPerson->label = 'Sales Person';
        $leadsSalesPerson->name = 'sales_person';
        $leadsSalesPerson->table = 'vtiger_leaddetails';
        $leadsSalesPerson->column = 'sales_person';
        $leadsSalesPerson->columntype = 'int(19)';
        $leadsSalesPerson->uitype = 16;
        $leadsSalesPerson->typeofdata = 'V~O';
        $leadsSalesPerson->displaytype = 1;
        $leadsSalesPerson->quickcreate = 0;

        $leadsInfo->addField($leadsSalesPerson);
        echo "<br> leads sales person field added.<br>";
    }
} else {
    echo "<br> block 'LBL_LEADS_INFORMATION' doesn't exist, no action taken<br>";
}

echo "<br> salesPerson hotfix complete!<br>";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";