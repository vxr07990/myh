<?php
/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking"License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class DataExportTracking_DownloadFile_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();

		if(!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $request->get('record'))) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
		}
	}

	public function process(Vtiger_Request $request) {
        $this->downloadFile($request->get('download_file'));
	}
    function downloadFile($download_file) {
        $arr_fdownload = pathinfo($download_file);
        $filePath = $arr_fdownload['dirname'] . "/";
        $fileName = $arr_fdownload['basename'];
        $fileContent = false;
        $fileName = html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset'));
        $fileSize = filesize($filePath.$fileName);
        $fileSize = $fileSize + ($fileSize % 1024);
        if (fopen($filePath.$fileName, "r")) {
            $fileContent = fread(fopen($filePath.$fileName, "r"), $fileSize);
            header("Content-type: ".mime_content_type($filePath.$fileName));
            header("Pragma: public");
            header("Cache-Control: private");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Description: PHP Generated Data");
        }
        echo $fileContent;
    }
}