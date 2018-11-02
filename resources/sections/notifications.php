<?php

?>
<section class="column notifications">
<h2>Notifications</h2>
	<ul>
<?php 
if ($number_below_par_2018_goals != 0) {
	echo "<li class='warning'><i class='fas fa-skull-crossbones'></i> &nbsp; $number_below_par_2018_goals annual goals are not on track!</li>";
}
?>
		<!--
		<li class='warning'><i class="fas fa-skull-crossbones"></i>A check is missing a start/end date</li>
		<li class='caution'><i class="fas fa-exclamation-triangle"></i>You did not hit all of your running targets last week</li>
		<li class='success'><i class="fas fa-thumbs-up"></i>You successfully met all lifting targets last week</li> -->
	</ul>
</section>


