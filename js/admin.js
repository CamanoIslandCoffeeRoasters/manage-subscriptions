
	jQuery(document).ready(function($) {
		$('.call-flow').live("click", function() {
			clicked_div = ($(this).attr("id"));
			var divs = ["customer", "script", "order", "survey"];
			jQuery.each(divs, function(i, val){
				//console.log(this).attr("id"));
				if (val != clicked_div) {
				$('#' + val + '_content').hide();
				$('#' + clicked_div + '_content').show();
				}
			});
		});

	});