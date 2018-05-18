$(document).ready(function() {
	"use strict";
    /* Function that uses an ajax call to a government website to pull weather data based on the user's current location and then uses the speak function to tell the user about that data it has retrieved.*/
    function tellMeAboutLocalWeather() {
        let options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };

        function error() {
            speak("Location services are unavailable, or this browser does not support them.")
        };

        function success(pos) {
            let currentLatitude = pos.coords.latitude;
            let currentLongitude = pos.coords.longitude;
            $.ajax({
            	url: 'https://forecast.weather.gov/MapClick.php?lat=' + currentLatitude + '&lon=' + currentLongitude + '&FcstType=json',
                datatype: 'jsonp',
                success: function(data) {
                    let currentCity = data.productionCenter;
                    let currentTemp = data.currentobservation.Temp;
                    let currentWeather = data.currentobservation.Weather;
                    let currentWindChill = data.currentobservation.WindChill;
                    if (currentWindChill === 'NA') {
                        currentWindChill = currentTemp;
                    }
                    if (getCurrentDate().getHours() >= 4 && getCurrentDate().getHours() <= 16) {
                        var firstValueInArrayIsTodaysWeather = true;
                        var dataAdjustment = 0;
                    } else {
                        var firstValueInArrayIsTodaysWeather = false;
                        var dataAdjustment = -1;
                    }
                    let tomorrowWeather = data.data.weather[2 + dataAdjustment];
                    let tomorrowDayHigh = data.data.temperature[2 + dataAdjustment];
                    let tomorrowDayLow = data.data.temperature[3 + dataAdjustment];
                    let warmestDayTemperature = -500;
                    let warmestDay = "non-existent";
                    for (var i = 0; i < 10; i++) {
                        if (data.data.temperature[i] > warmestDayTemperature) {
                            warmestDayTemperature = data.data.temperature[i];
                            warmestDay = data.time.startPeriodName[i];
                        }
                    }
                    let niceDayTemp = [];
                    let niceDays = [];
                    let niceDayWeather = [];
                    var minBeautifulTemp = 65;
                    for (var i = 0; i < 10; i++) {
                        if (data.data.temperature[i] >= minBeautifulTemp && (data.data.weather[i] === "Sunny" || data.data.weather[i] === "Mostly Sunny" || data.data.weather[i] === "Mostly Clear" || data.data.weather[i] === "Partly Sunny" || data.data.weather[i] === "Partly Cloudy") && data.time.startPeriodName[i].indexOf("Night") < 0 && data.time.startPeriodName[i].indexOf("night") < 0) {
                            niceDays.push(data.time.startPeriodName[i]);
                            niceDayTemp.push(data.data.temperature[i]);
                            niceDayWeather.push(data.data.weather[i]);
                        }
                    }
                    if (currentWeather.indexOf("Rain") !== -1 || currentWeather[0] === "A" || currentWeather.indexOf("Light") !== -1) {
                        speak("It is currently " + currentTemp + " degrees with " + currentWeather + " in " + currentCity);
                    } else {
                        speak("It is currently " + currentTemp + " degrees and " + currentWeather + " in " + currentCity);
                    }
                    if (currentWindChill !== currentTemp) {
                        speak("With the wind, the temperature feels like it is " + currentWindChill + " degrees.");
                    }
                    if (tomorrowWeather.indexOf("showers") !== -1) {
                        speak("Tomorrow is forecasted to have a " + tomorrowWeather + ", With a high near " + tomorrowDayHigh + ", and a low near " + tomorrowDayLow);
                    } else {
                        speak("Tomorrow is forecasted to be " + tomorrowWeather + ", With a high near " + tomorrowDayHigh + ", and a low near " + tomorrowDayLow);
                    }
                    speak("The warmest day in the five day forecast is " + warmestDay + ", with a projected high of " + warmestDayTemperature);
                    if (niceDays.length === 0) {
                        speak("Unfortunately, there are no beautiful days in the five day forecast.")
                    } else if (niceDays.length === 1) {
                        speak("There is one day in the forecast that looks beautiful.")
                        speak(niceDays[0] + " is forecast to be " + niceDayTemp[0] + " degrees and " + niceDayWeather[0]);
                    } else {
                        speak("There are " + niceDays.length + " days in the forecast that look beautiful.")
                        for (var i = 0; i < niceDays.length; i++) {
                            speak(niceDays[i] + " is forecast to be " + niceDayTemp[i] + " degrees and " + niceDayWeather[i]);
                        }
                    }
                }
            });
        };
        navigator.geolocation.getCurrentPosition(success, error, options);
    }

    /* Function that has latitude and longitude parameters and uses those to pull info
    on current weather in that area and then uses speak function*/
    function tellMeAboutWeatherIn(lat, lon) {
        $.ajax({
            url: 'http://forecast.weather.gov/MapClick.php?lat=' + lat + '&lon=' + lon + '&FcstType=json',
            datatype: 'jsonp',
            success: function(data) {
                let city = data.productionCenter;
                let temp = data.currentobservation.Temp;
                let weather = data.currentobservation.Weather;
                let windChill = data.currentobservation.WindChill;
                if (city.indexOf("Phoenix") !== -1) {
                    city = "Phoenix, Arizona";
                }
                if (weather[0] === "A" || weather.indexOf("Light") !== -1) {
                    speak("It is " + temp + " degrees with " + weather + " in " + city + ".");
                } else if (weather.indexOf("Chance Showers") !== -1) {
                    speak("It is " + temp + " degrees with a chance of showers in " + city + ".");
                } else if (weather.indexOf("Chance T-storms") !== -1) {
                    speak("It is " + temp + " degrees with a chance of thunder storms in " + city + ".");
                } else {
                    speak("It is " + temp + " degrees and " + weather + " in " + city + ".");
                }
                if (windChill !== 'NA') {
                    speak("With the wind chill it feels like it is " + windChill + " degrees.");
                }
            }
        });
    }

    /* Uses tellMeAboutWeatherIn function for different cities */
    var coordsOfSanDiego = [32.7153, -117.1573];
    var coordsOfAlbany = [42.7481, -73.8023];
    var coordsOfKula = [19.5189, -154.8342];
    var coordsOfPhoenix = [33.4484, -112.0740];

    function tellMeAboutSanDiegoWeather() {
        tellMeAboutWeatherIn(coordsOfSanDiego[0], coordsOfSanDiego[1]);
    }

    function tellMeAboutAlbanyWeather() {
        tellMeAboutWeatherIn(coordsOfAlbany[0], coordsOfAlbany[1]);
    }

    function tellMeAboutKulaWeather() {
        tellMeAboutWeatherIn(coordsOfKula[0], coordsOfKula[1]);
    }

    function tellMeAboutPhoenixWeather() {
        tellMeAboutWeatherIn(coordsOfPhoenix[0], coordsOfPhoenix[1]);
    }

    function generateWeatherImages() {
        pullWeatherImage(coordsOfAlbany[0], coordsOfAlbany[1], "albany-weather-button");
        pullWeatherImage(coordsOfSanDiego[0], coordsOfSanDiego[1], "san-diego-weather-button");
        pullWeatherImage(coordsOfKula[0], coordsOfKula[1], "kula-weather-button");
        pullWeatherImage(coordsOfPhoenix[0], coordsOfPhoenix[1], "phoenix-weather-button");

        function success(pos) {
            let currentLatitude = pos.coords.latitude;
            let currentLongitude = pos.coords.longitude;
            pullWeatherImage(currentLatitude, currentLongitude, "my-weather-button");
        };

        function error() {}
        navigator.geolocation.getCurrentPosition(success, error);
    }

    function pullWeatherImage(lat, lon, imgID) {
        $.ajax({
            url: 'http://forecast.weather.gov/MapClick.php?lat=' + lat + '&lon=' + lon + '&FcstType=json',
            datatype: 'jsonp',
            success: function(data) {
                if (data.currentobservation.Weatherimage == "NULL") {
                    var image = data.data.iconLink[0];
                } else {
                    var image = "http://forecast.weather.gov/newimages/large/" + data.currentobservation.Weatherimage;
                }
                document.getElementById(imgID).setAttribute("src", image);
            }
        });
    }
    generateWeatherImages();
    let weatherIntervalID = setInterval(function() {
        generateWeatherImages();
    }, (1000 * 60 * 10));

    document.getElementById("albany-weather-button").onclick = function() {
        tellMeAboutAlbanyWeather();
    }
    document.getElementById("san-diego-weather-button").onclick = function() {
        tellMeAboutSanDiegoWeather();
    }
    document.getElementById("kula-weather-button").onclick = function() {
        tellMeAboutKulaWeather();
    }
    document.getElementById("phoenix-weather-button").onclick = function() {
        tellMeAboutPhoenixWeather();
    }
    document.getElementById("my-weather-button").onclick = function() {
        tellMeAboutLocalWeather();
    }
});