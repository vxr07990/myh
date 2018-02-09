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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
$leads = Vtiger_Module::getInstance('Leads');
//$opps = Vtiger_Module::getInstance('Opportunities');
//$pots = Vtiger_Module::getInstance('Potentials');

//LBL_LEADS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leads);
if ($block1) {
    echo "<h3>The LBL_LEADS_INFORMATION block already exists</h3><br>\n";
} else {
    $block1        = new Vtiger_Block();
    $block1->label = 'LBL_LEADS_INFORMATION';
    $leads->addBlock($block1);
}
$field1 = Vtiger_Field::getInstance('ade_lead_id', $leads);
if ($field1) {
    echo "The ade_lead_id field already exists<br>\n";
} else {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_ADE_LEAD_ID';
    $field1->name       = 'ade_lead_id';
    $field1->table      = 'vtiger_leaddetails';
    $field1->column     = 'ade_lead_id';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype     = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 3;
    $block1->addField($field1);
}
$field2 = Vtiger_Field::getInstance('amc_salesperson_id', $leads);
if ($field2) {
    echo "The amc_salesperson_id field already exists<br>\n";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_AMC_SALESPERSON_ID';
    $field2->name       = 'amc_salesperson_id';
    $field2->table      = 'vtiger_leaddetails';
    $field2->column     = 'amc_salesperson_id';
    $field2->columntype = 'VARCHAR(50)';
    $field2->uitype     = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 3;
    $block1->addField($field2);
}
//LBL_POTENTIALS_INFORMATION
//$block2 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $opps);
//if ($block2) {
//    echo "<h3>The LBL_POTENTIALS_INFORMATION block already exists</h3><br> \n";
//} else {
//    $block2        = new Vtiger_Block();
//    $block2->label = 'LBL_POTENTIALS_INFORMATION';
//    $opps->addBlock($block2);
//}
//$field3 = Vtiger_Field::getInstance('ade_lead_id', $opps);
//if ($field3) {
//    echo "The ade_lead_id field already exists<br>\n";
//} else {
//    $field3             = new Vtiger_Field();
//    $field3->label      = 'LBL_ADE_LEAD_ID';
//    $field3->name       = 'ade_lead_id';
//    $field3->table      = 'vtiger_potential';
//    $field3->column     = 'ade_lead_id';
//    $field3->columntype = 'VARCHAR(50)';
//    $field3->uitype     = 1;
//    $field3->typeofdata = 'V~O';
//    $field3->displaytype = 3;
//    $block2->addField($field3);
//}
//$field4 = Vtiger_Field::getInstance('amc_salesperson_id', $opps);
//if ($field4) {
//    echo "The amc_salesperson_id field already exists<br>\n";
//} else {
//    $field4             = new Vtiger_Field();
//    $field4->label      = 'LBL_AMC_SALESPERSON_ID';
//    $field4->name       = 'amc_salesperson_id';
//    $field4->table      = 'vtiger_potential';
//    $field4->column     = 'amc_salesperson_id';
//    $field4->columntype = 'VARCHAR(50)';
//    $field4->uitype     = 1;
//    $field4->typeofdata = 'V~O';
//    $field4->displaytype = 3;
//    $block2->addField($field4);
//}
////LBL_OPPORTUNITY_INFORMATION
//$block3 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $pots);
//if ($block3) {
//    echo "<h3>The LBL_OPPORTUNITY_INFORMATION block already exists</h3><br> \n";
//} else {
//    $block3        = new Vtiger_Block();
//    $block3->label = 'LBL_OPPORTUNITY_INFORMATION';
//    $pots->addBlock($block3);
//}
//$field5 = Vtiger_Field::getInstance('ade_lead_id', $pots);
//if ($field5) {
//    echo "The ade_lead_id field already exists<br>\n";
//} else {
//    $field5             = new Vtiger_Field();
//    $field5->label      = 'LBL_ADE_LEAD_ID';
//    $field5->name       = 'ade_lead_id';
//    $field5->table      = 'vtiger_potential';
//    $field5->column     = 'ade_lead_id';
//    $field5->columntype = 'VARCHAR(50)';
//    $field5->uitype     = 1;
//    $field5->typeofdata = 'V~O';
//    $field5->displaytype = 3;
//    $block3->addField($field5);
//}
//$field6 = Vtiger_Field::getInstance('amc_salesperson_id', $pots);
//if ($field6) {
//    echo "The amc_salesperson_id field already exists<br>\n";
//} else {
//    $field6             = new Vtiger_Field();
//    $field6->label      = 'LBL_AMC_SALESPERSON_ID';
//    $field6->name       = 'amc_salesperson_id';
//    $field6->table      = 'vtiger_potential';
//    $field6->column     = 'amc_salesperson_id';
//    $field6->columntype = 'VARCHAR(50)';
//    $field6->uitype     = 1;
//    $field6->typeofdata = 'V~O';
//    $field6->displaytype = 3;
//    $block3->addField($field6);
//}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";