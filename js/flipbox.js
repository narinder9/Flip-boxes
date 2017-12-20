jQuery(document).ready(function($){
	$('.flip').each(function (){
	var effect=$(this).data('effect');
	$(this).flip({
	axis:effect,
	trigger: 'hover'
		});
		
	});
	
	$('.flip').each(function(){ 
	$(this).on('touchstart', function(){
	$(this).flip('toggle'); 
	}); 
	});
	
});
