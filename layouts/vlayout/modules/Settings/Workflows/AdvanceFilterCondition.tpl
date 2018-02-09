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
<div class="row-fluid conditionRow marginBottom10px">
	<span class="span4">
		<select class="{if empty($NOCHOSEN)}chzn-select{/if} row-fluid" name="columnname" data-placeholder="{vtranslate('LBL_SELECT_FIELD',$QUALIFIED_MODULE)}">
			<option value="none"></option>
			{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
				<optgroup label='{vtranslate($BLOCK_LABEL, $SELECTED_MODULE_NAME)}'>
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
					{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
					{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
					{if !empty($COLUMNNAME_API)}
						{assign var=columnNameApi value=$COLUMNNAME_API}
					{else}
						{assign var=columnNameApi value=getCustomViewColumnName}
					{/if}
					<option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
					{if decode_html($FIELD_MODEL->$columnNameApi()) eq $CONDITION_INFO['columnname']}
						{assign var=FIELD_TYPE value=$FIELD_MODEL->getFieldDataType()}
						{assign var=SELECTED_FIELD_MODEL value=$FIELD_MODEL}
						{$FIELD_INFO['value'] = decode_html($CONDITION_INFO['value'])}
						selected="selected"
					{/if}
					{if ($MODULE_MODEL->get('name') eq 'Events') and ($FIELD_NAME eq 'recurringtype')}
						{assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
						{$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
					{/if}
					{if $FIELD_MODEL->getFieldDataType() eq 'agentpicklist'}
						{*Grabage logic necessary because of a garbage method. Entropy sucks.*}
						{assign var=USER_MODEL value=Users_Record_Model::getCurrentUserModel()}
						{assign var=ACCESSIBLE_OWNERS value=$USER_MODEL->getAccessibleOwnersForUser($SELECTED_MODULE_NAME)}
						{assign var=AGENTS_IN_PROGRESS value=false}
						{foreach key=OWNER_ID item=CURRENT_OWNER from=$ACCESSIBLE_OWNERS}
							{if $OWNER_ID eq 'agents'}
								{$AGENTS_IN_PROGRESS = true}
							{elseif $OWNER_ID eq 'vanlines'}
								{$AGENTS_IN_PROGRESS = false}
							{else}
								{if $AGENTS_IN_PROGRESS eq true}
									{$AGENT_CODE = Vtiger_Record_Model::getInstanceById($OWNER_ID, 'AgentManager')->get('agency_code')}
									{$FIELD_INFO['picklistvalues']['Agents'][$OWNER_ID] = "$CURRENT_OWNER ($AGENT_CODE)"}
								{else}
									{$FIELD_INFO['picklistvalues']['Vanlines'][$OWNER_ID] = $CURRENT_OWNER}
								{/if}
							{/if}
						{/foreach}
					{/if}
                    {if $FIELD_NAME eq 'effective_tariff'}
                        {$FIELD_INFO['picklistvalues'] = $EFFECTIVE_TARIFF_PICKLIST}
                    {/if}
                    {if $FIELD_NAME eq 'sales_person'}
                        {$FIELD_INFO['picklistvalues'] = $SALES_PERSON_PICKLIST}
                    {/if}
					data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}'
							data-fieldmodulename="{$MODULE_MODEL->get('name')}"
                    {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
					{if $SELECTED_MODULE_NAME neq $MODULE_MODEL->get('name')}
						({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
					{else}
						{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}
					{/if}
				</option>
				{/foreach}
				</optgroup>
			{/foreach}
		</select>
		<!-- Oh my god don't do this: {*$USER_MODEL|@debug_print_var*} -->
	</span>
	<span class="span3">
		<select class="{if empty($NOCHOSEN)}chzn-select{/if} row-fluid" name="comparator">
			 <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
			{assign var=ADVANCE_FILTER_OPTIONS value=$ADVANCED_FILTER_OPTIONS_BY_TYPE[$FIELD_TYPE]}
			{foreach item=ADVANCE_FILTER_OPTION from=$ADVANCE_FILTER_OPTIONS}
				<option value="{$ADVANCE_FILTER_OPTION}"
				{if $ADVANCE_FILTER_OPTION eq $CONDITION_INFO['comparator']}
						selected
				{/if}
				>{vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION])}</option>
			{/foreach}
		</select>
	</span>
	<span class="span4 fieldUiHolder">
		<input name="{if $SELECTED_FIELD_MODEL}{$SELECTED_FIELD_MODEL->get('name')}{/if}" data-value="value" class="row-fluid" type="text" value="{$CONDITION_INFO['value']|escape}" />
	</span>
	<span class="hide">
		<!-- TODO : see if you need to respect CONDITION_INFO condition or / and  -->
		{if empty($CONDITION)}
			{assign var=CONDITION value="and"}
		{/if}
		<input type="hidden" name="column_condition" value="{$CONDITION}" />
	</span>
	 <span class="span1">
		<i class="deleteCondition icon-trash alignMiddle" title="{vtranslate('LBL_DELETE', $MODULE)}"></i>
	</span>
</div>
{/strip}
