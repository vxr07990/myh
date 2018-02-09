<?php

class LocalDispatch_List_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        global $adb;


//     $this->db->database->setFetchMode(DB_FETCHMODE_ASSOC);
//     print_r($this->db->database);
//
//     $result = $this->db->query('SELECT * FROM vtiger_project');
//     $result = $this->db->run_query_allrecords("SELECT * FROM vtiger_project");
//     print_r($result);
//
//     foreach($result as $res){
//       echo $res['projectid'] . '<br />';
//       echo $res['projectname'] . '<br />';
//     }





        $viewer = $this->getViewer($request);
        $viewer->view('List.tpl', $request->getModule());
    }

    public function getCrewMembers()
    {
        return $this->db->run_query_allrecords("SELECT * FROM vtiger_project");
    }
}
