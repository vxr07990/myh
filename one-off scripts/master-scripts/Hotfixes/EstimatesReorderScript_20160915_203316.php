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

//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/


$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$block197 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleEstimates);
$field1130 = Vtiger_Field::getInstance('acc_shuttle_origin_weight', $moduleEstimates);
$field1132 = Vtiger_Field::getInstance('acc_shuttle_origin_applied', $moduleEstimates);
$field1134 = Vtiger_Field::getInstance('acc_shuttle_origin_ot', $moduleEstimates);
$field1136 = Vtiger_Field::getInstance('acc_shuttle_origin_over25', $moduleEstimates);
$field1138 = Vtiger_Field::getInstance('acc_shuttle_origin_miles', $moduleEstimates);
$field1140 = Vtiger_Field::getInstance('acc_ot_origin_weight', $moduleEstimates);
$field1142 = Vtiger_Field::getInstance('acc_ot_origin_applied', $moduleEstimates);
$field1144 = Vtiger_Field::getInstance('acc_selfstg_origin_weight', $moduleEstimates);
$field1146 = Vtiger_Field::getInstance('acc_selfstg_origin_applied', $moduleEstimates);
$field1148 = Vtiger_Field::getInstance('acc_selfstg_origin_ot', $moduleEstimates);
$field1150 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $moduleEstimates);
$field1977 = Vtiger_Field::getInstance('apply_exlabor_rate_origin', $moduleEstimates);
$field1978 = Vtiger_Field::getInstance('exlabor_rate_origin', $moduleEstimates);
$field1979 = Vtiger_Field::getInstance('exlabor_flat_origin', $moduleEstimates);
$field1152 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $moduleEstimates);
$field1980 = Vtiger_Field::getInstance('apply_exlabor_ot_rate_origin', $moduleEstimates);
$field1982 = Vtiger_Field::getInstance('exlabor_ot_flat_origin', $moduleEstimates);
$field1981 = Vtiger_Field::getInstance('exlabor_ot_rate_origin', $moduleEstimates);
$field1154 = Vtiger_Field::getInstance('acc_wait_origin_hours', $moduleEstimates);
$field1156 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $moduleEstimates);
$field1739 = Vtiger_Field::getInstance('bulky_article_changes', $moduleEstimates);
$field1797 = Vtiger_Field::getInstance('accesorial_ot_unloading', $moduleEstimates);
$field1805 = Vtiger_Field::getInstance('accesorial_expedited_service', $moduleEstimates);
$field2294 = Vtiger_Field::getInstance('accessorial_space_reserve_bool', $moduleEstimates);
$field2296 = Vtiger_Field::getInstance('accesorial_ot_unpacking', $moduleEstimates);
$field2608 = Vtiger_Field::getInstance('acc_day_certain_pickup', $moduleEstimates);
$field1131 = Vtiger_Field::getInstance('acc_shuttle_dest_weight', $moduleEstimates);
$field1133 = Vtiger_Field::getInstance('acc_shuttle_dest_applied', $moduleEstimates);
$field1135 = Vtiger_Field::getInstance('acc_shuttle_dest_ot', $moduleEstimates);
$field1137 = Vtiger_Field::getInstance('acc_shuttle_dest_over25', $moduleEstimates);
$field1139 = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $moduleEstimates);
$field1141 = Vtiger_Field::getInstance('acc_ot_dest_weight', $moduleEstimates);
$field1143 = Vtiger_Field::getInstance('acc_ot_dest_applied', $moduleEstimates);
$field1145 = Vtiger_Field::getInstance('acc_selfstg_dest_weight', $moduleEstimates);
$field1147 = Vtiger_Field::getInstance('acc_selfstg_dest_applied', $moduleEstimates);
$field1149 = Vtiger_Field::getInstance('acc_selfstg_dest_ot', $moduleEstimates);
$field1151 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $moduleEstimates);
$field1983 = Vtiger_Field::getInstance('apply_exlabor_rate_dest', $moduleEstimates);
$field1984 = Vtiger_Field::getInstance('exlabor_rate_dest', $moduleEstimates);
$field1985 = Vtiger_Field::getInstance('exlabor_flat_dest', $moduleEstimates);
$field1153 = Vtiger_Field::getInstance('acc_exlabor_ot_dest_hours', $moduleEstimates);
$field1986 = Vtiger_Field::getInstance('apply_exlabor_ot_rate_dest', $moduleEstimates);
$field1988 = Vtiger_Field::getInstance('exlabor_ot_flat_dest', $moduleEstimates);
$field1987 = Vtiger_Field::getInstance('exlabor_ot_rate_dest', $moduleEstimates);
$field1155 = Vtiger_Field::getInstance('acc_wait_dest_hours', $moduleEstimates);
$field1157 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $moduleEstimates);
$field1795 = Vtiger_Field::getInstance('accesorial_ot_loading', $moduleEstimates);
$field1799 = Vtiger_Field::getInstance('accesorial_fuel_surcharge', $moduleEstimates);
$field2293 = Vtiger_Field::getInstance('accesorial_exclusive_vehicle', $moduleEstimates);
$field2295 = Vtiger_Field::getInstance('accesorial_ot_packing', $moduleEstimates);
$field2115 = Vtiger_Field::getInstance('consumption_fuel', $moduleEstimates);
$field2609 = Vtiger_Field::getInstance('acc_day_certain_fee', $moduleEstimates);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1130->id." THEN 1 WHEN fieldid=".$field1132->id." THEN 3 WHEN fieldid=".$field1134->id." THEN 5 WHEN fieldid=".$field1136->id." THEN 7 WHEN fieldid=".$field1138->id." THEN 9 WHEN fieldid=".$field1140->id." THEN 11 WHEN fieldid=".$field1142->id." THEN 13 WHEN fieldid=".$field1144->id." THEN 15 WHEN fieldid=".$field1146->id." THEN 17 WHEN fieldid=".$field1148->id." THEN 19 WHEN fieldid=".$field1150->id." THEN 21 WHEN fieldid=".$field1977->id." THEN 23 WHEN fieldid=".$field1978->id." THEN 25 WHEN fieldid=".$field1979->id." THEN 27 WHEN fieldid=".$field1152->id." THEN 29 WHEN fieldid=".$field1980->id." THEN 31 WHEN fieldid=".$field1982->id." THEN 33 WHEN fieldid=".$field1981->id." THEN 35 WHEN fieldid=".$field1154->id." THEN 37 WHEN fieldid=".$field1156->id." THEN 39 WHEN fieldid=".$field1739->id." THEN 41 WHEN fieldid=".$field1797->id." THEN 43 WHEN fieldid=".$field1805->id." THEN 45 WHEN fieldid=".$field2294->id." THEN 47 WHEN fieldid=".$field2296->id." THEN 49 WHEN fieldid=".$field2608->id." THEN 51 WHEN fieldid=".$field1131->id." THEN 2 WHEN fieldid=".$field1133->id." THEN 4 WHEN fieldid=".$field1135->id." THEN 6 WHEN fieldid=".$field1137->id." THEN 8 WHEN fieldid=".$field1139->id." THEN 10 WHEN fieldid=".$field1141->id." THEN 12 WHEN fieldid=".$field1143->id." THEN 14 WHEN fieldid=".$field1145->id." THEN 16 WHEN fieldid=".$field1147->id." THEN 18 WHEN fieldid=".$field1149->id." THEN 20 WHEN fieldid=".$field1151->id." THEN 22 WHEN fieldid=".$field1983->id." THEN 24 WHEN fieldid=".$field1984->id." THEN 26 WHEN fieldid=".$field1985->id." THEN 28 WHEN fieldid=".$field1153->id." THEN 30 WHEN fieldid=".$field1986->id." THEN 32 WHEN fieldid=".$field1988->id." THEN 34 WHEN fieldid=".$field1987->id." THEN 36 WHEN fieldid=".$field1155->id." THEN 38 WHEN fieldid=".$field1157->id." THEN 40 WHEN fieldid=".$field1795->id." THEN 42 WHEN fieldid=".$field1799->id." THEN 44 WHEN fieldid=".$field2293->id." THEN 46 WHEN fieldid=".$field2295->id." THEN 48 WHEN fieldid=".$field2115->id." THEN 50 WHEN fieldid=".$field2609->id." THEN 52 END, block=CASE WHEN fieldid=".$field1130->id." THEN ".$block197->id." WHEN fieldid=".$field1132->id." THEN ".$block197->id." WHEN fieldid=".$field1134->id." THEN ".$block197->id." WHEN fieldid=".$field1136->id." THEN ".$block197->id." WHEN fieldid=".$field1138->id." THEN ".$block197->id." WHEN fieldid=".$field1140->id." THEN ".$block197->id." WHEN fieldid=".$field1142->id." THEN ".$block197->id." WHEN fieldid=".$field1144->id." THEN ".$block197->id." WHEN fieldid=".$field1146->id." THEN ".$block197->id." WHEN fieldid=".$field1148->id." THEN ".$block197->id." WHEN fieldid=".$field1150->id." THEN ".$block197->id." WHEN fieldid=".$field1977->id." THEN ".$block197->id." WHEN fieldid=".$field1978->id." THEN ".$block197->id." WHEN fieldid=".$field1979->id." THEN ".$block197->id." WHEN fieldid=".$field1152->id." THEN ".$block197->id." WHEN fieldid=".$field1980->id." THEN ".$block197->id." WHEN fieldid=".$field1982->id." THEN ".$block197->id." WHEN fieldid=".$field1981->id." THEN ".$block197->id." WHEN fieldid=".$field1154->id." THEN ".$block197->id." WHEN fieldid=".$field1156->id." THEN ".$block197->id." WHEN fieldid=".$field1739->id." THEN ".$block197->id." WHEN fieldid=".$field1797->id." THEN ".$block197->id." WHEN fieldid=".$field1805->id." THEN ".$block197->id." WHEN fieldid=".$field2294->id." THEN ".$block197->id." WHEN fieldid=".$field2296->id." THEN ".$block197->id." WHEN fieldid=".$field2608->id." THEN ".$block197->id." WHEN fieldid=".$field1131->id." THEN ".$block197->id." WHEN fieldid=".$field1133->id." THEN ".$block197->id." WHEN fieldid=".$field1135->id." THEN ".$block197->id." WHEN fieldid=".$field1137->id." THEN ".$block197->id." WHEN fieldid=".$field1139->id." THEN ".$block197->id." WHEN fieldid=".$field1141->id." THEN ".$block197->id." WHEN fieldid=".$field1143->id." THEN ".$block197->id." WHEN fieldid=".$field1145->id." THEN ".$block197->id." WHEN fieldid=".$field1147->id." THEN ".$block197->id." WHEN fieldid=".$field1149->id." THEN ".$block197->id." WHEN fieldid=".$field1151->id." THEN ".$block197->id." WHEN fieldid=".$field1983->id." THEN ".$block197->id." WHEN fieldid=".$field1984->id." THEN ".$block197->id." WHEN fieldid=".$field1985->id." THEN ".$block197->id." WHEN fieldid=".$field1153->id." THEN ".$block197->id." WHEN fieldid=".$field1986->id." THEN ".$block197->id." WHEN fieldid=".$field1988->id." THEN ".$block197->id." WHEN fieldid=".$field1987->id." THEN ".$block197->id." WHEN fieldid=".$field1155->id." THEN ".$block197->id." WHEN fieldid=".$field1157->id." THEN ".$block197->id." WHEN fieldid=".$field1795->id." THEN ".$block197->id." WHEN fieldid=".$field1799->id." THEN ".$block197->id." WHEN fieldid=".$field2293->id." THEN ".$block197->id." WHEN fieldid=".$field2295->id." THEN ".$block197->id." WHEN fieldid=".$field2115->id." THEN ".$block197->id." WHEN fieldid=".$field2609->id." THEN ".$block197->id." END WHERE fieldid IN (".$field1130->id.",".$field1132->id.",".$field1134->id.",".$field1136->id.",".$field1138->id.",".$field1140->id.",".$field1142->id.",".$field1144->id.",".$field1146->id.",".$field1148->id.",".$field1150->id.",".$field1977->id.",".$field1978->id.",".$field1979->id.",".$field1152->id.",".$field1980->id.",".$field1982->id.",".$field1981->id.",".$field1154->id.",".$field1156->id.",".$field1739->id.",".$field1797->id.",".$field1805->id.",".$field2294->id.",".$field2296->id.",".$field2608->id.",".$field1131->id.",".$field1133->id.",".$field1135->id.",".$field1137->id.",".$field1139->id.",".$field1141->id.",".$field1143->id.",".$field1145->id.",".$field1147->id.",".$field1149->id.",".$field1151->id.",".$field1983->id.",".$field1984->id.",".$field1985->id.",".$field1153->id.",".$field1986->id.",".$field1988->id.",".$field1987->id.",".$field1155->id.",".$field1157->id.",".$field1795->id.",".$field1799->id.",".$field2293->id.",".$field2295->id.",".$field2115->id.",".$field2609->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";