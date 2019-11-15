<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');
include($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/classes/wellness_habit.php');
include($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/classes/wellness_metric.php');
include($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/classes/wellness_dimension.php');

//---RETRIEVE POST/GET VARIABLES--------------------------------------------------------
$date = $_POST['date'] ?? $_GET['date'] ?? date("Y-m-d");

//---CONNECT TO DATABASE------------------------------------------------------------
$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
$data = new stdClass();
$data->wellness_dimensions = array();

//---QUERY AGAINST DATABASE---------------------------------------------------------
$q = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/queries/retrieve_all_personal_wellness_dimensions.sql');

$res = $conn->query($q);
if ($res->num_rows > 0) { // If there are results then pull them up
    while ($row = $res->fetch_assoc()) {
        $wd = new Wellness_Dimension($row['id'], $row['name'], $row['golden_mean'], $row['mr_rating']);
        $wd->populate_metrics();
        foreach ($wd->metrics AS $m) {
            $m->populate_habits();
        }
        $data->wellness_dimensions[] = $wd;
    }
}
// echo "<pre>";
// var_dump($data);
// echo "</pre>";

$conn->close();

// Link to Style Sheets
//include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/main-new.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/modal.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/notifications.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/goals.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/fitness-new.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/weather.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/finance.css">

</head>

<body>
    <style>
        div.goal {
            position: relative;
            width: 17.5rem;
            height: 5rem;
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-flow: column nowrap;
            flex-flow: column nowrap;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-pack: distribute;
            justify-content: space-around;
            margin: 0.25rem;
            padding: 0.5rem;
            border-top-left-radius: 0.75rem 3rem;
            border-top-right-radius: 0.75rem 3rem;
            border-bottom-left-radius: 0.75rem 3rem;
            border-bottom-right-radius: 0.75rem 3rem;
            background: black;
        }

        div.goal span.goal-info {
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-flow: row nowrap;
            flex-flow: row nowrap;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        div.goal span.goal-info i {
            font-size: 0.5rem;
            width: 1.125rem;
            height: 1.125rem;
            position: absolute;
            top: 0.5rem;
            right: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1rem;
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
        }

        div.goal span.goal-info i:hover {
            background: white;
            color: black;
        }

        div.goal h3 {
            font-size: 1.5rem;
            color: #00d5ff;
        }

        div.goal h4 {
            font-size: 1.25rem;
        }

        div.goal h5 {
            font-size: 0.5rem;
        }

        div.goal div.progress {
            height: 1rem;
            width: 80%;
            border: 1px solid white;
            border-radius: 0.5rem;
        }

        div.goal div.fill {
            height: 100%;
            border: none;
            border-radius: 0.5rem;
            /*background: hsl(190, 100%, 50%);*/
        }

        div.goal div.target-fill {
            height: 14px;
            border-right: 1px dashed white;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.5);
            position: relative;
            bottom: 14px;
            z-index: 10;
        }
    </style>
    <main>
        <?php
        include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/sections/modal.php');
        include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/sections/header.php');
        ?>
        <?php
        foreach ($data->wellness_dimensions as $wd) {
            echo $wd->return_html();
        }
        ?>


        <?php
        include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
        ?>

        <script>
            $(document).ready(function() {
                $('.progress .fill').each(function() {
                    let value = $(this).attr('data-value');
                    //console.log(`VALUE: ${value}`);
                    let hue = value * 1.9;
                    //console.log(`HUE: ${hue}`);
                    $(this).css('background', `linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`);
                    //console.log(`linear-gradient(90deg, hsl(0, 100%, 50%), hsl(${hue}, 100%, 50%))`)
                });
                $('div.goal i.fa-info').on('click', function(event) {
                    let info = $(this).attr('data-goal-description');
                    showModal(event, info);
                });
            });
        </script>