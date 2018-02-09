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



$contracts = Vtiger_Module::getInstance('Contracts');
if ($contracts) {
    echo "Module exists";
} else {
    $contracts = new Vtiger_Module();
    $contracts->name = 'Contracts';
    $contracts->save();
    
    $contracts->initTables();
}

echo "<h1>Creating Table for contract2Agent</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_contract2agent')) {
    echo "<li>creating vtiger_contract2agent </li><br>";
    Vtiger_Utils::CreateTable('vtiger_contract2agent',
                              '(
							    agentid INT(19),
							    contractid INT(19)
								)', true);
}
echo "</ol>";

echo "<h1>Creating Table for contract2Vanline</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_contract2vanline')) {
    echo "<li>creating vtiger_contract2vanline </li><br>";
    Vtiger_Utils::CreateTable('vtiger_contract2vanline',
                              '(
							    vanlineid INT(19),
							    contractid INT(19),
								apply_to_all_agents TINYINT(1)
								)', true);
}
echo "</ol>";

echo "<h1>Creating Table for contracts_misc_items</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_contracts_misc_items')) {
    echo "<li>creating vtiger_contracts_misc_items </li><br>";
    Vtiger_Utils::CreateTable('vtiger_contracts_misc_items',
                              '(
								contracts_misc_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							    contractsid INT(11),
							    is_quantity_rate VARCHAR(3),
								description VARCHAR(255),
								rate DECIMAL(25,2),
								quantity INT(11),
								discounted VARCHAR(3),
								discount DECIMAL(5,1)
								)', true);
}
echo "</ol>";

$admin_block = Vtiger_Block::getInstance('LBL_CONTRACTS_ADMINISTRATIVE', $contracts);
if ($admin_block) {
    echo "<li>The LBL_CONTRACTS_ADMINISTRATIVE block already exists</li><br>";
} else {
    $admin_block = new Vtiger_Block();
    $admin_block->label = 'LBL_CONTRACTS_ADMINISTRATIVE';
    $admin_block->sequence = 2;
    $contracts->addBlock($admin_block);
}

$block = Vtiger_Block::getInstance('LBL_CONTRACTS_INFORMATION', $contracts);
if ($block) {
    echo "<li>The LBL_CONTRACTS_INFORMATION block already exists</li><br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_CONTRACTS_INFORMATION';
    $contracts->addBlock($block);
}

$custom_block = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $contracts);
if ($custom_block) {
    echo "<li>The LBL_CUSTOM_INFORMATION block already exists</li><br>";
} else {
    $custom_block = new Vtiger_Block();
    $custom_block->label = 'LBL_CUSTOM_INFORMATION';
    $contracts->addBlock($custom_block);
}

$billing_block = Vtiger_Block::getInstance('LBL_CONTRACTS_BILLING', $contracts);
if ($billing_block) {
    echo "<li>The LBL_CONTRACTS_BILLING block already exists</li><br>";
} else {
    $billing_block = new Vtiger_Block();
    $billing_block->label = 'LBL_CONTRACTS_BILLING';
    $contracts->addBlock($billing_block);
}

$tariff_block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $contracts);
if ($tariff_block) {
    echo "<li>The LBL_CONTRACTS_TARIFF block already exists</li><br>";
} else {
    $tariff_block = new Vtiger_Block();
    $tariff_block->label = 'LBL_CONTRACTS_TARIFF';
    $contracts->addBlock($tariff_block);
}

$valuation_block = Vtiger_Block::getInstance('LBL_CONTRACTS_VALUATION', $contracts);
if ($valuation_block) {
    echo "<li>The LBL_CONTRACTS_VALUATION block already exists</li><br>";
} else {
    $valuation_block = new Vtiger_Block();
    $valuation_block->label = 'LBL_CONTRACTS_VALUATION';
    $contracts->addBlock($valuation_block);
}

$admin_block = Vtiger_Block::getInstance('LBL_CONTRACTS_ADMIN', $contracts);
if ($admin_block) {
    echo "<li>The LBL_CONTRACTS_ADMIN block already exists</li><br>";
} else {
    $admin_block = new Vtiger_Block();
    $admin_block->label = 'LBL_CONTRACTS_ADMIN';
    $contracts->addBlock($admin_block);
}

$field0 = Vtiger_Field::getInstance('contract_no', $contracts);
if ($field0) {
    echo "<li>The contract_no field already exists</li><br>";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_CONTRACTS_CONTRACTNUM';
    $field0->name = 'contract_no';
    $field0->table = 'vtiger_contracts';
    $field0->column = 'contract_no';
    $field0->columntype = 'VARCHAR(50)';
    $field0->uitype = 1;
    $field0->typeofdata = 'V~M';
    $field0->summaryfield = 1;
    
    $block->addField($field0);
    
    $contracts->setEntityIdentifier($field0);
}

$field1 = Vtiger_Field::getInstance('nat_account_no', $contracts);
if ($field1) {
    echo "<li>The nat_account_no field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CONTRACTS_NATACCOUNT';
    $field1->name = 'nat_account_no';
    $field1->table = 'vtiger_contracts';
    $field1->column = 'nat_account_no';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 1;
    
    $block->addField($field1);
}

$field2 = Vtiger_Field::getInstance('account_id', $contracts);
if ($field2) {
    echo "<li>The account_id field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_CONTRACTS_ACCOUNT';
    $field2->name = 'account_id';
    $field2->table = 'vtiger_contracts';
    $field2->column = 'account_id';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;
    
    $block->addField($field2);
    
    $field2->setRelatedModules(array('Accounts'));
}

$field3 = Vtiger_Field::getInstance('contact_id', $contracts);
if ($field3) {
    echo "<li>The contact_id field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CONTRACTS_CONTACT';
    $field3->name = 'contact_id';
    $field3->table = 'vtiger_contracts';
    $field3->column = 'contact_id';
    $field3->columntype = 'INT(19)';
    $field3->uitype = 10;
    $field3->typeofdata = 'V~O';
    $field3->summaryfield = 1;
    
    $block->addField($field3);
    
    $field3->setRelatedModules(array('Contacts'));
}

$field4 = Vtiger_Field::getInstance('phone', $contracts);
if ($field4) {
    echo "<li>The phone field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_CONTRACTS_PHONE';
    $field4->name = 'phone';
    $field4->table = 'vtiger_contracts';
    $field4->column = 'phone';
    $field4->columntype = 'VARCHAR(20)';
    $field4->uitype = 11;
    $field4->typeofdata = 'V~O';
    $field4->summaryfield = 1;
    
    $block->addField($field4);
}

$field5 = Vtiger_Field::getInstance('begin_date', $contracts);
if ($field5) {
    echo "<li>The begin_date field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CONTRACTS_BEGINDATE';
    $field5->name = 'begin_date';
    $field5->table = 'vtiger_contracts';
    $field5->column = 'begin_date';
    $field5->columntype = 'DATE';
    $field5->uitype = 5;
    $field5->typeofdata = 'D~M';
    $field5->summaryfield = 1;
    
    $block->addField($field5);
}

$field6 = Vtiger_Field::getInstance('parent_contract', $contracts);
if ($field6) {
    echo "<li>The parent_contract field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CONTRACTS_PARENT';
    $field6->name = 'parent_contract';
    $field6->table = 'vtiger_contracts';
    $field6->column = 'parent_contract';
    $field6->columntype = 'INT(19)';
    $field6->uitype = 10;
    $field6->typeofdata = 'V~O';
    
    $block->addField($field6);
    
    $field6->setRelatedModules(array('Contracts'));
}

$field7 = Vtiger_Field::getInstance('end_date', $contracts);
if ($field7) {
    echo "<li>The end_date field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CONTRACTS_ENDDATE';
    $field7->name = 'end_date';
    $field7->table = 'vtiger_contracts';
    $field7->column = 'end_date';
    $field7->columntype = 'DATE';
    $field7->uitype = 5;
    $field7->typeofdata = 'D~M';
    $field7->summaryfield = 1;
    
    $block->addField($field7);
}

$field8 = Vtiger_Field::getInstance('related_tariff', $contracts);
if ($field8) {
    echo "<li>The related_tariff field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CONTRACTS_BASETARIFF';
    $field8->name = 'related_tariff';
    $field8->table = 'vtiger_contracts';
    $field8->column = 'related_tariff';
    $field8->columntype = 'INT(19)';
    $field8->uitype = 10;
    $field8->typeofdata = 'V~O';
    
    $block->addField($field8);
    
    $field8->setRelatedModules(array('TariffManager'));
}

$field9 = Vtiger_Field::getInstance('billing_address1', $contracts);
if ($field9) {
    echo "<li>The billing_address1 field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_CONTRACTS_BILLING_ADDRESS1';
    $field9->name = 'billing_address1';
    $field9->table = 'vtiger_contracts';
    $field9->column = 'billing_address1';
    $field9->columntype = 'VARCHAR(100)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';
    
    $billing_block->addField($field9);
}

$field10 = Vtiger_Field::getInstance('billing_address2', $contracts);
if ($field10) {
    echo "<li>The billing_address2 field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_CONTRACTS_BILLING_ADDRESS2';
    $field10->name = 'billing_address2';
    $field10->table = 'vtiger_contracts';
    $field10->column = 'billing_address2';
    $field10->columntype = 'VARCHAR(100)';
    $field10->uitype = 1;
    $field10->typeofdata = 'V~O';
    
    $billing_block->addField($field10);
}

$field11 = Vtiger_Field::getInstance('billing_city', $contracts);
if ($field11) {
    echo "<li>The billing_city field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_CONTRACTS_BILLING_CITY';
    $field11->name = 'billing_city';
    $field11->table = 'vtiger_contracts';
    $field11->column = 'billing_city';
    $field11->columntype = 'VARCHAR(100)';
    $field11->uitype = 1;
    $field11->typeofdata = 'V~O';
    
    $billing_block->addField($field11);
}

$field12 = Vtiger_Field::getInstance('billing_state', $contracts);
if ($field12) {
    echo "<li>The billing_state field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_CONTRACTS_BILLING_STATE';
    $field12->name = 'billing_state';
    $field12->table = 'vtiger_contracts';
    $field12->column = 'billing_state';
    $field12->columntype = 'VARCHAR(100)';
    $field12->uitype = 1;
    $field12->typeofdata = 'V~O';
    
    $billing_block->addField($field12);
}

$field13 = Vtiger_Field::getInstance('billing_zip', $contracts);
if ($field13) {
    echo "<li>The billing_zip field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_CONTRACTS_BILLING_ZIP';
    $field13->name = 'billing_zip';
    $field13->table = 'vtiger_contracts';
    $field13->column = 'billing_zip';
    $field13->columntype = 'VARCHAR(10)';
    $field13->uitype = 1;
    $field13->typeofdata = 'V~O';
    
    $billing_block->addField($field13);
}

$field14 = Vtiger_Field::getInstance('billing_pobox', $contracts);
if ($field14) {
    echo "<li>The billing_pobox field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_CONTRACTS_BILLING_POBOX';
    $field14->name = 'billing_pobox';
    $field14->table = 'vtiger_contracts';
    $field14->column = 'billing_pobox';
    $field14->columntype = 'VARCHAR(100)';
    $field14->uitype = 1;
    $field14->typeofdata = 'V~O';
    
    $billing_block->addField($field14);
}

$field15 = Vtiger_Field::getInstance('billing_country', $contracts);
if ($field15) {
    echo "<li>The billing_country field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_CONTRACTS_BILLING_COUNTRY';
    $field15->name = 'billing_country';
    $field15->table = 'vtiger_contracts';
    $field15->column = 'billing_country';
    $field15->columntype = 'VARCHAR(100)';
    $field15->uitype = 1;
    $field15->typeofdata = 'V~O';
    
    $billing_block->addField($field15);
}

$field16 = Vtiger_Field::getInstance('fixed_eff_date', $contracts);
if ($field16) {
    echo "<li>The fixed_eff_date field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_CONTRACTS_FIXED_DATE';
    $field16->name = 'fixed_eff_date';
    $field16->table = 'vtiger_contracts';
    $field16->column = 'fixed_eff_date';
    $field16->columntype = 'VARCHAR(3)';
    $field16->uitype = 56;
    $field16->typeofdata = 'C~O';
    
    $tariff_block->addField($field16);
}

$field17 = Vtiger_Field::getInstance('effective_date', $contracts);
if ($field17) {
    echo "<li>The effective_date field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_CONTRACTS_EFFECTIVE_DATE';
    $field17->name = 'effective_date';
    $field17->table = 'vtiger_contracts';
    $field17->column = 'effective_date';
    $field17->columntype = 'DATE';
    $field17->uitype = 5;
    $field17->typeofdata = 'D~O';

    $tariff_block->addField($field17);
}

$field18 = Vtiger_Field::getInstance('fixed_fuel', $contracts);
if ($field18) {
    echo "<li>The fixed_fuel field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_CONTRACTS_FIXED_FUEL';
    $field18->name = 'fixed_fuel';
    $field18->table = 'vtiger_contracts';
    $field18->column = 'fixed_fuel';
    $field18->columntype = 'VARCHAR(3)';
    $field18->uitype = 56;
    $field18->typeofdata = 'C~O';
    
    $tariff_block->addField($field18);
}

$field19 = Vtiger_Field::getInstance('fuel_charge', $contracts);
if ($field19) {
    echo "<li>The fuel_charge field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_CONTRACTS_FUEL';
    $field19->name = 'fuel_charge';
    $field19->table = 'vtiger_contracts';
    $field19->column = 'fuel_charge';
    $field19->columntype = 'DECIMAL(7,2)';
    $field19->uitype = 9;
    $field19->typeofdata = 'N~O';
    
    $tariff_block->addField($field19);
}

$field20 = Vtiger_Field::getInstance('fixed_irr', $contracts);
if ($field20) {
    echo "<li>The fixed_irr field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_CONTRACTS_FIXED_IRR';
    $field20->name = 'fixed_irr';
    $field20->table = 'vtiger_contracts';
    $field20->column = 'fixed_irr';
    $field20->columntype = 'VARCHAR(3)';
    $field20->uitype = 56;
    $field20->typeofdata = 'C~O';
    
    $tariff_block->addField($field20);
}

$field21 = Vtiger_Field::getInstance('irr_charge', $contracts);
if ($field21) {
    echo "<li>The irr_charge field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_CONTRACTS_IRR';
    $field21->name = 'irr_charge';
    $field21->table = 'vtiger_contracts';
    $field21->column = 'irr_charge';
    $field21->columntype = 'DECIMAL(7,2)';
    $field21->uitype = 9;
    $field21->typeofdata = 'N~O';
    
    $tariff_block->addField($field21);
}

$field22 = Vtiger_Field::getInstance('linehaul_disc', $contracts);
if ($field22) {
    echo "<li>The linehaul_disc field already exists</li><br>";
} else {
    $field22 = new Vtiger_Field();
    $field22->label = 'LBL_CONTRACTS_LINEHAUL';
    $field22->name = 'linehaul_disc';
    $field22->table = 'vtiger_contracts';
    $field22->column = 'linehaul_disc';
    $field22->columntype = 'DECIMAL(7,2)';
    $field22->uitype = 9;
    $field22->typeofdata = 'N~O';
    
    $tariff_block->addField($field22);
}

$field23 = Vtiger_Field::getInstance('accessorial_disc', $contracts);
if ($field23) {
    echo "<li>The accessorial_disc field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_CONTRACTS_ACCESSORIAL';
    $field23->name = 'accessorial_disc';
    $field23->table = 'vtiger_contracts';
    $field23->column = 'accessorial_disc';
    $field23->columntype = 'DECIMAL(7,2)';
    $field23->uitype = 9;
    $field23->typeofdata = 'N~O';
    
    $tariff_block->addField($field23);
}

$field24 = Vtiger_Field::getInstance('packing_disc', $contracts);
if ($field24) {
    echo "<li>The packing_disc field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_CONTRACTS_PACKING';
    $field24->name = 'packing_disc';
    $field24->table = 'vtiger_contracts';
    $field24->column = 'packing_disc';
    $field24->columntype = 'DECIMAL(7,2)';
    $field24->uitype = 9;
    $field24->typeofdata = 'N~O';
    
    $tariff_block->addField($field24);
}

$field25 = Vtiger_Field::getInstance('sit_disc', $contracts);
if ($field25) {
    echo "<li>The sit_disc field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_CONTRACTS_SIT';
    $field25->name = 'sit_disc';
    $field25->table = 'vtiger_contracts';
    $field25->column = 'sit_disc';
    $field25->columntype = 'DECIMAL(7,2)';
    $field25->uitype = 9;
    $field25->typeofdata = 'N~O';
    
    $tariff_block->addField($field25);
}

$field26 = Vtiger_Field::getInstance('bottom_line_disc', $contracts);
if ($field26) {
    echo "<li>The bottom_line_disc field already exists</li><br>";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_CONTRACTS_BOTTOMLINE';
    $field26->name = 'bottom_line_disc';
    $field26->table = 'vtiger_contracts';
    $field26->column = 'bottom_line_disc';
    $field26->columntype = 'DECIMAL(7,2)';
    $field26->uitype = 9;
    $field26->typeofdata = 'N~O';
    
    $tariff_block->addField($field26);
}

$field27 = Vtiger_Field::getInstance('min_val_per_lb', $contracts);
if ($field27) {
    echo "<li>The min_val_per_lb field already exists</li><br>";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_CONTRACTS_VAL_MINPRICE';
    $field27->name = 'min_val_per_lb';
    $field27->table = 'vtiger_contracts';
    $field27->column = 'min_val_per_lb';
    $field27->columntype = 'DECIMAL(7,2)';
    $field27->uitype = 71;
    $field27->typeofdata = 'N~O';
    
    $valuation_block->addField($field27);
}

$field28 = Vtiger_Field::getInstance('valuation_deductible', $contracts);
if ($field28) {
    echo "<li>The valuation_deductible field already exists</li><br>";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CONTRACTS_DEDUCTIBLE';
    $field28->name = 'valuation_deductible';
    $field28->table = 'vtiger_contracts';
    $field28->column = 'valuation_deductible';
    $field28->columntype = 'VARCHAR(255)';
    $field28->uitype = 16;
    $field28->typeofdata = 'V~O';
    
    $valuation_block->addField($field28);
}

$field29 = Vtiger_Field::getInstance('free_fvp_allowed', $contracts);
if ($field29) {
    echo "<li>The free_fvp_allowed field already exists</li><br>";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_CONTRACTS_FREEFVP';
    $field29->name = 'free_fvp_allowed';
    $field29->table = 'vtiger_contracts';
    $field29->column = 'free_fvp_allowed';
    $field29->columntype = 'VARCHAR(3)';
    $field29->uitype = 56;
    $field29->typeofdata = 'C~O';
    
    $valuation_block->addField($field29);
}

$field30 = Vtiger_Field::getInstance('free_fvp_amount', $contracts);
if ($field30) {
    echo "<li>The free_fvp_amount field already exists</li><br>";
} else {
    $field30 = new Vtiger_Field();
    $field30->label = 'LBL_CONTRACTS_FREEFVP_AMOUNT';
    $field30->name = 'free_fvp_amount';
    $field30->table = 'vtiger_contracts';
    $field30->column = 'free_fvp_amount';
    $field30->columntype = 'VARCHAR(100)';
    $field30->uitype = 1;
    $field30->typeofdata = 'V~O';
    
    $valuation_block->addField($field30);
}

$field31 = Vtiger_Field::getInstance('assigned_user_id', $contracts);
if ($field31) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field31 = new Vtiger_Field();
    $field31->label = 'Assigned To';
    $field31->name = 'assigned_user_id';
    $field31->table = 'vtiger_crmentity';
    $field31->column = 'smownerid';
    $field31->uitype = 53;
    $field31->typeofdata = 'V~M';
    
    $block->addField($field31);
}

$field32 = Vtiger_Field::getInstance('description', $contracts);
if ($field32) {
    echo "<li>The description field already exists</li><br>";
} else {
    $field32 = new Vtiger_Field();
    $field32->label = 'LBL_CONTRACTS_NOTES';
    $field32->name = 'description';
    $field32->table = 'vtiger_crmentity';
    $field32->column = 'description';
    $field32->uitype = 19;
    $field32->typeofdata = 'V~O';
    
    $block->addField($field32);
}

$field33 = Vtiger_Field::getInstance('createdtime', $contracts);
if ($field33) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field33 = new Vtiger_Field();
    $field33->label = 'Created Time';
    $field33->name = 'createdtime';
    $field33->table = 'vtiger_crmentity';
    $field33->column = 'createdtime';
    $field33->uitype = 70;
    $field33->typeofdata = 'T~O';
    $field33->displaytype = 2;
    
    $admin_block->addField($field33);
}

$field34 = Vtiger_Field::getInstance('modifiedtime', $contracts);
if ($field34) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field34 = new Vtiger_Field();
    $field34->label = 'Modified Time';
    $field34->name = 'modifiedtime';
    $field34->table = 'vtiger_crmentity';
    $field34->column = 'modifiedtime';
    $field34->uitype = 70;
    $field34->typeofdata = 'T~O';
    $field34->displaytype = 2;
    
    $admin_block->addField($field34);
}

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$contracts->addFilter($filter1);

$filter1->addField($field0)->addField($field1, 1)->addField($field2, 2)->addField($field3, 3)->addField($field5, 4)->addField($field7, 5);

$contracts->setDefaultSharing();

$contracts->initWebservice();

$moduleInstance = Vtiger_Module::getInstance('Contracts');
$relationLabel = 'Sub-contracts';
$contracts->setRelatedList($moduleInstance, $relationLabel, array('Add'));

$accountInstance = Vtiger_Module::getInstance('Accounts');
$relationLabel = 'Contracts';
$accountInstance->setRelatedList($contracts, $relationLabel, array('Add'));

ModTracker::enableTrackingForModule($contracts->id);

$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Contracts'));

$detailviewblock = ModComments::addWidgetTo('Contracts');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";