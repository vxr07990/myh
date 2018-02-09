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



$module1 = Vtiger_Module::getInstance('Employees');
if ($module1) {
    echo "<h2>Updating Employees Fields</h2><br>";

    $block1 = Vtiger_Block::getInstance('LBL_EMPLOYEES_SAFETYDETAILS', $module1);
    if ($block1) {
        $field1 = Vtiger_Field::getInstance('employees_testresult', $module1);
        if ($field1) {
            echo "<li>The employees_testresult field already exists</li><br>";
        } else {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_EMPLOYEES_TESTRESULT';
            $field1->name = 'employees_testresult';
            $field1->table = 'vtiger_employees';
            $field1->column = 'employees_testresult';
            $field1->columntype = 'DATE';
            $field1->uitype = 5;
            $field1->typeofdata = 'D~O';
            $block1->addField($field1);
        }

        $field2 = Vtiger_Field::getInstance('employees_backgroundcheck', $module1);
        if ($field2) {
            echo "<li>The employees_backgroundcheck field already exists</li><br>";
        } else {
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_EMPLOYEES_BACKGROUNDCHECK';
            $field2->name = 'employees_backgroundcheck';
            $field2->table = 'vtiger_employees';
            $field2->column = 'employees_backgroundcheck';
            $field2->columntype = 'DATE';
            $field2->uitype = 5;
            $field2->typeofdata = 'D~O';
            $block1->addField($field2);
        }

        $field3 = Vtiger_Field::getInstance('employees_regformrecdate', $module1);
        if ($field3) {
            echo "<li>The employees_regformrecdate field already exists</li><br>";
        } else {
            $field3 = new Vtiger_Field();
            $field3->label = 'LBL_EMPLOYEES_REGFORMRECDATE';
            $field3->name = 'employees_regformrecdate';
            $field3->table = 'vtiger_employees';
            $field3->column = 'employees_regformrecdate';
            $field3->columntype = 'DATE';
            $field3->uitype = 5;
            $field3->typeofdata = 'D~O';
            $block1->addField($field3);
        }

        $field4 = Vtiger_Field::getInstance('employees_autovalidformreceived', $module1);
        if ($field4) {
            echo "<li>The employees_autovalidformreceived field already exists</li><br>";
        } else {
            $field4 = new Vtiger_Field();
            $field4->label = 'LBL_EMPLOYEES_AUTOVALIDFORMRECEIVED';
            $field4->name = 'employees_autovalidformreceived';
            $field4->table = 'vtiger_employees';
            $field4->column = 'employees_autovalidformreceived';
            $field4->columntype = 'DATE';
            $field4->uitype = 5;
            $field4->typeofdata = 'D~O';
            $block1->addField($field4);
        }

        $field5 = Vtiger_Field::getInstance('employees_photoidcheckdate', $module1);
        if ($field5) {
            echo "<li>The employees_photoidcheckdate field already exists</li><br>";
        } else {
            $field5 = new Vtiger_Field();
            $field5->label = 'LBL_EMPLOYEES_PHOTOIDCHECKDATE';
            $field5->name = 'employees_photoidcheckdate';
            $field5->table = 'vtiger_employees';
            $field5->column = 'employees_photoidcheckdate';
            $field5->columntype = 'DATE';
            $field5->uitype = 5;
            $field5->typeofdata = 'D~O';
            $block1->addField($field5);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";