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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$revert = true;
$modules = array('Calendar', 'Emails', 'Events');

foreach ($modules as $mod) {
    $module = Vtiger_Module::getInstance($mod);
    if ($module) {
        //print "HERE1: " . print_r($module, 1) . "\n<br />";
        //print "HERE1: " . $module->id . "\n<br />";
        print "<h2>Working with module: $mod </h2><br>";

        $field = Vtiger_Field::getInstance('date_start', $module);
        //print "HERE2: " . print_r($field, 1) . "\n<br />";
        //print "HERE2: " . $field->typeofdata . "\n<br />";
        if ($field) {
            print "<li>Checking date_start for $mod </li><br>";

            $search = '/^DT~/i';
            $replace = 'D~';
            $uitype = 5;

            if ($revert) {
                $search = '/^D~/i';
                $replace = 'DT~';
                $uitype = 6;
            }

            if (preg_match($search, $field->typeofdata)) {
                $typeOfData = preg_replace($search, $replace, $field->typeofdata);
                print "<li>Updating date_start for $mod From: " . $field->typeofdata . " TO: " . $typeOfData . "</li><br>";
                $stmt = "UPDATE `vtiger_field` SET "
                    . " `typeofdata` = '" . Vtiger_Utils::SQLEscape($typeOfData) . "' "
                    . ", `uitype` = '" . Vtiger_Utils::SQLEscape($uitype) . "' "
                    . " WHERE `fieldid` = '" . Vtiger_Utils::SQLEscape($field->id) . "' LIMIT 1";
                //print "<li>stmt: ". $stmt . ";</li><br>";
                Vtiger_Utils::ExecuteQuery($stmt);
                print "<li> Done Updating date_start for $mod</li><br>";
            } else {
                print "<li>No change required for $mod </li><br>";
            }
        } else {
            print "<li>The field 'date_start' DOES NOT exist in $mod Module?</li><br>";
        }
    } else {
        print "<li>There is no $mod Module?</li><br>";
    }
}
