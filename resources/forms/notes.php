<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d H:i:s');

$entry_msg = "Welcome to the Notes submission page.";


// If variables have been posted insert into db
if(isset($_POST['summary']) && isset($_POST['description']) && isset($_POST['type'])) {
	$datetime = (empty($_POST['datetime'])) ? 'CURRENT_TIMESTAMP' : "'" . $_POST['datetime'] . "'";
	$type = "'" . $_POST['type'] . "'";
	$summary = "'" . $_POST['summary'] . "'";
	$description = "'" . $_POST['description'] . "'";
	$caution_datetime = (empty($_POST['caution-datetime'])) ? 'NULL' : "'" . $_POST['caution-datetime'] . "'";
	$warning_datetime = (empty($_POST['warning-datetime'])) ? 'NULL' : "'" . $_POST['warning-datetime'] . "'";
	$qry = "INSERT INTO `personal_notes` (`datetime`, `type`, `summary`, `description`, `caution_datetime`, `warning_datetime`) 
			VALUES ($datetime, $type, $summary, $description, $caution_datetime, $warning_datetime);";
	//echo $qry;
	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
	
}


$qry = "SELECT 	*
		FROM personal_notes 
		ORDER BY datetime DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
		if ( ! empty( $row['complete_datetime'] ) ) {
			$complete_date_formatted = new DateTime( $row['complete_datetime'] );
			$complete_date_formatted = date_format($complete_date_formatted, 'Y-m-d\TH:i');
		}
		else {
			$complete_date_formatted = '';
		}
        $data_log .= "
					<tr>
						<td>" . $row['datetime'] . "</td>
						<td>" . $row['summary'] . "</td>
						<td>" . $row['description'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $row['caution_datetime'] . "</td>
						<td>" . $row['warning_datetime'] . "</td>";
		if ($row['caution_datetime'] != '' || $row['warning_datetime'] != '') {
			$data_log .= "<td><input data-id='" . $row['id'] . "' type='datetime-local' name='complete-datetime' class='complete-datetime' value='$complete_date_formatted'/></td>";
		}
		else {
			$data_log .= "<td></td>";
		}
			$data_log .= "</tr>";
    }
}

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>

	<body>
		<main>

			<h1>Notes</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='datetime'>Datetime (Optional)</label>
				<input type='datetime-local' name='datetime'/>
				<label for='type'>Type</label>
				<select name='type'>
					<option value='positive experience'>Positive Experience</option>
					<option value='negative experience'>Negative Experience</option>
					<option value='reminder'>Reminder</option>
					<option value='idea'>Idea</option>
					<option value='thought'>Thought</option>
					<option value='quote'>Quote</option>
					<option value='lesson'>Lesson</option>
					<option value='book to read'>Book To Read</option>
					<option value='learning resource'>Learning Resource</option>
					<option value='homebase enhancement'>HomeBase Enhancement</option>
				</select>
				<label for='summary'>Summary</label>
				<input name='summary' type='text' placeholder='Heart to heart with Molly Bolzano' maxlength='50'/>
				<label for='description'>Description</label>
				<textarea name='description' maxlength='255' placeholder='Had a heart to heart with Molly during shift today.'></textarea>
				<label for='caution-datetime'>Caution Datetime (Optional)</label>
				<input type='datetime-local' name='caution-datetime'/>
				<label for='warning-datetime'>Warning Datetime (Optional)</label>
				<input type='datetime-local' name='warning-datetime'/>
				<button type="submit">Submit</button>
			</form>

			<table class='log' id='runs-table'>
				<thead>
					<tr>
						<th>DateTime</th>
						<th>Subject</th>
						<th>Description</th>
						<th>Type</th>
						<th>Caution DT</th>
						<th>Warning DT</th>
						<th>Complete</th>
					</tr>				
				</thead>
				<?php echo $data_log; ?>
			</table>

		</main>
<?php
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
?>
	        <script type="text/javascript" src="/homebase/resources/resources.js"></script>
			<script>
				$(document).ready( function () {
					$('#runs-table').DataTable( {
						"order": [ 0, 'desc' ]
					} );

					$('input.complete-datetime').on('blur', function() {
						let val = "'" + $(this).val() + "'";
						if (val == "''") {
							val = "NULL";
						}
						let id = $(this).attr('data-id');
						console.log("CHANGE!");
						ajaxPostUpdate("/homebase/resources/forms/form-resources/update_note.php", { 'column-name' : 'complete_datetime', 'value' : val , 'id' : id }, true );
					});
				} );
			</script>
	</body>

</html>