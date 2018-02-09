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
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

<div class="modelContainer">
<div class="modal-header contentsBackground">
	<button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
    <h3>{vtranslate('LBL_QUICK_CREATE', $MODULE)} {vtranslate($SINGLE_MODULE, $MODULE)}</h3>
</div>
<form class="form-horizontal recordEditView" name="QuickCreate" method="post" action="index.php">
	{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
		<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
	{/if}
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="action" value="SaveAjax">
	<div class="quickCreateContent">
		<div class="modal-body">
			<table class="massEditTable table table-bordered">
				<tr>
				{assign var=COUNTER value=0}
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$RECORD_STRUCTURE name=blockfields}
					{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
					{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
					{assign var="refrenceListCount" value=count($refrenceList)}
                    {if $FIELD_MODEL->get('uitype') eq "19"}
                        {if $COUNTER eq '1'}
                            <td></td><td></td></tr><tr>
                            {assign var=COUNTER value=0}
                        {/if}
                    {/if}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
					<td class='fieldLabel'>
						{if $isReferenceField neq "reference"}<label class="muted pull-right">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
						{if $isReferenceField eq "reference"}
							{if $refrenceListCount > 1}
								{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
								{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
								{if !empty($REFERENCED_MODULE_STRUCT)}
									{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
								{/if}
								<span class="pull-right">
									{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
									<select style="width: 150px;" class="chzn-select referenceModulesList" id="referenceModulesList">
										<optgroup>
											{foreach key=index item=value from=$refrenceList}
												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if} >{vtranslate($value, $value)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
								<label class="muted pull-right">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
							{/if}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
					</td>
					<td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
					</td>
				{/foreach}
				{if $COUNTER eq 2}</tr><tr>{assign var=COUNTER value=0}{/if}
				<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate('LBL_QUOTES_EFFECTIVETARIFF', $MODULE)}</label></td>
				<td class="fieldValue {$WIDTHTYPE}">
					<select class='chzn-select' name='effective_tariff'>
						{foreach item=TARIFF key=INDEX from=$ASSIGNED_TARIFFS}
							<option value="{$INDEX}" {if $INDEX eq $EFFECTIVE_TARIFF}selected{/if}>{$TARIFF["tariff_name"]}</option>
						{/foreach}
					</select>
				</td>
				{assign var=COUNTER value=$COUNTER+1}
				{if $COUNTER lt 2}
					<td class='fieldLabel'>&nbsp;</td>
					<td></td>
				{/if}
				</tr>
			</table>
		</div>
	</div>
	<div class="modal-footer quickCreateActions">
		{assign var="EDIT_VIEW_URL" value=$MODULE_MODEL->getCreateRecordUrl()}
		<a class="cancelLink cancelLinkContainer pull-right" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
		<button class="btn" id="goToFullForm" data-edit-view-url="{$EDIT_VIEW_URL}" type="button"><strong>{vtranslate('LBL_GO_TO_FULL_FORM', $MODULE)}</strong></button>
		<button class="btn" type='button' id="quickEstimateCreate" onclick="quickCreateRate()"><strong>Quick Rate Estimate</strong></button>
		<label id='quickCreateRateLabel'>Rate Estimate: <span id='quickCreateRateSpan' style='width:20%'><input type='text' id='quickEstimateRate' style='width:15%' value readonly /></span></label>
		<div id='processingInterval' style='text-align:center;width:100%;margin:0 auto;display:block;'></div>
	</div>
	<input type='hidden' name='ratingReturn' value />
</form>
</div>
<!-- Before script tag -->
{literal}
<script type='text/javascript'>
	var thisInstance = this;
	var intervalCount = 0;
	var ratingReturn = '';
	function quickCreateRate() {
		var quickCreate = jQuery('.quickCreateContent');

		var weight = quickCreate.find('input[name="weight"]').val();
        quickCreate.find('[name="valuation_amount"]').val(weight * 6);

		var pickupDateTime = getCurrentDate()+'T12:00:00';
		var originZip = quickCreate.find('input[name="origin_zip"]').val();
		var destinationZip = quickCreate.find('input[name="destination_zip"]').val();
		var fuelPrice = quickCreate.find('[name="accesorial_fuel_surcharge"]').val();
		var fullPackApplied = quickCreate.find('input[name="full_pack"]').is(':checked');
		var fullUnpackApplied = quickCreate.find('input[name="full_unpack"]').is(':checked');
		var bottomLineDiscount = quickCreate.find('input[name="bottom_line_discount"]').val();
		if(bottomLineDiscount == '') {bottomLineDiscount = 0;}
		var valDeductibleValue = quickCreate.find('[name="valuation_deductible"]').val();
		var valuationAmount = quickCreate.find('[name="valuation_amount"]').val();

        if($('[name="instance"]').val() == 'sirva') {
            var flatSMF = quickCreate.find('[name="flat_smf"]').val();
            var percentSMF = quickCreate.find('[name="percent_smf"]').val();
        }

		var selectElement = quickCreate.find('select[name="effective_tariff"]');
		var selectId = selectElement.attr('id');
		var chosenOption = selectElement.siblings('.chzn-container').find('.result-selected').attr('id');
		var effective_tariff = selectElement.find('option:eq('+chosenOption.split('_')[3]+')').val();

		//Validation
		var errorExists = false;
		var errorNum = 1;
		var errorString = 'The following errors have prevented creation of the rate estimate:\n';

		if(weight <= 0 || weight.length == 0) {errorString += errorNum + ") Weight must be greater than 0.\n"; errorExists = true; errorNum++;}
		if(originZip.length < 5) {errorString += errorNum + ") Origin Zip must be valid.\n"; errorExists = true; errorNum++;}
		if(destinationZip.length < 5) {errorString += errorNum + ") Destination Zip must be valid.\n"; errorExists = true; errorNum++;}
		if(bottomLineDiscount < 0) {errorString += errorNum + ") Bottom Line Discount must be non-negative.\n"; errorExists = true; errorNum++;}
		if(valuationAmount.length == 0 || valuationAmount < 0) {errorString += errorNum + ") Valuation Amount must be set.\n"; errorExists = true; errorNum++;}
		if(effective_tariff.length == 0) {errorString += errorNum + ") Effective Tariff must be set.\n"; errorExists = true; errorNum++;}

		if(errorExists) {alert(errorString); return;}
		//Validation Complete

		jQuery('.quickCreateActions').children().hide();
		jQuery('.quickCreateActions').progressIndicator();
        jQuery('#processingInterval').show();
		var timer = setInterval(processingRate, 2000);
		jQuery('#processingInterval').html('<strong>Preparing Rating Details</strong>');
		var dataURL = "index.php?module=Estimates&action=GetRateEstimate&weight="+weight+"&pickupDateTime="+pickupDateTime+"&originZip="+originZip+"&destinationZip="+destinationZip+"&fuelPrice="+fuelPrice+"&fullPackApplied="+fullPackApplied+"&fullUnpackApplied="+fullUnpackApplied+"&bottomLineDiscount="+bottomLineDiscount+"&valDeductible="+valDeductibleValue+"&valuationAmount="+valuationAmount+"&effective_tariff="+effective_tariff;
        if($('[name="instance"]').val() == "sirva") {
            dataURL += "&flat_smf=" + flatSMF + "&percent_smf=" + percentSMF;
        }
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					jQuery('#quickEstimateRate').val('$'+Number(data.result));
					jQuery('.quickCreateActions').progressIndicator({'mode':'hide'});
					jQuery('.quickCreateActions').children().show();
					jQuery('#quickEstimateRate').removeClass('hide');
					jQuery('#quickEstimateCreate').removeClass('hide');
					jQuery('#processingInterval').hide();
					clearInterval(timer);
					thisInstance.intervalCount = 0;
				}
			}
		);
	}

	function getCurrentDate() {
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1;
		var yyyy = today.getFullYear();

		if(dd<10) {
			dd='0'+dd;
		}

		if(mm<10) {
			mm='0'+mm;
		}

		return yyyy+'-'+mm+'-'+dd;
	}

	function processingRate() {
		var thisInstance = this;
		if(thisInstance.intervalCount == 0) {
			jQuery('#processingInterval').html('<strong>Calculating Mileage</strong>');
		} else if(thisInstance.intervalCount == 1) {
			jQuery('#processingInterval').html('<strong>Fetching Rating Information</strong>');
		} else {
			jQuery('#processingInterval').html('<strong>Processing Rating Information</strong>');
		}
		thisInstance.intervalCount++;
	}
</script>
{/literal}
<!-- After script tag -->
{/strip}
