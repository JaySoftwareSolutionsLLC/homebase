<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES----------------------------------------------------
	$today_time = time();
	$today_date = date('Y-m-d');
	$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";
	$days_left_in_2018 = floor((strtotime('January 1st, 2019') - $today_time) / SEC_IN_DAY);

//---SELECT FROM DATABASE-----------------------------------------------------------
	


//---CLOSE DATABASE CONNECTION------------------------------------------------------
	$conn->close();

//---BEGIN HTML---------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <title>Home Base 3.0</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="">
</head>

<body>
	<main>
	<?php

	?>
	</main>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src=""></script>

</body>

</html>