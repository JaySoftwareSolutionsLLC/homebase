<?php

	$serv = 'localhost';
//	$user = 'jaysoftw_brett';
//	$pass = 'Su944jAk127456';
	$user = 'root';
	$pass = 'Bc6219bAj';
	$db = 'jaysoftw_homebase';

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="INSERT">
    <link rel="shortcut icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <title>Home Base 3.0</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="resources/css/reset.css">
    <link rel="stylesheet" type="text/css" href="resources/css/main.css">
    <link rel="stylesheet" type="text/css" href="resources/css/fitness.css">
    <link rel="stylesheet" type="text/css" href="resources/css/weather.css">
    <link rel="stylesheet" type="text/css" href="resources/css/finance.css">

</head>

<body>

    <header>
        <div class="left">
            <h1>Home Base 3.0</h1>
        </div>
        <div class="right">
            <nav>
               	<a class="coords"></a>
                <a href="https://secure332.sgcpanel.com:2096/cpsess0106124978/webmail/Crystal/index.html?mailclient=horde" target="_blank">webmail</a>
                <a href="https://quizlet.com/215065234/programming-terminology-flash-cards/" target="_blank">Quizlet</a>
            </nav>
        </div>
    </header>
    <div id="ghost-header"></div>
	
	<main>
	<?php
		include('resources/sections/fitness.php');
		include('resources/sections/finance.php');
		include('resources/sections/weather.php');
	?>
	</main>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="resources/js/main.js"></script>
    <script type="text/javascript" src="resources/js/speech.js"></script>
    <script type="text/javascript" src="resources/js/greeting.js"></script>
	<script type="text/javascript" src="resources/js/fitness.js"></script>
    <script type="text/javascript" src="resources/js/finance.js"></script>
    <script type="text/javascript" src="resources/js/weather.js"></script>
</body>

</html>