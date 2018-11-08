$( document ).ready(function() {
	
	$('h2.fitness-title').on('click', function() {
		if ($('div.fitness-content').css('display') != 'none') {
			$('div.fitness-content').css('display', 'none');
		}
	});
	//console.log("BIGG");

let grn = 'hsl(100, 100%, 50%)';
let ylw = 'hsl(45, 100%, 50%)';
let red = 'hsl(0, 100%, 50%)';

//$(".trapezius").css('fill', red);
//$(".deltoid").css('fill', red);
//$(".pec").css('fill', red);
//$(".tricep").css('fill', red);
//$(".bicep").css('fill', red);
//$(".forearm").css('fill', red)
//$(".abdominal").css('fill', red);
//$(".oblique").css('fill', red);
//$(".calf").css('fill', red);
	
let bodyStatsString = $(".fitness .stats").html();

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
	$(this).css('fill', `hsl(${muscleHue}, 100%, 50%)`);
	let percIdeal = muscleObj['perc_ideal'];
	// If the muscle is ready and far from ideal or if the muscle has been ready for more than a week then apply the flashing class
	if ((percIdeal < 90 && muscleHUR <= 0) || muscleHUR < (-1 * 7 * 24))  {
		$(this).addClass('flashing');
	}
});
/*$("svg path.muscle").(function() {
	let muscleID = $(this).attr("data-muscle-id");
	$(`svg path.muscle[data-muscle-id="${muscleID}"]`).each(function() {
		$(this).removeClass('selected');
	});
	console.log(muscleID);
});
*/
/*
$("svg path.muscle").mouseover(function() {
	$(".fitness .stats").html("");
	let name = $(this).attr("class");
	name = name.match(/\w+/g).pop();
	name = name.substr(0,1).toUpperCase()+name.substr(1);
	addStatTitle(name);
	addStat("Size", "12.25\"");
	addStat("Ideal", "14.75\"");
	addStat("Worked", "33 hrs");
	addStat("Ready", "7 hrs");
	addStat("Priority", "A+");
});
$("svg .muscle").mouseout(function() {
	$(".fitness .stats").html("");
	addStatTitle("Body");
	addStat("Weight", "155 lbs");
	addStat("Ready Muscles", "11");
	addStat("Percent Ideal", "83%");
	addStat("Strength Index", "204");
	addStat("Ideal Muscles", "6 / 13");
});
*/
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