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


//OT 16414 - Make Document Title field non mandatory

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

print "<h2>Begin modifications to Documents Field notes_title </h2>\n";
RemoveMandatoryMTNM('Documents', 'notes_title');
print "<h2>END modifications to Documents Field: notes_title </h2>\n";

function RemoveMandatoryMTNM($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $notesTitle = Vtiger_Field::getInstance($fieldName, $module);
        if ($notesTitle) {
            $typeOfData = $notesTitle->typeofdata;
            $isMatch = preg_match('/~M/', $typeOfData);
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/~M/', '~O', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to NOT mandatory<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $notesTitle->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $notesTitle->id]);
                print "<br>$moduleName $fieldName is converted to NOT mandatory<br>\n";
            } else {
                print "<br>$moduleName $fieldName is Already not mandatory<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";