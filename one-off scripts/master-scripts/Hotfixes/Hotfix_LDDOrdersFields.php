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


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$ordersInstance = Vtiger_Module::getInstance('Orders');




$field5 = Vtiger_Field::getInstance('ordersstatus', $ordersInstance);

$field7 = Vtiger_Field::getInstance('linktoaccountscontacts', $ordersInstance);
    
$field9 = Vtiger_Field::getInstance('orders_no', $ordersInstance);
$field18 = Vtiger_Field::getInstance('orders_account', $ordersInstance);


    

$field19 = Vtiger_Field::getInstance('orders_accounttype', $ordersInstance);


$field20 = Vtiger_Field::getInstance('orders_vanlineregnum', $ordersInstance);



$field25 = Vtiger_Field::getInstance('orders_elinehaul', $ordersInstance);


$field26 = Vtiger_Field::getInstance('orders_etotal', $ordersInstance);






// Add orders fields to Date block
$field31 = Vtiger_Field::getInstance('orders_pdate', $ordersInstance);

$field32 = Vtiger_Field::getInstance('orders_ldate', $ordersInstance);


$field33 = Vtiger_Field::getInstance('orders_ddate', $ordersInstance);


$field34 = Vtiger_Field::getInstance('orders_ptdate', $ordersInstance);


$field35 = Vtiger_Field::getInstance('orders_ltdate', $ordersInstance);


$field36 = Vtiger_Field::getInstance('orders_dtdate', $ordersInstance);

$field39 = Vtiger_Field::getInstance('orders_ppdate', $ordersInstance);


$field40 = Vtiger_Field::getInstance('orders_pldate', $ordersInstance);


$field41 = Vtiger_Field::getInstance('orders_pddate', $ordersInstance);


//add fields to address block

$field42 = Vtiger_Field::getInstance('origin_address1', $ordersInstance);


$field43 = Vtiger_Field::getInstance('origin_address2', $ordersInstance);

$field44 = Vtiger_Field::getInstance('origin_city', $ordersInstance);


$field45 = Vtiger_Field::getInstance('origin_state', $ordersInstance);

$field46 = Vtiger_Field::getInstance('origin_zip', $ordersInstance);


$field47 = Vtiger_Field::getInstance('origin_country', $ordersInstance);


$field48 = Vtiger_Field::getInstance('origin_phone1', $ordersInstance);


$field49 = Vtiger_Field::getInstance('origin_phone2', $ordersInstance);


$field50 = Vtiger_Field::getInstance('origin_description', $ordersInstance);


$field51 = Vtiger_Field::getInstance('destination_address1', $ordersInstance);


$field52 = Vtiger_Field::getInstance('destination_address2', $ordersInstance);


$field53 = Vtiger_Field::getInstance('destination_city', $ordersInstance);
    

$field54 = Vtiger_Field::getInstance('destination_state', $ordersInstance);
    

$field55 = Vtiger_Field::getInstance('destination_zip', $ordersInstance);
    

$field56 = Vtiger_Field::getInstance('destination_country', $ordersInstance);
    

//add fields to invoice detail


$field62 = Vtiger_Field::getInstance('estimate_type', $ordersInstance);


//add fields in Order weigths block

$field66 = Vtiger_Field::getInstance('orders_eweight', $ordersInstance);

$field67 = Vtiger_Field::getInstance('orders_ecube', $ordersInstance);


$field68 = Vtiger_Field::getInstance('orders_pcount', $ordersInstance);


$field69 = Vtiger_Field::getInstance('orders_aweight', $ordersInstance);


$field70 = Vtiger_Field::getInstance('orders_gweight', $ordersInstance);


$field71 = Vtiger_Field::getInstance('orders_tweight', $ordersInstance);


$field72 = Vtiger_Field::getInstance('orders_netweight', $ordersInstance);


$field73 = Vtiger_Field::getInstance('orders_minweight', $ordersInstance);


$field74 = Vtiger_Field::getInstance('orders_rgweight', $ordersInstance);


$field75 = Vtiger_Field::getInstance('orders_rtweight', $ordersInstance);


$field76 = Vtiger_Field::getInstance('orders_rnetweight', $ordersInstance);


$ordersblockInstance8 = Vtiger_Block::getInstance('LBL_LONGDISPATCH_INFO', $ordersInstance);
    if ($ordersblockInstance8) {
        echo "<br> block 'LBL_LONGDISPATCH_INFO' already exists.<br>";
    } else {
        $ordersblockInstance8  = new Vtiger_Block();
        $ordersblockInstance8 ->label = 'LBL_LONGDISPATCH_INFO';
        $ordersInstance ->addBlock($ordersblockInstance8);
    }


        $field77 = Vtiger_Field::getInstance('orders_onhold', $ordersInstance);
        
if ($field77) {
    echo "<br> Field 'orders_onhold' is already present. <br>";
} else {
    $field77 = new Vtiger_Field();
    $field77->label = 'On Hold';
    $field77->name = 'orders_onhold';
    $field77->table = 'vtiger_orders';
    $field77->column = 'orders_onhold';
    $field77->columntype = 'VARCHAR(3)';
    $field77->uitype = 56;
    $field77->typeofdata = 'C~O';

    $ordersblockInstance8->addField($field77);
}

        $field78 = Vtiger_Field::getInstance('orders_apu', $ordersInstance);

if ($field78) {
    echo "<br> Field 'orders_onhold' is already present. <br>";
} else {
    $field78 = new Vtiger_Field();
    $field78->label = 'Agent PickUp';
    $field78->name = 'orders_apu';
    $field78->table = 'vtiger_orders';
    $field78->column = 'orders_apu';
    $field78->columntype = 'VARCHAR(3)';
    $field78->uitype = 56;
    $field78->typeofdata = 'C~O';

    $ordersblockInstance8->addField($field78);
}

 $field79 = Vtiger_Field::getInstance('orders_assignedtrip', $ordersInstance);

if ($field79) {
    echo "<br> Field 'orders_assignedtrip' is already present. <br>";
} else {
    $field79 = new Vtiger_Field();
    $field79->label = 'Assigned Trip';
    $field79->name = 'orders_assignedtrip';
    $field79->table = 'vtiger_orders';
    $field79->column = 'orders_assignedtrip';
    $field79->columntype = 'VARCHAR(3)';
    $field79->uitype = 56;
    $field79->typeofdata = 'C~O';

    $ordersblockInstance8->addField($field79);
}

 $field80 = Vtiger_Field::getInstance('orders_trip', $ordersInstance);

if ($field80) {
    echo "<br> Field 'orders_trip' is already present. <br>";
} else {
    $field80 = new Vtiger_Field();
    $field80->label = 'Trip Id';
    $field80->name = 'orders_trip';
    $field80->table = 'vtiger_orders';
    $field80->column = 'orders_trip';
    $field80->columntype = 'VARCHAR(15)';
    $field80->uitype = 10;
    $field80->typeofdata = 'V~O';

    $ordersblockInstance8->addField($field80);
    $field80->setRelatedModules(array('Trips'));
}

 $field81 = Vtiger_Field::getInstance('orders_pudate', $ordersInstance);

if ($field81) {
    echo "<br> Field 'orders_pudate' is already present. <br>";
} else {
    $field81 = new Vtiger_Field();
    $field81->label = 'PickUp Date';
    $field81->name = 'orders_pudate';
    $field81->table = 'vtiger_orders';
    $field81->column = 'orders_pudate';
    $field81->columntype = 'DATE';
    $field81->uitype = 5;
    $field81->typeofdata = 'D~O';

    $ordersblockInstance8->addField($field81);
}

 $field82 = Vtiger_Field::getInstance('orders_actualpudate', $ordersInstance);

if ($field82) {
    echo "<br> Field 'orders_actualpudate' is already present. <br>";
} else {
    $field82 = new Vtiger_Field();
    $field82->label = 'Actual Pickup Date';
    $field82->name = 'orders_actualpudate';
    $field82->table = 'vtiger_orders';
    $field82->column = 'orders_actualpudate';
    $field82->columntype = 'DATE';
    $field82->uitype = 5;
    $field82->typeofdata = 'D~O';

    $ordersblockInstance8->addField($field82);
}

$ordersblockInstance8->save($ordersInstance);

$ordersblockInstance2 = Vtiger_Block::getInstance('LBL_ORDERS_ORIGINADDRESS', $ordersInstance);
    if ($ordersblockInstance2) {
        echo "<br> block 'LBL_ORDERS_ORIGINADDRESS' already exists.<br>";
    } else {
        $ordersblockInstance2 = new Vtiger_Block();
        $ordersblockInstance2->label = 'LBL_ORDERS_ORIGINADDRESS';
        $ordersInstance->addBlock($ordersblockInstance2);
    }
  
        
$field83 = Vtiger_Field::getInstance('origin_zone', $ordersInstance);

if ($field83) {
    echo "<br> Field 'origin_zone' is already present. <br>";
} else {
    $field83 = new Vtiger_Field();
    $field83->label = 'Origin Zone';
    $field83->name = 'origin_zone';
    $field83->table = 'vtiger_orders';
    $field83->column = 'origin_zone';
    $field83->columntype = 'VARCHAR(150)';
    $field83->uitype = 16;
    $field83->typeofdata = 'V~O';
    
    $ordersblockInstance2->addField($field83);
}


$field84 = Vtiger_Field::getInstance('empty_zone', $ordersInstance);

if ($field84) {
    echo "<br> Field 'empty_zone' is already present. <br>";
} else {
    $field84 = new Vtiger_Field();
    $field84->label = 'Destination Zone';
    $field84->name = 'empty_zone';
    $field84->table = 'vtiger_orders';
    $field84->column = 'empty_zone';
    $field84->columntype = 'VARCHAR(150)';
    $field84->uitype = 16;
    $field84->typeofdata = 'V~O';

    $ordersblockInstance2->addField($field84);
}



$field85 = Vtiger_Field::getInstance('business_line', $ordersInstance);

if ($field85) {
    echo "<br> Field 'orders_rnetweight' is already present. <br>";
} else {
    $field85 = new Vtiger_Field();
    $field85->label = 'LBL_ORDERS_BUSINESSLINE';
    $field85->name = 'business_line';
    $field85->table = 'vtiger_orders';
    $field85->column = 'business_line';
    $field85->columntype = 'VARCHAR(150)';
    $field85->uitype = 16;
    $field85->typeofdata = 'V~O';

    $ordersblockInstance2->addField($field85);
}

$field86 = Vtiger_Field::getInstance('billing_type', $ordersInstance);
if ($field86) {
    echo "Field billing_type already exists in Orders module<br />";
} else {
    $field86 = new Vtiger_Field();
    $field86->label = 'LBL_ORDERS_BILLINGTYPE';
    $field86->name = 'billing_type';
    $field86->table = 'vtiger_orders';
    $field86->column = 'billing_type';
    $field86->columntype = 'VARCHAR(255)';
    $field86->uitype = 16;
    $field86->typeofdata = 'V~O';
    
    $ordersblockInstance2->addField($field86);
}

$ordersblockInstance2->save($ordersInstance);

      
        //add filter in orders module -- LDD Unassigned
$filter2 = Vtiger_Filter::getInstance('LDD Un Assigned', $ordersInstance);
    if ($filter2) {
        echo "<br> Filter exists <br>";
    } else {
        $filter2 = new Vtiger_Filter();
        $filter2->name = 'LDD Un Assigned';
        $filter2->isdefault = false;
        $ordersInstance->addFilter($filter2);

        $filter2->addField($field32)->addField($field35, 1)->addField($field33, 2)->addField($field36, 3)->addField($field9, 4)->addField($field80, 5)->addField($field85, 6)->addField($field7, 7)->addField($field44, 8)->addField($field45, 9)->addField($field53, 10)->addField($field53, 11)->addField($field54, 12)->addField($field66, 13)->addField($field67, 14)->addField($field25, 15);
    
        $filter2->addRule($field79, 'EQUALS', '0', 0, 1, 'and')->addRule($field85, 'EQUALS', 'Interstate Move', 1, 1, 'and')->addRule($field77, 'EQUALS', '1', 0, 1, '');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1,$filter2->id, 'and', '0 and 1 and 2')");
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_customview SET status=0 WHERE cvid=$filter2->id");
    }
        
        $filter3 = Vtiger_Filter::getInstance('LDD Assigned', $ordersInstance);
    if ($filter3) {
        echo "<br> Filter exists <br>";
    } else {
        $filter3 = new Vtiger_Filter();
        $filter3->name = 'LDD Assigned';
        $filter3->isdefault = false;
        $ordersInstance->addFilter($filter3);

        $filter3->addField($field32)->addField($field35, 1)->addField($field33, 2)->addField($field36, 3)->addField($field9, 4)->addField($field80, 5)->addField($field85, 6)->addField($field7, 7)->addField($field44, 8)->addField($field45, 9)->addField($field53, 10)->addField($field53, 11)->addField($field54, 12)->addField($field66, 13)->addField($field67, 14)->addField($field25, 15);
    
        $filter3->addRule($field79, 'EQUALS', '1', 0, 1, 'and')->addRule($field85, 'EQUALS', 'Interstate Move', 1, 1, 'and')->addRule($field77, 'EQUALS', '1', 0, 1, '');
        ;
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1,$filter3->id, 'and', '0 and 1 and 2')");
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_customview SET status=0 WHERE cvid=$filter3->id");
    }
        
        $filter4 = Vtiger_Filter::getInstance('LDD On Hold', $ordersInstance);
    if ($filter4) {
        echo "<br> Filter exists <br>";
    } else {
        $filter4 = new Vtiger_Filter();
        $filter4->name = 'LDD On Hold';
        $filter4->isdefault = false;
        $ordersInstance->addFilter($filter4);

        $filter4->addField($field32)->addField($field35, 1)->addField($field33, 2)->addField($field36, 3)->addField($field9, 4)->addField($field80, 5)->addField($field85, 6)->addField($field7, 7)->addField($field44, 8)->addField($field45, 9)->addField($field53, 10)->addField($field53, 11)->addField($field54, 12)->addField($field66, 13)->addField($field67, 14)->addField($field25, 15);
    
        $filter4->addRule($field77, 'EQUALS', '1', 0, 1, 'and')->addRule($field85, 'EQUALS', 'Interstate Move', 1, 1, '');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1,$filter4->id, 'and', '0 and 1')");
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_customview SET status=0 WHERE cvid=$filter4->id");
    }
        
        
 //adding new field with the script generator.

$moduleOrders = Vtiger_Module::getInstance('Orders');

$blockOrders237 = Vtiger_Block::getInstance('LBL_ORDERS_DATES', $moduleOrders);
if ($blockOrders237) {
    echo "<br> The LBL_ORDERS_DATES block already exists in Orders <br>";
} else {
    $blockOrders237 = new Vtiger_Block();
    $blockOrders237->label = 'LBL_ORDERS_DATES';
    $moduleOrders->addBlock($blockOrders237);
}

$blockOrders234 = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $moduleOrders);
if ($blockOrders234) {
    echo "<br> The LBL_ORDERS_INFORMATION block already exists in Orders <br>";
} else {
    $blockOrders234 = new Vtiger_Block();
    $blockOrders234->label = 'LBL_ORDERS_INFORMATION';
    $moduleOrders->addBlock($blockOrders234);
}

$field = Vtiger_Field::getInstance('orders_plannedloaddate', $moduleOrders);
if ($field) {
    echo "<br> The orders_plannedloaddate field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_PLANNEDLOADDATE';
    $field->name = 'orders_plannedloaddate';
    $field->table = 'vtiger_orders';
    $field->column ='orders_plannedloaddate';
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders237->addField($field);
}
$field = Vtiger_Field::getInstance('orders_planneddeliverydate', $moduleOrders);
if ($field) {
    echo "<br> The orders_planneddeliverydate field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_PLANNEDDELIVERYDATE';
    $field->name = 'orders_planneddeliverydate';
    $field->table = 'vtiger_orders';
    $field->column ='orders_planneddeliverydate';
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders237->addField($field);
}
$field = Vtiger_Field::getInstance('orders_otherstatus', $moduleOrders);
if ($field) {
    echo "<br> The orders_otherstatus field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_ORDERSOTHERSTATUS';
    $field->name = 'orders_otherstatus';
    $field->table = 'vtiger_orders';
    $field->column ='orders_otherstatus';
    $field->columntype = 'VARCHAR(150)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders234->addField($field);
    $field->setPicklistValues(['Planned', 'Confirmed', 'Loaded', 'Delivered']);
}
$field = Vtiger_Field::getInstance('orders_actualdeliverydate', $moduleOrders);
if ($field) {
    echo "<br> The orders_actualdeliverydate field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_ORDERSACTUALDELIVERYDATE';
    $field->name = 'orders_actualdeliverydate';
    $field->table = 'vtiger_orders';
    $field->column ='orders_actualdeliverydate';
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders237->addField($field);
}
$field = Vtiger_Field::getInstance('orders_sit', $moduleOrders);
if ($field) {
    echo "<br> The orders_sit field already exists in Orders <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_ORDERSSIT';
    $field->name = 'orders_sit';
    $field->table = 'vtiger_orders';
    $field->column ='orders_sit';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->displaytype = 1;
    $field->quickcreate = 1;
    $field->summaryfield = 0;

    $blockOrders234->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";