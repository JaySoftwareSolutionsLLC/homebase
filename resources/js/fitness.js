$( document ).ready(function() {

let bodyStatsString = $(".fitness .stats").html();

// On single click, pull up all of the info pertaining to muscle
$("svg path.muscle").click(function() {
	$("svg path.muscle").removeClass('selected');
	let muscleID = $(this).attr("data-muscle-id");
	let relMuscle = muscleObjects.filter(obj => {
  		return obj.id === muscleID;
	});
	let muscleObj = relMuscle[0];
	//TEST console.log(muscleID);
	//TEST console.log(muscleObj);
	$(`svg path.muscle[data-muscle-id="${muscleID}"]`).each(function() {
		$(this).addClass('selected');
	});
	let muscleName = muscleObj['name'];
	let asocCirc = muscleObj['circ'];
	let muscleCurrCirc = muscleObj['curr_circ'];
	let muscleIdealCirc = muscleObj['ideal_circ'];
	let muscleMRF = muscleObj['mrf'];
	let muscleHUR = muscleObj['hur'];
	let percIdeal = muscleObj['perc_ideal'];
	
	$(".fitness .stats").html("");
	addStatTitle(`${muscleName} (#${muscleID})`);
	addStat("Size", `${muscleCurrCirc}"`);
	addStat("Worked", `${muscleMRF} hrs`);
	addStat("Ideal", `${muscleIdealCirc}"`);
	addStat("Ready", `${muscleHUR} hrs`);
	addStat("% Ideal", `${percIdeal} %`);
});
// On double click, log a freestyle lift for given exercise for now
$("svg path.muscle").dblclick(function() {
	let muscleID = $(this).attr("data-muscle-id");
	let confirmed = confirm(`Are you sure you want to log a lift for muscle ${muscleID}`)
	if (confirmed) {
		let exerciseID = parseInt(muscleID) + 94; // All generic lifts have been created with an exercise id 94 greater than their muscle id
		$.ajax({
			type: "POST", // POST, GET, etc.
			url: "/homebase/resources/ajax/insert_fitness_lift.php",
			data: {
				exercise_id : exerciseID,
				workout_structure_id : 4
			},
			// contentType: "application/json",
			dataType: "JSON",
			success: function (response) {
				console.log(JSON.stringify(response));
				if (response.success) {
					window.location.reload(true); 
				}
			}
		});
	}
});

	
$("svg .outline").click(function() {
	$("svg path.muscle").removeClass('selected');
	$(".fitness .stats").html(bodyStatsString);
});
	
	// Change display of muscle based on Hours Until Recovered
	// Currently not ideal because each function will loop over each instance of a muscle and do the same thing each time (inefficient)
$("svg path.muscle").each(function() {
	let muscleID = $(this).attr("data-muscle-id");
	let relMuscle = muscleObjects.filter(obj => {
  		return obj.id === muscleID;
	});
	let muscleObj = relMuscle[0];
	let muscleHUR = muscleObj['hur'];
	let muscleHue = muscleHUR * (120 / muscleObj['ideal_rest']);
	if (muscleHue < 0) {
		muscleHue = 0;
	}
	else if (muscleHue > 120) {
		muscleHue = 120;
	}
	if (year == 2018) {
		$(this).css('fill', `hsla(190, 100%, 50%, 0.1)`);
	}
	else {
		$(this).css('fill', `hsl(${muscleHue}, 100%, 50%)`);
		let percIdeal = muscleObj['perc_ideal'];
		// If the muscle is ready and far from ideal or if the muscle has been ready for more than a week then apply the flashing class
		if ((percIdeal < 90 && muscleHUR <= 0) || muscleHUR < (-1 * 7 * 24))  {
			$(this).addClass('flashing');
		}
	}
	
});
function addStat(name,value) {
	$(".fitness .stats").append("<div class='stat'><h4 class='name'>" + name + "</h4><h4 class='value'>" + value + "</h4></div>");
}
function addStatTitle(value) {
	$(".fitness .stats").append(`<h3 class='title'>${value}</h3>`);
}

function growthRequired(current, ideal) {
	return Math.round(((ideal - current) / ideal) * 10000) / 100;
}

});