<?php
include_once('vtlib/Vtiger/Menu.php');
include_once 'includes/main/WebUI.php';
echo "begin clear temp tables<br>";
$prefix = $_REQUEST['session_prefix'];
$defaultTables = array(
    'vtiger_quotes_baseplus',
    'vtiger_quotes_breakpoint',
    'vtiger_quotes_bulky',
    'vtiger_quotes_countycharge',
    'vtiger_quotes_crating',
    'vtiger_quotes_cwtbyweight',
    'vtiger_quotes_hourlyset',
    'vtiger_quotes_packing',
    'vtiger_quotes_perunit',
    'vtiger_quotes_sectiondiscount',
    'vtiger_quotes_servicecost',
    'vtiger_quotes_valuation',
    'vtiger_quotes_vehicles',
    'vtiger_quotes_weightmileage',
    'vtiger_corporate_vehicles',
    'vtiger_inventoryshippingrel',
    'vtiger_inventorysubproductrel',
    'vtiger_packing_items',
    'vtiger_misc_accessorials',
    'vtiger_crates',
    'vtiger_bulky_items',
    'vtiger_crates_seq',
    'vtiger_crmentity',
    'vtiger_crmentity_seq',
    'vtiger_inventoryproductrel',
    'vtiger_inventoryproductrel_seq',
    'vtiger_misc_accessorials_seq',
    'vtiger_quotes',
    'vtiger_quotesbillads',
    'vtiger_quotescf',
    'vtiger_quotesshipads'
);
if (!empty($prefix)) {
    foreach ($defaultTables as $tableName) {
        echo "EXECUTING QUERY: DROP TABLE IF EXISTS `".$prefix.'_'.$tableName."`<br>";
        Vtiger_Utils::ExecuteQuery('DROP TABLE IF EXISTS `'.$prefix.'_'.$tableName.'`');
    }
} else {
    echo "no prefix given, clearing ALL the temp tables<br>";
    echo "DB NAME: ".getenv('DB_NAME')." <br>";
    $db = PearDatabase::getInstance();

    $sql = "SELECT table_name 
			FROM INFORMATION_SCHEMA.TABLES 
			WHERE table_schema = '".getenv('DB_NAME')."' 
			AND table_name regexp '^[0-9a-zA-Z]{26}_'";
    echo 'SQL: '.$sql.' <br>';
    $result = $db->pquery($sql, []);
    $row = $result->fetchRow();
    while ($row != null) {
        clearTable($row['table_name'], $db);
        $row = $result->fetchRow();
    }
}
echo "end clear temp tables<br>";

function clearTable($tableName, $db)
{
    echo "Dropping $tableName <br>".PHP_EOL;
    $sql = "DROP TABLE IF EXISTS `$tableName`";
    $result = $db->pquery($sql, []);
}
