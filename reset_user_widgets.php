<?php

require_once 'include/Webservices/Relation.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'includes/main/WebUI.php';
require_once 'vendor/autoload.php';
require_once 'config/database.php';

$request = new Vtiger_Request($_REQUEST);

$username = $request->get('username');
$accessKey = $request->get('accessKey');

if ($username && $username != '') {
    $db = PearDatabase::getInstance();

    $sql = "SELECT accesskey FROM `vtiger_users` WHERE id=1";
    $result = $db->query($sql);
    if ($accessKey != $result->fields['accesskey']) {
        echo "<h4 style='color:red'>Invalid Access Key provided.</h4>";
    } else {
        $sql    = "SELECT id FROM `vtiger_users` WHERE user_name=?";
        $result = $db->pquery($sql, [$username]);
        $row    = $result->fetchRow();
        if (!$row) {
            echo '<h4>No matching user found</h4>';
        } else {
            $userId = $row['id'];
            $sql    = "DELETE FROM `vtiger_module_dashboard_widgets` WHERE userid=?";
            $db->pquery($sql, [$userId]);
            echo "<h4>Widgets reset for user: $username</h4>";
        }
    }
}

?>

<h1>User Widget Reset Utility</h1>
<form action="" method="post">
    <label style="display:inline-block; width:100px">Access Key: </label>
    <input name="accessKey" type="text" /><br />&nbsp;<br />
    <label style="display:inline-block; width:100px">Username: </label>
    <input name="username" type="text" /><br />&nbsp;<br />
    <input type="submit" />
</form>
