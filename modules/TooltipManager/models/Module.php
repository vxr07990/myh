<?php
/* ********************************************************************************
 * The content of this file is subject to the Tooltip Manager ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class TooltipManager_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get Settings links
	 * @return <Array>
	 */
	function getSettingLinks() {
		$settingsLinks[] = array(
					'linktype' => 'MODULESETTING',
					'linklabel' => 'Settings',
                    'linkurl' => 'index.php?module=TooltipManager&view=Settings&parent=Settings',
					'linkicon' => ''
		);
		
        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Uninstall',
            'linkurl' => 'index.php?module=TooltipManager&parent=Settings&view=Uninstall',
            'linkicon' => ''
        );
		return $settingsLinks;
	}
}
