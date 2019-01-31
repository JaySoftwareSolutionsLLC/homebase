<section class='report-header'>
	<form action="<?php echo $_SERVER['../PHP_SELF']; ?>" method='post'>
		<label for='start-date'>Start Date</label>
		<input type='date' name='start-date' id='start-date' value='<?php
																		if ($start_date != '') {
																			echo $start_date;
																		}
																		else {
																			echo date('Y-m-d', strtotime('1 week ago')); 
																		} ?>' />	
		<label for='end-date'>End Date</label>
		<input type='date' name='end-date' id='end-date' value='<?php
																		if ($end_date != '') {
																			echo $end_date;
																		}
																		else {
																			echo date('Y-m-d', time());
																		} ?>' />	
		<button type='submit'>Generate Report</button>
		
	</form>
</section>