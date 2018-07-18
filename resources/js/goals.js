function fillInProgress(id, percentOfGoal) {
	let hue = 1.9 * percentOfGoal;
	if (hue > 190) {
		hue = 190;
	}
	$(`#${id} div.fill`).css("width", `${percentOfGoal}%`).css('background', `linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`);
	$(`#${id}`).append(`<h5>${percentOfGoal}%</h5>`);
}

$(document).ready(function() {
	$('.progress .fill').each(function() {
		let width = $(this).css('width');
		let hue = (width.match(/[\d]*/)) * 1.9;
		$(this).css('background', `linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`);
		console.log(`linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`)
	});
});
