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



//error_reporting('-1');
//header('Content-Type: text/plain; charset=utf-8');

require_once 'include/database/PearDatabase.php';

echo '<h2>Invoking `' . basename(__FILE__) . '`</h2>' . \PHP_EOL;
echo '<ul>' . \PHP_EOL;

// --------------------------------------------------------------------------

echo '<li>Querying for the "HelpDesk" `tabid`.' . \PHP_EOL;

$sql = 'SELECT tabid FROM vtiger_tab WHERE name = "HelpDesk"';
$db = \PearDatabase::getInstance();
$id = $db->getOne($sql);

if (!is_numeric($id)) {
    throw new \UnexpectedValueException;
}

// --------------------------------------------------------------------------

echo '<li>Querying for the existence of the `HelpDesk_Modified_Time:DT` ' .
     'column name.' . \PHP_EOL;

$column_name = join(':', [
    'vtiger_crmentity',
    'modifiedtime',
    'modifiedtime',
    'HelpDesk_Modified_Time',
    'DT'
]);

$sql = "SELECT COUNT(cvid) FROM vtiger_cvcolumnlist
        WHERE cvid = ${id} AND columnname = '${column_name}'";

$count = $db->getOne($sql);

if ($count === false || !is_numeric($count)) {
    throw new \UnexpectedValueException;
}

// --------------------------------------------------------------------------

if ($count == 0) {
    echo '<li>Determine the column index to insert the ' .
         '`HelpDesk_Modified_Time:DT` column name.' . \PHP_EOL;

    $sql = "SELECT MAX(columnindex) FROM vtiger_cvcolumnlist
			WHERE cvid = ${id}";

    $column_index = $db->getOne($sql);

    if (!is_numeric($column_index)) {
        throw new \UnexpectedValueException;
    }

    $column_index += 1;

    // ----------------------------------------------------------------------

    echo '<li>Inserting the `HelpDesk_Modified_Time:DT` column name.' . \PHP_EOL;

    $sql = "INSERT INTO vtiger_cvcolumnlist (cvid, columnindex, columnname)
	        VALUES (${id}, ${column_index}, '${column_name}')";

    $result = $db->query($sql);

    if (!($result instanceof \ADORecordSet_empty)) {
        throw new \UnexpectedValueException;
    }
} else {
    echo '<li>The `HelpDesk_Modified_Time:DT` column name is already present.' .
         \PHP_EOL;
}

echo '</ul>' . \PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";