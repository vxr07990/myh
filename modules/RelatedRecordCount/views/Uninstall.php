<?php
/* ********************************************************************************
 * The content of this file is subject to the Google Address ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class RelatedRecordCount_Uninstall_View extends Settings_Vtiger_Index_View {

    function process (Vtiger_Request $request) {
        global $adb;
        echo '<div class="container-fluid">
                <div class="widget_header row-fluid">
                    <h3>Related Record Count</h3>
                </div>
                <hr>';
        // Uninstall module
        $module = Vtiger_Module::getInstance('RelatedRecordCount');
        if ($module) $module->delete();

        // Remove related data
        $message = $this->removeData();
        echo $message;

        // remove directory
        $res_template = $this->delete_folder('layouts/vlayout/modules/RelatedRecordCount');
        echo "&nbsp;&nbsp;- Delete Related Record Count template folder";
        if($res_template) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';

        $res_module = $this->delete_folder('modules/RelatedRecordCount');
        echo "&nbsp;&nbsp;- Delete Related Record Count module folder";
        if($res_module) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';

        echo "Module was Uninstalled.";
        echo '</div>';
    }

    function delete_folder($tmp_path){
        if(!is_writeable($tmp_path) && is_dir($tmp_path)&& isFileAccessible($tmp_path)) {
            chmod($tmp_path,0777);
        }
        $handle = opendir($tmp_path);
        while($tmp=readdir($handle)) {
            if($tmp!='..' && $tmp!='.' && $tmp!=''){
                if(is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp) && isFileAccessible($tmp_path.DS.$tmp)) {
                    unlink($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp && isFileAccessible($tmp_path.DS.$tmp))){
                    chmod($tmp_path.DS.$tmp,0666);
                    unlink($tmp_path.DS.$tmp);
                }

                if(is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp && isFileAccessible($tmp_path.DS.$tmp))) {
                    $this->delete_folder($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp && isFileAccessible($tmp_path.DS.$tmp))){
                    chmod($tmp_path.DS.$tmp,0777);
                    $this->delete_folder($tmp_path.DS.$tmp);
                }
            }
        }
        closedir($handle);
        rmdir($tmp_path);
        if(!is_dir($tmp_path)) {
            return true;
        } else {
            return false;
        }
    }

    /* ********************************************************************************
     * All module must be have function removeData(). Because VTEStore will call this function to uninstall extension
     * ****************************************************************************** */
    function removeData(){
        global $adb;
        $message='';

        $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` = ?",array('VTE Related Record Count'));

        // drop tables
        $sql = "DROP TABLE `vte_related_record_count`;";
        $result = $adb->pquery($sql,array());

        $message.= "&nbsp;&nbsp;- Delete Related Record Count tables";
        if($result) $message.= " - DONE"; else $message.= " - <b>ERROR</b>";
        $message.= '<br>';

        return $message;
    }
}