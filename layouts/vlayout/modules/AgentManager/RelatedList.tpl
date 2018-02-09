{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
    {if $RELATEDMODULE_NAME eq 'TimeCalculator'}
        {if $METHOD eq 'Edit'}
        {include file=vtemplate_path('TimeCalculatorEdit.tpl', 'TimeCalculator')}
        {else}
        {include file=vtemplate_path('TimeCalculatorDetail.tpl', 'TimeCalculator') MODULE_NAME = 'TimeCalculator'}
        {/if}
    {else}
        {include file=vtemplate_path('RelatedList.tpl', 'Vtiger')}
    {/if}
{/strip}
