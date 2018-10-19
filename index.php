<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
	$today_time = time();
	$today_date = date('Y-m-d');
	$today_datetime = new DateTime();
	$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";
	$days_left_in_2018 = floor((strtotime('January 1st, 2019') - $today_time) / SEC_IN_DAY);

	$current_bench_press = 185;

//---SELECT FROM DATABASE-----------------------------------------------------------

	// FINANCIAL--------------------------------------------------------------------

	// Time Related Variables
	$start_date_financial = date('Y/m/d', strtotime(START_DATE_STRING_FINANCIAL));
	$start_time_financial = strtotime($start_date_financial);
	$days_active_financial = ceil(($today_time - $start_time_financial) / (SEC_IN_DAY));
	$days_left_in_year_financial = (365 - (date('z') + 1));

	// Retrieve Account Information
	$all_account_names = array();
	$oldest_date = "2550-01-01";
	$current_cash = 0;
	$current_assets = 0;
	$current_liabilities = 0;

	$qry = "SELECT name, MAX(date) AS date FROM finance_accounts GROUP BY name;";
	$res = $conn->query($qry);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$all_account_names[$row['name']] = $row['date'];
		}
	}

	foreach ($all_account_names as $name => $date) {
		$qry = "SELECT name, date, value, type FROM finance_accounts WHERE name = '$name' AND date = '$date';";
		$res = $conn->query($qry);
		$row = $res->fetch_assoc();
		$acnt_type = $row['type'];
		$acnt_date = $row['date'];
		if ($acnt_type == 'cash') {
			$current_cash += $row['value'];
		}
		else if ($acnt_type == 'asset') {
			$current_assets += $row['value'];
		}
		else if ($acnt_type == 'liability') {
			$current_liabilities += $row['value'];
		}
		else {
			echo "ERROR IN DETERMINING ACCOUNT VALUES";
		}
		if ($acnt_date < $oldest_date) {
			$oldest_date = $acnt_date;
		}
	}

	// Determine total expenses since start date
	$q = "SELECT SUM(amount) FROM finance_expenses WHERE date >= '$start_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_expenditure = $row[0];

	// Seal Hours
	$q = "SELECT SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) FROM finance_seal_shifts WHERE date >= '$start_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_seal_hours = $row[0];

	// Ricks Hours
	$q = "SELECT SUM(hours) FROM `finance_ricks_shifts` WHERE date >= '$start_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_hours = $row[0];
	
	// Net Hours
	$net_hours = $net_seal_hours + $net_ricks_hours;


	// Determine total income since start date

	// Ricks Income
	$q = "SELECT SUM(tips) FROM finance_ricks_shifts WHERE date >= '$start_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_tips = $row[0];

	// Seal Income
		// Seal income actually received
	$q = "SELECT SUM(amount) FROM finance_seal_income WHERE date >= '$start_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_seal_income = $row[0];
		// Seal income earned but not yet received
		// UPDATE 07/03/18 - This functionality breaks if a check is deposited before the two weeks is up (ie. we get checks early due to a holiday) because a few days of $ came from May...The lag days were compensated by the few may days so it worked out but now it causes an issue if a check is deposited early :/
	$q = "SELECT MAX(date) FROM finance_seal_income WHERE date >= '$start_date_financial' AND type = 'check'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$last_seal_check_date = $row[0]; // The most recent check date
	$last_seal_check_time = strtotime($last_seal_check_date);
	$four_pm_seconds = (60 * 60 * 16);
	$unreceived_seal_income = 0;
	$fuse = 0;
	$this_time_to_check = $last_seal_check_time + SEC_IN_DAY + $four_pm_seconds;
	while ($this_time_to_check < $today_time) {
		$this_dow = date('D',$this_time_to_check);
		$this_time_to_check += SEC_IN_DAY;
		if ($this_dow != 'Sat' && $this_dow != 'Sun' && ($this_time_to_check < strtotime('July 14th 2018') || $this_time_to_check > strtotime('July 21st 2018'))) {
			$num_hourly_wages = count(HOURLY_WAGES_DATESTRINGS_SEAL);
			$i = 0;
			for ($i; $i < $num_hourly_wages; $i++) {
				if (strtotime(HOURLY_WAGES_DATESTRINGS_SEAL[$i]) > $this_time_to_check) {
					break;
				}
				else {
					$correct_hourly = HOURLY_WAGES_SEAL[$i];
				}
			}
			$unreceived_seal_income += ($correct_hourly * 8);
			// TEST PASSED 2018.10.18 echo "$ $unreceived_seal_income | $days_active_financial days";
		}
		$fuse++;
		if ($fuse > 30) {
			echo 'FUSE BLOWN in finance.php';
			break;
		}
	}

	// NET INCOME : Hourlywage at ricks multiplied by ricks hours + net tips from ricks + net recorded income from seal and design + unreceived (but earned) income from seal and design
	$net_income = (HOURLY_WAGE_RICKS * $net_ricks_hours) + $net_ricks_tips + $net_seal_income + $unreceived_seal_income;

	$adi = number_format($net_income / ($days_active_financial - 7), 2); // the 7 is subtracted from days_active_financial to make ADI reflect NON-UNPAID VACATION DAYS b/c in future all vacations should be paid (at least from S&D)
	$ade = number_format($net_expenditure / ($days_active_financial), 2); // the 7 is NOT subtracted from days_active_financial because will take vacations in future
	$awh = number_format(7 * $net_hours / ($days_active_financial - 7), 2); // the 7 is subtracted from days_active_financial to make AWH reflect NON-UNPAID VACATION DAYS b/c in future all vacations should be paid (at least from S&D)
	$ahw = number_format(7 * $adi / $awh, 2);

	$current_net_worth = $current_assets + $current_cash - $current_liabilities;

	$estimated_2018_income = number_format(PRE_JUNE_RICKS_INCOME + ($adi  * ($days_left_in_year_financial + $days_active_financial)), 0);

	$estimated_EOY_net_worth = number_format($current_net_worth + ((($adi * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - $ade) * ($days_left_in_year_financial)), 0);

	//---FITNESS--------------------------------------------------------------------

	// Running

	$start_date_running = date('Y/m/d', strtotime(START_DATE_STRING_RUNNING));
	$start_time_running = strtotime($start_date_running);

	$days_active_running = ceil(($today_time - $start_time_running) / (SEC_IN_DAY));

	$q = "SELECT MIN(seconds) FROM `fitness_runs` WHERE miles >= 1;";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$best_mile_time = $row[0];

	// Body Weight
	
	$start_date_body_weight = 	date('Y/m/d', strtotime(START_DATE_STRING_BODY_WEIGHT));
	$start_time_body_weight = 	strtotime($start_date_body_weight);

	$days_active_body_weight =	ceil(($today_time - $start_time_body_weight) / (SEC_IN_DAY));

	$q = "SELECT pounds FROM `fitness_measurements_body_weight` WHERE datetime = (SELECT MAX(datetime) FROM `fitness_measurements_body_weight`)";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$most_recent_body_weight = $row[0];

	// Lifting
	$q = "SELECT workout_structure_id FROM `fitness_cycles` WHERE start_date <= '$today_date' AND end_date >= '$today_date' LIMIT 1";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$workout_structure = $row[0];
	// TEST PASSED 2018.10.14 echo "WORKOUT STRUCTURE: $workout_structure <br/>";

	$number_ready_muscles = 0;
	$number_total_muscles = 0;
	$number_near_ideal_muscles = 0;
	$ideal_percent_tolerance = 5; // Used to determine which muscles should be marked as 'ideal'
	$ideal_score =			0;

	$muscle_objects = array();
	$q = "SELECT 
	muscles.id, muscles.common_name, circs.id AS 'circ_id', circs.name AS 'circ_name', circs.ideal, rec_times.ideal_recovery
	FROM `fitness_muscles` AS muscles 
	INNER JOIN `fitness_circumferences` AS circs ON (muscles.circumference_id = circs.id)
	INNER JOIN `fitness_ideal_recovery_times` AS rec_times ON (muscles.id = rec_times.muscle_id)
	WHERE rec_times.workout_structure_id = $workout_structure";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			//var_dump($row);
			$muscle_id = 				$row['id'];
			$muscle_name = 				$row['common_name'];
			
			$muscle_associated_circ_id = $row['circ_id'];
			$muscle_associated_circ = 	$row['circ_name'];
			$muscle_ideal_circ = 		$row['ideal'];
			
			$muscle_ideal_rest =		$row['ideal_recovery'];
			
			$q_current_circ = 			"SELECT value FROM fitness_measurements_circumferences WHERE circumference_id = $muscle_associated_circ_id ORDER BY datetime DESC LIMIT 1";
			$res_current_circ = 		$conn->query($q_current_circ);
			$row_current_circ = 		mysqli_fetch_row($res_current_circ);
			$muscle_current_circ = 		$row_current_circ[0];
			
			$q_mrf = "SELECT datetime FROM `fitness_lifts` WHERE exercise_id IN (SELECT exercise_id FROM `fitness_pivot_exercises_muscles` WHERE muscle_id = $muscle_id AND type = 'primary') ORDER BY datetime DESC LIMIT 1";
			$res_mrf =					$conn->query($q_mrf);
			$row_mrf =					mysqli_fetch_row($res_mrf);
			$muscle_mrf_time =			strtotime($row_mrf[0]); // Most Recent Failure as datetime
			$muscle_mrf_hours =			ceil(($today_time - $muscle_mrf_time) / (60 * 60)); // Most Recent Failure as datetime
			if ($muscle_mrf_hours > 999) { $muscle_mrf_hours = 999; }
			
			$hur =						$muscle_ideal_rest - $muscle_mrf_hours;
			if ($hur <= 0) {
				$number_ready_muscles++;
			}
			$percent_ideal = 			number_format((100 - ((($muscle_ideal_circ - $muscle_current_circ) / $muscle_ideal_circ) * 100)), 2);

			if ($percent_ideal <= 100) {
				$idealness_score = $percent_ideal;
			}
			else {
				$idealness_score = (200 - $percent_ideal);
			}
			
			if ($idealness_score >= (100 - $ideal_percent_tolerance)) {
				$number_near_ideal_muscles++;
			}
			
			$this_muscle =	 			new stdClass();
			$this_muscle -> id =		$muscle_id;
			$this_muscle -> name = 		$muscle_name;
			$this_muscle -> circ =		$muscle_associated_circ;
			$this_muscle -> curr_circ =	$muscle_current_circ;
			$this_muscle -> ideal_circ = $muscle_ideal_circ;
			$this_muscle -> ideal_rest = $muscle_ideal_rest;
			$this_muscle -> mrf =		$muscle_mrf_hours;
			$this_muscle -> hur = 		$hur;
			$this_muscle -> perc_ideal = $percent_ideal;
			
						
			$muscle_objects[] =			$this_muscle;
			
			$number_total_muscles++;
			$ideal_score += 			$idealness_score;			
			
			//echo "ID: $muscle_id | COMMON NAME: $muscle_name | CIRC.: $muscle_associated_circ | IDEAL: $muscle_ideal_circ | CURRENT: $muscle_current_circ | MRF: $muscle_mrf_hours | IDEAL REC: $muscle_ideal_rest | HUR: $hur | % IDEAL: $percent_ideal <br/>";
		}
	}

	$body_net_percent_ideal =			number_format(($ideal_score / $number_total_muscles), 2);

	//var_dump($muscle_objects);

	//---GOALS----------------------------------------------------------------------

	$percent_goal_debt_free = 	number_format(((JUNE_1ST_DEBT - $current_liabilities) / JUNE_1ST_DEBT) * 100, 2);
	$percent_time_frame_debt_free = number_format((100 * $days_active_financial / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_FINANCIAL)) / SEC_IN_DAY)), 2);

	$percent_goal_net_worth = 	number_format((($current_cash + $current_assets - $current_liabilities - JUNE_1ST_NET_WORTH) / (END_OF_YEAR_NET_WORTH_TARGET - JUNE_1ST_NET_WORTH)) * 100, 2);
	$percent_time_frame_net_worth = number_format((100 * $days_active_financial / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_FINANCIAL)) / SEC_IN_DAY)), 2);
	
	// Case Tests: mrbw = 147 --> 0% | mrbw = 160 --> 100% | mrbw = 153.5 --> 50%
	// All Case Tests PASS
	$percent_goal_body_weight = number_format(($most_recent_body_weight - STARTING_BODY_WEIGHT) * (100 / (BODY_WEIGHT_TARGET -STARTING_BODY_WEIGHT)), 2);
	$percent_time_frame_body_weight = number_format((100 * $days_active_body_weight / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_BODY_WEIGHT)) / SEC_IN_DAY)), 2);

	// Case Tests: bmt = 405 --> 100% | bmt = 515 --> 0% | bmt = 460 --> 50%
	// All Case Tests PASS
	$percent_goal_mile_time = number_format(100 - (($best_mile_time - MILE_TIME_TARGET) * (100 / (STARTING_MILE_TIME - MILE_TIME_TARGET))), 2);
	$percent_time_frame_running = number_format((100 * $days_active_running / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_RUNNING)) / SEC_IN_DAY)), 2);

	$percent_goal_bench_press = number_format( ( 100 * ( $current_bench_press - STARTING_BENCH_PRESS ) / ( END_OF_YEAR_BENCH_PRESS_TARGET - STARTING_BENCH_PRESS ) ), 2);
	$percent_time_frame_bench_press = $percent_time_frame_body_weight; // Rather than redoing the calculation, just using the same time-frame as tracking body weight
//---CLOSE DATABASE CONNECTION------------------------------------------------------
	$conn->close();

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
    <link rel="stylesheet" type="text/css" href="resources/css/reset.css">
    <link rel="stylesheet" type="text/css" href="resources/css/main.css">
    <link rel="stylesheet" type="text/css" href="resources/css/goals.css">
    <link rel="stylesheet" type="text/css" href="resources/css/fitness.css">
    <link rel="stylesheet" type="text/css" href="resources/css/weather.css">
    <link rel="stylesheet" type="text/css" href="resources/css/finance.css">
</head>

<body>
	<main>
	<?php
		include('resources/sections/header.php');
		include('resources/sections/habits.php');
		include('resources/sections/goals.php');
		include('resources/sections/fitness.php');
		include('resources/sections/finance.php');
		include('resources/sections/weather.php');
	?>
	</main>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="resources/js/main.js"></script>
    <script type="text/javascript" src="resources/js/speech.js"></script>
    <script type="text/javascript" src="resources/js/greeting.js"></script>
    <script type="text/javascript" src="resources/js/goals.js"></script>
	<script type="text/javascript" src="resources/js/fitness.js"></script>
    <script type="text/javascript" src="resources/js/finance.js"></script>
    <script type="text/javascript" src="resources/js/weather.js"></script>
</body>

</html>