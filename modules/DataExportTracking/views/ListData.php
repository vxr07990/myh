<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class DataExportTracking_ListData_View extends Vtiger_Index_View {
    function __construct() {
        parent::__construct();
    }
    
    function process (Vtiger_Request $request) {
        $viewer = $this->getViewer ($request);
        $adb = PearDatabase::getInstance();
        $moduleName = 'DataExportTracking';
        //Delete record
        $mode =  $request ->get('mode');
        if(!empty($mode) && $mode == 'delete'){
            $log_id = $request ->get('log_id');
            $sql="SELECT id,download FROM vte_data_export_tracking_log
              WHERE vte_data_export_tracking_log.`id` = ? LIMIT 0,1";
            $rs = $adb->pquery($sql,array($log_id));
            $list_log = $adb ->num_rows($rs);
            if( $list_log > 0){
                while($row=$adb->fetch_array($rs)) {
                  $log_file = $row['download'];
                  global $root_directory;
                  unlink($root_directory.$log_file);
                }
                $sql="DELETE FROM vte_data_export_tracking_log
                  WHERE vte_data_export_tracking_log.id = ?";
                $rs = $adb->pquery($sql,array($log_id));
                }
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pageNumber = $request ->get('page');
        if(empty ($pageNumber)){
            $pageNumber = '1';
        }
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('viewid', $request->get('viewname'));
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $limitQuery = " LIMIT $startIndex,".($pageLimit+1);

        $action_types = array(
            1 => 'Listview Exports',
            2 => 'Report Exports',
            3 => 'Scheduled Reports',
            4 => 'Copy Records',
        );

        $sql="SELECT l.id,l.type,l.time,l.link,l.size,l.download, CONCAT(u.first_name,u.last_name) as user,u.id as user_id FROM vte_data_export_tracking_log l
              INNER JOIN vtiger_users u ON l.`user` = u.id";

        $rs = $adb->pquery($sql,array());

        $list_count = $adb ->num_rows($rs);
        $pageCount = ceil((int) $list_count / (int) $pageLimit);

        $list_log = array();
        $sql="SELECT l.id,l.type,l.time,l.link,l.size,l.download, CONCAT(u.first_name,u.last_name) as user,u.id as user_id FROM vte_data_export_tracking_log l
              INNER JOIN vtiger_users u ON l.`user` = u.id
              ORDER BY time DESC " . $limitQuery;

        $rs = $adb->pquery($sql,array());
        $list_count = $adb ->num_rows($rs);
        if( $list_count > 0){
            while($row=$adb->fetch_array($rs)) {
                $user = '<a href="index.php?module=Users&parent=Settings&view=Detail&record='.$row['user_id'].'" target="_blank">'.$row['user'].'</a>';
                $link = '<a href="'.$row['link'].'">'.vtranslate('View').'</a>';
                $download = '<a href="index.php?module=DataExportTracking&action=DownloadFile&download_file='.$row['download'].'">'.vtranslate('Download').'</a>';
                $list_log[]=array('id' => $row['id'], 'type' => $action_types[$row['type']],'time' => $row['time'],'link' =>$link,'size' => $row['size'],'download' => $download,'user' => $user);
            }
        }

        $pagingModel->calculatePageRange($list_log);
        if($pageCount == 0){
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('PAGE_NUMBER', $pageNumber);
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('LISTVIEW_ENTRIES_COUNT',$list_count);
        $viewer->assign('PAGING_MODEL',$pagingModel);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('LISTVIEW_ENTRIES', $list_log);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('ListData.tpl', $moduleName);
    }
    //End Class
}