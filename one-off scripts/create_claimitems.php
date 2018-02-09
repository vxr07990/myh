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
//include_once('modules/ModTracker/ModTracker.php');

$moduleInstance = new Vtiger_Module();
$moduleInstance->name = 'ClaimItems';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_CLAIMITEMS_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_CLAIMITEMS_INVENTORY';
$field1->name = 'inventory_number';
$field1->table = 'vtiger_claimitems';
$field1->column = 'inventory_number';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1;
$field1->typeofdata = 'V~M';

$blockInstance->addField($field1);
    
$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_CLAIMITEMS_CLAIM';
$field2->name = 'linked_claim';
$field2->table = 'vtiger_claimitems';
$field2->column = 'linked_claim';
$field2->columntype = 'VARCHAR(255)';
$field2->uitype = 10;
$field2->typeofdata = 'V~O';

$blockInstance->addField($field2);
$field2->setRelatedModules(array('Claims'));


$field3 = new Vtiger_Field();
$field3->label = 'Assigned To';
$field3->name = 'assigned_user_id';
$field3->table = 'vtiger_crmentity';
$field3->column = 'smownerid';
$field3->uitype = 53;
$field3->typeofdata = 'V~M';

$blockInstance->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'Created Time';
$field4->name = 'CreatedTime';
$field4->table = 'vtiger_crmentity';
$field4->column = 'createdtime';
$field4->uitype = 70;
$field4->typeofdata = 'T~O';
$field4->displaytype = 2;

$blockInstance->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'Modified Time';
$field5->name = 'ModifiedTime';
$field5->table = 'vtiger_crmentity';
$field5->column = 'modifiedtime';
$field5->uitype = 70;
$field5->typeofdata = 'T~O';
$field5->displaytype = 2;

$blockInstance->addField($field5);

$field27 = new Vtiger_Field();
$field27->label = 'LBL_CLAIMITEMS_TAGCOLOR';
$field27->name = 'tag_color';
$field27->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field27->column = 'tag_color';   //  This will be the columnname in your database for the new field.
$field27->columntype = 'VARCHAR(100)';
$field27->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field27->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field27);

$field28 = new Vtiger_Field();
$field28->label = 'LBL_CLAIMITEMS_ITEMDESCRIPTION';
$field28->name = 'item_description';
$field28->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field28->column = 'item_description';   //  This will be the columnname in your database for the new field.
$field28->columntype = 'VARCHAR(255)';
$field28->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field28->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field28);

$field29 = new Vtiger_Field();
$field29->label = 'LBL_CLAIMITEMS_CARRIEREXCEPTION';
$field29->name = 'carrier_exception';
$field29->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field29->column = 'carrier_exception';   //  This will be the columnname in your database for the new field.
$field29->columntype = 'VARCHAR(100)';
$field29->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field29->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field29);

$field30 = new Vtiger_Field();
$field30->label = 'LBL_CLAIMITEMS_SHIPPEREXCEPTION';
$field30->name = 'shipper_exception';
$field30->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field30->column = 'shipper_exception';   //  This will be the columnname in your database for the new field.
$field30->columntype = 'VARCHAR(100)';
$field30->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field30->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field30);

$field31 = new Vtiger_Field();
$field31->label = 'LBL_CLAIMITEMS_CLAIMDESCRIPTION';
$field31->name = 'claim_description';
$field31->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field31->column = 'claim_description';   //  This will be the columnname in your database for the new field.
$field31->columntype = 'VARCHAR(255)';
$field31->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field31->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field31);

$field32 = new Vtiger_Field();
$field32->label = 'LBL_CLAIMITEMS_ITEMSTATUS';
$field32->name = 'item_status';
$field32->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field32->column = 'item_status';   //  This will be the columnname in your database for the new field.
$field32->columntype = 'VARCHAR(255)';
$field32->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field32->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field32);
$field32->setPicklistValues(array('Open', 'Waiting On Quote', 'Repaired', 'Missing', 'Found', 'Denied', 'Closed'));

$field33 = new Vtiger_Field();
$field33->label = 'LBL_CLAIMITEMS_ITEMCLAIMA';
$field33->name = 'item_claimamount';
$field33->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field33->column = 'item_claimamount';   //  This will be the columnname in your database for the new field.
$field33->columntype = 'VARCHAR(255)';
$field33->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field33->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field33);

$field34 = new Vtiger_Field();
$field34->label = 'LBL_CLAIMITEMS_ORIGINALCOST';
$field34->name = 'original_cost';
$field34->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field34->column = 'original_cost';   //  This will be the columnname in your database for the new field.
$field34->columntype = 'VARCHAR(255)';
$field34->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field34->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field34);

$field35 = new Vtiger_Field();
$field35->label = 'LBL_CLAIMITEMS_DATEPURCHASED';
$field35->name = 'date_purchased';
$field35->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field35->column = 'date_purchased';   //  This will be the columnname in your database for the new field.
$field35->columntype = 'DATE';
$field35->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field35->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field35);

$field36 = new Vtiger_Field();
$field36->label = 'LBL_CLAIMITEMS_CONTRACTOR';
$field36->name = 'claims_contractors';
$field36->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field36->column = 'claims_contractors';   //  This will be the columnname in your database for the new field.
$field36->columntype = 'VARCHAR(255)';
$field36->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field36->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field36);
$field36->setRelatedModules(array('Contractors'));

$field39 = new Vtiger_Field();
$field39->label = 'LBL_CLAIMITEMS_EMPLOYEES';
$field39->name = 'claims_employees';
$field39->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field39->column = 'claims_employees';   //  This will be the columnname in your database for the new field.
$field39->columntype = 'VARCHAR(255)';
$field39->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field39->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field39);
$field39->setRelatedModules(array('Employees'));

$field40 = new Vtiger_Field();
$field40->label = 'LBL_CLAIMITEMS_AGENTS';
$field40->name = 'claims_agents';
$field40->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field40->column = 'claims_agents';   //  This will be the columnname in your database for the new field.
$field40->columntype = 'VARCHAR(255)';
$field40->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field40->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field40);
$field40->setRelatedModules(array('Agents'));

$field41 = new Vtiger_Field();
$field41->label = 'LBL_CLAIMITEMS_VENDORS';
$field41->name = 'claims_vendors';
$field41->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field41->column = 'claims_vendors';   //  This will be the columnname in your database for the new field.
$field41->columntype = 'VARCHAR(255)';
$field41->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field41->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field41);
$field41->setRelatedModules(array('Vendors'));

$field42 = new Vtiger_Field();
$field42->label = 'LBL_CLAIMITEMS_APVENDOR';
$field42->name = 'paid_vendor';
$field42->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field42->column = 'paid_vendor';   //  This will be the columnname in your database for the new field.
$field42->columntype = 'INT(20)';
$field42->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field42->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field42);

$field43 = new Vtiger_Field();
$field43->label = 'LBL_CLAIMITEMS_APCLAIMANT';
$field43->name = 'paid_claimant';
$field43->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field43->column = 'paid_claimant';   //  This will be the columnname in your database for the new field.
$field43->columntype = 'INT(20)';
$field43->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field43->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field43);

$field44 = new Vtiger_Field();
$field44->label = 'LBL_CLAIMITEMS_CHARGECONTRACTORS';
$field44->name = 'chargedback_contractors';
$field44->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field44->column = 'chargedback_contractors';   //  This will be the columnname in your database for the new field.
$field44->columntype = 'INT(20)';
$field44->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field44->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field44);

$field45 = new Vtiger_Field();
$field45->label = 'LBL_CLAIMITEMS_CHARGECOMPANY';
$field45->name = 'chargedback_company';
$field45->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
$field45->column = 'chargedback_company';   //  This will be the columnname in your database for the new field.
$field45->columntype = 'INT(20)';
$field45->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field45->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field45);


$blockInstance->save($moduleInstance);

//NEW BLOCK


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
/*
 //Adds a a row in the vtiger_modtracker_tabs for "updates" in the navigation Bar
ModTracker::enableTrackingForModule($moduleInstance->id);

//create comments relashionship and widget
require_once 'vtlib/Vtiger/Module.php';
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Claims'));

require_once 'modules/ModComments/ModComments.php';
$detailviewblock = ModComments::addWidgetTo('Claims');

//START Add navigation link in module claims to orders
$moduleInstance = Vtiger_Module::getInstance('Orders');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Claims'), 'Claims',Array('ADD'),'get_related_list');
//END Add navigation link in module

*/;
