let grn = 'hsl(100, 100%, 50%)';
let ylw = 'hsl(45, 100%, 50%)';
$(".trapezius").css('fill', grn);
$(".shoulder").css('fill', grn);
$(".pec").css('fill', grn);
$(".bicep").css('fill', grn);
//$(".abdominal").css('fill', ylw);
$(".trapezius").css('fill', grn);
$(".calf").css('fill', grn);
$(".delt").css('fill', grn);
$(".tricep").css('fill', grn);


$("svg .muscle").mouseover(function() {
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

function addStat(name,value) {
	$(".fitness .stats").append("<div class='stat'><h4 class='name'>" + name + "</h4><h4 class='value'>" + value + "</h4></div>");
}
function addStatTitle(value) {
	$(".fitness .stats").append(`<h3 class='title'>${value}</h3>`);
}

function growthRequired(current, ideal) {
	return Math.round(((ideal - current) / ideal) * 10000) / 100;
}