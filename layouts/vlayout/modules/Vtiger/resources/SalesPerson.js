Vtiger_Edit_Js('Sales_Person_Js', {
    getInstance: function() {
        return new Sales_Person_Js();
    },
    I: function() {
        return this.getInstance();
    }
}, {
    // This is here in order to allow the changing of the coordinator to the default if the sales person is changed by the user,
    // But not when the page loads.
    firstRun: true,

    currentSalesPerson: $('[name="sales_person"]').find('option:selected').val(),
    currentOwner: $('[name="agentid"]').find('option:selected').val(),

    // Event callback to be used on agent and salesperson change for custom logic.
    salesPersonCallback: function(){},
    agentCallback: function(){},

    onAgentChange: function(cb) {
        if(typeof cb == 'function') {
            this.agentCallback = cb
        }else {
            console.error("Invalid callback specified.");
        }
    },

    onSalesPersonChange: function(cb) {
        if(typeof cb == 'function') {
            this.salesPersonCallback = cb;
        }else {
            console.error("Invalid callback specified.");
        }
    },

    updateSalesPeople: function(salespeople) {
        var instance = $('input[name="instance"]').val();
        if (instance != 'sirva') {
            return;
        }

        if(salespeople.length == 0) {
            bootbox.alert("There are no sales people tied to the selected Origin Agency.");
        }

        // AJAX Result.
        var selectTag = jQuery('select[name="sales_person"]');

        // Keep old default if the default from AJAX is null.
        var defaultPerson = selectTag.find(":selected").val();

        //Clear existing sales people
        selectTag.find('option').remove();

        // Re-add "Select An Option"
        selectTag.append('<option>Select an Option</option>');

        jQuery.each(salespeople, function(index, value) {
            var optionTag = '<option value="' + index + '" data-picklistvalue="' + value + '"';
            if (index == defaultPerson) {
                optionTag += ' selected';
            }
            optionTag += ' data-recordaccess="true" data-userid="' + jQuery('#current_user_id').val() + '">';
            optionTag += value + "</option>";
            selectTag.append(optionTag);
        });

        selectTag.trigger('liszt:updated');
    },

    getCoordinators: function(agentid, userid) {
        var instance = $('input[name="instance"]').val();
        var thisInstance = this;
        if (instance != 'sirva') {

            //@NOTE: Don't make the call, might as well optimize somewhat.
            // Just return.
            return;
        }

        var dataUrl = "index.php?module=AgentManager&action=GetCoordinators&agentmanagerid=" + agentid;
        if (userid) {
            dataUrl += '&userid=' + userid;
        }
        AppConnector.request(dataUrl).then(
            function(data) {
                if (data.success) {
                    thisInstance.updateCoordinators(data.result);
                }
            }
        );
    },

    updateCoordinators: function(coord_data) {
        var instance = $('input[name="instance"]').val();
        if (instance != 'sirva') {
            //@NOTE: Don't make the call, might as well optimize somewhat.
            // Just return.
            return;
        }

        var coordinators = coord_data.coordinators;
        var defaultCoord = coord_data.default;

        if(coordinators == null) {
            return;
        }

        // Current Dropdown.
        var optGroup = jQuery('[name="assigned_user_id"]').find('optgroup[label="Users"]');

        // AJAX Result.
        var selectTag = jQuery('select[name="assigned_user_id"]');

        // Keep old default if the default from AJAX is null.
        // TFS28927: keep previously selected coord if it is present in the new list.
        var newCoordList = coordinators.map(function(a) {
            return a.id;
        });
        var selectedCoord = optGroup.find(":selected").val();
        if (defaultCoord == null) {
            defaultCoord = selectedCoord;
        }

        //Clear existing coordinators
        optGroup.empty();

        $.each(coordinators, function(index, value) {
            var optionTag = '<option value="' + value.id + '" data-picklistvalue="' + value.first_name + ' ' + value.last_name + '"';
            if (value.id == defaultCoord) {
                optionTag += ' selected';
            }
            optionTag += ' data-recordaccess="true" data-userid="' + jQuery('#current_user_id').val() + '">';
            optionTag += value.first_name + " " + value.last_name + "</option>";
            optGroup.append(optionTag);
        });

        jQuery('[name="assigned_user_id"]').trigger('liszt:updated');

        // Set this to false to indicate the loading and initial runs of the handlers are completed.
        this.firstRun = false;
    },

    registerChangeSalesPerson: function() {
        var thisInstance = this;
        var sales_person_ele = $('select[name="sales_person"]');

        var handler = function() {
            thisInstance.salesPersonCallback();

            var new_sales_person = sales_person_ele.find('option:selected').val();
            if (new_sales_person != thisInstance.currentSalesPerson) {
                $('input[name="sent_to_mobile"]').prop("checked", false);
                thisInstance.currentSalesPerson = new_sales_person;
                thisInstance.getCoordinators(thisInstance.currentOwner, thisInstance.currentSalesPerson);
            }
        };

        sales_person_ele.on('change', handler);

        // Only run default on new record.
        if($('[name="record"]').val() == '') {
            handler();
        }
    },

    getAgentInfo: function(agent_ele) {
        // Fallback in case an origin agent decides not to exist.
        var agentid = agent_ele.val();
        var agenttype = "manager";

        // Get agent based on Origin Agent.
        thisInstance.findParticipatingAgent("Origin Agent", function(type, name, id) {
            agenttype = "roster";
            agentid = $('[name="agents_id_' + id + '"]').val();
        });

        // Set class-wides.
        thisInstance.currentOwner = agentid;

        return {'id': agentid, 'type': agenttype};
    },

    registerChangeAgentId: function() {
        var thisInstance = this;
        var agent_ele = $('select[name="agentid"]');
        var part_agents = $('[name^="agents_id_"');
        var record = $('[name="record"]').val();

        var handler = function() {
            var agent = thisInstance.getAgentInfo(agent_ele);

            // Setup URL for call.
            var params = {
                'type': 'GET',
                'url': 'index.php',
                'data': {
                    'module': 'Opportunities',
                    'action': 'PopulateAgentDetails',
                    'source': agent.id,
                    'source_type': agent.type,
                    'record': record
                }
            };

            // Do that AJAX now.
            AppConnector.request(params).then(
                function(data) {
                    if (data.success) {
                        thisInstance.agentCallback(data);

                        // Update only if sirva, as this block is only returned if it is sirva.
                        if (
                            data.result &&
                            data.result.people
                        ) {
                            thisInstance.updateSalesPeople(data.result.people.salespeople);
                            thisInstance.updateCoordinators(data.result.people.coordinators);
                        }
                    } else {
                        console.error("Error occurred while gathering agent details.");
                    }
                }
            );
        };

        agent_ele.on('change', handler);
        part_agents.on(Vtiger_Edit_Js.postReferenceSelectionEvent, handler);
    },

    registerSirvaEvents: function() {
        this.registerChangeAgentId();
    },

    registerEvents: function() {
        this.registerChangeSalesPerson();

        if($('[name="instance"]').val() == 'sirva') {
            this.registerSirvaEvents();
        }
    }
});
