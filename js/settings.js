$(document).ready(function() {

	$('#formify-settings-tabs-nav a').on('click',function(e) {
		e.preventDefault();
		$('li.active').removeClass('active');
		$(this).parent().addClass("active");
		$('.formify-settings-tab').hide();
		$($(this).attr('href')).show();
	});
	
	setInterval(function(){
	    $('#formify-cID-hidden').val($('input[name="formify-cID"]').val());
	},100);
	
});