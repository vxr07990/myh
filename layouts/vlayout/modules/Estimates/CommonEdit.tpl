{strip}
    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
        {if $BLOCK_SUBLIST && !array_key_exists($BLOCK_LABEL, $BLOCK_SUBLIST)}
            {continue}
        {/if}
        {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
        {assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST[$BLOCK_LABEL])}
        <div id="contentHolder_{$BLOCK_LABEL}" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
        {if $HAS_CONTENT}
        <table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if} block_{$BLOCK_LABEL}">
			<thead>
				<tr>
					<th class="blockHeader" colspan="40">
						 <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
						 <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
                        {vtranslate($BLOCK_LABEL, $MODULE)}
					</th>
				</tr>
			</thead>
            <tbody {if $IS_HIDDEN}class="hide"{/if}>
        <tr>
        {assign var=COUNTER value=0}
        {if $BLOCK_LABEL == 'LBL_ESTIMATES_DATES'}
            {assign var=COUNTER_LOOP value=3}
            {assign var=LABEL_STYLE value="width: 14%"}
            {assign var=VALUE_STYLE value="width: 19%"}
        {else}
            {assign var=COUNTER_LOOP value=2}
            {assign var=LABEL_STYLE value=""}
            {assign var=VALUE_STYLE value=""}
        {/if}
            {assign var=LABEL_ATTR value=" class=\"fieldLabel {$WIDTHTYPE}\" style=\"{$LABEL_STYLE}\" "}
            {assign var=VALUE_ATTR value=" class=\"fieldValue {$WIDTHTYPE}\" style=\"{$VALUE_STYLE}\" "}

            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}

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
                    {if $COUNTER eq $COUNTER_LOOP}
                        </tr>
                        <tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}
                    {/if}
                            <td {$LABEL_ATTR}>
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
                                                <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
                                    <optgroup>
                                        {foreach key=index item=value from=$REFERENCE_LIST}
                                            <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                        {/foreach}
                                    </optgroup>
                                </select>
                            </span>
                        {else}
                            <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                        {/if}
                                    {elseif $FIELD_MODEL->get('uitype') eq "83"}
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                                    {else}
                                        {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                    {/if}
                                    {if $isReferenceField neq "reference"}</label>{/if}
            </td>
                            {if $FIELD_MODEL->get('uitype') neq "83"}
                                <td {$VALUE_ATTR} {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                    <div class="row-fluid">
                        <span class="span10">
                            {if $FIELD_MODEL->get('label') == 'LBL_QUOTES_EFFECTIVE_TARIFF'}
                                {include file=vtemplate_path('EffectiveTariffPicklist.tpl','Estimates') BLOCK_FIELDS=$BLOCK_FIELDS}
                            {elseif $FIELD_NAME == 'valuation_amount' && getenv('INSTANCE_NAME') == 'sirva'}
                                {include file=vtemplate_path('ValuationAmountPicklist.tpl',$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                            {else}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                            {/if}
                            {if $FIELD_NAME eq 'load_date' && $SHOW_TRANSIT_GUIDE}
                                <span id="TransitGuide">
									<button type="button" class="transitGuide" name="transitGuide"><strong>{vtranslate('LBL_TRANSIT_GUIDE', $MODULE)}</strong></button>
                                </span>
                            {/if}
                        </span>
                    </div>
                </td>
                            {/if}
                            {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                            {/if}
                            {if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
                                {include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
                            {/if}
                            {/foreach}
                            {* adding additional column for odd number of fields in a block *}
                            {while $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER < $COUNTER_LOOP}
                                <td {$LABEL_ATTR}></td><td {$VALUE_ATTR}></td>
                                {assign var=COUNTER value=$COUNTER+1}
                            {/while}
        </tr>
                {if $BLOCK_LABEL == 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
                    <tr><td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if getenv('INSTANCE_NAME') eq 'sirva'}&nbsp;
                                <button type='button' class='requote'>Re-Quote</button>
                            {else}&nbsp;<!--<button type='button' id='interstateRateQuick'>Quick Rate Estimate</button>-->{/if}
						</td>
						<td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if !$LOCK_RATING}
                                <button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button>
                            {/if}
						</td>
					</tr>
                {/if}
                {if $BLOCK_LABEL == 'LBL_QUOTES_LOCALMOVEDETAILS'}
                    <tr><td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if getenv('INSTANCE_NAME') eq 'graebel'
                                || getenv('INSTANCE_NAME') eq 'sirva'}&nbsp;
                                <button type='button' class='localRateMileage'>Get Mileage</button>
                            {/if}
						</td>
						<td class='fieldLabel'></td>
						<td class='fieldValue'>
							{if !$LOCK_RATING}
                                <button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button>
                            {/if}
						</td>
					</tr>
                {/if}

</tbody>
        </table>
        <br>
            {include file=vtemplate_path('SequencedGuestEditBlocks.tpl', $MODULE) BLOCK_LABEL=$BLOCK_LABEL}
        {/if}
    </div>
    {/foreach}
        {include file=vtemplate_path('GuestEditBlocks.tpl', $MODULE)}
{/strip}
