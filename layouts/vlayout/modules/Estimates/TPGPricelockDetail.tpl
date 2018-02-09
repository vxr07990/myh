{strip}
{*<div class='hide'>
	<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
	<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
	<div id="inline_content" class='details'>
	<div class='contents'>*}
	<div id="inline_content" class='LBL_QUOTES_INTERSTATEMOVEDETAILS'>
	{foreach key=BLOCK_LABEL item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{if $BLOCK_LABEL neq "LBL_QUOTES_VALUATIONDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_SITDETAILS" and $BLOCK_LABEL neq "LBL_QUOTES_ACCESSORIALDETAILS"}{continue}{/if}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value='1'}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS"}
	<table class="table table-bordered equalSplit detailview-table sit accessorials">
		<thead>
			<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
				&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL},{$MODULE_NAME})}
			</th>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		 {if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS"}
			<tr>
				<td class="fieldLabel">
					<label class="muted pull-right marginRight10px">Use Custom Rates - Origin</label>
				</td>
				<td class="fieldValue customSIT" style="text-align:left">
					<span class="value" data-field-type="boolean">{if $CUSTOM_RATES.apply_custom_sit_rate_override == 1}Yes{else}No{/if}</span>
					<span class="hide">
						<input type="hidden" name="apply_custom_sit_rate_override" value="{($CUSTOM_RATES.apply_custom_sit_rate_override)?$CUSTOM_RATES.apply_custom_sit_rate_override:0}">
						<input type="checkbox" name="apply_custom_sit_rate_override" {if $CUSTOM_RATES.apply_custom_sit_rate_override == 1}checked{/if}>
						<input type="hidden" class="fieldname" value="apply_custom_sit_rate_override" data-prev-value="{($CUSTOM_RATES.apply_custom_sit_rate_override)?Yes:No}">
					</span>

				</td>
				<td class="fieldLabel">
					<label class="muted pull-right marginRight10px">Use Custom Rates - Destination</label>
				</td>
				<td class="fieldValue customSIT" style="text-align:left">
					<span class="value" data-field-type="boolean">{if $CUSTOM_RATES.apply_custom_sit_rate_override_dest == 1}Yes{else}No{/if}</span>
					<span class="hide">
						<input type="hidden" name="apply_custom_sit_rate_override_dest"
							   value="{($CUSTOM_RATES.apply_custom_sit_rate_override_dest)?$CUSTOM_RATES.apply_custom_sit_rate_override_dest:0}">

						<input type="checkbox" name="apply_custom_sit_rate_override_dest"
							   {if $CUSTOM_RATES.apply_custom_sit_rate_override_dest == 1}checked{/if}>
						<input type="hidden" class="fieldname" value="apply_custom_sit_rate_override_dest"
							   data-prev-value="{($CUSTOM_RATES.apply_custom_sit_rate_override_dest)?Yes:No}">
					</span>
				</td>
			</tr>
			<tr class='cbxblockhead'>
				<td class='fieldLabel' colspan=2>Origin</td>
				<td class='fieldLabel' colspan=2>Destination</td>
			</tr>
		 {/if}
		{assign var=COUNTER value=0}
		{assign var=SHOW_SHUTTLE_SELF_MINI value=false}
		<tr>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
			{if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
			 {if $FIELD_MODEL->get('uitype') eq "83"}
				{foreach item=tax key=count from=$TAXCLASS_DETAILS}
				{if $tax.check_value eq 1}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var="COUNTER" value=1}
					{else}
						{assign var="COUNTER" value=$COUNTER+1}
					{/if}
					<td class="fieldLabel {$WIDTHTYPE}" style="width:20%">
					<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}</label>
					</td>
					 <td class="fieldValue {$WIDTHTYPE}" style="width:30%">
						 <span class="value">
							 {$tax.percentage}
						 </span>
					 </td>
				{/if}
				{/foreach}
			{elseif $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
				{if $COUNTER neq 0}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
				<td class="fieldValue {$WIDTHTYPE}" style="width:30%">
					<div id="imageContainer" width="300" height="200">
						{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
							{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
								<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
							{/if}
						{/foreach}
					</div>
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{else}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				 {if $COUNTER eq 2}
					 </tr><tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				 {/if}
				{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
					 <td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Shuttle Service</td></tr>
					 <tr class=''><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>{if getenv('INSTANCE_NAME') neq 'sirva'}Destination{/if}</td></tr>
					 <tr>
				{elseif $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
					 <tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;OT Service</td></tr>
					 <tr class=''><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 <tr>
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
					 <tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Self/Mini Stg</td></tr>
					 <tr class=''><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 <tr>
					 {assign var=SHOW_SHUTTLE_SELF_MINI value=true}
				{elseif $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
					 <tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Extra Labor</td></tr>
					 <tr class=''><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 <tr>
				{elseif $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
					 <tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Wait Time</td></tr>
					 <tr class=''><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 <tr>
				{/if}
				{assign var=SHOW_SHUTTLE value=true}
				{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_ot'}
					{assign var=SPEC_CLASS value=' shuttleOriginOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOTHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_ot'}
					{assign var=SPEC_CLASS value=' shuttleDestOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOTHide'}
					{assign var=SHOW_SHUTTLE value=false}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_origin_over25'}
					{assign var=SPEC_CLASS value=' shuttleOriginOver25'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOver25Hide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_over25'}
					{assign var=SPEC_CLASS value=' shuttleDestOver25'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOver25Hide'}
					{assign var=SHOW_SHUTTLE value=false}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_origin_miles'}
					{assign var=SPEC_CLASS value=' shuttleOriginMiles'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginMilesHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_miles'}
					{assign var=SPEC_CLASS value=' shuttleDestMiles'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestMilesHide'}
					{assign var=SHOW_SHUTTLE value=false}
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_ot'}
					{assign var=SPEC_CLASS value=' selfstgOriginOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide selfstgOriginOTHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_dest_ot'}
					{assign var=SPEC_CLASS value=' selfstgDestOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide selfstgDestOTHide'}
					{if !$SHOW_SHUTTLE_SELF_MINI}
						{assign var=SHOW_SHUTTLE value=false}
					{/if}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_applied' || $FIELD_MODEL->getName() eq 'acc_shuttle_dest_weight'}
					{assign var=SPEC_CLASS value=' shuttleDest'}
					{assign var=SHOW_SHUTTLE value=false}
				{else}
					{assign var=SPEC_CLASS value=''}
					{assign var=SPEC_CLASS_HIDE value=' hide'}
				{/if}
				 <td class="fieldLabel {$WIDTHTYPE}{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}{$SPEC_CLASS}{/if}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" sytle="width:20%">
					 {if getenv('INSTANCE_NAME') neq 'sirva' || $SHOW_SHUTTLE}
						 <label class="muted pull-right marginRight10px">
							 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
							 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
								 ({$BASE_CURRENCY_SYMBOL})
							 {/if}
						 </label>
					 {/if}
				 </td>
				 {if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}<td class="fieldLabel {$WIDTHTYPE}{$SPEC_CLASS_HIDE}" style="width:20%"></td>{/if}
				 <td class="fieldValue {$WIDTHTYPE}{$SPEC_CLASS}" style="width:30%" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 {if getenv('INSTANCE_NAME') neq 'sirva' || $SHOW_SHUTTLE}
						 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                        	{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
					 	</span>
					 {/if}
				 </td>
				 {if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}<td class="fieldValue {$WIDTHTYPE}{$SPEC_CLASS_HIDE}" style="width:20%"></td>{/if}
			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		{/foreach}
		{* adding additional column for odd number of fields in a block *}
		{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		</tr>
		</tbody>
	</table>
	{else}
	<table class="table table-bordered equalSplit detailview-table {$BLOCK_LABEL}">
		<thead>
			<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
					<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL]->get('id')}>
					&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL},{$MODULE_NAME})}
			</th>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
			{if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
			 {if $FIELD_MODEL->get('uitype') eq "83"}
				{foreach item=tax key=count from=$TAXCLASS_DETAILS}
				{if $tax.check_value eq 1}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var="COUNTER" value=1}
					{else}
						{assign var="COUNTER" value=$COUNTER+1}
					{/if}
					<td class="fieldLabel {$WIDTHTYPE}" style="width:20%">
					<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}</label>
					</td>
					 <td class="fieldValue {$WIDTHTYPE}" style="width:30%">
						 <span class="value">
							 {$tax.percentage}
						 </span>
					 </td>
				{/if}
				{/foreach}
			{elseif $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
				{if $COUNTER neq 0}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
				<td class="fieldValue {$WIDTHTYPE}" style="width:30%">
					<div id="imageContainer" width="300" height="200">
						{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
							{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
								<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
							{/if}
						{/foreach}
					</div>
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{else}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				 {if $COUNTER eq 2}
					 </tr><tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				 {/if}
				 <td class="fieldLabel {$WIDTHTYPE}" sytle="width:20%" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
					 <label class="muted pull-right marginRight10px">
						 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
							({$BASE_CURRENCY_SYMBOL})
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" style="width:20%" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
					 </span>
				 </td>
			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="fieldValue {$WIDTHTYPE}" style="width:30%"></td>
		{/if}
		{/foreach}
		{* adding additional column for odd number of fields in a block *}
		{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}" style="width:20%"></td><td class="fieldValue {$WIDTHTYPE}" style="width:30%"></td>
		{/if}
		</tr>
		</tbody>
	</table>
	{/if}
	<br>
	{/foreach}
	{assign var=PACK_LABEL_WIDTH value='15%'}
	{assign var=PACK_CSTRATE_WIDTH value='8%'}
	{assign var=PACK_VALUE_WIDTH value='10%'}
	{assign var=IS_HIDDEN value='1'}
	<table class='table table-bordered detailview-table packing' data-tariff-type="{$TARIFF_TYPE}">
		<thead>
			<tr>
				<th class='blockHeader' colspan='6'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Packing
			</th>
			</tr>
		</thead>
		<tbody id='packingTab'{if $IS_HIDDEN} class="hide" {/if}>
			{if in_array($TARIFF_TYPE, $CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES)}
			<tr>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel">
					<label class="muted pull-right marginRight10px">Use Custom Rates</label>
				</td>
				<td class="fieldValue customPacking" style="text-align:left">
					<span class="value" data-field-type="boolean">{if $CUSTOM_RATES.apply_custom_pack_rate_override == 1}Yes{else}No{/if}</span>
					<span class="hide">
						<input type="hidden" name="apply_custom_pack_rate_override" value="{($CUSTOM_RATES.apply_custom_pack_rate_override)?$CUSTOM_RATES.apply_custom_pack_rate_override:0}">
						<input type="checkbox" name="apply_custom_pack_rate_override" {if $CUSTOM_RATES.apply_custom_pack_rate_override == 1}checked{/if}>
						<input type="hidden" class="fieldname" value="apply_custom_pack_rate_override" data-prev-value="{($CUSTOM_RATES.apply_custom_pack_rate_override)?Yes:No}">
					</span>
				</td>
				<td colspan="2" class="fieldValue packingCustomRate" style="text-align:left">
					<button type='button' name="LoadTariffPacking" class="hide">Load Tariff Packing</button>
				</td>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel">
					<label class="muted pull-right marginRight10px">&nbsp;</label>
				</td>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel">
					<label class="muted pull-right marginRight10px">&nbsp;</label>
				</td>
			</tr>
			{/if}
			<tr>
				<td style='width:{$PACK_LABEL_WIDTH}' class="fieldLabel"></td>
				<td style='width:{$PACK_VALUE_WIDTH};text-align:center;' class="fieldValue ContCol">Containers</td>
				<td style='width:{$PACK_CSTRATE_WIDTH};text-align:center;' class="fieldValue packingCustomRate">Custom Rate</td>
				<td style='width:{$PACK_VALUE_WIDTH};text-align:center;' class="fieldValue PkCol">Packing</td>
				<td style='width:{$PACK_CSTRATE_WIDTH};text-align:center;' class="fieldValue packingPackRate">Pack Rate</td>
				<td style='width:{$PACK_VALUE_WIDTH};text-align:center;' class="fieldValue UnpkCol">Unpacking</td>
			</tr>
			{assign var=COUNTER value=0}
			{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
			<tr>
				{assign var=COUNTER value=$COUNTER+1}
				<td style='padding:0 5px 0 0;width:{$PACK_LABEL_WIDTH};' class='fieldLabel {$WIDTHTYPE}'>
					<label class="muted pull-right marginRight10px">
						{$PACKING_ITEM.label}
					</label>
				</td>
				<td style="text-align:center;width:{$PACK_VALUE_WIDTH};" class="fieldValue {$WIDTHTYPE} ContCol">
					<span class="value" data-field-type="string">{($PACKING_ITEM.cont)?$PACKING_ITEM.cont:0}</span>
				</td>
				<td style='width:{$PACK_CSTRATE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE} packingCustomRate'>
					<span class="value" data-field-type="number">
						{number_format($PACKING_ITEM.customRate, 2, '.', '')}
					</span>
				</td>
				<td style='text-align:center;width:{$PACK_VALUE_WIDTH};' class='fieldValue {$WIDTHTYPE} PkCol'>
					<span class='value' data-field-type='string'>{$PACKING_ITEM.pack}</span>
				</td>
				<td style='width:{$PACK_CSTRATE_WIDTH}; text-align:center;' class='fieldValue {$WIDTHTYPE} packingPackRate'>
					<span class="value" data-field-type="number">
						{number_format($PACKING_ITEM.packRate, 2, '.', '')}
					</span>
				</td>
				<td style='text-align:center;width:{$PACK_VALUE_WIDTH};' class='fieldValue {$WIDTHTYPE} UnpkCol'>
					<span class='value' data-field-type='string'>{$PACKING_ITEM.unpack}</span>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />

	<table class='table table-bordered equalSplit detailview-table packing otPacking'>
		<thead>
			<th class='blockHeader' colspan='9'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;OT Packing
			</th>
		</thead>
		{assign var=PACK_VALUE_WIDTH value='15%'}
		<tbody id='otPackingTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
				<td class="fieldLabel" style="width:{$PACK_LABEL_WIDTH}"></td>
				<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Pk</td>
				<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Unpk</td>
				<td class="fieldLabel" style="width:{$PACK_LABEL_WIDTH}"></td>
				<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Pk</td>
				<td class="fieldValue" style="width:{$PACK_VALUE_WIDTH}">Unpk</td>
			</tr>
			{assign var=COUNTER value=0}
			<tr>
			{foreach item=PACKING_ITEM key=ITEM_NUM from=$PACKING_ITEMS}
			{if $COUNTER eq 2}
			</tr>
			<tr>
			{assign var=COUNTER value=1}
			{else}
				{assign var=COUNTER value=$COUNTER+1}
			{/if}
				<td style='padding:0 5px 0 0;width:{$PACK_LABEL_WIDTH};' class='fieldLabel {$WIDTHTYPE}'>
					<label class="muted pull-right marginRight10px">
						{$PACKING_ITEM.label}
					</label>
				</td>
				<td style='text-align:center;width:{$PACK_VALUE_WIDTH};' class='fieldValue {$WIDTHTYPE}'>
					<span class='value' data-field-type='string'>{$PACKING_ITEM.otpack}</span>
				</td>
				<td style='text-align:center;width:{$PACK_VALUE_WIDTH};' class='fieldValue {$WIDTHTYPE}'>
					<span class='value' data-field-type='string'>{$PACKING_ITEM.otunpack}</span>
				</td>
			{/foreach}
			{while $COUNTER lt 2}
				<td style='padding:0 5px 0 0;width:{$PACK_LABEL_WIDTH};' class='fieldLabel {$WIDTHTYPE}'>
					&nbsp;
				</td>
				<td class='fieldValue {$WIDTHTYPE}' style="width:{$PACK_VALUE_WIDTH}">
					&nbsp;
				</td>
				<td class='fieldValue {$WIDTHTYPE}' style="width:{$PACK_VALUE_WIDTH}">
					&nbsp;
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{/while}
			</tr>
		</tbody>
	</table>
	<br />

	<table class='table table-bordered equalSplit detailview-table bulky'>
		<thead>
			{assign var=BULKY_TABLE_LABEL value = 'BULKY_TABLE_LABEL'}
			<th class='blockHeader' colspan='8'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;{vtranslate({$BULKY_TABLE_LABEL},{$MODULE_NAME})}
			</th>
		</thead>
		<tbody id='bulkyItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
			{assign var=COUNTER value=0}
			<tr>
			{foreach item=BULKY_ITEM key=ITEM_NUM from=$BULKY_ITEMS}
			{if $COUNTER eq 4}
			</tr>
			<tr>
			{assign var=COUNTER value=1}
			{else}
				{assign var=COUNTER value=$COUNTER+1}
			{/if}
				<td style=' padding:0 5px 0 0;width:15%;' class='fieldLabel {$WIDTHTYPE}'>
					<label class="muted pull-right marginRight10px">
						{$BULKY_ITEM.label}
					</label>
				</td>
				<td style='text-align:center;width:10%;' class='fieldValue {$WIDTHTYPE}'>
					<span class='value' data-field-type='string'>{$BULKY_ITEM.qty}</span>
				</td>
			{/foreach}
			{while $COUNTER lt 4}
				<td style='padding:0 5px 0 0;width:15%;' class='fieldLabel {$WIDTHTYPE}'>
					&nbsp;
				</td>
				<td class='{$WIDTHTYPE}' style="width:10%">
					&nbsp;
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{/while}
			</tr>
		</tbody>
	</table>
	<br />

	<table class='table table-bordered equalSplit detailview-table flatCharge'>
		<thead>
			<th class='blockHeader' colspan='5'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Flat Charge Details
			</th>
		</thead>
		<tbody id='flatItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
				{assign var=TRASH_WIDTH value=5}
				{assign var=FLAT_CHARGE_TD_WIDTH value=95/4}
				<td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class="redColor">*</span><b>Description</b>
				</td>
				<td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class="redColor">*</span><b>Charge</b>
				</td>
				<td class="fieldLabel" style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<b>Discountable</b>
				</td>
			</tr>
			<tr class='hide defaultFlatItem flatItemRow newItemRow'>
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>&nbsp;</span>
				</td>
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>0.00</span>
				</td>
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>No</span>
				</td>
			</tr>
			{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.flat}
			<tr class='flatItemRow' id='flatItemRow{$ROW_NUM}'>
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->description}</span>
				</td>
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->charge}</span>
				</td>
				<td class='fieldValue' style="width:{$FLAT_CHARGE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->discounted eq '0'}No{else}Yes{/if}</span>
				</td>
				<input type='hidden' class='lineItemId' value='{$MISC_CHARGE_ROW->lineItemId}' />
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />

	<table class='table table-bordered equalSplit detailview-table qtyRate'>
		<thead>
			<th class='blockHeader' colspan='6'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Qty/Rate Details
			</th>
		</thead>
		<tbody id='qtyRateItemsTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
				{assign var=RATE_DETAILS_TD_WIDTH value=25}
				<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class="redColor">*</span><b>Description</b>
				</td>
				<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class="redColor">*</span><b>Rate</b>
				</td>
				<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class="redColor">*</span><b>Qty</b>
				</td>
				<td class="fieldLabel" style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<b>Discountable</b>
				</td>
			</tr>
			<tr class='hide defaultQtyRateItem qtyRateItemRow newItemRow'>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='string'>&nbsp;</span>
				</td>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='string'>0.00</span>
				</td>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='string'>1</span>
				</td>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>No</span>
				</td>
			</tr>
			{foreach item=MISC_CHARGE_ROW key=ROW_NUM from=$MISC_CHARGES.qty}
			<tr class='qtyRateItemRow' id='qtyRateItemRow{$ROW_NUM}'>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->description}</span>
				</td>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->charge}</span>
				</td>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$MISC_CHARGE_ROW->qty}</span>
				</td>
				<td class='fieldValue' style="width:{$RATE_DETAILS_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $MISC_CHARGE_ROW->discounted eq '0'}No{else}Yes{/if}</span>
				</td>
				<input type='hidden' class='lineItemId' value='{$MISC_CHARGE_ROW->lineItemId}' />
			</tr>
			{/foreach}
		</tbody>
	</table>
	<br />

	<table class='table table-bordered equalSplit detailview-table crating'>
		<thead>
			<th class='blockHeader' colspan='10'>
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;Crate Details
			</th>
		</thead>
		<tbody id='cratesTab'{if $IS_HIDDEN} class="hide" {/if}>
			<tr>
				<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
					&nbsp;
				</td>
				<td style='text-align:center; background-color:#E8E8E8;' colspan='3'>
					<b>Dimensions (in)</b>
				</td>
				<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
					<b>Pack</b>
				</td>
				<td style='text-align:center; background-color:#E8E8E8;' colspan='2'>
					<b>OT Pack</b>
				</td>
				<td style='text-align:center; background-color:#E8E8E8;'>
					&nbsp;
				</td>
			</tr>
			<tr>
				{assign var=INTERSTATE_CRATE_TD_WIDTH value=10}
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
					<b>ID</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
					<span class="redColor">*</span><b>Description</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class="redColor">*</span><b>L</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class="redColor">*</span><b>W</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class="redColor">*</span><b>H</b>
				</td>
				<td class="fieldLabel" style="text-align:center; width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Cube</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Pk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Unpk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Pk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Unpk</b>
				</td>
				<td class="fieldLabel" style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<b>Disc</b>
				</td>
			</tr>
			{foreach item=CRATE_ROW key=ROW_NUM from=$CRATES}
			<tr class='crateRow' id='crateRow{$ROW_NUM}'>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH-5}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateid}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH+5}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->description}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateLength}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateWidth}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->crateHeight}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='string'>{$CRATE_ROW->cube}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $CRATE_ROW->pack eq '0'}No{else}Yes{/if}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $CRATE_ROW->unpack eq '0'}No{else}Yes{/if}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $CRATE_ROW->otpack eq '0'}No{else}Yes{/if}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='boolean'>{if $CRATE_ROW->otunpack eq '0'}No{else}Yes{/if}</span>
				</td>
				<td class='fieldValue' style="width:{$INTERSTATE_CRATE_TD_WIDTH}%">
					<span class='value' data-field-type='double'>{$CRATE_ROW->discount}</span>
				</td>
				<input type='hidden' class='lineItemId' value='{$CRATE_ROW->lineItemId}' />
			</tr>
			{/foreach}
		</tbody>
	</table>
	</div>
{if 1==1}
{if
$TARIFF_TYPE == 'ALLV-2A' ||
$TARIFF_TYPE == 'NAVL-12A' ||
$TARIFF_TYPE == '400N Base' ||
$TARIFF_TYPE == '400N/104G' ||
$TARIFF_TYPE == '400NG'
}
	{include file=vtemplate_path('CorporateVehiclesDetail.tpl',$MODULE)}
{else}
<table class='table table-bordered blockContainer showInlineTable misc equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='6'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
		<tr>
			<td>
				<b>Description</b>
			</td>
			<td>
				<b>Weight</b>
			</td>
			<td>
				<b>Make</b>
			</td>
			<td>
				<b>Model</b>
			</td>
			<td>
				<b>Year</b>
			</td>
		</tr>
		{foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
		<tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["description"]}</span>
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_Id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["weight"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["make"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["model"]}</span>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<span class='value' data-field-type='string'>{$VEHICLE_ROW["year"]}</span>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}
{/if}
{if 1==0}<!-- TODO: This section needs the correct values from $VEHICLES -->
<table class='table table-bordered blockContainer showInlineTable misc equalSplit detailview-table'>
	<thead>
		<th class='blockHeader' colspan='5'>
			<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
			<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
			&nbsp;&nbsp;Vehicles
		</th>
	</thead>
	<tbody id='vehiclesTab'{if $IS_HIDDEN} class="hide" {/if}>
		{if !$LOCK_RATING}
			<tr>
				<td colspan='5' style='padding:0'>
					<button type='button' id='addVehicle'>+</button>
					<button type='button' id='addVehicle2' style='clear:right;float:right;'>+</button><br />
				</td>
			</tr>
		{/if}
		<tr>
			<td style='width:5%'>
				&nbsp;
			</td>
			<td style='width:30%'>
				<span class="redColor">*</span><b>Rating Type</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>Description</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>SIT Days</b>
			</td>
			<td style='width:25%'>
				<span class="redColor">*</span><b>Weight</b>
			</td>
		</tr>
		<tr class='hide vehicleItem vehicleRow newVehicleRow'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteVehicleButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<select name='vehicleRateType'>
					<option value='weight'>
						Actual Weight
					</option>
					<option value='mileage'>
						Mileage Rate
					</option>
					<option value='point'>
						Point-to-Point
					</option>
				</select>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleDescription' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleSITDays' value=''/>
				</div>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-medium' style='float:left' name='vehicleWeight' value=''/>
				</div>
			</td>
		</tr>

		{foreach item=VEHICLE_ROW key=ROW_NUM from=$VEHICLES}
		<tr class='vehicleItem vehicleRow' id='vehicleRow-{$ROW_NUM}'>
			<td class='fieldValue' style='text-align:center'>
				<a class="deleteMiscChargeButton">
					<i title="Delete" class="icon-trash alignMiddle"></i>
				</a>
			</td>

			<td class='fieldValue' style='text-align:center'>
				<select name='vehicleRateType-{$ROW_NUM}'>
					<option value='weight'>
						Actual Weight
					</option>
					<option value='mileage'>
						Mileage Rate
					</option>
					<option value='point'>
						Point-to-Point
					</option>
				</select>
			</td>

			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleDescription-{$ROW_NUM}' value='{$VEHICLE_ROW["description"]}'/>
				<input type='hidden' name='vehicleID-{$ROW_NUM}' value='{$VEHICLE_ROW["vehicle_Id"]}'>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<input type='text' class='input-large' style='width:90%' name='vehicleSITDays-{$ROW_NUM}' value='{$VEHICLE_ROW["sit_days"]}'/>
			</td>
			<td class='fieldValue' style='text-align:center'>
				<div class='input-prepend input-prepend-centered'>
					<input type='text' class='input-large' style='width:90%' name='vehicleWeight-{$ROW_NUM}' value='{$VEHICLE_ROW["weight"]}' />
				</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}
{/strip}
