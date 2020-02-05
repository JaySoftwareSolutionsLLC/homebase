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
$notifications = "";

//---QUERY AGAINST DATABASE---------------------------------------------------------
$day = new stdClass();
$q = "SELECT * FROM personal_day_info WHERE date = '$date' ";
$res = $conn->query($q);
if ($res->num_rows > 0) { // If there are results then pull them up
    while ($row = $res->fetch_assoc()) {
        $day->id = $row['id'];
        $day->date = $row['date'];
        $day->datetime = new DateTime($day->date);
        $day->day_of_week = date_format($day->datetime, 'l');
        $day->yesterday = date_format((clone $day->datetime)->modify('-1 day'), 'Y-m-d');
        $day->tomorrow = date_format((clone $day->datetime)->modify('+1 days'), 'Y-m-d');
        $day->software_dev_hours = $row['software_dev_hours'];
        $day->software_cert_hours = $row['software_cert_hours'];
        $day->mindfulness_hours = $row['mindfulness_hours'];
        $day->optimal_health = $row['optimal_health'];
        $day->expense_review = $row['expense_review'];
        $day->tanning_minutes = $row['tanning_minutes'];
        $day->tanning_uv_index = $row['tanning_uv_index'];
        if ($day->optimal_health === null) {
            // echo $day->optimal_health;
        } else if ($day->optimal_health == '1') {
            $day->optimal_health = true;
            // echo $day->optimal_health;
        } else {
            $day->optimal_health = false;
            // echo $day->optimal_health;
        }
    }
} else { // If there are not results yet and the day in question is before or equal to today, create a new row for the day in question
    $start_date = date('Y-m-d', strtotime(START_DATE_STRING_DAY_INFO));
    $today = date('Y-m-d');
    if ($date <= $today && $date >= $start_date) {
        $notifications .= "<li>Generating row because $date is between $start_date and $today...</li>";
        $qry = " INSERT INTO `personal_day_info` (`id`, `date`, `software_dev_hours`, `software_cert_hours`, `mindfulness_hours`, `optimal_health`) VALUES (NULL, '$date', '0.00', '0.00', '0.00', NULL); ";
        if ($conn->query($qry) === TRUE) {
            $notifications = "<li>New row created successfully<li/>";
            $qry_s = "SELECT id FROM `personal_day_info` WHERE date = '$date' ORDER BY id LIMIT 1";
            $res_s = $conn->query($qry_s);
            if ($res_s->num_rows > 0) {
                $row_s = $res_s->fetch_assoc();
                $day->id = $row_s['id'];
                $day->date = $date;
                $day->software_dev_hours = 0;
                $day->software_cert_hours = 0;
                $day->mindfulness_hours = 0;
                $day->optimal_health = null;
                $day->expense_review = 0;
                $day->tanning_minutes = 0;
                $day->tanning_uv_index = null;
                $notifications .= "<li>New ID is $day->id</li>";
            } else {
                $notifications .= "<li>Unable to retrieve ID</li>";
            }
        } else {
            $notifications .= "<li>Unable to insert new row</li>";
        }
    } else {
        $out_of_range = true;
        echo "Sorry. The date you requested is out of range. Page will redirect in 2 seconds.";
        sleep(2);
        header('Location: https://www.brettjaybrewster.com/homebase/resources/forms/day_info.php');
    }
}
// Push into day object other relevant information derived from other tables such as income, expenditure, lifts performed, miles ran, etc.

$day->ricks_hours = return_ricks_hours($conn, $day->date, $day->date);
$day->ricks_income = return_ricks_pre_tax_income($conn, $day->date, $day->date, 7.5);

$day->seal_hours = return_seal_hours($conn, $day->date, $day->date);
$day->seal_income = return_seal_pre_tax_salary($conn, $day->date, $day->date, 3);
$day->seal_cert_min = return_seal_cert_min($conn, $day->date, $day->date);

$day->income = $day->seal_income + $day->ricks_income;
$day->net_working_hours = $day->seal_hours + $day->ricks_hours;

$day->net_hourly = ($day->net_working_hours > 0) ? round($day->income / $day->net_working_hours, 2) : 0;

$day->expenditure = return_expenditure($conn, $day->date, $day->date);
// Estimated Net Cont.
$day->est_net_cont = round(($day->income * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - $day->expenditure, 2);

$day->habits = return_habitobj_array($conn, $day->date);

?>

<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <title><?php echo $date; ?></title>
    <?php     //include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/forms/form-resources/css-files.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php'); ?>
    <link rel="stylesheet" type="text/css" href="../css/day_info.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/modal.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/fontawesome-free-5.8.1-web/css/all.min.css">
</head>

<body>
    <?php
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/sections/modal.php');
    ?>
    <main>
        <form id='day-info-form' method='POST'>
            <span style='display: inline-flex; justify-content: center; align-items: center; font-size: 2rem; color: hsl(190, 100%, 50%);'>
                <span class='minus-one-day' style='cursor: pointer;'>
                    <i class='fas fa-chevron-left' style='margin-right: 3rem;'></i>
                </span>
                <h1 style='width: auto;'><?php echo $day->day_of_week ?></h1>
                <input class='day-input' type='date' name='date-input' id='date-input' value='<?php echo $date; ?>' style='' />
                <span class='plus-one-day' style='cursor: pointer;'>
                    <i class='fas fa-chevron-right'></i>
                </span>
            </span>
            <!-- <p class='notifications' <?php if ($notifications == '') {
                                            echo "style='display: none;'";
                                        } ?>>
                <ul>
                    <?php echo $notifications; ?>
                </ul>
            </p> -->
            <!-- <section style=''>
                <h3 style=''>Notifications</h3>
            </section>
            <section style=''>
                <h3 style=''>Fitness</h3>
            </section> -->
            <!-- <section style=''>
                <h3 style=''>Nutrition</h3>
                <div class='row'>
                    <span class='flex-input' style=''>
                        <label for='consumption-timewindow'>Consumption Window (hrs)</label>
                        <input disabled='true' type='number' name='consumption-timewindow' id='consumption-timewindow' value='' placeholder='9' />
                    </span>
                    <span class='flex-input' style=''>
                        <label for='consumption-sugar'>Sugar (g)</label>
                        <input disabled='true' type='number' name='consumption-sugar' id='consumption-sugar' value='' placeholder='25' />
                    </span>
                    <span class='flex-input' style=''>
                        <label for='consumption-fat-percentage'>Fat %</label>
                        <input disabled='true' type='number' name='consumption-fat-percentage' id='consumption-fat-percentage' value='' placeholder='' />
                    </span>
                    <span class='flex-input' style=''>
                        <label for='consumption-protein-percentage'>Protein %</label>
                        <input disabled='true' type='number' name='consumption-protein-percentage' id='consumption-protein-percentage' value='' placeholder='' />
                    </span>
                    <span class='flex-input' style=''>
                        <label for='consumption-carb-percentage'>Carb %</label>
                        <input disabled='true' type='number' name='consumption-carb-percentage' id='consumption-carb-percentage' value='' placeholder='' />
                    </span>
                </div>
            </section> -->
            <section style=''>
                <h3 style=''>Finance</h3>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='software-dev-minutes-input'>Dev Min.</label>
                        <input type='number' step='5' min='0' max='1440' name='software-dev-minutes-input' id='software-dev-minutes-input' class='numeric' value='<?php echo time_conversion('hours', $day->software_dev_hours, 'minutes'); ?>'>
                    </span>
                    <span class='timer' id='dev-min-timer'>
                        <i class='fas fa-stopwatch'></i>
                    </span>
                    <span class='input' style=''>
                        <label for='software-cert-minutes-input'>Cert Min.</label>
                        <input type='number' step='5' min='0' max='1440' name='software-cert-minutes-input' id='software-cert-minutes-input' class='numeric' value='<?php echo time_conversion('hours', $day->software_cert_hours, 'minutes'); ?>'>
                    </span>
                    <span class='timer' id='cert-min-timer'>
                        <i class='fas fa-stopwatch'></i>
                    </span>
                </div>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='estimated-income'>Income</label>
                        <input disabled='true' type='number' name='estimated-income' id='estimated-income' class='numeric' value='<?php echo ($day->income); ?>' />
                    </span>
                    <span class='input' style=''>
                        <label for='estimated-expenditure'>Expenditure</label>
                        <input disabled='true' type='number' name='estimated-expenditure' id='estimated-expenditure' class='numeric' value='<?php echo $day->expenditure; ?>' />
                    </span>
                    <span class='input' style=''>
                        <label for='estimated-net-worth-cont'>NW Cont.</label>
                        <input disabled='true' type='number' name='estimated-net-worth-cont' id='estimated-net-worth-cont' class='numeric' value='<?php echo $day->est_net_cont; ?>' />
                    </span>
                </div>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='seal-hours-input'>Seal Hours</label>
                        <input disabled='true' type='number' name='seal-hours-input' id='seal-hours-input' class='numeric' value='<?php echo $day->seal_hours; ?>' />
                    </span>
                    <span class='input' style=''>
                        <label for='seal-cert-min-input'>Seal Cert. Min</label>
                        <input disabled='true' type='number' name='seal-cert-min-input' id='seal-cert-min-input' class='numeric' value='<?php echo $day->seal_cert_min; ?>' />
                    </span>
                    <span class='input' style=''>
                        <label for='ricks-hours-input'>Ricks Hours</label>
                        <input disabled='true' type='number' name='ricks-hours-input' id='ricks-hours-input' class='numeric' value='<?php echo $day->ricks_hours; ?>' />
                    </span>
                    <span class='input' style=''>
                        <label for='net-working-hours-input'>Net Working Hours</label>
                        <input disabled='true' type='number' name='net-working-hours-input' id='net-working-hours-input' class='numeric' value='<?php echo $day->net_working_hours; ?>' />
                    </span>
                </div>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='hourly'>Hourly</label>
                        <input disabled='true' type='number' name='hourly-input' id='hourly-input' class='numeric' value='<?php echo $day->net_hourly; ?>' />
                    </span>
                </div>
            </section>
            <section style=''>
                <h3 style=''>Personal</h3>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='mindfulness-minutes-input'>Mindful Min.</label>
                        <input type='number' step='5' min='0' max='1440' name='mindfulness-minutes-input' id='mindfulness-minutes-input' class='numeric' value='<?php echo time_conversion('hours', $day->mindfulness_hours, 'minutes'); ?>'>
                    </span>
                    <span class='timer' id='mindfulness-min-timer'>
                        <i class='fas fa-stopwatch'></i>
                    </span>
                </div>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='optimal-health-input'>Optimal Health</label>
                        <select name='optimal-health-input' id='optimal-health-input'>
                            <option value='null' <?php echo (is_null($day->optimal_health)) ? 'selected' : ''; ?>>Null</option>
                            <option value='1' <?php echo ($day->optimal_health == '1') ? 'selected' : ''; ?>>True</option>
                            <option value='0' <?php echo (isset($day->optimal_health) && (!$day->optimal_health)) ? 'selected' : ''; ?>>False</option>
                        </select>
                    </span>
                </div>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='tanning-minutes-input'>Tanning Min.</label>
                        <input type='number' step='5' min='0' max='255' name='tanning-minutes-input' id='tanning-minutes-input' class='numeric' value='<?php echo $day->tanning_minutes ?? '0'; ?>'>
                    </span>
                    <span class='timer' id='tanning-min-timer'>
                        <i class='fas fa-stopwatch'></i>
                    </span>
                    <span class='input' style=''>
                        <label for='avg-uv-input'>Avg. UV Index</label>
                        <input type='number' step='1' min='0' max='15' name='avg-uv-input' id='avg-uv-input' class='numeric' value='<?php echo $day->tanning_uv_index; ?>'>
                    </span>
                </div>
            </section>
            <section>
                <h3>Tasks</h3>
                <div class='row'>
                    <span class='input' style=''>
                        <label for='expense-review-input'>Expenses Reviewed</label>
                        <select name='expense-review-input' id='expense-review-input'>
                            <option value='1' <?php echo ($day->expense_review == '1') ? 'selected' : ''; ?>>True</option>
                            <option value='0' <?php echo ($day->expense_review == 0 || empty($day->expense_review)) ? 'selected' : ''; ?>>False</option>
                        </select>
                    </span>
                </div>
            </section>
            <section>
                <h3>Habits</h3>
                <ul>
                <?php foreach ($day->habits as $h) {
                    echo "<li>";
                    if (is_null($h->max_logs_per_day) || $h->logged_today < $h->max_logs_per_day) {
                        echo "<i class='fas fa-feather-alt'></i>";
                    }
                    if ($h->logged_today > 0) { 
                        echo "<i class='fas fa-trash' style='color: red'></i>";
                    }
                    echo "$h->name" . "(" . $h->id . ")";
                    echo "</li>";
                } ?>
                </ul>
            </section>
        </form>

    </main>

    <script type="text/javascript" src="/homebase/resources/resources.js"></script>
    <script type="text/javascript" src="/homebase/resources/js/day_info.js"></script>
    <script type="text/javascript" src="/homebase/resources/fontawesome-free-5.8.1-web/js/all.min.js"></script>
    <script>
        $('input#date-input').on('change', function() {
            $('form#day-info-form').submit();
        });
        $('input#software-dev-minutes-input').on('change', function() {
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'software_dev_hours',
                'value': elegantRounding(($('input#software-dev-minutes-input').val() / 60), 2),
                'id': <?php echo $day->id; ?>
            }, false);
        });
        $('input#software-cert-minutes-input').on('change', function() {
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'software_cert_hours',
                'value': elegantRounding(($('input#software-cert-minutes-input').val() / 60), 2),
                'id': <?php echo $day->id; ?>
            }, false);
        });
        $('input#mindfulness-minutes-input').on('change', function() {
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'mindfulness_hours',
                'value': elegantRounding(($('input#mindfulness-minutes-input').val() / 60), 2),
                'id': <?php echo $day->id; ?>
            }, false);
        });
        $('input#tanning-minutes-input').on('change', function() {
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'tanning_minutes',
                'value': $('input#tanning-minutes-input').val(),
                'id': <?php echo $day->id; ?>
            }, false);
        });
        $('input#avg-uv-input').on('change', function() {
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'tanning_uv_index',
                'value': $('input#avg-uv-input').val(),
                'id': <?php echo $day->id; ?>
            }, false);
        });
        $('select#optimal-health-input').on('change', function() {
            console.log('trig-1');
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'optimal_health',
                'value': $('select#optimal-health-input').children("option:selected").val(),
                'id': <?php echo $day->id; ?>
            }, false);
        });
        $('span.minus-one-day').on('click', function() {
            $('input#date-input').val('<?php echo $day->yesterday; ?>');
            $('form#day-info-form').submit();
        });
        $('span.plus-one-day').on('click', function() {
            $('input#date-input').val('<?php echo $day->tomorrow; ?>');
            $('form#day-info-form').submit();
        });
        $('select#expense-review-input').on('change', function() {
            ajaxPostUpdate("/homebase/resources/forms/form-resources/update_day_info_cell.php", {
                'column-name': 'expense_review',
                'value': $('select#expense-review-input').children("option:selected").val(),
                'id': <?php echo $day->id; ?>
            }, false);
        });
    </script>

</body>

</html>