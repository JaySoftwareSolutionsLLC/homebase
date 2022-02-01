$(document).ready(function() {
	"use strict";
	const MIN_BEAUTIFUL_TEMP = 65; 	// Sets the minimum temp. which may qualify as a beautiful day
	const MAX_BEAUTIFUL_TEMP = 85; 	// Sets the maximum temp. which may qualify as a beautiful day
	const DBL_CLICK_DELAY = 700; 	// Sets the time the click event will wait for a second click, before calling the first.
	
	/* Function: creates div and img elements for the city object argument. Then performs an ajax call to a government website based on the city object's latitude and longitude which pulls weather information about the city. Upon a successful ajax call the image will be changed to display current weather and the current temperature will be displayed as well as the number of beautiful days and nights. Also sets events to trigger the speakCurrentWeather and speakForecastWeather functions. */
	function displayCity(city) {
		$("section.weather .content").prepend( "<div class='city " + city.name + "'>" );
		$("div." + city.name).append("<img src=''>");
		$.ajax({
				url: 'https://forecast.weather.gov/MapClick.php?lat=' + city.lat + '&lon=' + city.lon + '&FcstType=json',
				datatype: 'jsonp',
				success: function(data) {
					if (city.name === "current_location") {
						city.displayName = data.productionCenter;
					}
					else {
						city.displayName = city.name;
					}
					city.temp = data.currentobservation.Temp;
					city.weather = data.data.weather[0];
					city.windChill = data.currentobservation.WindChill;
					city.image = data.data.iconLink[0];
					var secureImg = city.image.replace('http:', 'https:');
					$("div." + city.name + " img").attr("src", secureImg);
					$("div." + city.name).append("<h3>" + city.displayName + "</h3>");
					$("div." + city.name).append("<h4>" + city.temp + "°F</h4>");
					if (getCurrentDate().getHours() >= 4 && getCurrentDate().getHours() <= 16) {
						var firstValueInArrayIsTodaysWeather = true;
						var dataAdjustment = 0;
					} else {
						var firstValueInArrayIsTodaysWeather = false;
						var dataAdjustment = -1;
					}
					city.tomorrowWeather = data.data.weather[2 + dataAdjustment];
					city.tomorrowDayHigh = data.data.temperature[2 + dataAdjustment];
					city.tomorrowDayLow = data.data.temperature[3 + dataAdjustment];
					city.warmestDayTemperature = -500;
					city.warmestDay = "non-existent";
					for (var i = 0; i < 10; i++) {
						if (parseInt(data.data.temperature[i]) > parseInt(city.warmestDayTemperature)) {
							city.warmestDayTemperature = parseInt(data.data.temperature[i]);
							if (data.time.startPeriodName[i] == 'This Afternoon'
								|| data.time.startPeriodName[i] == 'Today') {
								city.warmestDay = 'Today'
							} else {
								city.warmestDay = data.time.startPeriodName[i].substring(0,3);
							}
							if (data.time.startPeriodName[i].includes("Night")) {
								city.warmestDay += ' (PM)';
							}
						}
					}
					city.coldestDayTemperature = 500;
					city.coldestDay = "non-existent";
					for (var i = 0; i < 10; i++) {
						if (parseInt(data.data.temperature[i]) < parseInt(city.coldestDayTemperature)) {
							city.coldestDayTemperature = parseInt(data.data.temperature[i]);
							if (data.time.startPeriodName[i] == 'This Afternoon'
								|| data.time.startPeriodName[i] == 'Today') {
								city.coldestDay = 'Today'
							} else {
								city.coldestDay = data.time.startPeriodName[i].substring(0,3);
							}
							if (data.time.startPeriodName[i].includes("Night")) {
								city.coldestDay += ' (PM)';
							}
						}
					}
					city.beautifulDayTemp = [];
					city.beautifulDays = [];
					city.beautifulDayWeather = [];
					for (var i = 0; i < 10; i++) {
						if (data.data.temperature[i] >= MIN_BEAUTIFUL_TEMP && data.data.temperature[i] <= MAX_BEAUTIFUL_TEMP && (data.data.weather[i] === "Sunny" || data.data.weather[i] === "Mostly Sunny" || data.data.weather[i] === "Mostly Clear" || data.data.weather[i] === "Partly Sunny" || data.data.weather[i] === "Partly Cloudy") && data.time.startPeriodName[i].indexOf("Night") < 0 && data.time.startPeriodName[i].indexOf("night") < 0) {
							city.beautifulDays.push(data.time.startPeriodName[i]);
							city.beautifulDayTemp.push(data.data.temperature[i]);
							city.beautifulDayWeather.push(data.data.weather[i]);
						}
					}
					city.beautifulNightTemp = [];
					city.beautifulNights = [];
					city.beautifulNightWeather = [];
					for (var i = 0; i < 10; i++) {
						if (data.data.temperature[i] >= MIN_BEAUTIFUL_TEMP && data.data.temperature[i] <= MAX_BEAUTIFUL_TEMP && (data.data.weather[i] === "Sunny" || data.data.weather[i] === "Mostly Sunny" || data.data.weather[i] === "Mostly Clear" || data.data.weather[i] === "Partly Sunny" || data.data.weather[i] === "Partly Cloudy") && (data.time.startPeriodName[i].indexOf("Night") >= 0 || data.time.startPeriodName[i].indexOf("night") >= 0)) {
							city.beautifulNights.push(data.time.startPeriodName[i]);
							city.beautifulNightTemp.push(data.data.temperature[i]);
							city.beautifulNightWeather.push(data.data.weather[i]);
						}
					}

					let beautifulDaysLightness = 100 - ((city.beautifulDays.length+city.beautifulNights.length)*7.5);
					if (beautifulDaysLightness < 50) {
						beautifulDaysLightness = 50;
					}
					$("div." + city.name).append(`<h4 style='color: hsl(120, 100%, ${beautifulDaysLightness}%)'>Beautiful: ${city.beautifulDays.length} | ${city.beautifulNights.length}</h4>`);
					
					let warmestDayLightness = 100 - (Math.abs(75-city.warmestDayTemperature)*0.5);
					if (warmestDayLightness < 50) {
						warmestDayLightness = 50;
					}
					$("div." + city.name).append(`<h4 style='color: hsl(0, 100%, ${warmestDayLightness}%)'>${city.warmestDay}: ${city.warmestDayTemperature}</h4>`);
					
					let coldestDayLightness = 100 - (Math.abs(50-city.coldestDayTemperature)*0.5);
					if (coldestDayLightness < 50) {
						coldestDayLightness = 50;
					}
					$("div." + city.name).append(`<h4 style='color: hsl(0, 100%, ${coldestDayLightness}%)'>${city.coldestDay}: ${city.coldestDayTemperature}</h4>`);

//					if (isTouchDevice()) {
//						$("div." + city.name).append("<button>Full Forecast</button>");
//						$("div." + city.name + " button").on("click", function(e) {
//							speakForecastWeather(city);
//							e.stopPropagation();
//						});
//					}
				}
		});
		$("div." + city.name).on("click", function(e) {
			speakCurrentWeather(city);				
		});
		$("div." + city.name).on("dblclick", function(e) {
			speakForecastWeather(city);
		});
	}
	
	/* Function: loops through the myCities array of objects and calls displayCity passing each array object as an argument. */
	function displayCities() {
		for (let city of myCities) {
			if (city.active == 1) {
				displayCity(city);
			}
		}
	}
	
	/* Function: uses geolocation to determine the user's latitude and longitude; creates a new city object and calls displayCity on that new city object. */
	function getLocation() {
		let options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };

        function error() {
            speak("Brett, I am unable to determine your location.");
        }

        function success(pos) {
			let currentCity = new City("current_location", String(pos.coords.latitude), String(pos.coords.longitude));
			displayCity(currentCity);
        }
		navigator.geolocation.getCurrentPosition(success, error, options);
	}
	
	/* Function: calls the speak function from speech.js passing a string about a city's weather as the argument. */
	function speakCurrentWeather(cityObject) {
		window.speechSynthesis.cancel();
		if (cityObject.weather.indexOf("Light") !== -1 || cityObject.weather.indexOf("Scattered") !== -1) {
			speak("It is " + cityObject.temp + " degrees with " + cityObject.weather + " in " + cityObject.displayName + ".");
		} else if (cityObject.weather.indexOf("Chance Showers") !== -1) {
			speak("It is " + cityObject.temp + " degrees with a chance of showers in " + cityObject.displayName + ".");
		} else if (cityObject.weather.indexOf("T-storms") !== -1) {
			speak("It is " + cityObject.temp + " degrees with a chance of thunder storms in " + cityObject.displayName + ".");
		} else {
			speak("It is " + cityObject.temp + " degrees and " + cityObject.weather + " in " + cityObject.displayName + ".");
		}
		if (cityObject.windChill !== 'NA') {
			speak("With the wind chill it feels like it is " + cityObject.windChill + " degrees.");
		}
	}
	
	/* Function: calls the speak function from speech.js multiple times with current and forecasted weather as well as information about beautiful days and nights as found in the cityObject passed as an argument. */
	function speakForecastWeather(cityObject) {
		window.speechSynthesis.cancel();
		if (cityObject.weather.indexOf("Rain") !== -1 || cityObject.weather[0] === "A" || cityObject.weather.indexOf("Light") !== -1) {
			speak("It is currently " + cityObject.temp + " degrees with " + cityObject.weather + " in " + cityObject.displayName);
		} else {
			speak("It is currently " + cityObject.temp + " degrees and " + cityObject.weather + " in " + cityObject.displayName);
		}
//		if (cityObject.currentWindChill !== cityObject.temp) {
//			speak("With the wind, the temperature feels like it is " + cityObject.currentWindChill + " degrees.");
//		}
		if (cityObject.tomorrowWeather.indexOf("showers") !== -1) {
			speak("Tomorrow is forecasted to have a " + cityObject.tomorrowWeather + ", With a high near " + cityObject.tomorrowDayHigh + ", and a low near " + cityObject.tomorrowDayLow);
		} else {
			speak("Tomorrow is forecasted to be " + cityObject.tomorrowWeather + ", With a high near " + cityObject.tomorrowDayHigh + ", and a low near " + cityObject.tomorrowDayLow);
		}
		speak("The warmest day in the five day forecast is " + cityObject.warmestDay + ", with a projected high of " + cityObject.warmestDayTemperature);
		if (cityObject.beautifulDays.length === 0) {
			speak("Unfortunately, there are no beautiful days in the five day forecast.")
		} else if (cityObject.beautifulDays.length === 1) {
			speak("There is one day in the forecast that looks beautiful.")
			speak(cityObject.beautifulDays[0] + " is forecast to be " + cityObject.beautifulDayTemp[0] + " degrees and " + cityObject.beautifulDayWeather[0]);
		} else {
			speak("There are " + cityObject.beautifulDays.length + " days in the forecast that look beautiful.")
			for (var i = 0; i < cityObject.beautifulDays.length; i++) {
				speak(cityObject.beautifulDays[i] + " is forecast to be " + cityObject.beautifulDayTemp[i] + " degrees and " + cityObject.beautifulDayWeather[i]);
			}
		}
		if (cityObject.beautifulNights.length === 0) {
			speak("Unfortunately, there are no beautiful nights in the five day forecast.")
		} else if (cityObject.beautifulNights.length === 1) {
			speak("There is one night in the forecast that looks beautiful.")
			speak(cityObject.beautifulNights[0] + " is forecast to be " + cityObject.beautifulNightTemp[0] + " degrees and " + cityObject.beautifulNightWeather[0]);
		} else {
			speak("There are " + cityObject.beautifulNights.length + " nights in the forecast that look beautiful.")
			for (var i = 0; i < cityObject.beautifulNights.length; i++) {
				speak(cityObject.beautifulNights[i] + " is forecast to be " + cityObject.beautifulNightTemp[i] + " degrees and " + cityObject.beautifulNightWeather[i]);
			}
		}
	}
	
	function isTouchDevice() {
		var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
		var mq = function(query) {
			return window.matchMedia(query).matches;
		};
		if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
			return true;
		}
		var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
		return mq(query);
	}
	
	getLocation();
	displayCities();
});