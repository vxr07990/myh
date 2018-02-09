<?php

class Orders_ColorSettingsSave_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        $jsonPercentage = $request->get('data_colores');
        $assigned_color = ($request->get('assigned_color') == '') ? 'inherit' : $request->get('assigned_color');
        $apu_color = ($request->get('apu_color') == '') ? 'inherit' : $request->get('apu_color');
        $short_haul_color = ($request->get('short_haul_color') == '') ? 'inherit' : $request->get('short_haul_color');
        $overflow = ($request->get('overflow') == '') ? 'inherit' : $request->get('overflow');

        $db->query("DELETE FROM vtiger_colorsettings");
        $query = $db->pquery("INSERT INTO vtiger_colorsettings(id, value, color) VALUES (?,?,?)", array(1, 'assigned', $assigned_color));
        $query = $db->pquery("INSERT INTO vtiger_colorsettings(id, value, color) VALUES (?,?,?)", array(2, 'apu', $apu_color));
        $query = $db->pquery("INSERT INTO vtiger_colorsettings(id, value, color) VALUES (?,?,?)", array(3, 'short_haul', $short_haul_color));
        $query = $db->pquery("INSERT INTO vtiger_colorsettings(id, value, color) VALUES (?,?,?)", array(4, 'overflow', $overflow));

        $j = 5;
        
        for ($i = 0; $i < count($jsonPercentage); $i++) {
            $query = $db->pquery("INSERT INTO vtiger_colorsettings(id, value, color) VALUES (?,?,?)", array($j, $jsonPercentage[$i]['days'], $jsonPercentage[$i]['color']));
            $j++;
        }

        if (Vtiger_Session::has('ldd_backgrondcolors')) {
            Vtiger_Session::set('ldd_backgrondcolors', ''); //Reset session value to force the new query
        }
        
        $msg = new Vtiger_Response();
        $msg->setResult("Ok");
        $msg->emit();
    }
}
