function returnFirstDayNextMonthStr(thisMonthStr) {
				let dateStartArr = thisMonthStr.match(/\d+/g);
				let newDateStartArr = [];
				if (dateStartArr[1] == '12') {
					newDateStartArr[0] = String(Number(dateStartArr[0]) + 1);
					newDateStartArr[1] = '1';
				}
				else {
					newDateStartArr[0] = dateStartArr[0];
					newDateStartArr[1] = String(Number(dateStartArr[1]) + 1);
				}
				newDateStartArr[1] = (newDateStartArr[1].length == 1) ? '0' + newDateStartArr[1] : newDateStartArr[1];
				newDateStartArr[2] = '01';
				return newDateStartArr.join("-");
			}
function returnLastDayNextMonthStr(thisMonthStr) {
				let dateEndArr = thisMonthStr.match(/\d+/g);
				let newDateEndArr = [];
				if (dateEndArr[1] == '12') {
					newDateEndArr[0] = String(Number(dateEndArr[0]) + 1);
					newDateEndArr[1] = '01';
				}
				else {
					newDateEndArr[0] = dateEndArr[0];
					newDateEndArr[1] = String(Number(dateEndArr[1]) + 1);
				}
				newDateEndArr[1] = (newDateEndArr[1].length == 1) ? '0' + newDateEndArr[1] : newDateEndArr[1];
				if (newDateEndArr[1] == '01' || newDateEndArr[1] == '03' || newDateEndArr[1] == '05' || newDateEndArr[1] == '07' || newDateEndArr[1] == '08' || newDateEndArr[1] == '10' || newDateEndArr[1] == '12') {
					newDateEndArr[2] = '31';
				}
				else if (newDateEndArr[1] == '02') {
					if ( ( ( newDateEndArr[0] - 2016 ) % 4 ) === 0 ) {
						newDateEndArr[2] = '29';
					}
					else {
						newDateEndArr[2] = '28';
					}
				}
				else {
					newDateEndArr[2] = '30';
				}
				
				return newDateEndArr.join("-");
			}
function returnFirstDayPreviousMonthStr(thisMonthStr) {
				let dateStartArr = thisMonthStr.match(/\d+/g);
				let newDateStartArr = [];
				if (dateStartArr[1] == '01') {
					newDateStartArr[0] = String(Number(dateStartArr[0]) - 1);
					newDateStartArr[1] = '12';
				}
				else {
					newDateStartArr[0] = dateStartArr[0];
					newDateStartArr[1] = String(Number(dateStartArr[1]) - 1);
				}
				newDateStartArr[1] = (newDateStartArr[1].length == 1) ? '0' + newDateStartArr[1] : newDateStartArr[1];
				newDateStartArr[2] = '01';
				return newDateStartArr.join("-");
			}
function returnLastDayPreviousMonthStr(thisMonthStr) {
				let dateEndArr = thisMonthStr.match(/\d+/g);
				let newDateEndArr = [];
				if (dateEndArr[1] == '01') {
					newDateEndArr[0] = String(Number(dateEndArr[0]) - 1);
					newDateEndArr[1] = '12';
				}
				else {
					newDateEndArr[0] = dateEndArr[0];
					newDateEndArr[1] = String(Number(dateEndArr[1]) - 1);
				}
				newDateEndArr[1] = (newDateEndArr[1].length == 1) ? '0' + newDateEndArr[1] : newDateEndArr[1];
				if (newDateEndArr[1] == '01' || newDateEndArr[1] == '03' || newDateEndArr[1] == '05' || newDateEndArr[1] == '07' || newDateEndArr[1] == '08' || newDateEndArr[1] == '10' || newDateEndArr[1] == '12') {
					newDateEndArr[2] = '31';
				}
				else if (newDateEndArr[1] == '02') {
					if ( ( ( newDateEndArr[0] - 2016 ) % 4 ) === 0 ) {
						newDateEndArr[2] = '29';
					}
					else {
						newDateEndArr[2] = '28';
					}
				}
				else {
					newDateEndArr[2] = '30';
				}
				
				return newDateEndArr.join("-");
			}