{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['INTERSTATE_SERVICE_CHARGES'])}
<div id="contentHolder_INTERSTATE_SERVICE_CHARGES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
    <input type="hidden" name="compiledServiceCharges" id="compiledServiceCharges" value />
    <table name='{$BLOCK_LABEL}' id="serviceChargesTable" class="table table-bordered blockContainer showInlineTable{if $BLOCK_LABEL eq "LBL_QUOTES_TPGPRICELOCK"} hide{/if}">
        <thead>
            <tr>
                <th class="blockHeader" colspan="5">
                    <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
                    {vtranslate($BLOCK_LABEL, $MODULE)}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="interstateServiceChargeHeader">
                <td class="fieldLabel" style="text-align:center;margin:auto;width:10%;">{vtranslate('LBL_QUOTES_APPLIED', $MODULE)}</td>
                {* <td class="fieldLabel" style="text-align:center;margin:auto;width:10%;">{vtranslate('LBL_QUOTES_INTERSTATE_SERVICECHARGES_ALWAYSUSED', $MODULE)}</td> *}
                <td class="fieldLabel" style="text-align:center;margin:auto;width:20%;">{vtranslate('LBL_QUOTES_INTERSTATE_SERVICECHARGES_WEIGHT', $MODULE)}</td>
                <td class="fieldLabel" style="text-align:center;margin:auto;width:40%;">{vtranslate('LBL_QUOTES_INTERSTATE_SERVICECHARGES_DESCRIPTION', $MODULE)}</td>
                <td class="fieldLabel hide" style="text-align:center;margin:auto;width:20%;">{vtranslate('LBL_QUOTES_INTERSTATE_SERVICECHARGES_CHARGE', $MODULE)}</td>
            </tr>
            <tbody id="originServiceCharges">
                <tr class="cbxblockhead">
                    <td colspan="5" class="fieldLabel">
                        Origin
                    </td>
                </tr>
                {foreach item=SERVICE_CHARGE key=KEY from=$INTERSTATE_SERVICECHARGES[0]}
                    <tr class="interstateServiceChargeRow">
                    <input type="hidden" name="serviceid" value="{$SERVICE_CHARGE['serviceid']}" />
                    <input type="hidden" name="is_dest" value="0" />
                    <input type="hidden" name="minimum" value="{$SERVICE_CHARGE['minimum']}" />
                    <td style="text-align:center">
                        <input type="checkbox" readonly name="applied" {if $SERVICE_CHARGE['applied']}checked{/if}/>
                    </td>
                    {* <td style="text-align:center">
                        <input type="checkbox" readonly value="1" name="always_used" readonly {if $SERVICE_CHARGE['always_used']}checked{/if} />
                    </td> *}
                    <td style="text-align:center">
                        <input type="hidden" name="service_weight" style="width:85%" value="{$SERVICE_CHARGE['service_weight']}" />
                        <span>
                            {$SERVICE_CHARGE['service_weight']}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <input type="hidden" name="service_description" style="width:85%" readonly value="{$SERVICE_CHARGE['service_description']}" />
                        <span>
                            {$SERVICE_CHARGE['service_description']}
                        </span>
                    </td>
                    <td style="text-align:center" class='hide'>
                        <div class="input-prepend input-prepend-centered">
                            <span class="add-on">{$USER_MODEL->get('currency_symbol')}</span>
                            <input type="text" name="charge" style="width:75%;float:left;" readonly value="{$SERVICE_CHARGE['charge']}" />
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tbody id="destinationServiceCharges">
                <tr class="cbxblockhead">
                    <td colspan="5" class="fieldLabel">
                        Destination
                    </td>
                </tr>
                {foreach item=SERVICE_CHARGE key=KEY from=$INTERSTATE_SERVICECHARGES[1]}
                    <tr class="interstateServiceChargeRow">
                    <input type="hidden" name="serviceid" value="{$SERVICE_CHARGE['serviceid']}" />
                    <input type="hidden" name="is_dest" value="1" />
                    <input type="hidden" name="minimum" value="{$SERVICE_CHARGE['minimum']}" />
                    <td style="text-align:center">
                        <input type="checkbox" readonly name="applied" {if $SERVICE_CHARGE['applied']}checked{/if}/>
                    </td>
                    {* <td style="text-align:center">
                        <input type="checkbox" readonly value="1" name="always_used" readonly {if $SERVICE_CHARGE['always_used']}checked{/if} />
                    </td> *}
                    <td style="text-align:center">
                        <input type="hidden" name="service_weight" style="width:85%" value="{$SERVICE_CHARGE['service_weight']}" />
                        <span>
                            {$SERVICE_CHARGE['service_weight']}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <input type="hidden" name="service_description" style="width:85%" readonly value="{$SERVICE_CHARGE['service_description']}" />
                        <span>
                            {$SERVICE_CHARGE['service_description']}
                        </span>
                    </td>
                    <td style="text-align:center" class='hide'>
                        <div class="input-prepend input-prepend-centered">
                            <span class="add-on">{$USER_MODEL->get('currency_symbol')}</span>
                            <input type="text" name="charge" style="width:75%;float:left;" readonly value="{$SERVICE_CHARGE['charge']}" />
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </tbody>
    </table>
    <br />
    {/if}
    </div>
{/strip}
