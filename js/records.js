$(document).ready(function() {
	setHeight('nav.formify-records-nav');
});

$(window).resize(function() {
	setHeight('nav.formify-records-nav');
});

var setHeight = function(element) {
	var $element = $(element);
	if($element.length) {
		var offset = $element.offset().top;
		var windowHeight = $(window).height();
		$element.height(windowHeight - offset);
	}
}