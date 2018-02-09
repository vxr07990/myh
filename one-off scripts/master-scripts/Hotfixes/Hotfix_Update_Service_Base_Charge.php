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



/*
 *Goals:
 *
 *
 *
PriceFrom|PriceTo|Factor
0|1.94|0
1.95|2.099|1
2.1|2.249|2
2.25|2.399|3
2.4|2.549|4
2.55|2.699|5
2.7|2.849|6
2.85|2.999|7
3|3.149|8
3.15|3.299|9
3.3|3.449|10
3.45|3.599|11
3.6|3.749|12
3.75|3.899|13
3.9|4.049|14
4.05|4.199|15
4.2|4.349|16
4.35|4.499|17
4.5|4.649|18
4.65|4.799|19
4.8|4.949|20
4.95|5.099|21
5.1|5.249|22
5.25|5.399|23
5.4|5.549|24
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once 'includes/main/WebUI.php';

$module = Vtiger_Module::getInstance('TariffServices');
$block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_SERVICECHARGE', $module);
if ($block) {
    $field = Vtiger_Field::getInstance('service_base_charge_matrix', $module);
    if ($field) {
        echo "<br /> The service_base_charge_applies field already exists in tariff services <br />";
    } else {
        echo "<br /> Adding service_base_charge_applies field to tariffs services <br />";
        $field = new Vtiger_Field();
        $field->label = 'LBL_BASE_CHARGE_SERVICE_MATRIX';
        $field->name = 'service_base_charge_matrix';
        $field->table = 'vtiger_tariffservices';
        $field->column = 'service_base_charge_matrix';
        $field->columntype = 'VARCHAR(3)';
        $field->uitype = 56;
        $field->typeofdata = 'C~O';
        $field->displaytype = 1;
        $field->quickcreate = 0;
        $field->presence = 2;
        $block->addField($field);
    }
} else {
    echo "<h3>The LBL_TARIFFSERVICES_SERVICECHARGE block DOES NOT exists</h3><br>\n";
}

$db = PearDatabase::getInstance();

$oldFuelMatrixTableName = 'vtiger_quotes_servicecharge_matrix';
if (!Vtiger_Utils::CheckTable($oldFuelMatrixTableName)) {
    print "<h2>Removing old table</h2>";
    $stmt = 'DROP TABLE IF EXISTS `'.$oldFuelMatrixTableName.'`';
    $db->query($stmt);
}

$fuelMatrixTableName = 'vtiger_tariffservicebasecharge';
if (!Vtiger_Utils::CheckTable($fuelMatrixTableName)) {
    print "<h2>Creating Table: $fuelMatrixTableName</h2>";
    $stmt = 'CREATE TABLE IF NOT EXISTS `'.$fuelMatrixTableName.'` (
             `serviceid` int(30) NOT NULL,
             `price_from` decimal(11,4) NOT NULL,
             `price_to` decimal(11,4) NOT NULL,
             `factor` decimal(11,4) NOT NULL,
             `line_item_id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
             PRIMARY KEY (`line_item_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    $db->query($stmt);
}

/*
$fuelMatrix = [
    ['PriceFrom' => '0', 'PriceTo' => '1.94', 'Factor' => '0'],
    ['PriceFrom' => '1.95', 'PriceTo' => '2.099', 'Factor' => '1'],
    ['PriceFrom' => '2.1', 'PriceTo' => '2.249', 'Factor' => '2'],
    ['PriceFrom' => '2.25', 'PriceTo' => '2.399', 'Factor' => '3'],
    ['PriceFrom' => '2.4', 'PriceTo' => '2.549', 'Factor' => '4'],
    ['PriceFrom' => '2.55', 'PriceTo' => '2.699', 'Factor' => '5'],
    ['PriceFrom' => '2.7', 'PriceTo' => '2.849', 'Factor' => '6'],
    ['PriceFrom' => '2.85', 'PriceTo' => '2.999', 'Factor' => '7'],
    ['PriceFrom' => '3', 'PriceTo' => '3.149', 'Factor' => '8'],
    ['PriceFrom' => '3.15', 'PriceTo' => '3.299', 'Factor' => '9'],
    ['PriceFrom' => '3.3', 'PriceTo' => '3.449', 'Factor' => '10'],
    ['PriceFrom' => '3.45', 'PriceTo' => '3.599', 'Factor' => '11'],
    ['PriceFrom' => '3.6', 'PriceTo' => '3.749', 'Factor' => '12'],
    ['PriceFrom' => '3.75', 'PriceTo' => '3.899', 'Factor' => '13'],
    ['PriceFrom' => '3.9', 'PriceTo' => '4.049', 'Factor' => '14'],
    ['PriceFrom' => '4.05', 'PriceTo' => '4.199', 'Factor' => '15'],
    ['PriceFrom' => '4.2', 'PriceTo' => '4.349', 'Factor' => '16'],
    ['PriceFrom' => '4.35', 'PriceTo' => '4.499', 'Factor' => '17'],
    ['PriceFrom' => '4.5', 'PriceTo' => '4.649', 'Factor' => '18'],
    ['PriceFrom' => '4.65', 'PriceTo' => '4.799', 'Factor' => '19'],
    ['PriceFrom' => '4.8', 'PriceTo' => '4.949', 'Factor' => '20'],
    ['PriceFrom' => '4.95', 'PriceTo' => '5.099', 'Factor' => '21'],
    ['PriceFrom' => '5.1', 'PriceTo' => '5.249', 'Factor' => '22'],
    ['PriceFrom' => '5.25', 'PriceTo' => '5.399', 'Factor' => '23'],
    ['PriceFrom' => '5.4', 'PriceTo' => '5.549', 'Factor' => '24'],
];

foreach ($fuelMatrix as $singleRecord) {
    print "Checking Fuel Matrix row: ". $singleRecord['PriceFrom'] . ' -- ' . $singleRecord['PriceTo'] . ' -- ' . $singleRecord['Factor'] . "<br />";
    $stmt = 'SELECT * FROM `' . $fuelMatrixTableName . '` WHERE '
            . ' `PriceFrom` = ?'
            . ' AND `PriceTo` = ?'
            . ' AND `Factor` = ?';
    $checkRes = $db->pquery($stmt, [$singleRecord['PriceFrom'], $singleRecord['PriceTo'], $singleRecord['Factor']]);

    if ($checkRes && $row = $checkRes->fetchRow()) {
        //say it exists...
        //print "FOUND Fuel Matrix row: ". $singleRecord['PriceFrom'] . ' -- ' . $singleRecord['PriceTo'] . ' -- ' . $singleRecord['Factor'] . "<br />";
    } else {
        //print "Inserting Fuel Matrix row: ". $singleRecord['PriceFrom'] . ' -- ' . $singleRecord['PriceTo'] . ' -- ' . $singleRecord['Factor'] . "<br />";
        $stmt = 'INSERT INTO `' . $fuelMatrixTableName . '` SET '
                . ' `PriceFrom` = ?, '
                . ' `PriceTo` = ?, '
                . ' `Factor` = ?';
        $db->pquery($stmt, [$singleRecord['PriceFrom'], $singleRecord['PriceTo'], $singleRecord['Factor']]);
    }
}
*/;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";