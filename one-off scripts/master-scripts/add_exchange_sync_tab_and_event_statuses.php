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



$Vtiger_Utils_Log = true;

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'vendor/autoload.php';

echo '<h1>Adding Exchange Sync Tab</h1>', PHP_EOL;
echo '<ul>', PHP_EOL;

$db  = PearDatabase::getInstance();
$sql = 'SELECT tabid FROM vtiger_tab WHERE name = "Exchange"';
$id  = $db->getOne($sql);

if (!$id) {
    $sql = 'SELECT tabid FROM vtiger_tab ORDER BY tabid DESC LIMIT 1';
    $id  = $db->getOne($sql) + 1;

    // -----

    $sql         = 'INSERT INTO vtiger_tab (tabid, name, presence, tabsequence, tablabel) VALUES (?, ?, ?, ?, ?)';
    $tabid       = $id;
    $name        = 'Exchange';
    $presence    = 0;
    $tabsequence = -1;
    $tablabel    = 'Exchange';
    $params      = [$tabid, $name, $presence, $tabsequence, $tablabel];
    $result      = $db->pquery($sql, $params);

    echo '<li>Added the `Exchange` module to the `vtiger_tab` table.', PHP_EOL;
}

echo '</ul>', PHP_EOL;

// -----

echo '<h1>Adding Exchange Sync Event Statuses</h1>', PHP_EOL;
echo '<ul>', PHP_EOL;

$sql = 'SELECT eventstatusid FROM vtiger_eventstatus WHERE eventstatus = "Busy" LIMIT 1';
$id  = $db->getOne($sql);

if (!$id) {
    $sql               = 'SELECT picklist_valueid FROM vtiger_eventstatus ORDER BY picklist_valueid DESC LIMIT 1';
    $picklist_valueid  = $db->getOne($sql) + 1;

    $sql               = 'SELECT sortorderid FROM vtiger_eventstatus ORDER BY sortorderid DESC LIMIT 1';
    $sortorderid       = $db->getOne($sql) + 1;

    $eventstatus       = 'Busy';
    $presence          = 0;
    $sql               = 'INSERT INTO vtiger_eventstatus (eventstatus, presence, picklist_valueid, sortorderid) VALUES (?, ?, ?, ?)';
    $params            = [$eventstatus, $presence, $picklist_valueid, $sortorderid];
    $result            = $db->pquery($sql, $params);

    echo '<li>Added "Busy" to the `vtiger_eventstatus` table.', PHP_EOL;
}

// -----

$sql = 'SELECT eventstatusid FROM vtiger_eventstatus WHERE eventstatus = "Free" LIMIT 1';
$id  = $db->getOne($sql);

if (!$id) {
    $sql               = 'SELECT picklist_valueid FROM vtiger_eventstatus ORDER BY picklist_valueid DESC LIMIT 1';
    $picklist_valueid  = $db->getOne($sql) + 1;

    $sql               = 'SELECT sortorderid FROM vtiger_eventstatus ORDER BY sortorderid DESC LIMIT 1';
    $sortorderid       = $db->getOne($sql) + 1;

    $eventstatus       = 'Free';
    $presence          = 0;
    $sql               = 'INSERT INTO vtiger_eventstatus (eventstatus, presence, picklist_valueid, sortorderid) VALUES (?, ?, ?, ?)';
    $params            = [$eventstatus, $presence, $picklist_valueid, $sortorderid];
    $result            = $db->pquery($sql, $params);

    echo '<li>Added "Free" to the `vtiger_eventstatus` table.', PHP_EOL;
}

echo '</ul>', PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";