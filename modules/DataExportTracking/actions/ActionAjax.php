<?php
/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class DataExportTracking_ActionAjax_Action extends Vtiger_Action_Controller {

    private   $action_types = array(
        1 => 'track_listview_exports',
        2 => 'track_report_exports',
        3 => 'track_scheduled_reports',
        4 => 'track_copy_records',
        );
    private $moduleInstance;
    private $focus;
    function checkPermission(Vtiger_Request $request) {
        return;
    }
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveDataExportTrackingLog');
        $this->exposeMethod('isTracking');
        $this->exposeMethod('saveExportReportTrackingLog');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    function saveDataExportTrackingLog(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        $response = new Vtiger_Response();
        $user = Users_Record_Model::getCurrentUserModel();
        $current_time_db = $db->formatDate(date("Y-m-d H:i:s"), true);
        $action_type = $request -> get('action_type');
        $file_size = 0;
        if($action_type == 1){
            $source_module = $request -> get('source_module');
            $view_name = $request -> get('viewname');
            $page = $request -> get('page');
            $search_params = $request -> get('search_params');
            $link = 'index.php?module='.$source_module.'&view=List&viewname='.$view_name.'&page='.$page.'&search_params='.json_encode($search_params);
            $selected_record_ids = $request ->get('selected_ids');
            //$text_to_save = implode(PHP_EOL,$selected_record_ids);
            $store_path = 'DataExportTracking';
            $txt_file_name = 'vte_' .  date("YmdHis") . '_' . rand(1,1000) . '.csv';
            $txt_file_path = 'storage/'.$store_path.'/'. date("Y").'/'.date("m");
            $text_to_save = $this ->ExportData($request,$this -> forceDir($txt_file_path).'/'.$txt_file_name);
        }
        elseif($action_type == 4){
            $link = 'index.php?'.$request -> get('link');
            $text_to_save = $request -> get('txt_clipboard');
            $store_path = 'DataCopyTracking';
            $txt_file_name = 'vte_' .  date("YmdHis") . '_' . rand(1,1000) . '.txt';
            $txt_file_path = 'storage/'.$store_path.'/'. date("Y").'/'.date("m");
            file_put_contents($this -> forceDir($txt_file_path).'/'.$txt_file_name,$text_to_save);
            $file_size = $this -> getFileSize($txt_file_path.'/'.$txt_file_name);
        }

        try{
            $sql="INSERT INTO vte_data_export_tracking_log(type,time,user,link,download,size) VALUES(?,?,?,?,?,?)";
            $rs = $db->pquery($sql,array($action_type,$current_time_db,$user->getId(),$link,$txt_file_path.'/'.$txt_file_name,$file_size));
            //Send Email
            $body_mail =  "User:" . $user ->getDisplayName().'<br>';
            $body_mail .= "Actions type:" . $this -> action_types[$action_type].'<br>';
            $body_mail .= "Time:" .$current_time_db.'<br>';
            $attach_file = $txt_file_path.'/'.$txt_file_name;
            $this -> sendEmail($attach_file,$body_mail);
            $response->setResult(array('success'=>1));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
            file_put_contents($this -> forceDir('logs').'/mail_error.txt',$e->getMessage());
        }
        $response->emit();
    }
    function saveExportReportTrackingLog(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        $response = new Vtiger_Response();
        $user = Users_Record_Model::getCurrentUserModel();

        $current_time_db = $db->formatDate(date("Y-m-d H:i:s"), true);

        $url_export = $request -> get('url_export');
        $url_export = $request -> get('url_export');
        $link = $request -> get('current_url');
        $export_format = (strpos($url_export,'mode=GetCSV') !== false)?'.csv':'.xls';

        $txt_file_name = 'vte_' .  date("YmdHis") . '_' . rand(1,1000) . $export_format;
        $txt_file_path = $this -> forceDir('storage/DataExportTracking/'. date("Y").'/'.date("m"));

        $this->save_export_report_file($txt_file_path.'/'.$txt_file_name,$request,$export_format);

        try{
            $file_size = $this -> getFileSize($txt_file_path.'/'.$txt_file_name);
            $sql="INSERT INTO vte_data_export_tracking_log(type,time,user,link,download,size) VALUES(?,?,?,?,?,?)";
            $rs = $db->pquery($sql,array('2',$current_time_db,$user->getId(),$link,$txt_file_path.'/'.$txt_file_name,$file_size));

            $response->setResult(array('success'=>1));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
    function isTracking(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $response = new Vtiger_Response();
        $action_type= $request -> get('action_type');
        $is_track =  0;
        $sql="SELECT * FROM vte_data_export_tracking LIMIT 0,1";
        $rs = $adb->pquery($sql,array());
        if($adb ->num_rows($rs) > 0){
            $action_type_value = $adb->query_result($rs, 0, $this -> action_types[$action_type]);
            if($action_type_value)  $is_track =  1;
        }
        $response->setResult(array('success'=>true,'is_track' =>  $is_track));
        $response->emit();
    }
    function forceDir($dir){
        if(!is_dir($dir)){
            $dir_p = explode('/',$dir);
            for($a = 1 ; $a <= count($dir_p) ; $a++){
                @mkdir(implode('/',array_slice($dir_p,0,$a)));
            }
        }
        return $dir;
    }
    function save_export_report_file($file_to_save = null,Vtiger_Request $request,$file_type) {
        $report_id = $request->get('report_id');

        $reportModel = Reports_Record_Model::getInstanceById($report_id);
        $reportModel->set('advancedFilter', $request->get('advanced_filter'));

        $advanceFilterSql = $reportModel -> getAdvancedFilterSQL();
        if($file_type == '.csv')   $reportModel ->reportRun->writeReportToCSVFile($file_to_save, $advanceFilterSql);
        else $reportModel ->reportRun->writeReportToExcelFile($file_to_save, $advanceFilterSql);
    }
    function getFileSize($fp)
    {
        return filesize($fp);
    }
    function sendEmail($attachfile,$body){
        $adb = PearDatabase::getInstance();
        $sql="SELECT notification_email FROM vte_data_export_tracking LIMIT 0,1";
        $rs = $adb->pquery($sql,array());
        if($adb ->num_rows($rs) > 0){
            $notification_email = $adb->query_result($rs, 0, 'notification_email');
            if(!empty($notification_email)){
                $subject  = 'Data export tracking';
                $this ->sendReportToEmail($notification_email,$subject,$body,$attachfile);

            }
        }
    }
    function sendReportToEmail($toEmail,$subject,$body, $attachfile='',$type='Schedule') {
        global $adb;
        //require("modules/Emails/class.smtp.php");
        //require("modules/Emails/class.phpmailer.php");
        $sql = "SELECT `server`, server_port, server_username, server_password, smtp_auth, from_email_field FROM vtiger_systems WHERE server_type = 'email'";
        $res = $adb->query($sql);
        if($adb->num_rows($res)>0) {
            $server= $adb->query_result($res,0,'server');
            $server_port = $adb->query_result($res,0,'server_port');
            $server_username = $adb->query_result($res,0,'server_username');
            $server_password = $adb->query_result($res,0,'server_password');
            $smtp_auth = $adb->query_result($res,0,'smtp_auth');
            $from_email_field = $adb->query_result($res,0,'from_email_field');

            // $fromUser = 'Administrator';
            $fromUser = 'Data Export Tracking Report';
            $mail = new PHPMailer();
            $mail->IsSMTP();                                      // set mailer to use SMTP
            $mail->Host = $server;  // specify main and backup server
            $mail->SMTPAuth = $smtp_auth;     // turn on SMTP authentication
            $mail->Username = $server_username;  // SMTP username
            $mail->Password = $server_password; // SMTP password


            $mail->From = $from_email_field;
            $mail->FromName = $fromUser;

            $toEmailArr=explode(',',$toEmail);
            foreach($toEmailArr as $emailAddress){
                $mail->AddAddress($emailAddress);
            }

            $mail->WordWrap = 50;                                 // set word wrap to 50 characters
            $mail->IsHTML(true);                                  // set email format to HTML

            $mail->Subject = $subject;
            $mail->Body    = $body;
            if(is_array($attachfile) && count($attachfile)>0){
                foreach($attachfile as $file){
                    $mail->AddAttachment($file);
                }
            }else{
                $mail->AddAttachment($attachfile);
            }
            $mail->Send();
        }
    }
    //Clone export data

    /**
     * Function exports the data based on the mode
     * @param Vtiger_Request $request
     */
    function ExportData(Vtiger_Request $request,$csv_file) {
        $db = PearDatabase::getInstance();
        $moduleName = $request->get('source_module');

        $this->moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        $this->moduleFieldInstances = $this->moduleInstance->getFields();
        $this->focus = CRMEntity::getInstance($moduleName);

        $query = $this->getExportQuery($request);
        $result = $db->pquery($query, array());

        $headers = array();
        //Query generator set this when generating the query
        if(!empty($this->accessibleFields)) {
            $accessiblePresenceValue = array(0,2);
            foreach($this->accessibleFields as $fieldName) {
                $fieldModel = $this->moduleFieldInstances[$fieldName];
                // Check added as querygenerator is not checking this for admin users
                $presence = $fieldModel->get('presence');
                if(in_array($presence, $accessiblePresenceValue)) {
                    $headers[] = $fieldModel->get('label');
                }
            }
        } else {
            foreach($this->moduleFieldInstances as $field) $headers[] = $field->get('label');
        }
        $translatedHeaders = array();
        foreach($headers as $header) $translatedHeaders[] = vtranslate(html_entity_decode($header, ENT_QUOTES), $moduleName);

        $entries = array();
        for($j=0; $j<$db->num_rows($result); $j++) {
            $entries[] = $this->sanitizeValues($db->fetchByAssoc($result, $j));
        }

        $this->output($csv_file, $translatedHeaders, $entries);
    }

    /**
     * Function that generates Export Query based on the mode
     * @param Vtiger_Request $request
     * @return <String> export query
     */
    function getExportQuery(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $mode = $request->getMode();
        $cvId = $request->get('viewname');
        $moduleName = $request->get('source_module');

        $queryGenerator = new QueryGenerator($moduleName, $currentUser);
        $queryGenerator->initForCustomViewById($cvId);
        $fieldInstances = $this->moduleFieldInstances;

        $accessiblePresenceValue = array(0,2);
        foreach($fieldInstances as $field) {
            // Check added as querygenerator is not checking this for admin users
            $presence = $field->get('presence');
            if(in_array($presence, $accessiblePresenceValue)) {
                $fields[] = $field->getName();
            }
        }
        $queryGenerator->setFields($fields);
        $query = $queryGenerator->getQuery();

        if(in_array($moduleName, getInventoryModules())){
            $query = $this->moduleInstance->getExportQuery($this->focus, $query);
        }

        $this->accessibleFields = $queryGenerator->getFields();

        switch($mode) {
            case 'ExportAllData' :	return $query;
                break;

            case 'ExportCurrentPage' :	$pagingModel = new Vtiger_Paging_Model();
                $limit = $pagingModel->getPageLimit();

                $currentPage = $request->get('page');
                if(empty($currentPage)) $currentPage = 1;

                $currentPageStart = ($currentPage - 1) * $limit;
                if ($currentPageStart < 0) $currentPageStart = 0;
                $query .= ' LIMIT '.$currentPageStart.','.$limit;

                return $query;
                break;

            case 'ExportSelectedRecords' :	$idList = $this->getRecordsListFromRequest($request);
                $baseTable = $this->moduleInstance->get('basetable');
                $baseTableColumnId = $this->moduleInstance->get('basetableid');
                if(!empty($idList)) {
                    if(!empty($baseTable) && !empty($baseTableColumnId)) {
                        $idList = implode(',' , $idList);
                        $query .= ' AND '.$baseTable.'.'.$baseTableColumnId.' IN ('.$idList.')';
                    }
                } else {
                    $query .= ' AND '.$baseTable.'.'.$baseTableColumnId.' NOT IN ('.implode(',',$request->get('excluded_ids')).')';
                }
                return $query;
                break;


            default :	return $query;
            break;
        }
    }

    /**
     * Function returns the export type - This can be extended to support different file exports
     * @param Vtiger_Request $request
     * @return <String>
     */
    function getExportContentType(Vtiger_Request $request) {
        $type = $request->get('export_type');
        if(empty($type)) {
            return 'text/csv';
        }
    }

    /**
     * Function that create the exported file
     * @param Vtiger_Request $request
     * @param <Array> $headers - output file header
     * @param <Array> $entries - outfput file data
     */
    function output($csv_file, $headers, $entries) {
        $file = fopen($csv_file,"w");
        $header = implode("\", \"", $headers);
        $header = "\"" .$header;
        $header .= "\"\r\n";
        fputcsv($file,explode(',',$header));
        foreach($entries as $row) {
            $line = implode("\",\"",$row);
            $line = "\"" .$line;
            $line .= "\"\r\n";
            fputcsv($file,explode(',',$line));
        }
        fclose($file);
    }

    private $picklistValues;
    private $fieldArray;
    private $fieldDataTypeCache = array();
    /**
     * this function takes in an array of values for an user and sanitizes it for export
     * @param array $arr - the array of values
     */
    function sanitizeValues($arr){
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $roleid = $currentUser->get('roleid');
        if(empty ($this->fieldArray)){
            $this->fieldArray = $this->moduleFieldInstances;
            foreach($this->fieldArray as $fieldName => $fieldObj){
                //In database we have same column name in two tables. - inventory modules only
                if($fieldObj->get('table') == 'vtiger_inventoryproductrel' && ($fieldName == 'discount_amount' || $fieldName == 'discount_percent')){
                    $fieldName = 'item_'.$fieldName;
                    $this->fieldArray[$fieldName] = $fieldObj;
                } else {
                    $columnName = $fieldObj->get('column');
                    $this->fieldArray[$columnName] = $fieldObj;
                }
            }
        }
        $moduleName = $this->moduleInstance->getName();
        foreach($arr as $fieldName=>&$value){
            if(isset($this->fieldArray[$fieldName])){
                $fieldInfo = $this->fieldArray[$fieldName];
            }else {
                unset($arr[$fieldName]);
                continue;
            }
            $value = trim(decode_html($value),"\"");
            $uitype = $fieldInfo->get('uitype');
            $fieldname = $fieldInfo->get('name');

            if(!$this->fieldDataTypeCache[$fieldName]) {
                $this->fieldDataTypeCache[$fieldName] = $fieldInfo->getFieldDataType();
            }
            $type = $this->fieldDataTypeCache[$fieldName];

            if($fieldname != 'hdnTaxType' && ($uitype == 15 || $uitype == 16 || $uitype == 33)){
                if(empty($this->picklistValues[$fieldname])){
                    $this->picklistValues[$fieldname] = $this->fieldArray[$fieldname]->getPicklistValues();
                }
                // If the value being exported is accessible to current user
                // or the picklist is multiselect type.
                if($uitype == 33 || $uitype == 16 || array_key_exists($value,$this->picklistValues[$fieldname])){
                    // NOTE: multipicklist (uitype=33) values will be concatenated with |# delim
                    $value = trim($value);
                } else {
                    $value = '';
                }
            } elseif($uitype == 52 || $type == 'owner') {
                $value = Vtiger_Util_Helper::getOwnerName($value);
            }elseif($type == 'reference'){
                $value = trim($value);
                if(!empty($value)) {
                    $parent_module = getSalesEntityType($value);
                    $displayValueArray = getEntityName($parent_module, $value);
                    if(!empty($displayValueArray)){
                        foreach($displayValueArray as $k=>$v){
                            $displayValue = $v;
                        }
                    }
                    if(!empty($parent_module) && !empty($displayValue)){
                        $value = $parent_module."::::".$displayValue;
                    }else{
                        $value = "";
                    }
                } else {
                    $value = '';
                }
            } elseif($uitype == 72 || $uitype == 71) {
                $value = CurrencyField::convertToUserFormat($value, null, true, true);
            } elseif($uitype == 7 && $fieldInfo->get('typeofdata') == 'N~O' || $uitype == 9){
                $value = decimalFormat($value);
            } else if($type == 'date' || $type == 'datetime'){
                $value = DateTimeField::convertToUserFormat($value);
            }
            if($moduleName == 'Documents' && $fieldname == 'description'){
                $value = strip_tags($value);
                $value = str_replace('&nbsp;','',$value);
                array_push($new_arr,$value);
            }
        }
        return $arr;
    }
}