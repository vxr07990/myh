<?php
include_once('vtlib/Vtiger/Menu.php');
include_once 'includes/main/WebUI.php';
echo "begin Reset of sequenced tables to match the max seq.<br>";
$db = PearDatabase::getInstance();

$stmt = "show tables like '%_seq'";
$allTablesResult = $db->pquery($stmt, []);

$seqencedTables = [];

while ($TableRow = $allTablesResult->fetchRow()) {
    $mainTable = $tableName = $TableRow[0];
    $mainTable = preg_replace('/_seq$/i', '', $mainTable);
    $seqencedTables[$mainTable] = 1;
    $stmt = 'show keys from ' . $mainTable . " WHERE Key_name = 'PRIMARY'";
    $keysResults = $db->pquery($stmt);
    if ($keysResults) {
        if ($keys = $keysResults->fetchRow()) {
            $mainKey   = $keys['Column_name'];
            if ($mainKey) {
                $stmtCount = 'SELECT max('.$mainKey.') AS maxID from '.$mainTable;
                $maxID       = $db->pquery($stmtCount)->fetchRow()[0];
                $stmt        = 'SELECT *FROM '.$tableName;
                $seqResultID = $db->pquery($stmt)->fetchRow()[0];
                if ($maxID == $seqResultID) {
                    //file_put_contents('logs/devLog.log', "\n (_reset_seq_tables.php:".__LINE__."):  (SEQ MATCHES: Table: ".$tableName." -- ".$mainTable.")", FILE_APPEND);
                } elseif (!$maxID) {
                    //file_put_contents('logs/devLog.log', "\n (_reset_seq_tables.php:".__LINE__."):  (NO MAX_ID: $stmtCount)", FILE_APPEND);
                } elseif (!is_numeric($maxID)) {
                    //file_put_contents('logs/devLog.log', "\n (_reset_seq_tables.php:".__LINE__."):  (MAX_ID NOT NUMERIC: $stmtCount -- $maxID)", FILE_APPEND);
                } else {
                    $stmtUpdate = 'UPDATE '.$tableName.' SET id=?';
                    //file_put_contents('logs/devLog.log', "\n (_reset_seq_tables.php:".__LINE__."):  ($stmtUpdate -- $maxID)", FILE_APPEND);
                    $db->pquery($stmtUpdate, [$maxID]);
                }
            } else {
                //file_put_contents('logs/devLog.log', "\n (_reset_seq_tables.php:".__LINE__."):  (FAIL: no primary keys; ".$mainTable .")", FILE_APPEND);
            }
        } else {
            //file_put_contents('logs/devLog.log', "\n (_reset_seq_tables.php:".__LINE__."):  (FAIL: to get keys; ".$mainTable .")", FILE_APPEND);
        }
    }
}

/*
$stmt = 'SHOW TABLES WHERE `Tables_in_' . getenv('DB_NAME') . '` NOT LIKE \'%_seq\'';
print "$stmt;\n";
$nonSeqTablesResults = $db->pquery($stmt,[]);

$crmMaxID = 56025;
while($allTableRow = $nonSeqTablesResults->fetchRow()) {
    $tableName = $allTableRow[0];
    if ($seqencedTables[$tableName]) {
        //print "continue: $tableName\n";
        continue;
    }
    $stmt        = 'show keys from '.$tableName." WHERE Key_name = 'PRIMARY'";
    $keysResults = $db->pquery($stmt);
    if ($keysResults) {
        if ($keys = $keysResults->fetchRow()) {
            $mainKey = $keys['Column_name'];
            if ($mainKey) {
                $stmtCount   = 'SELECT max('.$mainKey.') AS maxID from '.$tableName;
                $maxID       = $db->pquery($stmtCount)->fetchRow()[0];
                //print "$tableName -- MAX: $maxID -- $crmMaxID\n";
                if ($maxID > $crmMaxID) {
                    print "HERE: $tableName -- $maxID -- $crmMaxID\n";
                }
            } else {
                //no main key
            }
        } else {
            //no keys?
        }
        //no keys?
    } else {
    }
}
*/
