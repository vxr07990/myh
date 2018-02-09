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
<input type="hidden" id="disabledGoogleModules" value="{getenv('GOOGLE_ADDRESS_DISABLE')}">
<input type="hidden" id="contracts_available_to_business_lines" value='{$CONTRACTS_AVAILABLE_TO_BUSINESS_LINES}'>
{include file="EditViewBlocks.tpl"|@vtemplate_path:'Estimates'}
{if $INSTANCE_NAME eq 'graebel'}
    {*include file="DetailLineItemEdit.tpl"|@vtemplate_path:$MODULE*}
    {include file="DetailLineItemEdit.tpl"|@vtemplate_path:'Estimates'}
{else if getenv('IGC_MOVEHQ') eq 1}
    {include file="DetailLineItemEdit.tpl"|@vtemplate_path:'Estimates'}
{else}
    {*include file="LineItemsEdit.tpl"|@vtemplate_path:$MODULE*}
    {include file="LineItemsEdit.tpl"|@vtemplate_path:'Estimates'}
{/if}
{include file="EditViewActions.tpl"|@vtemplate_path:'Vtiger'}
