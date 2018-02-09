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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Quotes');

$block1 = new Vtiger_Block();
$block1->label = 'SIT Details';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'Date In';
$field1->name = 'sit_origin_date_in';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_date_in';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Date In';
$field1->name = 'sit_dest_date_in';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_date_in';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pickup Date';
$field1->name = 'sit_origin_pickup_date';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_pickup_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Delivery Date';
$field1->name = 'sit_dest_delivery_date';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_delivery_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'sit_origin_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'sit_dest_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Zip';
$field1->name = 'sit_origin_zip';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_zip';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Zip';
$field1->name = 'sit_dest_zip';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_zip';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Miles';
$field1->name = 'sit_origin_miles';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_miles';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Miles';
$field1->name = 'sit_dest_miles';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_miles';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Num. Days';
$field1->name = 'sit_origin_number_days';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_number_days';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Num. Days';
$field1->name = 'sit_dest_number_days';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_number_days';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'First Day Rate';
$field1->name = 'sit_origin_first_day';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_first_day';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'First Day Rate';
$field1->name = 'sit_dest_first_day';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_first_day';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'First Day Cost';
$field1->name = 'sit_origin_first_day_cost';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_first_day_cost';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'First Day Cost';
$field1->name = 'sit_dest_first_day_cost';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_first_day_cost';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Second Day Rate';
$field1->name = 'sit_origin_sec_day';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_sec_day';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Second Day Rate';
$field1->name = 'sit_dest_sec_day';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_sec_day';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Second Day Cost';
$field1->name = 'sit_origin_sec_day_cost';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_sec_day_cost';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Second Day Cost';
$field1->name = 'sit_dest_sec_day_cost';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_sec_day_cost';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pickup & Delivery';
$field1->name = 'sit_origin_pickup_delivery';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_pickup_delivery';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Pickup & Delivery';
$field1->name = 'sit_dest_pickup_delivery';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_pickup_delivery';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Fuel Surcharge %';
$field1->name = 'sit_origin_fuel_percent';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_fuel_percent';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Fuel Surcharge %';
$field1->name = 'sit_dest_fuel_percent';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_fuel_percent';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Fuel Surcharge';
$field1->name = 'sit_origin_fuel_surcharge';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_fuel_surcharge';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Fuel Surcharge';
$field1->name = 'sit_dest_fuel_surcharge';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_fuel_surcharge';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'IRR %';
$field1->name = 'sit_origin_irr_percent';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_irr_percent';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'IRR %';
$field1->name = 'sit_dest_irr_percent';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_irr_percent';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'IRR';
$field1->name = 'sit_origin_irr';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_irr';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'IRR';
$field1->name = 'sit_dest_irr';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_irr';
$field1->columntype = 'DECIMAL(10,3)';
$field1->uitype = 71;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Overtime';
$field1->name = 'sit_origin_overtime';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_origin_overtime';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Overtime';
$field1->name = 'sit_dest_overtime';
$field1->table = 'vtiger_quotes';
$field1->column = 'sit_dest_overtime';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);


$block1->save($module);
