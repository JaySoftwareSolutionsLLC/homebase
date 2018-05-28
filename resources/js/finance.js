$("#weekly-income").append("<h4>$" + thisWeekRicks + "</h4>")

let currNetWorth = Number(currCash) + Number(currAssets) - Number(currLiabilities);

$("#actual-net-worth").append("<h4>$" + currNetWorth + "</h4>")