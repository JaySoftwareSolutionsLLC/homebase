<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Set year to posted value. If no posted value then set to today's year.
$year = set_post_value('year') ?? date('Y');
include($_SERVER["DOCUMENT_ROOT"] . "/homebase/resources/constants-$year.php");

// Connect to Database
$conn = connect_to_db();

// Initialize variables
$title = "AR - $year";
$date_start_dt = ($year == '2018') ? new DateTime("June 01 2018") : $date_start_dt = new DateTime("first day of January $year"); // Start date is either June 1st (for 2018) or January 1st
// If today is before Dec 31st of year in question then use today as end date. Otherwise use Dec 31st
$date_end_dt = new DateTime("last day of december $year");
if (new DateTime() < $date_end_dt) {
	$date_end_dt = new DateTime();
}
$date_start = date_format($date_start_dt, 'Y-m-d');
$date_end = date_format($date_end_dt, 'Y-m-d');
//$count_days = ( strtotime($date_end . "+1 days") - strtotime($date_start) ) / SEC_IN_DAY;
$months = array(); // array to house month objects
$weeks = array(); // array to house week objects
$days = array(); // array to house day objects

// House all records as its own object
Class Record{
	public $name;
	public $value;
	public $datestr;
	public $prefix;
	public $suffix;

	public function __construct($name, $value, $datestr, $prefix = '', $suffix = '') {
		$this->name = $name;
		$this->value = $value;
		$this->datestr = $datestr;
		$this->prefix = $prefix;
		$this->suffix = $suffix;
	}
}
$records = array();

$highest_income_day = new Record('Highest Income Day', 0, '', '$');
$longest_working_day = new Record('Longest Working Day', 0, '', '', ' hrs');

$generated = true;

if ($generated) {
	// TEST PASSED echo "$date_start - $date_end <br/>";
	
	$date_start_to_check_dt = clone $date_start_dt;

	// TEST PASSED echo date_format($date_to_check, 'Y/m/d');
	// var_dump($date_start_to_check_dt);
	// echo "<br/>";
	// var_dump($date_end_dt);
	// echo "<br/>";

	// Retrieve monthly info
	$fuse_length = 15;
	$fuse = 0;
	$date_to_check = clone $date_start_dt;
	while ($date_to_check <= $date_end_dt) {
		$month = new stdClass();
		$month->start_dt = clone $date_to_check;
		$month->start_date = date_format( $month->start_dt, 'Y-m-d' );
		$month->end_dt = clone $month->start_dt;
		$month->end_dt->modify('last day of');
		$month->end_date = date_format( $month->end_dt, 'Y-m-d' );
		$month->num = date_format($month->start_dt, 'n');
		$month->name = date_format($month->start_dt, 'F');
		$month->day_count = date_format($month->start_dt, 't');
		// Determine number of days that have passed this month (will be used for ADE calc for current month)
		$months[] = $month;
		$date_to_check->modify('+1 months');
		$fuse++; 
		if ($fuse > $fuse_length) {
			echo "MONTH FUSE BROKEN";
		}
	}
	echo "<br/><br/><br/><br/><br/>";
	$expenditure_types = array();
	foreach($months as $m) {
		//echo "<h3>Month ($m->name #$m->num) [$m->start_date - $m->end_date]:</h3>";
		$m->net_exp = return_expenditure($conn, $m->start_date, $m->end_date);
		$m->ade = $m->net_exp / ($m->end_dt->diff($m->start_dt)->format("%a"));
		//echo "<h4>$m->net_exp ($m->ade / day)</h4><ul>";
		$m->expenditures = return_expenditure_array($conn, $m->start_date, $m->end_date);
		foreach($m->expenditures as $e) {
			if ( ! in_array($e['type'], $expenditure_types) ) {
				$expenditure_types[] = $e['type'];
			}
			//echo "<li>" . $e['type'] . " : " . round( $e['Expenditure'], 0) . "</li>";
		}
	}
	// var_dump($expenditure_types);

	// Retrieve weekly info
	$date_end_to_check_dt = clone $date_start_to_check_dt;
	$date_end_to_check_dt->modify('next Sunday');
	$fuse_length = 55;
	$fuse = 0;
	while ($date_start_to_check_dt <= $date_end_dt) {
		$week = new stdClass();
		$week->start_dt = clone $date_start_to_check_dt;
		if ($date_end_to_check_dt > $date_end_dt ) { // If date end (Sunday) is beyond the time frame of report (ie. today or Dec 31st) then set the date end to the last day on report
			$date_end_to_check_dt = $date_end_dt;
		}
		$week->end_dt = clone $date_end_to_check_dt;
		$week->start_day = date_format($week->start_dt, 'Y-m-d');
		$week->end_day = date_format($week->end_dt, 'Y-m-d');
		$date_start_to_check_dt->modify('next Monday'); // Increment Start Date to next Monday
		$date_end_to_check_dt->modify('next Sunday'); // Increment End Date to next Sunday
		$weeks[] = $week; // Push week object into weeks array
		$fuse++;
		// echo "$fuse<br/>";
		if ($fuse > $fuse_length) {
			echo "FUSE BROKEN";
			break;
		}
	}
	// var_dump($weeks);
	foreach ($weeks as $w) {
		// For each week create properties for all important info
		$q = "SELECT 
				SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - (break_min / 60)) 
				FROM finance_seal_shifts 
				WHERE date >= '$w->start_day' 
					AND date <= '$w->end_day'";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$w->hours_seal = round($row[0], 2);

		// RICKS HOURS
		$q = "SELECT 
				SUM(hours) 
				FROM `finance_ricks_shifts` 
				WHERE date >= '$w->start_day' 
					AND date <= '$w->end_day'";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$w->hours_ricks = round($row[0], 2);
		// RICKS OTB HOURS
		$q = "SELECT 
				SUM(hours) 
				FROM `finance_ricks_shifts` 
				WHERE date >= '$w->start_day' 
					AND date <= '$w->end_day'
					AND type = 'otb' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$w->hours_otb_ricks = round($row[0], 2);

		// SEAL INCOME
		$w->income_seal = 0;
		$w->salary_seal = 0; // Calculated based on hourly wage during that time frame and subtracting out unpaid time off
		$w->bonuses_seal = 0; // Calculated based on the date of the bonus on finance_seal_income table
		$day_to_check = $w->start_day;
		$fuse = 0;
		while ($day_to_check <= $w->end_day) {
			$this_dow = date('D', strtotime($day_to_check));
			if ($this_dow != 'Sat' && $this_dow != 'Sun' && ($day_to_check < date('Y-m-d', strtotime('July 14th 2018')) || $day_to_check > date('Y-m-d', strtotime('July 21st 2018')))) {
				$num_hourly_wages = count(HOURLY_WAGES_DATESTRINGS_SEAL);
				// TEST PASSED 2018.10.18 echo $num_hourly_wages;
				$i = 0;
				for ($i; $i < $num_hourly_wages; $i++) {
					if (strtotime(HOURLY_WAGES_DATESTRINGS_SEAL[$i]) > strtotime($day_to_check)) {
						break;
					}
					else {
						$correct_hourly = HOURLY_WAGES_SEAL[$i];
						// TEST PASSED 2018.10.18 echo $correct_hourly;
					}
				}
				$w->salary_seal += ($correct_hourly * 8);
			}
			$day_to_check = date('Y-m-d', strtotime($day_to_check.'+1day'));

			// TEST PASSED echo "DOW: $this_dow | INCOME: salary_seal | DAYTOCHECK: $day_to_check | END OF WEEK: $w->end_day <br/>";
			$fuse++;
			if ($fuse >= 10) {
				echo "FUSE BLOWN";
				exit;
			}
		}

			// SEAL BONUSES
		$q = " 	SELECT SUM(amount) AS 'bonus value'
				FROM `finance_seal_income`
				WHERE 	date >= '$w->start_day'
					AND date <= '$w->end_day'
					AND type = 'bonus' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_array($res);
		$w->bonuses_seal = $row['bonus value'] ?? 0;

		$w->income_seal = $w->bonuses_seal + $w->salary_seal;


		// RICKS TIPS
		$q = "SELECT 
				SUM(tips) 
				FROM finance_ricks_shifts 
				WHERE date >= '$w->start_day'
					AND date <='$w->end_day'";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$w->tips_ricks = round($row[0], 2);

		$w->income_ricks = round( ( $w->tips_ricks + ( HOURLY_WAGE_RICKS * ( $w->hours_ricks - $w->hours_otb_ricks ) ) ), 2);

		// SEAL HOURLY
		$w->hourly_seal = round(($w->income_seal / $w->hours_seal), 2);


		// RICKS HOURLY
		$w->hourly_ricks = round(($w->income_ricks / $w->hours_ricks), 2);


		// EXPENDITURES
		$q = "SELECT SUM(amount) FROM finance_expenses WHERE date >= '$w->start_day' AND date <= '$w->end_day'";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$w->expenditure_net = round($row[0], 2);


		$w->expenses = array(); // Not currently used due to way chart.js works, needed to make two arrays instead of one associative array
		$w->expense_names = array();
		$w->expense_values = array();
		$q = "SELECT 
				SUM(amount), type FROM `finance_expenses` 
				WHERE date >= '$w->start_day' 
					AND date <= '$w->end_day' 
				GROUP BY type 
				ORDER BY SUM(amount) DESC";
		$res = $conn->query($q);
		if ($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$w->expenses[$row['type']] = round($row['SUM(amount)'], 2);
				$w->expense_names[] = $row['type'];
				$w->expense_values[] = round($row['SUM(amount)'], 2);
			}
		} else {
			//echo "0 results";
		}


		// NET HOURS
		$w->hours_net = round(($w->hours_seal + $w->hours_ricks), 2) ?? 0;
		// NET INCOME
		$w->income_net = round(($w->income_seal + $w->income_ricks), 2) ?? 0;
		// AVERAGE DAILY INCOME
		$w->adi = round(($w->income_net / $w->count_days), 2) ?? 0;
		// AVERAGE DAILY EXPENDITURE
		$w->ade = round(($w->expenditure_net / $w->count_days), 2) ?? 0;
		// NET HOURLY
		if ( $w->hours_net != 0 ) {
			$w->hourly_net = round(($w->income_net / $w->hours_net), 2);
		}
		else {
			$w->hourly_net = 0;
		}
		// NET INCOME DIFFERENTIAL
		$w->income_diff = round(($w->income_net - $w->expenditure_net), 2) ?? 0;

		// CERT HOURS
		$w->cert_hrs = return_cert_hours($conn, $w->start_day, $w->end_day, 2) ?? 0;
		$w->seal_cert_hrs = round((return_seal_cert_min($conn, $w->start_day, $w->end_day) / 60), 2) ?? 0;

		// DEV HOURS
		$w->dev_hrs = return_dev_hours($conn, $w->start_day, $w->end_day, 2) ?? 0;

		// COMMUTE HOURS (estimate)
		$w->commute_hrs = return_estimated_commute_time($conn, $w->start_day, $w->end_day, 2) ?? 0;

		$w->career_capital_hrs = $w->dev_hrs + $w->hours_net + $w->cert_hrs;
	}
	
	// Retrieve daily info
	$fuse_length = 370;
	$fuse = 0;
	$day_to_check = clone $date_start_dt;
	while ($day_to_check <= $date_end_dt) {
		$day = new stdClass();
		$day->datetime = clone $day_to_check;
		$day->datestr = date_format($day->datetime, 'Y-m-d');
		$day_to_check->modify('+1 days');
		$days[] = $day;
		$fuse++; 
		if ($fuse > $fuse_length) {
			echo "DAILY FUSE BROKEN";
		}
	}
	foreach ($days as $d) {
		// For each week create properties for all important info
		/*
		$q = "SELECT 
				SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - (break_min / 60) ) 
				FROM finance_seal_shifts 
				WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->hours_seal = round($row[0], 2);
		*/
		$d->hours_seal = return_seal_hours($conn, $d->datestr, $d->datestr);

		// RICKS HOURS
		/*
		$q = "SELECT 
				SUM(hours) 
				FROM `finance_ricks_shifts` 
				WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->hours_ricks = round($row[0], 2);
		*/
		$d->hours_ricks = return_ricks_hours($conn, $d->datestr, $d->datestr);

		// RICKS OTB HOURS
		/*
		$q = "SELECT 
				SUM(hours) 
				FROM `finance_ricks_shifts` 
				WHERE date = '$d->datestr'
				AND type = 'otb' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->hours_otb_ricks = round($row[0], 2);
		*/
		$d->hours_otb_ricks = return_ricks_otb_hours($conn, $d->datestr, $d->datestr);		

		// SEAL INCOME
		/*
		$d->income_seal = 0;
		$d->salary_seal = 0; // Calculated based on hourly wage during that time frame and subtracting out unpaid time off
		$d->bonuses_seal = 0; // Calculated based on the date of the bonus on finance_seal_income table
		$day_to_check = $d->datestr;
		$this_dow = date('D', strtotime($day_to_check));
		if ($this_dow != 'Sat' && $this_dow != 'Sun' && ($day_to_check < date('Y-m-d', strtotime('July 14th 2018')) || $day_to_check > date('Y-m-d', strtotime('July 21st 2018')))) {
			$num_hourly_wages = count(HOURLY_WAGES_DATESTRINGS_SEAL);
			$i = 0;
			for ($i; $i < $num_hourly_wages; $i++) {
				if (strtotime(HOURLY_WAGES_DATESTRINGS_SEAL[$i]) > strtotime($day_to_check)) {
					break;
				}
				else {
					$correct_hourly = HOURLY_WAGES_SEAL[$i];
				}
			}
			$d->salary_seal += ($correct_hourly * 8);
		}
		*/
		$d->salary_seal = return_seal_pre_tax_salary($conn, $d->datestr, $d->datestr, 3);
		
		// SEAL BONUSES
		/*
		$q = " 	SELECT SUM(amount) AS 'bonus value'
				FROM `finance_seal_income`
				WHERE 	date = '$d->datestr'
					AND type = 'bonus' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_array($res);
		$d->bonuses_seal = $row['bonus value'] ?? 0;
		*/
		$d->bonuses_seal = return_seal_pre_tax_bonus($conn, $d->datestr, $d->datestr);

		$d->income_seal = $d->bonuses_seal + $d->salary_seal;


		// RICKS TIPS
		/*
		$q = "SELECT 
				SUM(tips) 
				FROM finance_ricks_shifts 
				WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->tips_ricks = round($row[0], 2);
		*/
		$d->tips_ricks = return_ricks_tips($conn, $d->datestr, $d->datestr);

		$d->income_ricks = return_ricks_pre_tax_income($conn, $d->datestr, $d->datestr, HOURLY_WAGE_RICKS);
		
		//$d->income_ricks = round(($d->tips_ricks + (HOURLY_WAGE_RICKS * ($d->hours_ricks - $d->hours_otb_ricks))), 2);

		// SEAL HOURLY
		$d->hourly_seal = round(($d->income_seal / $d->hours_seal), 2);

		// RICKS HOURLY
		$d->hourly_ricks = round(($d->income_ricks / $d->hours_ricks), 2);


		// EXPENDITURES
		/*
		$q = "SELECT SUM(amount) FROM finance_expenses WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->expenditure_net = round($row[0], 2);
		*/
		$d->expenditure_net = return_expenditure($conn, $d->datestr, $d->datestr);


		$d->expenses = array(); // Not currently used due to way chart.js works, needed to make two arrays instead of one associative array
		$d->expense_names = array();
		$d->expense_values = array();
		$q = "SELECT 
				SUM(amount), type FROM `finance_expenses` 
				WHERE date = '$d->datestr'
				GROUP BY type 
				ORDER BY SUM(amount) DESC";
		$res = $conn->query($q);
		if ($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$d->expenses[$row['type']] = round($row['SUM(amount)'], 2);
				$d->expense_names[] = $row['type'];
				$d->expense_values[] = round($row['SUM(amount)'], 2);
			}
		} else {
			//echo "0 results";
		}


		// NET HOURS
		$d->hours_net = round(($d->hours_seal + $d->hours_ricks), 2) ?? 0;
		// NET INCOME
		$d->income_net = round(($d->income_seal + $d->income_ricks), 2) ?? 0;
		// AVERAGE DAILY INCOME
		$d->adi = round(($d->income_net / $d->count_days), 2) ?? 0;
		// AVERAGE DAILY EXPENDITURE
		$d->ade = round(($d->expenditure_net / $d->count_days), 2) ?? 0;
		// NET HOURLY
		$d->hourly_net = round(($d->income_net / $d->hours_net), 2) ?? 0;
		// NET INCOME DIFFERENTIAL
		$d->income_diff = round(($d->income_net - $d->expenditure_net), 2) ?? 0;
		
		$d->non_bonus_income = $d->income_net - $d->bonuses_seal;
		// DAILY RECORDS EVALUATION
		if ( $d->non_bonus_income > $highest_income_day->value ) {
			$highest_income_day->value = $d->non_bonus_income;
			$highest_income_day->datestr = $d->datestr;
		}
		if ( $d->hours_net > $longest_working_day->value ) {
			$longest_working_day->value = $d->hours_net;
			$longest_working_day->datestr = $d->datestr;
		}
		//echo $d->income_seal;
	}
	$records[] = $highest_income_day;
	$records[] = $longest_working_day;
}

// var_dump($days[0]);
// TEST PASSED $date_to_check->modify('+1 weeks');

// Start HTML
?><head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <title><?php echo $title; ?></title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../css/reset.css">
    <link rel="stylesheet" type="text/css" href="../../css/main-new.css">
    <link rel="stylesheet" type="text/css" href="/homebase/resources/css/report.css">
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="/homebase/resources/js/moment.js"></script>
    <script src="/homebase/resources/js/keyfunctions.js"></script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
	<script type="text/javascript" src="/homebase/resources/js/main.js"></script>

</head>


<?php 

include $_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/sections/header.php'; // Website header
include $_SERVER["DOCUMENT_ROOT"] . "/homebase/resources/reports/annual-report/report-options.php"; // Report header

?>


<?php 
	if ($generated) {
?>
		<section class='generated-report'>
			<h1><?= $year; ?> Annual Report</h1>
			<div id='income-flow-chart-container' style='height: 300px; width: 100%;'></div>
			<script>			
//				window.onload = function () {

				let luxuryExpenses = <?php echo json_encode(LUXURY_EXPENDITURES); ?>;

				var incomeFlowChart = new CanvasJS.Chart("income-flow-chart-container", {
					animationEnabled: true,
					title: {
						text: "Income Flow"
					},
					axisX: {
						valueFormatString: "DDD DD MMM YYYY",
						interval: 15,
						intervalType: 'day',
					},
					axisY: {
						title: '$',
						prefix: "$",
						minimum: 0,
					},
					axisY2: {
						title: 'hours',
						minimum: 0,
					},
					toolTip: {
						shared: true
					},
					legend: {
						cursor: "pointer",
						itemclick: toggleDataSeries // Allows user to click a dataset in the legend and toggle thet display of that data set
					},
					dataPointWidth: 10,
					data: [
					{
						type: "stackedColumn",
						name: "S&D Salary",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: '<?php echo COLOR_SEAL_AND_DESIGN; ?>',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . " ), y: $w->salary_seal },";
	}
?>
						]
					},
					{
						type: "stackedColumn",
						name: "Rick's Income",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: '<?php echo COLOR_RICKS_ON_MAIN; ?>',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . " ), y: $w->income_ricks },";
	}
?>
						]
					},
					{
						type: "stackedColumn",
						name: "Bonuses",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: '#ccc',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . " ), y: $w->bonuses_seal },";
	}
?>
						]
					},
					{
						type: "line",
						name: "Expenditure",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: 'red',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . " ), y: $w->expenditure_net },";
	}
?>
						]
					},
					{
						type: "area",
						name: "Differential",
						markerBorderColor: "white",
						markerBorderThickness: 2,
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: 'green',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . " ), y: $w->income_diff },";
	}
?>
						]
					},
					{
						type: "line",
						name: "Hours Worked",
						axisYType: "secondary",
						showInLegend: true,
						color: 'black',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . " ), y: $w->hours_net },";
	}
?>
						]
					},
					{
						type: "line",
						name: "Hourly Wage",
						showInLegend: true,
						yValueFormatString: "",
						color: 'green',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->hourly_net },";
	}
?>
						]
					}	
					
					]
				});
				incomeFlowChart.render();

				function toggleDataSeries(e) {
					if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
						e.dataSeries.visible = false;
					} else {
						e.dataSeries.visible = true;
					}
					e.chart.render();
				}

//				}
			</script>
			
			<div id='hour-chart-container' style='height: 300px; width: 100%;'></div>
			<script>			
			// 	window.onload = function () {

				var hoursChart = new CanvasJS.Chart("hour-chart-container", {
					animationEnabled: true,
					title: {
						text: "Hours"
					},
					axisX: {
						valueFormatString: "DDD DD MMM YYYY",
						interval: 15,
						intervalType: 'day',
					},
					axisY: {
						title: '',
						prefix: '',
						minimum: 0
					},
					toolTip: {
						shared: true
					},
					legend: {
						cursor: "pointer",
						itemclick: toggleDataSeries
					},
					dataPointWidth: 10,
					data: [
						<?php if ($year >= '2020') {
		// Prior to 2020 there was no distinction between dev/cert hrs done at work, so there would be overlap with this calculation  ?>
						{
						type: "line",
						name: "Career Capital Hrs",
						lineDashType: "shortDash",   
						showInLegend: true,
						yValueFormatString: "",
						color: 'hsl(190, 100%, 50%)',
						dataPoints: [
<?php
		foreach ($weeks as $w) {
			echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->career_capital_hrs },";
		}
?>
							]
						},	
<?php } ?>
						{
						type: "line",
						name: "Working Hours",
						showInLegend: true,
						yValueFormatString: "",
						color: 'black',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->hours_net },";
	}
?>
							]
						},	
						{
						type: "line",
						name: "Cert Hrs",
						showInLegend: true,
						yValueFormatString: "",
						color: 'hsl(130, 100%, 50%)',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->cert_hrs },";
	}
?>
							]
						},	
						{
						type: "line",
						name: "Seal Cert Hrs",
						showInLegend: true,
						yValueFormatString: "",
						color: 'hsl(150, 100%, 50%)',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->seal_cert_hrs },";
	}
?>
							]
						},	
						{
						type: "line",
						name: "Dev Hrs",
						showInLegend: true,
						yValueFormatString: "",
						color: 'hsl(100, 100%, 50%)',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->dev_hrs },";
	}
?>
							]
						},	
						{
						type: "line",
						name: "Commute Hrs",
						showInLegend: true,
						yValueFormatString: "",
						color: 'hsl(0, 100%, 50%)',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($w->start_dt, 'Y') . "), y: $w->commute_hrs },";
	}
?>
							]
						},	
					]
				});
				hoursChart.render();

//				}
			</script>

			<div id='monthly-expenditure-chart-container' style='height: 300px; width: 100%;'></div>
			<button id='toggle-all-expenses'>All Expenses</button>
			<button id='toggle-luxury-expenses'>Luxury Expenses</button>
			<button id='toggle-non-luxury-expenses'>Non Luxury Expenses</button>
			<script>

				var monthlyExpenditureChart = new CanvasJS.Chart("monthly-expenditure-chart-container", {
					animationEnabled: true,
					title: {
						text: "Expenditure"
					},
					axisX: {
						valueFormatString: "MMM YYYY",
						interval: 1,
						intervalType: 'month',
					},
					axisY: {
						title: 'Expenditure',
						prefix: '$',
						minimum: 0,
						interval: 250,
					},
					toolTip: {
						enabled: true,
						shared: true,
						cornerRadius: 5,
						borderThickness: 3,
						borderColor: 'hsl(190, 100%, 50%)',
						//backgroundColor: '#960B39',
						//fontColor: 'white',
						contentFormatter: function ( e ) {
							let str = "";
							var arr = [];
							for(var i = 0; i < e.entries.length; i++) {
								if(e.entries[i].dataPoint.y != 0 && e.entries[i].dataSeries.visible) {
									let dataPointStr = " <span class='tooltip'><span style='font-weight: 900; color: " + e.entries[i].dataSeries.color + "' class=''>" + e.entries[i].dataSeries.name + " : &nbsp; &nbsp; &nbsp; </span>" + e.entries[i].dataPoint.y.toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "</span>";
									arr.push(dataPointStr);
								}
							}
							str += arr.join('<br/>');
							return str || 'No Expenditures';
						}  
					},
					legend: {
						cursor: "pointer",
						itemclick: toggleDataSeries
					},
					dataPointWidth: 10,
					data: [
						{
						type: "line",
						name: "Net",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: 'blue',
						dataPoints: [
<?php
	foreach ($months as $m) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($m->start_dt, 'Y') . " ), y: $m->net_exp },";
	}
?>
						]
					},
					{
						type: "line",
						name: "Target",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: 'green',
						lineDashType: 'dash',
						markerType: 'square',
						dataPoints: [
<?php
	foreach ($months as $m) {
		echo "{ x: new Date( " . php_dt_to_js_datestr($m->start_dt, 'Y') . " ), y: " . ( $m->day_count * WEEKLY_EXPENDITURE_TARGET / 7 ) . " },";
	}
?>
						]
					},
					// Create luxury expense (sum) line
					{
						type: "line",
						name: "Luxury",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: 'orange',
						lineDashType: 'solid',
						markerType: 'triangle',
						dataPoints: [
							
<?php
	
	foreach ($months as $m) {
		$val = 0;
		foreach($m->expenditures as $e) {
			if ( in_array($e['type'], LUXURY_EXPENDITURES ) ) {
				$val += $e['Expenditure'];
			}
		}
		echo "{ x: new Date( " . php_dt_to_js_datestr($m->start_dt, 'Y') . " ), y: $val },";
	}
	
?>
				
						]
					},
					{
						type: "line",
						name: "Non-Luxury",
						showInLegend: true,
						yValueFormatString: "$#,##0",
						color: 'grey',
						lineDashType: 'solid',
						markerType: 'triangle',
						dataPoints: [
							
<?php
	
	foreach ($months as $m) {
		$val = 0;
		foreach($m->expenditures as $e) {
			if ( ! in_array($e['type'], LUXURY_EXPENDITURES ) ) {
				$val += $e['Expenditure'];
			}
		}
		echo "{ x: new Date( " . php_dt_to_js_datestr($m->start_dt, 'Y') . " ), y: $val },";
	}
	
?>
				
						]
					},
					
<?php
	foreach ($expenditure_types as $et) {
		echo "{
					type: 'line',
					name: '$et',
					showInLegend: true,
					yValueFormatString: '$#,##0',
					dataPoints: [ ";
		foreach ( $months as $m ) {
			$expenditure_category_count = count( $m->expenditures );
			$non_zero = false;
			for ($i = 0; $i < $expenditure_category_count; $i++) {
				if ($m->expenditures[$i]['type'] == $et) {
					$non_zero = true;
					echo " { x: new Date(" . php_dt_to_js_datestr( $m->start_dt ) . "), y: " . round( $m->expenditures[$i]['Expenditure'], 2) . " }, ";
				}
				else if ( (! $non_zero) && ($i + 1) == $expenditure_category_count) {
					echo " { x: new Date(" . php_dt_to_js_datestr( $m->start_dt ) . "), y: 0 },";
				}
			}
		}
		echo " ] }, ";
	}
?>
					]
				});
				monthlyExpenditureChart.render();

				$('button#toggle-luxury-expenses').on('click', function() {
					data = monthlyExpenditureChart.options.data;
					data.forEach(function(item, index) {
						if ( item.name != 'Luxury' && ! luxuryExpenses.includes(item.name) ) {
							item.visible = false;
						}
						else {
							item.visible = true;
						}
					});
					monthlyExpenditureChart.render();
				});
				$('button#toggle-non-luxury-expenses').on('click', function() {
					data = monthlyExpenditureChart.options.data;
					data.forEach(function(item, index) {
						if ( luxuryExpenses.includes(item.name) || item.name == 'Luxury' || item.name == 'Net' || item.name == 'Target' ) {
							item.visible = false;
						}
						else {
							item.visible = true;
						}
					});
					monthlyExpenditureChart.render();
				});
				$('button#toggle-all-expenses').on('click', function() {
					data = monthlyExpenditureChart.options.data;
					data.forEach(function(item, index) {
						item.visible = true;
					});
					monthlyExpenditureChart.render();
				});

			</script>

			<section class='records' style='width: 100%; text-align: center; flex-flow: row wrap; justify-content: space-around;'>
<?php
	foreach($records as $r) {
		echo "<div class='record'>";
		echo "	<h2>$r->name</h2>";
		echo "	<h3 style='font-weight: 900;'>$r->prefix"."$r->value"."$r->suffix</h3>";
		echo "	<h4>$r->datestr</h4>";
		echo "</div>";
	}
?>
				<!--
				<div class='record'>
					<h2>Highest (non-bonus) Income Day</h2>
					<h3>$<?php // echo $highest_income_day->income; ?></h3>
					<h4>(<?php // echo $highest_income_day->datestr; ?>)</h4>
				</div>
				-->
			</section>
		
		</section>

<?php
	}
?>