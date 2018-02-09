{strip}
    <table class="table table-bordered blockContainer showInlineTable equalSplit" name="MenuEditorTable" xmlns="http://www.w3.org/1999/html">
        <thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
					<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">  &nbsp;&nbsp;
					{vtranslate('LBL_MENUEDITOR', 'MenuCreator')}
				</th>
			</tr>
        </thead>
		<tbody>
			<tr colspan="4">
				<td class="fieldLabel medium narrowWidthType" colspan="1">
					<label class="muted">Modules</label>
				</td>
				<td class="fieldValue typeCell narrowWidthType" style="text-align:center;margin:auto" colspan="3">
					<div class="row-fluid">
						<span class="span10">
							{$SELECTED_MODULES}
						</span>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
{/strip}