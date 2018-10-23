$(document).ready(function() {
	
	$('#fID').on('change',function() {
		if($(this).val() != '') {
			$('#import-submit').stop().fadeIn('fast');
			if($(this).val() == '0') {
				$('#form-name-container').show();
			} else {
				$('#form-name-container').hide();
			}
		} else {
			$('#import-submit').stop().fadeOut('fast');
		}
	});
	
	$('#import-form').on('submit',function() {
		if(($('#fID').val() == '0') && ($('#form-name').val() == '')) {
			return false;
		}
	});
	
	$('.ffID').on('change',function() {
		if($(this).val() == 0) {
			$('.label-' + $(this).attr('data-row')).val($(this).attr('data-label')).prop('disabled',false);
		} else {
			$('.label-' + $(this).attr('data-row')).val('').prop('disabled',true);
		}
	});
	
})