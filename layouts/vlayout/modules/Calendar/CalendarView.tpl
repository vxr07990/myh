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
    {assign var=USER_MODULE_MODEL value=$CURRENT_USER->getModule()}
    {assign var=TIMEZONE_FIELD_MODEL value=$USER_MODULE_MODEL->getField('time_zone')}
    {if $TIMEZONE_FIELD_MODEL}
        {assign var=PICKLIST_VALUES value=$TIMEZONE_FIELD_MODEL->getPicklistValues()}
        {assign var=TIMEZONE_VALUE value=$USER_MODEL->get('time_zone')}
        <select id="timeZoneTmp" class="hide">
            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($TIMEZONE_VALUE)) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
            {/foreach}
        </select>
    {/if}
<input type="hidden" id="currentView" value="{getPurifiedSmartyParameters('view')}" />
<input type="hidden" id="activity_view" value="{$CURRENT_USER->get('activity_view')}" />
<input type="hidden" id="time_format" value="{$CURRENT_USER->get('hour_format')}" />
<input type="hidden" id="start_hour" value="{$CURRENT_USER->get('start_hour')}" />
<input type="hidden" id="date_format" value="{$CURRENT_USER->get('date_format')}" />
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<p><!-- Divider --></p>
				<div id="calendarview"></div>
			</div>
		</div>
	</div>
{/strip}
