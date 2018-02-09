<div class="modelContainer" style="width: 1100px; max-width: 1100px;">
	<input type="hidden" value="{$columns}" id="toEditColumns">
	<input type="hidden" value="{$toEdit}" id="toEditID">
    <div class="modal-header contentsBackground">
        <button data-dismiss="modal" class="close" title="{vtranslate('CLOSED') }">&times;</button>
        <h4>{vtranslate('Create new filter', $MODULENAME)}</h4>
    </div>
    <div class="modal-body tabbable" style="min-height: 150px;">
        <div class="container" style="width:100%;">
            <div class="row-fluid">
				<input type="hidden" id="tableType" value="{$relModule}">
                <table class="table table-bordered equalSplit detailview-table">
                    <tbody>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">User Name</label>
                            </td>
                            <td class="fieldValue medium">
                                <input type="hidden" id="modaluserid" value="{$CURRENT_USER_ID}">
                                <span class="value" data-field-type="string">{$CURRENT_USER_FULL_NAME}</span>
                            </td>
                        </tr>
						<tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Filter Name</label>
                            </td>
							<td class="fieldValue medium">
								<input type="text" id="filterName" value="{$filterName}">
							</td>
						</tr>
						<tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Default Filter</label>
                            </td>
							<td class="fieldValue medium">
								<input type="hidden" id="defaultFilter" value="{$defaultFilter}">
								<input type="checkbox" value="" onclick="$(this).parent().find('input:hidden').val(($(this).prop('checked')) ? '1' : '0')" {if $defaultFilter eq '1'}checked{/if}>
							</td>
						</tr>
                        <tr>
                            <td class="fieldLabel medium">
                                <label class="muted pull-right marginRight10px">Columns</label>
                            </td>
							<td class="fieldValue medium">
								<div class="row-fluid">
									<span class="span10">
										<select class="chzn-select" id="columns" multiple style="width: 100%;">
											{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
												{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
													<option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}">({$relModule}) {vtranslate($FIELD_MODEL->get('label'), $relModule)} </option>
												{/foreach}
											{/foreach}
										</select>
									</span>
								</div>   
							</td>
                        </tr>
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-success" id="saveCustomFilter" type="submit" name="saveCustomFilter"><strong>{vtranslate('Save', $MODULENAME) }</strong></button>
    </div>        
</div>