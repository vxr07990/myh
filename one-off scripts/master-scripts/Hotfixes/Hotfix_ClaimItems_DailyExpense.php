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

$db = PearDatabase::getInstance();

echo "<br /> Adding vtiger_claims_daily_expense table (Claims & ClaimItems) <br />";


$db->query("CREATE TABLE IF NOT EXISTS `vtiger_claims_daily_expense` (
  `dailyexpenseid` int(19) NOT NULL AUTO_INCREMENT,
  `rel_crmid` int(19) NOT NULL,
  `expense_date` date DEFAULT NULL,
  `no_adults` varchar(150) DEFAULT NULL,
  `no_children` varchar(150) DEFAULT NULL,
  `daily_rate` varchar(150) DEFAULT NULL,
  `no_meals` varchar(150) DEFAULT NULL,
  `total_cost_meals` varchar(150) DEFAULT NULL,
  `daily_total` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`dailyexpenseid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

echo "<br /> Done!  <br />";


$claimItemsInstance = Vtiger_Module::getInstance('ClaimItems');
if ($claimItemsInstance) {
    echo "<h2>Updating Module Fields</h2><br>";
    
    $block = Vtiger_Block::getInstance('LBL_CLAIMITEMS_CUSTOMER_REQUEST', $claimItemsInstance);
    if (!$block) {
        $block = new Vtiger_Block();
        $block->label = 'LBL_CLAIMITEMS_CUSTOMER_REQUEST';
        $claimItemsInstance->addBlock($block);
    }
    
    if ($block) {
        $field3 = Vtiger_Field::getInstance('claimitemsdetails_request_date', $claimItemsInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->name = 'claimitemsdetails_request_date';
            $field3->label = 'LBL_CLAIMITEMS_REQUEST_DATE';
            $field3->uitype = 5;
            $field3->table = 'vtiger_claimitems';
            $field3->summaryfield = 1;
            $field3->column = $field3->name;
            $field3->columntype = 'DATE';
            $field3->typeofdata = 'D~O';
            $block->addField($field3);
        }

        $field4 = Vtiger_Field::getInstance('claimitemsdetails_request_damount', $claimItemsInstance);
        if (!$field4) {
            $field4 = new Vtiger_Field();
            $field4->name = 'claimitemsdetails_request_damount';
            $field4->label = 'LBL_CLAIMITEMS_REQUEST_DAMOUNT';
            $field4->uitype = 71;
            $field4->table = 'vtiger_claimitems';
            $field4->summaryfield = 1;
            $field4->column = $field4->name;
            $field4->columntype = 'decimal(13,2)';
            $field4->typeofdata = 'N~O~10,2';
            $block->addField($field4);
        }
        
        $field5 = Vtiger_Field::getInstance('claimitemsdetails_request_days', $claimItemsInstance);
        if (!$field5) {
            $field5 = new Vtiger_Field();
            $field5->name = 'claimitemsdetails_request_days';
            $field5->label = 'LBL_CLAIMITEMS_REQUEST_DAYS';
            $field5->uitype = 7;
            $field5->table = 'vtiger_claimitems';
            $field5->summaryfield = 1;
            $field5->column = $field5->name;
            $field5->columntype = 'INT(5)';
            $field5->typeofdata = 'I~O';
            $block->addField($field5);
        }
        
        $field6 = Vtiger_Field::getInstance('claimitemsdetails_request_tamount', $claimItemsInstance);
        if (!$field6) {
            $field6 = new Vtiger_Field();
            $field6->name = 'claimitemsdetails_request_tamount';
            $field6->label = 'LBL_CLAIMITEMS_REQUEST_TAMOUNT';
            $field6->uitype = 71;
            $field6->table = 'vtiger_claimitems';
            $field6->summaryfield = 1;
            $field6->column = $field6->name;
            $field6->columntype = 'decimal(13,2)';
            $field6->typeofdata = 'N~O~10,2';
            $block->addField($field6);
        }
    }
    
    $block = Vtiger_Block::getInstance('LBL_CLAIMITEMS_CUSTOMER_AUTHORIZED', $claimItemsInstance);
    if (!$block) {
        $block = new Vtiger_Block();
        $block->label = 'LBL_CLAIMITEMS_CUSTOMER_AUTHORIZED';
        $claimItemsInstance->addBlock($block);
    }
    
    if ($block) {
        $field7 = Vtiger_Field::getInstance('claimitemsdetails_authorized_date', $claimItemsInstance);
        if (!$field7) {
            $field7 = new Vtiger_Field();
            $field7->name = 'claimitemsdetails_authorized_date';
            $field7->label = 'LBL_CLAIMITEMS_AUTHORIZED_DATE';
            $field7->uitype = 5;
            $field7->table = 'vtiger_claimitems';
            $field7->summaryfield = 1;
            $field7->column = $field7->name;
            $field7->columntype = 'DATE';
            $field7->typeofdata = 'D~O';
            $block->addField($field7);
        }

        $field8 = Vtiger_Field::getInstance('claimitemsdetails_authorized_damount', $claimItemsInstance);
        if (!$field8) {
            $field8 = new Vtiger_Field();
            $field8->name = 'claimitemsdetails_authorized_damount';
            $field8->label = 'LBL_CLAIMITEMS_AUTHORIZED_DAMOUNT';
            $field8->uitype = 71;
            $field8->table = 'vtiger_claimitems';
            $field8->summaryfield = 1;
            $field8->column = $field8->name;
            $field8->columntype = 'decimal(13,2)';
            $field8->typeofdata = 'N~O~10,2';
            $block->addField($field8);
        }
        
        $field9 = Vtiger_Field::getInstance('claimitemsdetails_authorized_days', $claimItemsInstance);
        if (!$field9) {
            $field9 = new Vtiger_Field();
            $field9->name = 'claimitemsdetails_authorized_days';
            $field9->label = 'LBL_CLAIMITEMS_AUTHORIZED_DAYS';
            $field9->uitype = 7;
            $field9->table = 'vtiger_claimitems';
            $field9->summaryfield = 1;
            $field9->column = $field9->name;
            $field9->columntype = 'INT(5)';
            $field9->typeofdata = 'I~O';
            $block->addField($field9);
        }
        
        $field10 = Vtiger_Field::getInstance('claimitemsdetails_authorized_tamount', $claimItemsInstance);
        if (!$field10) {
            $field10 = new Vtiger_Field();
            $field10->name = 'claimitemsdetails_authorized_tamount';
            $field10->label = 'LBL_CLAIMITEMS_AUTHORIZED_TAMOUNT';
            $field10->uitype = 71;
            $field10->table = 'vtiger_claimitems';
            $field10->summaryfield = 1;
            $field10->column = $field10->name;
            $field10->columntype = 'decimal(13,2)';
            $field10->typeofdata = 'N~O~10,2';
            $block->addField($field10);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";