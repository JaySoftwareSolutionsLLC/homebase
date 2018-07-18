<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
	$today_time = time();
	$today_date = date('Y-m-d');
	$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";

//---SELECT FROM DATABASE-----------------------------------------------------------

	// FINANCIAL--------------------------------------------------------------------

	// Time Related Variables
	$start_date_financial = date('Y/m/d', strtotime($START_DATE_STRING_FINANCIAL));
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
			$unreceived_seal_income += ($HOURLY_WAGE_SEAL * 8);
		}
		$fuse++;
		if ($fuse > 30) {
			echo 'FUSE BLOWN in finance.php';
			break;
		}
	}

	// NET INCOME : Hourlywage at ricks multiplied by ricks hours + net tips from ricks + net recorded income from seal and design + unreceived (but earned) income from seal and design
	$net_income = ($HOURLY_WAGE_RICKS * $net_ricks_hours) + $net_ricks_tips + $net_seal_income + $unreceived_seal_income;

	$adi = number_format($net_income / $days_active_financial, 2);
	$ade = number_format($net_expenditure / $days_active_financial, 2);
	$awh = number_format(7 * $net_hours / $days_active_financial, 2);
	$ahw = number_format(7 * $adi / $awh, 2);

	$current_net_worth = $current_assets + $current_cash - $current_liabilities;

	$estimated_2018_income = number_format($PRE_JUNE_RICKS_INCOME + ($adi  * ($days_left_in_year_financial + $days_active_financial)), 0);

	$estimated_EOY_net_worth = number_format($current_net_worth + ((($adi * ($ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - $ade) * ($days_left_in_year_financial)), 0);

	//---FITNESS--------------------------------------------------------------------

	// Running

	$start_date_running = date('Y/m/d', strtotime($START_DATE_STRING_RUNNING));
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

	//---GOALS----------------------------------------------------------------------

	$percent_goal_debt_free = 	number_format(((JUNE_1ST_DEBT - $current_liabilities) / JUNE_1ST_DEBT) * 100, 2);
	$percent_time_frame_debt_free = number_format((100 * $days_active_financial / (((strtotime('January 1st, 2019')) - strtotime($START_DATE_STRING_FINANCIAL)) / SEC_IN_DAY)), 2);

	$percent_goal_net_worth = 	number_format((($current_cash + $current_assets - $current_liabilities - JUNE_1ST_NET_WORTH) / (END_OF_YEAR_NET_WORTH_TARGET - JUNE_1ST_NET_WORTH)) * 100, 2);
	$percent_time_frame_net_worth = number_format((100 * $days_active_financial / (((strtotime('January 1st, 2019')) - strtotime($START_DATE_STRING_FINANCIAL)) / SEC_IN_DAY)), 2);
	
	// Case Tests: mrbw = 147 --> 0% | mrbw = 160 --> 100% | mrbw = 153.5 --> 50%
	// All Case Tests PASS
	$percent_goal_body_weight = number_format(($most_recent_body_weight - STARTING_BODY_WEIGHT) * (100 / (BODY_WEIGHT_TARGET -STARTING_BODY_WEIGHT)), 2);
	$percent_time_frame_body_weight = number_format((100 * $days_active_body_weight / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_BODY_WEIGHT)) / SEC_IN_DAY)), 2);

	// Case Tests: bmt = 405 --> 100% | bmt = 515 --> 0% | bmt = 460 --> 50%
	// All Case Tests PASS
	$percent_goal_mile_time = number_format(100 - (($best_mile_time - MILE_TIME_TARGET) * (100 / ($STARTING_MILE_TIME - MILE_TIME_TARGET))), 2);
	$percent_time_frame_running = number_format((100 * $days_active_running / (((strtotime('January 1st, 2019')) - strtotime($START_DATE_STRING_RUNNING)) / SEC_IN_DAY)), 2);
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
		$conn->close();
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