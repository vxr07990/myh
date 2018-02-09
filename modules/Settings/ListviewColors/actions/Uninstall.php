<?php
/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
define('DS', DIRECTORY_SEPARATOR); 
require_once('include/utils/utils.php');
class Settings_ListviewColors_Uninstall_Action extends Vtiger_Action_Controller {
	public function checkPermission() {
		return true;
	}
	public function process(Vtiger_Request $request) {
		
		global $site_URL;
        $adb = PearDatabase::getInstance();

		echo "<br>&nbsp;&nbsp;<b>Uninstall ListviewColors module</b>";

		// vtiger_tab
		$sql = "DELETE FROM `vtiger_tab` WHERE `name` = 'ListviewColors';";
		$result = $adb->pquery($sql, array());
		echo "<br>&nbsp;&nbsp;- Delete in vtiger_tab";
		if($result) echo " - DONE"; else echo " - <b>ERROR</b>";

		// drop tables
		$sql = "DROP TABLE `vte_listview_colors`;";
		$result = $adb->pquery($sql, array());
		echo "<br>&nbsp;&nbsp;- Delete ListviewColors setting tables";
		if($result) echo " - DONE"; else echo " - <b>ERROR</b>";

		// vtiger_links
		$sql = "DELETE FROM `vtiger_links` WHERE `linklabel` IN('VTEListviewColorsJs','VTEListviewColorsJs') AND `linktype` = 'HEADERSCRIPT';";
		$result = $adb->pquery($sql, array());
		echo "<br>&nbsp;&nbsp;- Delete in vtiger_links";
		if($result) echo " - DONE"; else echo " - <b>ERROR</b>";
		
		//remove module to settings list sidebar
		$result = $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` LIKE 'VTE List View Colors'", array());
		echo "<br>&nbsp;&nbsp;- Delete module to settings list sidebar";
		if($result) echo " - DONE"; else echo " - <b>ERROR</b>";

		// remove directory
		$res_template = $this->delete_folder('layouts/vlayout/modules/ListviewColors');
		$res_template = $this->delete_folder('layouts/vlayout/modules/Settings/ListviewColors');
		echo "<br>&nbsp;&nbsp;- Delete VTE List View Colors template folder";
		if($res_template) echo " - DONE"; else echo " - <b>ERROR</b>";
		$res_module = $this->delete_folder('modules/ListviewColors');
		$res_module = $this->delete_folder('modules/Settings/ListviewColors');
		echo "<br>&nbsp;&nbsp;- Delete VTE List View Colors module folder";
		if($res_module) echo " - DONE"; else echo " - <b>ERROR</b>";

        die;
        //header('Location: index.php?module=ModuleManager&parent=Settings&view=List');
	}
	
	//===========================================================================
	public function delete_folder($tmp_path){
		if(!is_writeable($tmp_path) && is_dir($tmp_path)) {
			chmod($tmp_path,0777);
		}
		$handle = opendir($tmp_path);
		while($tmp=readdir($handle)) {
			if($tmp!='..' && $tmp!='.' && $tmp!=''){
				if(is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)) {
                    checkFileAccessForInclusion($tmp_path.DS.$tmp);
					unlink($tmp_path.DS.$tmp);
				} elseif(!is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)){
                    checkFileAccessForInclusion($tmp_path.DS.$tmp);
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
}

?>