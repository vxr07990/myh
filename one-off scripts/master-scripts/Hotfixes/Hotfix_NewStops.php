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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

if (Vtiger_Utils::CheckTable('vtiger_extrastops')) {
    echo '<br>extra stops table already exists<br>';
} else {
    echo "<br>extra stops table doesn't exist, creating it now<br>";
    Vtiger_Utils::CreateTable('vtiger_extrastops',
                              '(
							    stopid INT(11),
							    stop_sequence INT(11),
								stop_description VARCHAR(255),
								stop_weight INT(11),
								stop_date VARCHAR(255),
								stop_address1 VARCHAR(255),
								stop_address2 VARCHAR(255),
								stop_city VARCHAR(255),
								stop_state VARCHAR(255),
								stop_zip VARCHAR(255),
								stop_country VARCHAR(255),
								stop_phonetype1 VARCHAR(255),
								stop_phone1 VARCHAR(255),
								stop_phonetype2 VARCHAR(255),
								stop_phone2 VARCHAR(255),
								stop_isprimary VARCHAR(3),
								stop_opp INT(11),
								stop_order INT(11),
								stop_estimate INT(11),
								stop_type VARCHAR(255),
								stop_contact VARCHAR(255)
								)', true);
    echo "<br>extra stops table created<br>";
}

if (Vtiger_Utils::CheckTable('vtiger_extrastops_seq')) {
    echo '<br>extra stops seq table already exists<br>';
} else {
    echo "<br>extra stops seq table doesn't exist, creating it now<br>";
    Vtiger_Utils::CreateTable('vtiger_extrastops_seq',
                              '(
							    id INT(11)
								)', true);
    echo "<br>extra stops seq table created<br>";
}

$db = PearDatabase::getInstance();

$oppsModule = Vtiger_Module::getInstance('Opportunities');

$extraStops = Vtiger_Block::getInstance('LBL_OPPORTUNITY_EXTRASTOPS', $oppsModule);

if (!$extraStops) {
    echo "<br>LBL_OPPORTUNITY_EXTRASTOPS block doesn't exist. creating it now.<br>";
    $extraStops = new Vtiger_Block();
    $extraStops->label = 'LBL_OPPORTUNITY_EXTRASTOPS';
    $oppsModule->addBlock($extraStops);
    echo "<br>LBL_OPPORTUNITY_EXTRASTOPS block creation complete.<br>";
} else {
    echo "<br>LBL_OPPORTUNITY_EXTRASTOPS already exists.<br>";
}

$ordersModule = Vtiger_Module::getInstance('Orders');

$extraStopsOrders = Vtiger_Block::getInstance('LBL_ORDERS_EXTRASTOPS', $ordersModule);

if (!$extraStopsOrders) {
    echo "<br>LBL_ORDERS_EXTRASTOPS block doesn't exist. creating it now.<br>";
    $extraStopsOrders = new Vtiger_Block();
    $extraStopsOrders->label = 'LBL_ORDERS_EXTRASTOPS';
    $ordersModule->addBlock($extraStopsOrders);
    echo "<br>LBL_ORDERS_EXTRASTOPS block creation complete.<br>";
} else {
    echo "<br>LBL_ORDERS_EXTRASTOPS already exists.<br>";
}

$quoteModule = Vtiger_Module::getInstance('Quotes');

$extraStopsQuote = Vtiger_Block::getInstance('LBL_QUOTES_EXTRASTOPS', $quoteModule);

if (!$extraStopsQuote) {
    echo "<br>LBL_QUOTES_EXTRASTOPS block doesn't exist. creating it now.<br>";
    $extraStopsQuote = new Vtiger_Block();
    $extraStopsQuote->label = 'LBL_QUOTES_EXTRASTOPS';
    $quoteModule->addBlock($extraStopsQuote);
    echo "<br>LBL_QUOTES_EXTRASTOPS block creation complete.<br>";
} else {
    echo "<br>LBL_QUOTES_EXTRASTOPS already exists.<br>";
}

$estModule = Vtiger_Module::getInstance('Estimates');

$extraStopsEst = Vtiger_Block::getInstance('LBL_ESTIMATES_EXTRASTOPS', $estModule);

if (!$extraStopsEst) {
    echo "<br>LBL_ESTIMATES_EXTRASTOPS block doesn't exist. creating it now.<br>";
    $extraStopsEst = new Vtiger_Block();
    $extraStopsEst->label = 'LBL_ESTIMATES_EXTRASTOPS';
    $estModule->addBlock($extraStopsEst);
    echo "<br>LBL_ESTIMATES_EXTRASTOPS block creation complete.<br>";
} else {
    echo "<br>LBL_ESTIMATES_EXTRASTOPS already exists.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";