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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin hotfix sirva add contact address lead field mapping<br>";

$db = PearDatabase::getInstance();
$alreadyMapped = $db->pquery("SELECT * FROM `vtiger_convertleadmapping` WHERE contactfid = 96", [])->fetchRow();
if (!$alreadyMapped) {
    $leadMappingFields = [
        '752' => '96',
        '756' => '98',
        '758' => '100',
        '760' => '102',
        '762' => '104',
    ];

    foreach ($leadMappingFields as $leadFieldId => $contactFieldId) {
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_convertleadmapping` (leadfid, contactfid, editable) VALUES ($leadFieldId, $contactFieldId, 1)");
    }
} else {
    echo "<br>contact address fields already mapped<br>";
}

echo "<br>end hotfix sirva add contact address lead field mapping<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";