<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
if ( strtotime( set_post_value('start-date') ) < strtotime('2019-01-01') ) {
	$report_year = '2018';
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2018.php');
}
else {
	$report_year = '2019';
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');
}


// Connect to Database
$conn = connect_to_db();

// Initialize variables
$title = "WR - $report_year";
$date_start = set_post_value('start-date');
$date_end = set_post_value('end-date');
$count_days = ( strtotime($date_end . "+1 days") - strtotime($date_start) ) / SEC_IN_DAY;
$generated = ($date_start != '' && $date_end != '') ? true : false;

$color_seal = COLOR_SEAL_AND_DESIGN;
$color_ricks = COLOR_RICKS_ON_MAIN;

if ($generated) {
	// SEAL HOURS
	$q = "SELECT 
			SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) 
			FROM finance_seal_shifts 
			WHERE date >= '$date_start' 
				AND date <= '$date_end'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$hours_seal = round($row[0], 2);

	
	// RICKS HOURS
	$q = "SELECT 
			SUM(hours) 
			FROM `finance_ricks_shifts` 
			WHERE date >= '$date_start' 
				AND date <= '$date_end'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$hours_ricks = round($row[0], 2);
	// RICKS OTB HOURS
	$q = "SELECT 
			SUM(hours) 
			FROM `finance_ricks_shifts` 
			WHERE date >= '$date_start' 
				AND date <= '$date_end'
				AND type = 'otb' ";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$hours_otb_ricks = round($row[0], 2);

	
	// SEAL INCOME
	$income_seal = 0;
	$salary_seal = 0; // Calculated based on hourly wage during that time frame and subtracting out unpaid time off
	$bonuses_seal = 0; // Calculated based on the date of the bonus on finance_seal_income table
	$day_to_check = $date_start;
	$fuse = 0;
	while ($day_to_check <= $date_end) {
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
			$salary_seal += ($correct_hourly * 8);
		}
		$day_to_check = date('Y-m-d', strtotime($day_to_check.'+1day'));
		// echo "DOW: $this_dow | INCOME: salary_seal | DAYTOCHECK: $day_to_check <br/>";
		$fuse++;
		if ($fuse >= 10) {
			echo "FUSE BLOWN";
			exit;
		}
	}
	
		// SEAL BONUSES
	$q = " 	SELECT SUM(amount) AS 'bonus value'
			FROM `finance_seal_income`
			WHERE 	date >= '$date_start'
				AND date <= '$date_end'
				AND type = 'bonus' ";
	$res = $conn->query($q);
	$row = mysqli_fetch_array($res);
	$bonuses_seal = $row['bonus value'];
	
	$income_seal = $bonuses_seal + $salary_seal;
	
	
	// RICKS TIPS
	$q = "SELECT 
			SUM(tips) 
			FROM finance_ricks_shifts 
			WHERE date >= '$date_start'
				AND date <='$date_end'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$tips_ricks = round($row[0], 2);

	$income_ricks = round( ( $tips_ricks + ( HOURLY_WAGE_RICKS * ( $hours_ricks - $hours_otb_ricks ) ) ), 2);

	
	// SEAL HOURLY
	$hourly_seal = round(($income_seal / $hours_seal), 2);

	
	// RICKS HOURLY
	$hourly_ricks = round(($income_ricks / $hours_ricks), 2);

	
	// EXPENDITURES
	$q = "SELECT SUM(amount) FROM finance_expenses WHERE date >= '$date_start' AND date <= '$date_end'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$expenditure_net = round($row[0], 2);
	

	$expenses = array(); // Not currently used due to way chart.js works, needed to make two arrays instead of one associative array
	$expense_names = array();
	$expense_values = array();
	$q = "SELECT 
			SUM(amount), type FROM `finance_expenses` 
			WHERE date >= '$date_start' 
				AND date <= '$date_end' 
			GROUP BY type 
			ORDER BY SUM(amount) DESC";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$expenses[$row['type']] = round($row['SUM(amount)'], 2);
			$expense_names[] = $row['type'];
			$expense_values[] = round($row['SUM(amount)'], 2);
		}
	} else {
		echo "0 results";
	}


	// NET HOURS
	$hours_net = round(($hours_seal + $hours_ricks), 2);
	// NET INCOME
	$income_net = round(($income_seal + $income_ricks), 2);
	// AVERAGE DAILY INCOME
	$adi = round(($income_net / $count_days), 2);
	// AVERAGE DAILY EXPENDITURE
	$ade = round(($expenditure_net / $count_days), 2);
	// NET HOURLY
	$hourly_net = round(($income_net / $hours_net), 2);
	// NET INCOME DIFFERENTIAL
	$income_diff = round(($income_net - $expenditure_net), 2);
}

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

</head>

<?php 


include $_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/sections/header.php'; // Website header
include $_SERVER["DOCUMENT_ROOT"] . "/homebase/resources/reports/weekly-report/report-options.php"; // Report header

// var_dump($_POST);

?>

<?php 
	if ($generated) {
	//echo implode(',' , $expense_names);
	//echo implode(',' , $expense_values);
	$number_of_expense_categories = count($expense_names);
?>
		
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js" integrity="sha256-XF29CBwU1MWLaGEnsELogU6Y6rcc5nCkhhx89nFMIDQ=" crossorigin="anonymous"></script>
<section class='generated-report'>
			<h1>Weekly Report</h1>
			<h2><?php echo "($date_start - $date_end)" ?></h2>
			<div class='stats financial'>
				<div class='stat income'>
					<div class='stat-text'>
						<h3>Income</h3>
						<h4><?php echo $income_net; ?></h4>
						<h5>Target: <?php echo WEEKLY_INCOME_TARGET; ?></h5>
						<h5>ADI: <?php echo $adi; ?></h5>
					</div>
					<canvas id='net-income-graph'></canvas>
					<script>
						new Chart(document.getElementById("net-income-graph"),{
							"type":"pie",
							"data": {
								"labels":["S&D", "Ricks"],
								"datasets":[
									{"label":"Weekly Income",
									 "data":[<?php echo "$income_seal,$income_ricks" ?>],
									 "backgroundColor":[<?php echo "'" . COLOR_SEAL_AND_DESIGN . "','$color_ricks'" ?>],
									 "borderColor":["white", "white"],
									 "borderWidth":[0,0]
									}
								]
							},
							options: {
								legend: {
									labels: {
										fontColor: 'black',
										boxWidth: 15,
										fontFamily: "'Orbitron', sans-serif"
									}
								}
							}
						});
					</script>
				</div>
				<div class='stat expenditure'>
					<div class='stat-text'>
						<h3>Expenditure</h3>
						<h4><?php echo $expenditure_net; ?></h4>
						<h5>Target: <?php echo WEEKLY_EXPENDITURE_TARGET; ?></h5>
						<h5>ADE: <?php echo $ade; ?></h5>
					</div>
					<canvas id='expenditure-breakdown-graph'></canvas>
					<script>
						new Chart(document.getElementById("expenditure-breakdown-graph"),{
							"type":"pie",
							"data": {
								"labels":["<?php echo implode('","' , $expense_names) ?>"],
								"datasets":[
									{"label":"Expense Breakdown",
									 "data":["<?php echo implode('","', $expense_values) ?>"],
									 "borderWidth":[<?php 
															$str = '';
															for ($i = 0; $i < $number_of_expense_categories; $i++) {
																$str .= "0";
																$str .= ",";
															}
															echo substr($str, 0, -1);
													?>],
									 "backgroundColor":[<?php 
															$str = '';
															for ($i = 0; $i < $number_of_expense_categories; $i++) {
																$hue = $i * 30;
																$lightness = ($i * 5) + 50;
																if ($hue > 360) {
																	$hue -= 360;
																}
																if ($lightness > 100) {
																	$lightness -= 100;
																}
																$str .= "'hsl($hue, 100%, $lightness%)'";
																$str .= ",";
															}
															echo substr($str, 0, -1);
													?>]
									}
								]
							},
							options: {
								legend: {
									display: false
								}
							}
						});
					</script>
				</div>
				<div class='stat income-differential'>
					<div class='stat-text'>
						<h3>Income Differential</h3>
						<h4><?php echo $income_diff; ?></h4>
						<h5>Target: <?php echo WEEKLY_INCOME_DIFF_TARGET; ?></h5>
					</div>
					<canvas id='net-income-diff-graph'></canvas>
					<script>
						new Chart(document.getElementById("net-income-diff-graph"),{
							"type":"bar",
							"data": {
								"labels":["Income", "Expenditure"],
								"datasets":[
									{"data":[<?php echo "$income_net,$expenditure_net" ?>],
									 "backgroundColor":["hsl(120, 100%, 50%)", "hsl(0, 100%, 50%)"],
									 "borderColor":["white", "white"],
									 "borderWidth":[0,0]
									}
								]
							},
							options: {
								legend: {
									display: false
								},
								scales: {
									yAxes: [{
										ticks: {
											beginAtZero: true
										}
									}]
								}
							}							
						});
					</script>
				</div>
				<div class='stat hours'>
					<div class='stat-text'>
						<h3>Hours</h3>
						<h4><?php echo $hours_net; ?></h4>
						<h5>Target: <?php echo WEEKLY_HOURS_TARGET; ?></h5>
					</div>
					<canvas id='net-hours-graph'></canvas>
					<script>
						new Chart(document.getElementById("net-hours-graph"),{
							"type":"pie",
							"data": {
								"labels":["S&D", "Ricks"],
								"datasets":[
									{"label":"Hours",
									 "data":[<?php echo "$hours_seal,$hours_ricks" ?>],
									 "backgroundColor":[<?php echo "'" . COLOR_SEAL_AND_DESIGN . "','$color_ricks'" ?>],
									 "borderColor":["white", "white"],
									 "borderWidth":[0,0]
									}
								]
							},
							options: {
								legend: {
									labels: {
										fontColor: 'black',
										boxWidth: 15,
										fontFamily: "'Orbitron', sans-serif"
									}
								}
							}
						});
					</script>
				</div>
				<div class='stat hourly'>
					<div class='stat-text'>
						<h3>Hourly Wage</h3>
						<h4><?php echo $hourly_net; ?></h4>
						<h5>Target: <?php echo HOURLY_WAGE_TARGET; ?></h5>
					</div>
					<canvas id='net-hourly-graph'></canvas>
					<script>
						new Chart(document.getElementById("net-hourly-graph"),{
							"type":"horizontalBar",
							"data": {
								"labels":["S&D", "Ricks"],
								"datasets":[
									{"data":[<?php echo "$hourly_seal,$hourly_ricks" ?>],
									 "backgroundColor":[<?php echo "'" . COLOR_SEAL_AND_DESIGN . "','$color_ricks'" ?>],
									 "borderColor":["white", "white"],
									 "borderWidth":[0,0]
									}
								]
							},
							options: {
								legend: {
									display: false
								},
								scales: {
									xAxes: [{
										ticks: {
											beginAtZero: true
										}
									}]
								}
							}							
						});
					</script>
				</div>
			</div>
			<div class='stats'>
				<div class='stat dev-hours'>
					<div class='stat-text'>
						<h3>Dev Hours</h3>
						<h4><?= return_dev_hours($conn, $date_start, $date_end, 2) ?></h4>
						<h5>Target: <?php echo '10 hours'; ?></h5>
					</div>
				</div>
			</div>
		</section>
<?php } ?>
	<script>
		/*
		function generate_arc() { 
			var arc = d3.svg.arc().innerRadius(100).outerRadius(140).startAngle(0).endAngle(190 * (Math.PI)/180); 
			svg.append("path").attr('d', arc); 
		};
		generate_arc();
		*/

		$('button.previous-week').on('click', function() {
			let dateStart = $('input#start-date').val();
			let dateEnd = $('input#end-date').val();
			$('input#start-date').val(moment(dateStart).subtract(7, 'days').format('YYYY-MM-DD'));
			$('input#end-date').val(moment(dateStart).subtract(7, 'days').endOf('week').add(1, 'days').format('YYYY-MM-DD'));
		});
		$('button.next-week').on('click', function() {
			let dateStart = $('input#start-date').val();
			let dateEnd = $('input#end-date').val();
			$('input#start-date').val(moment(dateStart).add(7, 'days').startOf('week').add(1, 'days').format('YYYY-MM-DD'));
			$('input#end-date').val(moment(dateStart).add(7, 'days').endOf('week').add(1, 'days').format('YYYY-MM-DD'));
		});
	</script>
