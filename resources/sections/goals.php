<?php
	$today_time = 			time(); // Should be moved into main index.php file

	$start_date_running = 	date('Y/m/d', strtotime($START_DATE_STRING_RUNNING));
	$start_time_running = 	strtotime($start_date);

	$days_active_running =	ceil(($today_time - $start_time) / (SEC_IN_DAY));

	$q = "SELECT MIN(seconds) FROM `fitness_runs` WHERE miles >= 1;";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$best_mile_time = $row[0];

	// Case Tests: bmt = 405 --> 100% | bmt = 485 --> 0% | bmt = 444 --> 50%
	// All Case Tests PASS
	$percent_goal_mile_time = number_format(100 - (($best_mile_time - MILE_TIME_TARGET) * (100 / ($STARTING_MILE_TIME - MILE_TIME_TARGET))), 2);


?>



<section class="column goals">
	<h2>Goals</h2>
		<div class="content">
			<h3>End of Year Goals</h3>
			<div class="goal" id="goal-debt-free">
				<h3>Debt Free</h3>
				<div class="progress">
					<div class="fill">
						
					</div>
				</div>
			</div>
			<div class="goal" id="goal-net-worth">
				<h3>30k Net Worth</h3>
				<div class="progress">
					<div class="fill">
						
					</div>
				</div>
			</div>
			<div class="goal" id="goal-body-weight">
				<h3>165 lbs</h3>
				<div class="progress">
					<div class="fill">
						
					</div>
				</div>
			</div>
			<div class="goal" id="goal-bench-press">
				<h3>Bench 200</h3>
				<div class="progress">
					<div class="fill">
						
					</div>
				</div>
			</div>
			<div class="goal" id="goal-running">
				<h3>6 Minute Mile</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_mile_time ?>%'>
						
					</div>
				</div>
				<h5><?php echo $percent_goal_mile_time ?>%</h5>
			</div>
		</div>
</section>