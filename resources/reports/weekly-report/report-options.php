<section class='report-options'>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
		<button type='button' class='previous-week'>Previous Week</button>
		
		<label for='start-date'>Start Date</label>
		<input type='date' name='start-date' id='start-date' value='<?php
																	if ($date_start != '') {
																		echo $date_start;
																	}
																	else {
																		echo date('Y-m-d', strtotime('Last Monday'));
																	}
																	?>' />
		
		<label for='end-date'>End Date</label>
		<input type='date' name='end-date' id='end-date' value='<?php 
																	if ($date_end != '') {
																		echo $date_end;
																	}
																	else {
																		echo date('Y-m-d', time());
																	}
																	?>' />
		<button type='button' class='next-week'>Next Week</button>		
		<button type='submit'>Generate Report</button>
		
	</form>
</section>