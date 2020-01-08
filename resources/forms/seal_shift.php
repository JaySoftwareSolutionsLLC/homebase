<?php 
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');
$types_of_expenses; // pull this from DB enumerated field of type

$entry_msg = "Welcome to the Seal & Design shift submission page.";

// If variables have been posted insert into db
if ( isset( $_POST['date'] ) && isset( $_POST['arrival_time'] ) && isset( $_POST['departure_time'] ) && isset( $_POST['break_min'] ) ) {
	$desc = ( empty( $_POST['description'] ) ) ? "NULL" : "'" . htmlspecialchars( $_POST['description'], ENT_QUOTES ) . "'";
	$strain = ( empty( $_POST['strain'] ) ) ? "NULL" : $_POST['strain'];
	$feedback = ( empty( $_POST['feedback'] ) ) ? "NULL" : $_POST['feedback'];
	$stress = ( empty( $_POST['stress'] ) ) ? "NULL" : $_POST['stress'];
	$telecommute = ( empty( $_POST['telecommute'] ) ) ? "0" : $_POST['telecommute'];

	$qry = "INSERT INTO `finance_seal_shifts` (`date`, `arrival_time`, `departure_time`, `strain`, `feedback`, `stress`, `description`, `break_min`, `telecommute`)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['arrival_time'] . "', '" . $_POST['departure_time'] . "', $strain, $feedback, $stress, $desc, " . $_POST['break_min'] . ", " . $telecommute . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT 	date,
				DAYNAME(date) AS 'dow'
				,arrival_time
				,departure_time
				,( ( time_to_sec( TIMEDIFF( departure_time, arrival_time ) ) / ( 60 * 60 ) ) - ( break_min / 60 ) ) AS 'hours'
				,break_min
				,strain
				,feedback
				,stress
				,description
				,telecommute
		FROM finance_seal_shifts ORDER BY date DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['date'] . "</td>
						<td>" . substr($row['dow'], 0, 3) . "</td>
						<td>" . substr($row['arrival_time'], 0, 5) . "</td>
						<td>" . substr($row['departure_time'], 0, 5) . "</td>
						<td>" . $row['break_min'] . "</td>
						<td>" . round($row['hours'], 2) . "</td>
						<td>" . $row['strain'] . "</td>
						<td>" . $row['feedback'] . "</td>
						<td>" . $row['stress'] . "</td>
						<td>" . $row['description'] . "</td>";
		$data_log .= $row['telecommute'] ? '<td>Y</td>' : '<td>N</td>';
		$data_log .= "</tr>";
    }
}

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>

	<body>
		<main>

			<h1>Seal & Design Shift</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='date'>Date</label>
				<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
				<label for='arrival_time'>Arrival Time</label>
				<input id='arrival_time' type='time' name='arrival_time'/>
				<label for='departure_time'>Departure Time</label>
				<input id='departure_time' type='time' name='departure_time'/>
				<span style='display: flex; flex-flow: row nowrap; justify-content: space-between;'>
					<span class='flex-input' style=''>
						<label for='strain'>Strain</label>
						<input id='strain' type='number' name='strain' min='1' max='10' step='1'/>
					</span>
					<span class='flex-input' style=''>
						<label for='feedback'>Feedback</label>
						<input id='feedback' type='number' name='feedback' min='1' max='10' step='1'/>
					</span>
					<span class='flex-input' style=''>
						<label for='stress'>Stress</label>
						<input id='stress' type='number' name='stress' min='1' max='10' step='1'/>
					</span>
				</span>
				<span style='display: flex; flex-flow: row nowrap; justify-content: space-between;'>
					<span class='flex-input' style=''>
						<label for='break_min'>Break Minutes</label>
						<input id='break_min' type='number' name='break_min' min='0' max='255' step='1' value='30'/>
					</span>
					<span class='flex-input' style=''>
						<label for='telecommute'>Telecommute</label>
						<input id='telecommute' type='checkbox' name='telecommute' value='1'/>
					</span>
				</span>
				<label for='description'>Description (Optional)</label>
				<span style='position: relative;'>
					<textarea name='description' class='description' maxlength='255' placeholder='Epicor Upgrade Meeting with Jim. Spent most of day writing SQL queries...' style='width: 100%;'></textarea>
					<h3 class='desc-char-used' style='position: absolute; bottom: 0.5rem; right: 0.5rem; color: hsla(0, 0%, 0%, 0.5);'>0/255</h3>
				</span>
				<button type="submit">Submit</button>
			</form>

			<table class='log' id='seal-and-design-shifts-table'>
				<thead>
					<tr>
						<th>Date</th>
						<th>Day</th>
						<th>Arr.</th>
						<th>Dep.</th>
						<th>Brk. Min</th>
						<th>Hrs</th>
						<th>Strain</th>
						<th>Feedback</th>
						<th>Stress</th>
						<th>Description</th>
						<th><i class="fas fa-home"></i></th>
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
					$('#seal-and-design-shifts-table').DataTable( {
						"order": [[ 0, "desc" ]]
					} );

					$('textarea.description').on('keyup', function() {
						let charCount = $(this).val();
						charCount = charCount.length;
						$('h3.desc-char-used').html(`${charCount}/255`);
					});

				} );
			</script>
	</body>

</html>