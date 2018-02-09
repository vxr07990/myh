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
	<span class="span10 margin0px">
		<span class="row-fluid">
			{assign var=BUSINESSLINE_FIELD value=$MODULE_MODEL->getField('agentcompgr_businessline')}
			{assign var=BILLINGTYPE_FIELD value=$MODULE_MODEL->getField('agentcompgr_billingtype')}
			{assign var=AUTHORITY_FIELD value=$MODULE_MODEL->getField('agentcompgr_authority')}
			{assign var=BUSINESSLINE_PICKLIST_VALUES value=$BUSINESSLINE_FIELD->getPicklistValues()}
			{assign var=BILLINGTYPE_PICKLIST_VALUES value=$BILLINGTYPE_FIELD->getPicklistValues()}
			{assign var=AUTHORITY_PICKLIST_VALUES value=$AUTHORITY_FIELD->getPicklistValues()}
			{assign var="BUSINESSLINE_VALUE_LIST" value=explode(' |##| ',$RECORD->get('agentcompgr_businessline'))}
			{assign var="BILLINGTYPE_VALUE_LIST" value=explode(' |##| ',$RECORD->get('agentcompgr_billingtype'))}
			{assign var="AUTHORITY_VALUE_LIST" value=explode(' |##| ',$RECORD->get('agentcompgr_authority'))}
			{if $BUSINESSLINE_PICKLIST_VALUES|count eq $BUSINESSLINE_VALUE_LIST|count}
				{assign var="BUSINESSLINE_VALUE_LIST" value='All'}
			{else}
				{assign var="BUSINESSLINE_VALUE_LIST" value=implode(', ',$BUSINESSLINE_VALUE_LIST)}
			{/if}
			{if $BILLINGTYPE_PICKLIST_VALUES|count eq $BILLINGTYPE_VALUE_LIST|count}
				{assign var="BILLINGTYPE_VALUE_LIST" value='All'}
			{else}
				{assign var="BILLINGTYPE_VALUE_LIST" value=implode(', ',$BILLINGTYPE_VALUE_LIST)}
			{/if}
			{if $AUTHORITY_PICKLIST_VALUES|count eq $AUTHORITY_VALUE_LIST|count}
				{assign var="AUTHORITY_VALUE_LIST" value='All'}
			{else}
				{assign var="AUTHORITY_VALUE_LIST" value=implode(', ',$AUTHORITY_VALUE_LIST)}
			{/if}

			<span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown" title="{$BUSINESSLINE_VALUE_LIST} / {$BILLINGTYPE_VALUE_LIST} / {$AUTHORITY_VALUE_LIST}">
				{$BUSINESSLINE_VALUE_LIST} / {$BILLINGTYPE_VALUE_LIST} / {$AUTHORITY_VALUE_LIST}
			</span>
		</span>
	</span>
{/strip}