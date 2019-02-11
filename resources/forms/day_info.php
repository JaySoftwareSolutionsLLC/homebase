<?php
    //---INCLUDE RESOURCES--------------------------------------------------------------
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');

    //---RETRIEVE POST VARIABLES--------------------------------------------------------
    $date = $_POST['date-input'] ?? date("Y-m-d");
    //var_dump($_POST);

    //---CONNECT TO DATABASE------------------------------------------------------------
    $conn = connect_to_db();

    //---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
    $out_of_range = false;
    $user_feedback = "";

    //---QUERY AGAINST DATABASE---------------------------------------------------------
    $day = new stdClass();
    $q = "SELECT * FROM personal_day_info WHERE date = '$date' ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) { // If there are results then pull them up
		while($row = $res->fetch_assoc()) {
			$day->id = $row['id'];
			$day->date = $row['date'];
			$day->software_dev_hours = $row['software_dev_hours'];
            $day->mindfulness_hours = $row['mindfulness_hours'];
            $day->optimal_health = $row['optimal_health'];
            if ($day->optimal_health === null) {
                // echo $day->optimal_health;
            }
            else if ($day->optimal_health == '1') {
                $day->optimal_health = true;
                // echo $day->optimal_health;
            }
            else {
                $day->optimal_health = false;
                // echo $day->optimal_health;
            }
		}
    }
    else { // If there are not results yet and the day in question is before or equal to today, create a new row for the day in question
        $start_date = date('Y-m-d', strtotime( START_DATE_STRING_DAY_INFO ) );
        $today = date('Y-m-d');
        if ( $date <= $today && $date >= $start_date ) {
            $user_feedback .= "<li>Generating row because $date is between $start_date and $today...</li>";
            $qry = " INSERT INTO `personal_day_info` (`id`, `date`, `software_dev_hours`, `mindfulness_hours`, `optimal_health`) VALUES (NULL, '$date', '0.00', '0.00', NULL); ";
            if ($conn->query($qry) === TRUE) {
                $user_feedback = "<li>New row created successfully<li/>";
                $qry_s = "SELECT id FROM `personal_day_info` WHERE date = '$date' ORDER BY id LIMIT 1";
                $res_s = $conn->query($qry_s);
                if ($res_s->num_rows > 0) {
                    $row_s = $res_s->fetch_assoc();
                    $day->id = $row_s['id'];
		        	$day->date = $date;
			        $day->software_dev_hours = 0;
                    $day->mindfulness_hours = 0;
                    $day->optimal_health = null;
                    $user_feedback .= "<li>New ID is $day->id</li>";
                }
                else {
                    $user_feedback .= "<li>Unable to retrieve ID</li>";
                }
            }
            else {
                $user_feedback .= "<li>Unable to insert new row</li>";
            }
        }
        else {
            $out_of_range = true;
            echo "Sorry. The date you requested is out of range. Page will redirect in 2 seconds.";
            sleep( 2 );
            header('Location: https://www.brettjaybrewster.com/homebase/resources/forms/day_info.php');
        }
    }
?>

<html lang="en-US">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
        <meta name="description" content="change">
        <link rel="shortcut icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
        <link rel="icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
        <title><?php echo $date; ?></title>
    <?php 	//include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/forms/form-resources/css-files.php');
            include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');
            include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php'); ?>
        <link rel="stylesheet" type="text/css" href="../css/day_info.css">
    </head>

    <body>

        <main>
            <form id='day-info-form' method='POST'>
                <input class='day-input' type='date' name='date-input' id='date-input' value='<?php echo $date; ?>' style=''/>
                <p class='user-feedback' <?php if ($user_feedback == '') { echo "style='display: none;'"; } ?> >
                    <ul>
                        <?php echo $user_feedback; ?>
                    </ul>
                </p>
                <section style=''>
                    <h3 style=''>Notifications</h3>
                </section>
                <section style=''>
                    <h3 style=''>Fitness</h3>
                </section>  
                <section style=''>
                    <h3 style=''>Nutrition</h3>
                </section>
                <section style=''>
                    <h3 style=''>Finance</h3>
                    <div class='row'>
                        <span class='input' style=''>
                            <label for='software-dev-minutes-input'>Dev Min.</label>
                            <input type='number' step='5' min='0' max='1440' name='software-dev-minutes-input' id='software-dev-minutes-input' class='numeric' value='<?php echo time_conversion( 'hours', $day->software_dev_hours, 'minutes' ); ?>'>
                        </span>
                    </div>
                </section>
                <section style=''>
                    <h3 style=''>Personal</h3>
                    <div class='row'>
                        <span class='input' style=''>
                            <label for='mindfulness-minutes-input'>Mindful Min.</label>
                            <input type='number' step='5' min='0' max='1440' name='mindfulness-minutes-input' id='mindfulness-minutes-input' class='numeric' value='<?php echo time_conversion( 'hours', $day->mindfulness_hours, 'minutes' ); ?>'>
                        </span>
                    </div>
                    <div class='row'>
                        <span class='input' style=''>
                            <label for='optimal-health-input'>Optimal Health</label>
                            <select name='optimal-health-input' id='optimal-health-input'>
                                <option value='null' <?php echo ( is_null( $day->optimal_health ) ) ? 'selected' : ''; ?> >Null</option>
                                <option value='1' <?php echo ( $day->optimal_health == '1' ) ? 'selected' : ''; ?> >True</option>
                                <option value='0' <?php echo ( isset( $day->optimal_health ) && ( ! $day->optimal_health ) ) ? 'selected' : ''; ?> >False</option>
                            </select>
                        </span>
                    </div>
                </section>
            </form>

        </main>

        <script>
            $('input#date-input').on('change', function() {
                $('form#day-info-form').submit();
            });
            $('input#software-dev-minutes-input').on('change', function() {
                ajaxPostUpdate( "/homebase/resources/forms/form-resources/update_day_info_cell.php", { 'column-name' : 'software_dev_hours', 'value' : elegantRounding( ( $('input#software-dev-minutes-input').val() / 60 ) , 2 ) , 'id' : <?php echo $day->id; ?> }, false );
            });
            $('input#mindfulness-minutes-input').on('change', function() {
                ajaxPostUpdate( "/homebase/resources/forms/form-resources/update_day_info_cell.php", { 'column-name' : 'mindfulness_hours', 'value' : elegantRounding( ( $('input#mindfulness-minutes-input').val() / 60 ) , 2 ) , 'id' : <?php echo $day->id; ?> }, false );
            });
            $('select#optimal-health-input').on('change', function() {
                console.log('trig-1');
                ajaxPostUpdate( "/homebase/resources/forms/form-resources/update_day_info_cell.php", { 'column-name' : 'optimal_health', 'value' : $('select#optimal-health-input').children("option:selected").val() , 'id' : <?php echo $day->id; ?> }, false );
            });
            
        </script>

    </body>

</html>