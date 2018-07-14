<?php
	$today_time = 			time(); // Should be moved into main index.php file

//--- Running ---

	$start_date_running = 	date('Y/m/d', strtotime($START_DATE_STRING_RUNNING));
	$start_time_running = 	strtotime($start_date_running);

	$days_active_running =	ceil(($today_time - $start_time) / (SEC_IN_DAY));

	$q = "SELECT MIN(seconds) FROM `fitness_runs` WHERE miles >= 1;";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$best_mile_time = $row[0];

	// Case Tests: bmt = 405 --> 100% | bmt = 485 --> 0% | bmt = 445 --> 50%
	// All Case Tests PASS
	$percent_goal_mile_time = number_format(100 - (($best_mile_time - MILE_TIME_TARGET) * (100 / ($STARTING_MILE_TIME - MILE_TIME_TARGET))), 2);

//--- Body Weight ---
	
	$start_date_body_weight = 	date('Y/m/d', strtotime(START_DATE_STRING_BODY_WEIGHT));
	$start_time_body_weight = 	strtotime($start_date_body_weight);

	$days_active_body_weight =	ceil(($today_time - $start_time_body_weight) / (SEC_IN_DAY));

	$q = "SELECT pounds FROM `fitness_measurements_body_weight` WHERE datetime = (SELECT MAX(datetime) FROM `fitness_measurements_body_weight`)";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$most_recent_body_weight = $row[0];

	// Case Tests: mrbw = 147 --> 0% | mrbw = 160 --> 100% | mrbw = 153.5 --> 50%
	// All Case Tests PASS
	$percent_goal_body_weight = number_format(($most_recent_body_weight - STARTING_BODY_WEIGHT) * (100 / (BODY_WEIGHT_TARGET -STARTING_BODY_WEIGHT)), 2);

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
				<h3>160 lbs</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_body_weight ?>%'>
						
					</div>
				</div>
				<h5><?php echo $percent_goal_body_weight ?>%</h5>
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