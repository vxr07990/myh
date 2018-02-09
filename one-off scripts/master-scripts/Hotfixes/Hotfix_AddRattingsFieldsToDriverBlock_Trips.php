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


$moduleInstance = Vtiger_Module::getInstance('Trips');
if ($moduleInstance) {
    $block = Vtiger_Block::getInstance('LBL_TRIPS_DRIVER', $moduleInstance);
    if ($block) {
        echo "<h3>The LBL_TRIPS_DRIVER block already exists</h3><br> \n";
    } else {
        $block = new Vtiger_Block();
        $block->label = 'LBL_TRIPS_DRIVER';
        $moduleInstance->addBlock($block);
    }
    
    if ($block) {
        // Field Setup
    $field1 = Vtiger_Field::getInstance('trips_csarating', $moduleInstance);
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->name = 'trips_csarating';
            $field1->label = 'LBL_TRIPS_CSARATING';
            $field1->uitype = 15;
            $field1->table = 'vtiger_trips';
            $field1->column = $field1->name;
            $field1->summaryfield = 1;
            $field1->columntype = 'VARCHAR(255)';
            $field1->typeofdata = 'V~O';
            $field1->setPicklistValues(array('RAIR', 'LYTX'));
            $block->addField($field1);
        }

        $field2 = Vtiger_Field::getInstance('trips_csaranking', $moduleInstance);
        if (!$field2) {
            $field2 = new Vtiger_Field();
            $field2->name = 'trips_csaranking';
            $field2->label = 'LBL_TRIPS_CSARANKING';
            $field2->uitype = 15;
            $field2->table = 'vtiger_trips';
            $field2->column = $field2->name;
            $field2->summaryfield = 1;
            $field2->columntype = 'VARCHAR(255)';
            $field2->typeofdata = 'V~O';
            $field2->setPicklistValues(array('RAIR', 'LYTX'));
            $block->addField($field2);
        }

        $field3 = Vtiger_Field::getInstance('trips_performancerating', $moduleInstance);
        if (!$field3) {
            $field3 = new Vtiger_Field();
            $field3->name = 'trips_performancerating';
            $field3->label = 'LBL_TRIPS_PERFORMANCERATING';
            $field3->uitype = 1;
            $field3->table = 'vtiger_trips';
            $field3->column = $field3->name;
            $field3->summaryfield = 1;
            $field3->columntype = 'VARCHAR(255)';
            $field3->typeofdata = 'V~O';
            $block->addField($field3);
        }

        $field4 = Vtiger_Field::getInstance('trips_pqcrating', $moduleInstance);
        if (!$field4) {
            $field4 = new Vtiger_Field();
            $field4->name = 'trips_pqcrating';
            $field4->label = 'LBL_TRIPS_PQCRATING';
            $field4->uitype = 1;
            $field4->table = 'vtiger_trips';
            $field4->column = $field4->name;
            $field4->summaryfield = 1;
            $field4->columntype = 'VARCHAR(255)';
            $field4->typeofdata = 'V~O';
            $block->addField($field4);
        }

        $field5 = Vtiger_Field::getInstance('trips_driverclaimratio', $moduleInstance);
        if (!$field5) {
            $field5 = new Vtiger_Field();
            $field5->name = 'trips_driverclaimratio';
            $field5->label = 'LBL_TRIPS_DRIVERCLAIMRATIO';
            $field5->uitype = 1;
            $field5->table = 'vtiger_trips';
            $field5->column = $field5->name;
            $field5->summaryfield = 1;
            $field5->columntype = 'VARCHAR(255)';
            $field5->typeofdata = 'V~O';
            $block->addField($field5);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";