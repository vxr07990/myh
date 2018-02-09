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

if (!$db) {
    $db = PearDatabase::getInstance();
}

//Clear out our current selection, since UVL uses their own set
Vtiger_Utils::ExecuteQuery("TRUNCATE `vtiger_billing_type`");

$moduleQuotes = Vtiger_Module::getInstance('Quotes');

$billingTypeField = Vtiger_Field::getInstance('billing_type', $moduleQuotes);
if ($billingTypeField) {
    echo '<br />Setting UVLC billing type<br />';
    $billingTypeField->setPicklistValues([
        'U - U.V.L. to Bill',
        'M - Member to Bill',
        'C - C.O.D.',
        'P - Prepaid',
        'V - Visa',
        'S - MasterCard',
        'A - American Express',
    ]);
}

//Leads module changes
$moduleLeads = Vtiger_Module::getInstance('Leads');

$blockLeads13 = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $moduleLeads);
if ($blockLeads13) {
    echo "<br> The LBL_LEADS_INFORMATION block already exists in Leads <br>";
} else {
    $blockLeads13 = new Vtiger_Block();
    $blockLeads13->label = 'LBL_LEADS_INFORMATION';
    $moduleLeads->addBlock($blockLeads13);
}

$field = Vtiger_Field::getInstance('lead_title', $moduleLeads);
if ($field) {
    echo "<br> The LBL_LEADTITLE field already exists in Leads <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_LEADS_LEADTITLE';
    $field->name = 'lead_title';
    $field->table = 'vtiger_leaddetails';
    $field->column ='lead_title';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockLeads13->addField($field);
    $field->setPicklistValues(['Mr', 'Mrs', 'Miss']);

    $block13 = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $moduleLeads);
    $field1966 = Vtiger_Field::getInstance('lead_title', $moduleLeads);
    $field38 = Vtiger_Field::getInstance('firstname', $moduleLeads);
    $field41 = Vtiger_Field::getInstance('lastname', $moduleLeads);
    $field40 = Vtiger_Field::getInstance('phone', $moduleLeads);
    $field46 = Vtiger_Field::getInstance('email', $moduleLeads);
    $field709 = Vtiger_Field::getInstance('emailoptout', $moduleLeads);
    $field54 = Vtiger_Field::getInstance('assigned_user_id', $moduleLeads);
    $field56 = Vtiger_Field::getInstance('createdtime', $moduleLeads);
    $field751 = Vtiger_Field::getInstance('business_line', $moduleLeads);
    $field1726 = Vtiger_Field::getInstance('sales_person', $moduleLeads);
    $field39 = Vtiger_Field::getInstance('lead_no', $moduleLeads);
    $field43 = Vtiger_Field::getInstance('company', $moduleLeads);
    $field42 = Vtiger_Field::getInstance('mobile', $moduleLeads);
    $field55 = Vtiger_Field::getInstance('secondaryemail', $moduleLeads);
    $field47 = Vtiger_Field::getInstance('leadsource', $moduleLeads);
    $field50 = Vtiger_Field::getInstance('leadstatus', $moduleLeads);
    $field57 = Vtiger_Field::getInstance('modifiedtime', $moduleLeads);
    $field727 = Vtiger_Field::getInstance('created_user_id', $moduleLeads);
    $field1904 = Vtiger_Field::getInstance('agentid', $moduleLeads);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1966->id." THEN 1 WHEN fieldid=".$field38->id." THEN 3 WHEN fieldid=".$field41->id." THEN 5 WHEN fieldid=".$field40->id." THEN 7 WHEN fieldid=".$field46->id." THEN 9 WHEN fieldid=".$field709->id." THEN 11 WHEN fieldid=".$field54->id." THEN 13 WHEN fieldid=".$field56->id." THEN 15 WHEN fieldid=".$field751->id." THEN 17 WHEN fieldid=".$field1726->id." THEN 19 WHEN fieldid=".$field39->id." THEN 2 WHEN fieldid=".$field43->id." THEN 4 WHEN fieldid=".$field42->id." THEN 6 WHEN fieldid=".$field55->id." THEN 8 WHEN fieldid=".$field47->id." THEN 10 WHEN fieldid=".$field50->id." THEN 12 WHEN fieldid=".$field57->id." THEN 14 WHEN fieldid=".$field727->id." THEN 16 WHEN fieldid=".$field1904->id." THEN 18 END, block=CASE WHEN fieldid=".$field1966->id." THEN ".$block13->id." WHEN fieldid=".$field38->id." THEN ".$block13->id." WHEN fieldid=".$field41->id." THEN ".$block13->id." WHEN fieldid=".$field40->id." THEN ".$block13->id." WHEN fieldid=".$field46->id." THEN ".$block13->id." WHEN fieldid=".$field709->id." THEN ".$block13->id." WHEN fieldid=".$field54->id." THEN ".$block13->id." WHEN fieldid=".$field56->id." THEN ".$block13->id." WHEN fieldid=".$field751->id." THEN ".$block13->id." WHEN fieldid=".$field1726->id." THEN ".$block13->id." WHEN fieldid=".$field39->id." THEN ".$block13->id." WHEN fieldid=".$field43->id." THEN ".$block13->id." WHEN fieldid=".$field42->id." THEN ".$block13->id." WHEN fieldid=".$field55->id." THEN ".$block13->id." WHEN fieldid=".$field47->id." THEN ".$block13->id." WHEN fieldid=".$field50->id." THEN ".$block13->id." WHEN fieldid=".$field57->id." THEN ".$block13->id." WHEN fieldid=".$field727->id." THEN ".$block13->id." WHEN fieldid=".$field1904->id." THEN ".$block13->id." END WHERE fieldid IN (".$field1966->id.",".$field38->id.",".$field41->id.",".$field40->id.",".$field46->id.",".$field709->id.",".$field54->id.",".$field56->id.",".$field751->id.",".$field1726->id.",".$field39->id.",".$field43->id.",".$field42->id.",".$field55->id.",".$field47->id.",".$field50->id.",".$field57->id.",".$field727->id.",".$field1904->id.")");
}

$field = Vtiger_Field::getInstance('secondary_contact', $moduleLeads);
if ($field) {
    echo "<br> The secondary_contact field already exists in Leads <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_LEADS_SECONDARYCONTACT';
    $field->name = 'secondary_contact';
    $field->table = 'vtiger_leaddetails';
    $field->column ='secondary_contact';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockLeads13->addField($field);

    $field1969 = Vtiger_Field::getInstance('lead_title', $moduleLeads);
    $field38 = Vtiger_Field::getInstance('firstname', $moduleLeads);
    $field41 = Vtiger_Field::getInstance('lastname', $moduleLeads);
    $field40 = Vtiger_Field::getInstance('phone', $moduleLeads);
    $field46 = Vtiger_Field::getInstance('email', $moduleLeads);
    $field709 = Vtiger_Field::getInstance('emailoptout', $moduleLeads);
    $field1971 = Vtiger_Field::getInstance('secondary_contact', $moduleLeads);
    $field54 = Vtiger_Field::getInstance('assigned_user_id', $moduleLeads);
    $field56 = Vtiger_Field::getInstance('createdtime', $moduleLeads);
    $field751 = Vtiger_Field::getInstance('business_line', $moduleLeads);
    $field39 = Vtiger_Field::getInstance('lead_no', $moduleLeads);
    $field43 = Vtiger_Field::getInstance('company', $moduleLeads);
    $field42 = Vtiger_Field::getInstance('mobile', $moduleLeads);
    $field55 = Vtiger_Field::getInstance('secondaryemail', $moduleLesads);
    $field47 = Vtiger_Field::getInstance('leadsource', $moduleLeads);
    $field50 = Vtiger_Field::getInstance('leadstatus', $moduleLeads);
    $field57 = Vtiger_Field::getInstance('modifiedtime', $moduleLeads);
    $field727 = Vtiger_Field::getInstance('created_user_id', $moduleLeads);
    $field1904 = Vtiger_Field::getInstance('agentid', $moduleLeads);
    $field1726 = Vtiger_Field::getInstance('sales_person', $moduleLeads);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1969->id." THEN 1 WHEN fieldid=".$field38->id." THEN 3 WHEN fieldid=".$field41->id." THEN 5 WHEN fieldid=".$field40->id." THEN 7 WHEN fieldid=".$field46->id." THEN 9 WHEN fieldid=".$field709->id." THEN 11 WHEN fieldid=".$field1971->id." THEN 13 WHEN fieldid=".$field54->id." THEN 15 WHEN fieldid=".$field56->id." THEN 17 WHEN fieldid=".$field751->id." THEN 19 WHEN fieldid=".$field39->id." THEN 2 WHEN fieldid=".$field43->id." THEN 4 WHEN fieldid=".$field42->id." THEN 6 WHEN fieldid=".$field55->id." THEN 8 WHEN fieldid=".$field47->id." THEN 10 WHEN fieldid=".$field50->id." THEN 12 WHEN fieldid=".$field57->id." THEN 14 WHEN fieldid=".$field727->id." THEN 16 WHEN fieldid=".$field1904->id." THEN 18 WHEN fieldid=".$field1726->id." THEN 20 END, block=CASE WHEN fieldid=".$field1969->id." THEN ".$block13->id." WHEN fieldid=".$field38->id." THEN ".$block13->id." WHEN fieldid=".$field41->id." THEN ".$block13->id." WHEN fieldid=".$field40->id." THEN ".$block13->id." WHEN fieldid=".$field46->id." THEN ".$block13->id." WHEN fieldid=".$field709->id." THEN ".$block13->id." WHEN fieldid=".$field1971->id." THEN ".$block13->id." WHEN fieldid=".$field54->id." THEN ".$block13->id." WHEN fieldid=".$field56->id." THEN ".$block13->id." WHEN fieldid=".$field751->id." THEN ".$block13->id." WHEN fieldid=".$field39->id." THEN ".$block13->id." WHEN fieldid=".$field43->id." THEN ".$block13->id." WHEN fieldid=".$field42->id." THEN ".$block13->id." WHEN fieldid=".$field55->id." THEN ".$block13->id." WHEN fieldid=".$field47->id." THEN ".$block13->id." WHEN fieldid=".$field50->id." THEN ".$block13->id." WHEN fieldid=".$field57->id." THEN ".$block13->id." WHEN fieldid=".$field727->id." THEN ".$block13->id." WHEN fieldid=".$field1904->id." THEN ".$block13->id." WHEN fieldid=".$field1726->id." THEN ".$block13->id." END WHERE fieldid IN (".$field1969->id.",".$field38->id.",".$field41->id.",".$field40->id.",".$field46->id.",".$field709->id.",".$field1971->id.",".$field54->id.",".$field56->id.",".$field751->id.",".$field39->id.",".$field43->id.",".$field42->id.",".$field55->id.",".$field47->id.",".$field50->id.",".$field57->id.",".$field727->id.",".$field1904->id.",".$field1726->id.")");
}

$moduleOpportunities = Vtiger_Module::getInstance('Opportunities');
$modulePotentials = Vtiger_Module::getInstance('Potentials');

$blockOpportunities202 = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $moduleOpportunities);
if ($blockOpportunities202) {
    echo "<br> The LBL_POTENTIALS_ADDRESSDETAILS block already exists in Opportunities <br>";
} else {
    $blockOpportunities202 = new Vtiger_Block();
    $blockOpportunities202->label = 'LBL_POTENTIALS_ADDRESSDETAILS';
    $moduleOpportunities->addBlock($blockOpportunities202);
}

$blockPotentials202 = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $modulePotentials);
if ($blockPotentials202) {
    echo "<br> The LBL_POTENTIALS_ADDRESSDETAILS block already exists in Potentials <br>";
} else {
    $blockPotentials202 = new Vtiger_Block();
    $blockPotentials202->label = 'LBL_POTENTIALS_ADDRESSDETAILS';
    $modulePotentials->addBlock($blockPotentials202);
}

$field = Vtiger_Field::getInstance('origin_elevator', $moduleOpportunities);
if ($field) {
    echo "<br> The origin_elevator field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_ORIGINELEVATOR';
    $field->name = 'origin_elevator';
    $field->table = 'vtiger_potential';
    $field->column ='origin_elevator';
    $field->columntype = 'varchar(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities202->addField($field);
}
$field = Vtiger_Field::getInstance('origin_elevator', $modulePotentials);
if ($field) {
    echo "<br> The origin_elevator field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_ORIGINELEVATOR';
    $field->name = 'origin_elevator';
    $field->table = 'vtiger_potential';
    $field->column ='origin_elevator';
    $field->columntype = 'varchar(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials202->addField($field);
}
$field = Vtiger_Field::getInstance('destination_elevator', $moduleOpportunities);
if ($field) {
    echo "<br> The destination_elevator field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_DESTINATIONELIVATOR';
    $field->name = 'destination_elevator';
    $field->table = 'vtiger_potential';
    $field->column ='destination_elevator';
    $field->columntype = 'varchar(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities202->addField($field);
}
$field = Vtiger_Field::getInstance('destination_elevator', $modulePotentials);
if ($field) {
    echo "<br> The destination_elevator field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_DESTINATIONELIVATOR';
    $field->name = 'destination_elevator';
    $field->table = 'vtiger_potential';
    $field->column ='destination_elevator';
    $field->columntype = 'varchar(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials202->addField($field);
}

$blockOpportunities201 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleOpportunities);
if ($blockOpportunities201) {
    echo "<br> The LBL_POTENTIALS_INFORMATION block already exists in Opportunities <br>";
} else {
    $blockOpportunities201 = new Vtiger_Block();
    $blockOpportunities201->label = 'LBL_POTENTIALS_INFORMATION';
    $moduleOpportunities->addBlock($blockOpportunities201);
}

$blockPotentials201 = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $modulePotentials);
if ($blockPotentials201) {
    echo "<br> The LBL_OPPORTUNITY_INFORMATION block already exists in Potentials <br>";
} else {
    $blockPotentials201 = new Vtiger_Block();
    $blockPotentials201->label = 'LBL_OPPORTUNITY_INFORMATION';
    $modulePotentials->addBlock($blockPotentials201);
}

$field = Vtiger_Field::getInstance('reward_number_type', $moduleOpportunities);
if ($field) {
    echo "<br> The reward_number_type field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_REWARDNUMBERTYPE';
    $field->name = 'reward_number_type';
    $field->table = 'vtiger_potential';
    $field->column ='reward_number_type';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities201->addField($field);
    $field->setPicklistValues(['Airmiles', 'Other']);
}
$field = Vtiger_Field::getInstance('reward_number_type', $modulePotentials);
if ($field) {
    echo "<br> The reward_number_type field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_REWARDNUMBERTYPE';
    $field->name = 'reward_number_type';
    $field->table = 'vtiger_potential';
    $field->column ='reward_number_type';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials201->addField($field);
    $field->setPicklistValues(['Airmiles', 'Other']);
}
$field = Vtiger_Field::getInstance('reward_number', $moduleOpportunities);
if ($field) {
    echo "<br> The reward_number field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_REWARDNUMBER';
    $field->name = 'reward_number';
    $field->table = 'vtiger_potential';
    $field->column ='reward_number';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities201->addField($field);
}
$field = Vtiger_Field::getInstance('reward_number', $modulePotentials);
if ($field) {
    echo "<br> The reward_number field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_REWARDNUMBER';
    $field->name = 'reward_number';
    $field->table = 'vtiger_potential';
    $field->column ='reward_number';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials201->addField($field);
}

$blockOpportunities306 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_PAYMENTDETAILS', $moduleOpportunities);
if ($blockOpportunities306) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_PAYMENTDETAILS block already exists in Opportunities <br>";
} else {
    $blockOpportunities306 = new Vtiger_Block();
    $blockOpportunities306->label = 'LBL_OPPORTUNITIES_BLOCK_PAYMENTDETAILS';
    $moduleOpportunities->addBlock($blockOpportunities306);
}

$blockPotentials306 = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_PAYMENTDETAILS', $modulePotentials);
if ($blockPotentials306) {
    echo "<br> The LBL_OPPORTUNITIES_BLOCK_PAYMENTDETAILS block already exists in Potentials <br>";
} else {
    $blockPotentials306 = new Vtiger_Block();
    $blockPotentials306->label = 'LBL_OPPORTUNITIES_BLOCK_PAYMENTDETAILS';
    $modulePotentials->addBlock($blockPotentials306);
}

$field = Vtiger_Field::getInstance('payment_type', $moduleOpportunities);
if ($field) {
    echo "<br> The payment_type field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_PAYMENTTYPE';
    $field->name = 'payment_type';
    $field->table = 'vtiger_potential';
    $field->column ='payment_type';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities306->addField($field);
    $field->setPicklistValues(['Check', 'Credit Card', 'Cash']);
}
$field = Vtiger_Field::getInstance('payment_type', $modulePotentials);
if ($field) {
    echo "<br> The payment_type field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_PAYMENTTYPE';
    $field->name = 'payment_type';
    $field->table = 'vtiger_potential';
    $field->column ='payment_type';
    $field->columntype = 'varchar(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials306->addField($field);
    $field->setPicklistValues(['Check', 'Credit Card', 'Cash']);
}
$field = Vtiger_Field::getInstance('date_payment_received', $moduleOpportunities);
if ($field) {
    echo "<br> The date_payment_received field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_DATEPAYMENTRECEIVED';
    $field->name = 'date_payment_received';
    $field->table = 'vtiger_potential';
    $field->column ='date_payment_received';
    $field->columntype = 'date';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities306->addField($field);
}
$field = Vtiger_Field::getInstance('date_payment_received', $modulePotentials);
if ($field) {
    echo "<br> The date_payment_received field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_DATEPAYMENTRECEIVED';
    $field->name = 'date_payment_received';
    $field->table = 'vtiger_potential';
    $field->column ='date_payment_received';
    $field->columntype = 'date';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials306->addField($field);
}
$field = Vtiger_Field::getInstance('credit_last_four', $moduleOpportunities);
if ($field) {
    echo "<br> The credit_last_four field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDLASTFOUR';
    $field->name = 'credit_last_four';
    $field->table = 'vtiger_potential';
    $field->column ='credit_last_four';
    $field->columntype = 'int(4)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities306->addField($field);
}
$field = Vtiger_Field::getInstance('credit_last_four', $modulePotentials);
if ($field) {
    echo "<br> The credit_last_four field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDLASTFOUR';
    $field->name = 'credit_last_four';
    $field->table = 'vtiger_potential';
    $field->column ='credit_last_four';
    $field->columntype = 'int(4)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials306->addField($field);
}
$field = Vtiger_Field::getInstance('card_auth_num', $moduleOpportunities);
if ($field) {
    echo "<br> The card_auth_num field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDAUTHNUM';
    $field->name = 'card_auth_num';
    $field->table = 'vtiger_potential';
    $field->column ='card_auth_num';
    $field->columntype = 'int(3)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities306->addField($field);
}
$field = Vtiger_Field::getInstance('card_auth_num', $modulePotentials);
if ($field) {
    echo "<br> The card_auth_num field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDAUTHNUM';
    $field->name = 'card_auth_num';
    $field->table = 'vtiger_potential';
    $field->column ='card_auth_num';
    $field->columntype = 'int(3)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials306->addField($field);
}
$field = Vtiger_Field::getInstance('card_owner_name', $moduleOpportunities);
if ($field) {
    echo "<br> The card_owner_name field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDOWNERNAME';
    $field->name = 'card_owner_name';
    $field->table = 'vtiger_potential';
    $field->column ='card_owner_name';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities306->addField($field);
}
$field = Vtiger_Field::getInstance('card_owner_name', $modulePotentials);
if ($field) {
    echo "<br> The card_owner_name field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDOWNERNAME';
    $field->name = 'card_owner_name';
    $field->table = 'vtiger_potential';
    $field->column ='card_owner_name';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O~LE~255';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials306->addField($field);
}
$field = Vtiger_Field::getInstance('card_phone_num', $moduleOpportunities);
if ($field) {
    echo "<br> The card_phone_num field already exists in Opportunities <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDPHONENUM';
    $field->name = 'card_phone_num';
    $field->table = 'vtiger_potential';
    $field->column ='card_phone_num';
    $field->columntype = 'varchar(30)';
    $field->uitype = 11;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOpportunities306->addField($field);
}
$field = Vtiger_Field::getInstance('card_phone_num', $modulePotentials);
if ($field) {
    echo "<br> The card_phone_num field already exists in Potentials <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_OPPORTUNITIES_CARDPHONENUM';
    $field->name = 'card_phone_num';
    $field->table = 'vtiger_potential';
    $field->column ='card_phone_num';
    $field->columntype = 'varchar(30)';
    $field->uitype = 11;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockPotentials306->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";