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
 * Purpose is to remove existing excessive unique keys added to vtiger_users table on user_name column.
 *
 * show indexes from vtiger_users where column_name='user_name' and not Non_unique;
 * show create table vtiger_users;
 */

//
//commenting out until we decide if we want to do this
//
////This will remove existing but excessive unique keys.
//$stmt = 'SHOW INDEXES FROM `vtiger_users` WHERE `Column_name`=? AND NOT `Non_unique`';
//if ($res = $db->pquery($stmt, ['user_name'])) {
//    while ($value = $res->fetchRow()) {
//        //make sure a Key_name is a thing.
//        if (array_key_exists('Key_name', $value)) {
//            list($user, $name, $number) = explode('_', $value['Key_name']);
//            //because _N is tacked on to the excessive ones, if it has a number drop it.
//            if ($number && $number > 0) {
//                $dropStmt = 'DROP INDEX `'.$value['Key_name'].'` ON `vtiger_users`';
//                $db->pquery($dropStmt);
//            }
//        }
//    }
//} else {
//	print "Failed to determine if any unique key exists.  Probably errors out and this doesn't display.<br />\n";
//}
;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";