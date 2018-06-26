let currNetWorth = Number(currCash) + Number(currAssets) - Number(currLiabilities);


let preJuneRicksIncome = 12914;
let workWeeksJuneToJan = 30;
let sealAndDesignWeekly = 865;
let expectedPostJuneSDIncome = sealAndDesignWeekly * workWeeksJuneToJan;
let avgRicksWeekly = 765; // Adjust this to be based on actual work days once enough data is in the system and am confident in working M,R,S(dbl)
let expectedPostJuneRicksIncome = avgRicksWeekly * workWeeksJuneToJan * 0.7; // Conservative estimate of Ricks expected earnings
let expected2018Income = preJuneRicksIncome + expectedPostJuneRicksIncome + expectedPostJuneSDIncome;

let adi = (netInc / activeDays).toFixed(2);
let adiTrgt = 205;
let ade = (netExp / activeDays).toFixed(2);
let adeTrgt = 41;
let awh = (7 * netHrs / activeDays).toFixed(2);
let ahw = (netInc / netHrs).toFixed(2);

let percentTax = 0.25; // Ideally this would be calculated based on the actual expected income and income tax brackets (basically a function which does what smartasset tax calculator does)

let EOYProjection = Math.floor(currNetWorth + ((adi - ade) * (1 - percentTax)) * (214 - activeDays)); 

console.log(`netInc: ${netInc} | netExp: ${netExp} | activeDays: ${activeDays} | netHrs: ${netHrs} | `);

$(".finance .adi").prepend(`<h4>+$${adi}/day</h4>`);
$(".finance .ade").prepend(`<h4>-$${ade}/day</h4>`);
$(".finance .awh").prepend(`<h4>${awh} hrs</h4>`);
$(".finance .ahw").prepend(`<h4>$${ahw}/hr</h4>`);

$(".finance .net-worth").prepend("<h4>$" + currNetWorth + "</h4>");
$(".finance .net-worth").append("<h5>" + netWorthDate + "</h5>");

$(".finance .income-projection-2018").append("<h4>$" + expected2018Income + "</h4>");

$(".finance .proj-net-worth").append("<h4>$" + EOYProjection + "</h4>");

new Chart(document.getElementById("account-allocation-graph"),{
	"type":"doughnut",
	"data": {
		"labels":["Cash","Assets","Liabilities"],
		"datasets":[
			{"label":"Asset Allocation",
			 "data":[currCash, currAssets, currLiabilities],
			 "backgroundColor":["hsl(120, 100%, 50%)", "hsl(200, 100%, 50%)", "hsl(0, 100%, 50%)"],
			 "borderColor":["black", "black", "black"],
			 "borderWidth":[1,1,1]
			}
		]
	},
	options: {
		legend: {
			labels: {
				fontColor: 'white',
				boxWidth: 15,
				fontFamily: "'Orbitron', sans-serif"
			}
		}
	}
});