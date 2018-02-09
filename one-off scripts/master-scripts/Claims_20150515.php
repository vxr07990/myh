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


/*$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');*/

$claimsIsNew = false;  //flag for filters at the end
$claimItemsIsNew = false;

$module1 = Vtiger_Module::getInstance('Claims');
if ($module1) {
    echo "<h2> Updating fields for Claims </h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Claims';
    $module1->save();
    echo "<h2> Creating Claims Module and Updating Fields </h2><br>";
    $module1->initTables();
}
//start block1 : LBL_CLAIMS_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_CLAIMS_INFORMATION', $module1);
if ($block1) {
    echo "<h3> LBL_CLAIMS_INFORMATION block already exists </h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_CLAIMS_INFORMATION';
    $module1->addBlock($block1);
    $claimsIsNew = true;
}
//start block1 fields
echo "<ul>";
$field1 = Vtiger_Field::getInstance('claims_number', $module1);
if ($field1) {
    echo "<li>The claims_number field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CLAIMS_NUMBER';
    $field1->name = 'claims_number';
    $field1->table = 'vtiger_claims';
    $field1->column = 'claims_number';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~M';
    
    $block1->addField($field1);
    $module1->setEntityIdentifier($field1);
}
/*$field2 = Vtiger_Field::getInstance('claims_transferees', $module1);
if($field2) {
    echo "<li>The claims_transferees field already exists</li><br>";
}
else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CLAIMS_TRANSFEREES';
    $field2->name = 'claims_transferees';
    $field2->table = 'vtiger_claims';
    $field2->column = 'claims_transferees';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~O';

    $block1->addField($field2);

    $tempModule = Vtiger_Module::getInstance('Transferees');
    if($tempModule) {
        $field2->setRelatedModules(Array('Transferees'));
    }
    else {
        echo "<h2>Unable to set Related to Transferees as Transferees does not exist</h2><br>";
    }

}*/
$field3 = Vtiger_Field::getInstance('claims_account', $module1);
if ($field3) {
    echo "<li>The claims_account field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CLAIMS_ACCOUNT';
    $field3->name = 'claims_account';
    $field3->table = 'vtiger_claims';
    $field3->column = 'claims_account';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 10;
    $field3->typeofdata = 'V~O';
    
    $block1->addField($field3);
    
    $tempModule = Vtiger_Module::getInstance('Accounts');
    if ($tempModule) {
        $field3->setRelatedModules(array('Accounts'));
    } else {
        echo "<h2>Unable to set Related to Accounts as Accounts does not exist</h2><br>";
    }
}
/* I honestly have no idea what the hell this field is supposed to be here for so it's getting commented out for now - ACS 20150521
$field4 = Vtiger_Field::getInstance('claims_ordernumber', $module1);
if($field4) {
    echo "<li>The claims_ordernumber field already exists</li><br>";
}
else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_CLAIMS_ORDERNUMBER';
    $field4->name = 'claims_ordernumber';
    $field4->table = 'vtiger_claims';
    $field4->column = 'claims_ordernumber';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
*/
$field5 = Vtiger_Field::getInstance('claims_status', $module1);
if ($field5) {
    echo "<li>The claims_status field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CLAIMS_STATUS';
    $field5->name = 'claims_status';
    $field5->table = 'vtiger_claims';
    $field5->column = 'claims_status';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~O';
    
    $field5->setPicklistValues(array('Received', 'Researching', 'In Progress', 'Open', 'Denied', 'Closed'));
    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('valuation_type', $module1);
if ($field6) {
    echo "<li>The valuation_type field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CLAIMS_VALUATIONTYPE';
    $field6->name = 'valuation_type';
    $field5->table = 'vtiger_claims';
    $field6->column = 'valuation_type';
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = '1';
    $field6->typeofdata = 'V~O';
    
    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('declared_value', $module1);
if ($field7) {
    echo "<li>The declared_value field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CLAIMS_DECLAREDVALUE';
    $field7->name = 'declared_value';
    $field7->table = 'vtiger_claims';
    $field7->column = 'declared_value';
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    
    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('claim_type', $module1);
if ($field8) {
    echo "<li>The claim_type field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CLAIMS_CLAIMTYPE';
    $field8->name = 'claim_type';
    $field8->table = 'vtiger_claims';
    $field8->column = 'claim_type';
    $field8->columntype = 'VARCHAR(255)';
    $field8->uitype = 16;
    $field8->typeofdata = 'V~O';
    
    //add the picklist here
    $field8->setPicklistValues(array('Property', 'Missing', 'Damaged'));
    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('claim_filedby', $module1);
if ($field9) {
    echo "<li>The claim_filedby field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_CLAIMS_FILEDBY';
    $field9->name = 'claim_filedby';
    $field9->table = 'vtiger_claims';
    $field9->column = 'claim_filedby';
    $field9->columntype = 'VARCHAR(255)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';
    
    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('date_created', $module1);
if ($field10) {
    echo "<li>The date_created field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_CLAIMS_DATECREATED';
    $field10->name = 'date_created';
    $field10->table = 'vtiger_claims';
    $field10->column = 'date_created';
    $field10->columntype = 'date';
    $field10->uitype = 5;
    $field10->typeofdata = 'D~O';
    
    $block1->addField($field10);
}
$field11 = Vtiger_Field::getInstance('date_submitted', $module1);
if ($field11) {
    echo "<li>The date_submitted field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_CLAIMS_DATESUBMITTED';
    $field11->name = 'date_submitted';
    $field11->table = 'vtiger_claims';
    $field11->column = 'date_submitted';
    $field11->columntype = 'date';
    $field11->uitype = 5;
    $field11->typeofdata = 'D~O';
    
    $block1->addField($field11);
}
$field12 = Vtiger_Field::getInstance('date_closed', $module1);
if ($field12) {
    echo "<li>The date_created field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_CLAIMS_DATECLOSED';
    $field12->name = 'date_closed';
    $field12->table = 'vtiger_claims';
    $field12->column = 'date_closed';
    $field12->columntype = 'date';
    $field12->uitype = 5;
    $field12->typeofdata = 'D~O';
    
    $block1->addField($field12);
}
$field13 = Vtiger_Field::getInstance('total_claim', $module1);
if ($field13) {
    echo "<li>The total_claim field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_CLAIMS_TOTALCLAIM';
    $field13->name = 'total_claim';
    $field13->table = 'vtiger_claims';
    $field13->column = 'total_claim';
    $field13->columntype = 'DECIMAL(25,8)';
    $field13->uitype = 71;
    $field13->typeofdata = 'N~O';
    
    $block1->addField($field13);
}
$field14 = Vtiger_Field::getInstance('amount_claimant', $module1);
if ($field14) {
    echo "<li>The amount_claimant field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_CLAIMS_AMOUNTPC';
    $field14->name = 'amount_claimant';
    $field14->table = 'vtiger_claims';
    $field14->column = 'amount_claimant';
    $field14->columntype = 'DECIMAL(25,8)';
    $field14->uitype = 71;
    $field14->typeofdata = 'N~O';
    
    $block1->addField($field14);
}
$field15 = Vtiger_Field::getInstance('amount_vendors', $module1);
if ($field15) {
    echo "<li>The amount_vendors field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_CLAIMS_AMOUNTPV';
    $field15->name = 'amount_vendors';
    $field15->table = 'vtiger_claims';
    $field15->column = 'amount_vendors';
    $field15->columntype = 'DECIMAL(25,8)';
    $field15->uitype = 71;
    $field15->typeofdata = 'N~O';
    
    $block1->addField($field15);
}
$field16 = Vtiger_Field::getInstance('charged_contractors', $module1);
if ($field16) {
    echo "<li>The charged_contractors field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_CLAIMS_CHARGEDC';
    $field16->name = 'charged_contractors';
    $field16->table = 'vtiger_claims';
    $field16->columnname = 'charged_contractors';
    $field16->columntype = 'DECIMAL(25,8)';
    $field16->uitype = 71;
    $field16->typeofdata = 'N~O';
    
    $block1->addField($field16);
}
$field17 = Vtiger_Field::getInstance('charged_company', $module1);
if ($field17) {
    echo "<li>The charged_company field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_CLAIMS_CHARGEDCOMP';
    $field17->name = 'charged_company';
    $field17->table = 'vtiger_claims';
    $field17->column = 'charged_company';
    $field17->columntype = 'DECIMAL(25,8)';
    $field17->uitype = 71;
    $field17->typeofdata = 'N~O';
    
    $block1->addField($field17);
}
$field18 = Vtiger_Field::getInstance('assigned_user_id', $module1);
if ($field18) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'Assigned To';
    $field18->name = 'assigned_user_id';
    $field18->table = 'vtiger_crmentity';
    $field18->column = 'smownerid';
    $field18->uitype = 53;
    $field18->typeofdata = 'V~M';
    
    $block1->addField($field18);
}
$field19 = Vtiger_Field::getInstance('claims_order', $module1);
if ($field19) {
    echo "<li>The claims_order field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_CLAIMS_ORDER';
    $field19->name = 'claims_order';
    $field19->table = 'vtiger_claims';
    $field19->column = 'claims_order';
    $field19->columntype = 'VARCHAR(255)';
    $field19->uitype = 10;
    $field19->typeofdata = 'V~O';
    
    //This field is related to orders only set Related if Orders already exists
    $block1->addField($field19);
    $tempModule = Vtiger_Module::getInstance('Orders');
    if ($tempModule) {
        $field19->setRelatedModules(array('Orders'));
    } else {
        echo "<h2>Unable to set Related to Orders as Orders does not exist</h2><br>";
    }
}
//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1

//start block2 : LBL_CLAIMS_RECORDUPDATE
$block2 = Vtiger_Block::getInstance('LBL_CLAIMS_RECORDUPDATE', $module1);
if ($block2) {
    echo "<h3> LBL_CLAIMS_RECORDUPDATE block already exists </h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_CLAIMS_RECORDUPDATE';
    $module1->addBlock($block2);
}
//start block2 fields
echo "<ul>";
$field20 = Vtiger_Field::getInstance('createdtime', $module1);
if ($field20) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_CLAIMS_CREATEDTIME';
    $field20->name = 'createdtime';
    $field20->table = 'vtiger_crmentity';
    $field20->column = 'createdtime';
    $field20->columntype = 'datetime';
    $field20->uitype = 70;
    $field20->typeofdata = 'T~O';
    $field20->displaytype = 2;
    
    $block2->addField($field20);
}
$field21 = Vtiger_Field::getInstance('modifiedtime', $module1);
if ($field21) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_CLAIMS_MODIFIEDTIME';
    $field21->name = 'modifiedtime';
    $field21->table = 'vtiger_crmentity';
    $field21->column = 'modifiedtime';
    $field21->columntype = 'datetime';
    $field21->uitype = 70;
    $field21->typeofdata = 'T~O';
    $field21->displaytype = 2;
    
    $block2->addField($field21);
}
//end block2 fields
echo "</ul>";
$block2->save($module1);
//end block2

//Start ClaimItems Module
$module2 = Vtiger_Module::getInstance('ClaimItems');
if ($module2) {
    echo "<h2>Updating ClaimItems Fields</h2><br>";
} else {
    $module2 = new Vtiger_Module();
    $module2->name = 'ClaimItems';
    $module2->save();
    echo "<h2>Creating Module ClaimItems and Updating Fields</h2><br>";
    $module2->initTables();
}

//start block3 : LBL_CLAIMITEMS_INFORMATION
$block3 = Vtiger_Block::getInstance('LBL_CLAIMITEMS_INFORMATION', $module2);
if ($block3) {
    echo "<h3> LBL_CLAIMITEMS_INFORMATION block already exists </h3><br>";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_CLAIMITEMS_INFORMATION';
    $module2->addBlock($block3);
    $claimItemsIsNew = true;
}
//start block3 fields
echo "<ul>";

$field22 = Vtiger_Field::getInstance('inventory_number', $module2);
if ($field22) {
    echo "<li>The inventory_number field already exists</li><br>";
} else {
    echo "this is going the field should be showing up";
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_CLAIMITEMS_INVENTORY';
    $field22->name = 'inventory_number';
    $field22->table = 'vtiger_claimitems';
    $field22->column = 'inventory_number';
    $field22->columntype = 'VARCHAR(255)';
    $field22->uitype = 1;
    $field22->typeofdata = 'V~M';

    $block3->addField($field22);
    $module2->setEntityIdentifier($field22);
}

$field23 = Vtiger_Field::getInstance('item_status', $module2);
if ($field23) {
    echo "<li>The item_status field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_CLAIMITEMS_ITEMSTATUS';
    $field23->name = 'item_status';
    $field23->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field23->column = 'item_status';   //  This will be the columnname in your database for the new field.
    $field23->columntype = 'VARCHAR(255)';
    $field23->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field23->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field23);
    $field23->setPicklistValues(array('Open', 'Waiting On Quote', 'Repaired', 'Missing', 'Found', 'Denied', 'Closed'));
}

$field24 = Vtiger_Field::getInstance('item_description', $module2);
if ($field24) {
    echo "<li>The item_description field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_CLAIMITEMS_ITEMDESCRIPTION';
    $field24->name = 'item_description';
    $field24->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field24->column = 'item_description';   //  This will be the columnname in your database for the new field.
    $field24->columntype = 'VARCHAR(255)';
    $field24->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field24->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field24);
}

$field25 = Vtiger_Field::getInstance('carrier_exception', $module2);
if ($field25) {
    echo "<li>The carrier_exception field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_CLAIMITEMS_CARRIEREXCEPTION';
    $field25->name = 'carrier_exception';
    $field25->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field25->column = 'carrier_exception';   //  This will be the columnname in your database for the new field.
    $field25->columntype = 'VARCHAR(100)';
    $field25->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field25->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field25);
}

$field26 = Vtiger_Field::getInstance('shipper_exception', $module2);
if ($field26) {
    echo "<li>The shipper_exception field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_CLAIMITEMS_SHIPPEREXCEPTION';
    $field26->name = 'shipper_exception';
    $field26->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field26->column = 'shipper_exception';   //  This will be the columnname in your database for the new field.
    $field26->columntype = 'VARCHAR(100)';
    $field26->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field26->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field26);
}

$field27 = Vtiger_Field::getInstance('tag_color', $module2);
if ($field27) {
    echo "<li>The tag_color field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_CLAIMITEMS_TAGCOLOR';
    $field27->name = 'tag_color';
    $field27->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field27->column = 'tag_color';   //  This will be the columnname in your database for the new field.
    $field27->columntype = 'VARCHAR(100)';
    $field27->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field27->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field27);
}

$field28 = Vtiger_Field::getInstance('original_cost', $module2);
if ($field28) {
    echo "<li>The original_cost field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CLAIMITEMS_ORIGINALCOST';
    $field28->name = 'original_cost';
    $field28->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field28->column = 'original_cost';   //  This will be the columnname in your database for the new field.
    $field28->columntype = 'VARCHAR(255)';
    $field28->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field28->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field28);
}
$field29 = Vtiger_Field::getInstance('date_purchased', $module2);
if ($field29) {
    echo "<li>The date_purchased field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_CLAIMITEMS_DATEPURCHASED';
    $field29->name = 'date_purchased';
    $field29->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field29->column = 'date_purchased';   //  This will be the columnname in your database for the new field.
    $field29->columntype = 'DATE';
    $field29->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field29->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field29);
}

$field30 = Vtiger_Field::getInstance('item_claimamount', $module2);
if ($field30) {
    echo "<li>The item_claimamount field already exists</li><br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_CLAIMITEMS_ITEMCLAIMA';
    $field30->name = 'item_claimamount';
    $field30->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field30->column = 'item_claimamount';   //  This will be the columnname in your database for the new field.
    $field30->columntype = 'VARCHAR(255)';
    $field30->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field30->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field30);
}

$field31 = Vtiger_Field::getInstance('claim_description', $module2);
if ($field31) {
    echo "<li>The claim_description field already exists</li><br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'LBL_CLAIMITEMS_CLAIMDESCRIPTION';
    $field31->name = 'claim_description';
    $field31->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field31->column = 'claim_description';   //  This will be the columnname in your database for the new field.
    $field31->columntype = 'VARCHAR(255)';
    $field31->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field31->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field31);
}

$field32 = Vtiger_Field::getInstance('linked_claim', $module2);
if ($field32) {
    echo "<li>The linked_claim field already exists</li><br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_CLAIMITEMS_CLAIM';
    $field32->name = 'linked_claim';
    $field32->table = 'vtiger_claimitems';
    $field32->column = 'linked_claim';
    $field32->columntype = 'VARCHAR(255)';
    $field32->uitype = 10;
    $field32->typeofdata = 'V~O';

    $block3->addField($field32);
    $tempModule = Vtiger_Module::getInstance('Claims');
    if ($tempModule) {
        $field32->setRelatedModules(array('Claims'));
    } else {
        echo "<h2>Unable to set related module Claims as Claims does not exists</h2><br>";
    }
}
$field33 = Vtiger_Field::getInstance('assigned_user_id', $module2);
if ($field33) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'Assigned To';
    $field33->name = 'assigned_user_id';
    $field33->table = 'vtiger_crmentity';
    $field33->column = 'smownerid';
    $field33->uitype = 53;
    $field33->typeofdata = 'V~M';

    $block3->addField($field33);
}
//end block3 fields
echo "</ul>";
$block3->save($module2);
//end block3

//start block4 : LBL_CLAIMITEMS_SERVICEPROVIDER
$block4 = Vtiger_Block::getInstance('LBL_CLAIMITEMS_SERVICEPROVIDER', $module2);
if ($block4) {
    echo "<h3>The LBL_CLAIMITEMS_SERVICEPROVIDER block already exists</h3><br>";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_CLAIMITEMS_SERVICEPROVIDER';
    $module2->addBlock($block4);
}
//start block4 fields
echo "<ul>";

$field34 = Vtiger_Field::getInstance('claims_agents', $module2);
if ($field34) {
    echo "<li>The claims_agents field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'LBL_CLAIMITEMS_AGENTS';
    $field34->name = 'claims_agents';
    $field34->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field34->column = 'claims_agents';   //  This will be the columnname in your database for the new field.
    $field34->columntype = 'VARCHAR(255)';
    $field34->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field34->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field34);
    $tempModule = Vtiger_Module::getInstance('Agents');
    if ($tempModule) {
        $field34->setRelatedModules(array('Agents'));
    } else {
        echo "<h2>Unable to set related module Agents as Agents does not exist</h2><br>";
    }
}
/*
$field35 = Vtiger_Field::getInstance('claims_contractors', $module2);
if($field35) {
    echo "<li>The claims_contractors field already exists</li><br>";
}
else {
    $field35 = new Vtiger_Field();
    $field35->label = 'LBL_CLAIMITEMS_CONTRACTOR';
    $field35->name = 'claims_contractors';
    $field35->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field35->column = 'claims_contractors';   //  This will be the columnname in your database for the new field.
    $field35->columntype = 'VARCHAR(255)';
    $field35->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field35->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field35);
    $tempModule = Vtiger_Module::getInstance('Contractors');
    if($tempModule) {
        $field35->setRelatedModules(Array('Contractors'));
    }
    else {
        echo "<h2>Unable to set related module Contractors as Contractors does not exist</h2><br>";
    }
}*/

$field36 = Vtiger_Field::getInstance('claims_employees', $module2);
if ($field36) {
    echo "<li>The claims_employees field already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'LBL_CLAIMITEMS_EMPLOYEES';
    $field36->name = 'claims_employees';
    $field36->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field36->column = 'claims_employees';   //  This will be the columnname in your database for the new field.
    $field36->columntype = 'VARCHAR(255)';
    $field36->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field36->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block4->addField($field36);
    $tempModule = Vtiger_Module::getInstance('Employees');
    if ($tempModule) {
        $field36->setRelatedModules(array('Employees'));
    } else {
        echo "<h2>Unable to set related module Employees as Employees does not exist</h2><br>";
    }
}
echo "</ul>";
$block4->save($module2);
//end block4

//start block5 : LBL_CLAIMITEMS_PAYMENTS
$block5 = Vtiger_Block::getInstance('LBL_CLAIMITEMS_PAYMENTS', $module2);
if ($block5) {
    echo "<h3>The LBL_CLAIMITEMS_PAYMENTS block already exists</h3><br>";
} else {
    $block5 = new Vtiger_Block();
    $block5->label = 'LBL_CLAIMITEMS_PAYMENTS';
    $module2->addBlock($block5);
}
//start block5 fields
echo "<ul>";

$field37 = Vtiger_Field::getInstance('claims_vendors', $module2);
if ($field37) {
    echo "<li>The claims_vendors field already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'LBL_CLAIMITEMS_VENDORS';
    $field37->name = 'claims_vendors';
    $field37->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field37->column = 'claims_vendors';   //  This will be the columnname in your database for the new field.
    $field37->columntype = 'VARCHAR(255)';
    $field37->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field37->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field37);
    $tempModule = Vtiger_Module::getInstance('Vendors');
    if ($tempModule) {
        $field37->setRelatedModules(array('Vendors'));
    } else {
        echo "<h2>Unable to set related module Vendors as Vendors does not exist</h2><br>";
    }
}
$field38 = Vtiger_Field::getInstance('paid_vendor', $module2);
if ($field38) {
    echo "<li>The paid_vendor field already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'LBL_CLAIMITEMS_APVENDOR';
    $field38->name = 'paid_vendor';
    $field38->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field38->column = 'paid_vendor';   //  This will be the columnname in your database for the new field.
    $field38->columntype = 'INT(20)';
    $field38->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field38->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field38);
}
$field39 = Vtiger_Field::getInstance('chargedback_contractors', $module2);
if ($field39) {
    echo "<li>The chargedback_contractors field already exists</li><br>";
} else {
    $field39 = new Vtiger_Field();
    $field39->label = 'LBL_CLAIMITEMS_CHARGECONTRACTORS';
    $field39->name = 'chargedback_contractors';
    $field39->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field39->column = 'chargedback_contractors';   //  This will be the columnname in your database for the new field.
    $field39->columntype = 'INT(20)';
    $field39->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field39->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field39);
}
$field40 = Vtiger_Field::getInstance('chargedback_company', $module2);
if ($field40) {
    echo "<li>The chargedback_company field already exists</li><br>";
} else {
    $field40 = new Vtiger_Field();
    $field40->label = 'LBL_CLAIMITEMS_CHARGECOMPANY';
    $field40->name = 'chargedback_company';
    $field40->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field40->column = 'chargedback_company';   //  This will be the columnname in your database for the new field.
    $field40->columntype = 'INT(20)';
    $field40->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field40->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field40);
}

$field41 = Vtiger_Field::getInstance('paid_claimant', $module2);
if ($field41) {
    echo "<li>The paid_claimant field already exists</li><br>";
} else {
    $field41 = new Vtiger_Field();
    $field41->label = 'LBL_CLAIMITEMS_APCLAIMANT';
    $field41->name = 'paid_claimant';
    $field41->table = 'vtiger_claimitems';  // This is the tablename from your database that the new field will be added to.
    $field41->column = 'paid_claimant';   //  This will be the columnname in your database for the new field.
    $field41->columntype = 'INT(20)';
    $field41->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field41->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block5->addField($field41);
}

echo "</ul>";
$block5->save($module2);
//end block5

//start block6 : LBL_CLAIMITEMS_RECORDUPDATE
$block6 = Vtiger_Block::getInstance('LBL_CLAIMITEMS_RECORDUPDATE', $module2);
if ($block6) {
    echo "<h3>The LBL_CLAIMITEMS_RECORDUPDATE block already exists</h3><br>";
} else {
    $block6 = new Vtiger_Block();
    $block6->label = 'LBL_CLAIMITEMS_RECORDUPDATE';
    $module2->addBlock($block6);
}
//start block6 fields
echo "<ul>";
$field42 = Vtiger_Field::getInstance('createdtime', $module2);
if ($field42) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field42 = new Vtiger_Field();
    $field42->label = 'Created Time';
    $field42->name = 'createdtime';
    $field42->table = 'vtiger_crmentity';
    $field42->column = 'createdtime';
    $field42->uitype = 70;
    $field42->typeofdata = 'T~O';
    $field42->displaytype = 2;

    $block6->addField($field42);
}

$field43 = Vtiger_Field::getInstance('modifiedtime', $module2);
if ($field43) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field43 = new Vtiger_Field();
    $field43->label = 'Modified Time';
    $field43->name = 'modifiedtime';
    $field43->table = 'vtiger_crmentity';
    $field43->column = 'modifiedtime';
    $field43->uitype = 70;
    $field43->typeofdata = 'T~O';
    $field43->displaytype = 2;

    $block6->addField($field43);
}
echo "</ul>";
$block6->save($module2);
//end Module 2

//add ModComments Widget
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Claims'));
ModComments::removeWidgetFrom('Claims');
ModComments::addWidgetTo('Claims');
//end ModComments Widget
//add ModTracker Widget
// Adds the Updates link to the vertical navigation menu on the right.
ModTracker::enableTrackingForModule($module1->id);

if ($claimsIsNew) {
    //setRelatedList side bar to Claim Items
    echo "Claims Is New<br>";
    $module1->unsetRelatedList(Vtiger_Module::getInstance('ClaimItems'), 'Claim Items', 'get_related_list');
    $module1->setRelatedList(Vtiger_Module::getInstance('ClaimItems'), 'Claim Items', array('add'), 'get_related_list');
    
    $module1->setDefaultSharing();
    $module1->initWebservice();
    Vtiger_Filter::deleteForModule($module1);
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);
    $filter1->addField($field1)->addField($field5, 1)->addField($field3, 2);

    $ordersInstance = Vtiger_Module::getInstance('Orders');
    $ordersInstance->setRelatedList(Vtiger_Module::getInstance('Claims'), 'Claims', array('ADD'), 'get_related_list');
//END Add navigation link in module
} else {
    echo "<h2>Hiding all pre-existing fields</h2><br>";
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE tabid = '.$module1->id);
    
    //Reorder the blocks
    echo "<h2>Reordering Blocks</h2><br>";
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 1 WHERE blockid  = ' . $block1->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 2 WHERE blockid  = ' . $block2->id);
    
    //Reorder the fields
    echo "<h2>Reordering Fields</h2><br>";
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 1, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field1->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 2, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field2->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 3, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field3->id);
    //this field was commented out
    //Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 4, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field4->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 4, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field5->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 5, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field6->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 6, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field7->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 7, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field8->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 8, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field9->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 9, presence = 0'. ' WHERE fieldid = ' . $field10->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 10, presence = 0'. ' WHERE fieldid = ' . $field11->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 11, presence = 0'. ' WHERE fieldid = ' . $field12->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 12, presence = 0'. ' WHERE fieldid = ' . $field13->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 13, presence = 0'. ' WHERE fieldid = ' . $field14->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 14, presence = 0'. ' WHERE fieldid = ' . $field15->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 15, presence = 0'. ' WHERE fieldid = ' . $field16->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 16, presence = 0'. ' WHERE fieldid = ' . $field17->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 17, presence = 0'. ' WHERE fieldid = ' . $field18->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 18, presence = 0'. ' WHERE fieldid = ' . $field19->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 1, presence = 2, displaytype = 2'. ' WHERE fieldid = ' . $field20->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 2, presence = 2, displaytype = 2'. ' WHERE fieldid = ' . $field21->id);
}

if ($claimItemsIsNew) {
    $filter2 = new Vtiger_Filter();
    $filter2->name = 'All';
    $filter2->isdefault = true;
    $module2->addFilter($filter2);
    $filter2->addField($field22);
    $module2->setDefaultSharing();
    $module2->initWebservice();
    
    echo "ClaimsItems Is New<br>";
} else {
    echo "<h2>Hiding all pre-existing fields</h2><br>";
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE tabid = '.$module2->id);
    
    //Reorder the blocks
    echo "<h2>Reordering Blocks</h2><br>";
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 1 WHERE blockid  = ' . $block3->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 2 WHERE blockid  = ' . $block4->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 3 WHERE blockid  = ' . $block5->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_blocks` SET sequence = 4 WHERE blockid  = ' . $block6->id);
    
    //Reorder the fields
    echo "<h2>Reordering Fields</h2><br>";
    
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 1, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field22->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 2, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field23->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 3, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field24->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 4, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field25->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 5, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field26->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 6, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field27->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 7, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field28->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 8, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field29->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 9, presence = 0, summaryfield = 1'. ' WHERE fieldid = ' . $field30->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 10, presence = 0'. ' WHERE fieldid = ' . $field31->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 11, presence = 0'. ' WHERE fieldid = ' . $field32->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block3->id . ', sequence = 12, presence = 0'. ' WHERE fieldid = ' . $field33->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field34->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field35->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block4->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field36->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field37->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field38->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field39->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field40->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block5->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field41->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block6->id . ', sequence = 1, presence = 2, displaytype = 2'. ' WHERE fieldid = ' . $field42->id);
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block6->id . ', sequence = 1, presence = 2, displaytype = 2'. ' WHERE fieldid = ' . $field43->id);
}
echo "<h1>END OF SCRIPT</h1><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";