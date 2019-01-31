<?php

?>
<section class="column finance">
	<h2>Finances</h2>
	<div class="content">
		<div class="stat net-worth">
			<h3>Current Net Worth</h3>
			<h4>$<?php echo $current_net_worth; ?></h4>
			<h5><?php echo $oldest_date; ?></h5>
		</div>
		<div class="stat unreceived-seal-income">
			<h3>Unreceived Seal Income</h3>
			<h4>$<?php echo $unreceived_seal_income; ?></h4>
			<h5>(Est Take Home: $<?php echo (ESTIMATED_AFTER_TAX_PERCENTAGE * $unreceived_seal_income / 100); ?>)</h5>
		</div>
		<div class="row">
			<div class="small stat adi">
				<h3>ADI</h3>
				<h4>$<?php echo $adi?>/day</h4>
				<h5><?php echo AVG_DAILY_INCOME_TARGET; ?></h5>
			</div>
			<div class="small stat ade">
				<h3>ADE</h3>
				<h4>$<?php echo $ade?>/day</h4>
				<h5><?php echo AVG_DAILY_EXPENDITURE_TARGET; ?></h5>
			</div>
		</div>
		<div class="row">
			<div class="small stat awh">
				<h3>AWH</h3>
				<h4><?php echo $awh?>hrs/wk</h4>
				<h5><?php echo WEEKLY_HOURS_TARGET; ?></h5>
			</div>
			<div class="small stat ahw">
				<h3>AHW</h3>
				<h4>$<?php echo $ahw?>/hr</h4>
				<h5><?php echo HOURLY_WAGE_TARGET; ?></h5>
			</div>
		</div>
		<div class="stat income-projection-2018">
			<h3><?php echo $year; ?> Income Projection</h3>
			<h4>$<?php echo $estimated_2018_income; ?></h4>
		</div>
<?php 
	if ($year == '2018') {
?>
		<div class="stat income-projection-2019">
			<h3>2019 Income Projection</h3>
			<h4>$<?php echo $estimated_2019_income; ?></h4>
		</div>
<?php
	}
?>
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
			</a>
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

