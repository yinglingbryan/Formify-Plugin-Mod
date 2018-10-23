$(document).ready(function() {

	$('#formify-field-settings-tabs-nav a').click(function(e) {
		e.preventDefault();
		$('li.active').removeClass('active');
		$(this).parent().addClass("active");
		$('.formify-field-settings-tab').hide();
		$($(this).attr('href')).show();
	});
	
});