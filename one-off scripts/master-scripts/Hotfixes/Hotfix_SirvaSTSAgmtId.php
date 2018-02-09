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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "Begin Hotfix Sirva STS AgmtId & SubAgmtNbr";

$moduleOpps = Vtiger_Module::getInstance('Opportunities');

$stsBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_REGISTERSTS', $moduleOpps);

$agmtId = Vtiger_Field::getInstance('agmt_id', $moduleOpps);
$subAgmtNbr = Vtiger_Field::getInstance('subagmt_nbr', $moduleOpps);

$db = PearDatabase::getInstance();
echo "<br>changing agmt_id and sub_agmt_nbr to UI type 10";
//$db->pquery("UPDATE `vtiger_field` SET uitype = 10 WHERE fieldlabel = 'LBL_OPPORTUNITY_AGMTID' OR fieldlabel = 'LBL_OPPORTUNITY_SUBAGMTNUMBER'", []);
echo "<br>Completed change";
echo "<br>Hotfix Completed<br>";
/*
if($agmtId){
    echo "<br>agmt id found<br>";
    $agmtId->setRelatedModules(['Contracts']);
}else{
    echo "<br>no agmt id found<br>";
}


if($subAgmtNbr){
    echo "<br>sub agmt nbr found<br>";
    $subAgmtNbr->setRelatedModules(['Contracts']);
} else{
    echo "<br>no sub agmt nbr found<br>";
}*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";