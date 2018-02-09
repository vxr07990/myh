<?php
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

$db = PearDatabase::getInstance();

$sql = "SELECT domain FROM Users WHERE username=?";
$params[] = $_GET['username'];

$result = $db->pquery($sql, $params);

$row = $result->fetchRow();

if ($row == null) {
    die();
}

$data['success'] = 1;
$data['result'] = $row[0];

echo json_encode($data);
