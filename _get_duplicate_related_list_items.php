<?php

ini_set('display_errors', 'on');
error_reporting(\E_ERROR & \E_WARNING);

require_once 'include/utils/utils.php';
require_once 'vendor/autoload.php';

$db = PearDatabase::getInstance();

// ---------------------------------------------------------------------------

$sql = 'SELECT DISTINCT tabid FROM vtiger_relatedlists';
$result = $db->query($sql);

if (!$result) {
    exit('Result variable is invalid' . \PHP_EOL);
}

$tab_ids = [];

foreach ($result as $row) {
    $tab_ids[] = (int)$row['tabid'];
}

sort($tab_ids);

// ---------------------------------------------------------------------------

$sql = 'SELECT * FROM vtiger_relatedlists WHERE tabid = ?';

foreach ($tab_ids as $tab_id) {
    $result = $db->pquery($sql, [$tab_id]);

    if (!$result) {
        throw new \UnexpectedValueException;
    }

    $labels = [];
    $duplicates = [];

    foreach ($result as $row) {
        $label = $row['label'];

        if (in_array($label, $labels)) {
            $duplicates[] = $label;

            continue;
        }

        $labels[] = $label;
    }

    if ($duplicates) {
        echo "\nTab ID: ${tab_id}\n";
        dump($duplicates);
    }
}
