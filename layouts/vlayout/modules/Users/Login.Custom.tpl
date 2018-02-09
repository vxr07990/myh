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
<!DOCTYPE html>
<html>
	<head>
		<title>{if $HQ == 1}{vtranslate('MOVEHQ_APPTITLE')} Login Page{else}{vtranslate('MOVECRM_APPTITLE')} Login Page{/if}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- for Login page we are added -->
        <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
        <link rel="manifest" href="manifest.json">
        <link rel="mask-icon" href="safari-pinned-tab.svg" color="#f84e57">
        <meta name="theme-color" content="#ffffff">
		<link href="libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="libraries/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="libraries/bootstrap/css/jqueryBxslider.css" rel="stylesheet" />
        <link rel="shortcut icon" type="image/x-icon" href="{vtranslate('FAVICO_PATH')}">
		<script src="libraries/jquery/jquery.min.js"></script>
		<script src="libraries/jquery/boxslider/jqueryBxslider.js"></script>
		<script src="libraries/jquery/boxslider/respond.min.js"></script>
		<script>
			jQuery(document).ready(function(){
			scrollx = jQuery(window).outerWidth();
			window.scrollTo(scrollx,0);
			slider = jQuery('.bxslider').bxSlider({
				auto: true,
				pause: 4000,
				randomStart : true,
				autoHover: true
			});
			jQuery('.bx-prev, .bx-next, .bx-pager-item').live('click',function(){ slider.startAuto(); });
			});
		</script>
	</head>
	<body>
		<div class="container-fluid login-container">
			<div class="row-fluid">
				<div class="span3">
					<div class="logo">
						<br />
                        <a target="_blank"><img src="{vtranslate('LOGIN_LOGO_IMG_SRC')}" title="LOGIN_LOGO" alt="LOGIN_LOGO"></a>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="content-wrapper">
						<div class="container-fluid">
							<div class="row-fluid">
								<div class="span6">
									<div class="login-area">
										<div class="login-box" id="loginDiv">
											<div class="">
												<h3 class="login-header">Login to {if $HQ == 1}{vtranslate('MOVEHQ_APPTITLE')}{else}{vtranslate('MOVECRM_APPTITLE')}{/if}</h3>
											</div>
											<form class="form-horizontal login-form" style="margin:0;" action="index.php?module=Users&action=Login" method="POST">
												{if isset($smarty.request.error)}
													<div class="alert alert-error">
														<p>Invalid username or password.</p>
													</div>
												{/if}
                                                 {if isset($smarty.request.msg) && $smarty.request.msg eq 'user_limit'}
                                                <div class="alert alert-error">
                                                    <p>You have reached the maximum number of concurrent users. Please contact IGC Support for more information.</p>
                                                </div>
                                                {/if}
												{if isset($smarty.request.fpError)}
													<div class="alert alert-error">
														<p>Invalid Username or Email address.</p>
													</div>
												{/if}
												{if isset($smarty.request.status)}
													<div class="alert alert-success">
														<p>Mail was send to your inbox, please check your e-mail.</p>
													</div>
												{/if}
												{if isset($smarty.request.statusError)}
													<div class="alert alert-error">
														<p>Outgoing mail server was not configured.</p>
													</div>
												{/if}
												<div class="control-group">
													<label class="control-label" for="username"><b>User name</b></label>
													<div class="controls">
														<input type="text" id="username" name="username" placeholder="Username">
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" for="password"><b>Password</b></label>
                                                    <div class="controls">
                                                        <input type="password" id="password" name="password" placeholder="Password">
                                                    </div>
                                                </div>
                                                <div class="control-group signin-button">
                                                    <div class="controls" id="forgotPassword">
                                                        <button type="submit" class="btn btn-primary sbutton">Sign in</button>
                                                        &nbsp;&nbsp;&nbsp;<a>Forgot Password ?</a>
                                                    </div>
                                                </div>
                                                {* Retain this tracker to help us get usage details
                                                <img src='//stats.vtiger.com/stats.php?uid={$APPUNIQUEKEY}&v={$CURRENT_VERSION}&type=U' alt='' title='' border=0 width='1px' height='1px'>*}
                                            </form>
                                            {if getenv('INSTANCE_NAME') neq 'sirva'}
                                                <span>{vtranslate('LBL_LOGIN_PAGE_TERMS', $MODULE)}<a href="https://www.movehq.com/legal">moveHQ.com/legal</a></span>
                                            {/if}
                                            <div class="login-subscript">

                                                <small> {if $HQ == 1}{vtranslate('POWEREDBY_MOVEHQ')}{else}{vtranslate('POWEREDBY_MOVECRM')}{/if}&nbsp;{$CURRENT_VERSION}</small>
                                            </div>
											</div>
											<div class="login-box hide" id="forgotPasswordDiv">
												<form class="form-horizontal login-form" style="margin:0;" action="forgotPassword.php" method="POST">
												<div class="">
													<h3 class="login-header">Forgot Password</h3>
												</div>
												<div class="control-group">
													<label class="control-label" for="user_name"><b>User name</b></label>
													<div class="controls">
														<input type="text" id="user_name" name="user_name" placeholder="Username">
													</div>
												</div>
												<div class="control-group">
													<label class="control-label" for="email"><b>Email</b></label>
													<div class="controls">
														<input type="text" id="emailId" name="emailId"  placeholder="Email">
													</div>
												</div>
												<div class="control-group signin-button">
													<div class="controls" id="backButton">
														<input type="submit" class="btn btn-primary sbutton" value="Submit" name="retrievePassword">
														&nbsp;&nbsp;&nbsp;<a>Back</a>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script>
		jQuery(document).ready(function(){
		jQuery("#forgotPassword a").click(function() {
		jQuery("#loginDiv").hide();
		jQuery("#forgotPasswordDiv").show();
		});
		jQuery("#backButton a").click(function() {
		jQuery("#loginDiv").show();
		jQuery("#forgotPasswordDiv").hide();
		});
		jQuery("input[name='retrievePassword']").click(function (){
			var username = jQuery('#user_name').val();
			var email = jQuery('#emailId').val();
			var email1 = email.replace(/^\s+/,'').replace(/\s+$/,'');
			var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
			var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;
			if(username == ''){
				alert('Please enter valid username');
				return false;
			} else if(!emailFilter.test(email1) || email == ''){
				alert('Please enater valid email address');
				return false;
			} else if(email.match(illegalChars)){
				alert( "The email address contains illegal characters.");
				return false;
			} else {
				return true;
			}
			});
		});
	</script>
</html>
{/strip}
