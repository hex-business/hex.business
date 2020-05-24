$(document).ready(function() {
	$('.menu-icon').on('click', function(e){
		$('.menu').show();
	});
	$('.close-button').on('click', function(e){
		$('.menu').hide();
	});
	currentSlide = 0;
	$( '.control-prev' ).click(function() {
		 ;
		$( "" ).toggle( "slide" );
	});
	$( '.control-next' ).click(function() {
		;
		$( "" ).toggle( "slide" );
	});

})
