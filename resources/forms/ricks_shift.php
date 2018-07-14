<?php 
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');
$types_of_expenses; // pull this from DB enumerated field of type

$entry_msg = "Welcome to the Rick's on Main shift submission page.";

// If variables have been posted insert into db
if(isset($_POST['date'])) {
	$qry = "INSERT INTO `finance_ricks_shifts`(`date`,`type`,`hours`,`tips`)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['type'] . "', '" . $_POST['hours'] . "', " . $_POST['tips'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT DAYNAME(date) AS 'dow', date, type, hours, tips FROM finance_ricks_shifts ORDER BY date DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
		$hours = $row['hours'];
		$tips = $row['tips'];
		$hourly = number_format((($tips / $hours) + $HOURLY_WAGE_RICKS), 2);
		$total = number_format((($hours * $HOURLY_WAGE_RICKS) + $tips), 2);
        $data_log .= "<tr>
						<td>" . $row['date'] . "</td>
						<td>" . $row['dow'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $hours . "</td>
						<td>" . $tips . "</td>
						<td>" . $hourly . "</td>
						<td>" . $total . "</td>
						</tr>";
    }
}

$qry = "SELECT DAYNAME(date) AS 'dow', type, AVG(tips + (hours * 7.5)) AS 'net income', AVG((tips / hours) + 7.5) AS 'hourly wage' FROM `finance_ricks_shifts` GROUP BY DAYNAME(date), type";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$shifts = array();
    while($row = $res->fetch_assoc()) {
		$shifts[] = array(substr($row['dow'], 0, 3), $row['type'], number_format($row['net income'], 2), number_format($row['hourly wage'], 2));
    }
}
else {
	echo "NO RESULTS";
}

// var_dump($shifts); // NOT WORKING

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>


	<body>
		<main>

			<h1>Rick's on Main Shifts</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='date'>Date</label>
				<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
				<label for='type'>Type</label>
				<select id='type' name='type'>
					<option value='am'>AM</option>
					<option value='pm' selected>PM</option>
				</select>
				<label for='hours'>Hours</label>
				<input id='hours' type='number' name='hours' min='0' max='10' step='0.01'/>
				<label for='tips'>Tips</label>
				<input id='tips' type='number' name='tips' min='0' max='500' step='1'/>
				<button type="submit">Submit</button>
			</form>
			
			<section class='shift-averages' style='display: inline-flex; justify-content: space-between; width: 60%; flex-flow: row wrap; padding: 0;'>
<?php
			foreach ($shifts as $s) {
				echo "	<div style='border: 1px solid hsl(190, 100%, 50%); width: 10rem; height: 10rem; border-radius: 5rem; display: inline-flex; flex-flow: column nowrap; align-items: center; justify-content: space-around; padding: 1rem; box-sizing: border-box;'>
							<h3><span style='font-weight: 900;'>$s[0]</span> - $s[1]</h3>
							<h4>$<span style='font-weight: 900;'>$s[2]</span>/shift</h4>
							<h4>$<span style='font-weight: 900;'>$s[3]</span>/hour</h4>
						</div>";
			}
?>
			</section>

			<table class='log' id='ricks-shifts-table'>
				<thead>
					<tr>
						<th>Date</th>
						<th>Day</th>
						<th>Type</th>
						<th>Hours</th>
						<th>Tips</th>
						<th>Hourly</th>
						<th>Net</th>
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
				$('#ricks-shifts-table').DataTable( {
					"order": [[ 0, "desc" ]]
				} );
			} );
		</script>
	</body>