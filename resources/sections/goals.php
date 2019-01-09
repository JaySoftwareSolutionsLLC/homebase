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
?>
			<div class="goal" id="goal-debt-free">
				<h3>Net Worth</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_net_worth_2019; ?>%' data-value="<?php echo $percent_goal_net_worth_2019; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_net_worth_2019; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_net_worth_2019; ?>%</h5>
			</div>
			<div class="goal" id="goal-body-weight">
				<h3>Body Weight</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_body_weight_2019; ?>%' data-value="<?php echo $percent_goal_body_weight_2019; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_body_weight_2019; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_body_weight_2019; ?>%</h5>
			</div>
			<div class="goal" id="goal-arm-size">
				<h3>Arm Size</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo 0; ?>%' data-value="<?php echo 0; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_body_weight_2019; ?>%;"></div>
				</div>
				<h5><?php echo 0; ?>%</h5>
			</div>
<?php
	}
?>

		</div>
</section>