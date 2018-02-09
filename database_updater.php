<?php
    include_once('config.php');
    require_once('include/utils/utils.php');
    
    $db = PearDatabase::GetInstance();
    
    $db->query("UPDATE `vtiger_field` SET `presence`='1' WHERE `columnname`='rowheight' OR `columnname`='theme'");
    $db->query("UPDATE `vtiger_field` SET `defaultvalue`='bluelagoon' WHERE `columnname`='theme'");
