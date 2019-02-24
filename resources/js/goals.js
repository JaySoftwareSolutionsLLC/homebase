$(document).ready(function() {
	$('.progress .fill').each(function() {
		let value = $(this).attr('data-value');
		//console.log(`VALUE: ${value}`);
		let hue = value * 1.9;
		//console.log(`HUE: ${hue}`);
		$(this).css('background', `linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`);
		//console.log(`linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`)
	});
	$('div.goal i.fa-info').on('click', function( event ) {
		event.stopPropagation();
		let info = $(this).attr('data-goal-description');
		$('div#modal').html(info).css('display', 'inline-flex');
		//console.log( info );
	});
	$('body').on('click', function() {
		console.log('trigger')
		$('div#modal').css('display', 'none');
	});
});
