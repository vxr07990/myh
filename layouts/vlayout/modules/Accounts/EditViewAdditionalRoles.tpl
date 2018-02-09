{strip}
    <!-- EditBillingAddressesTable.tpl -->
    <table class="table table-bordered blockContainer showInlineTable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">{vtranslate("LBL_ACCOUNTS_ADDITIONAL_ROLES", $MODULE)}</th>
        </tr>
        </thead>
        <tbody id='additionalRolesTable'>
        <!-- Adding Buttons -->
        <tr>
            <td style='text-align:center' colspan="4">
                <button type="button" class="marginLeft10px addAdditionalRole" style="clear:left; float:left;">+
                </button>
                <button type="button" class="marginRight10px addAdditionalRole" style="clear:right; float:right;">+
                </button>
                <input type="hidden" name="additionalRolesCount" id="additionalRolesCount" value="0">&nbsp
            </td>
        </tr>

        <tr>
            <td style="width:15px;"></td>
            <td class="blockHeader" style="width: 150px;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_COMMODITY", $MODULE)}</td>
            <td class="blockHeader" style="width: 170px;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_ROLE", $MODULE)}</td>
            <td class="blockHeader" style="width: 170px;"><span class="redColor">*</span> {vtranslate("LBL_ACCOUNTS_USER", $MODULE)}</td>

        </tr>

        <tr class="defaultAdditionalRole hide">
            <td style="text-align: center; width:15px;">
                <i title="Delete" class="icon-trash deleteAdditionalRole" style="margin: 5px 10px;"></i>
                <input type="hidden" name="id[]">
            </td>

            <td>
                <div class="row-fluid">
                    <span class="span10">
                        <select id="{$MODULE}_Edit_fieldName_role_commodity" multiple class="multipicklistall" name="role_commodity[][]" style="width: 60%">
                            <option value="All">{vtranslate('LBL_ALL')}</option>
                            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME  from=$BUSINESS_LINES}
                                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_VALUE)}">{vtranslate($PICKLIST_VALUE, $MODULE)}</option>
                            {/foreach}
                        </select>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span10">
                        <input name="popupReferenceModule" type="hidden" value="EmployeeRoles" />
                        <input name="role[]" type="hidden" value="" class="sourceField EmployeeRoles"  />
                        <div class="row-fluid input-prepend input-append">
                            <span class="add-on clearReferenceSelection cursorPointer">
                                <i class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
                            </span>
                            <input id="role_display" name="role_display" type="text" class="span7 marginLeftZero autoComplete"
                                   value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                   placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"/>
                            <span class="add-on relatedPopup cursorPointer">
                                <i class="icon-search" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
                            </span>
                        </div>
                    </span>
                </div>
            </td>
            <td>
                <div class="row-fluid">
                    <span class="span10">
                        <input name="popupReferenceModule" type="hidden" value="Employees" />
                            <input name="user_role[]" type="hidden" value="" class="sourceField Employees"  />
                            <div class="row-fluid input-prepend input-append">
                                <span class="add-on clearReferenceSelection cursorPointer">
                                    <i class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
                                </span>
                                <input id="user_role_display" name="user_role_display" type="text" class="span7 marginLeftZero autoComplete"
                                       value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                       placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"/>
                                <span class="add-on relatedPopup cursorPointer">
                                    <i class="icon-search" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
                                </span>
                            </div>
                    </span>
                </div>
            </td>
        </tr>


        <!-- DEFAULT TABLE ENDS -->
        {foreach item=roles key=ROW_NUM from=$ACCOUNT_ROLES_VALUES}
            <tr>
                <td style="width: 15px; text-align: center">
                    <i title="Delete" class="icon-trash deleteBillingAddress" style="margin: 5px 10px;"></i>
                    <input type="hidden" name="id[]" value="{$roles.id}">
                </td>
                <td>
                    <div class="row-fluid">
                    <span class="span10">
                        <select class="select2" multiple name="role_commodity[{$ROW_NUM+1}][]" required>
                            {if $roles.commodity|count eq $BUSINESS_LINES|count}
                                <option value="All" selected>{vtranslate('LBL_ALL')}</option>
                                {foreach item=business_line key=NUM  from=$BUSINESS_LINES}
                                    <option value="{$business_line}">{vtranslate($business_line, $MODULE)}</option>
                                {/foreach}
                            {else}
                                <option value="All">{vtranslate('LBL_ALL')}</option>
                                {foreach item=business_line key=NUM from=$BUSINESS_LINES}
                                    <option {if in_array(vtranslate($business_line, $MODULE), $roles.commodity)}selected {/if} value="{$business_line}">{vtranslate($business_line, $MODULE)}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                        <span class="span10">
                            {assign var=ROLE value=Vtiger_Record_Model::getInstanceById($roles.role)}
                            <input name="popupReferenceModule" type="hidden" value="EmployeeRoles" />
                        <input name="role[]" type="hidden" value="{$roles.role}" class="sourceField EmployeeRoles"  />
                        <div class="row-fluid input-prepend input-append">
                            <span class="add-on clearReferenceSelection cursorPointer">
                                <i class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
                            </span>
                            <input id="role_display" name="role_display" type="text" class="span7 marginLeftZero autoComplete"
                                   value="{$ROLE->getDisplayName()}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                   placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}" {if !empty($roles.role)}readonly="true"{/if}/>
                            <span class="add-on relatedPopup cursorPointer">
                                <i class="icon-search" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
                            </span>
                        </div>
                        </span>
                    </div>
                </td>
                <td>
                    <div class="row-fluid">
                        <span class="span10">
                            {assign var=EMPLOYEE value=Vtiger_Record_Model::getInstanceById($roles.user)}
                            <input name="popupReferenceModule" type="hidden" value="Employees" />
                            <input name="user_role[]" type="hidden" value="{$roles.user}" class="sourceField Employees"  />
                            <div class="row-fluid input-prepend input-append">
                                <span class="add-on clearReferenceSelection cursorPointer">
                                    <i class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
                                </span>
                                <input id="user_role_display" name="user_role_display" type="text" class="span7 marginLeftZero autoComplete"
                                       value="{$EMPLOYEE->getDisplayName()}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                       placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}" {if !empty($roles.user)}readonly="true"{/if}/>
                                <span class="add-on relatedPopup cursorPointer">
                                    <i class="icon-search" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
                                </span>
                            </div>
                    </span>
                    </div>
                </td>
            </tr>
        {/foreach}



        </tbody>
    </table>

    <br/>
{/strip}

