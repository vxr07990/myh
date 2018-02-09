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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin testhotfix<br>";

$moduleInstance = Vtiger_Module::getInstance('Estimates');

$block1 = Vtiger_Block::getInstance('LBL_QUOTES_TRANSPORTATIONPRICING', $moduleInstance);

if (!$block1) {
    echo "<br>craeting block LBL_QUOTES_TRANSPORTATIONPRICING...";

    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_QUOTES_TRANSPORTATIONPRICING';
    $moduleInstance->addBlock($block1);
    echo "done!<br>";

    echo "<br>creating fields...";
    $field3 = Vtiger_Field::getInstance('small_shipment', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_SMALLSHIPMENT';
        $field3->name       = 'small_shipment';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'small_shipment';
        $field3->columntype = 'VARCHAR(3)';
        $field3->uitype     = 56;
        $field3->typeofdata = 'V~O';
        $block1->addField($field3);
    } else {
        echo "<br>small_shipment already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('small_shipment_miles', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_SMALLSHIPMENTMILES';
        $field3->name       = 'small_shipment_miles';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'small_shipment_miles';
        $field3->columntype = 'INT(10)';
        $field3->uitype     = 7;
        $field3->typeofdata = 'I~O';
        $block1->addField($field3);
    } else {
        echo "<br>small_shipment_miles already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('small_shipment_ot', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_SMALLSHIPMENTOT';
        $field3->name       = 'small_shipment_ot';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'small_shipment_ot';
        $field3->columntype = 'VARCHAR(3)';
        $field3->uitype     = 56;
        $field3->typeofdata = 'V~O';
        $block1->addField($field3);
    } else {
        echo "<br>small_shipment_ot already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('priority_shipping', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_PRIORITYSHIPPING';
        $field3->name       = 'priority_shipping';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'priority_shipping';
        $field3->columntype = 'VARCHAR(3)';
        $field3->uitype     = 56;
        $field3->typeofdata = 'V~O';
        $block1->addField($field3);
    } else {
        echo "<br>priority_shipping already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('pshipping_booker_commission', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_PSHIPPINGBOOKERCOMMISSION';
        $field3->name       = 'pshipping_booker_commission';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'pshipping_booker_commission';
        $field3->columntype = 'VARCHAR(50)';
        $field3->defaultvalue = '400.00';
        $field3->uitype     = 16;
        $field3->typeofdata = 'V~O';
        $block1->addField($field3);
        $picklistValues = [];
        $startValue = 300;
        while ($startValue <= 1900) {
            $startValue += 100;
            $picklistValues[] = number_format($startValue, 2, '.', '');
        }
        $field3->setPicklistValues($picklistValues);
    } else {
        echo "<br>booker_commission already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('pshipping_origin_miles', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_PSHIPPINGORIGINMILES';
        $field3->name       = 'pshipping_origin_miles';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'pshipping_origin_miles';
        $field3->columntype = 'INT(10)';
        $field3->uitype     = 7;
        $field3->typeofdata = 'I~O';
        $block1->addField($field3);
    } else {
        echo "<br>pshipping_origin_miles already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('pshipping_destination_miles', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_PSHIPPINGDESTINATIONMILES';
        $field3->name       = 'pshipping_destination_miles';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'pshipping_destination_miles';
        $field3->columntype = 'INT(10)';
        $field3->uitype     = 7;
        $field3->typeofdata = 'I~O';
        $block1->addField($field3);
    } else {
        echo "<br>pshipping_origin_miles already exists<br>";
    }
    $block1->save($moduleInstance);
    echo "done!";
} else {
    echo "<br>LBL_QUOTES_TRANSPORTATIONPRICING already exists, no action taken";
}

echo "<br>end testhotfix";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";