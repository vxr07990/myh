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
	<span class="span2">
		<img src="{vimage_path('summary_Projects.png')}" class="summaryImg" />
	</span>
	<span class="span8 margin0px">
		<span class="row-fluid">
			<h4 class="recordLabel pushDown" title="{$RECORD->getName()}">
				{foreach item=NAME_FIELD from=$MODULE_MODEL->getNameFields()}
					{assign var=FIELD_MODEL value=$MODULE_MODEL->getField($NAME_FIELD)}
						{if $FIELD_MODEL->getPermissions()}
							{* OT 2893 - adding contact name to header *}
							{if $NAME_FIELD eq 'orders_contacts'}
								{*if getenv('INSTANCE_NAME') == 'graebel'*}
                                {if getenv('IGC_MOVEHQ')}
									<span class="{$NAME_FIELD}">{$RECORD->getDisplayValue($NAME_FIELD)}</span>
								{/if}
							{else}
								<span class="{$NAME_FIELD}">{$RECORD->get($NAME_FIELD)}</span>&nbsp;
                                {$RECORD->get('order_name')}
                                &nbsp;
							{/if}
						{/if}
				{/foreach}
			</h4>
		</span>
		{assign var=RELATED_TO value=$RECORD->get('linktoaccountscontacts')}
		{if !empty($RELATED_TO)}
		<span class="row-fluid">
			<span class="muted">{vtranslate('Related to',$MODULE_NAME)} - </span>
			{$RECORD->getDisplayValue('linktoaccountscontacts')}
		</span>
		{/if}
	</span>
{/strip}
