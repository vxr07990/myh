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

$moduleName = 'Employees';
$fieldNames = ['employee_dlclass'];
$targetUiType = 16;
$picklistArray = ['Class A', 'Class B', 'Non CDL'];

print "<h2>Begin change to picklist</h2>\n";
foreach ($fieldNames as $fieldName) {
    ChangeToPicklistMLTAP($moduleName, $fieldName, $targetUiType);
    updatePicklistValuesMLTAP($fieldName, $moduleName, $picklistArray);
    convertToPicklistValuesMLTAP($fieldName, $moduleName, $picklistArray);
}
print "<h2>Script to modify fields completed.</h2>\n";

function ChangeToPicklistMLTAP($moduleName, $fieldName, $targetUiType, $picklistArray)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $changingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($changingField) {
            $uiType = $changingField->uitype;
            if ($uiType != $targetUiType) {
                print "<br>$moduleName $fieldName needs converting to picklist<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `uitype` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                $db->pquery($stmt, [$targetUiType, $changingField->id]);
                $changingField = Vtiger_Field::getInstance($fieldName, $module);
                $changingField->setPicklistValues($picklistArray);
                print "$moduleName $fieldName is converted to a picklist<br>\n";
            } else {
                print "$moduleName $fieldName is already a picklist. <br/>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}




function updatePicklistValuesMLTAP($fieldName, $moduleName, $picklistArray)
{
    $db = PearDatabase::getInstance();
    $module = Vtiger_Module::getInstance($moduleName);
    $field     = Vtiger_Field::getInstance($fieldName, $module);
    if($field) {
        $tableName = 'vtiger_'.$fieldName;
        //    $keyField = $fieldName.'id';
        $sql = "TRUNCATE TABLE `$tableName`";
        $db->pquery($sql, []);
        $field->setPicklistValues($picklistArray);
        echo "<p>Updated $fieldName picklist.</p>";
    }
}

function convertToPicklistValuesMLTAP($fieldName, $moduleName, $picklistArray){
    $db = PearDatabase::getInstance();
    $picklistArray[] = '';
    $tableNameEscaped = $db->escapeDbName('vtiger_'.strtolower($moduleName));
    $fieldNameEscaped = $db->escapeDbName($fieldName);
    $sql = "SELECT COUNT($fieldNameEscaped), $fieldNameEscaped from $tableNameEscaped WHERE $fieldNameEscaped NOT IN (".generateQuestionMarks($picklistArray).") 
    GROUP BY ".$db->escapeDbName($fieldName);
    $result = $db->pquery($sql,[$picklistArray] );
    if($result && $result->fields[$fieldName] != NULL){
        echo"<p>Incompatible values found. Updating $tableName for compatibility.";
        $classAReplacementVals = ['A', 'CDL - A', 'CDL A', 'Driver - A', 'ClassA', 'CLASS A', 'CDL Class A', 'CDL-A', 'CDL-CLASSA'];
        $classBReplacementVals = ['CDL B', 'B', 'CDL-B', 'ClassB'];
        $nonCDLReplacementVals = ['Class C', 'Class D', 'Class D, F Endorseme', 'C', 'd', 'E', 'ID', 'N', 'Non-CDL', 'REG-D', 'Regular'];
        $blankReplacementVals = ['05-25-15', 'CDL', 'CM', 'CP', 'State ID'];
        $replacementArrays = [$classAReplacementVals, $classBReplacementVals, $nonCDLReplacementVals, $blankReplacementVals];
        $i=0;
        foreach($picklistArray as $picklistVal){
            $sql2 = "UPDATE $tableNameEscaped SET $fieldNameEscaped = ? WHERE $fieldNameEscaped IN (".generateQuestionMarks($replacementArrays[$i]).")";
            $result2 = $db->pquery($sql2, [$picklistVal, $replacementArrays[$i]]);
            if (!$result2){
                echo "<br/>No changes made for $picklistVal<br/>";
            }
            $i++;
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";