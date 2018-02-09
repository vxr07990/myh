<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//4861 Claims - Claims Representative Field change to uitype 10 related to Employees

echo "<br /> Updating claimssummary_representative field (ClaimsSummary) <br />";

$moduleInstance = Vtiger_Module::getInstance('ClaimsSummary');
$field3 = Vtiger_Field::getInstance('claimssummary_representative', $moduleInstance);

if($field3){
    $db->pquery('UPDATE vtiger_field SET uitype=10 WHERE fieldid=?', [$field3->id]);
    $field3->setRelatedModules(array('Employees'));
}


echo "<br /> Done!  <br />";