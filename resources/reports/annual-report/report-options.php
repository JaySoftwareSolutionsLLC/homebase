<?php

?>
<section class='report-options'>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
		<label for='year'>Year</label>
		<select name='year' id='year-input'>
			<option value='2018' <?php if ($year == '2018') {echo 'selected';} ?>>2018</option>
			<option value='2019' <?php if ($year == '2019') {echo 'selected';} ?>>2019</option>
		</select>
		<button type='submit'>Generate Report</button>
	</form>
</section>