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

require_once 'includes/main/WebUI.php';
// require_once 'include/Webservices/Create.php';
// require_once 'modules/Users/Users.php';
// echo "Preparing to create services<br>\n";

$db = PearDatabase::getInstance();

if (!$db) {
    print "NO DB: SKIPPING ".__FILE__."<br/>\n";
    return;
}
//Adds this field if it doesn't exist, if it does.. well it will just throw an error.. which we will ignore, obviously

Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_users` ADD `profile_image_id` INT NULL");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_users` ADD `profile_image_name` VARCHAR(255) NULL");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_users` ADD `profile_image_path` TEXT NULL");

$sql = "SELECT * FROM vtiger_attachments
LEFT JOIN vtiger_salesmanattachmentsrel ON vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid = vtiger_crmentity.crmid AND vtiger_crmentity.setype like 'Users%'";

$result = $db->query($sql);

if(!$db->num_rows($result)) {
    print "NO USERS HAVE PROFILE IMAGES, SKIPPING ".__FILE__."<br/>\n";
    return;
}

//This step will update the new fields with the old data, so people do not have to re-upload their images
foreach($result as $row) {
    $docid  = $row['attachmentsid'];
    $name   = $row['name'];
    $path   = $row['path'];
    $id     = $row['smid'];

    $sql = "UPDATE vtiger_users SET profile_image_id = ?, profile_image_name = ?, profile_image_path = ? WHERE id = ?";
    $params = [$docid,$name,$path,$id];
    $db->pquery($sql, $params);

    //Now for the removal of the old record.
    $sql = "UPDATE vtiger_salesmanattachmentsrel SET smid = 0 where smid = ?";
    $db->pquery($sql, $row['smid']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";