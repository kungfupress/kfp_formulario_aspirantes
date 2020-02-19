
jQuery( document ).ready( function($) {
	$("#ci").keyup(function(){
		$("#ci").validate_ci(); // adds 'valid' or 'invalid' classes to the input on keyup
	});
	$("#form_aspirante").on("submit", function(e) {
		e.preventDefault();
		var $ci = $("#ci");
		if ($ci.hasClass("valid")) {
			this.submit(); 
		}
	});
});
(function ($) {
	$.fn.validate_ci = function() {
	  function validation_digit(ci){
		var a = 0; var i = 0;
		if(ci.length <= 6){
		  for(i = ci.length; i < 7; i++){
			ci = '0' + ci;
		  }
		}
		for(i = 0; i < 7; i++){
		  a += (parseInt("2987634"[i]) * parseInt(ci[i])) % 10;
		}
		if(a%10 === 0){
		  return 0;
		}else{
		  return 10 - a % 10;
		}
	  }
  
	  var ci = this.val().replace(/\D/g, '');
	  var dig = ci[ci.length - 1];
	  ci = ci.replace(/[0-9]$/, '');
	  if( dig == validation_digit(ci) ){
		this.removeClass("invalid");
		this.addClass("valid");
	  } else {
		this.removeClass("valid");
		this.addClass("invalid");
	  }
	};
}( jQuery ));
