<?php

?>
<section class="column goals">
	<h2>Goals</h2>
		<div class="content">
			<h3><?php echo $year; ?> Goals</h3>
			<h5>(<?php echo $days_left_in_year; ?> Days Remaining)</h5>
<?php
	if ($year == '2018') {
?>
			<div class="goal" id="goal-debt-free">
				<h3>Debt Free</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_debt_free_2018; ?>%' data-value="<?php echo $percent_goal_debt_free_2018; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_debt_free_2018; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_debt_free_2018; ?>%</h5>
			</div>
			<div class="goal" id="goal-net-worth">
				<h3>30k Net Worth</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_net_worth_2018; ?>%' data-value="<?php echo $percent_goal_net_worth_2018; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_net_worth_2018; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_net_worth_2018; ?>%</h5>
			</div>
			<div class="goal" id="goal-body-weight">
				<h3>160 lbs</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_body_weight_2018; ?>%' data-value="<?php echo $percent_goal_body_weight_2018; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_body_weight_2018; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_body_weight_2018; ?>%</h5>
			</div>
			<div class="goal" id="goal-running">
				<h3>6 Minute Mile</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_mile_time_2018; ?>%' data-value="<?php echo $percent_goal_mile_time_2018; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_running_2018; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_mile_time_2018; ?>%</h5>
			</div>
			<div class="goal" id="goal-bench-press">
				<h3>Bench 200</h3>
				<div class="progress">
					<div class="fill" style="width:<?php echo $percent_goal_bench_press_2018; ?>%;" data-value="<?php echo $percent_goal_bench_press_2018; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_bench_press_2018; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_bench_press_2018; ?>%</h5>
			</div>
<?php		
	}
	else if ($year == '2019') {
		
		echo return_timed_goal_progress_bar_html('NW Cont.', 'goal-net-worth-cont', 0, ANNUAL_NET_WORTH_CONTRIBUTION_TARGET, $current_est_nw_contribution, '2019-01-01', '2019-12-31', '2019-12-31', 'linear', null); // Defined in homebase/resources/resources.php
		
		echo return_timed_goal_progress_bar_html('Cert Hrs', 'goal-full-stack-certification', 0, SOFTWARE_DEV_TARGET_CERT_GOAL, $software_cert_hours, START_DATE_STRING_CERT_GOAL, END_DATE_STRING_CERT_GOAL, END_DATE_STRING_CERT_GOAL, 'linear', null); // Defined in homebase/resources/resources.php
		
		echo return_timed_goal_progress_bar_html('Body Weight', 'goal-body-weight', STARTING_BODY_WEIGHT, BODY_WEIGHT_TARGET, $most_recent_body_weight, START_DATE_STRING_BODY_WEIGHT, '2019-12-31', '2019-12-31', /*'polynomial',*/ 'linear', null); // Defined in homebase/resources/resources.php
		
		echo return_timed_goal_progress_bar_html('Arm Size', 'goal-arm-size', STARTING_UPPER_ARM_CIRC, UPPER_ARM_CIRC_TARGET, $most_recent_upper_arm_size, START_DATE_STRING_UPPER_ARM_CIRC, '2019-12-31', '2019-12-31', /*'polynomial',*/ 'linear', null); // Defined in homebase/resources/resources.php
		
		//echo return_timed_goal_progress_bar_html('Mile Time', 'goal-mile-time', STARTING_MILE_TIME, MILE_TIME_TARGET, $best_mile_time, START_DATE_STRING_RUNNING, '2019-12-31', 'now', 'polynomial', null); // Defined in homebase/resources/resources.php

		//echo return_timed_goal_progress_bar_html('Dev Hours', 'goal-dev-hours', 0, SOFTWARE_DEV_TARGET_HOURS, $software_dev_hours, START_DATE_SOFTWARE_DEV_HOURS, '2019-12-31'); // Defined in homebase/resources/resources.php

		//echo return_timed_goal_progress_bar_html('JSS Income', 'goal-jss-income', 0, 5000, 0, null, null); // Defined in homebase/resources/resources.php

		//echo return_timed_goal_progress_bar_html('Mindful Hours.', 'goal-mindful-sessions', 0, MINDFULNESS_TARGET_HOURS, $mindfulness_hours, START_DATE_MINDFULNESS_HOURS, '2019-12-31'); // Defined in homebase/resources/resources.php

		
		echo return_timed_goal_progress_bar_html('Opt. Health', 'goal-optimal-health', 0, 100, (100 * $optimal_health_percentage), null, null); // Defined in homebase/resources/resources.php
	}
	else if ($year == '2020') {
		# Fourth Quarter
		echo return_timed_goal_progress_bar_html('Family Habit App', 'family-app', 0, 9, 7, '2020-10-01', '2020-12-31', '2020-12-31');
		
		echo return_timed_goal_progress_bar_html('Become Kratos', 'become-kratos', 0, 11, 5, '2020-10-01', '2020-12-31', '2020-12-31');
		
		echo return_timed_goal_progress_bar_html('Yale SOWB', 'yale-sowb', 0, 8, 7, '2020-10-01', '2020-12-31', '2020-12-31');
		
		echo return_timed_goal_progress_bar_html('MBA Apps', 'mba-apps', 0, 10, 9, '2020-10-01', '2020-12-31', '2020-12-31');
	} else if ($year == '2022') {
		$today_dt = new DateTime();
		$mba_orientation_dt = new DateTime("2022-08-01");
		$day_diff = $today_dt->diff($mba_orientation_dt)->days;
		echo "Days until MBA Orientation: $day_diff<br/>";

		// Each point is a quiz / project. There are 25 total. 17 Quizes. 8 Projects.
		echo return_timed_goal_progress_bar_html('Stanford ML', 'stanford-machine-learning', 0, 25, 20, '2021-12-13', '2022-03-07');
	}
?>

		</div>
</section>