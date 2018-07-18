<?php

?>
<section class="column goals">
	<h2>Goals</h2>
		<div class="content">
			<h3>End of Year Goals</h3>
			<div class="goal" id="goal-debt-free">
				<h3>Debt Free</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_debt_free; ?>%' data-value="<?php echo $percent_goal_debt_free; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_debt_free; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_debt_free; ?>%</h5>
			</div>
			<div class="goal" id="goal-net-worth">
				<h3>30k Net Worth</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_net_worth; ?>%' data-value="<?php echo $percent_goal_net_worth; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_net_worth; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_net_worth; ?>%</h5>
			</div>
			<div class="goal" id="goal-body-weight">
				<h3>160 lbs</h3>
				<div class="progress">
					<div class="fill" style='width:<?php echo $percent_goal_body_weight; ?>%' data-value="<?php echo $percent_goal_body_weight; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_body_weight; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_body_weight; ?>%</h5>
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
					<div class="fill" style='width:<?php echo $percent_goal_mile_time; ?>%' data-value="<?php echo $percent_goal_mile_time; ?>">
						
					</div>
					<div class="target-fill" style="width: <?php echo $percent_time_frame_running; ?>%;"></div>
				</div>
				<h5><?php echo $percent_goal_mile_time; ?>%</h5>
			</div>
		</div>
</section>