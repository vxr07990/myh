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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting TO update dwelling Options</h1><br>\n";

$db = PearDatabase::getInstance();
Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_dwelling_type`");

// Options you want to add to the dwelling types
$data = array(
    array(
        'dwelling_type' => 'Studio',
        'presence' => '1',
        'sortorderid' => '1',
    ),
    array(
        'dwelling_type' => 'Small Move (1000 lbs.)',
        'presence' => '1',
        'sortorderid' => '2',
    ),
    array(
        'dwelling_type' => 'Small Move (2000 lbs.)',
        'presence' => '1',
        'sortorderid' => '3',
    ),
    array(
        'dwelling_type' => '1 Bedroom Apt',
        'presence' => '1',
        'sortorderid' => '4',
    ),
    array(
        'dwelling_type' => '1 Bedroom House',
        'presence' => '1',
        'sortorderid' => '5',
    ),
    array(
        'dwelling_type' => '2 Bedroom Apt',
        'presence' => '1',
        'sortorderid' => '6',
    ),
    array(
        'dwelling_type' => '2 Bedroom House',
        'presence' => '1',
        'sortorderid' => '7',
    ),
    array(
        'dwelling_type' => '3 Bedroom House',
        'presence' => '1',
        'sortorderid' => '8',
    ),
    array(
        'dwelling_type' => '3 Bedroom Apt.',
        'presence' => '1',
        'sortorderid' => '9',
    ),
    array(
        'dwelling_type' => '4+ Bedroom House',
        'presence' => '1',
        'sortorderid' => '10',
    ),
);

// Grab all the results from tiger_dwelling_type to we can renumber the sort order


for ($i=0;$i<sizeof($data);$i++) {
    $sql = 'INSERT INTO vtiger_dwelling_type
          (dwelling_typeid, dwelling_type, sortorderid, presence)
          VALUES
          (?, ?, ?, ?)';
    $values = array($seqId, $data[$i]['dwelling_type'], ($i+1), $data[$i]['presence']);

    $updated = $db->pquery($sql, $values);
    if ($db->getAffectedRowCount($updated)) {
        echo $data[$i]['dwelling_type']. ' has successfully been added!<br>';
    } else {
        echo '<span style="color:#f00">Failed to add '.$data[$i]['dwelling_type']. ' as an option in
        vtiger_dwelling_type </span><br>';
    }
}

$sql = 'UPDATE vtiger_dwelling_type_seq SET id = ?';
$result = $db->pquery($sql, array(count($data)));
if ($db->getAffectedRowCount($result)) {
    echo '<h5>Dwelling Type Seq Table Updated</h5>';
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";