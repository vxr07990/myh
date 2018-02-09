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
 * User: dbolin
 * Date: 10/4/2016
 * Time: 9:50 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo __FILE__.PHP_EOL;

$db = &PearDatabase::getInstance();

$sql = 'SHOW INDEX FROM `vtiger_crmentityrel`';
if (!$db->pquery($sql)->fetchRow()) {
    echo 'Creating combined primary key for vtiger_crmentityrel'.PHP_EOL;
    $db->pquery('CREATE TABLE temp_cer SELECT DISTINCT * FROM vtiger_crmentityrel');
    $db->pquery('ALTER TABLE vtiger_crmentityrel RENAME vtiger_crmentityrel_old');
    $db->pquery('ALTER TABLE temp_cer RENAME vtiger_crmentityrel');
    $db->pquery('ALTER TABLE `vtiger_crmentityrel` ADD PRIMARY KEY(crmid, module, relcrmid, relmodule)');
}

// now fix estimate/actual to order relations

$res = $db->pquery('SELECT quoteid, orders_id FROM vtiger_quotes 
                    INNER JOIN vtiger_crmentity ON(vtiger_quotes.quoteid=vtiger_crmentity.crmid)
                    WHERE setype=?', ['Estimates']);
while ($row = $res->fetchRow()) {
    CRMEntity::UpdateRelation($row['quoteid'], 'Estimates', $row['orders_id'], 'Orders');
}
$res = $db->pquery('SELECT quoteid, orders_id FROM vtiger_quotes 
                    INNER JOIN vtiger_crmentity ON(vtiger_quotes.quoteid=vtiger_crmentity.crmid)
                    WHERE setype=?', ['Actuals']);
while ($row = $res->fetchRow()) {
    CRMEntity::UpdateRelation($row['quoteid'], 'Actuals', $row['orders_id'], 'Orders');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";