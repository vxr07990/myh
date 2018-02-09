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

echo "Creating new Capacity Calendar Settings Table with User Id";

if (!Vtiger_Utils::CheckTable('vtiger_calendar_settings')) {
    echo "<li>Creating vtiger_calendar_settings </li><br>";
    $result0= $db->pquery("CREATE TABLE `vtiger_calendar_settings` (
                        `userid` int(11) NOT NULL,
                        `percentage_1` varchar(8) NOT NULL,
                        `color_1` varchar(8) NOT NULL DEFAULT '#FFFFFF',
                        `percentage_2` varchar(8) NOT NULL,
                        `color_2` varchar(8) NOT NULL DEFAULT '#FFFFFF',
                        `percentage_3` varchar(8) NOT NULL,
                        `color_3` varchar(8) NOT NULL DEFAULT '#FFFFFF',
                        `saturday_work_day` varchar(3),
                        `sunday_work_day` varchar(3),
                        PRIMARY KEY (`userid`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}


$old_settings = array();
$db = PearDatabase::getInstance();
$sql = "SELECT * FROM vtiger_calsettings_colors";
$result = $db->pquery($sql);
if($db->num_rows($result) > 0){
    $i = 1;
    while($row = $db->fetchByAssoc($result)){
        $old_settings['percentage_'.$i] = $row['percentage'];
        $old_settings['color_'.$i] = $row['color'];
        $i++;
    }
}
$sql2 = "SELECT * FROM vtiger_calsettings_days";
$result2 = $db->pquery($sql2);
if($db->num_rows($result2) > 0){
    while($row = $db->fetchByAssoc($result2)){
        $old_settings['saturday_work_day'] = $row['saturday'];
        $old_settings['sunday_work_day'] = $row['sunday'];
    }
}

$sql = "INSERT INTO vtiger_calendar_settings VALUES (?,?,?,?,?,?,?,?,?)";
$params = array(1,$old_settings['percentage_1'],$old_settings['color_1'],$old_settings['percentage_2'],$old_settings['color_2'],$old_settings['percentage_3'],$old_settings['color_3'],$old_settings['saturday_work_day'],$old_settings['sunday_work_day']);
$result = $db->pquery($sql,$params);

echo "OK!";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";