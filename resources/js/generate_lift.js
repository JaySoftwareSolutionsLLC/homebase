// When the reroll button is clicked on an exercise, a new exercise should be generated which has the associated muscle as a primary muscle; is not the same as the current exercise; and is within workout structure and/or equipment restrictions once they are integrated. The new exercise and associated targets will then replace the current text on the screen.
function setEventForAllRerollIcons() {
	$('i.reroll').on('click', function() {
		let workoutStructure = $('h1').attr('data-workout-structure-id');
		let thisRow = $(this);
		let muscleID = $(this).attr('data-muscle-id');
		let exerciseID = $(this).attr('data-exercise-id');
		let muscleIdealness = $(this).attr('data-muscle-idealness');
		$.ajax(	{
			url: '/homebase/resources/forms/form-resources/reroll_exercise.php',
			method: 'POST',
			data: {
				'muscle-id' : muscleID,
				'exercise-id' : exerciseID,
				'muscle-idealness' : muscleIdealness,
				'workout-structure' : workoutStructure
			},
			success: function(data) {
				console.log(data);
				thisRow.parent('li').html(data);
				setEventForAllRerollIcons();
			}
		});
	});
}
setEventForAllRerollIcons();


