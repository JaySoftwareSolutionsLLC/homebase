$(document).ready(function() {
	"use strict";
	const MIN_BEAUTIFUL_TEMP = 60; 	// Sets the minimum temp. which may qualify as a beautiful day
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
					$("div." + city.name + " img").attr("src", city.image);
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
						if (data.data.temperature[i] > city.warmestDayTemperature) {
							city.warmestDayTemperature = data.data.temperature[i];
							city.warmestDay = data.time.startPeriodName[i];
						}
					}
					city.niceDayTemp = [];
					city.niceDays = [];
					city.niceDayWeather = [];
					for (var i = 0; i < 10; i++) {
						if (data.data.temperature[i] >= MIN_BEAUTIFUL_TEMP && data.data.temperature[i] <= MAX_BEAUTIFUL_TEMP && (data.data.weather[i] === "Sunny" || data.data.weather[i] === "Mostly Sunny" || data.data.weather[i] === "Mostly Clear" || data.data.weather[i] === "Partly Sunny" || data.data.weather[i] === "Partly Cloudy") && data.time.startPeriodName[i].indexOf("Night") < 0 && data.time.startPeriodName[i].indexOf("night") < 0) {
							city.niceDays.push(data.time.startPeriodName[i]);
							city.niceDayTemp.push(data.data.temperature[i]);
							city.niceDayWeather.push(data.data.weather[i]);
						}
					}
					city.niceNightTemp = [];
					city.niceNights = [];
					city.niceNightWeather = [];
					for (var i = 0; i < 10; i++) {
						if (data.data.temperature[i] >= MIN_BEAUTIFUL_TEMP && data.data.temperature[i] <= MAX_BEAUTIFUL_TEMP && (data.data.weather[i] === "Sunny" || data.data.weather[i] === "Mostly Sunny" || data.data.weather[i] === "Mostly Clear" || data.data.weather[i] === "Partly Sunny" || data.data.weather[i] === "Partly Cloudy") && (data.time.startPeriodName[i].indexOf("Night") >= 0 || data.time.startPeriodName[i].indexOf("night") >= 0)) {
							city.niceNights.push(data.time.startPeriodName[i]);
							city.niceNightTemp.push(data.data.temperature[i]);
							city.niceNightWeather.push(data.data.weather[i]);
						}
					}
					$("div." + city.name).append("<h4>Days: " + city.niceDays.length + "</h4>");
					$("div." + city.name).append("<h4>Nights: " + city.niceNights.length + "</h4>");
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
			if (e.shiftKey) {
				speakForecastWeather(city);
			}
			else {
				speakCurrentWeather(city);				
			}
		});
	}
	
	/* Function: loops through the myCities array of objects and calls displayCity passing each array object as an argument. */
	function displayCities() {
		for (let city of myCities) {
			displayCity(city);
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
		if (cityObject.niceDays.length === 0) {
			speak("Unfortunately, there are no beautiful days in the five day forecast.")
		} else if (cityObject.niceDays.length === 1) {
			speak("There is one day in the forecast that looks beautiful.")
			speak(cityObject.niceDays[0] + " is forecast to be " + cityObject.niceDayTemp[0] + " degrees and " + cityObject.niceDayWeather[0]);
		} else {
			speak("There are " + cityObject.niceDays.length + " days in the forecast that look beautiful.")
			for (var i = 0; i < cityObject.niceDays.length; i++) {
				speak(cityObject.niceDays[i] + " is forecast to be " + cityObject.niceDayTemp[i] + " degrees and " + cityObject.niceDayWeather[i]);
			}
		}
		if (cityObject.niceNights.length === 0) {
			speak("Unfortunately, there are no beautiful nights in the five day forecast.")
		} else if (cityObject.niceNights.length === 1) {
			speak("There is one night in the forecast that looks beautiful.")
			speak(cityObject.niceNights[0] + " is forecast to be " + cityObject.niceNightTemp[0] + " degrees and " + cityObject.niceNightWeather[0]);
		} else {
			speak("There are " + cityObject.niceNights.length + " nights in the forecast that look beautiful.")
			for (var i = 0; i < cityObject.niceNights.length; i++) {
				speak(cityObject.niceNights[i] + " is forecast to be " + cityObject.niceNightTemp[i] + " degrees and " + cityObject.niceNightWeather[i]);
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