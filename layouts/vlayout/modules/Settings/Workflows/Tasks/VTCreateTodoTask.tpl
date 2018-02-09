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
	<div class="row-fluid">
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_TITLE',$QUALIFIED_MODULE)}<span class="redColor">*</span></span>
			<input data-validation-engine='validate[required]' class="span9" name="todo" type="text" value="{$TASK_OBJECT->todo}" />
		</div>
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_DESCRIPTION',$QUALIFIED_MODULE)}</span>
			<textarea class="span9" name="description">{$TASK_OBJECT->description}</textarea>
		</div>
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_STATUS',$QUALIFIED_MODULE)}</span>
			<span class="span4">
				{assign var=STATUS_PICKLIST_VALUES value=$TASK_TYPE_MODEL->getTaskBaseModule()->getField('taskstatus')->getPickListValues()}
				<select name="status" class="chzn-select">
					{foreach  from=$STATUS_PICKLIST_VALUES item=STATUS_PICKLIST_VALUE key=STATUS_PICKLIST_KEY}
						<option value="{$STATUS_PICKLIST_KEY}" {if $STATUS_PICKLIST_KEY eq $TASK_OBJECT->status} selected="" {/if}>{$STATUS_PICKLIST_VALUE}</option>
					{/foreach}
				</select>
			</span>
			<span class="span2 marginLeftZero">Insert Field</span>
			<span class="span2 marginLeftZero">
				<select name="insertfieldname" class="chzn-select" style="min-width: 250px" data-placeholder="{vtranslate('LBL_SELECT_FIELD',$QUALIFIED_MODULE)}">
					<option value=''></option>
					{*{foreach from=$MODULE_MODEL->getFields() item=FIELD_MODEL}*}
						{*{if !$FIELD_MODEL->isEditable() or $FIELD_MODEL->getFieldDataType() eq 'reference' or ($MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}*}
							{*{continue}*}
						{*{/if}*}
						{*{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}*}
						{*{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}*}
						{*<option value="{$FIELD_MODEL->get('name')}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >*}
							{*{if $SOURCE_MODULE neq $MODULE_MODEL->get('name')}*}
								{*({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}*}
							{*{else}*}
								{*{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}*}
							{*{/if}*}
						{*</option>*}
					{*{/foreach}*}
                    {$ALL_FIELD_OPTIONS}
				</select>
			</span>
		</div>
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_PRIORITY',$QUALIFIED_MODULE)}</span>
			<span class="span4">
				{assign var=PRIORITY_PICKLIST_VALUES value=$TASK_TYPE_MODEL->getTaskBaseModule()->getField('taskpriority')->getPickListValues()}
				<select name="priority" class="chzn-select">
					{foreach  from=$PRIORITY_PICKLIST_VALUES item=PRIORITY_PICKLIST_VALUE key=PRIORITY_PICKLIST_KEY}
						<option value="{$PRIORITY_PICKLIST_KEY}" {if $PRIORITY_PICKLIST_KEY eq $TASK_OBJECT->priority} selected="" {/if}>{$PRIORITY_PICKLIST_VALUE}</option>
					{/foreach}
				</select>
			</span>
		</div>
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_ASSIGNED_TO',$QUALIFIED_MODULE)}</span>
			<span class="span4">
				<select name="assigned_user_id" class="chzn-select">
					<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
					{foreach from=$ASSIGNED_TO key=LABEL item=ASSIGNED_USERS_LIST}
						<optgroup label="{vtranslate($LABEL,$QUALIFIED_MODULE)}">
							{foreach from=$ASSIGNED_USERS_LIST item=ASSIGNED_USER key=ASSIGNED_USER_KEY}
								<option value="{$ASSIGNED_USER_KEY}" {if $ASSIGNED_USER_KEY eq $TASK_OBJECT->assigned_user_id} selected="" {/if}>{$ASSIGNED_USER}</option>
							{/foreach}
						</optgroup>
					{/foreach}
					{if $ASSIGNED_MOVE_ROLES_LIST}
						<optgroup label="{vtranslate('LBL_MOVE_ROLES',$QUALIFIED_MODULE)}">
							{foreach from=$ASSIGNED_MOVE_ROLES_LIST item=ASSIGNED_ROLE}
								<option value="{$ASSIGNED_ROLE}" {if $ASSIGNED_ROLE eq $TASK_OBJECT->assigned_user_id} selected="" {/if}>{$ASSIGNED_ROLE}</option>
							{/foreach}
						</optgroup>
					{/if}
                    <optgroup label="{vtranslate('LBL_SPECIAL_OPTIONS')}">
                            <option value="copyParentOwner" {if $TASK_OBJECT->assigned_user_id eq 'copyParentOwner'} selected="" {/if}>{vtranslate('LBL_PARENT_OWNER')}</option>
                    </optgroup>
				</select>
			</span>
		</div>
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_TIME',$QUALIFIED_MODULE)}</span>
			<div class="input-append time span6">
			{if $TASK_OBJECT->time neq ''}
				{assign var=TIME value=$TASK_OBJECT->time}
			{else}
				{assign var=DATE_TIME_VALUE value=Vtiger_Datetime_UIType::getDateTimeValue('now')}
				{assign var=DATE_TIME_COMPONENTS value=explode(' ' ,$DATE_TIME_VALUE)}
				{assign var=TIME value=implode(' ',array($DATE_TIME_COMPONENTS[1],$DATE_TIME_COMPONENTS[2]))}
			{/if}
				<input  type="text" class="timepicker-default input-small" value="{$TIME}" name="time" />
				<span class="add-on cursorPointer">
					<i class="icon-time"></i>
				</span>
			</div>
		</div>
		<div class="row-fluid padding-bottom1per">
			<span class="span2">{vtranslate('LBL_DUE_DATE',$QUALIFIED_MODULE)}</span>
			<span class="span2 row-fluid">
				<input class="span6" type="text" name="days" value="{$TASK_OBJECT->days}">&nbsp;
				<span class="alignMiddle">{vtranslate('LBL_DAYS',$QUALIFIED_MODULE)}</span>
			</span>
			<span class="span marginLeftZero">
				<select class="chzn-select" name="direction" style="width: 100px">
					<option {if $TASK_OBJECT->direction eq 'after'}selected=""{/if} value="after">{vtranslate('LBL_AFTER',$QUALIFIED_MODULE)}</option>
					<option {if $TASK_OBJECT->direction eq 'before'}selected=""{/if} value="before">{vtranslate('LBL_BEFORE',$QUALIFIED_MODULE)}</option>
				</select>
			</span>
			<span class="span6">
				<select class="chzn-select" name="datefield" style="width: 150px">
					{foreach from=$DATETIME_FIELDS item=DATETIME_FIELD}
						<option {if $TASK_OBJECT->datefield eq $DATETIME_FIELD->get('name')}selected{/if} value="{$DATETIME_FIELD->get('name')}">{vtranslate(vtranslate($DATETIME_FIELD->get('label'),$QUALIFIED_MODULE), $MODULE_MODEL->get('name'))}</option>
					{/foreach}
				</select>&nbsp;<span style="vertical-align: super">({vtranslate('LBL_THE_SAME_VALUE_IS_USED_FOR_START_DATE',$QUALIFIED_MODULE)})</span>
			</span>
		</div>
		<div class="row-fluid">
			<span class="span2">{vtranslate('LBL_SEND_NOTIFICATION',$QUALIFIED_MODULE)}</span>
			<div class="span6">
				<input  type="checkbox" name="sendNotification" value="true" {if $TASK_OBJECT->sendNotification}checked{/if} />
			</div>
		</div>
	</div>
{/strip}
