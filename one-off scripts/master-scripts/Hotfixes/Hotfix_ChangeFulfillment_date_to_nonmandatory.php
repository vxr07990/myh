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


/*
 * The goal is to make the closingdate field non-mandatory and to not appear on the quick create.
 *
 * based on Hotfix_SirvaOppsFieldsMod.php
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

print "<h2>Begin modifications to opportunity field: closingdate. </h2>\n";
RemoveMandatoryDate('Potentials', 'closingdate');
RemoveMandatoryDate('Opportunities', 'closingdate');
print "<h2>END modifications to opportunity field: closingdate. </h2>\n";

function RemoveMandatoryDate($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $closingDate = Vtiger_Field::getInstance($fieldName, $module);
        if ($closingDate) {
            $typeOfData = $closingDate->typeofdata;
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
                print "$typeOfData, " . $closingDate->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $closingDate->id]);
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