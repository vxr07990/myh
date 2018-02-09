<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();

if (!$db) {
    print "NO DB: SKIPPING ".__FILE__."<br/>\n";
    return;
}

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$extrastop_seg = 0;

$origdest =  array('Extra Pickup 1' => 'Origin',
                        'Extra Pickup 2' => 'Origin',
                        'Extra Pickup 3' => 'Origin',
                        'Extra Pickup 4' => 'Origin',
                        'Extra Pickup 5' => 'Origin',
                        'Extra Delivery 1' => 'Destination',
                        'Extra Delivery 2' => 'Destination',
                        'Extra Delivery 3' => 'Destination',
                        'Extra Delivery 4' => 'Destination',
                        'Extra Delivery 5' => 'Destination',
                        'O - SIT' => 'Origin',
                        'D - SIT' => 'Destination',
                        'Self Stg PU' => 'Origin',
                        'Perm Dlv' => 'Destination',
                        'Perm PU' => 'Origin',
                        'Self Stg Dlv' => 'Destination'
);
$table = 'vtiger_extrastops_type_origdest';
$sql = 'SHOW TABLES LIKE \''.$table.'\';';
$result = $adb->query($sql);
if($db->num_rows($result) > 0) {
    print "TABLE EXISTS, TRUNCATING.<br/>\n";
    $sql = 'TRUNCATE TABLE `'.$table.'`;';
    $result = $adb->query($sql);
    if(!$result) {
        print "ERROR TRUNCATING TABLE, EXITTING.<br/>\n";
        return;
    }
}else{
    print "TABLE DOES NOT EXIST, CREATING.<br/>\n";
    $sql = "CREATE TABLE `".$table."` ( `type_id` INT(10) NOT NULL , `origdest` ENUM('Origin','Destination') NOT NULL , PRIMARY KEY (`type_id`)) ENGINE = InnoDB;";
    $result = $adb->query($sql);
    if(!$result) {
        print "ERROR CREATING TABLE, EXITTING.<br/>\n";
        return;
    }
}

foreach ($origdest as $key => $value) {
    $sql = 'SELECT * FROM `vtiger_extrastops_type` WHERE `extrastops_type`=?';
    $result = $adb->pquery($sql, [$key]);
    $stops_type = $adb->fetch_array($result);

    $sql = "SELECT * FROM `".$table."` WHERE type_id=".$stops_type['extrastops_typeid'];
    $result = $adb->query($sql);
    if(!$db->num_rows($result)) {
        $result = $adb->pquery("INSERT INTO `".$table."` (type_id, origdest) VALUES(?,?)", [$stops_type['extrastops_typeid'], $value]);
        if(!$result) {
            print "ERROR ADDING ".$key." TO TABLE.<br/>\n";
        }
    }else{
        print $stops_type['extrastops_typeid']." ALREADY EXISTS IN TABLE.<br/>\n";
    }


    $extrastop_seg++;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";