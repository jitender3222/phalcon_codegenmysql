$(document).ready(function($){

	$('.number_only').keyup(function(){
		this.value = this.value.replace(/[^0-9\.]/g, '');
	});

	setTimeout(function() {
    	$('.successMessage').fadeOut('fast');
	}, 1000);
});