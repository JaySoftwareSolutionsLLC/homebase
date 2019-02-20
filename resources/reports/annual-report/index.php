<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
$year = set_post_value('year') ?? date('Y');
if ( $year == '2018' ) {
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2018.php');
}
else {
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');
}

// Connect to Database
$conn = connect_to_db();

// Initialize variables
$title = "AR - $year";
$date_start_dt = ($year == '2018') ? new DateTime("June 01 2018") : $date_start_dt = new DateTime("first day of January $year");
$date_end_dt = new DateTime("last day of december $year");
if (new DateTime() < $date_end_dt) {
	//var_dump( $date_end_dt );
	//echo "triggered";
	$date_end_dt = new DateTime();
	//var_dump( $date_end_dt );
}
$date_start = date_format($date_start_dt, 'Y-m-d');
$date_end = date_format($date_end_dt, 'Y-m-d');
$count_days = ( strtotime($date_end . "+1 days") - strtotime($date_start) ) / SEC_IN_DAY;
$weeks = array(); // array to house week objects
$days = array(); // array to house day objects

// House all records as its own object
$highest_income_day = new stdClass();
$highest_income_day->income = 0;
$highest_income_day->datestr = '';

$generated = true;


if ($generated) {
	// TEST PASSED echo "$date_start - $date_end <br/>";
	
	$date_start_to_check_dt = clone $date_start_dt;
	$date_end_to_check_dt = clone $date_start_to_check_dt;
	$date_end_to_check_dt->modify('next Sunday');
	// TEST PASSED echo date_format($date_to_check, 'Y/m/d');
	//var_dump($date_start_to_check_dt);
	//echo "<br/>";
	//var_dump($date_end_dt);
	//echo "<br/>";
	$fuse_length = 55;
	$fuse = 0;
	while ($date_start_to_check_dt <= $date_end_dt) {
		$week = new stdClass();
		$week->start_dt = clone $date_start_to_check_dt;
		if ($date_end_to_check_dt > $date_end_dt ) {
			$date_end_to_check_dt = $date_end_dt;
		}
		$week->end_dt = clone $date_end_to_check_dt;
		$week->start_day = date_format($week->start_dt, 'Y-m-d');
		$week->end_day = date_format($week->end_dt, 'Y-m-d');
		$date_start_to_check_dt->modify('next Monday'); // Increment Start Date to next Monday
		$date_end_to_check_dt->modify('next Sunday'); // Increment End Date to next Sunday
		$weeks[] = $week; // Push week object into weeks array
		$fuse++;
		//echo "$fuse<br/>";
		if ($fuse > $fuse_length) {
			echo "FUSE BROKEN";
			break;
		}
	}
	//var_dump($weeks);
	foreach ($weeks as $w) {
		// For each week create properties for all important info
		$q = "SELECT 
				SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) 
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
	}
	
	$fuse_length = 367;
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
		$q = "SELECT 
				SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) 
				FROM finance_seal_shifts 
				WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->hours_seal = round($row[0], 2);

		// RICKS HOURS
		$q = "SELECT 
				SUM(hours) 
				FROM `finance_ricks_shifts` 
				WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->hours_ricks = round($row[0], 2);
		// RICKS HOURS
		$q = "SELECT 
				SUM(hours) 
				FROM `finance_ricks_shifts` 
				WHERE date = '$d->datestr'
				AND type = 'otb' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->hours_otb_ricks = round($row[0], 2);
		

		// SEAL INCOME
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
		
			// SEAL BONUSES
		$q = " 	SELECT SUM(amount) AS 'bonus value'
				FROM `finance_seal_income`
				WHERE 	date = '$d->datestr'
					AND type = 'bonus' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_array($res);
		$d->bonuses_seal = $row['bonus value'] ?? 0;

		$d->income_seal = $d->bonuses_seal + $d->salary_seal;


		// RICKS TIPS
		$q = "SELECT 
				SUM(tips) 
				FROM finance_ricks_shifts 
				WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->tips_ricks = round($row[0], 2);

		$d->income_ricks = round(($d->tips_ricks + (HOURLY_WAGE_RICKS * ($d->hours_ricks - $d->hours_otb_ricks))), 2);


		// SEAL HOURLY
		$d->hourly_seal = round(($d->income_seal / $d->hours_seal), 2);


		// RICKS HOURLY
		$d->hourly_ricks = round(($d->income_ricks / $d->hours_ricks), 2);


		// EXPENDITURES
		$q = "SELECT SUM(amount) FROM finance_expenses WHERE date = '$d->datestr' ";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$d->expenditure_net = round($row[0], 2);


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
		
		if ( ( $d->income_net - $d->bonuses_seal ) > $highest_income_day->income ) {
			$highest_income_day->income = ( $d->income_net - $d->bonuses_seal );
			$highest_income_day->datestr = $d->datestr;
		}
		//echo $d->income_seal;
	}

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

</head>


<?php 

include $_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/sections/header.php'; // Website header
include $_SERVER["DOCUMENT_ROOT"] . "/homebase/resources/reports/annual-report/report-options.php"; // Report header

?>


<?php 
	if ($generated) {
?>
		<section class='generated-report'>
			<h1>Annual Report</h1>
			<div id='income-flow-chart-container' style='height: 300px; width: 100%;'></div>
			<script>			
//				window.onload = function () {

				var chart = new CanvasJS.Chart("income-flow-chart-container", {
					animationEnabled: true,
					title: {
						text: "2018 Income Flow"
					},
					axisX: {
						valueFormatString: "DDD DD MMM YYYY",
						interval: 15,
						intervalType: 'day',
					},
					axisY: {
						title: '$',
						prefix: "$",
						//labelFormatter: addSymbols,
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->salary_seal },";
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->income_ricks },";
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->bonuses_seal },";
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->expenditure_net },";
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->income_diff },";
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->hours_net },";
	}
?>
						]
					}
					
					]
				});
				chart.render();

				function addSymbols(e) {
					var suffixes = ["", "K", "M", "B"];
					var order = Math.max(Math.floor(Math.log(e.value) / Math.log(1000)), 0);

					if(order > suffixes.length - 1)                	
						order = suffixes.length - 1;

					var suffix = suffixes[order];      
					return CanvasJS.formatNumber(e.value / Math.pow(1000, order)) + suffix;
				}

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
			//	window.onload = function () {

				var hoursChart = new CanvasJS.Chart("hour-chart-container", {
					animationEnabled: true,
					title: {
						text: "2018 Hours"
					},
					axisX: {
						valueFormatString: "DDD DD MMM YYYY",
						interval: 15,
						intervalType: 'day',
					},
					axisY: {
						title: '',
						prefix: '',
						//labelFormatter: addSymbols,
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
						{
						type: "line",
						name: "Hours",
						showInLegend: true,
						yValueFormatString: "",
						color: 'black',
						dataPoints: [
<?php
	foreach ($weeks as $w) {
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->hours_net },";
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
		$month_str = ( date_format($w->start_dt, 'm') - 1 );
		if ($month_str == 0) {
			$month_str = '00';
		}
		echo "{ x: new Date( " . date_format($w->start_dt, 'Y') . ", $month_str," .  date_format($w->start_dt, 'd') . "), y: $w->hourly_net },";
	}
?>
							]
						},	
					]
				});
				hoursChart.render();

//				}
			</script>
			<section class='records'>
				<div class='record'>
					<h2>Highest (non-bonus) Income Day</h2>
					<h3>$<?php echo $highest_income_day->income; ?></h3>
					<h4>(<?php echo $highest_income_day->datestr; ?>)</h4>
				</div>
			</section>
		
		</section>

<?php
	}
?>