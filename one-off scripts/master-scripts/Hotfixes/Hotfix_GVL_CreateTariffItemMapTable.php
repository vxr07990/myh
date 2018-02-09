<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/**
 * Created by PhpStorm.
 * User: jgriffin
 * Date: 10/7/2016
 * Time: 3:25 PM
 */

$filePath = 'go_live/gvl/FBR_TARIFF_SRVCE_ITEM_W_FLAGS.csv';
$tablename = 'vtiger_gvl_tariff_item_map';

if (!file_exists($filePath)) {
    //no file?
    print "File: " . $filePath ." does not exist.  DONE!<br />\n";
    return;
}

truncateTariffMapTable($tablename);
buildTariffMapTable($tablename);
$dbFields = getTariffMapTableFields($tablename);

if (!$dbFields) {
    print "NO Database fields: " . print_r($dbFields) . "<br />\n";
    print "For table: " . $tablename . "<br />\n";
    print "Exiting.<br />\n";
    return;
}

echo "Start Tariff Map Import <br>";

$itemList = fopen($filePath, 'r');
$headers = fgetcsv($itemList);

$dbFieldHeaderMap = buildDBFieldHeaderTariffItemMap($dbFields, $headers);

$db = PearDatabase::getInstance();
$doArray = [];
while ($csv = fgetcsv($itemList)) {
    $doArray = [];
    foreach ($dbFieldHeaderMap as $fieldName => $key) {
        $doArray[$fieldName] = $csv[$key];
    }
    $params     = [];
    $tabList    = '';
    foreach ($doArray as $key => $value) {
        $tabList .= ($tabList?',':'').' `'.$key.'`';
        if ($value) {
            $params[] = $value;
        } else {
            $params[] = '';
        }
    }
    //Use INSERT IGNORE to we continue past key mismatches since they shouldn't be possible;
    $new_sql  = 'INSERT IGNORE INTO `'.$tablename.'` ('.$tabList.') VALUES ('.generateQuestionMarks($params).')';
    $db->pquery($new_sql, $params);
}
fclose($itemList);

function truncateTariffMapTable($tablename)
{
    $db = PearDatabase::getInstance();
    print "Clearing ".$tablename." if it exists.<br />\n";
    if (!Vtiger_Utils::CheckTable($tablename)) {
        return;
    }
    $stmt = 'TRUNCATE TABLE `'.$tablename.'`';
    $db->pquery($stmt);
    print "Successfully cleared ".$tablename."<br />\n";
}

function buildTariffMapTable($tablename)
{
    $db = PearDatabase::getInstance();
    print "CHecking ".$tablename." exists.<br />\n";
    if (!Vtiger_Utils::CheckTable($tablename)) {
        print "creating ".$tablename.".<br />\n";
        $stmt = 'CREATE TABLE `'.$tablename.'` (
            `service_code` VARCHAR(55),
            `tariff_number` VARCHAR(55),
            `standard_item_code` VARCHAR(55),
            `stop_type_code` VARCHAR(55),
            `standard_item_code_description` VARCHAR(55),
            `service` VARCHAR(3),
            `invoicable` VARCHAR(3),
            `distributable` VARCHAR(3),
            `itype_code` VARCHAR(3),
            `role_code` VARCHAR(3),
            `invseq` decimal(5,2),
            `invprint` VARCHAR(3),
            `unit_default` VARCHAR(11),
            `reporting_category` VARCHAR(3),
            `gcs_flag` VARCHAR(3),
            PRIMARY KEY (`service_code`,`tariff_number`),
            KEY `service_code` (`service_code`)
            )';
        //`status` VARCHAR (50),
        //KEY `status` (`status`)
        $db->pquery($stmt);
    }
}

function getTariffMapTableFields($tablename)
{
    $db = PearDatabase::getInstance();
    print "Checking ".$tablename." exists.<br />\n";
    if (!Vtiger_Utils::CheckTable($tablename)) {
        return;
    }
    $stmt   = 'DESCRIBE `'.$tablename.'`';
    $result = $db->pquery($stmt);
    if (!method_exists($result, 'fetchRow')) {
        return;
    }
    $rowArray = [];
    while ($row = $result->fetchRow()) {
        $rowArray[] = $row['Field'];
    }

    return $rowArray;
}

function buildDBFieldHeaderTariffItemMap($dbFields, $headers)
{
    if (!$headers) {
        return false;
    }

    if (!$dbFields) {
        return false;
    }

    $array = [];
    foreach ($headers as $index => $fieldName) {
        $fieldName = strtolower($fieldName);
        if (false !== array_search($fieldName, $dbFields)) {
            $array[$fieldName] = $index;
        }
    }

    return $array;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";