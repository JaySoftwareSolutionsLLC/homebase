<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d H:i:s');

$entry_msg = "Welcome to the Runs submission page.";

// If variables have been posted insert into db
if(isset($_POST['datetime']) && isset($_POST['miles']) && isset($_POST['seconds'])) {
	$qry = "INSERT INTO `fitness_runs`(`datetime`,`miles`,`seconds`)
	VALUES ('" . $_POST['datetime'] . "', " . $_POST['miles'] . ", " . $_POST['seconds'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT 	datetime,
				miles,
				seconds
		FROM fitness_runs ORDER BY datetime DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
		$pace = number_format(($row['seconds'] / $row['miles']) / 60, 2);
        $data_log .= "<tr>
						<td>" . $row['datetime'] . "</td>
						<td>" . $row['miles'] . "</td>
						<td>" . $row['seconds'] . "</td>
						<td>" . $pace . "min/mi</td>
						</tr>";
    }
}

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>

	<body>
		<main>

			<h1>Runs</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='datetime'>Datetime</label>
				<input id='datetime' type='datetime-local' name='datetime' value="<?php echo $today;?>"/>
				<label for='miles'>Miles</label>
				<input id='miles' type='number' name='miles' min='0' step='0.01'/>
				<label for='seconds'>Seconds</label>
				<input id='seconds' type='number' name='seconds' min='0' step='1'/>
				<button type="submit">Submit</button>
			</form>

			<table class='log' id='runs-table'>
				<thead>
					<tr>
						<th>Datetime</th>
						<th>Miles</th>
						<th>Seconds</th>
						<th>Pace</th>
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