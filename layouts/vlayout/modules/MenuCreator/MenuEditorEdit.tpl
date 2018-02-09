{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="MenuEditorTable" xmlns="http://www.w3.org/1999/html">
        <thead>
			<tr>
				<th class="blockHeader" colspan="4">
					{vtranslate('LBL_MENUEDITOR', 'MenuCreator')}
				</th>
			</tr>
        </thead>
		<tbody>
			<tr>
				<td class="fieldValue typeCell narrowWidthType" colspan="4">
					<div class="row-fluid" style="padding-top:10px">
						{assign var=SELECTED_MODULE_IDS value=array()}

						<select data-placeholder="{vtranslate('LBL_ADD_MENU_ITEM',$QUALIFIED_MODULE)}" id="menuListSelectElement" name="menuListSelectElement[]" class="select2 span12" multiple="" data-validation-engine="validate[required]" >
							{foreach key=SELECTED_MODULE item=MODULE_MODEL from=$SELECTED_MODULES}
								{array_push($SELECTED_MODULE_IDS, $MODULE_MODEL->getId())}
							{/foreach}

							{foreach key=MODULE_NAME item=MODULE_MODEL from=$ALL_MODULES}
								{assign var=TABID value=$MODULE_MODEL->getId()}
								<option value="{$TABID}" {if in_array($TABID, $SELECTED_MODULE_IDS)} selected {/if}>{vtranslate($MODULE_NAME)}</option>
							{/foreach}
						</select>
						<input type="hidden" name="menuListSelectElement_selected_order" value='{ZEND_JSON::encode($SELECTED_MODULE_IDS)}'>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="row-fluid" style="padding-top:10px">
		<div class="notification span12">
			<div class="alert alert-info">
				<div class="padding1per"><i class="icon-info-sign" style="margin-top:2px"></i>
					<span style="margin-left: 2%">{vtranslate('LBL_MENU_EDITOR_MESSAGE', $MODULE)}</span>
				</div>
			</div>
		</div>
	</div>						
{/strip}