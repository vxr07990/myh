Vtiger_Edit_Js("ParticipatingAgents_Edit_Js", {
	getInstance: function() {
		return new ParticipatingAgents_Edit_Js();
	}
}, {
	registerRemoveParticipantButton : function(){
		var statuses = ['Pending', 'Accepted', 'Removed'];
		jQuery('html').on( 'click', '.removeParticipant', function(){
			if(jQuery(this).siblings('input:hidden[name^="participantId"]').val() == 'none'){
				jQuery(this).parent().parent().remove()
			} else{
				jQuery(this).parent().parent().addClass('hide');
				jQuery(this).siblings('input:hidden[name^="participantDelete"]').val('deleted');
                                jQuery(this).closest('tr').find('select option:selected').removeAttr("selected");
			}
			jQuery('select[name="brand"]').trigger('change');
		});
	},
    // Can't be static because it relies on the instance.
    add: function() {
		var table = jQuery('[name^="participatingAgentsTable"]').find('tbody');

		var defaultRowClass = 'defaultParticipant';
		var rowId = 'participantRow';
		var names = ['agent_type','opp_participants','participantPermission'];

        var newRow = jQuery('.defaultParticipant').clone();
        var sequenceNode = jQuery("input[name='numAgents']");
        //a beautiful way to handle the tally that tracks the number of the participant we are currently adding
        var sequence = sequenceNode.val();
        sequence++;
        sequenceNode.val(sequence);
        //find the agent type select and add the class that changes its UI look
        newRow.find('select').attr('name','agent_type').addClass('chzn-select');
        newRow.addClass('newParticipant');
        //remove the classes from the default row that cause it to be hidden and labeled
        newRow.removeClass('hide defaultParticipant');

        newRow.find('.default').each(function(){
            jQuery(this).attr('name', jQuery(this).attr('name')+'_'+sequence);
            jQuery(this).removeClass('default change');
        });
        //this again is a fix/hack to format the needed fields for the agent lookup so it can work
        newRow.find('input[name="agents_id_display"]').attr('id', 'agents_id_display'.replace('_display', '_'+sequence+'_display'));
        newRow.find('input[name="agents_id_display"]').attr('name', 'agents_id_display'.replace('_display', '_'+sequence+'_display'));
        newRow.find('i[title="Select"]').attr('id', 'Opportunities_editView_fieldName_agents_id'+sequence+'_select');
        newRow.find('.validate').each(function(){
            var validator = "validate[";
            if(jQuery(this).data('fieldinfo').mandatory == true){
                validator += "required,";
            }
            validator += "funcCall[Vtiger_Base_Validator_Js.invokeValidation]]";
            jQuery(this).data('validationEngine', validator).attr('data-validation-engine', validator);
            jQuery(this).removeClass('validate');
        });

        //add the new row to the table
        newRow = newRow.appendTo(table);
        //notifiy the js library that handles the reformating the ui has changed
        newRow.find('.chzn-select').chosen();
        //register the clear reference selection even so the new UI 10s can be cleared
        var editInstance = Vtiger_Edit_Js.getInstance();
        editInstance.registerClearReferenceSelectionEvent(newRow);
        editInstance.registerAutoCompleteFields(newRow);
        this.registerAgentChange();
        this.registerTypeChange();
        this.checkAgentDistance();
    },

	registerAddParticipantButtons : function() {
        var thisI = this;
		var button = jQuery('.addParticipant');
		button.on('click', function() {
            thisI.add();
        });
	},

	registerAgentChange : function(){
		var thisInstance = this;
		//change event for UI type 10 to restrict estimating agents to being the same as either the booking or origin agent
		//first things first, only set event for relevant tariff types
		var tariffType = jQuery('input:hidden[name="primary_est_tariff_type"]').val();
		//var agentTypeArr = ['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'];
		if(
			tariffType == 'TPG' ||
			tariffType == 'Pricelock' ||
			tariffType == 'TPG GRR' ||
			tariffType == 'Pricelock GRR' ||
			tariffType == 'Allied Express' ||
			tariffType == 'Blue Express'
		){
			jQuery('input:hidden[name^="agents_id_"]').each(function(){
				jQuery(this).off(Vtiger_Edit_Js.referenceSelectionEvent);
				//when the type 10 changes
				jQuery(this).on(Vtiger_Edit_Js.referenceSelectionEvent, function(){
					var referenceElement = jQuery(this);
					var participantRow = referenceElement.closest('tr');
					//var agentTypeElement = participantRow.find('select[name^="agent_type"]');
					var agentType = participantRow.find('select[name^="agent_type"]').val();
					var agentId = referenceElement.val();
					//console.dir(agentType);
					if(agentType == 'Estimating Agent'){
						//changed agent is an estimating agent assembling a list of BA/OA agent ids to check for a match
						agentsList = [];
						jQuery('select[name^="agent_type"][name!="agent_type"] > option:selected').each(function(){
							if(jQuery(this).val() == 'Booking Agent' || jQuery(this).val() == 'Origin Agent'){
								agentsList.push(jQuery(this).closest('tr').find('input:hidden[name^="agents_id_"]').val());
							}
						});
						//see if the agentId is in the agentsList, if not clear the selection and throw a popup
						if(agentsList.indexOf(agentId) == -1){
							//clear the reference field
							referenceElement.siblings('.clearReferenceSelection').trigger('click');
							bootbox.alert('Estimating Agent must match Booking or Origin Agent for the current tariff type.');
						}
					} else if(agentType == 'Booking Agent' || agentType == 'Origin Agent'){
						//changed agent is a booking/origin agent checking to see if 1 of the 2 still match the estimating agent
						//assembling lists of agent ids & estimating agent elements
						agentsList = [];
						estimatingAgentsElements = [];
						jQuery('select[name^="agent_type"][name!="agent_type"] > option:selected').each(function(){
							if(jQuery(this).val() == 'Booking Agent' || jQuery(this).val() == 'Origin Agent'){
								agentsList.push(jQuery(this).closest('tr').find('input:hidden[name^="agents_id_"]').val());
							} else if(jQuery(this).val() == 'Estimating Agent'){
								estimatingAgentsElements.push(jQuery(this).closest('tr').find('input:hidden[name^="agents_id_"]'));
							}
						});
						estimatingAgentsElements.forEach(function(element, index, array){
							//console.dir('index: ' + index);
							//console.dir(element);
							var agentId = element.val();
							//console.dir(agentId)
							if(agentsList.indexOf(agentId) == -1){
								//console.dir('');
								//clear the reference field
								element.siblings('.clearReferenceSelection').trigger('click');
								bootbox.alert('Estimating Agent must match Booking or Origin Agent for the current tariff type.');
							}
						});
					}
					//jQuery(this).closest('tr').find('select[name^="agent_type"][name!="agent_type"]').trigger('change');
					thisInstance.sirvaOriginEstCheck(referenceElement);
				});
			});
		} else{
			jQuery('input:hidden[name^="agents_id_"]').each(function(){
				jQuery(this).off(Vtiger_Edit_Js.referenceSelectionEvent);
				//when the type 10 changes
				jQuery(this).on(Vtiger_Edit_Js.referenceSelectionEvent, function(){
					var referenceElement = jQuery(this);
                    var participantRow = referenceElement.closest('tr');
                    var agentType = participantRow.find('select[name^="agent_type"]').val();
                    if(
                        jQuery('input:hidden[name="instance"]').val() == 'graebel' &&
                        agentType == "Booking Agent"
                    ) {
                        var participantInstance = ParticipatingAgents_Edit_Js.getInstance();
                        var carrierRow = participantInstance.findParticipantRow('Carrier');
                        if (
                            carrierRow &&
                            carrierRow.attr('data-state') == 'auto-set'
                        ) {
                            businessLineValue = jQuery('select[name="business_line"]').find('option:selected').html();
                            thisInstance.setDefaultCarrier(businessLineValue);
                        }
                    }
					thisInstance.sirvaOriginEstCheck(referenceElement);
				});
			});
		}
	},
    setDefaultCarrier : function(businessLineValue){
        var bookingRow = participantInstance.findParticipantRow('Booking Agent');
        var carrierID = bookingRow.find('input:hidden[class="sourceField"]').val();
        var carrierName = bookingRow.find('input[name$="display"]').val();
        var interstateCarrierAgent = jQuery('input:hidden[name="InterstateCarrierAgents_id"]').val();
        var interstateCarrierAgentName = jQuery('input:hidden[name="InterstateCarrierAgentName"]').val();
        var commoditiesCarrierAgent = jQuery('input:hidden[name="CommoditiesCarrierAgents_id"]').val();
        var commoditiesCarrierAgentName = jQuery('input:hidden[name="CommoditiesCarrierAgentName"]').val();
        var intraTexasCarrierAgent = jQuery('input:hidden[name="IntraTexasCarrierAgents_id"]').val();
        var intraTexasCarrierAgentName = jQuery('input:hidden[name="IntraTexasCarrierAgentName"]').val();
        // var intraLocalGSACarrierAgent = jQuery('input:hidden[name="IntraLocalGSACarrierAgents_id"]').val();
        // var intraLocalGSACarrierAgentName = jQuery('input:hidden[name="IntraLocalGSACarrierAgentName"]').val();
        if(businessLineValue.indexOf('Interstate') >= 0){
            if (interstateCarrierAgent) {
                carrierID = interstateCarrierAgent;
                carrierName = interstateCarrierAgentName;
            }
        } else if (businessLineValue.indexOf('Work Space - Commodities') >= 0) {
            if (commoditiesCarrierAgent) {
                carrierID = commoditiesCarrierAgent;
                carrierName = commoditiesCarrierAgentName;
            }
        } else if (businessLineValue.indexOf('Intrastate') >= 0 && thisInstance.isTexas()){
            if (intraTexasCarrierAgent) {
                carrierID = intraTexasCarrierAgent;
                carrierName = intraTexasCarrierAgentName;
            }
        } //else if ((businessLineValue.indexOf('Local') >= 0 || businessLineValue.indexOf('Intrastate') >=0) && jQuery('select[name="billing_type"]').val() == 'GSA' ){
            // if (intraLocalGSACarrierAgent) {
            //     carrierID = intraLocalGSACarrierAgent;
            //     carrierName = intraLocalGSACarrierAgentName;
            // }
        // }
        participantInstance.setParticipantField(carrierRow, 'Carrier', carrierID, carrierName, 'full');
    },

    isTexas : function(){
        var originStateVal = jQuery('[name="origin_state"]').val().toUpperCase();
        var destinationStateVal = jQuery('[name="destination_state"]').val().toUpperCase();
        var texasArray = ['TX', 'TEXAS'];
        if (jQuery.inArray(originStateVal, texasArray) >= 0 && jQuery.inArray(destinationStateVal, texasArray) >= 0){
            return true;
        }
        return false;
    },

    registerOriginDestStates : function(){
        var thisInstance = this;
        var stateArray = [jQuery('input[name="origin_state"]'), jQuery('input[name="destination_state"]')];
        jQuery(stateArray).each(function() {
            jQuery(this).change(function () {
                var participantInstance = ParticipatingAgents_Edit_Js.getInstance();
                var carrierRow = participantInstance.findParticipantRow('Carrier');
                if (carrierRow && carrierRow.attr('data-state') == 'auto-set') {
                    var businessLineValue = jQuery('select[name="business_line"]').find('option:selected').html();
                    thisInstance.setDefaultCarrier(businessLineValue);
                }
            });
        });
    },

	sirvaOriginEstCheck : function(referenceElement){
		if(jQuery('input:hidden[name="instance"]').val() == 'sirva' && $('[name="record"]').val() == "") {
			var participantRow = referenceElement.closest('tr');
			var agentTypeElement = participantRow.find('select[name^="agent_type"]');
			var agentType = participantRow.find('select[name^="agent_type"]').val();
			var agentId = referenceElement.val();
			if (agentType == 'Origin Agent' || agentType == 'Estimating Agent') {
				var originAgentId = participantRow.closest('tbody').find('option:selected[value="5"]').closest('tr').find('input:hidden[name^="agents_id_"]').val();
				var estimatingAgentId = participantRow.closest('tbody').find('option:selected[value="7"]').closest('tr').find('input:hidden[name^="agents_id_"]').val();
				if (originAgentId == agentId && estimatingAgentId == agentId) {
					participantRow.find('input:radio[value="full"]').prop('checked', true);
				} else {
					participantRow.find('input:radio[value="no_rates"]').prop('checked', true);
				}
			}
		}
	},

	registerTypeChange : function(){
		thisInstance = this;
		//var agentTypeArr = ['Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent', 'Estimating Agent'];
		// console.dir(jQuery('select:not(:hidden)[name^="agent_type"][name!="agent_type"]'));
		jQuery('select[name^="agent_type_"][name!="agent_type"]').each(function(){
			jQuery(this).off('change');
			jQuery(this).on('change', function(){
				var selectElement = jQuery(this);
				var selectedTypeName = jQuery(this).find('option:selected').val();
				var usedTypes = [];
				jQuery('select[name^="agent_type"][name!="agent_type"][name!="' + selectElement.attr('name')+ '"]').each(function(){
					if(jQuery(this).find('option:selected').val()){
						usedTypes.push(jQuery(this).find('option:selected').val());
					}
				});
				// console.dir(usedTypes);
				if(usedTypes.indexOf(selectedTypeName) != -1){
					bootbox.alert('Type \'' + selectedTypeName + '\' already in use. Participanting Agents can not share types.');
					selectElement.find('option:selected').prop('selected', false);
					selectElement.find('option').first().prop('selected', true);
					selectElement.trigger('liszt:updated');
				} else{
					var selectRow = selectElement.closest('tr');
					selectRow.find('input:hidden[name^="agents_id_"]').trigger(Vtiger_Edit_Js.referenceSelectionEvent);
					if(
						selectedTypeName == 'Booking Agent' ||
						selectedTypeName == 'Estimating Agent'
					){
						//set to full access
						if(jQuery('input:hidden[name="instance"]').val() == 'sirva'){
							selectRow.find('input:radio[value="full"]').prop('disabled', false); //enable full radio
							selectRow.find('input:radio[value="no_rates"]').prop('disabled', true); //disable all other radios
							selectRow.find('input:radio[value="no_access"]').prop('disabled', true);
							selectRow.find('input:radio[value="read_only"]').prop('disabled', true);

							selectRow.find('input:radio[value="full"]').prop('checked', true); //check it
						} else{
							selectRow.find('input:radio[value="full"]').prop('checked', true);
						}
					} else if(selectedTypeName == 'Origin Agent'){
						//set to no rates
						if(jQuery('input:hidden[name="instance"]').val() == 'sirva'){
							//logic to handle if origin agent is also an estimating agent
							var agentId = jQuery(this).closest('tr').find('input:hidden[name^="agents_id_"]').val();
							var agentsList = [];
							var fullAccess = false;
							jQuery('select[name^="agent_type"][name!="agent_type"] > option:selected').each(function(){
								if(jQuery(this).val() == 'Estimating Agent'){
									agentsList.push(jQuery(this).closest('tr').find('input:hidden[name^="agents_id_"]').val());
								}
							});
							if(agentsList.indexOf(agentId) != -1){
								fullAccess = true;
							}

                            selectRow.find('input:radio[value="full"]').prop('disabled', false); //enable no-rates radio
                            selectRow.find('input:radio[value="no_rates"]').prop('disabled', false);
                            selectRow.find('input:radio[value="no_access"]').prop('disabled', false);
                            selectRow.find('input:radio[value="read_only"]').prop('disabled', false);
							if(fullAccess){
								selectRow.find('input:radio[value="full"]').prop('checked', true); //check it
							} else {
								selectRow.find('input:radio[value="no_rates"]').prop('checked', true); //check it
							}
						} else{
							selectRow.find('input:radio[value="no_rates"]').prop('checked', true);
						}
					} else if(selectedTypeName == 'Hauling Agent' || selectedTypeName == 'Destination Agent'){
						//set to no access
						if(jQuery('input:hidden[name="instance"]').val() == 'sirva'){
                            selectRow.find('input:radio[value="no_access"]').prop('disabled', false); //enable no-access radio
							selectRow.find('input:radio[value="full"]').prop('disabled', true); //disable all other radios
							selectRow.find('input:radio[value="no_rates"]').prop('disabled', true);
							selectRow.find('input:radio[value="read_only"]').prop('disabled', true);

							selectRow.find('input:radio[value="no_access"]').prop('checked', true); //check it
						} else{
							selectRow.find('input:radio[value="no_access"]').prop('checked', true);
						}
					} else{
						//for anything else: re-enable all the buttons
						if(jQuery('input:hidden[name="instance"]').val() == 'sirva'){
							selectRow.find('input:radio[value="full"]').prop('disabled', false); //re-enable all of the radios
							selectRow.find('input:radio[value="no_rates"]').prop('disabled', false);
							selectRow.find('input:radio[value="read_only"]').prop('disabled', false);
							selectRow.find('input:radio[value="no_access"]').prop('disabled', false);
						}
					}
				}
			});
		});
		jQuery('select[name="brand"]').trigger('change');
	},

	initializeOwnerAgents : function(){
		var thisInstance = this;
		//set the current users primary owner agent as the booking/origin/estimating agent for new records
		if(
			!jQuery('input[name="record"]').val() &&
			jQuery('input[name="primary_owner_agent"]').val() &&
			// Don't do this if this if created from an Opportunity
			jQuery('.participantRow').length <= 1
		) {
            //console.dir('primary_owner_agent: ' + jQuery('input[name="primary_owner_agent"]').val());
            primaryOwnerAgent = jQuery('input[name="primary_owner_agent"]').val();
            primaryAgentName = jQuery('input[name="primary_owner_agent_name"]').val();
            jQuery('.addParticipant').first().trigger('click');
            //moveHQ currently uses one default participating agent, not three.
            if (jQuery('[name="movehq"]').val() == 0) {
                jQuery('.addParticipant').first().trigger('click');
                jQuery('.addParticipant').first().trigger('click');
            }
			if(jQuery('input:hidden[name="instance"]').val() == 'graebel'){
				jQuery('.addParticipant').first().trigger('click');
                jQuery('.addParticipant').first().trigger('click');
			}
			if(
				jQuery('input:hidden[name="instance"]').val() == 'sirva' &&
				jQuery('input:hidden[name="hauling_agent_id"]').val()
			){
				jQuery('.addParticipant').first().trigger('click');
			}
			//now that we have blank rows, find all the rows
			bookingRow = jQuery('.newParticipant').first();
            if (jQuery('[name="movehq"]').val() == 0) {
                originRow = bookingRow.next('.newParticipant');
                estimatingRow = originRow.next('.newParticipant');
            } else if (jQuery('input:hidden[name="hauling_agent_id"]').val()) {
                jQuery('.addParticipant').first().trigger('click');
                haulingRow = bookingRow.next('.newParticipant');
            }
			if(jQuery('input:hidden[name="instance"]').val() == 'graebel'){
				salesOrgRow = estimatingRow.next('.newParticipant');
                carrierRow = salesOrgRow.next('.newParticipant');
                carrierRow.attr('data-state', 'auto-set');
			}
			if(
				jQuery('input:hidden[name="instance"]').val() == 'sirva' &&
				jQuery('input:hidden[name="hauling_agent_id"]').val()
			){
				haulingRow = estimatingRow.next('.newParticipant');
			}
			//now that we have all the row elements fill in the fields
            if (jQuery('[name="movehq"]').val() == 1){
                var bookingAgentId = jQuery('[name = "agentid"]').val();
                var bookingAgentName = jQuery('[name = "agentid"]').text();
                var dataURL = "index.php?module=Opportunities&action=GetParticipantIdFromAgentOwner&agentmanagerid=" + bookingAgentId;
                AppConnector.request(dataURL).then(
                    function (data) {
                        if(data.success){
                            bookingAgentId = data.result['agentid'];
                            bookingAgentName = data.result['agentName'];
                        }
                        thisInstance.setParticipantField(bookingRow, 'Booking Agent', bookingAgentId, bookingAgentName, 'full');
                    }
                );
            }
			else {
			    thisInstance.setParticipantField(bookingRow, 'Booking Agent', primaryOwnerAgent, primaryAgentName, 'full');
                thisInstance.setParticipantField(originRow, 'Origin Agent', primaryOwnerAgent, primaryAgentName, 'no_rates');
                thisInstance.setParticipantField(estimatingRow, 'Estimating Agent', primaryOwnerAgent, primaryAgentName, 'full');
			}
			if(jQuery('input:hidden[name="instance"]').val() == 'graebel'){
				thisInstance.setParticipantField(salesOrgRow, 'Sales Org', primaryOwnerAgent, primaryAgentName, 'full');
                thisInstance.setParticipantField(carrierRow, 'Carrier', primaryOwnerAgent, primaryAgentName, 'full');
			}
			if(
				jQuery('input:hidden[name="instance"]').val() == 'sirva' &&
				jQuery('input:hidden[name="hauling_agent_id"]').val()
			){
				thisInstance.setParticipantField(haulingRow, 'Hauling Agent', jQuery('input:hidden[name="hauling_agent_id"]').val(), jQuery('input:hidden[name="hauling_agent_name"]').val(), 'full');
			}
		}
	},

	setParticipantField : function(participantRow, agentType, agentId, agentName, viewLevel){
		//set the agent_type
		participantRow.find('select > option:selected').prop('selected', false);
		participantRow.find('select > option[value="' + agentType + '"]').prop('selected', true);
		participantRow.find('select').trigger('change');
		participantRow.find('select').trigger('liszt:updated');
		//set the reference field's id
		//participantRow.find('input:hidden[name^="agents_id"]').val(agentId);
		this.setReferenceFieldValue(participantRow, {id: agentId, name: agentName});
		//set the view level
		participantRow.find('input:radio:checked').prop('checked', false);
		participantRow.find('input:radio[value="' + viewLevel + '"]').prop('checked', true);
	},

    findParticipantRow : function(participantType) {
        foundRow = false;
	    //This will return the row of the first participant of a particular type.
        jQuery('input:hidden[name^="agents_id_"]').each(function(){
            var referenceElement = jQuery(this);
            var participantRow = referenceElement.closest('tr');
            agentType = participantRow.find('select[name^="agent_type"]').val();
            if (agentType == participantType){
                foundRow = participantRow;
                return false;
            }
        });
        return foundRow;
    },

    // registerUserModifiedParticipant : function() {
    //     jQuery('input:hidden[name^="agents_id_"]').each(function(){
    //         jQuery(this).off(Vtiger_Edit_Js.referenceSelectionEvent);
    //         //when the type 10 changes
    //         jQuery(this).on(Vtiger_Edit_Js.referenceSelectionEvent, function() {
    //             var referenceElement = jQuery(this);
    //             var participantRow = referenceElement.closest('tr');
    //             var agentType = participantRow.find('select[name^="agent_type"]').val();
    //             if(agentType == 'Carrier'){
    //                 participantRow.attr('data-state', 'user-modified');
    //             }
    //         });
    //     });
    // },

	//was dump do do this in orders, heck it was dumb to do in the js
	//now it's just going to automatically set the coordinating agent when saving the csc move role
	//TODO: remove this garbage
	/*initializeCoordinatorAgent : function() {
		if(jQuery('input:hidden[name="instance"]').val() == 'graebel'){
			//create coordinator agent
			//console.dir(jQuery('input:hidden[name="coordinator_agents_id"]').val());
			//console.dir(jQuery('select[name^="agents_type_"][value="Coordinator Agent"]'));
			if(jQuery('input:hidden[name="coordinator_agents_id"]').val() && jQuery('option:selected[value="Coordinating Agent"]').length == 0){
				//console.dir("Coordinator don't exist, but it should");
				bootbox.confirm("A customer service coordinator was detected in the order's move roles, would you like to add this agency as a participant?", function(result) {
					if(result) {
						jQuery('.addParticipant').first().trigger('click');
						coordinatorRow = jQuery('.participantRow').last();
						thisInstance.setParticipantField(coordinatorRow, 'Coordinating Agent', jQuery('input:hidden[name="coordinator_agents_id"]').val(), jQuery('input:hidden[name="coordinator_agent_name"]').val(), 'full');
					}
				});
			} else if (jQuery('input:hidden[name="coordinator_agents_id"]').val() && jQuery('option:selected[value="Coordinating Agent"]').closest('tr').find('input:hidden[name^="agents_id"]').val() != jQuery('input:hidden[name="coordinator_agents_id"]').val()){
				//console.dir('Coordinator does exist, but it needs changed');
				bootbox.confirm("The customer service coordinator detected in the Order's Move Roles is different from the currently selected Coordinator Agent, would you like to override the Coordinator Agent?", function(result) {
					if(result) {
						//clear any other agent with the same agents_id
						jQuery('input:hidden[name^="agents_id_"][value="' + jQuery('input:hidden[name="coordinator_agents_id"]').val() + '"]').closest('td').find('.icon-remove-sign').trigger('click');
						//then plug coordinator agent into existing row
						var coordinatorRow = jQuery('option:selected[value="Coordinating Agent"]').closest('tr');
						thisInstance.setParticipantField(coordinatorRow, 'Coordinating Agent', jQuery('input:hidden[name="coordinator_agents_id"]').val(), jQuery('input:hidden[name="coordinator_agent_name"]').val(), 'full');
					}
				});
			}
		}
	},*/

	graebelSalesPersonOrg : function () {
		jQuery('select[name="sales_person"]').on('change', function () {
			if (jQuery('input:hidden[name="instance"]').val() == 'graebel' && jQuery('input:hidden[name="module"]').val() == 'Orders') {
				var salesAgentId = jQuery('input[name="primary_owner_agent"]').val();
				var salesAgentName = jQuery('input[name="primary_owner_agent_name"]').val();
				//grab new sales person agent id
				var dataURL = "index.php?module=Orders&action=getSalesAgent&salesperson=" + jQuery('select[name="sales_person"] > option:selected').val();
				AppConnector.request(dataURL).then(
					function (data) {
						if (data) {
							salesAgentId = data.result['id'];
							salesAgentName = data.result['name'];
							//create/update salesorg agent
							if (jQuery('option:selected[value="Sales Org"]').length == 0) {
								console.dir("Sales org don't exist, but it should");
								bootbox.confirm("No Sales Org detected. Would you like to create one with the sales-person's primary agent?", function (result) {
									if (result) {
										jQuery('.addParticipant').first().trigger('click');
										salesOrgRow = jQuery('.participantRow').last();
										thisInstance.setParticipantField(salesOrgRow, 'Sales Org', salesAgentId, salesAgentName, 'full');
									}
								});
							} else if (jQuery('option:selected[value="Sales Org"]').closest('tr').find('input:hidden[name^="agents_id"]').val() != salesAgentId) {
								console.dir('Sales org does exist, but it needs changed');
								bootbox.confirm("The salesperson agent detected is different from the currently selected Sales Org agent, would you like to override the Sales Org agent?", function (result) {
									if (result) {
										//jQuery('input:hidden[name^="agents_id_"][value="' + salesAgentId + '"]').closest('td').find('.icon-remove-sign').trigger('click');
										//then plug coordinator agent into existing row
										salesOrgRow = jQuery('option:selected[value="Sales Org"]').closest('tr');
										thisInstance.setParticipantField(salesOrgRow, 'Sales Org', salesAgentId, salesAgentName, 'full');
									}
								});
							}
						}
					}, function (error) {
						console.dir(error);
					}
				);
			}
		});
	},

	doUpdateFromAddresses: function(addressList, agent)
	{
			var deferred = jQuery.Deferred();
			var params = {
					module:       'Google',
					action:       'MilesCalculator',
					table:        'agents',
					tableid:      'agentsid',
					list:         addressList,
					agent:				agent
			};

			AppConnector.request(params).then(function (data) {
				if(data.success) {
					// The returned array gives us three entries
					// [0] - Distance from Office (agent's address) to Office (always 0)
					// [1] - Distance from Office to Destination
					// [2] - Distance from Destination to Office
					deferred.resolve(data.result[1]['miles']);
				}
			}, function(err) {
					deferred.reject(err);
					bootbox.alert('An error has occurred with the Google miles / travel time calculator.');
			});
			return deferred.promise();
	},

	getDistance : function(agentid) {
		var addressList = [];
		//Leaving this comment in for the inevitability that they want this for the origin agent too
		//addressList.push(
		// 	{
		// 		type: 'Origin',
		// 		address: Vtiger_Edit_Js.getAddress(jQuery('.contentsDiv'),'origin_address1', 'origin_city', 'origin_state', 'origin_zip'),
		// 		sequence: 0,
		// 	}
		//);
		addressList.push(
			{
				type: 'Destination',
				address: Vtiger_Edit_Js.getAddress(jQuery('.contentsDiv'),'destination_address1', 'destination_city', 'destination_state', 'destination_zip'),
				sequence: 100,
			}
		);
		return thisInstance.doUpdateFromAddresses(addressList, agentid);
	},

	checkAgentDistance : function() {
		jQuery('input:hidden[name^="agents_id_"]').on(Vtiger_Edit_Js.referenceSelectionEvent,function(){
			ele = jQuery(this);
			if (ele.val() == '') {
				return;
			}
			rowid = ele.attr('name').split('_')[2];
			eleType = jQuery('select[name="agent_type_' + rowid +'"]').val();
			if(eleType == 'Destination Agent' /* || eleType == 'Origin Agent' */) {
				thisInstance.getDistance(ele.val()).then(function(data){
					if(Number(data) >= 50) {
						bootbox.alert(eleType + ' is more than 50 miles away from the address.');
					}
				});
			}
		});
	},

	registerEvents : function() {
		this.registerAddParticipantButtons();
		this.registerRemoveParticipantButton();
		this.registerAgentChange();
		this.registerTypeChange();
        if(
            jQuery('[name="movehq"]').val() == 0 ||
            jQuery('input:hidden[name="instance"]').val() != 'graebel'
        ) {
            this.initializeOwnerAgents();
        }
		this.graebelSalesPersonOrg();
        this.registerOriginDestStates();
		this.registerAutoCompleteFields(jQuery('[name="participatingAgentsTable"]'));
		this.checkAgentDistance();
//        this.registerUserModifiedParticipant();
		if(jQuery('input:hidden[name="instance"]').val() == 'sirva' && $('[name="record"]').val() == "") {
			jQuery('select[name^="agent_type"][name!="agent_type"]').trigger('change');
		}
	},
});

jQuery(document).ready(function() {
	var instance = ParticipatingAgents_Edit_Js.getInstance();
	instance.registerEvents();
});
