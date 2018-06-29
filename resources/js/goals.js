let startingDebt = 			17000;	// $ AS OF 06/01/2018 (Estimate)
let startingNetWorth = 		10000;	// $ AS OF 06/01/2018 (Estimate)
let startingBodyWeight = 	147.2;	// lbs (NEEDS TO BE UPDATED)
let startingBenchPress = 	160;	// lbs (NEEDS TO BE UPDATED)
let startingMileTime = 		600; 	// seconds (NEEDS TO BE UPDATED)

let percentGoalDebtFree = 	Math.round(((startingDebt - currLiabilities) / startingDebt) * 100);
let percentGoalNetWorth = 	Math.round((((Number(currCash) + Number(currAssets) - Number(currLiabilities)) - startingNetWorth) / (30000 - startingNetWorth)) * 100);
let percentGoalBodyWeight = Math.round(1);
let percentGoalBenchPress = Math.round(1);


function fillInProgress(id, percentOfGoal) {
	let hue = 1.9 * percentOfGoal;
	if (hue > 190) {
		hue = 190;
	}
	$(`#${id} div.fill`).css("width", `${percentOfGoal}%`).css('background', `linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`);
	$(`#${id}`).append(`<h5>${percentOfGoal}%</h5>`);
}

fillInProgress("goal-debt-free", percentGoalDebtFree);
fillInProgress("goal-net-worth", percentGoalNetWorth);
fillInProgress("goal-body-weight", percentGoalBodyWeight);
fillInProgress("goal-bench-press", percentGoalBenchPress);
