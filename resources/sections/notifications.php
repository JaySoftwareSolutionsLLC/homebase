<?php
	//$notifications = array(); // Array to house notification objects
	//var_dump($notifications);
	foreach ($muscle_objects as $mo) {
		if ($mo->name == 'triceps') {
			$tricep_mo = $mo;
		}
		else if ($mo->name == 'biceps') {
			$bicep_mo = $mo;
		}
	}

	// Would be nice to break these out into a tasks and tasks_log table but I may need to research triggers to implement this. ie. If personal_day_info has expense review updated then trigger an update to tasks_log
	$notifications[] = return_metric_based_notification_object($days_since_most_recent_expense_review, "Days Since Last Expense Review ", 0, 7, 0, 14, "N/A", "N/A", "Expense Review Overdue (" . date_format($most_recent_expense_review_dt, 'Y-m-d') . ")", "Expense Review Far Overdue (" . date_format($most_recent_expense_review_dt, 'Y-m-d') . ")", 15 ); // Check Schwab & BoA account history (as well as memory account of cash expenditure) to verify all expenses were logged correctly
	$notifications[] = return_metric_based_notification_object($days_since_most_recent_upper_arm_circ_measurement, 'Days Since Last Upper Arm Measurement', 0, 14, 0, 28, "N/A", "N/A", "Upper Arm Circ Overdue", "Upper Arm Circ Far Overdue", 5 );
	//$notifications[] = return_metric_based_notification_object($account_types['liquid cash'], 'Current Cash', 5400, 10800, 2700, 14000, "Cash on hand low", "Cash on hand very low", "Cash on hand high", "Cash on hand very high", 10 ); Notification hidden until dust settles with mom and dad house situation
	$notifications[] = return_metric_based_notification_object($tricep_mo->hur, 'Tricep HUR', 0, 999, -24, 999, 'Tricep lift overdue', 'Tricep lift far overdue', '', '', 8);
	$notifications[] = return_metric_based_notification_object($bicep_mo->hur, 'Bicep HUR', 0, 999, -24, 999, 'Bicep lift overdue', 'Bicep lift far overdue', '', '', 8);
	// var_dump inside of hidden div for debugging and development purposes
	hidden_var_dump($notifications); // Instantiated in epicor-web/common/resources.php
?>
<section class="column notifications">
<h2>Notifications</h2>
	<ul>
<?php
$net_time_requirement = 0;
foreach ($notifications as $n) { // Print out warnings first
	global $net_time_requirement;
	switch ($n->type) {
		/*
		case 'success' :
			break;
		case 'caution' :
			echo "<li><i style='color: hsl(50, 100%, 50%);' class='fas fa-exclamation-triangle'></i>$n->message [~$n->est_min_to_comp min.]</li>";
			$net_time_requirement += $n->est_min_to_comp;
			break;
		*/
		case 'warning' :
			echo "<li><i style='color: hsl(0, 100%, 50%);' class='fas fa-skull-crossbones'></i>$n->message [~$n->est_min_to_comp min.]</li>";
			$net_time_requirement += $n->est_min_to_comp;
			break;
		default:
			break;
	}
}
foreach ($notifications as $n) { // Print out cautions second
	global $net_time_requirement;
	switch ($n->type) {
		/*
		case 'success' :
			break;
		*/
		case 'caution' :
			echo "<li><i style='color: hsl(50, 100%, 50%);' class='fas fa-exclamation-triangle'></i>$n->message [~$n->est_min_to_comp min.]</li>";
			$net_time_requirement += $n->est_min_to_comp;
			break;
		/*
		case 'warning' :
			echo "<li><i style='color: hsl(0, 100%, 50%);' class='fas fa-skull-crossbones'></i>$n->message [~$n->est_min_to_comp min.]</li>";
			$net_time_requirement += $n->est_min_to_comp;
			break;
		*/
		default:
			break;
	}
}
echo "<li><i class='fas fa-stopwatch'></i>$net_time_requirement minutes</li>";
?>
	</ul>
	
	<div class="row">
		<a href='resources/forms/notes.php' class="form notes" target='_blank'>
			<img src='resources/assets/images/icon-note.png'/>
			<figcaption>Notes</figcaption>
		</a>
	</div>
</section>


