/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Users_Detail_Js",{

	triggerChangePassword : function (CHPWActionUrl, module){
		AppConnector.request(CHPWActionUrl).then(
			function(data) {
				if(data) {
					var callback = function(data) {
						var params = app.validationEngineOptions;
						params.onValidationComplete = function(form, valid){
							if(valid){
								Users_Detail_Js.savePassword(form)
							}
							return false;
						}
						jQuery('#changePassword').validationEngine(app.validationEngineOptions);
					}
					app.showModalWindow(data, function(data){
						if(typeof callback == 'function'){
							callback(data);
						}
					});
				}
			}
		);
	},

	savePassword : function(form){
		var new_password  = form.find('[name="new_password"]');
		var confirm_password = form.find('[name="confirm_password"]');
		var old_password  = form.find('[name="old_password"]');
		var userid = form.find('[name="userid"]').val();
		var relModule = form.find('[name="relModule"]').val();

		if(new_password.val() == confirm_password.val()){
			var params = {
				'module': 'Users',
                'relModule': relModule,
				'action' : "SaveAjax",
				'mode' : 'savePassword',
				'old_password' : old_password.val(),
				'new_password' : new_password.val(),
				'userid' : userid
			}
			AppConnector.request(params).then(
				function(data) {
					if(data.success){
						app.hideModalWindow();
						Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.message));
					}else{
						new_password.validationEngine('showPrompt', app.vtranslate(data.error.message) , 'error','topLeft',true);
						return false;
					}
				}
			);
		} else {
			new_password.validationEngine('showPrompt', app.vtranslate('JS_REENTER_PASSWORDS') , 'error','topLeft',true);
			return false;
		}
	},

	saveExchangeCredentials : function(form){
		var hostname  = form.find('[name="exchange_hostname"]');
		var username = form.find('[name="exchange_username"]');
		var password  = form.find('[name="exchange_password"]');
		var userid = form.find('[name="userid"]').val();

		var params = {
			'module': 'Users',
			'action' : "SaveAjax",
			'mode' : 'saveExchangeCredentials',
			'hostname' : hostname.val(),
			'username' : username.val(),
			'password' : password.val(),
			'userid' : userid
		}
		AppConnector.request(params).then(
			function(data) {
				if(data.success){
					app.hideModalWindow();
					Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result));
					jQuery('#Users_detailView_fieldValue_user_exchange_hostname').find('.value').html(params.hostname);
					jQuery('#Users_detailView_fieldValue_user_exchange_username').find('.value').html(params.username);
					jQuery('#Users_detailView_fieldValue_user_exchange_password').find('.value').html('********************');
				}else{
					password.validationEngine('showPrompt', app.vtranslate(data.error.message) , 'error','topLeft',true);
					return false;
				}
			}
		);
	},

	/*
	 * function to trigger delete record action
	 * @params: delete record url.
	 */
    triggerDeleteUser : function(deleteUserUrl) {
		var message = app.vtranslate('LBL_DELETE_USER_CONFIRMATION');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
				AppConnector.request(deleteUserUrl).then(
				function(data){
					if(data){
						var callback = function(data) {
							var params = app.validationEngineOptions;
							params.onValidationComplete = function(form, valid){
								if(valid){
									Users_Detail_Js.deleteUser(form)
								}
								return false;
							}
							jQuery('#deleteUser').validationEngine(app.validationEngineOptions);
						}
						app.showModalWindow(data, function(data){
							if(typeof callback == 'function'){
								callback(data);
							}
						});
					}
				});
			},
			function(error, err){
			}
		);
	},

	deleteUser: function (form){
		var userid = form.find('[name="userid"]').val();
		var transferUserId = form.find('[name="tranfer_owner_id"]').val();

		var params = {
				'module': app.getModuleName(),
				'action' : "DeleteAjax",
				'mode' : 'deleteUser',
				'transfer_user_id' : transferUserId,
				'userid' : userid
			}
		AppConnector.request(params).then(
			function(data) {
				if(data.success){
					app.hideModalWindow();
					Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.status.message));
					var url = data.result.listViewUrl;
					window.location.href=url;
				}
			}
		);
	},

	triggerTransferOwner : function(transferOwnerUrl){
		var message = app.vtranslate('LBL_TRANSFEROWNER_CONFIRMATION');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
				AppConnector.request(transferOwnerUrl).then(
				function(data){
					if(data){
						var callback = function(data) {
							var params = app.validationEngineOptions;
							params.onValidationComplete = function(form, valid){
								if(valid){
									Users_Detail_Js.transferOwner(form)
								}
								return false;
							}
							jQuery('#transferOwner').validationEngine(app.validationEngineOptions);
						}
						app.showModalWindow(data, function(data){
							if(typeof callback == 'function'){
								callback(data);
							}
						});
					}
				});
			},
			function(error, err){
			}
		);
	},

	transferOwner : function(form){
		var userid = form.find('[name="userid"]').val();
		var transferUserId = form.find('[name="tranfer_owner_id"]').val();

		var params = {
				'module': app.getModuleName(),
				'action' : "SaveAjax",
				'mode' : 'transferOwner',
				'transfer_user_id' : transferUserId,
				'userid' : userid
			}
		AppConnector.request(params).then(
			function(data) {
				if(data.success){
					app.hideModalWindow();
					Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.message));
					var url = data.result.listViewUrl;
					window.location.href=url;
				}
			}
		);
	}
},{

	usersEditInstance : false,

	updateStartHourElement : function(form) {
		this.usersEditInstance.triggerHourFormatChangeEvent(form);
		this.updateStartHourElementValue();
	},
	hourFormatUpdateEvent  : function() {
		var thisInstance = this;
		this.getForm().on(this.fieldUpdatedEvent, '[name="hour_format"]', function(e, params){
			thisInstance.updateStartHourElementValue();
		});
	},

	updateStartHourElementValue : function() {
		var form = this.getForm();
		var startHourSelectElement = jQuery('select[name="start_hour"]',form);
		var selectedElementValue = startHourSelectElement.find('option:selected').text();
		startHourSelectElement.closest('td').find('span.value').text(selectedElementValue);
	},

	startHourUpdateEvent : function(form) {
		var thisInstance = this;
		form.on(this.fieldUpdatedEvent, '[name="start_hour"]', function(e, params){
			thisInstance.updateStartHourElement(form);
		});
	},

	registerExchangeCredentialsButton : function() {
		var thisInstance = this;
		var button = jQuery('#exchangeCredentialsButton');
		if(button.length < 1) {
			return;
		}
		button.off('click').on('click', function() {
			AppConnector.request('index.php?module=Users&view=EditAjax&mode=setExchangeCredentials&recordId='+jQuery('#recordId').val()).then(
				function(data) {
					if(data) {
						var callback = function(data) {
							var params = app.validationEngineOptions;
							params.onValidationComplete = function(form, valid){
								if(valid){
									Users_Detail_Js.saveExchangeCredentials(form)
								}
								return false;
							}
							jQuery('#setExchangeCredentials').validationEngine(app.validationEngineOptions);
						}
						app.showModalWindow(data, function(data){
							if(typeof callback == 'function'){
								callback(data);
							}
						});
					}
				}
			);
		});
	},

	registerEvents : function() {
        this._super();
		var form = this.getForm();
		this.usersEditInstance = Vtiger_Edit_Js.getInstance();
		this.updateStartHourElement(form);
		this.hourFormatUpdateEvent();
		this.startHourUpdateEvent(form);
		this.registerExchangeCredentialsButton();
	}

});

function getDropboxAuth() {
	var dataURL = "index.php?module=Users&action=GetDropboxAuth&method=0";
	AppConnector.request(dataURL).then(
		function(data) {
			if(data.success) {
				var html = "Proceed to the <a href='"+data.result.url+"' target='_blank' style='text-decoration:underline'>Dropbox Authorization Page</a>, \'click Allow\', and copy the authorization code into the box below.";
				html = html+"<input type='text' name='dropbox_auth'><button type='button' onclick='setDropboxAuth()'>Send Code</button>";
				jQuery('span[id="dropbox_auth_token"]').html(html);
			}
		},
		function(error, err) {
			alert(err);
		}
	);
}

function setDropboxAuth() {
	var authCode = jQuery('input[name="dropbox_auth"]').val();
	var dataURL = "index.php?module=Users&action=GetDropboxAuth&method=1&authCode="+authCode;

	AppConnector.request(dataURL).then(
		function(data) {
			if(data.success) {
				//alert(data.result.access);
				jQuery('span[id="dropbox_auth_token"]').html("Thank you for linking Dropbox.");
			}
		},
		function(error, err) {
			alert(err);
		}
	);
}
