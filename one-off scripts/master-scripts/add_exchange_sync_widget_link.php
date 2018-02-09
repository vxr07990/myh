<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



$Vtiger_Utils_Log = true;

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'vendor/autoload.php';

$calendar = Vtiger_Module::getInstance('Calendar');
$type     = 'LISTVIEWSIDEBARWIDGET';
$label    = 'Microsoft Exchange Calendar';
$url      = 'module=Exchange&view=List&sourcemodule=Calendar';

$calendar->addLink($type, $label, $url);

/**
 * ```php
 * // @see Vtiger_Module
 * function addLink($type, $label, $url, $iconpath='', $sequence=0, $handlerInfo=null) {
 *     Vtiger_Link::addLink($this->id, $type, $label, $url, $iconpath, $sequence, $handlerInfo);
 * }
 * ```
 */

/**
 * ```sql
 * INSERT INTO `vtiger_links` (`linkid`, `tabid`, `linktype`, `linklabel`, `linkurl`, `linkicon`, `sequence`, `handler_path`, `handler_class`, `handler`)
 * VALUES
 * (144, 9, 'LISTVIEWSIDEBARWIDGET', 'Microsoft Exchange Calendar', 'module=EwsTest&view=List&sourcemodule=Calendar', '', 0, NULL, NULL, NULL);
 * ```
 */


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";