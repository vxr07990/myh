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
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "<br>begin shipper type hotfix...<br>";

/* $potentialsModule = Vtiger_Module::getInstance('Potentials');

$potentialsInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potentialsModule);
if($potentialsInfo){
    echo "<br> block 'LBL_OPPORTUNITY_INFORMATION' exists, attempting to add shipper type field<br>";
    $potentialsShipperType = Vtiger_Field::getInstance('shipper_type', $potentialsModule);
    if($potentialsShipperType){
        echo "<br> potentials shipper type field already exists.<br>";
    } else{
        echo "<br> potentials shipper type field doesn't exist, adding it now.<br>";
        $potentialsShipperType = new Vtiger_Field();
        $potentialsShipperType->label = 'LBL_POTENTIALS_SHIPPERTYPE';
        $potentialsShipperType->name = 'shipper_type';
        $potentialsShipperType->table = 'vtiger_potential';
        $potentialsShipperType->column = 'shipper_type';
        $potentialsShipperType->columntype = 'varchar(200)';
        $potentialsShipperType->uitype = 16;
        $potentialsShipperType->typeofdata = 'V~O';
        $potentialsShipperType->displaytype = 1;
        $potentialsShipperType->quickcreate = 0;

        $potentialsInfo->addField($potentialsShipperType);
        $potentialsShipperType->setPicklistValues(Array('COD', 'NAT'));
        echo "<br> potentials shipper type field added.<br>";
    }
} else{
    echo "<br> block 'LBL_OPPORTUNITY_INFORMATION' doesn't exist, no action taken<br>";
} */

$opportunitiesModule = Vtiger_Module::getInstance('Opportunities');

$opportunitiesInfo = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $opportunitiesModule);
if ($opportunitiesInfo) {
    echo "<br> block 'LBL_POTENTIALS_INFORMATION' exists, attempting to add shipper type field<br>";
    $opportunitiesShipperType = Vtiger_Field::getInstance('shipper_type', $opportunitiesModule);
    if ($opportunitiesShipperType) {
        echo "<br> opportunities shipper type field already exists.<br>";
    } else {
        echo "<br> opportunities shipper type field doesn't exist, adding it now.<br>";
        $opportunitiesShipperType = new Vtiger_Field();
        $opportunitiesShipperType->label = 'LBL_POTENTIALS_SHIPPERTYPE';
        $opportunitiesShipperType->name = 'shipper_type';
        $opportunitiesShipperType->table = 'vtiger_potential';
        $opportunitiesShipperType->column = 'shipper_type';
        $opportunitiesShipperType->columntype = 'varchar(200)';
        $opportunitiesShipperType->uitype = 16;
        $opportunitiesShipperType->typeofdata = 'V~O';
        $opportunitiesShipperType->displaytype = 1;
        $opportunitiesShipperType->quickcreate = 0;

        $opportunitiesInfo->addField($opportunitiesShipperType);
        $opportunitiesShipperType->setPicklistValues(array('COD', 'NAT'));
        echo "<br> opportunities shipper type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_POTENTIALS_INFORMATION' doesn't exist, no action taken<br>";
}

$ordersModule = Vtiger_Module::getInstance('Orders');

$ordersInfo = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordersModule);
if ($ordersInfo) {
    echo "<br> block 'LBL_ORDERS_INFORMATION' exists, attempting to add shipper type field<br>";
    $ordersShipperType = Vtiger_Field::getInstance('shipper_type', $ordersModule);
    if ($ordersShipperType) {
        echo "<br> orders shipper type field already exists.<br>";
    } else {
        echo "<br> orders shipper type field doesn't exist, adding it now.<br>";
        $ordersShipperType = new Vtiger_Field();
        $ordersShipperType->label = 'LBL_ORDERS_SHIPPERTYPE';
        $ordersShipperType->name = 'shipper_type';
        $ordersShipperType->table = 'vtiger_orders';
        $ordersShipperType->column = 'shipper_type';
        $ordersShipperType->columntype = 'varchar(200)';
        $ordersShipperType->uitype = 16;
        $ordersShipperType->typeofdata = 'V~O';
        $ordersShipperType->displaytype = 1;
        $ordersShipperType->quickcreate = 0;

        $ordersInfo->addField($ordersShipperType);
        $ordersShipperType->setPicklistValues(array('COD', 'NAT'));
        echo "<br> orders shipper type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_ORDERS_INFORMATION' doesn't exist, no action taken<br>";
}

$leadsModule = Vtiger_Module::getInstance('Leads');

$leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);
if ($leadsInfo) {
    echo "<br> block 'LBL_LEADS_INFORMATION' exists, attempting to add shipper type field<br>";
    $leadsShipperType = Vtiger_Field::getInstance('shipper_type', $leadsModule);
    if ($leadsShipperType) {
        echo "<br> leads shipper type field already exists.<br>";
    } else {
        echo "<br> leads shipper type field doesn't exist, adding it now.<br>";
        $leadsShipperType = new Vtiger_Field();
        $leadsShipperType->label = 'LBL_LEADS_SHIPPERTYPE';
        $leadsShipperType->name = 'shipper_type';
        $leadsShipperType->table = 'vtiger_leaddetails';
        $leadsShipperType->column = 'shipper_type';
        $leadsShipperType->columntype = 'varchar(200)';
        $leadsShipperType->uitype = 16;
        $leadsShipperType->typeofdata = 'V~O';
        $leadsShipperType->displaytype = 1;
        $leadsShipperType->quickcreate = 0;

        $leadsInfo->addField($leadsShipperType);
        $leadsShipperType->setPicklistValues(array('COD', 'NAT'));
        echo "<br> leads shipper type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_LEADS_INFORMATION' doesn't exist, no action taken<br>";
}

$estimatesModule = Vtiger_Module::getInstance('Estimates');

$estimatesInfo = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $estimatesModule);
if ($estimatesInfo) {
    echo "<br> block 'LBL_QUOTES_INFORMATION' exists, attempting to add shipper type field<br>";
    $estimatesShipperType = Vtiger_Field::getInstance('shipper_type', $estimatesModule);
    if ($estimatesShipperType) {
        echo "<br> estimates shipper type field already exists.<br>";
    } else {
        echo "<br> estimates shipper type field doesn't exist, adding it now.<br>";
        $estimatesShipperType = new Vtiger_Field();
        $estimatesShipperType->label = 'LBL_QUOTES_SHIPPERTYPE';
        $estimatesShipperType->name = 'shipper_type';
        $estimatesShipperType->table = 'vtiger_quotes';
        $estimatesShipperType->column = 'shipper_type';
        $estimatesShipperType->columntype = 'varchar(200)';
        $estimatesShipperType->uitype = 16;
        $estimatesShipperType->typeofdata = 'V~O';
        $estimatesShipperType->displaytype = 1;
        $estimatesShipperType->quickcreate = 0;

        $estimatesInfo->addField($estimatesShipperType);
        $estimatesShipperType->setPicklistValues(array('COD', 'NAT'));
        echo "<br> estimates shipper type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_QUOTE_INFORMATION' doesn't exist, no action taken<br>";
}

$quotesModule = Vtiger_Module::getInstance('Quotes');

$quotesInfo = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $quotesModule);
if ($quotesInfo) {
    echo "<br> block 'LBL_QUOTES_INFORMATION' exists, attempting to add shipper type field<br>";
    $quotesShipperType = Vtiger_Field::getInstance('shipper_type', $quotesModule);
    if ($quotesShipperType) {
        echo "<br> quotes shipper type field already exists.<br>";
    } else {
        echo "<br> quotes shipper type field doesn't exist, adding it now.<br>";
        $quotesShipperType = new Vtiger_Field();
        $quotesShipperType->label = 'LBL_QUOTES_SHIPPERTYPE';
        $quotesShipperType->name = 'shipper_type';
        $quotesShipperType->table = 'vtiger_quotes';
        $quotesShipperType->column = 'shipper_type';
        $quotesShipperType->columntype = 'varchar(200)';
        $quotesShipperType->uitype = 16;
        $quotesShipperType->typeofdata = 'V~O';
        $quotesShipperType->displaytype = 1;
        $quotesShipperType->quickcreate = 0;

        $quotesInfo->addField($quotesShipperType);
        $quotesShipperType->setPicklistValues(array('COD', 'NAT'));
        echo "<br> quotes shipper type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_QUOTE_INFORMATION' doesn't exist, no action taken<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";