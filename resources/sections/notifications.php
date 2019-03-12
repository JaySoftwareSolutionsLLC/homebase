<?php
	$notifications = array(); // Array to house notification objects
	$notifications[] = return_metric_based_notification_object($account_types['liquid cash'], 'Current Cash', 5400, 10800, 2700, 14000, "Liquid cash low", "Liquid cash very low", "Liquid cash high", "Liquid cash very high" );
	foreach ($muscle_objects as $mo) {
		if ($mo->name == 'triceps') {
			$tricep_mo = $mo;
		}
		else if ($mo->name == 'biceps') {
			$bicep_mo = $mo;
		}
	}
	$notifications[] = 
	return_metric_based_notification_object($tricep_mo->hur, 'Tricep HUR', 1, 999, -24, 999, 'Triceps ready', 'Tricep lift overdue', 'Triceps ready soon', '');
	$notifications[] = 
	return_metric_based_notification_object($bicep_mo->hur, 'Bicep HUR', 1, 999, -24, 999, 'Biceps ready', 'Bicep lift overdue', 'Biceps ready soon', '');

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


