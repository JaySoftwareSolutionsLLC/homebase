<?php
	$notifications = array(); // Array to house notification objects
	$notifications[] = return_metric_based_notification_object($account_types['liquid cash'], 'Current Cash', 5400, 10800, 2700, 14000, "Cash on hand low", "Cash on hand very low", "Cash on hand high", "Cash on hand very high" );
	foreach ($muscle_objects as $mo) {
		if ($mo->name == 'triceps') {
			$tricep_mo = $mo;
		}
		else if ($mo->name == 'biceps') {
			$bicep_mo = $mo;
		}
	}
	$notifications[] = 
	return_metric_based_notification_object($tricep_mo->hur, 'Tricep HUR', 0, 999, -24, 999, 'Tricep lift overdue', 'Tricep lift far overdue', '', '');
	$notifications[] = 
	return_metric_based_notification_object($bicep_mo->hur, 'Bicep HUR', 0, 999, -24, 999, 'Bicep lift overdue', 'Bicep lift far overdue', '', '');

?>
<section class="column notifications">
<h2>Notifications</h2>
	<ul>
<?php 
foreach ($notifications as $n) {
	switch ($n->type) {
		case 'success' :
			break;
		case 'caution' :
			echo "<li><i style='color: hsl(50, 100%, 50%);' class='fas fa-exclamation-triangle'></i>&emsp;$n->message</li>";
			break;
		case 'warning' :
			echo "<li><i style='color: hsl(0, 100%, 50%);' class='fas fa-skull-crossbones'></i>&emsp;$n->message</li>";
			break;
	}
}
?>
	</ul>
	
	<div class="row">
		<a href='resources/forms/notes.php' class="form notes" target='_blank'>
			<img src='resources/assets/images/icon-note.png'/>
			<figcaption>Notes</figcaption>
		</a>
	</div>
</section>


