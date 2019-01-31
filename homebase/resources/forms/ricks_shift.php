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
	$qry = "INSERT INTO `finance_ricks_shifts`(`date`, `type`, `hours`, `tips`, `stress`, `enjoyment`, `description`)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['type'] . "', '" . $_POST['hours'] . "', " . $_POST['tips'] . ", " . $_POST['stress'] . ", " . $_POST['enjoyment'] . ", '" . $_POST['description'] . "')";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
		var_dump($_POST);
	}
}

$qry = "SELECT LEFT(DAYNAME(date), 3) AS 'dow', date, type, hours, tips, stress, enjoyment, description FROM finance_ricks_shifts ORDER BY date DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
		$hours = $row['hours'];
		$tips = $row['tips'];
		$type = $row['type'];
		$hourly = 0;
		if ( $type == 'otb' ) {
			$hourly = number_format( ( $tips / $hours ) , 2 );
			$total = number_format( $tips , 2 );
		}
		else {
			$hourly = number_format((($tips / $hours) + HOURLY_WAGE_RICKS), 2);
			$total = number_format((($hours * HOURLY_WAGE_RICKS) + $tips), 2);
		}
		
		$stress = $row['stress'];
		$enjoyment = $row['enjoyment'];
		$description = $row['description'];
        $data_log .= "<tr>
						<td>" . $row['date'] . "</td>
						<td>" . $row['dow'] . "</td>
						<td>" . $type . "</td>
						<td>" . $hours . "</td>
						<td>" . $tips . "</td>
						<td>" . $hourly . "</td>
						<td>" . $total . "</td>
						<td>" . $stress . "</td>
						<td>" . $enjoyment . "</td>
						<td style='font-size: 0.625rem'>" . $description . "</td>
						</tr>";
    }
}

$qry = "SELECT DAYNAME(date) AS 'dow', type, AVG(tips + (hours * " . HOURLY_WAGE_RICKS . ")) AS 'net income', AVG((tips / hours) + 7.5) AS 'hourly wage' FROM `finance_ricks_shifts` WHERE type <> 'otb' GROUP BY DAYNAME(date), type ORDER BY DAYOFWEEK(date)";
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
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="/homebase/resources/css/ricks_shift_form.css">

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
					<option value='otb'>OTB</option>
				</select>
				<label for='hours'>Hours</label>
				<input id='hours' type='number' name='hours' min='0' max='10' step='0.01'/>
				<label for='tips'>Tips</label>
				<input id='tips' type='number' name='tips' min='0' max='500' step='1'/>
				<label for='stress'>Stress (Optional)</label>
				<input id='stress' type='number' name='stress' min='1' max='10' step='1'/>
				<label for='enjoyment'>Enjoyment (Optional)</label>
				<input id='enjoyment' type='number' name='enjoyment' min='1' max='10' step='1'/>
				<label for='description'>Description (Optional)</label>
				<textarea id='description' name='description' maxlength='255' placeholder='Section 205. Very Busy. Worked with awesome crew...'></textarea>
				<button type="submit">Submit</button>
			</form>
			
			<section class='shift-averages' style='display: inline-flex; justify-content: space-between; flex-flow: row wrap; padding: 0;'>
<?php
			foreach ($shifts as $s) {
				echo "	<div style='' class='$s[1] day-avg-summary'>
							<h3><span style='font-weight: 900;'>$s[0]</span> - $s[1]</h3>
							<h4 style='padding: 1rem; font-size: 0.75rem;'>$<span style='font-weight: 900;'>$s[2]</span>/shift</h4>
							<h4 style='font-size: 0.75rem;'>$<span style='font-weight: 900;'>$s[3]</span>/hour</h4>
						</div>";
			}
?>
			</section>

			<table class='log' id='ricks-shifts-table'>
				<thead>
					<tr>
						<th style='width:15%;'>Date</th>
						<th>Day</th>
						<th>Type</th>
						<th>Hours</th>
						<th>Tips</th>
						<th>Hourly</th>
						<th>Net</th>
						<th><i class="far fa-tired"></i></th>
						<th><i class="far fa-smile-beam"></i></th>
						<th><i class="fas fa-scroll"></i></th>
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