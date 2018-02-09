<?php

class LocalDispatch_Dispatch_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        //         global $adb;
// $php_variable = 'sasasassaassaa';
// $smarty->assign("[SMARTY_VARIABLE]",[$php_variable]);

// var_dump($smarty);

//         $adb->database->setFetchMode(DB_FETCHMODE_ASSOC);
//         print_r($this->db->database);

//         $result = $adb->query('SELECT * FROM vtiger_project');
//         $result = $adb->run_query_allrecords("SELECT * FROM vtiger_vehicles");
//         print_r($result);

//         foreach($result as $res){
//           echo $res['projectid'] . '<br />';
//           echo $res['projectname'] . '<br />';
//         }

        $viewer = $this->getViewer($request);
        $viewer->view('Dispatch.tpl', $request->getModule());
    }
}
