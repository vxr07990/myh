<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 9/21/2017
 * Time: 11:30 AM
 */
// Reorder Fields

$module = Vtiger_Module::getInstance('WFActivityCodes');

$orderOfFields = ['shortdescription', 'longdescription', 'wfactivitycodes_basecode', 'sync', 'agentid', 'assigned_user_id'];


$count = 0;
foreach ($orderOfFields as $val) {
    $field = Vtiger_Field::getInstance($val, $module);
    if ($field) {
        $count++;
        $params = [$count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        echo '<p>UPDATED '.$val.' to the sequence</p>';
    } else {
        echo '<p>'.$val.' Field doesn\'t exists</p>';
    }
}
