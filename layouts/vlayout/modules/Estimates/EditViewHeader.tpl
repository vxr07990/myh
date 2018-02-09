{strip}
<input type="hidden" id="disabledGoogleModules" value="{getenv('GOOGLE_ADDRESS_DISABLE')}">
<input type="hidden" id="contracts_available_to_business_lines" value='{$CONTRACTS_AVAILABLE_TO_BUSINESS_LINES}'>
<div class='editViewContainer container-fluid' data-movetype="{$MOVE_TYPE}" data-lockfields="{$LOCK_ESTIMATE}">
	<form novalidate class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
            <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
        {/if}
        {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
        {assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
        {if $IS_PARENT_EXISTS}
            {assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
            <input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
            <input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
        {/if}
        <input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
        <input type="hidden" name="contractValuationOverride" value="0" />
        <input type="hidden" name="currentBrand" value="" />
        <input type="hidden" name="hasUnratedChanges" value="0" />
        <input type="hidden" name="duplicate" value="{$IS_DUPLICATE}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
        {if $IS_RELATION_OPERATION }
            <input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
            <input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
        {/if}
        <div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
            {if $RECORD_ID neq ''}
                <h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
            {/if}
            <span class="pull-right">
				<button disabled class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>

    <input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />
{/strip}
