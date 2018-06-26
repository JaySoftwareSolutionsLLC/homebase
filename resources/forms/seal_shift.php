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
if(isset($_POST['date']) && isset($_POST['arrival_time']) && isset($_POST['departure_time'])) {
	$qry = "INSERT INTO `finance_seal_shifts`(`date`,`arrival_time`,`departure_time`,`strain`,`feedback`,`stress`)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['arrival_time'] . "', '" . $_POST['departure_time'] . "', " . $_POST['strain'] . ", " . $_POST['feedback'] . ", " . $_POST['stress'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT 	date, 
				DAYNAME(date) AS 'dow', 
				arrival_time, 
				departure_time,
				((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) AS 'hours',
				strain, 
				feedback, 
				stress 
		FROM finance_seal_shifts ORDER BY date DESC LIMIT 10;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['date'] . "</td>
						<td>" . substr($row['dow'], 0, 3) . "</td>
						<td>" . substr($row['arrival_time'], 0, 5) . "</td>
						<td>" . substr($row['departure_time'], 0, 5) . "</td>
						<td>" . round($row['hours'], 2) . "</td>
						<td>" . $row['strain'] . "</td>
						<td>" . $row['feedback'] . "</td>
						<td>" . $row['stress'] . "</td>
						</tr>";
    }
}

$conn->close();

?>
<!-- Link to style sheets -->
<link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../css/reset.css">
<link rel="stylesheet" type="text/css" href="../css/main.css">
<link rel="stylesheet" type="text/css" href="../css/form.css">

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
			<label for='strain'>Strain</label>
			<input id='strain' type='number' name='strain' min='1' max='10' step='1'/>
			<label for='feedback'>Feedback</label>
			<input id='feedback' type='number' name='feedback' min='1' max='10' step='1'/>
			<label for='stress'>Stress</label>
			<input id='stress' type='number' name='stress' min='1' max='10' step='1'/>
			<button type="submit">Submit</button>
		</form>
		
		<table class='log'>
			<tr>
				<th colspan='6'>Recent Seal & Design Shifts</th>
			</tr>
			<tr>
				<th>Date</th>
				<th>Day</th>
				<th>Arr.</th>
				<th>Dep.</th>
				<th>Hrs</th>
				<th>Strain</th>
				<th>Feedback</th>
				<th>Stress</th>
			</tr>
			<?php echo $data_log; ?>
		</table>
		
	</main>
</body>

</html>