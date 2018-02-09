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


//OT 2570 - Adding move roles module to Opportunities

echo "<br> begin Set guestModule MoveRoles in Opportunities";
$guestModule = Vtiger_Module::getInstance('MoveRoles');
if ($guestModule) {
    //    $field0 = Vtiger_Field::getInstance('moveroles_employees', $guestModule);
//    if ($field0) {
//        echo "<br>Setting $field0 related modules";
//        $field0->setRelatedModules(['Employees']);
//        echo "<br>set $field0 related modules";
//    };
    $field1 = Vtiger_Field::getInstance('moveroles_orders', $guestModule);
    if ($field1) {
        //echo "<br>Setting $field1->name related modules";
        $field1->setRelatedModules(['Opportunities', 'Orders']);
        echo "<br>set related modules";
    };
} else {
    echo "$guestModule not found";
}
$opportunitiesInstance = Vtiger_Module::getInstance('Opportunities');
$opportunitiesInstance->setGuestBlocks('MoveRoles', ['LBL_MOVEROLES_INFORMATION']);

echo "<br> Hiding original Sales Person field in Opportunities <br/>";

$hideFields = ['sales_person'];

hideFields_SGMR($hideFields, $opportunitiesInstance);

function hideFields_SGMR($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 1) {
                    echo "Updating $field_name to be a have presence = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
}


echo "<br> end Set guestModule MoveRoles in Opportunities";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";