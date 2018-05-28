let grn = 'hsl(100, 100%, 50%)';
$(".pec").css('fill', grn);
$(".bicep").css('fill', grn);
$(".abdominal").css('fill', grn);


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
	addStatTitle("Stats");
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