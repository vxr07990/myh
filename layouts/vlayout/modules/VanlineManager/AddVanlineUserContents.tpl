{strip}
<form class='form-horizontal recordEditView equalSplit' id='EditView' name='EditView' method='post' enctype='multipart/form-data' action='index.php'>
<input type='hidden' name='module' value='VanlineManager' />
<input type='hidden' name='action' value='SaveVanlineUser' />
<input type='hidden' name='record' value />
<input type='hidden' name='srcRecord' value='{$SRC_RECORD}' />
<input type='hidden' name='user' value='{$USER}' />
<input type='hidden' id='hiddenFields' name='hiddenFields' value />
<table class='table table-bordered blockContainer showInlineTable'>
{$SUB_ROLES = $CURRENT_USER->limitPicklistRoles($SUB_ROLES, 'VanlineManager', $SRC_RECORD)}
	<thead>
		<th class='blockHeader' colspan='5'>
			&nbsp;&nbsp;User Login & Role
		</th>
	</thead>
	<tbody>
		<tr>
			<td class='fieldLabel'>
				Email <span class='redColor'>*</span>
			</td>
			<td class='fieldValue'>
				<input type='text' id='Users_editView_fieldName_email1' name='email1' class='input-large' />
			</td>
			<td class='fieldLabel'>
				Role <span class='redColor'>*</span>
			</td>
			<td class='fieldValue'>
				<select class='chzn-select' name='roleid'>
					{foreach item=ROLE key=INDEX from=$SUB_ROLES}
						<option value="{$ROLE}">{$ROLE_NAMES[$INDEX]}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class='fieldLabel'>
				First Name
			</td>
			<td class='fieldValue'>
				<input type='text' id='Users_editView_fieldName_first_name' name='first_name' class='input-large' />
			</td>
			<td class='fieldLabel'>
				Last Name <span class='redColor'>*</span>
			</td>
			<td class='fieldValue'>
				<input type='text' id='Users_editView_fieldName_last_name' name='last_name' class='input-large' />
			</td>
		</tr>
	</tbody>
</table>
<br />
<div class='pull-right'>
	<button class='btn btn-success' type='button' id='submitButton'>
		<strong>Save</strong>
	</button>
	<a class='cancelLink' type='reset' onclick='javascript:window.close();'>Cancel</a>
</div>
</form>

{/strip}