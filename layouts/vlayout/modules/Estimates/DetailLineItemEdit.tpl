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

<link rel="stylesheet" href="layouts/vlayout/modules/Estimates/resources/lineItems.css" />
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['DETAILED_LINE_ITEMS'])}
<div id="contentHolder_DETAILED_LINE_ITEMS" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
<table class="table table-bordered blockContainer showInlineTable lineItemsEdit"><!--equalSplit-->
    <thead>
        {if is_array($ROLESLIST) }
                {foreach item=INDEX key=ROLE_NAME from=$ROLESLIST}
                    <input class="rolemap" type="hidden" name="{$ROLE_NAME}agents_id" value="{$ROLESLIST[$ROLE_NAME]['agents_id']}" />
                    <input class="rolemap" type="hidden" name="{$ROLE_NAME}name" value="{$ROLESLIST[$ROLE_NAME]['name']}" />
                {/foreach}
            {/if}
        {if is_array($MOVEROLES) }
            {foreach item=INDEX key=ROLE_NAME from=$MOVEROLES}
                <input class="moverolemap" type="hidden" name="{$INDEX['name']}icode" value="{$INDEX['icode']}" />
                <input class="moverolemap" type="hidden" name="{$INDEX['name']}id" value="{$INDEX['id']}" />
                <input class="moverolemap" type="hidden" name="{$INDEX['name']}name" value="{$INDEX['name']}" />
            {/foreach}
        {/if}
        <tr>
            <th class="blockHeader" colspan="37">{vtranslate('Item Details', $MODULE)}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="{$hide_or_not} lietdmin100" ><b>{vtranslate('Tariff Item', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax75" ><b>{vtranslate('Schedule', $MODULE)}</b></td>
            <td class="lietdmin150"><b>{vtranslate('Item Name', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin200" ><b>{vtranslate('Service Description', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Participating Agent', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin120" ><b>{vtranslate('Base Rate', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin120" ><b>{vtranslate('Unit Rate', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin120" ><b>{vtranslate('Quantity', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax50" ><b>{vtranslate('Unit Of Measurement', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin120" ><b>{vtranslate('Gross', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax50" ><b>{vtranslate('Invoice Discount', $MODULE)}</b></td>
            <td class="lietdmin120"><b {if $INSTANCE_NAME neq 'graebel'}class="pull-right"{/if}>{vtranslate($LBL_SUBTOTAL, $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax50" ><b>{vtranslate('Phase', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax50" ><b>{vtranslate('Event', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax100" ><b>{vtranslate('Invoice Sequence', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax100" ><b>{vtranslate('Distribution Sequence', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmax100" ><b>{vtranslate('Distribution Discount', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin120" ><b>{vtranslate('Distribution Amount', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Move Policy', $MODULE)}</b></td>
            <td class="{$hide_or_not}" ><b>{vtranslate('Approval', $MODULE)}</b>
                <select class="approvalPicklistMaster" name="approval{$row_no}">
                  {foreach item=INDEX key=PICKLIST_NAME from=$APPROVAL}
                      <option value="{Vtiger_Util_Helper::toSafeHTML($APPROVAL[$PICKLIST_NAME])}">{$APPROVAL[$PICKLIST_NAME]}</option>
                  {/foreach}
                </select>
            </td>
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
            <td class="{$hide_or_not} {$hide_estimate} lietdmin100" ><b>{vtranslate('Date Performed', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin100" ><b>{vtranslate('Invoiceable', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin100" ><b>{vtranslate('Distributable', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate} lietdmin100" ><b>{vtranslate('Ready To Invoice', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate} lietdmin100" ><b>{vtranslate('Ready To Distribute', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate} lietdmin100" ><b>{vtranslate('Invoiced', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate} lietdmin100" ><b>{vtranslate('Distributed', $MODULE)}</b></td>
            <td class="{$hide_or_not} {$hide_estimate} lietdmin100" ><b>{vtranslate('Invoice Number', $MODULE)}</b></td>
            <td class="{$hide_or_not} lietdmin100" ><b>{vtranslate('Delete Line Item', $MODULE)}</b></td>
        </tr>
        {assign var=GROSS_TOTALS value=0}
        {assign var=INVOICE_AMOUNT_TOTALS value=0}
        {assign var=READY_TO_INVOICE_AMOUNT_TOTALS value=0}
        {assign var=DISTRIBUTABLE_AMOUNT_TOTALS value=0}
        {assign var=READY_TO_DIST_AMOUNT_TOTALS value=0}
        {assign var=COUNT value=1}
        <tr class="hide defaultLineItemRow">
                <input type="hidden" name="detaillineitemid0" value="{$LINEITEM.DetailLineItemId}" />
                <input type="hidden" name="rowNumber" value="0" />
                <input type="hidden" name="deleted0" />
                <input type="hidden" name="location0" value="" />
                <input type="hidden" name="gcs_flag0" value="N" />
                <input type="hidden" name="metro_flag0" value="" />
                <input type="hidden" name="item_weight0" value="" />
                <input type="hidden" name="rate_net0" value="" />
                <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="tariffitem{$row_no}">{$LINEITEM.TariffItem}</div>*}
                    <input type="text" readonly disabled name="tariffitem0" value="" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="tariffsection{$row_no}">{$LINEITEM.TariffSection}</div>*}
                    <input type="text" readonly disabled name="tariffsection0" value="" />
                </td>
                <td class="narrowWidthType">
                    {*<div class="row-fluid" name="section0">{$SECTION_NAME}</div>*}
                    <input type="text" readonly disabled name="section0" value="" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="description0">{$LINEITEM.Description}</div>*}
                    <input type="text" disabled name="description0" value="" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <!-- Role => Participating Agent --//-->
                  <div class="row-fluid" name="role0">
                    <select disabled class="rolePicklist" name="role0">
                      <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        {foreach item=INDEX key=PICKLIST_NAME from=$ROLES}
                            <option value="{Vtiger_Util_Helper::toSafeHTML($ROLES[$PICKLIST_NAME])}" >{$ROLES[$PICKLIST_NAME]}</option>
                        {/foreach}
                    </select>
                  </div>
                    <div class="row-fluid" name="roleID0"></div>
                    <input disabled type="hidden" name="roleID0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <input disabled type="text" name="baserate0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <input disabled type="text" name="unitrate0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="quantity0">{$LINEITEM.Quantity}</div>*}
                   <input disabled type="text" name="quantity0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="unitOfMeasurement0">{$LINEITEM.UnitOfMeasurement}</div>*}
                   <input disabled type="text" name="unitOfMeasurement0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="gross0">{$LINEITEM.Gross}</div>*}
                   <input disabled type="text" name="gross0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="invoicediscountpct0">{$LINEITEM.InvoiceDiscountPct}</div>*}
                   <input disabled type="text" name="invoicediscountpct0" value="" />
                </td>
               <td class="narrowWidthType">
                    {*<div class="row-fluid" name="invoicecostnet0">{$LINEITEM.InvoiceCostNet}</div>*}
                   <input disabled type="text" name="invoicecostnet0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input type="text" disabled name="invoice_phase0" value="" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input type="text" disabled name="invoice_event0" value="" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input disabled type="text" name="invoice_sequence0" value="" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input disabled type="text" name="distribution_sequence0" value="" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="distributablediscountpct0">{$LINEITEM.DistributableDiscountPct}</div>*}
                   <input disabled type="text" name="distributablediscountpct0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="distributablecostnet0">{$LINEITEM.DistributableCostNet}</div>*}
                   <input disabled type="text" name="distributablecostnet0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="movepolicy0">{$LINEITEM.MovePolicy}</div>*}
                   <input disabled type="text" name="movepolicy0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                  <div class="row-fluid" name="approval0">
                    <select disabled class="approvalPicklist" name="approval0">
                      <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                        {foreach item=INDEX key=PICKLIST_NAME from=$APPROVAL}
                            <option value="{Vtiger_Util_Helper::toSafeHTML($APPROVAL[$PICKLIST_NAME])}" >{$APPROVAL[$PICKLIST_NAME]}</option>
                        {/foreach}
                    </select>
                  </div>
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                       <div class="serviceProviderDiv span10">
                           <div class="row-fluid" name="info_serviceProvider0_0">
                                <table class="table table-bordered">
                                    <tbody class="innerTableBody">
                                    <tr class="innerRow">
                                        <td style="width: 50%">
                                           <button type="button" class="addServiceProvider" name="addServiceProvider0">+</button>
                                           <a style="float: left; padding: 3px"><i title="Delete" class="deleteServiceProvider icon-trash hide"></i></a>
                                           <select disabled class="serviceProviderPicklist" name="serviceProviderRow0_0">
                                               <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                               {foreach item=VENDOR key=PICKLIST_NAME from=$MOVEROLES}
                                                   <option value="{Vtiger_Util_Helper::toSafeHTML($VENDOR['name'])}" >{$VENDOR['name']}</option>
                                               {/foreach}
                                           </select>
                                           <div class="row-fluid" name="serviceProvider0_0"></div>
                                        </td>
                                        <td style="width: 10%">
                                           <input disabled type='text' class='splitInput input-medium currencyField' style='width:90%' name='serviceProviderSplit0_0' value='' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
                                        </td>
                                        <td style="width: 10%">
                                           <input disabled type='text' class='splitInput input-medium' style='width:90%' name='serviceProviderPercent0_0' value='' />
                                        </td>
                                        <td style="width: 10%">
                                            <input disabled type='text' class='splitInput input-medium' style='width:90%' name='serviceProviderMiles0_0' value='' />
                                        </td>
                                        <td style="width: 10%">
                                            <input disabled type='text' class='splitInput input-medium' style='width:90%' name='serviceProviderWeight0_0' value='' />
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                           </div>
                           <input disabled type="hidden" name="serviceProvider0_0" value="" />
                           <input disabled type="hidden" name="serviceProviderID0_0" value="" />
                           <input disabled type="hidden" name="spIndexNumber" value="0" />
                           <input disabled type="hidden" name="serviceProviderDeleted0_0">
                       </div>
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                   <div class="row-fluid">
                       <span class="span10">
                           <div class="input-append row-fluid">
                               <div class="span12 row-fluid date">
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML('{"mandatory":false, "presence":true, "quickcreate":false, "masseditable":true, "defaultvalue":false, "type":"date", "name":"preformed0", "label":"Date Performed", "date-format":"'|cat:{$dateFormat}|cat:'"}')}
                                   <input disabled id="datePerformed" type="text" class="dateField detailDateField" name="preformed0" data-date-format="{$dateFormat}" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$FIELD_INFO}">
                                   <span class="add-on"><i class="icon-calendar"></i></span>
                               </div>
                           </div>
                       </span>
                   </div>
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <input disabled type="checkbox" name="invoiceable0" value="1" checked />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <input disabled type="checkbox" name="distributable0" value="1" checked />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input disabled type="checkbox" name="ready_to_invoice0" value="1" />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input disabled type="checkbox" name="ready_to_distribute0" value="1" />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input type="checkbox" readonly disabled name="invoicedone0" value="1" />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input type="checkbox" readonly disabled name="distributed0" value="1" />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="invoicenumber0"></div>
                    <input disabled type="hidden" name="invoicenumber0" value="" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <div class="row-fluid" name="lineItemRemoveOnSave0"></div>
                    <input disabled type="hidden" name="lineItemRemoveOnSave0" value="" />
                </td>
            </tr>
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
                <input type="hidden" name="item_weight{$row_no}" value="{$LINEITEM.}Item_Weight" />
                <input type="hidden" name="rate_net{$row_no}" value="{$LINEITEM.}Rate_Net" />
                <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="tariffitem{$row_no}">{$LINEITEM.TariffItem}</div>*}
                    <input type="text" readonly name="tariffitem{$row_no}" value="{$LINEITEM.TariffItem}" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="tariffsection{$row_no}">{$LINEITEM.TariffSection}</div>*}
                    <input type="text" readonly name="tariffsection{$row_no}" value="{$LINEITEM.TariffSection}" />
                </td>
                <td class="narrowWidthType">
                    {*<div class="row-fluid" name="section{$row_no}">{$SECTION_NAME}</div>*}
                    <input type="text" readonly name="section{$row_no}" value="{$SECTION_NAME}" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="description{$row_no}">{$LINEITEM.Description}</div>*}
                    <input type="text" name="description{$row_no}" value="{$LINEITEM.Description}" />
                </td>
                <td class="narrowWidthType {$hide_or_not}">
                    <!-- Role => Participating Agent --//-->
                  <div class="row-fluid" name="role{$row_no}">
                    <select class="rolePicklist" name="role{$row_no}">
                      <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                      {foreach item=INDEX key=PICKLIST_NAME from=$ROLES}
                        <option value="{Vtiger_Util_Helper::toSafeHTML($ROLES[$PICKLIST_NAME])}" {if (trim(decode_html($LINEITEM.Role)) eq trim($ROLES[$PICKLIST_NAME]))} selected {/if}>{$ROLES[$PICKLIST_NAME]}</option>
                      {/foreach}
                    </select>
                  </div>
                    <div class="row-fluid" name="roleID{$row_no}">{$LINEITEM.RoleName}</div>
                    <input type="hidden" name="roleID{$row_no}" value="{$LINEITEM.RoleNameID}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   {if $LINEITEM.BaseRate != ''}
                        <input type="text" name="baserate{$row_no}" value="{$LINEITEM.BaseRate}" />
                   {else}
                       {*<div class="row-fluid" name="baserate{$row_no}">{$LINEITEM.BaseRate}</div>*}
                       <input type="text" name="baserate{$row_no}" value="{$LINEITEM.BaseRate}" />
                   {/if}
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   {if $LINEITEM.UnitRate != ''}
                        <input type="text" name="unitrate{$row_no}" value="{$LINEITEM.UnitRate}" />
                   {else}
                       {* <div class="row-fluid" name="unitrate{$row_no}">{$LINEITEM.UnitRate}</div>*}
                       <input type="text" name="unitrate{$row_no}" value="{$LINEITEM.UnitRate}" />
                   {/if}
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="quantity{$row_no}">{$LINEITEM.Quantity}</div>*}
                    <input type="text" name="quantity{$row_no}" value="{$LINEITEM.Quantity}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="unitOfMeasurement{$row_no}">{$LINEITEM.UnitOfMeasurement}</div>*}
                    <input type="text" name="unitOfMeasurement{$row_no}" value="{$LINEITEM.UnitOfMeasurement}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="gross{$row_no}">{$LINEITEM.Gross}</div>*}
                    <input type="text" name="gross{$row_no}" value="{$LINEITEM.Gross}" />
                   {assign var=GROSS_TOTALS value=$GROSS_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.Gross)}
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="invoicediscountpct{$row_no}">{$LINEITEM.InvoiceDiscountPct}</div>*}
                    <input type="text" name="invoicediscountpct{$row_no}" value="{$LINEITEM.InvoiceDiscountPct}" />
                </td>
               <td class="narrowWidthType">
                    {*<div class="row-fluid" name="invoicecostnet{$row_no}">{$LINEITEM.InvoiceCostNet}</div>*}
                    <input type="text" name="invoicecostnet{$row_no}" value="{$LINEITEM.InvoiceCostNet}" />
                   {assign var=INVOICE_AMOUNT_TOTALS value=$INVOICE_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.InvoiceCostNet)}
                   {if $LINEITEM.Invoiceable && $LINEITEM.ReadyToInvoice && !$LINEITEM.Invoiced}
                       {assign var=READY_TO_INVOICE_AMOUNT_TOTALS value=$READY_TO_INVOICE_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.InvoiceCostNet)}
                   {/if}
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input type="text" name="invoice_phase{$row_no}" value="{$LINEITEM.InvoicePhase}" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input type="text" name="invoice_event{$row_no}" value="{$LINEITEM.InvoiceEvent}" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input type="text" name="invoice_sequence{$row_no}" value="{$LINEITEM.InvoiceSequence}" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                   <span class="span2">
                    <input type="text" name="distribution_sequence{$row_no}" value="{$LINEITEM.DistributionSequence}" />
                   </span>
               </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="distributablediscountpct{$row_no}">{$LINEITEM.DistributableDiscountPct}</div>*}
                    <input type="text" name="distributablediscountpct{$row_no}" value="{$LINEITEM.DistributableDiscountPct}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="distributablecostnet{$row_no}">{$LINEITEM.DistributableCostNet}</div>*}
                    <input type="text" name="distributablecostnet{$row_no}" value="{$LINEITEM.DistributableCostNet}" />
                   {assign var=DISTRIBUTABLE_AMOUNT_TOTALS value=$DISTRIBUTABLE_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.DistributableCostNet)}
                   {if $LINEITEM.Distributable && $LINEITEM.ReadyToDistribute && !$LINEITEM.Distributed}
                       {assign var=READY_TO_DIST_AMOUNT_TOTALS value=$READY_TO_DIST_AMOUNT_TOTALS+CurrencyField::convertToDBFormat($LINEITEM.DistributableCostNet)}
                   {/if}
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    {*<div class="row-fluid" name="movepolicy{$row_no}">{$LINEITEM.MovePolicy}</div>*}
                    <input type="text" name="movepolicy{$row_no}" value="{$LINEITEM.MovePolicy}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                  <div class="row-fluid" name="approval{$row_no}">
                    <select class="approvalPicklist" name="approval{$row_no}">
                      <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                      {foreach item=INDEX key=PICKLIST_NAME from=$APPROVAL}
                          <option value="{Vtiger_Util_Helper::toSafeHTML($APPROVAL[$PICKLIST_NAME])}" {if (trim(decode_html($LINEITEM.Approval)) eq trim($APPROVAL[$PICKLIST_NAME]))} selected {/if}>{$APPROVAL[$PICKLIST_NAME]}</option>
                      {/foreach}
                    </select>
                  </div>
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                   {foreach item=SERVICE_PROVIDER key=INDEX from=$LINEITEM.ServiceProviders}
                       <div class="serviceProviderDiv span10">
                           <div class="row-fluid" name="info_serviceProvider{$row_no}_{$INDEX}">
                                <table class="table table-bordered">
                                    <tbody class="innerTableBody">
                                    <tr class="innerRow">
                                        <td style="width: 50%">
                                           {if $INDEX == 0}
                                               <button type="button" class="addServiceProvider" name="addServiceProvider{$row_no}">+</button>
                                               <a style="float: left; padding: 3px"><i title="Delete" class="deleteServiceProvider icon-trash hide"></i></a>
                                           {else}
                                               <a style="float: left; padding: 3px"><i title="Delete" class="deleteServiceProvider icon-trash"></i></a>
                                           {/if}
                                           <select class="serviceProviderPicklist" name="serviceProviderRow{$row_no}_{$INDEX}">
                                               <option value="">--</option>
                                               {foreach item=VENDOR key=PICKLIST_NAME from=$MOVEROLES}
                                                   <option value="{Vtiger_Util_Helper::toSafeHTML($VENDOR['name'])}" {if (trim(decode_html($SERVICE_PROVIDER.name)) eq trim($VENDOR['name']|cat:' - '|cat: $VENDOR['icode']))} selected {/if}>{$VENDOR['name']}</option>
                                               {/foreach}
                                           </select>
                                            <div class="row-fluid" name="serviceProvider{$row_no}_{$INDEX}">{$SERVICE_PROVIDER['name']}</div>
                                        </td>
                                        <td style="width: 10%">
                                           <input type='text' class='splitInput input-medium currencyField' style='width:90%' name='serviceProviderSplit{$row_no}_{$INDEX}' value='{$SERVICE_PROVIDER.split_amount}' data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" />
                                        </td>
                                        <td style="width: 10%">
                                            <input type='text' class='splitInput input-medium' style='width:90%' name='serviceProviderPercent{$row_no}_{$INDEX}' value='{$SERVICE_PROVIDER.split_percent}' />
                                        </td>
                                        <td style="width: 10%">
                                            <input type='text' class='splitInput input-medium' style='width:90%' name='serviceProviderMiles{$row_no}_{$INDEX}' value='{$SERVICE_PROVIDER.split_miles}' />
                                        </td>
                                        <td style="width: 10%">
                                            <input type='text' class='splitInput input-medium' style='width:90%' name='serviceProviderWeight{$row_no}_{$INDEX}' value='{$SERVICE_PROVIDER.split_weight}' />
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                           </div>
                           <input type="hidden" name="serviceProvider{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER['vendor_id']}" />
                           <input type="hidden" name="serviceProviderID{$row_no}_{$INDEX}" value="{$SERVICE_PROVIDER['dli_service_providers_id']}" />
                           <input type="hidden" name="spIndexNumber" value="{$INDEX}" />
                           <input type="hidden" name="serviceProviderDeleted{$row_no}_{$INDEX}">
                       </div>
                   {/foreach}
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                   <div class="row-fluid">
                       <span class="span10">
                           <div class="input-append row-fluid">
                               <div class="span12 row-fluid date">
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML('{"mandatory":false, "presence":true, "quickcreate":false, "masseditable":true, "defaultvalue":false, "type":"date", "name":"preformed'|cat:{$row_no}|cat:'", "label":"Date Performed", "date-format":"'|cat:{$dateFormat}|cat:'"}')}
<input id="datePerformed" type="text" class="dateField detailDateField" name="preformed{$row_no}" data-date-format="{$dateFormat}" value="{$LINEITEM.DatePerformed}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$FIELD_INFO}">
                                   <span class="add-on"><i class="icon-calendar"></i></span>
                               </div>
                           </div>
                       </span>
                   </div>
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <input type="checkbox" readonly name="invoiceable{$row_no}" value="1" {if $LINEITEM.Invoiceable eq true} checked {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
                    <input type="checkbox" readonly name="distributable{$row_no}" value="1" {if $LINEITEM.Distributable eq true} checked {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input type="checkbox" name="ready_to_invoice{$row_no}" value="1" {if $LINEITEM.ReadyToInvoice eq true} checked {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input type="checkbox" name="ready_to_distribute{$row_no}" value="1" {if $LINEITEM.ReadyToDistribute eq true} checked {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input type="checkbox" readonly name="invoicedone{$row_no}" value="1" {if $LINEITEM.Invoiced eq true} checked {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <input type="checkbox" readonly name="distributed{$row_no}" value="1" {if $LINEITEM.Distributed eq true} checked {/if} />
                </td>
               <td class="narrowWidthType {$hide_or_not} {$hide_estimate}">
                    <div class="row-fluid" name="invoicenumber{$row_no}">{if $LINEITEM.InvoiceNumber}{$LINEITEM.InvoiceNumber}{/if}</div>
                    <input type="hidden" name="invoicenumber{$row_no}" value="{$LINEITEM.InvoiceNumber}" />
                </td>
               <td class="narrowWidthType {$hide_or_not}">
					{*<a style="float: right; padding: 3px"><i title="Delete" class="deleteDetailLineItem icon-trash"></i></a>*}
				   <input type="checkbox" name="lineItemRemoveOnSave{$row_no}" {if $LINEITEM.Invoiced || $LINEITEM.Distributed}disabled{/if} value="1" />
				</td>
            </tr>
    {/foreach}
{/foreach}
<input type="hidden" name="detailLineItemCount" value="{$COUNT}" />
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
            {CurrencyField::convertToUserFormat($GROSS_TOTALS)}
        </span>
	</td>
{else}
    {*Gross	Invoice Discount	Invoice Amount	Distribution Discount	Distribution Amount*}
    <td colspan="9">
        <button type="button" class="addNewLineItem" name="addNewLineItem">Create New Line Item</button>
    <span class="pull-right">
            <b>
            {vtranslate('Gross Total', $MODULE)}
            </b>
        </span>
    </td>
    <td class="narrowWidthType">
        <span class="pull-right grandTotals">
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
                <span class="pull-right grandTotals readyToInvoiceTotals">
                    {*number_format($DISTRIBUTABLE_AMOUNT_TOTALS,2)*}
                    {CurrencyField::convertToUserFormat($READY_TO_INVOICE_AMOUNT_TOTALS)}
                </span>
		    </td>
    <td class="narrowWidthType {$hide_estimate}">
                <span class="pull-right grandTotals readyToDistTotals">
                    {*number_format($DISTRIBUTABLE_AMOUNT_TOTALS,2)*}
                    {CurrencyField::convertToUserFormat($READY_TO_DIST_AMOUNT_TOTALS)}
                </span>
		    </td>
    <td colspan="25">&nbsp;</td>
{/if}
</tr>
{if ($MODULE eq 'Estimates') || ($MODULE eq 'Actuals')}
    <tr valign="top">
    <td colspan="37">
        <span class={if getenv('INSTANCE_NAME') eq 'graebel'}"pull-left"{else}"pull-right"{/if}>
            {if !$LOCK_RATING}
                <button type='button' class="interstateRateBtns interstateRateDetail">{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button>
            {/if}
            {if $RECORD_ID}
                <button type='button' class="interstateRateBtns" id='getReportSelectButton'>Get Report</button>
            {/if}
        </span>
    </td>
</tr>
{/if}
</tbody>
</table>
{/if}
</div>
