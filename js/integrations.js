$(document).ready(function() {

	$('#formify-integration-tabs-nav a').click(function(e) {
		e.preventDefault();
		$('li.active').removeClass('active');
		$(this).parent().addClass('active');
		$('.formify-integrations-tab').hide();
		$($(this).attr('href')).show();
	});
	
	$('#formify-integration-tabs-nav ul li:first-of-type a').click();
	
});