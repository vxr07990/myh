<?php
if (function_exists("call_ms_function_ver")) {
    $version = '1.2';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "Changing uitype of street field for opportunities from 21 to 1...<br/>\n";

// Both of these yield
$result = $db->query("SELECT fieldid, uitype FROM vtiger_field WHERE fieldname='street' AND tablename='vtiger_potential'");
while($data = $result->fetchRow()) {
    if($data['uitype'] == 21) {
        echo "Switching field ".$data['fieldid']." from uitype 21 to 1.<br/>\n";
        $res = $db->pquery("UPDATE vtiger_field SET uitype=1 WHERE fieldid=?", [$data['fieldid']]);

        // Just throw a notice out if something failed.
        if(!$res) {
            echo "Unable to switch field ".$data['fieldid']." from uitype 21 to 1. Check MySQL fail log.<br/>\n";
        }
    }else{
        echo "Field ".$data['fieldid']." is not uitype 21, no switch needed.<br/>\n";
    }
}
echo "Done.<br/>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";