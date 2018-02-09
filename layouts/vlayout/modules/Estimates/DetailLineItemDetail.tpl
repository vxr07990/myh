{if $INSTANCE_NAME eq 'graebel'}
    {assign var="hide_or_not" value=""}
    {assign var="LBL_SUBTOTAL" value="Invoice Amount"}
    {if $MODULE eq 'Estimates'}
        {assign var="hide_estimate" value="hide"}
    {/if}
{else if getenv('IGC_MOVEHQ') eq 1}
    {assign var="hide_or_not" value=""}
    {assign var="LBL_SUBTOTAL" value="Invoice Amount"}
    {if $MODULE eq 'Estimates'}
        {assign var="hide_estimate" value="hide"}
    {/if}
{else}
    {assign var="hide_or_not" value="hide"}
    {assign var="LBL_SUBTOTAL" value="Net Price"}
{/if}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['DETAILED_LINE_ITEMS'])}
<div id="contentHolder_DETAILED_LINE_ITEMS" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
<table class="table table-bordered blockContainer showInlineTable lineItemsEdit"><!--equalSplit-->
    <thead>
        <tr>
            <th class="blockHeader" colspan="35">{vtranslate('Item Details', $MODULE)}</th>
        </tr>
    </thead>
    <tbody">
    <tr>
            <td class="{$hide_or_not}" ><b>{vtranslate('Tariff Item', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Tariff Schedule/Section', $MODULE)}</b></td>
            <td><b>{vtranslate('Item Name', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Service Description', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Participating Agent', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Base Rate', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Unit Rate', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Quantity', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Unit Of Measurement', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Gross', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Invoice Discount', $MODULE)}</b></td>
            <td><b {if $INSTANCE_NAME neq 'graebel'}class="pull-right"{/if}>{vtranslate($LBL_SUBTOTAL, $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Phase', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Event', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Invoice Sequence', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Distribution Sequence', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Distribution Discount', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Distribution Amount', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Move Policy', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Approval', $MODULE)}</b></td>
            <td class="{$hide_or_not}" >
                <table class="table table-bordered">
                    <tbody class="innerTableBody">
                    <tr class="innerRow">
                        <td style="width: 50%">
                            <b>{vtranslate('Service Provider', $MODULE)}</b>
                        </td>
                        <td style="width: 10%">
                            Split Amt.
                        </td>
                        <td style="width: 10%">
                            Pct.
                        </td>
                        <td style="width: 10%">
                            Miles
                        </td>
                        <td style="width: 10%">
                            Weight
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td class="{$hide_or_not} {$hide_estimate}" ><b>{vtranslate('Date Performed', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Invoiceable', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Distributable', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate}" ><b>{vtranslate('Ready To Invoice', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate}" ><b>{vtranslate('Ready To Distribute', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate}" ><b>{vtranslate('Invoiced', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate}" ><b>{vtranslate('Distributed', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate}" ><b>{vtranslate('Invoice Number', $MODULE)}</b></td>
        </tr>
    {assign var=GROSS_TOTALS value=0}
    {assign var=INVOICE_AMOUNT_TOTALS value=0}
    {assign var=READY_TO_INVOICE_AMOUNT_TOTALS value=0}
    {assign var=DISTRIBUTABLE_AMOUNT_TOTALS value=0}
    {assign var=READY_TO_DIST_AMOUNT_TOTALS value=0}
    {assign var=COUNT value=1}
        {foreach key=SECTION_NAME item=LINEITEM_SECTION from=$LINEITEMS}
            {foreach key=row_no item=LINEITEM from=$LINEITEM_SECTION}
                {assign var=row_no value=$COUNT}
                {assign var=COUNT value=$COUNT+1}
            <tr class="loadedLineItem">
                <input type="hidden" name="detaillineitemid{$row_no}" value="{$LINEITEM.DetailLineItemId}" />
                <input type="hidden" name="rowNumber" value="{$row_no}" />
                <input type="hidden" name="deleted{$row_no}" />
                <input type="hidden" name="location{$row_no}" value="{$LINEITEM.Location}" />
                <input type="hidden" name="gcs_flag{$row_no}" value="{$LINEITEM.GCS_Flag}" />
                <input type="hidden" name="metro_flag{$row_no}" value="{$LINEITEM.Metro_Flag}" />
                <input type="hidden" name="item_weight{$row_no}" value="{$LINEITEM.Item_Weight}" />
                <input type="hidden" name="rate_net{$row_no}" value="{$LINEITEM.Rate_Net}" />
                <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="tariffitem{$row_no}">{$LINEITEM.TariffItem}</div>
                    <input type="hidden" name="tariffitem{$row_no}" value="{$LINEITEM.TariffItem}" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="tariffsection{$row_no}">{$LINEITEM.TariffSection}</div>
                    <input type="hidden" name="tariffsection{$row_no}" value="{$LINEITEM.TariffSection}" />
                </td>
                <td class="narrowWidthType">
                    <div class="row-fluid" name="section{$row_no}">{$SECTION_NAME}</div>
                    <input type="hidden" name="section{$row_no}" value="{$SECTION_NAME}" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="description{$row_no}">{$LINEITEM.Description}</div>
                    <input type="hidden" name="description{$row_no}" value="{$LINEITEM.Description}" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <div class="span4">
                        <div class="row-fluid" name="role{$row_no}">{$LINEITEM.Role}</div>
                        <input type="hidden" name="role{$row_no}" value="{$LINEITEM.Role}" />
                        <div class="row-fluid" name="roleID{$row_no}">{$LINEITEM.RoleName}</div>
                        <input type="hidden" name="roleID{$row_no}" value="{$LINEITEM.RoleNameID}" />
                    </div>
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="baserate{$row_no}">{$LINEITEM.BaseRate}</div>
                    <input type="hidden" name="baserate{$row_no}" value="{$LINEITEM.BaseRate}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="unitrate{$row_no}">{$LINEITEM.UnitRate}</div>
                   <input type="hidden" name="unitrate{$row_no}" value="{$LINEITEM.UnitRate}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="quantity{$row_no}">{$LINEITEM.Quantity}</div>
                   <input type="hidden" name="quantity{$row_no}" value="{$LINEITEM.Quantity}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="unitOfMeasurement{$row_no}">{$LINEITEM.UnitOfMeasurement}</div>
                   <input type="hidden" name="unitOfMeasurement{$row_no}" value="{$LINEITEM.UnitOfMeasurement}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="gross{$row_no}">{$LINEITEM.Gross}</div>
                   <input type="hidden" name="gross{$row_no}" value="{$LINEITEM.Gross}" />
                   {assign var=GROSS_TOTALS value=$GROSS_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.Gross)}
                   <!-- {$LINEITEM.Gross} -->
                   <!-- {CurrencyField::convertToDBFormat($LINEITEM.Gross)} -->
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="invoicediscountpct{$row_no}">{$LINEITEM.InvoiceDiscountPct}</div>
                   <input type="hidden" name="invoicediscountpct{$row_no}" value="{$LINEITEM.InvoiceDiscountPct}" />
                </td>
               <td class="narrowWidthType">
                    <div class="row-fluid" name="invoicecostnet{$row_no}">{$LINEITEM.InvoiceCostNet}</div>
                   <input type="hidden" name="invoicecostnet{$row_no}" value="{$LINEITEM.InvoiceCostNet}" />
                   {assign var=INVOICE_AMOUNT_TOTALS value=$INVOICE_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.InvoiceCostNet)}
                   {if $LINEITEM.Invoiceable && $LINEITEM.ReadyToInvoice && !$LINEITEM.Invoiced}
                       {assign var=READY_TO_INVOICE_AMOUNT_TOTALS value=$READY_TO_INVOICE_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.InvoiceCostNet)}
                   {/if}
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="invoice_phase{$row_no}">{$LINEITEM.InvoicePhase}</div>
                   <input type="hidden" name="invoice_phase{$row_no}" value="{$LINEITEM.InvoicePhase}" />
               </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="invoice_event{$row_no}">{$LINEITEM.InvoiceEvent}</div>
                   <input type="hidden" name="invoice_event{$row_no}" value="{$LINEITEM.InvoiceEvent}" />
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="invoice_sequence{$row_no}">{$LINEITEM.InvoiceSequence}</div>
                   <input type="hidden" name="invoice_sequence{$row_no}" value="{$LINEITEM.InvoiceSequence}" />
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="distribution_sequence{$row_no}">{$LINEITEM.DistributionSequence}</div>
                   <input type="hidden" name="distribution_sequence{$row_no}" value="{$LINEITEM.DistributionSequence}" />
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="distributablediscountpct{$row_no}">{$LINEITEM.DistributableDiscountPct}</div>
                   <input type="hidden" name="distributablediscountpct{$row_no}" value="{$LINEITEM.DistributableDiscountPct}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="distributablecostnet{$row_no}">{$LINEITEM.DistributableCostNet}</div>
                   <input type="hidden" name="distributablecostnet{$row_no}" value="{$LINEITEM.DistributableCostNet}" />
                   {assign var=DISTRIBUTABLE_AMOUNT_TOTALS value=$DISTRIBUTABLE_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.DistributableCostNet)}
                   {if $LINEITEM.Distributable && $LINEITEM.ReadyToDistribute && !$LINEITEM.Distributed}
                       {assign var=READY_TO_DIST_AMOUNT_TOTALS value=$READY_TO_DIST_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.DistributableCostNet)}
                   {/if}
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="movepolicy{$row_no}">{$LINEITEM.MovePolicy}</div>
                   <input type="hidden" name="movepolicy{$row_no}" value="{$LINEITEM.MovePolicy}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="approval{$row_no}">{$LINEITEM.Approval}</div>
                   <input type="hidden" name="approval{$row_no}" value="{$LINEITEM.Approval}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {foreach item=SERVICE_PROVIDER key=INDEX from=$LINEITEM.ServiceProviders}
                        <div class="serviceProviderDiv span8">
                            <div class="row-fluid" name="serviceProvider{$row_no}_{$INDEX}">
                                <table class="table table-bordered">
                                    <tbody class="innerTableBody">
                                    <tr class="innerRow">
                                        <td style="width: 50%">
                                            {decode_html($SERVICE_PROVIDER.name)}
                                        </td>
                                        <td style="width: 10%">
                                            {$SERVICE_PROVIDER.split_amount}
                                        </td>
                                        <td style="width: 10%">
                                            {$SERVICE_PROVIDER.split_percent}%
                                        </td>
                                        <td style="width: 10%">
                                            {$SERVICE_PROVIDER.split_miles}
                                        </td>
                                        <td style="width: 10%">
                                            {$SERVICE_PROVIDER.split_weight}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" name="serviceProviderSplit{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER.split_amount}" />
                                <input type="hidden" name="serviceProviderPercent{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER.split_percent}"/>
                                <input type="hidden" name="serviceProviderMiles{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER.split_miles}"/>
                                <input type="hidden" name="serviceProviderWeight{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER.split_weight}"/>
                            </div>
                            <input type="hidden" name="serviceProvider{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER['vendor_id']}" />
                            <input type="hidden" name="serviceProviderID{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER['dli_service_providers_id']}" />
                            <input type="hidden" name="spIndexNumber" value="{$INDEX}" />
                        </div>
                    {/foreach}
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="preformed{$row_no}">{$LINEITEM.DatePerformed}</div>
                   <input type="hidden" name="preformed{$row_no}" value="{$LINEITEM.DatePerformed}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="invoiceable{$row_no}">{if $LINEITEM.Invoiceable}Yes{else}No{/if}</div>
                   <input type="hidden" class="booleanInput" name="invoiceable{$row_no}" value="{$LINEITEM.Invoiceable}" {if $LINEITEM.Invoiceable eq true} checked="checked" {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="distributable{$row_no}">{if $LINEITEM.Distributable}Yes{else}No{/if}</div>
                   <input type="hidden" class="booleanInput"name="distributable{$row_no}" value="{$LINEITEM.Distributable}" {if $LINEITEM.Distributable eq true} checked="checked" {/if}/>
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="ready_to_invoice{$row_no}">{if $LINEITEM.ReadyToInvoice}Yes{else}No{/if}</div>
                   <input type="hidden" class="booleanInput"name="ready_to_invoice{$row_no}" value="{$LINEITEM.ReadyToInvoice}" {if $LINEITEM.ReadyToInvoice eq true} checked="checked" {/if}/>
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="ready_to_distribute{$row_no}">{if $LINEITEM.ReadyToDistribute}Yes{else}No{/if}</div>
                   <input type="hidden" class="booleanInput"name="ready_to_distribute{$row_no}" value="{$LINEITEM.ReadyToDistribute}" {if $LINEITEM.ReadyToDistribute eq true} checked="checked" {/if}/>
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="invoicedone{$row_no}">{if $LINEITEM.Invoiced}Yes{else}No{/if}</div>
                   <input type="hidden" class="booleanInput"name="invoicedone{$row_no}" value="{$LINEITEM.Invoiced}" {if $LINEITEM.Invoiced eq true} checked="checked" {/if}/>
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="distributed{$row_no}">{if $LINEITEM.Distributed}Yes{else}No{/if}</div>
                   <input type="hidden" class="booleanInput"name="distributed{$row_no}" value="{$LINEITEM.Distributed}" {if $LINEITEM.Distributed eq true} checked="checked" {/if}/>
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="invoicenumber{$row_no}">{if $LINEITEM.InvoiceNumber}{$LINEITEM.InvoiceNumber}{/if}</div>
                   <input type="hidden" name="invoicenumber{$row_no}" value="{$LINEITEM.InvoiceNumber}" />
                </td>
            </tr>
            {/foreach}
        {/foreach}
    <input type="hidden" name="gross_total" value="{$GROSS_TOTALS}" />
<input type="hidden" name="invoice_net_total" value="{$INVOICE_AMOUNT_TOTALS}" />
<input type="hidden" name="dist_net_total" value="{$DISTRIBUTABLE_AMOUNT_TOTALS}" />
    <input type="hidden" name="total_ready_to_invoice" value="{$READY_TO_INVOICE_AMOUNT_TOTALS}" />
    <input type="hidden" name="total_ready_to_dist" value="{$READY_TO_DIST_AMOUNT_TOTALS}" />
        <tr>
{if $hide_or_not}
            <td width="83%" class="narrowWidthType">
                <span class="pull-right">
                    <b>
                    {vtranslate('Grand Total', $MODULE)}
                    </b>
                </span>
		    </td>
            <td class="narrowWidthType">
		    <span class="pull-right grandTotals">
                {*number_format($PASSED_RATE_ESTIMATE,2)*}
                {*number_format($GROSS_TOTALS,2)*}
                {CurrencyField::convertToUserFormat($GROSS_TOTALS)}
            </span>
		    </td>
{else}
            {*Gross	Invoice Discount	Invoice Amount	Distribution Discount	Distribution Amount*}
            <td colspan="9">
                <span class="pull-right">
                    <b>
                    {vtranslate('Gross Total', $MODULE)}
                    </b>
                </span>
            </td>
            <td class="narrowWidthType">
                <span class="pull-right grandTotals">
                    {*number_format($PASSED_RATE_ESTIMATE,2)*}
                    {*number_format($GROSS_TOTALS,2)*}
                    {CurrencyField::convertToUserFormat($GROSS_TOTALS)}
                </span>
		    </td>
            <td>
                <span class="pull-right">
                    <b>
                    {vtranslate('Invoice Net Total', $MODULE)}
                    </b>
                </span>
            </td>
            <td class="narrowWidthType">
                <span class="pull-right grandTotals">
                    {*number_format($INVOICE_AMOUNT_TOTALS,2)*}
                     {CurrencyField::convertToUserFormat($INVOICE_AMOUNT_TOTALS)}
                </span>
		    </td>
            <td colspan="5">
                <span class="pull-right">
                    <b>
                    {vtranslate('Distributable Net Total', $MODULE)}
                    </b>
                </span>
            </td>
            <td class="narrowWidthType">
                <span class="pull-right grandTotals">
                    {*number_format($DISTRIBUTABLE_AMOUNT_TOTALS,2)*}
                    {CurrencyField::convertToUserFormat($DISTRIBUTABLE_AMOUNT_TOTALS)}
                </span>
		    </td>
            <td colspan="6" class="{$hide_estimate}">
                <span class="pull-right">
                    <b>
                    {vtranslate('Ready to Invoice/Distribute Totals', $MODULE)}
                    </b>
                </span>
            </td>
            <td class="narrowWidthType {$hide_estimate}">
                <span class="pull-right grandTotals">
                    {*number_format($DISTRIBUTABLE_AMOUNT_TOTALS,2)*}
                    {CurrencyField::convertToUserFormat($READY_TO_INVOICE_AMOUNT_TOTALS)}
                </span>
		    </td>
    <td class="narrowWidthType {$hide_estimate}">
                <span class="pull-right grandTotals">
                    {*number_format($DISTRIBUTABLE_AMOUNT_TOTALS,2)*}
                    {CurrencyField::convertToUserFormat($READY_TO_DIST_AMOUNT_TOTALS)}
                </span>
		    </td>
            <td colspan="20">&nbsp;</td>
{/if}
        </tr>
        {if ($MODULE eq 'Estimates') || ($MODULE eq 'Actuals')}
            <tr>
			<td colspan="30">
				<span class={if getenv('INSTANCE_NAME') eq 'graebel'}"pull-left"{else}"pull-right"{/if}>
					{*
                    {if getenv('INSTANCE_NAME') eq 'sirva'}
						&nbsp;
                    {else}
                        <button type='button' class="interstateRateBtns" id='interstateRateQuick'>Quick Rate Estimate</button>
                    {/if}
					*}
                    {if !$LOCK_RATING}
                            <button type='button' class="interstateRateBtns interstateRateDetail">{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button>
                    {/if}
                    <button type='button' class="interstateRateBtns" id='getReportSelectButton'>Get Report</button>
				</span>
			</td>
		</tr>
        {/if}
    </tbody>
</table>
{/if}
</div>
