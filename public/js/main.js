$(document).ready(function() {
	$('#username, #password').keyup(function() {
	    if ($(this).val()) {
	    	$(this).attr('class', 'hasText');
	    } else {
	    	$(this).removeAttr('class', 'hasText'); 
	    }
	});
    
});

