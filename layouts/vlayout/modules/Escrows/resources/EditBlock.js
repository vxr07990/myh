Vtiger_Edit_Js("Escrows_EditBlock_Js", {
	getInstance: function() {
		return new Escrows_EditBlock_Js();
	}
}, {
	registerRemoveEscrowsButton : function(container){
		container.on( 'click', '.deleteMappingButton', function(){
			if(jQuery(this).siblings('input:hidden[name^="escrowsid"]').val() == ''){
				jQuery(this).closest('div.EscrowsRecords').remove()
			} else{
				jQuery(this).closest('div.EscrowsRecords').addClass('hide');
				jQuery(this).siblings('input:hidden[name^="mapping_deleted_"]').val('deleted');
			}
			var rowno=jQuery('div.EscrowsRecords').length;
			jQuery('[name="EscrowsTable"]').find('[name="numMapping"]').val(rowno);
		});
	},

	registerCopyEscrowsButton : function(container){
		var thisInstance = this;
		var editViewForm = this.getForm();
		container.on( 'click', '.copyMappingButton', function(){
			var EscrowsRecords = jQuery(this).closest('div.EscrowsRecords');
			var copyRowNo = EscrowsRecords.data('row-no');
			var rowno=jQuery('div.EscrowsRecords').length;
			var copyData=EscrowsRecords.find(':input').serialize();
			copyData = copyData + '&module=Escrows&view=MassActionAjax&mode=duplicateBlock&rowno='+rowno+'&copy_rowno='+copyRowNo;
			var viewParams = {
				"type": "POST",
				"url": 'index.php',
				"dataType": "html",
				"data": copyData
			};

			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newItemCodeMapping=jQuery(data);
						jQuery('div.EscrowsList').append(newItemCodeMapping);
						app.changeSelectElementView(jQuery('div.EscrowsList'));
						jQuery('[name="EscrowsTable"]').find('[name="numMapping"]').val(rowno+1);
					}
				}
			)
		});
	},

	registerAddEscrowsButtons : function() {
		var thisInstance = this;
		var editViewForm = this.getForm();
		var container = jQuery('[name="EscrowsTable"]');

		var rowno=jQuery('div.EscrowsRecords').length;
		container.find('.addEscrows').on('click', function () {
            var agentid='';
            var agentidElement  = editViewForm.find('[name="agentid"]');
            if(agentidElement.length > 0 && agentidElement.val().length > 0) {
                agentid = agentidElement.val();
            }
			var viewParams = {
				"type": "POST",
				"url": 'index.php?module=Escrows',
				"dataType": "html",
				"data": {
					'view': 'MassActionAjax',
					'mode': 'generateNewBlock',
					'rowno': rowno,
                    'agentid' :agentid
				}
			};

			AppConnector.request(viewParams).then(
				function (data) {
					if (data) {
						var newItemCodeMapping=jQuery(data);
						jQuery('div.EscrowsList').append(newItemCodeMapping);
						app.changeSelectElementView(jQuery('div.EscrowsList'));
						jQuery('[name="EscrowsTable"]').find('[name="numMapping"]').val(rowno+1);
					}
				}
			)
		});
	},

	registerEventForChangeBlockTitle: function (container) {
		var thisInstance = this;
		container.on("change",'[name^="escrows_desc_"]', function () {
			var EscrowsRecords = jQuery(this).closest('div.EscrowsRecords');
			var EscrowsTitle = EscrowsRecords.find('.EscrowsTitle');
			var escrows_desc = jQuery(this).val();
			if(escrows_desc !='') {
				EscrowsTitle.html(escrows_desc)
			}
		});
	},

	registerEvents : function() {
		this.registerAddEscrowsButtons();
		var container = jQuery('div.EscrowsList');
		this.registerRemoveEscrowsButton(container);
		this.registerCopyEscrowsButton(container);
		this.registerEventForChangeBlockTitle(container);

	},
});

jQuery(document).ready(function() {
	var instance = Escrows_EditBlock_Js.getInstance();
	instance.registerEvents();
});
