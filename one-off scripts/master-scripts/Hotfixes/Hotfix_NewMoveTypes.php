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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

function newMoveTypeEntry($name, $sequence)
{
    $db = PearDatabase::getInstance();
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_move_type_seq SET id = id + 1');
    echo "<br>updated sequence table<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_move_type_seq`', array());
    $row = $result->fetchRow();
    $moveTypeId = $row[0];
    echo "<br>move_type id set: ".$moveTypeId."<br>";
    $sql = 'INSERT INTO `vtiger_move_type` (move_typeid, move_type, sortorderid, presence) VALUES (?, ?, ?, 1)';
    $db->pquery($sql, array($moveTypeId, $name, $sequence));
}

echo "<br>Begin Sirva move type (contracts) hotfix<br>";

$newTypes = ['Interstate', 'Intrastate', 'O&I', 'Local Canada', 'Local US', 'Sirva Military', 'Inter-Provincial', 'Intra-Provincial', 'Cross Border', 'Alaska', 'Hawaii', 'International', 'Max 3', 'Max 4'];

if (Vtiger_Utils::CheckTable('vtiger_move_type')) {
    echo "<br>vtiger_move_type exists! Truncating...<br>";
    Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_move_type`');
    echo "<br>completed truncating...adding Sirva specific move types<br>";
    foreach ($newTypes as $index => $type) {
        echo "<br> adding type: ".$type;
        newMoveTypeEntry($type, $index+1);
    }
} else {
    echo "<br>vtiger_move_type not found! No action taken<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";