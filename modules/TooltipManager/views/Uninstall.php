<?php
/* ********************************************************************************
 * The content of this file is subject to the Tooltip Manager ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class TooltipManager_Uninstall_View extends Settings_Vtiger_Index_View {




    function process (Vtiger_Request $request) {
        global $adb;
        echo '<div class="container-fluid">
                <div class="widget_header row-fluid">
                    <h3>Tooltip Manager</h3>
                </div>
                <hr>';

        // Uninstall module
        $module = Vtiger_Module::getInstance('TooltipManager');
        if ($module) $module->delete();

        // Remove related data
        $message = $this->removeData();
        echo $message;

        // remove directory
        $res_template = $this->delete_folder('layouts/vlayout/modules/TooltipManager');
        echo "&nbsp;&nbsp;- Delete Tooltip Manager template folder";
        if($res_template) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';

        $res_module = $this->delete_folder('modules/TooltipManager');
        echo "&nbsp;&nbsp;- Delete Tooltip Manager module folder";
        if($res_module) echo " - DONE"; else echo " - <b>ERROR</b>";
		
        echo '<br>';
        echo "Module was Uninstalled.";
        echo '</div>';
    }

    function delete_folder($tmp_path){
        if(!is_writeable($tmp_path) && is_dir($tmp_path)) {
            chmod($tmp_path,0777);
        }
        $handle = opendir($tmp_path);
        while($tmp=readdir($handle)) {
            if($tmp!='..' && $tmp!='.' && $tmp!=''){
                if(is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)) {
                    unlink($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)){
                    chmod($tmp_path.DS.$tmp,0666);
                    unlink($tmp_path.DS.$tmp);
                }

                if(is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)) {
                    $this->delete_folder($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)){
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

        // delete fields
        $sql = "ALTER TABLE `vtiger_field` DROP COLUMN `icon`";
        $result = $adb->pquery($sql,array());
        $sql = "ALTER TABLE `vtiger_field` DROP COLUMN `preview_type`;";
        $result = $adb->pquery($sql,array());

        $link_list = "'TooltipManagerJS','TooltipManagerjQueryUrlJS','TooltipManagerqTip'";
        echo "&nbsp;&nbsp;- Delete Tooltip Manager header scripts";
        $adb->pquery("DELETE FROM vtiger_links WHERE linklabel IN (?)",array($link_list));

        $sql = "DELETE FROM `vtiger_settings_field` WHERE (`name`=?)";
        $result = $adb->pquery($sql,array('Tooltip Manager'));
        $sql = "UPDATE `vtiger_settings_field_seq` SET `id`=`id`-1";
        $result2 = $adb->pquery($sql,array());

        $message.= "&nbsp;&nbsp;- Delete TooltipManager tables";
        if($result) $message.= " - DONE"; else $message.= " - <b>ERROR</b>";
        $message.= '<br>';

        return $message;
    }
}