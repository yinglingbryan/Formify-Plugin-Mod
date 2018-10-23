$(document).ready(function() {
	
	if($.cookie('formifyDashboardViewMode') == 'table') {
		$('#view-mode-table').addClass('btn-primary');
		$('#formify-forms-grid-view').removeClass('on');
		$('#formify-forms-table-view').addClass('on');
	} else {
		$('#view-mode-grid').addClass('btn-primary');
		$('#formify-forms-table-view').removeClass('on');
		$('#formify-forms-grid-view').addClass('on');
	}
	
	$('#formify-form-filter-input').keyup(function() {
		var filterText = $(this).val().toLowerCase();
		$('.form-box-name').each(function() {
			
			var formName = $(this).val().toLowerCase();
			
			if(formName.indexOf(filterText) == -1) {
				$(this).parent().parent().hide();
			} else {
				$(this).parent().parent().show();
			}
		});
	});
	
	$('#view-mode-table').on('click',function(e) {
		$.cookie('formifyDashboardViewMode', 'table');
		$('.view-mode button').removeClass('btn-primary');
		$(this).addClass('btn-primary');
		$('#formify-forms-grid-view').removeClass('on');
		$('#formify-forms-table-view').addClass('on');
	});
	
	$('#view-mode-grid').on('click',function(e) {
		$.cookie('formifyDashboardViewMode', 'grid');
		$('.view-mode button').removeClass('btn-primary');
		$(this).addClass('btn-primary');
		$('#formify-forms-table-view').removeClass('on');
		$('#formify-forms-grid-view').addClass('on');
	});
	
	
	$('#formify-groups-tabs-nav').on('click','a',function(e) {
		e.preventDefault();
		$('li.active').removeClass('active');
		$(this).parent().addClass("active");
		$('.formify-settings-tab').hide();
		$($(this).attr('href')).show();
	});
	
});