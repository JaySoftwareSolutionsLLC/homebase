<?php
	$birthdate = return_date_from_str('April 28th 1994', 'datetime');
	$birthdate_age_sixty = clone $birthdate;
	$birthdate_age_sixty->modify('+60 years');
	$today_dt = return_date_from_str('today', 'datetime');
	$interval_until_60 = date_diff($today_dt, $birthdate_age_sixty, false);
	$days_until_60 = $interval_until_60->days;
	$years_until_60 = round( $days_until_60 / 365.25 , 2 );

	$theoretical_age_60_annual_withdrawal_rate = return_theoretical_age_60_withdrawal_rate($accounts, $years_until_60);

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
<?php 	if ($year == '2018') { ?>
		<div class="stat net-worth">
			<h3>Current Net Worth</h3>
			<h4>$<?php echo $current_net_worth; ?></h4>
			<h5><?php echo $oldest_date; ?></h5>
		</div>
		<div class="stat unreceived-seal-income">
			<h3>Unreceived Seal Income</h3>
			<h4>$<?php echo number_format($unreceived_seal_income, 2); ?></h4>
			<h5>(Est Take Home: $<?php echo number_format($unreceived_after_tax_seal_income, 2); ?>)</h5>
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
		<div class="small stat theoretical-age-60-ADW">
			<h3>Age 60 ADW</h3>
			<h4>$<?php echo number_format($theoretical_age_60_annual_withdrawal_rate / 365.25 , 2); ?></h4>
		</div>
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
	else {
		$current_net_worth_info = return_finance_stat_info_html('Current Net Worth'
																, 'Determine value today of all accounts and assets minus liabilities'
																, ''
																, array('Accounts havent changed since last update', 'Non-liquid assets are able to be sold for theoretical value')
																, 'Includes Unreceived After Tax Income');
		echo return_finance_stat_html( 'Current Net Worth', "$" . number_format($current_net_worth), '', '', $current_net_worth_info );
	
		echo "<div class='row'>"; // Create a row div for 2 small stats
		
		$adi_info = return_finance_stat_info_html('Average Daily Income', 'Determine Average Daily Income (YTD)', 'Net Income / Day of year', array(), 'Pre-tax amount');
		echo return_finance_stat_html( 'ADI', "$" . number_format( $adi, 2 ) . "/day", AVG_DAILY_INCOME_TARGET, 'small', $adi_info, '#0F0', 'stat-adi', array('all' => 'all', 'S&D' => 'S&D', 'Ricks' => 'Ricks') );

		$ade_info = return_finance_stat_info_html('Average Daily Expenditure', 'Determine Average Daily Expenses (YTD)', 'Net Expenditure / Day of year', array(), '');
		echo return_finance_stat_html( 'ADE', "$" . number_format( $ade, 2 ) . "/day", AVG_DAILY_EXPENDITURE_TARGET, 'small', $ade_info, '#F00', 'stat-ade', array('all' => 'all', 'lux' => 'lux', 'non' => 'non')  );

		echo "</div>"; // End row div

		echo "<div class='row'>"; // Create a row div for 2 small stats
		
		$awh_info = return_finance_stat_info_html('Average Working Hours', 'Determine average hours worked per week (YTD)', 'Net Hours / (Day of year / 7)', array(), '');
		echo return_finance_stat_html( 'AWH', number_format( $awh, 2 ) . " hrs/wk", WEEKLY_HOURS_TARGET, 'small', $awh_info );
		
		$ahw_info = return_finance_stat_info_html('Average Hourly Wage', 'Determine Average Hourly Wage (YTD)', 'Net Income / Hours Worked', array('All shifts are recorded properly'), '');
		echo return_finance_stat_html( 'AHW', "$" . number_format( $ahw, 2 ) . "/hr", HOURLY_WAGE_TARGET, 'small', $ahw_info );

		echo "</div>"; // End row div

		$opportunity_surplus_info = return_finance_stat_info_html('Opportunity Surplus'
																, 'Determine amount of $ I can spend and still hit annual net worth contribution goal'
																, '(($net_income + ( CASHABLE_PTO_HOURS * $correct_hourly) + (($avg_full_week_ricks_income + $avg_full_week_seal_income) * $weeks_left_in_year) + REMAINING_BONUSES + REMAINING_EMP_401K_DELTA) * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - ($net_expenditure + (AVG_DAILY_EXPENDITURE_TARGET * $days_left_in_year)) - ANNUAL_NET_WORTH_CONTRIBUTION_TARGET'
																, array('Work Thu/Sat PMs @ Ricks', 'Cash out all PTO', 'Future ADE = Target ADE'), 'Accounts for seasonality');
		echo return_finance_stat_html( 'Opportunity Surplus', "$" . number_format($opportunity_surplus), '', '', $opportunity_surplus_info, $opportunity_surplus_font_color );
	
		$theoretical_age_60_ADW_info = return_finance_stat_info_html('Theoretical Age 60 ADW'
																   , 'Determine Average Daily Withdrawal from Investments at age 60'
																   , ''
																   , array('exp_roi is hit each year for each account (or avg ROI is equal to selected variant)')
																   , 'Only considers ROTH and taxable account types.');
		echo return_finance_stat_html( 'Age 60 ADW', "$" . number_format( $theoretical_age_60_annual_withdrawal_rate / 365.25 , 2 ) . "/day", '', '', $theoretical_age_60_ADW_info, '#FFF', 'stat-age-60-adw', array('5' => '5%', '7' => '7%','8' => '8%','10' => '10%','12' => '12%') );
	
		$theoretical_eoy_nw_info = return_finance_stat_info_html('Theoretical EOY Net Worth', 'Determine Net Worth on last day of year if I earn expected ROI on investments and earn my theoretical income amount.', 'appreciated account values + (theoretical future income * take home %) - (Target ADE * days left in year)');
		echo return_finance_stat_html('Theoretical EOY Net Worth',"$" . number_format($theoretical_EOY_net_worth), '', '', $theoretical_eoy_nw_info);
	
		$theoretical_income_info = return_finance_stat_info_html('Theoretical Income'
															   , 'Determine 2019 pre-tax income'
															   , 'Current Income + (Cashable PTO Hours * Current Hourly Wage) + ((Avg Weekly Ricks Income + Avg Weekly Seal Income) * Weeks left in year ) + REMAINING_BONUSES + REMAINING_EMP_401K_DELTA'
															   , array('Work all regular shifts', 'Cashout all PTO', 'No raises', 'No bonuses')
															   , 'Accounts for seasonality');
		echo return_finance_stat_html('Theoretical Income', "$" . number_format($theoretical_income_this_year), '', '', $theoretical_income_info, '', 'stat-theoretical-income', array('SD' => 'S&D', 'MPM' => 'M', 'TPM' => 'T', 'WPM' => 'W', 'RPM' => 'R', 'FPM' => 'F', 'SPM' => 'S'));

		$days_financially_free_info = return_finance_stat_info_html('Days Financially Free', 'Determine the number of days that I could not work consecutively before having to dip into retirement accounts', 'Liquid Cash / ADE', array('My average expenditure during hiatus equals ADE'), '');
		echo return_finance_stat_html('FF Timeline', $financial_freedom_datetime->format('M jS, Y'), "($days_financially_free days)", '', $days_financially_free_info, '#FFF', 'stat-ff', array('45' => '$45', '60' => '$60', '75' => '$75', '100' => '$100'));
	}
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
				$str .= " , color: 'hsl(0, 100%, 50%)', exploded: true, ";
				break;
			case 'liquid cash' :
				$str .= " , color: 'hsl(120, 100%, 50%)', exploded: true, ";
				break;
			case 'Loaned' :
				$str .= " , color: 'hsl(80, 100%, 50%)', exploded: true, ";
				break;
			case 'ROTH' :
				$str .= " , color: 'hsl(190, 100%, 50%)', exploded: true, ";
				break;
			case 'FSA' :
				$str .= " , color: 'hsl(285, 100%, 50%)', exploded: true, ";
				break;
			case 'taxable account' :
				$str .= " , color: 'hsl(170, 100%, 50%)', exploded: true, ";
				break;
			case 'unreceived ATI' :
				$str .= " , color: 'hsl(0, 100%, 100%)', exploded: true, ";
				break;
			case 'depreciating asset' :
				$str .= " , color: 'hsl(30, 100%, 50%)', exploded: true, ";
				break;
			case 'crypto' :
				$str .= " , color: 'hsl(60, 100%, 50%)', exploded: true, ";
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

