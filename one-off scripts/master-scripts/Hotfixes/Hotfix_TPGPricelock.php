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


echo "<br>In TPG/Pricelock Hotfix<br>";
$module1 = Vtiger_Module::getInstance('Estimates');
$module2 = Vtiger_Module::getInstance('Quotes');

$block1 = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $module1);
if ($block1) {
    echo "<br> The LBL_QUOTES_TPGPRICELOCK block already exists in Estimates <br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_QUOTES_TPGPRICELOCK';
    $module1->addBlock($block1);
}
$field3 = Vtiger_Field::getInstance('pricing_color_lock', $module1);
if ($field3) {
    echo "<br> The pricing_color_lock field already exists in Estimates <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_PRICING_COLOR_LOCK';
    $field3->name = 'pricing_color_lock';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'pricing_color_lock';
    $field3->columntype = 'VARCHAR(3)';
    $field3->uitype = 56;
    $field3->typeofdata = 'V~O';
    $field3->displaytype = 1;
    $field3->quickcreate = 0;
    $field3->presence = 2;
    
    $block1->addField($field3);
}
$field1 = Vtiger_Field::getInstance('pricing_color', $module1);
if ($field1) {
    echo "<br> The pricing_color field already exists in Estimates <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_QUOTES_PRICING_COLOR';
    $field1->name = 'pricing_color';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'pricing_color';
    $field1->columntype = 'VARCHAR(30)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;
    
    $block1->addField($field1);
    //only do this once or we'll end up with duplicates in the picklist
    $field1->setPicklistValues(['Green', 'Yellow', 'Blue', 'Red', 'Gold']);
}
//repeat for Quotes because it is a decoupled module
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_TPGPRICELOCK', $module2);
if ($block2) {
    echo "<br> The LBL_QUOTES_TPGPRICELOCK block already exists in Quotes <br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_QUOTES_TPGPRICELOCK';
    $module2->addBlock($block2);
}
$field4 = Vtiger_Field::getInstance('pricing_color_lock', $module2);
if ($field4) {
    echo "<br> The pricing_color_lock field already exists in Quotes <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_PRICING_COLOR_LOCK';
    $field4->name = 'pricing_color_lock';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'pricing_color_lock';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';
    $field4->displaytype = 1;
    $field4->quickcreate = 0;
    $field4->presence = 2;
    
    $block2->addField($field4);
}
$field2 = Vtiger_Field::getInstance('pricing_color', $module2);
if ($field2) {
    echo "<br> The pricing_color field already exists in Quotes <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_QUOTES_PRICING_COLOR';
    $field2->name = 'pricing_color';
    $field2->table = 'vtiger_quotes';
    $field2->column = 'pricing_color';
    $field2->columntype = 'VARCHAR(30)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;
    $field2->quickcreate = 0;
    $field2->presence = 2;
    
    $block2->addField($field2);
}
//Estimates
//5 %SMF
//6 Flat SMF
//7 Desired Total Price
//8 SMF Type
$field5 = Vtiger_Field::getInstance('percent_smf', $module1);
if ($field5) {
    echo "<br> The percent_smf field already exists in Estimates <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_QUOTES_PERCENT_SMF';
    $field5->name = 'percent_smf';
    $field5->table = 'vtiger_quotes';
    $field5->column = 'percent_smf';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 9;
    $field5->typeofdata = 'N~O';
    $field5->displaytype = 1;
    $field5->quickcreate = 0;
    $field5->presence = 2;
    
    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('flat_smf', $module1);
if ($field6) {
    echo "<br> The flat_smf field already exists in Estimates <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_QUOTES_FLAT_SMF';
    $field6->name = 'flat_smf';
    $field6->table = 'vtiger_quotes';
    $field6->column = 'flat_smf';
    $field6->columntype = 'DECIMAL(19,2)';
    $field6->uitype = 71;
    $field6->typeofdata = 'N~O';
    $field6->displaytype = 1;
    $field6->quickcreate = 0;
    $field6->presence = 2;
    
    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('desired_total', $module1);
if ($field7) {
    echo "<br> The flat_smf field already exists in Estimates <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_QUOTES_DESIRED_TOTAL';
    $field7->name = 'desired_total';
    $field7->table = 'vtiger_quotes';
    $field7->column = 'desired_total';
    $field7->columntype = 'DECIMAL(19,2)';
    $field7->uitype = 71;
    $field7->typeofdata = 'N~O';
    $field7->displaytype = 1;
    $field7->quickcreate = 0;
    $field7->presence = 2;
    
    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('smf_type', $module1);
if ($field8) {
    echo "<br> The smf_type field already exists in Estimates <br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_QUOTES_SMF_TYPE';
    $field8->name = 'smf_type';
    $field8->table = 'vtiger_quotes';
    $field8->column = 'smf_type';
    $field8->columntype = 'VARCHAR(3)';
    $field8->uitype = 56;
    $field8->typeofdata = 'V~O';
    $field8->displaytype = 1;
    $field8->quickcreate = 0;
    $field8->presence = 2;
    
    $block1->addField($field8);
}
//Quotes
//9 %SMF
//10 Flat SMF
//11 Desired Total Price
//12 SMF Type
$field9 = Vtiger_Field::getInstance('percent_smf', $module2);
if ($field9) {
    echo "<br> The percent_smf field already exists in Quotes <br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_QUOTES_PERCENT_SMF';
    $field9->name = 'percent_smf';
    $field9->table = 'vtiger_quotes';
    $field9->column = 'percent_smf';
    $field9->columntype = 'VARCHAR(255)';
    $field9->uitype = 9;
    $field9->typeofdata = 'N~O';
    $field9->displaytype = 1;
    $field9->quickcreate = 0;
    $field9->presence = 2;
    
    $block2->addField($field9);
}
$field10 = Vtiger_Field::getInstance('flat_smf', $module2);
if ($field10) {
    echo "<br> The flat_smf field already exists in Quotes <br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_QUOTES_FLAT_SMF';
    $field10->name = 'flat_smf';
    $field10->table = 'vtiger_quotes';
    $field10->column = 'flat_smf';
    $field10->columntype = 'DECIMAL(19,2)';
    $field10->uitype = 71;
    $field10->typeofdata = 'N~O';
    $field10->displaytype = 1;
    $field10->quickcreate = 0;
    $field10->presence = 2;
    
    $block2->addField($field10);
}
$field11 = Vtiger_Field::getInstance('desired_total', $module2);
if ($field11) {
    echo "<br> The flat_smf field already exists in Estimates <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_QUOTES_DESIRED_TOTAL';
    $field11->name = 'desired_total';
    $field11->table = 'vtiger_quotes';
    $field11->column = 'desired_total';
    $field11->columntype = 'DECIMAL(19,2)';
    $field11->uitype = 71;
    $field11->typeofdata = 'N~O';
    $field11->displaytype = 1;
    $field11->quickcreate = 0;
    $field11->presence = 2;
    
    $block2->addField($field11);
}
$field12 = Vtiger_Field::getInstance('smf_type', $module2);
if ($field12) {
    echo "<br> The smf_type field already exists in Quotes <br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_QUOTES_SMF_TYPE';
    $field12->name = 'smf_type';
    $field12->table = 'vtiger_quotes';
    $field12->column = 'smf_type';
    $field12->columntype = 'VARCHAR(3)';
    $field12->uitype = 56;
    $field12->typeofdata = 'V~O';
    $field12->displaytype = 1;
    $field12->quickcreate = 0;
    $field12->presence = 2;
    
    $block2->addField($field12);
}
//OT 1018
$est = Vtiger_Module::getInstance('Estimates');
$quote = Vtiger_Module::getInstance('Quotes');
if ($est && $quote) {
    //In Intersate Move Details Block
    $estBlock1 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $est);
    $estField1 = Vtiger_Field::getInstance('apply_full_pack_rate_override', $est);
    if ($estField1) {
        echo "<br> The apply_full_pack_rate_override field already exists in Estimates <br>";
    } else {
        $estField1 = new Vtiger_Field();
        $estField1->label = 'LBL_QUOTES_APPLYFULLPACKRATEOVERRIDE';
        $estField1->name = 'apply_full_pack_rate_override';
        $estField1->table = 'vtiger_quotes';
        $estField1->column = 'apply_full_pack_rate_override';
        $estField1->columntype = 'VARCHAR(3)';
        $estField1->uitype = 56;
        $estField1->typeofdata = 'V~O';
        $estField1->displaytype = 1;
        $estField1->quickcreate = 0;
        $estField1->presence = 2;
        
        $estBlock1->addField($estField1);
    }
    $estField2 = Vtiger_Field::getInstance('full_pack_rate_override', $est);
    if ($estField2) {
        echo "<br> The full_pack_rate_override field already exists in Estimates <br>";
    } else {
        $estField2 = new Vtiger_Field();
        $estField2->label = 'LBL_QUOTES_FULLPACKRATEOVERRIDE';
        $estField2->name = 'full_pack_rate_override';
        $estField2->table = 'vtiger_quotes';
        $estField2->column = 'full_pack_rate_override';
        $estField2->columntype = 'DECIMAL(19,2)';
        $estField2->uitype = 71;
        $estField2->typeofdata = 'N~O';
        $estField2->displaytype = 1;
        $estField2->quickcreate = 0;
        $estField2->presence = 2;
        
        $estBlock1->addField($estField2);
    }
    $estBlock2 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $est);
    //Apply SIT First Day Origin
    $estField3 = Vtiger_Field::getInstance('apply_sit_first_day_origin', $est);
    if ($estField3) {
        echo "<br> The apply_sit_first_day_origin field already exists in Estimates <br>";
    } else {
        $estField3 = new Vtiger_Field();
        $estField3->label = 'LBL_QUOTES_APPLYSITFIRSTDAYORIGIN';
        $estField3->name = 'apply_sit_first_day_origin';
        $estField3->table = 'vtiger_quotes';
        $estField3->column = 'apply_sit_first_day_origin';
        $estField3->columntype = 'VARCHAR(3)';
        $estField3->uitype = 56;
        $estField3->typeofdata = 'V~O';
        $estField3->displaytype = 1;
        $estField3->quickcreate = 0;
        $estField3->presence = 2;
        $estBlock2->addField($estField3);
    }
    //Apply SIT First Day Dest
    $estField4 = Vtiger_Field::getInstance('apply_sit_first_day_dest', $est);
    if ($estField4) {
        echo "<br> The apply_sit_first_day_dest field already exists in Estimates <br>";
    } else {
        $estField4 = new Vtiger_Field();
        $estField4->label = 'LBL_QUOTES_APPLYSITFIRSTDAYDEST';
        $estField4->name = 'apply_sit_first_day_dest';
        $estField4->table = 'vtiger_quotes';
        $estField4->column = 'apply_sit_first_day_dest';
        $estField4->columntype = 'VARCHAR(3)';
        $estField4->uitype = 56;
        $estField4->typeofdata = 'V~O';
        $estField4->displaytype = 1;
        $estField4->quickcreate = 0;
        $estField4->presence = 2;
        
        $estBlock2->addField($estField4);
    }
    //SIT First Day Origin
    $estField5 = Vtiger_Field::getInstance('sit_first_day_origin_override', $est);
    if ($estField5) {
        echo "<br> The sit_first_day_origin_override Field already exists in Estimates <br>";
    } else {
        $estField5 = new Vtiger_Field();
        $estField5->label = 'LBL_QUOTES_SITFIRSTDAYORIGINOVERRIDE';
        $estField5->name = 'sit_first_day_origin_override';
        $estField5->table = 'vtiger_quotes';
        $estField5->column = 'sit_first_day_origin_override';
        $estField5->columntype = 'DECIMAL(19,2)';
        $estField5->uitype = 71;
        $estField5->typeofdata = 'N~O';
        $estField5->displaytype = 1;
        $estField5->quickcreate = 0;
        $estField5->presence = 2;
        $estBlock2->addField($estField5);
    }
    //SIT First Day Dest
    $estField6 = Vtiger_Field::getInstance('sit_first_day_dest_override', $est);
    if ($estField6) {
        echo "<br> The sit_first_day_dest_override Field already exists in Estimates <br>";
    } else {
        $estField6 = new Vtiger_Field();
        $estField6->label = 'LBL_QUOTES_SITFIRSTDAYDESTOVERRIDE';
        $estField6->name = 'sit_first_day_dest_override';
        $estField6->table = 'vtiger_quotes';
        $estField6->column = 'sit_first_day_dest_override';
        $estField6->columntype = 'DECIMAL(19,2)';
        $estField6->uitype = 71;
        $estField6->typeofdata = 'N~O';
        $estField6->displaytype = 1;
        $estField6->quickcreate = 0;
        $estField6->presence = 2;
        
        $estBlock2->addField($estField6);
    }
    //Apply SIT Add'l Day Origin
    $estField7 = Vtiger_Field::getInstance('apply_sit_addl_day_origin', $est);
    if ($estField7) {
        echo "<br> The apply_sit_addl_day_origin Field already exists in Estimates <br>";
    } else {
        $estField7 = new Vtiger_Field();
        $estField7->label = 'LBL_QUOTES_APPLYSITADDLDAYORIGIN';
        $estField7->name = 'apply_sit_addl_day_origin';
        $estField7->table = 'vtiger_quotes';
        $estField7->column = 'apply_sit_addl_day_origin';
        $estField7->columntype = 'VARCHAR(3)';
        $estField7->uitype = 56;
        $estField7->typeofdata = 'V~O';
        $estField7->displaytype = 1;
        $estField7->quickcreate = 0;
        $estField7->presence = 2;
        
        $estBlock2->addField($estField7);
    }
    //Apply SIT Add'l Day Dest
    $estField8 = Vtiger_Field::getInstance('apply_sit_addl_day_dest', $est);
    if ($estField8) {
        echo "<br> The apply_sit_addl_day_dest Field already exists in Estimates <br>";
    } else {
        $estField8 = new Vtiger_Field();
        $estField8->label = 'LBL_QUOTES_APPLYSITADDLDAYDEST';
        $estField8->name = 'apply_sit_addl_day_dest';
        $estField8->table = 'vtiger_quotes';
        $estField8->column = 'apply_sit_addl_day_dest';
        $estField8->columntype = 'VARCHAR(3)';
        $estField8->uitype = 56;
        $estField8->typeofdata = 'V~O';
        $estField8->displaytype = 1;
        $estField8->quickcreate = 0;
        $estField8->presence = 2;
        
        $estBlock2->addField($estField8);
    }
    //SIT Add'l Day Origin
    $estField9 = Vtiger_Field::getInstance('sit_addl_day_origin_override', $est);
    if ($estField9) {
        echo "<br> The sit_addl_day_origin_override Field already exists in Estimates <br>";
    } else {
        $estField9 = new Vtiger_Field();
        $estField9->label = 'LBL_QUOTES_SITADDLDAYORIGINOVERRIDE';
        $estField9->name = 'sit_addl_day_origin_override';
        $estField9->table = 'vtiger_quotes';
        $estField9->column = 'sit_addl_day_origin_override';
        $estField9->columntype = 'DECIMAL(19,2)';
        $estField9->uitype = 71;
        $estField9->typeofdata = 'N~O';
        $estField9->displaytype = 1;
        $estField9->quickcreate = 0;
        $estField9->presence = 2;
        
        $estBlock2->addField($estField9);
    }
    //SIT Add'l Day Dest
    $estField10 = Vtiger_Field::getInstance('sit_addl_day_dest_override', $est);
    if ($estField10) {
        echo "<br> The sit_addl_day_dest_override Field already exists in Estimates <br>";
    } else {
        $estField10 = new Vtiger_Field();
        $estField10->label = 'LBL_QUOTES_SITADDLDAYDESTOVERRIDE';
        $estField10->name = 'sit_addl_day_dest_override';
        $estField10->table = 'vtiger_quotes';
        $estField10->column = 'sit_addl_day_dest_override';
        $estField10->columntype = 'DECIMAL(19,2)';
        $estField10->uitype = 71;
        $estField10->typeofdata = 'N~O';
        $estField10->displaytype = 1;
        $estField10->quickcreate = 0;
        $estField10->presence = 2;
        
        $estBlock2->addField($estField10);
    }
    //Apply SIT Cartage Origin
    $estField11 = Vtiger_Field::getInstance('apply_sit_cartage_origin', $est);
    if ($estField11) {
        echo "<br> The apply_sit_cartage_origin field already exists in Estimates <br>";
    } else {
        $estField11 = new Vtiger_Field();
        $estField11->label = 'LBL_QUOTES_APPLYSITCARTAGEORIGIN';
        $estField11->name = 'apply_sit_cartage_origin';
        $estField11->table = 'vtiger_quotes';
        $estField11->column = 'apply_sit_cartage_origin';
        $estField11->columntype = 'VARCHAR(3)';
        $estField11->uitype = 56;
        $estField11->typeofdata = 'V~O';
        $estField11->displaytype = 1;
        $estField11->quickcreate = 0;
        $estField11->presence = 2;
        
        $estBlock2->addField($estField11);
    }
    //Apply SIT Cartage Dest
    $estField12 = Vtiger_Field::getInstance('apply_sit_cartage_dest', $est);
    if ($estField12) {
        echo "<br> The apply_sit_cartage_dest field already exists in Estimates <br>";
    } else {
        $estField12 = new Vtiger_Field();
        $estField12->label = 'LBL_QUOTES_APPLYSITCARTAGEDEST';
        $estField12->name = 'apply_sit_cartage_dest';
        $estField12->table = 'vtiger_quotes';
        $estField12->column = 'apply_sit_cartage_dest';
        $estField12->columntype = 'VARCHAR(3)';
        $estField12->uitype = 56;
        $estField12->typeofdata = 'V~O';
        $estField12->displaytype = 1;
        $estField12->quickcreate = 0;
        $estField12->presence = 2;
        
        $estBlock2->addField($estField12);
    }
    //SIT Cartage Origin
    $estField13 = Vtiger_Field::getInstance('sit_cartage_origin_override', $est);
    if ($estField13) {
        echo "<br> The sit_cartage_origin_override field already exists in Estimates <br>";
    } else {
        $estField13 = new Vtiger_Field();
        $estField13->label = 'LBL_QUOTES_SITCARTAGEORIGINOVERRIDE';
        $estField13->name = 'sit_cartage_origin_override';
        $estField13->table = 'vtiger_quotes';
        $estField13->column = 'sit_cartage_origin_override';
        $estField13->columntype = 'DECIMAL(19,2)';
        $estField13->uitype = 71;
        $estField13->typeofdata = 'N~O';
        $estField13->displaytype = 1;
        $estField13->quickcreate = 0;
        $estField13->presence = 2;
        
        $estBlock2->addField($estField13);
    }
    //SIT Cartage Dest
    $estField14 = Vtiger_Field::getInstance('sit_cartage_dest_override', $est);
    if ($estField14) {
        echo "<br> The sit_cartage_dest_override field already exists in Estimates <br>";
    } else {
        $estField14 = new Vtiger_Field();
        $estField14->label = 'LBL_QUOTES_SITCARTAGEDESTOVERRIDE';
        $estField14->name = 'sit_cartage_dest_override';
        $estField14->table = 'vtiger_quotes';
        $estField14->column = 'sit_cartage_dest_override';
        $estField14->columntype = 'DECIMAL(19,2)';
        $estField14->uitype = 71;
        $estField14->typeofdata = 'N~O';
        $estField14->displaytype = 1;
        $estField14->quickcreate = 0;
        $estField14->presence = 2;
        
        $estBlock2->addField($estField14);
    }
    
    $estBlock3 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $est);
    //15 apply_exlabor_rate_origin
    $estField15 = Vtiger_Field::getInstance('apply_exlabor_rate_origin', $est);
    if ($estField15) {
        echo "<br> The apply_exlabor_rate_origin field already exists in Estimates <br>";
    } else {
        $estField15 = new Vtiger_Field();
        $estField15->label = 'LBL_QUOTES_APPLYEXLABORRATEORIGIN';
        $estField15->name = 'apply_exlabor_rate_origin';
        $estField15->table = 'vtiger_quotes';
        $estField15->column = 'apply_exlabor_rate_origin';
        $estField15->columntype = 'VARCHAR(3)';
        $estField15->uitype = 56;
        $estField15->typeofdata = 'V~O';
        $estField15->displaytype = 1;
        $estField15->quickcreate = 0;
        $estField15->presence = 2;
        
        $estBlock3->addField($estField15);
    }
    //16 exlabor_rate_origin
    $estField16 = Vtiger_Field::getInstance('exlabor_rate_origin', $est);
    if ($estField16) {
        echo "<br> The exlabor_rate_origin field already exists in Estimates <br>";
    } else {
        $estField16 = new Vtiger_Field();
        $estField16->label = 'LBL_QUOTES_EXLABORRATEORIGIN';
        $estField16->name = 'exlabor_rate_origin';
        $estField16->table = 'vtiger_quotes';
        $estField16->column = 'exlabor_rate_origin';
        $estField16->columntype = 'DECIMAL(19,2)';
        $estField16->uitype = 71;
        $estField16->typeofdata = 'N~O';
        $estField16->displaytype = 1;
        $estField16->quickcreate = 0;
        $estField16->presence = 2;
        
        $estBlock3->addField($estField16);
    }
    //31 exlabor_flat_origin
    $estField31 = Vtiger_Field::getInstance('exlabor_flat_origin', $est);
    if ($estField31) {
        echo "<br> The exlabor_flat_origin field already exists in Estimates <br>";
    } else {
        $estField31 = new Vtiger_Field();
        $estField31->label = 'LBL_QUOTES_EXLABORFLATORIGIN';
        $estField31->name = 'exlabor_flat_origin';
        $estField31->table = 'vtiger_quotes';
        $estField31->column = 'exlabor_flat_origin';
        $estField31->columntype = 'DECIMAL(19,2)';
        $estField31->uitype = 71;
        $estField31->typeofdata = 'N~O';
        $estField31->displaytype = 1;
        $estField31->quickcreate = 0;
        $estField31->presence = 2;
        
        $estBlock3->addField($estField31);
    }
    //17 apply_exlabor_ot_rate_origin
    $estField17 = Vtiger_Field::getInstance('apply_exlabor_ot_rate_origin', $est);
    if ($estField17) {
        echo "<br> The apply_exlabor_ot_rate_origin field already exists in Estimates <br>";
    } else {
        $estField17 = new Vtiger_Field();
        $estField17->label = 'LBL_QUOTES_APPLYEXLABOROTRATEORIGIN';
        $estField17->name = 'apply_exlabor_ot_rate_origin';
        $estField17->table = 'vtiger_quotes';
        $estField17->column = 'apply_exlabor_ot_rate_origin';
        $estField17->columntype = 'VARCHAR(3)';
        $estField17->uitype = 56;
        $estField17->typeofdata = 'V~O';
        $estField17->displaytype = 1;
        $estField17->quickcreate = 0;
        $estField17->presence = 2;
        
        $estBlock3->addField($estField17);
    }
    //18 exlabor_ot_rate_origin
    $estField18 = Vtiger_Field::getInstance('exlabor_ot_rate_origin', $est);
    if ($estField18) {
        echo "<br> The exlabor_ot_rate_origin field already exists in Estimates <br>";
    } else {
        $estField18 = new Vtiger_Field();
        $estField18->label = 'LBL_QUOTES_EXLABOROTRATEORIGIN';
        $estField18->name = 'exlabor_ot_rate_origin';
        $estField18->table = 'vtiger_quotes';
        $estField18->column = 'exlabor_ot_rate_origin';
        $estField18->columntype = 'DECIMAL(19,2)';
        $estField18->uitype = 71;
        $estField18->typeofdata = 'N~O';
        $estField18->displaytype = 1;
        $estField18->quickcreate = 0;
        $estField18->presence = 2;
        
        $estBlock3->addField($estField18);
    }
    //32 exlabor_ot_flat_origin
    $estField32 = Vtiger_Field::getInstance('exlabor_ot_flat_origin', $est);
    if ($estField32) {
        echo "<br> The exlabor_ot_flat_origin field already exists in Estimates <br>";
    } else {
        $estField32 = new Vtiger_Field();
        $estField32->label = 'LBL_QUOTES_EXLABOROTFLATORIGIN';
        $estField32->name = 'exlabor_ot_flat_origin';
        $estField32->table = 'vtiger_quotes';
        $estField32->column = 'exlabor_ot_flat_origin';
        $estField32->columntype = 'DECIMAL(19,2)';
        $estField32->uitype = 71;
        $estField32->typeofdata = 'N~O';
        $estField32->displaytype = 1;
        $estField32->quickcreate = 0;
        $estField32->presence = 2;
        
        $estBlock3->addField($estField32);
    }
    //19 apply_exlabor_rate_dest
    $estField19 = Vtiger_Field::getInstance('apply_exlabor_rate_dest', $est);
    if ($estField19) {
        echo "<br> The apply_exlabor_rate_dest field already exists in Estimates <br>";
    } else {
        $estField19 = new Vtiger_Field();
        $estField19->label = 'LBL_QUOTES_APPLYEXLABORRATEDEST';
        $estField19->name = 'apply_exlabor_rate_dest';
        $estField19->table = 'vtiger_quotes';
        $estField19->column = 'apply_exlabor_rate_dest';
        $estField19->columntype = 'VARCHAR(3)';
        $estField19->uitype = 56;
        $estField19->typeofdata = 'V~O';
        $estField19->displaytype = 1;
        $estField19->quickcreate = 0;
        $estField19->presence = 2;
        
        $estBlock3->addField($estField19);
    }
    //20 exlabor_rate_dest
    $estField20 = Vtiger_Field::getInstance('exlabor_rate_dest', $est);
    if ($estField20) {
        echo "<br> The exlabor_rate_dest field already exists in Estimates <br>";
    } else {
        $estField20 = new Vtiger_Field();
        $estField20->label = 'LBL_QUOTES_EXLABORRATEDEST';
        $estField20->name = 'exlabor_rate_dest';
        $estField20->table = 'vtiger_quotes';
        $estField20->column = 'exlabor_rate_dest';
        $estField20->columntype = 'DECIMAL(19,2)';
        $estField20->uitype = 71;
        $estField20->typeofdata = 'N~O';
        $estField20->displaytype = 1;
        $estField20->quickcreate = 0;
        $estField20->presence = 2;
        
        $estBlock3->addField($estField20);
    }
    //33 exlabor_flat_dest
    $estField33 = Vtiger_Field::getInstance('exlabor_flat_dest', $est);
    if ($estField33) {
        echo "<br> The exlabor_flat_dest field already exists in Estimates <br>";
    } else {
        $estField33 = new Vtiger_Field();
        $estField33->label = 'LBL_QUOTES_EXLABORFLATDEST';
        $estField33->name = 'exlabor_flat_dest';
        $estField33->table = 'vtiger_quotes';
        $estField33->column = 'exlabor_flat_dest';
        $estField33->columntype = 'DECIMAL(19,2)';
        $estField33->uitype = 71;
        $estField33->typeofdata = 'N~O';
        $estField33->displaytype = 1;
        $estField33->quickcreate = 0;
        $estField33->presence = 2;
        
        $estBlock3->addField($estField33);
    }
    //21 apply_exlabor_ot_rate_dest
    $estField21 = Vtiger_Field::getInstance('apply_exlabor_ot_rate_dest', $est);
    if ($estField21) {
        echo "<br> The apply_exlabor_ot_rate_dest field already exists in Estimates <br>";
    } else {
        $estField21 = new Vtiger_Field();
        $estField21->label = 'LBL_QUOTES_APPLY_EXLABOROTRATEDEST';
        $estField21->name = 'apply_exlabor_ot_rate_dest';
        $estField21->table = 'vtiger_quotes';
        $estField21->column = 'apply_exlabor_ot_rate_dest';
        $estField21->columntype = 'VARCHAR(3)';
        $estField21->uitype = 56;
        $estField21->typeofdata = 'V~O';
        $estField21->displaytype = 1;
        $estField21->quickcreate = 0;
        $estField21->presence = 2;
        
        $estBlock3->addField($estField21);
    }
    //22 exlabor_ot_rate_dest
    $estField22 = Vtiger_Field::getInstance('exlabor_ot_rate_dest', $est);
    if ($estField22) {
        echo "<br> The exlabor_ot_rate_dest field already exists in Estimates <br>";
    } else {
        $estField22 = new Vtiger_Field();
        $estField22->label = 'LBL_QUOTES_EXLABOROTRATEDEST';
        $estField22->name = 'exlabor_ot_rate_dest';
        $estField22->table = 'vtiger_quotes';
        $estField22->column = 'exlabor_ot_rate_dest';
        $estField22->columntype = 'DECIMAL(19,2)';
        $estField22->uitype = 71;
        $estField22->typeofdata = 'N~O';
        $estField22->displaytype = 1;
        $estField22->quickcreate = 0;
        $estField22->presence = 2;
        
        $estBlock3->addField($estField22);
    }
    //34 exlabor_ot_flat_dest
    $estField34 = Vtiger_Field::getInstance('exlabor_ot_flat_dest', $est);
    if ($estField34) {
        echo "<br> The exlabor_ot_flat_dest field already exists in Estimates <br>";
    } else {
        $estField34 = new Vtiger_Field();
        $estField34->label = 'LBL_QUOTES_EXLABOROTFLATDEST';
        $estField34->name = 'exlabor_ot_flat_dest';
        $estField34->table = 'vtiger_quotes';
        $estField34->column = 'exlabor_ot_flat_dest';
        $estField34->columntype = 'DECIMAL(19,2)';
        $estField34->uitype = 71;
        $estField34->typeofdata = 'N~O';
        $estField34->displaytype = 1;
        $estField34->quickcreate = 0;
        $estField34->presence = 2;
        
        $estBlock3->addField($estField34);
    }
    //get the fields and reorder them so our new fields go into logical places
    //New Ordering :
    /*********************************************************************************
     * 21 acc_exlabor_origin_hours 		f23	|  22	acc_exlabor_dest_hours		f24	 *
     * 23 apply_exlabor_rate_origin		f15	|  24	apply_exlabor_rate_dest		f19	 *
     * 25 exlabor_rate_origin			f16	|  26	exlabor_rate_dest		 	f20	 *
     * 27 exlabor_flat_origin			f31	|  28	exlabor_flat_dest		 	f33	 *
     * 28 acc_exlabor_ot_origin_hours 	f25	|  29	acc_exlabor_ot_dest_hours	f26	 *
     * 30 apply_exlabor_ot_rate_origin	f17	|  31	apply_exlabor_ot_rate_dest	f21	 *
     * 32 exlabor_ot_rate_origin		f18	|  33	exlabor_ot_rate_dest		f22	 *
     * 33 exlabor_ot_flat_origin		f32	|  34	exlabor_ot_flat_dest	 	f34	 *
     * 35 acc_wait_origin_hours			f27	|  36	acc_wait_dest_hours			f28	 *
     * 37 acc_wait_ot_origin_hours		f29	|  38	acc_wait_ot_dest_hours		f30	 *
     *********************************************************************************/
    $estField23 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $est);
    $estField24 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $est);
    $estField25 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $est);
    $estField26 = Vtiger_Field::getInstance('acc_exlabor_ot_dest_hours', $est);
    $estField27 = Vtiger_Field::getInstance('acc_wait_origin_hours', $est);
    $estField28 = Vtiger_Field::getInstance('acc_wait_dest_hours', $est);
    $estField29 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $est);
    $estField30 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $est);
     
    //21 field23
    echo '<br>UPDATE `vtiger_field` SET sequence = 21 WHERE fieldid = '.$estField23->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 21 WHERE fieldid = '.$estField23->id);
    //22 field24
    echo '<br>UPDATE `vtiger_field` SET sequence = 22 WHERE fieldid = '.$estField24->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 22 WHERE fieldid = '.$estField24->id);
    //23 field15
    echo '<br>UPDATE `vtiger_field` SET sequence = 23 WHERE fieldid = '.$estField15->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 23 WHERE fieldid = '.$estField15->id);
    //24 field19
    echo '<br>UPDATE `vtiger_field` SET sequence = 24 WHERE fieldid = '.$estField19->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 24 WHERE fieldid = '.$estField19->id);
    //25 field16
    echo '<br>UPDATE `vtiger_field` SET sequence = 25 WHERE fieldid = '.$estField16->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 25 WHERE fieldid = '.$estField16->id);
    //26 field20
    echo '<br>UPDATE `vtiger_field` SET sequence = 26 WHERE fieldid = '.$estField20->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 26 WHERE fieldid = '.$estField20->id);
    //27 field31
    echo '<br>UPDATE `vtiger_field` SET sequence = 27 WHERE fieldid = '.$estField31->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 27 WHERE fieldid = '.$estField31->id);
    //28 field33
    echo '<br>UPDATE `vtiger_field` SET sequence = 28 WHERE fieldid = '.$estField33->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 28 WHERE fieldid = '.$estField33->id);
    //29 field25
    echo '<br>UPDATE `vtiger_field` SET sequence = 29 WHERE fieldid = '.$estField25->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 29 WHERE fieldid = '.$estField25->id);
    //30 field26
    echo '<br>UPDATE `vtiger_field` SET sequence = 30 WHERE fieldid = '.$estField26->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 30 WHERE fieldid = '.$estField26->id);
    //31 field17
    echo '<br>UPDATE `vtiger_field` SET sequence = 31 WHERE fieldid = '.$estField17->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 31 WHERE fieldid = '.$estField17->id);
    //32 field21
    echo '<br>UPDATE `vtiger_field` SET sequence = 32 WHERE fieldid = '.$estField21->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 32 WHERE fieldid = '.$estField21->id);
    //33 field32
    echo '<br>UPDATE `vtiger_field` SET sequence = 33 WHERE fieldid = '.$estField32->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 33 WHERE fieldid = '.$estField32->id);
    //34 field34
    echo '<br>UPDATE `vtiger_field` SET sequence = 34 WHERE fieldid = '.$estField34->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 34 WHERE fieldid = '.$estField34->id);
    //35 field18
    echo '<br>UPDATE `vtiger_field` SET sequence = 35 WHERE fieldid = '.$estField18->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 35 WHERE fieldid = '.$estField18->id);
    //36 field22
    echo '<br>UPDATE `vtiger_field` SET sequence = 36 WHERE fieldid = '.$estField22->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 36 WHERE fieldid = '.$estField22->id);
    //37 field27
    echo '<br>UPDATE `vtiger_field` SET sequence = 37 WHERE fieldid = '.$estField27->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 37 WHERE fieldid = '.$estField27->id);
    //38 field28
    echo '<br>UPDATE `vtiger_field` SET sequence = 38 WHERE fieldid = '.$estField28->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 38 WHERE fieldid = '.$estField28->id);
    //39 field29
    echo '<br>UPDATE `vtiger_field` SET sequence = 39 WHERE fieldid = '.$estField29->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 39 WHERE fieldid = '.$estField29->id);
    //40 field30
    echo '<br>UPDATE `vtiger_field` SET sequence = 40 WHERE fieldid = '.$estField30->id;
    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = 40 WHERE fieldid = '.$estField30->id);
    
    $quoteBlock1 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $quote);
    $quoteField1 = Vtiger_Field::getInstance('apply_full_pack_rate_override', $quote);
    if ($quoteField1) {
        echo "<br> The apply_full_pack_rate_override field already exists in Estimates <br>";
    } else {
        $quoteField1 = new Vtiger_Field();
        $quoteField1->label = 'LBL_QUOTES_APPLYFULLPACKRATEOVERRIDE';
        $quoteField1->name = 'apply_full_pack_rate_override';
        $quoteField1->table = 'vtiger_quotes';
        $quoteField1->column = 'apply_full_pack_rate_override';
        $quoteField1->columntype = 'VARCHAR(3)';
        $quoteField1->uitype = 56;
        $quoteField1->typeofdata = 'V~O';
        $quoteField1->displaytype = 1;
        $quoteField1->quickcreate = 0;
        $quoteField1->presence = 2;
        
        $quoteBlock1->addField($quoteField1);
    }
    $quoteField2 = Vtiger_Field::getInstance('full_pack_rate_override', $quote);
    if ($quoteField2) {
        echo "<br> The full_pack_rate_override field already exists in Estimates <br>";
    } else {
        $quoteField2 = new Vtiger_Field();
        $quoteField2->label = 'LBL_QUOTES_FULLPACKRATEOVERRIDE';
        $quoteField2->name = 'full_pack_rate_override';
        $quoteField2->table = 'vtiger_quotes';
        $quoteField2->column = 'full_pack_rate_override';
        $quoteField2->columntype = 'DECIMAL(19,2)';
        $quoteField2->uitype = 71;
        $quoteField2->typeofdata = 'N~O';
        $quoteField2->displaytype = 1;
        $quoteField2->quickcreate = 0;
        $quoteField2->presence = 2;
        
        $quoteBlock1->addField($quoteField2);
    }
    $quoteBlock2 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $quote);
    //Apply SIT First Day Origin
    $quoteField3 = Vtiger_Field::getInstance('apply_sit_first_day_origin', $quote);
    if ($quoteField3) {
        echo "<br> The apply_sit_first_day_origin field already exists in Estimates <br>";
    } else {
        $quoteField3 = new Vtiger_Field();
        $quoteField3->label = 'LBL_QUOTES_APPLYSITFIRSTDAYORIGIN';
        $quoteField3->name = 'apply_sit_first_day_origin';
        $quoteField3->table = 'vtiger_quotes';
        $quoteField3->column = 'apply_sit_first_day_origin';
        $quoteField3->columntype = 'VARCHAR(3)';
        $quoteField3->uitype = 56;
        $quoteField3->typeofdata = 'V~O';
        $quoteField3->displaytype = 1;
        $quoteField3->quickcreate = 0;
        $quoteField3->presence = 2;
        $quoteBlock2->addField($quoteField3);
    }
    //Apply SIT First Day Dest
    $quoteField4 = Vtiger_Field::getInstance('apply_sit_first_day_dest', $quote);
    if ($quoteField4) {
        echo "<br> The apply_sit_first_day_dest field already exists in Estimates <br>";
    } else {
        $quoteField4 = new Vtiger_Field();
        $quoteField4->label = 'LBL_QUOTES_APPLYSITFIRSTDAYDEST';
        $quoteField4->name = 'apply_sit_first_day_dest';
        $quoteField4->table = 'vtiger_quotes';
        $quoteField4->column = 'apply_sit_first_day_dest';
        $quoteField4->columntype = 'VARCHAR(3)';
        $quoteField4->uitype = 56;
        $quoteField4->typeofdata = 'V~O';
        $quoteField4->displaytype = 1;
        $quoteField4->quickcreate = 0;
        $quoteField4->presence = 2;
        
        $quoteBlock2->addField($quoteField4);
    }
    //SIT First Day Origin
    $quoteField5 = Vtiger_Field::getInstance('sit_first_day_origin_override', $quote);
    if ($quoteField5) {
        echo "<br> The sit_first_day_origin_override Field already exists in Estimates <br>";
    } else {
        $quoteField5 = new Vtiger_Field();
        $quoteField5->label = 'LBL_QUOTES_SITFIRSTDAYORIGINOVERRIDE';
        $quoteField5->name = 'sit_first_day_origin_override';
        $quoteField5->table = 'vtiger_quotes';
        $quoteField5->column = 'sit_first_day_origin_override';
        $quoteField5->columntype = 'DECIMAL(19,2)';
        $quoteField5->uitype = 71;
        $quoteField5->typeofdata = 'N~O';
        $quoteField5->displaytype = 1;
        $quoteField5->quickcreate = 0;
        $quoteField5->presence = 2;
        $quoteBlock2->addField($quoteField5);
    }
    //SIT First Day Dest
    $quoteField6 = Vtiger_Field::getInstance('sit_first_day_dest_override', $quote);
    if ($quoteField6) {
        echo "<br> The sit_first_day_dest_override Field already exists in Estimates <br>";
    } else {
        $quoteField6 = new Vtiger_Field();
        $quoteField6->label = 'LBL_QUOTES_SITFIRSTDAYDESTOVERRIDE';
        $quoteField6->name = 'sit_first_day_dest_override';
        $quoteField6->table = 'vtiger_quotes';
        $quoteField6->column = 'sit_first_day_dest_override';
        $quoteField6->columntype = 'DECIMAL(19,2)';
        $quoteField6->uitype = 71;
        $quoteField6->typeofdata = 'N~O';
        $quoteField6->displaytype = 1;
        $quoteField6->quickcreate = 0;
        $quoteField6->presence = 2;
        
        $quoteBlock2->addField($quoteField6);
    }
    //Apply SIT Add'l Day Origin
    $quoteField7 = Vtiger_Field::getInstance('apply_sit_addl_day_origin', $quote);
    if ($quoteField7) {
        echo "<br> The apply_sit_addl_day_origin Field already exists in Estimates <br>";
    } else {
        $quoteField7 = new Vtiger_Field();
        $quoteField7->label = 'LBL_QUOTES_APPLYSITADDLDAYORIGIN';
        $quoteField7->name = 'apply_sit_addl_day_origin';
        $quoteField7->table = 'vtiger_quotes';
        $quoteField7->column = 'apply_sit_addl_day_origin';
        $quoteField7->columntype = 'VARCHAR(3)';
        $quoteField7->uitype = 56;
        $quoteField7->typeofdata = 'V~O';
        $quoteField7->displaytype = 1;
        $quoteField7->quickcreate = 0;
        $quoteField7->presence = 2;
        
        $quoteBlock2->addField($quoteField7);
    }
    //Apply SIT Add'l Day Dest
    $quoteField8 = Vtiger_Field::getInstance('apply_sit_addl_day_dest', $quote);
    if ($quoteField8) {
        echo "<br> The apply_sit_addl_day_dest Field already exists in Estimates <br>";
    } else {
        $quoteField8 = new Vtiger_Field();
        $quoteField8->label = 'LBL_QUOTES_APPLYSITADDLDAYDEST';
        $quoteField8->name = 'apply_sit_addl_day_dest';
        $quoteField8->table = 'vtiger_quotes';
        $quoteField8->column = 'apply_sit_addl_day_dest';
        $quoteField8->columntype = 'VARCHAR(3)';
        $quoteField8->uitype = 56;
        $quoteField8->typeofdata = 'V~O';
        $quoteField8->displaytype = 1;
        $quoteField8->quickcreate = 0;
        $quoteField8->presence = 2;
        
        $quoteBlock2->addField($quoteField8);
    }
    //SIT Add'l Day Origin
    $quoteField9 = Vtiger_Field::getInstance('sit_addl_day_origin_override', $quote);
    if ($quoteField9) {
        echo "<br> The sit_addl_day_origin_override Field already exists in Estimates <br>";
    } else {
        $quoteField9 = new Vtiger_Field();
        $quoteField9->label = 'LBL_QUOTES_SITADDLDAYORIGINOVERRIDE';
        $quoteField9->name = 'sit_addl_day_origin_override';
        $quoteField9->table = 'vtiger_quotes';
        $quoteField9->column = 'sit_addl_day_origin_override';
        $quoteField9->columntype = 'DECIMAL(19,2)';
        $quoteField9->uitype = 71;
        $quoteField9->typeofdata = 'N~O';
        $quoteField9->displaytype = 1;
        $quoteField9->quickcreate = 0;
        $quoteField9->presence = 2;
        
        $quoteBlock2->addField($quoteField9);
    }
    //SIT Add'l Day Dest
    $quoteField10 = Vtiger_Field::getInstance('sit_addl_day_dest_override', $quote);
    if ($quoteField10) {
        echo "<br> The sit_addl_day_dest_override Field already exists in Estimates <br>";
    } else {
        $quoteField10 = new Vtiger_Field();
        $quoteField10->label = 'LBL_QUOTES_SITADDLDAYDESTOVERRIDE';
        $quoteField10->name = 'sit_addl_day_dest_override';
        $quoteField10->table = 'vtiger_quotes';
        $quoteField10->column = 'sit_addl_day_dest_override';
        $quoteField10->columntype = 'DECIMAL(19,2)';
        $quoteField10->uitype = 71;
        $quoteField10->typeofdata = 'N~O';
        $quoteField10->displaytype = 1;
        $quoteField10->quickcreate = 0;
        $quoteField10->presence = 2;
        
        $quoteBlock2->addField($quoteField10);
    }
    //Apply SIT Cartage Origin
    $quoteField11 = Vtiger_Field::getInstance('apply_sit_cartage_origin', $quote);
    if ($quoteField11) {
        echo "<br> The apply_sit_cartage_origin field already exists in Estimates <br>";
    } else {
        $quoteField11 = new Vtiger_Field();
        $quoteField11->label = 'LBL_QUOTES_APPLYSITCARTAGEORIGIN';
        $quoteField11->name = 'apply_sit_cartage_origin';
        $quoteField11->table = 'vtiger_quotes';
        $quoteField11->column = 'apply_sit_cartage_origin';
        $quoteField11->columntype = 'VARCHAR(3)';
        $quoteField11->uitype = 56;
        $quoteField11->typeofdata = 'V~O';
        $quoteField11->displaytype = 1;
        $quoteField11->quickcreate = 0;
        $quoteField11->presence = 2;
        
        $quoteBlock2->addField($quoteField11);
    }
    //Apply SIT Cartage Dest
    $quoteField12 = Vtiger_Field::getInstance('apply_sit_cartage_dest', $quote);
    if ($quoteField12) {
        echo "<br> The apply_sit_cartage_dest field already exists in Estimates <br>";
    } else {
        $quoteField12 = new Vtiger_Field();
        $quoteField12->label = 'LBL_QUOTES_APPLYSITCARTAGEDEST';
        $quoteField12->name = 'apply_sit_cartage_dest';
        $quoteField12->table = 'vtiger_quotes';
        $quoteField12->column = 'apply_sit_cartage_dest';
        $quoteField12->columntype = 'VARCHAR(3)';
        $quoteField12->uitype = 56;
        $quoteField12->typeofdata = 'V~O';
        $quoteField12->displaytype = 1;
        $quoteField12->quickcreate = 0;
        $quoteField12->presence = 2;
        
        $quoteBlock2->addField($quoteField12);
    }
    //SIT Cartage Origin
    $quoteField13 = Vtiger_Field::getInstance('sit_cartage_origin_override', $quote);
    if ($quoteField13) {
        echo "<br> The sit_cartage_origin_override field already exists in Estimates <br>";
    } else {
        $quoteField13 = new Vtiger_Field();
        $quoteField13->label = 'LBL_QUOTES_SITCARTAGEORIGINOVERRIDE';
        $quoteField13->name = 'sit_cartage_origin_override';
        $quoteField13->table = 'vtiger_quotes';
        $quoteField13->column = 'sit_cartage_origin_override';
        $quoteField13->columntype = 'DECIMAL(19,2)';
        $quoteField13->uitype = 71;
        $quoteField13->typeofdata = 'N~O';
        $quoteField13->displaytype = 1;
        $quoteField13->quickcreate = 0;
        $quoteField13->presence = 2;
        
        $quoteBlock2->addField($quoteField13);
    }
    //SIT Cartage Dest
    $quoteField14 = Vtiger_Field::getInstance('sit_cartage_dest_override', $quote);
    if ($quoteField14) {
        echo "<br> The sit_cartage_dest_override field already exists in Estimates <br>";
    } else {
        $quoteField14 = new Vtiger_Field();
        $quoteField14->label = 'LBL_QUOTES_SITCARTAGEDESTOVERRIDE';
        $quoteField14->name = 'sit_cartage_dest_override';
        $quoteField14->table = 'vtiger_quotes';
        $quoteField14->column = 'sit_cartage_dest_override';
        $quoteField14->columntype = 'DECIMAL(19,2)';
        $quoteField14->uitype = 71;
        $quoteField14->typeofdata = 'N~O';
        $quoteField14->displaytype = 1;
        $quoteField14->quickcreate = 0;
        $quoteField14->presence = 2;
        
        $quoteBlock2->addField($quoteField14);
    }
    
    $quoteBlock3 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $quote);
    //15 apply_exlabor_rate_origin
    $quoteField15 = Vtiger_Field::getInstance('apply_exlabor_rate_origin', $quote);
    if ($quoteField15) {
        echo "<br> The apply_exlabor_rate_origin field already exists in Estimates <br>";
    } else {
        $quoteField15 = new Vtiger_Field();
        $quoteField15->label = 'LBL_QUOTES_APPLYEXLABORRATEORIGIN';
        $quoteField15->name = 'apply_exlabor_rate_origin';
        $quoteField15->table = 'vtiger_quotes';
        $quoteField15->column = 'apply_exlabor_rate_origin';
        $quoteField15->columntype = 'VARCHAR(3)';
        $quoteField15->uitype = 56;
        $quoteField15->typeofdata = 'V~O';
        $quoteField15->displaytype = 1;
        $quoteField15->quickcreate = 0;
        $quoteField15->presence = 2;
        
        $quoteBlock3->addField($quoteField15);
    }
    //16 exlabor_rate_origin
    $quoteField16 = Vtiger_Field::getInstance('exlabor_rate_origin', $quote);
    if ($quoteField16) {
        echo "<br> The exlabor_rate_origin field already exists in Estimates <br>";
    } else {
        $quoteField16 = new Vtiger_Field();
        $quoteField16->label = 'LBL_QUOTES_EXLABORRATEORIGIN';
        $quoteField16->name = 'exlabor_rate_origin';
        $quoteField16->table = 'vtiger_quotes';
        $quoteField16->column = 'exlabor_rate_origin';
        $quoteField16->columntype = 'DECIMAL(19,2)';
        $quoteField16->uitype = 71;
        $quoteField16->typeofdata = 'N~O';
        $quoteField16->displaytype = 1;
        $quoteField16->quickcreate = 0;
        $quoteField16->presence = 2;
        
        $quoteBlock3->addField($quoteField16);
    }
    //31 exlabor_flat_origin
    $quoteField31 = Vtiger_Field::getInstance('exlabor_flat_origin', $quote);
    if ($quoteField31) {
        echo "<br> The exlabor_flat_origin field already exists in Estimates <br>";
    } else {
        $quoteField31 = new Vtiger_Field();
        $quoteField31->label = 'LBL_QUOTES_EXLABORFLATORIGIN';
        $quoteField31->name = 'exlabor_flat_origin';
        $quoteField31->table = 'vtiger_quotes';
        $quoteField31->column = 'exlabor_flat_origin';
        $quoteField31->columntype = 'DECIMAL(19,2)';
        $quoteField31->uitype = 71;
        $quoteField31->typeofdata = 'N~O';
        $quoteField31->displaytype = 1;
        $quoteField31->quickcreate = 0;
        $quoteField31->presence = 2;
        
        $quoteBlock3->addField($quoteField31);
    }
    //17 apply_exlabor_ot_rate_origin
    $quoteField17 = Vtiger_Field::getInstance('apply_exlabor_ot_rate_origin', $quote);
    if ($quoteField17) {
        echo "<br> The apply_exlabor_ot_rate_origin field already exists in Estimates <br>";
    } else {
        $quoteField17 = new Vtiger_Field();
        $quoteField17->label = 'LBL_QUOTES_APPLYEXLABOROTRATEORIGIN';
        $quoteField17->name = 'apply_exlabor_ot_rate_origin';
        $quoteField17->table = 'vtiger_quotes';
        $quoteField17->column = 'apply_exlabor_ot_rate_origin';
        $quoteField17->columntype = 'VARCHAR(3)';
        $quoteField17->uitype = 56;
        $quoteField17->typeofdata = 'V~O';
        $quoteField17->displaytype = 1;
        $quoteField17->quickcreate = 0;
        $quoteField17->presence = 2;
        
        $quoteBlock3->addField($quoteField17);
    }
    //18 exlabor_ot_rate_origin
    $quoteField18 = Vtiger_Field::getInstance('exlabor_ot_rate_origin', $quote);
    if ($quoteField18) {
        echo "<br> The exlabor_ot_rate_origin field already exists in Estimates <br>";
    } else {
        $quoteField18 = new Vtiger_Field();
        $quoteField18->label = 'LBL_QUOTES_EXLABOROTRATEORIGIN';
        $quoteField18->name = 'exlabor_ot_rate_origin';
        $quoteField18->table = 'vtiger_quotes';
        $quoteField18->column = 'exlabor_ot_rate_origin';
        $quoteField18->columntype = 'DECIMAL(19,2)';
        $quoteField18->uitype = 71;
        $quoteField18->typeofdata = 'N~O';
        $quoteField18->displaytype = 1;
        $quoteField18->quickcreate = 0;
        $quoteField18->presence = 2;
        
        $quoteBlock3->addField($quoteField18);
    }
    //32 exlabor_ot_flat_origin
    $quoteField32 = Vtiger_Field::getInstance('exlabor_ot_flat_origin', $quote);
    if ($quoteField32) {
        echo "<br> The exlabor_ot_flat_origin field already exists in Estimates <br>";
    } else {
        $quoteField32 = new Vtiger_Field();
        $quoteField32->label = 'LBL_QUOTES_EXLABOROTFLATORIGIN';
        $quoteField32->name = 'exlabor_ot_flat_origin';
        $quoteField32->table = 'vtiger_quotes';
        $quoteField32->column = 'exlabor_ot_flat_origin';
        $quoteField32->columntype = 'DECIMAL(19,2)';
        $quoteField32->uitype = 71;
        $quoteField32->typeofdata = 'N~O';
        $quoteField32->displaytype = 1;
        $quoteField32->quickcreate = 0;
        $quoteField32->presence = 2;
        
        $quoteBlock3->addField($quoteField32);
    }
    //19 apply_exlabor_rate_dest
    $quoteField19 = Vtiger_Field::getInstance('apply_exlabor_rate_dest', $quote);
    if ($quoteField19) {
        echo "<br> The apply_exlabor_rate_dest field already exists in Estimates <br>";
    } else {
        $quoteField19 = new Vtiger_Field();
        $quoteField19->label = 'LBL_QUOTES_APPLYEXLABORRATEDEST';
        $quoteField19->name = 'apply_exlabor_rate_dest';
        $quoteField19->table = 'vtiger_quotes';
        $quoteField19->column = 'apply_exlabor_rate_dest';
        $quoteField19->columntype = 'VARCHAR(3)';
        $quoteField19->uitype = 56;
        $quoteField19->typeofdata = 'V~O';
        $quoteField19->displaytype = 1;
        $quoteField19->quickcreate = 0;
        $quoteField19->presence = 2;
        
        $quoteBlock3->addField($quoteField19);
    }
    //20 exlabor_rate_dest
    $quoteField20 = Vtiger_Field::getInstance('exlabor_rate_dest', $quote);
    if ($quoteField20) {
        echo "<br> The exlabor_rate_dest field already exists in Estimates <br>";
    } else {
        $quoteField20 = new Vtiger_Field();
        $quoteField20->label = 'LBL_QUOTES_EXLABORRATEDEST';
        $quoteField20->name = 'exlabor_rate_dest';
        $quoteField20->table = 'vtiger_quotes';
        $quoteField20->column = 'exlabor_rate_dest';
        $quoteField20->columntype = 'DECIMAL(19,2)';
        $quoteField20->uitype = 71;
        $quoteField20->typeofdata = 'N~O';
        $quoteField20->displaytype = 1;
        $quoteField20->quickcreate = 0;
        $quoteField20->presence = 2;
        
        $quoteBlock3->addField($quoteField20);
    }
    //33 exlabor_flat_dest
    $quoteField33 = Vtiger_Field::getInstance('exlabor_flat_dest', $quote);
    if ($quoteField33) {
        echo "<br> The exlabor_flat_dest field already exists in Estimates <br>";
    } else {
        $quoteField33 = new Vtiger_Field();
        $quoteField33->label = 'LBL_QUOTES_EXLABORFLATDEST';
        $quoteField33->name = 'exlabor_flat_dest';
        $quoteField33->table = 'vtiger_quotes';
        $quoteField33->column = 'exlabor_flat_dest';
        $quoteField33->columntype = 'DECIMAL(19,2)';
        $quoteField33->uitype = 71;
        $quoteField33->typeofdata = 'N~O';
        $quoteField33->displaytype = 1;
        $quoteField33->quickcreate = 0;
        $quoteField33->presence = 2;
        
        $quoteBlock3->addField($quoteField33);
    }
    //21 apply_exlabor_ot_rate_dest
    $quoteField21 = Vtiger_Field::getInstance('apply_exlabor_ot_rate_dest', $quote);
    if ($quoteField21) {
        echo "<br> The apply_exlabor_ot_rate_dest field already exists in Estimates <br>";
    } else {
        $quoteField21 = new Vtiger_Field();
        $quoteField21->label = 'LBL_QUOTES_APPLY_EXLABOROTRATEDEST';
        $quoteField21->name = 'apply_exlabor_ot_rate_dest';
        $quoteField21->table = 'vtiger_quotes';
        $quoteField21->column = 'apply_exlabor_ot_rate_dest';
        $quoteField21->columntype = 'VARCHAR(3)';
        $quoteField21->uitype = 56;
        $quoteField21->typeofdata = 'V~O';
        $quoteField21->displaytype = 1;
        $quoteField21->quickcreate = 0;
        $quoteField21->presence = 2;
        
        $quoteBlock3->addField($quoteField21);
    }
    //22 exlabor_ot_rate_dest
    $quoteField22 = Vtiger_Field::getInstance('exlabor_ot_rate_dest', $quote);
    if ($quoteField22) {
        echo "<br> The exlabor_ot_rate_dest field already exists in Estimates <br>";
    } else {
        $quoteField22 = new Vtiger_Field();
        $quoteField22->label = 'LBL_QUOTES_EXLABOROTRATEDEST';
        $quoteField22->name = 'exlabor_ot_rate_dest';
        $quoteField22->table = 'vtiger_quotes';
        $quoteField22->column = 'exlabor_ot_rate_dest';
        $quoteField22->columntype = 'DECIMAL(19,2)';
        $quoteField22->uitype = 71;
        $quoteField22->typeofdata = 'N~O';
        $quoteField22->displaytype = 1;
        $quoteField22->quickcreate = 0;
        $quoteField22->presence = 2;
        
        $quoteBlock3->addField($quoteField22);
    }
    //34 exlabor_ot_flat_dest
    $quoteField34 = Vtiger_Field::getInstance('exlabor_ot_flat_dest', $quote);
    if ($quoteField34) {
        echo "<br> The exlabor_ot_flat_dest field already exists in Estimates <br>";
    } else {
        $quoteField34 = new Vtiger_Field();
        $quoteField34->label = 'LBL_QUOTES_EXLABOROTFLATDEST';
        $quoteField34->name = 'exlabor_ot_flat_dest';
        $quoteField34->table = 'vtiger_quotes';
        $quoteField34->column = 'exlabor_ot_flat_dest';
        $quoteField34->columntype = 'DECIMAL(19,2)';
        $quoteField34->uitype = 71;
        $quoteField34->typeofdata = 'N~O';
        $quoteField34->displaytype = 1;
        $quoteField34->quickcreate = 0;
        $quoteField34->presence = 2;
        
        $quoteBlock3->addField($quoteField34);
    }
} else {
    echo "<br> Either the Estimates or Quotes Module does not exist.";
}

Vtiger_Utils::AddColumn('vtiger_quotes', 'tpg_transfactor', 'DECIMAL(12,2)');
echo "<br><h3> Finished TPG/Pricelock Hotfix </h3><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";