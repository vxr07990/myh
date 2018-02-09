{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['INTERSTATE_SERVICES'])}
<div id="contentHolder_INTERSTATE_SERVICES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{*<div class='hide'>
	<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
	<div id="inline_content" class='details'>
	<div class='contents'>*}
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
		{if !$BLOCK_LABEL_KEY|in_array:$ALLOWED_BLOCKS}
			{continue}
		{/if}

		{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value='1'}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<input type="hidden" name="currentBrand" value="{$CURRENT_BRAND}" />
	{if $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS"}
	<table {if $BLOCK_LABEL_KEY eq 'LBL_QUOTES_ACCESSORIALDETAILS'}id='acc_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_SITDETAILS'}id='sit_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_VALUATION'}id='valuation_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_SITDETAILS2'}id='sit2_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_STAIR'}id='stair_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_LONGCARRY'}id='longcarry_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_ELEVATOR'}id='elevator_table' {/if}class="table table-bordered equalSplit detailview-table{if $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS"} sit{/if}">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</th>
		</tr>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		 {if $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS"}
			<tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
		 {/if}
		{assign var=COUNTER value=0}
		{assign var=ROW_COUNT value=1}
		<tr{if $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS"} id='shuttleRow_{$ROW_COUNT}'{/if}>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
			{if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
			{if getenv('INSTANCE_NAME') == 'sirva'}
				{if $FIELD_MODEL->getName() eq 'acc_ot_origin_weight' || $FIELD_MODEL->getName() eq 'acc_ot_dest_weight' || $FIELD_MODEL->getName() eq 'acc_ot_origin_applied' || $FIELD_MODEL->getName() eq 'acc_ot_dest_applied'}{continue}{/if}
			{/if}
			{if getenv('INSTANCE_NAME') == 'graebel'}
				{if $FIELD_NAME eq 'accesorial_ot_packing' OR $FIELD_NAME eq 'accesorial_ot_unpacking' OR $FIELD_NAME eq 'accessorial_space_reserve_bool' OR $FIELD_NAME eq 'acc_day_certain_fee' OR $FIELD_NAME eq 'acc_day_certain_pickup'}{continue}{/if}
				{if $FIELD_NAME eq 'acc_ot_origin_weight' || $FIELD_NAME eq 'acc_ot_dest_weight' || $FIELD_NAME eq 'acc_ot_origin_applied' || $FIELD_MODEL->getName() eq 'acc_ot_dest_applied'}{continue}{/if}
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

				{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
					 {assign var=ROW_HAS_CLASS value=true}
					 {assign var=ROW_CLASS value='shuttleRow'}
					 {assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
					 {assign var=ROW_HAS_CLASS value=true}
					 {assign var=ROW_CLASS value='otServiceRow'}
					 {assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
					 {assign var=ROW_HAS_CLASS value=true}
					 {assign var=ROW_CLASS value='selfStgRow'}
					 {assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
					 {assign var=ROW_HAS_CLASS value=true}
					 {assign var=ROW_CLASS value='exLaborRow'}
					 {assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
					 {assign var=ROW_HAS_CLASS value=true}
					 {assign var=ROW_CLASS value='waitRow'}
					 {assign var=ROW_COUNT value=0}
				{else if $FIELD_MODEL->getName() eq 'bulky_article_changes'}
					{assign var=ROW_HAS_CLASS value=true}
					{assign var=ROW_CLASS value='bulkyArticleRow'}
					{assign var=ROW_COUNT value=0}
				{else if $FIELD_MODEL->getName() eq 'rush_shipment_fee' ||
				($FIELD_MODEL->getName() eq 'accesorial_ot_loading')}
					{assign var=ROW_HAS_CLASS value=true}
					{assign var=ROW_CLASS value='pricingRow'}
					{assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'gsa500_supervisory_hours_origin_regular'}
					{assign var=ROW_HAS_CLASS value=true}
					{assign var=ROW_CLASS value='gsa500SuperRow'}
					{assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'gsa500_extra_driver_hours'}
					{assign var=ROW_HAS_CLASS value=true}
					{assign var=ROW_CLASS value='gsa500DriverRow'}
					{assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'gsa500_washing_machine_employee'}
					{assign var=ROW_HAS_CLASS value=true}
					{assign var=ROW_CLASS value='gsa500MachineRow'}
					{assign var=ROW_COUNT value=0}
				{elseif $FIELD_MODEL->getName() eq 'acc_debris_reg'}
					{assign var=ROW_HAS_CLASS value=true}
					{assign var=ROW_CLASS value='debrisRow'}
					{assign var=ROW_COUNT value=0}
				{/if}

				 {if $COUNTER eq 2 || $ROW_COUNT eq 0}
					 {if $COUNTER eq 1}
						 <td class='fieldLabel'></td><td></td></tr>
					 {else}
						 {assign var=COUNTER value=1}
					 {/if}
					{assign var=ROW_COUNT value=$ROW_COUNT+1}
					</tr><tr{if $ROW_HAS_CLASS} id='{$ROW_CLASS}_{$ROW_COUNT}'{/if}>
				 {else}
					 {assign var=COUNTER value=$COUNTER+1}
				 {/if}
				{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
					 <td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Shuttle Service</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
					<td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;OT Service</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
					 <td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Self/Mini Stg</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
					 <td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Extra Labor</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
					 <td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Wait Time</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'bulky_article_changes'}
					 {if $COUNTER eq 2}
					 <td class='fieldLabel'></td><td></td></tr>
					 {assign var=COUNTER value=1}
					 {/if}
					<tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Bulky Article Changes</td></tr>
					<tr>
				{elseif $FIELD_MODEL->getName() eq 'rush_shipment_fee' ||
					($FIELD_MODEL->getName() eq 'accesorial_ot_loading')}
					<tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Pricing</td></tr>
					<tr>
				{elseif $FIELD_MODEL->getName() eq 'gsa500_supervisory_hours_origin_regular'}
						<td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Supervisory Personnel</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'gsa500_extra_driver_hours'}
					<td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Extra Driver</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'gsa500_washing_machine_employee'}
					<td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Front Load Washing Machines and Associated Pedestals</td></tr>
					 {assign var=ROW_COUNT value=$ROW_COUNT+1}
					 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
				{elseif $FIELD_MODEL->getName() eq 'acc_debris_reg'}
					<tr><td style='background-color:#E8E8E8;' colspan=4>&nbsp;&nbsp;&nbsp;Debris Removal/Minimum Unpacking</td></tr>
					<tr>
				{/if}
				{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_ot'}
					{assign var=SPEC_CLASS value=' shuttleOriginOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOTHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_ot'}
					{assign var=SPEC_CLASS value=' shuttleDestOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOTHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_origin_over25'}
					{assign var=SPEC_CLASS value=' shuttleOriginOver25'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOver25Hide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_over25'}
					{assign var=SPEC_CLASS value=' shuttleDestOver25'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOver25Hide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_origin_miles'}
					{assign var=SPEC_CLASS value=' shuttleOriginMiles'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginMilesHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_shuttle_dest_miles'}
					{assign var=SPEC_CLASS value=' shuttleDestMiles'}
					{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestMilesHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_origin_ot'}
					{assign var=SPEC_CLASS value=' selfstgOriginOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide selfstgOriginOTHide'}
				{elseif $FIELD_MODEL->getName() eq 'acc_selfstg_dest_ot'}
					{assign var=SPEC_CLASS value=' selfstgDestOT'}
					{assign var=SPEC_CLASS_HIDE value=' hide selfstgDestOTHide'}
				{else}
					{assign var=SPEC_CLASS value=''}
					{assign var=SPEC_CLASS_HIDE value=' hide'}
				{/if}
				 <td class="fieldLabel {$WIDTHTYPE}{$SPEC_CLASS}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" style="width:20%">
					 <label class="muted pull-right marginRight10px">
						 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
							({$BASE_CURRENCY_SYMBOL})
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue preservePlace {$WIDTHTYPE}{$SPEC_CLASS}" style="width:30%" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
					 </span>
				 </td>
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
	<table {if $BLOCK_LABEL_KEY eq 'LBL_QUOTES_ACCESSORIALDETAILS'}id='acc_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_SITDETAILS'}id='sit_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_VALUATION'}id='valuation_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_SITDETAILS2'}id='sit2_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_STAIR'}id='stair_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_LONGCARRY'}id='longcarry_table' {elseif $BLOCK_LABEL_KEY eq 'LBL_QUOTES_ELEVATOR'}id='elevator_table' {/if}class="table table-bordered equalSplit detailview-table{if $BLOCK_LABEL_KEY eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL_KEY eq "LBL_QUOTES_ACCESSORIALDETAILS"} sit{/if}">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</th>
		</tr>
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
				 <td class="fieldLabel {$WIDTHTYPE}" style="width:20%" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
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
	<br/>
	{/foreach}
{*	</div>
	</div>
	<div id='reportContent' class='details'>
	</div>
</div>*}
{/if}
</div>
{/strip}
