<?php
// Time related variables

	$start_date = date('Y/m/d', strtotime($START_DATE_STRING_FINANCIAL));
	$start_time = strtotime($start_date);
	$today_time = time();
	$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";
	$sec_in_day = (60 * 60 * 24);
	$days_active = ceil(($today_time - $start_time) / ($sec_in_day));
	$days_left_in_year = (365 - (date('z') + 1));

// Income related variables



// Perform all queries to pull relevant data from DB and put in PHP variables

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
	$q = "SELECT SUM(amount) FROM finance_expenses WHERE date >= '$start_date'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_expenditure = $row[0];

	// Determine total hours worked since start date

	// SEAL HOURS
	$q = "SELECT SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) FROM finance_seal_shifts WHERE date >= '$start_date'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_seal_hours = $row[0];

	// RICKS HOURS
	$q = "SELECT SUM(hours) FROM `finance_ricks_shifts` WHERE date >= '$start_date'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_hours = $row[0];
	
	// NET HOURS
	$net_hours = $net_seal_hours + $net_ricks_hours;


	// Determine total income since start date

	// RICKS INCOME
	$q = "SELECT SUM(tips) FROM finance_ricks_shifts WHERE date >= '$start_date'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_tips = $row[0];

	// SEAL INCOME
		// Seal income actually received
	$q = "SELECT SUM(amount) FROM finance_seal_income WHERE date >= '$start_date'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_seal_income = $row[0];
		// Seal income earned but not yet received
	$q = "SELECT MAX(date) FROM finance_seal_income WHERE date >= '$start_date' AND type = 'check'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$last_seal_check_date = $row[0]; // The most recent check date
	$last_seal_check_time = strtotime($last_seal_check_date);
	$four_pm_seconds = (60 * 60 * 16);
	$unreceived_seal_income = 0;
	$fuse = 0;
	$this_time_to_check = $last_seal_check_time + $sec_in_day + $four_pm_seconds;
	while ($this_time_to_check < $today_time) {
		$this_dow = date('D',$this_time_to_check);
		$this_time_to_check += $sec_in_day;
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
	$net_income = (7.5 * $net_ricks_hours) + $net_ricks_tips + $net_seal_income + $unreceived_seal_income;

	$adi = number_format($net_income / $days_active, 2);
	$ade = number_format($net_expenditure / $days_active, 2);
	$awh = number_format(7 * $net_hours / $days_active, 2);
	$ahw = number_format(7 * $adi / $awh, 2);

	$current_net_worth = $current_assets + $current_cash - $current_liabilities;

	$estimated_2018_income = number_format($PRE_JUNE_RICKS_INCOME + ($adi  * ($days_left_in_year + $days_active)), 0);

	$estimated_EOY_net_worth = number_format($current_net_worth + ((($adi * ($ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - $ade) * ($days_left_in_year)), 0);

?>
<section class="column finance">
	<h2>Finances</h2>
	<div class="content">
		<div class="stat net-worth">
			<h3>Current Net Worth</h3>
			<h4>$<?php echo $current_net_worth; ?></h4>
			<h5><?php echo $oldest_date; ?></h5>
		</div>
		<div class="row">
			<div class="small stat adi">
				<h3>ADI</h3>
				<h4>$<?php echo $adi?>/day</h4>
				<h5><?php echo $AVG_DAILY_INCOME_TARGET; ?></h5>
			</div>
			<div class="small stat ade">
				<h3>ADE</h3>
				<h4>$<?php echo $ade?>/day</h4>
				<h5><?php echo $AVG_DAILY_EXPENDITURE_TARGET; ?></h5>
			</div>
		</div>
		<div class="row">
			<div class="small stat awh">
				<h3>AWH</h3>
				<h4><?php echo $awh?>hrs/wk</h4>
				<h5><?php echo $WEEKLY_HOURS_TARGET; ?></h5>
			</div>
			<div class="small stat ahw">
				<h3>AHW</h3>
				<h4>$<?php echo $ahw?>/hr</h4>
				<h5><?php echo $HOURLY_WAGE_TARGET; ?></h5>
			</div>
		</div>
		<div class="stat income-projection-2018">
			<h3>2018 Income Projection</h3>
			<h4>$<?php echo $estimated_2018_income; ?></h4>
		</div>
		<div class="stat proj-net-worth">
			<h3>Projected EOY Net Worth</h3>
			<h4>$<?php echo $estimated_EOY_net_worth; ?></h4>
		</div>
		<div class="stat graphic account-allocation">
			<h3>Accounts Allocation</h3>
			<canvas id='account-allocation-graph'></canvas>
		</div>
		
		
		<div class="row">
			<a href='resources/forms/expense.php' class="form expense" target='_blank'>
				<img src='resources/assets/images/expense.png'/>
				<figcaption>Expense</figcaption>
			</a>
			<a href='resources/forms/accounts.php' class="form accounts" target="_blank">
				<img src='resources/assets/images/accounts.png'/>
				<figcaption>Accounts</figcaption>
			</a>
		</div>
		<div class="row">
			<a href='resources/forms/seal_shift.php' class="form seal-shift" target='_blank'>
				<img src='resources/assets/images/seal-shift.png'/>
				<figcaption>S&D Shift</figcaption>
			</a>
			<a href='resources/forms/ricks_shift.php' class="form ricks-shift" target="_blank">
				<img src='resources/assets/images/ricks-shift.png'/>
				<figcaption>Ricks Shift</figcaption>
			</a>
			<a href='resources/forms/seal_income.php' class="form seal-income" target="_blank">
				<img src='resources/assets/images/seal-income.png'/>
				<figcaption>S&D Income</figcaption>
			<a>
		</div>
	</div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js" integrity="sha256-XF29CBwU1MWLaGEnsELogU6Y6rcc5nCkhhx89nFMIDQ=" crossorigin="anonymous"></script>
<script>
	new Chart(document.getElementById("account-allocation-graph"),{
		"type":"doughnut",
		"data": {
			"labels":["$","Asts.","Lbls."],
			"datasets":[
				{"label":"Asset Allocation",
				 "data":[<?php echo "$current_cash, $current_assets, $current_liabilities" ?>],
				 "backgroundColor":["hsl(120, 100%, 50%)", "hsl(200, 100%, 50%)", "hsl(0, 100%, 50%)"],
				 "borderColor":["black", "black", "black"],
				 "borderWidth":[1,1,1]
				}
			]
		},
		options: {
			legend: {
				labels: {
					fontColor: 'white',
					boxWidth: 15,
					fontFamily: "'Orbitron', sans-serif"
				}
			}
		}
	});
</script>
<script>
	var activeDays = "<?php echo $days_active ?>";
		
	var currCash = "<?php echo $current_cash ?>";
	var currAssets = "<?php echo $current_assets ?>";
	var currLiabilities = "<?php echo $current_liabilities ?>";
	var netWorthDate = "<?php echo $oldest_date ?>";
		
	var netExp = "<?php echo $net_expenditure ?>"
	var netHrs = "<?php echo $net_hours ?>";
	var netInc = "<?php echo $net_income ?>";
</script>

