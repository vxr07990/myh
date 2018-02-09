jQuery(document).ready(function() {
	jQuery('#EditView').submit(function(e) {
		
		e.preventDefault();

		var form = jQuery(this);
		
		var postData = new Object();
		
		var fields = jQuery('input').each(function() {
			postData[jQuery(this).attr('name')] = jQuery(this).val();
		});
		
		console.dir(postData);
		
		var poster = jQuery.post(dataURL, postData);
		
		var dataURL = 'index.php?module=AgentManager&action=SaveCoordinators&srcRecord='+jQuery("input[name='record']").val();
		
		poster.done(function(data) {
			console.dir(data);
		});
	});
	
	jQuery('.addCoordinator').on('click', function(){
		var newRow = jQuery('.defaultCoordinator').clone();
		var coordinatorTotal = parseInt(jQuery('#numCoordinators').val());
		coordinatorTotal++;
		jQuery('#numCoordinators').val(coordinatorTotal);
		newRow.removeClass('hide defaultCoordinator').addClass('coordinatorRow'+coordinatorTotal);
		newRow.find('input[name="sales_person"]').attr('name', 'sales_person'+coordinatorTotal);
		newRow.find('input[name="coordinators"]').attr('name', 'coordinators'+coordinatorTotal);
		newRow.find('input[name="coordinatorId"]').attr('name', 'coordinatorId'+coordinatorTotal);
		newRow.find('input[name="coordinatorDeleted"]').attr('name', 'coordinatorId'+coordinatorTotal);
		newRow = newRow.appendTo(jQuery(this).closest('table'));
		newRow.find('select').addClass('select2').select2();
	});
	
	jQuery('.deleteCoordiantorButton').on('click', function(e) {
			var currentRow = jQuery(this).closest('tr');
			currentRow.find('input[name^="coordinatorDeleted"]').val('DELETE');
			currentRow.addClass('hide');
	});
	
});