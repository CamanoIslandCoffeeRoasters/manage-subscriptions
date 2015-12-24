jQuery(document).ready(function($) {
   // Create Array to hold number and id of buttons
   var divs = new Array();
   $('div.call-flow').each(function (index) {
    divs.push($(this).attr("id"));
   });
   // Show/Hide content divs
   $('.call-flow').live("click", function() {
      clicked_div = ($(this).attr("id"));
      $.each(divs, function(i, val){
          if (val != clicked_div) {
              $('#' + val + '_content').hide();
              $('#' + clicked_div + '_content').show();
          }
      });
  });
});
