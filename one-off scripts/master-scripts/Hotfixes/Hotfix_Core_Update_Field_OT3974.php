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

echo 'Updating orders_otherstatus <br>\n';

$ordersInstance = Vtiger_Module_Model::getInstance('Orders');
if($ordersInstance){
    $field = Vtiger_Field::getInstance('orders_otherstatus',$ordersInstance);
    if($field){
        $db = PearDatabase::getInstance();
        $db->pquery('UPDATE vtiger_field SET presence = 2 WHERE fieldid = ?',array($field->id));
        echo 'orders_otherstatus updated OK<br>\n';
    }

}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";