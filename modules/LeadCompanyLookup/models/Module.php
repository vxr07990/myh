<?php
/* ********************************************************************************
 * The content of this file is subject to the Lead Company Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class LeadCompanyLookup_Module_Model extends Vtiger_Module_Model {
    function getSettingLinks() {
//        $settingsLinks[] = array(
//            'linktype' => 'MODULESETTING',
//            'linklabel' => 'License & Upgrade',
//            'linkurl' => 'index.php?module=LeadCompanyLookup&parent=Settings&view=Upgrade',
//            'linkicon' => ''
//        );
        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Uninstall',
            'linkurl' => 'index.php?module=LeadCompanyLookup&parent=Settings&view=Uninstall',
            'linkicon' => ''
        );
        return $settingsLinks;
    }
}
?>