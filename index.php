<?php

?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="INSERT">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <!-- TITLE -->
    <title>Home Base 3.0</title>
    <!-- LINK TO A RESET CSS -->
    <link rel="stylesheet" type="text/css" href="resources/css/reset.css">
    <!-- LINK TO GOOGLE FONTS -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Mogra" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
    <!-- LINK TO MAIN STYLING SHEET -->
    <link rel="stylesheet" type="text/css" href="resources/css/main.css">
</head>

<body>

    <header>
        <div class="left">
            <h1>Home Base 3.0</h1>
        </div>
        <div class="right">
            <nav>
                <a href="file:///C:/cygwin64/home/Brett%20Brewster/projects/Unfinished%20Projects/yolked/project/index.html" target="_blank">Yolked!</a>
                <a href="https://quizlet.com/215065234/programming-terminology-flash-cards/" target="_blank">Quizlet</a>
            </nav>
        </div>
    </header>
    <div id="ghost-header"></div>

    <div class="all-columns">

        <div class="first-column">
            <div id="work-schedule">
                <h2>Today's Work Schedule</h2>
                <button id="work-schedule-pa">Programmer Analyst</button>
                <button id="work-schedule-pa-ricks">Programmer Analyst + Rick's</button>
                <button id="work-schedule-ricks-am">Rick's AM</button>
                <button id="work-schedule-ricks-pm">Rick's PM</button>
                <button id="work-schedule-ricks-dbl">Rick's Double</button>
                <button id="work-schedule-none">None</button>
            </div>
            <div id="queue">
                <h2 id="queueH2">Queue</h2>
                <div id="log-a-session">
                </div>
            </div>
        </div>
        <div class="second-column">
            <div id="stats">
                <!-- <h2>Activity Name</h2>
          <p>General Goal: </p>
          <p>Today's Target: </p>
          <p>Today's Progress: </p>
          <p>Total Hours: </p> -->
            </div>
        </div>
        <div class="third-column">
            <div class="daily-habits">
                <h2>Habits</h2>
                <div class="habit">
                    <img src="resources/assets/images/medication.jpg" class="habit-image">
                    <h3>A.M.</h3>
                </div>
                <div class="habit">
                    <img src="resources/assets/images/juice.jpg" class="habit-image">
                    <h3>Juicing</h3>
                </div>
                <div class="habit">
                    <img src="resources/assets/images/medication.jpg" class="habit-image">
                    <h3>P.M.</h3>
                </div>
            </div>
            <div class="daily-weather">
                <h2>Weather</h2>
                <div class="weather">
                    <img src="resources/assets/images/transparent.png" class="weather-image" id="my-weather-button">
                    <h3>My Weather</h3>
                </div>
                <div class="weather">
                    <img src="resources/assets/images/transparent.png" class="weather-image" id="albany-weather-button">
                    <h3>Albany</h3>
                </div>
                <div class="weather">
                    <img src="resources/assets/images/transparent.png" class="weather-image" id="san-diego-weather-button">
                    <h3>San Diego</h3>
                </div>
                <div class="weather">
                    <img src="resources/assets/images/transparent.png" class="weather-image" id="kula-weather-button">
                    <h3>Kula</h3>
                </div>
                <div class="weather">
                    <img src="resources/assets/images/transparent.png" class="weather-image" id="phoenix-weather-button">
                    <h3>Phoenix</h3>
                </div>
                <div class="weather">
                    <img src="resources/assets/images/transparent.png" class="weather-image" id="austin-weather-button">
                    <h3>Austin</h3>
                </div>
            </div>
            <div class="to-do" id="urgent-errand-div">
                <h2>Urgent Errands</h2>
                <ol>
                </ol>
            </div>
            <div class="to-do" id="errand-div">
                <h2>Errands</h2>
                <ol>
                </ol>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="resources/js/main.js"></script>
    <script type="text/javascript" src="resources/js/speech.js"></script>
    <script type="text/javascript" src="resources/js/greeting.js"></script>
    <script type="text/javascript" src="resources/js/weather.js"></script>
</body>

</html>