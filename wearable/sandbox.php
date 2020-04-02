<?php

// This page is exempt from login requirement
$login_exemption = true;

//---INCLUDE RESOURCES--------------------------------------------------------------
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

//---CONNECT TO DATABASE------------------------------------------------------------
$conn = connect_to_db();

//---Initialize variables-----------------------------------------------------------
$dt_start = new DateTime('monday this week');
$dt_end = new DateTime('sunday this week');
$today_dt = new DateTime('now');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Wearable Sandbox</title>
    <style>
        * {
            box-sizing: border-box;
        }
        html {
            background: black;
            height: 100vh;
            width: 100vw;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: 20px;
        }
        body {
            background: white;
            box-sizing: border-box;
            border-radius: 100rem;
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        div#buttons {
            margin: 0;
            padding: 1rem 2rem;
            display: flex;
            border-radius: 100rem;
            flex-flow: row wrap;
            width: 100%;
            height: 100%;
            justify-content: space-evenly;
            align-items: center;
            overflow: hidden;
        }
        button {
            height: 4rem;
            width: 4rem;
            border-radius: 2rem;
            outline: none;
            border: none;
        }
    </style>
</head>
<body>
    <div id='buttons'>
        <button>A</button>
        <button>B</button>
        <button>C</button>
        <button>D</button>
        <button>E</button>
        <button>F</button>
        <button>G</button>
        <button>H</button>
        <button>I</button>
        <button>J</button>
        <button>K</button>
        <button>L</button>
    </div>
</body>
</html>