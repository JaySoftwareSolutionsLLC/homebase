<?php
	$birthdate = return_date_from_str('April 28th 1994', 'datetime');
	$birthdate_age_sixty = clone $birthdate;
	$birthdate_age_sixty->modify('+60 years');
	$today_dt = return_date_from_str('today', 'datetime');
	$interval_until_60 = date_diff($today_dt, $birthdate_age_sixty, false);
	$days_until_60 = $interval_until_60->days;
	$years_until_60 = round( $days_until_60 / 365.25 , 2 );

	$theoretical_age_60_net_worth = 0;
	$theoretical_age_60_annual_withdrawal_rate = 0;
	foreach ($accounts as $a) { // For each account determine the estimated value at age 60
		$curr_principle = $a->mrv;

		$age_60_val = ( $curr_principle * pow( (1 + ($a->exp_roi / 100)) , $years_until_60 ) );
		if ($age_60_val < 0) {
			$age_60_val = 0;
		}
		$theoretical_age_60_net_worth += $age_60_val;
		if ($a->type == 'ROTH') { // If the account is a ROTH then no taxes or capital gains need to be paid
			$theoretical_age_60_annual_withdrawal_rate += round( $age_60_val * 0.04 , 2 ); // Assuming a 4% withdraw rate
		}
		elseif ($a->type == 'traditional 401k') { // If the account is a traditional 401k then taxes need to be paid
			$theoretical_age_60_annual_withdrawal_rate += round( ($age_60_val * 0.04) * 0.7 , 2 ); // Assuming a 30% Tax Rate
		}
		elseif ($a->type == 'taxable account') { // If the account 
			$percent_growth = ($age_60_val - $curr_principle) / $age_60_val;
			$theoretical_age_60_annual_withdrawal_rate += round( ($age_60_val * 0.04) - ( ($age_60_val * 0.04) * $percent_growth * 0.15) , 2); // Assuming a 15% Capital Gains Rate
		}
		// TEST PASSED 2019.03.12 echo "$a->name: $age_60_val | $theoretical_age_60_net_worth | $theoretical_age_60_annual_withdrawal_rate <br/>";
	}

	if ( $opportunity_surplus > 2500 ) { // If opportunity surplus is greater than $2,000 then set color to green
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
			<h4>$<?php echo number_format($current_net_worth); ?></h4>
<?php 	if ($year == '2018') { ?>
			<h5><?php echo $oldest_date; ?></h5>
<?php	} ?>
		</div>
<?php 	if ($year == '2018') { ?>
		<div class="stat unreceived-seal-income">
			<h3>Unreceived Seal Income</h3>
			<h4>$<?php echo number_format($unreceived_seal_income, 2); ?></h4>
			<h5>(Est Take Home: $<?php echo number_format($unreceived_after_tax_seal_income, 2); ?>)</h5>
		</div>
<?php	} ?>
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
		<div class="small stat theoretical-age-60-ADW">
			<h3>Age 60 ADW</h3>
			<h4>$<?php echo number_format($theoretical_age_60_annual_withdrawal_rate / 365.25 , 2); ?></h4>
		</div>
<?php if ($year != '2018') { ?>
		<div class="stat opportunity-surplus">
			<h3>Opportunity Surplus</h3>
			<h4 style='color: <?php echo $opportunity_surplus_font_color ?>;'>$<?php echo number_format($opportunity_surplus); ?></h4>
		</div>
<?php } ?>
<?php 
	if ($year == '2018') {
		?>
		<div class="stat income-projection-2018">
			<h3><?php echo $year; ?> Income Projection</h3>
			<h4>$<?php echo $estimated_2018_income; ?></h4>
		</div>
		<div class="stat income-projection-2019">
			<h3>2019 Income Projection</h3>
			<h4>$<?php echo $estimated_2019_income; ?></h4>
		</div>
<?php
	}

	$theoretical_age_60_ADW_info = return_finance_stat_info_html('Theoretical Age 60 ADW', 'Determine Average Daily Withdrawal from Investments at age 60', '', array('exp_roi is hit each year for each account'), 'Only considers ROTH and taxable account types.');
	echo return_finance_stat_html( 'Age 60 ADW', number_format( $theoretical_age_60_annual_withdrawal_rate / 365.25 , 2 ), '', 'small', $theoretical_age_60_ADW_info );

	$theoretical_eoy_nw_info = return_finance_stat_info_html('Theoretical EOY Net Worth', 'Determine Net Worth on last day of year if I earn expected ROI on investments and earn my theoretical income amount.', 'appreciated account values + (theoretical future income * take home %) - (Target ADE * days left in year)');
	echo return_finance_stat_html('Theoretical EOY Net Worth', number_format($theoretical_EOY_net_worth), '', '', $theoretical_eoy_nw_info);

	$theoretical_income_info = return_finance_stat_info_html('Theoretical Income', 'Determine 2019 pre-tax income', 'Current Income + (Cashable PTO Hours * Current Hourly Wage) + ((Avg Weekly Ricks Income + Avg Weekly Seal Income) * Weeks left in year )', array('Work all regular shifts', 'Cashout all PTO', 'No raises', 'No bonuses'), 'Accounts for seasonality');
	echo return_finance_stat_html('Theoretical Income', number_format($theoretical_income_this_year), '', '', $theoretical_income_info);
?>
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
				$str .= " , color: 'hsl(0, 100%, 50%)', /*exploded: true,*/ ";
				break;
			case 'liquid cash' :
				$str .= " , color: 'hsl(120, 100%, 50%)', /*exploded: true,*/ ";
				break;
			case 'ROTH' :
				$str .= " , color: 'hsl(190, 100%, 50%)', /*exploded: true,*/ ";
				break;
			case 'taxable account' :
				$str .= " , color: 'hsl(160, 100%, 50%)', /*exploded: true,*/ ";
				break;
			case 'unreceived ATI' :
				$str .= " , color: 'hsl(90, 100%, 100%)', /*exploded: true,*/ ";
				break;
			case 'depreciating asset' :
				$str .= " , color: 'hsl(40, 100%, 50%)', /*exploded: true,*/ ";
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

