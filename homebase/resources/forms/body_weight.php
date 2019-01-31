<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d H:i:s');

$entry_msg = "Welcome to the Bodyweight submission page.";

// If variables have been posted insert into db
if(isset($_POST['datetime']) && isset($_POST['pounds'])) {
	$qry = "INSERT INTO `fitness_measurements_body_weight`(`datetime`,`pounds`)
	VALUES ('" . $_POST['datetime'] . "', " . $_POST['pounds'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT 	datetime,
				pounds
		FROM fitness_measurements_body_weight ORDER BY datetime DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['datetime'] . "</td>
						<td>" . $row['pounds'] . "</td>
						</tr>";
    }
}

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>

	<body>
		<main>

			<h1>Body Weights</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='datetime'>Datetime</label>
				<input id='datetime' type='datetime-local' name='datetime' value="<?php echo $today;?>"/>
				<label for='pounds'>Pounds</label>
				<input id='pounds' type='number' name='pounds' min='0' step='0.01'/>
				<button type="submit">Submit</button>
			</form>

			<table class='log' id='body-weights-table'>
				<thead>
					<tr>
						<th>Datetime</th>
						<th>Pounds</th>
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
					$('#body-weights-table').DataTable( {
						"order": [[ 0, "desc" ]]
					} );
				} );
			</script>
	</body>

</html>