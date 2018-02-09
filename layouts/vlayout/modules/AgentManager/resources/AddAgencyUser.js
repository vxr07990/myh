jQuery(document).ready(function() {
	console.dir('entering registerEvents');
	console.dir(jQuery('#submitButton'));
	jQuery('#submitButton').on('click', function() {
		console.dir('clicked!');
		var roleidTD = jQuery('select[name="roleid"]');
		var roleidOption = roleidTD.siblings('.chzn-container').find('.result-selected').attr('id');
		var roleid = roleidTD.find('option:eq('+roleidOption.split('_')[3]+')').val();
		console.dir(roleid);
		
		var dataURL = 'index.php?module=AgentManager&action=SaveAgencyUser&srcRecord='+jQuery("input[name='srcRecord']").val()+"&user="+jQuery("input[name='user']").val()+"&email1="+jQuery("input[name='email1']").val()+"&first_name="+jQuery("input[name='first_name']").val()+"&last_name="+jQuery("input[name='last_name']").val()+"&roleid="+roleid;
		
		AppConnector.request(dataURL).then(
			function(data) {
				if(data.success) {
					console.dir('success');
					window.opener.location.reload();
					window.close();
				}
			},
			function(error) {
				console.dir('Error : '+error);
				window.opener.location.reload();
				window.close();
			}
		);
		
	});
});