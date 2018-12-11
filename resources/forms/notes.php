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
	$datetime = (empty($_POST['datetime'])) ? 'CURRENT_TIMESTAMP' : $_POST['datetime'];
	$type = "'" . $_POST['type'] . "'";
	$summary = "'" . $_POST['summary'] . "'";
	$description = "'" . $_POST['description'] . "'";
	$reminder_datetime = (empty($_POST['reminder-datetime'])) ? 'NULL' : $_POST['reminder-datetime'];
	$qry = "INSERT INTO `personal_notes` (`id`, `datetime`, `type`, `summary`, `description`, `reminder_datetime`) 
			VALUES (NULL, $datetime, $type, $summary, $description, $reminder_datetime);";
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
        $data_log .= "
					<tr>
						<td>" . $row['datetime'] . "</td>
						<td>" . $row['summary'] . "</td>
						<td>" . $row['description'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $row['reminder_datetime'] . "</td>
						<td></td>
					</tr>";
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
				<input type='datetime-local' name='datetime' value="<?php echo $today;?>"/>
				<label for='type'>Type</label>
				<select name='type'>
					<option value='positive experience'>Positive Experience</option>
					<option value='negative experience'>Negative Experience</option>
					<option value='reminder'>Reminder</option>
					<option value='idea'>Idea</option>
					<option value='thought'>Thought</option>
				</select>
				<label for='summary'>Summary</label>
				<input name='summary' type='text' placeholder='Heart to heart with Molly Bolzano'/>
				<label for='description'>Description</label>
				<textarea name='description' maxlength='255' placeholder='Had a heart to heart with Molly during shift today.'></textarea>
				<label for='reminder-datetime'>Reminder Datetime (Optional)</label>
				<input type='datetime-local' name='reminder-datetime'/>
				<button type="submit">Submit</button>
			</form>

			<table class='log' id='runs-table'>
				<thead>
					<tr>
						<th>DateTime</th>
						<th>Subject</th>
						<th>Description</th>
						<th>Type</th>
						<th>Reminder</th>
						<th>Complete</th>
					</tr>				
				</thead>
				<?php echo $data_log; ?>
			</table>

		</main>
<?php
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
?>
			<script>
				$(document).ready( function () {
					$('#runs-table').DataTable();
				} );
			</script>
	</body>

</html>