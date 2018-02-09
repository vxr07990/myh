<?php
/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class Notifications_Module_Model extends Vtiger_Module_Model {
    function getSettingLinks() {
        $settingsLinks = parent::getSettingLinks();
        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Settings',
            'linkurl' => 'index.php?module=Notifications&parent=Settings&view=Settings',
            'linkicon' => ''
        );
        return $settingsLinks;
    }
}