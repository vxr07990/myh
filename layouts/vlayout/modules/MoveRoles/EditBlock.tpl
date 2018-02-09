{strip}
    {assign var=GUEST_MODULE_CAPS value=$GUEST_MODULE|upper}
    {assign var=GUEST_MODULE_LOWER value=$GUEST_MODULE|lower}
    {if ${$GUEST_MODULE_CAPS}}
        {assign var=GUEST_LIST value=$GUEST_MODULE_CAPS|cat: '_LIST'}
        {assign var=FIELD_ARRAY value=$GUEST_MODULE_CAPS|cat: '_BLOCK_FIELDS'}
        {assign var=GUEST_BLOCK_LABEL value=$GUEST_MODULE_CAPS|cat: '_BLOCK_LABEL'}
        {assign var=ID_COLUMN value=$GUEST_MODULE_LOWER|cat: 'id'}
        {assign var=TABLE_NAME value=$GUEST_MODULE|cat: 'Table'}
		{if getenv('INSTANCE_NAME') == 'graebel'}
			{assign var=MR_COLUMNS value=8}
			{assign var=LABEL_WIDTH value=10}
			{assign var=VALUE_WIDTH value=15}
		{else}
			{assign var=MR_COLUMNS value=4}
			{assign var=LABEL_WIDTH value=20}
			{assign var=VALUE_WIDTH value=30}
		{/if}
        <table class="table table-bordered blockContainer showInlineTable {if is_array($HIDDEN_BLOCKS)}{if in_array($TABLE_NAME, $HIDDEN_BLOCKS)}hide{/if}{/if}" name="{$TABLE_NAME}">
			<thead>
			<tr>
				<th class="blockHeader" colspan="{$MR_COLUMNS}">{vtranslate(${$GUEST_BLOCK_LABEL}, $GUEST_MODULE)}</th>
			</tr>
			</thead>
            {if !$LOCK_RATING}
                <tbody>
				<tr class="fieldLabel">
					<td colspan="{$MR_COLUMNS}">
						<button type="button" class="add{$GUEST_MODULE}" name="add{$GUEST_MODULE}">+</button>
						<input type="hidden" id="num{$GUEST_MODULE}" name="num{$GUEST_MODULE}" value="{${$GUEST_LIST}|@count}">
						<button type="button" class="add{$GUEST_MODULE}" name="add{$GUEST_MODULE}2" style="clear:right;float:right">+</button>
					</td>
				</tr>
				</tbody>
            {/if}
            <tbody class="default{$GUEST_MODULE} {$GUEST_MODULE}Block hide">
				<tr class="fieldLabel" colspan="{$MR_COLUMNS}">
					<td colspan="{$MR_COLUMNS}" class="blockHeader" style="background-color:#E8E8E8;">
						<span class="{$GUEST_MODULE}Title"></span>
						<a style="float: right; padding: 3px"><i title="Delete" class="delete{$GUEST_MODULE} icon-trash"></i></a>
						<input id="{$GUEST_MODULE_LOWER}_id" type="hidden" name="{$GUEST_MODULE_LOWER}_id" value="none">
					</td>
				</tr>
                {assign var=DEFAULT_CHZN value=1}
                {assign var=COUNTER value=0}
                {foreach key=FIELD_NAME item=FIELD_MODEL from=${$FIELD_ARRAY} name=blockfields}

                {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                {if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
                {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                {if $COUNTER eq '1'}
                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                </tr>
                <tr>
					{assign var=COUNTER value=0}
                    {/if}
                    {/if}
                    {if $COUNTER eq 4}
				</tr>
				<tr>
					{assign var=COUNTER value=1}
                    {else}
                    {assign var=COUNTER value=$COUNTER+1}
                    {/if}
                    <td class="{$WIDTHTYPE}" style="width: {$LABEL_WIDTH}%">
						{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                            {if $isReferenceField eq "reference"}
                                {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                {if $REFERENCE_LIST_COUNT > 1}
                                    {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                    {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                    {if !empty($REFERENCED_MODULE_STRUCT)}
                                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                    {/if}
                                    <span class="pull-right">
										{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                        <select id="{$GUEST_MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
											<optgroup>
												{foreach key=index item=value from=$REFERENCE_LIST}
                                                    <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $GUEST_MODULE)}</option>
                                                {/foreach}
											</optgroup>
										</select>
									</span>
								{else}
									<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $GUEST_MODULE)}</label>
                                {/if}
                            {elseif $FIELD_MODEL->get('uitype') eq "83"}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$GUEST_MODULE) COUNTER=$COUNTER MODULE=$GUEST_MODULE}
                            {else}
                                {vtranslate($FIELD_MODEL->get('label'), $GUEST_MODULE)}
                            {/if}
                            {if $isReferenceField neq "reference"}</label>{/if}
					</td>
                    {if $FIELD_MODEL->get('uitype') neq "83"}
                        <td class="{$WIDTHTYPE}" style="width: {$FIELD_WIDTH}%" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
							<div class="row-fluid">
								<span>
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$GUEST_MODULE) GUEST_FIELDS=${$FIELD_ARRAY}}
								</span>
							</div>
						</td>
                    {/if}
                    {if ${$FIELD_ARRAY}|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
                    {/foreach}
                    {* adding additional column for odd number of fields in a block *}
                    {*
                    {if ${$FIELD_ARRAY}|@end eq true and ${$FIELD_ARRAY}|@count neq 1 and $COUNTER eq 1}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}*}
				</tr>
			</tbody>
            {assign var=DEFAULT_CHZN value=0}
            {foreach key=RECORD_INDEX item=CURRENT_RECORD from=${$GUEST_LIST}}
                {assign var=RECORD_COUNT value=$RECORD_INDEX+1}
                <tbody class="{$GUEST_MODULE}Block">
					<tr class="fieldLabel" colspan="{$MR_COLUMNS}">
						<td colspan="{$MR_COLUMNS}" class="blockHeader" style="background-color:#E8E8E8;">
							<span class="{$GUEST_MODULE}Title"></span>
							<a style="float: right; padding: 3px"><i title="Delete" class="delete{$GUEST_MODULE} icon-trash"></i></a>
							<input id="{$GUEST_MODULE_LOWER}_id_{$RECORD_COUNT}" type="hidden" name="{$GUEST_MODULE_LOWER}_id_{$RECORD_COUNT}" value="{$CURRENT_RECORD[$ID_COLUMN]}">
							<input id="{$GUEST_MODULE_LOWER}_deleted" type="hidden" name="{$GUEST_MODULE_LOWER}_deleted_{$RECORD_COUNT}" value="none">
						</td>
					</tr>
                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=${$FIELD_ARRAY} name=blockfields}
                    {assign var=CUSTOM_FIELD_NAME value=$FIELD_NAME|cat:"_"|cat:$RECORD_COUNT}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',${$GUEST_LIST}[$RECORD_INDEX][$FIELD_NAME])}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('name',$CUSTOM_FIELD_NAME)}
                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('noncustomname',$FIELD_NAME)}
                    {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                    {if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
                    {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                    {if $COUNTER eq '1'}
                    <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    </tr>
                    <tr>
							{assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        {if $COUNTER eq 4}
						</tr>
						<tr>
							{assign var=COUNTER value=1}
                            {else}
                            {assign var=COUNTER value=$COUNTER+1}
                            {/if}
                            <td class="{$WIDTHTYPE}" style="width: {$LABEL_WIDTH}%">
								{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
                                    {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                                    {if $isReferenceField eq "reference"}
                                        {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                        {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                        {if $REFERENCE_LIST_COUNT > 1}
                                            {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                            {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                            {if !empty($REFERENCED_MODULE_STRUCT)}
                                                {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                            {/if}
                                            <span class="pull-right">
												{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                                <select id="{$GUEST_MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
													<optgroup>
														{foreach key=index item=value from=$REFERENCE_LIST}
                                                            <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $GUEST_MODULE)}</option>
                                                        {/foreach}
													</optgroup>
												</select>
											</span>
										{else}
											<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $GUEST_MODULE)}</label>
                                        {/if}
                                    {elseif $FIELD_MODEL->get('uitype') eq "83"}
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$GUEST_MODULE) COUNTER=$COUNTER MODULE=$GUEST_MODULE}
                                    {else}
                                        {vtranslate($FIELD_MODEL->get('label'), $GUEST_MODULE)}
                                    {/if}
                                    {if $isReferenceField neq "reference"}</label>{/if}
							</td>
                            {if $FIELD_MODEL->get('uitype') neq "83"}
                                <td class="{$WIDTHTYPE}" style="width: {$VALUE_WIDTH}%" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
									<div class="row-fluid">
										<span>
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$GUEST_MODULE) GUEST_FIELDS=${$FIELD_ARRAY}}
										</span>
									</div>
								</td>
                            {/if}
                            {if ${$FIELD_ARRAY}|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                            {/if}
                            {/foreach}
                            {* adding additional column for odd number of fields in a block
                            {if ${$FIELD_ARRAY}|@end eq true and ${$FIELD_ARRAY}|@count neq 1 and $COUNTER eq 1}
                                <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                            {/if} *}
					</tr>
				</tbody>
            {/foreach}
		</table>
        <br>
    {/if}
{/strip}