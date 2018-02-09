<?php
/* ********************************************************************************
 * The content of this file is subject to the Lead Company Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class LeadCompanyLookup_Uninstall_View extends Settings_Vtiger_Index_View {

    function process (Vtiger_Request $request) {
        global $adb;
        echo '<div class="container-fluid">
                <div class="widget_header row-fluid">
                    <h3>Lead Company Lookup</h3>
                </div>
                <hr>';

        $module = Vtiger_Module::getInstance('LeadCompanyLookup');

        echo "&nbsp;&nbsp;- Delete Lead Company Lookup header scripts";
        $module->deleteLink('HEADERSCRIPT','LeadCompanyLookupJs');
        echo " - DONE<br/>";

        // uninstall module
        if ($module) $module->delete();

        // Remove related data
        $message = $this->removeData();
        echo $message;

        $lang_file = 'languages/en_us/LeadCompanyLookup.php';
        if(is_file($lang_file)) {
            $res_lang = unlink($lang_file);
            echo "&nbsp;&nbsp;- Delete Lead Company Lookup language file";
            if($res_lang) echo " - DONE"; else echo " - <b>ERROR</b>";
            echo '<br/>';
        }

        // remove directory
        $res_template = $this->delete_folder('layouts/vlayout/modules/LeadCompanyLookup');
        echo "&nbsp;&nbsp;- Delete Lead Company Lookup template folder";
        if($res_template) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';

        $res_module = $this->delete_folder('modules/LeadCompanyLookup');
        echo "&nbsp;&nbsp;- Delete Lead Company Lookup module folder";
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
        return $message;
    }
}