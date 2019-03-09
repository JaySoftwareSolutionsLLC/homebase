<?php
	if ( $opportunity_surplus > 2000 ) { // If opportunity surplus is greater than $2,000 then set color to green
		$opportunity_surplus_font_color = 'hsl(120, 100%, 50%)'; 
	}
	else if ( $opportunity_surplus > 0 ) { // If opportunity surplus is positive then set color to white
		$opportunity_surplus_font_color = 'hsl(0, 100%, 100%)'; 
	}
	else { // Otherwise the surplus must be negative so set the color to red
		$opportunity_surplus_font_color = 'hsl(0, 100%, 50%)'; 
	}
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
			<h5>(Est Take Home: $<?php echo $unreceived_after_tax_seal_income; ?>)</h5>
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
<?php if ($year != '2018') { ?>
		<div class="stat opportunity-surplus">
			<h3>Opportunity Surplus</h3>
			<h4 style='color: <?php echo $opportunity_surplus_font_color ?>;'>$<?php echo $opportunity_surplus; ?></h4>
		</div>
<?php } ?>
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
			<div id='account-allocation-graph' style='height: 10rem; width: 15rem;'></div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>
<script>
var chart = new CanvasJS.Chart("account-allocation-graph", {
	backgroundColor: 'black',
	animationEnabled: true,
	data: [{
		type: "doughnut",
		startAngle: 270,
      	innerRadius: 5,
		radius: 100,
		toolTipContent: "<b>{name}</b>: ${y}",
		dataPoints: [
<?php
	arsort($account_types); // Associative row sort (desc)
	foreach ($account_types as $name=>$val) {
		$str = "{ y: $val, name: '$name' ";
		switch ( $name ) {
			case 'liability' :
				$str .= " , color: 'hsl(0, 100%, 50%)' ";
				break;
			case 'liquid cash' :
				$str .= " , color: 'hsl(210, 100%, 50%)' ";
				break;
			case 'retirement account' :
				$str .= " , color: 'hsl(120, 100%, 50%)' ";
				break;
			case 'taxable account' :
				$str .= " , color: 'hsl(150, 100%, 50%)' ";
				break;
			case 'unreceived ATI' :
				$str .= " , color: 'hsl(210, 0%, 50%)' ";
				break;
			case 'depreciating asset' :
				$str .= " , color: 'hsl(50, 100%, 50%)' ";
				break;
		}
		$str .= " }, ";
		echo $str;
	}
?>
		]
	}]
});
chart.render();
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

