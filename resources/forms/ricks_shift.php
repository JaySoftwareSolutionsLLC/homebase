<?php 
// Include resources
include('../resources.php');
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

$qry = "SELECT DAYNAME(date) AS 'dow', date, type, hours, tips FROM finance_ricks_shifts ORDER BY date DESC LIMIT 10;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['date'] . "</td>
						<td>" . $row['dow'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $row['hours'] . "</td>
						<td>" . $row['tips'] . "</td>
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
	
		<h1>Rick's on Main Shift</h1>
		<h2 class='msg'><?php echo $entry_msg ?></h2>
		
		<form method='post'>
			<label for='date'>Date</label>
			<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
			<label for='type'>Type</label>
			<select id='type' name='type'>
				<option value='am'>AM</option>
				<option value='pm'>PM</option>
			</select>
			<label for='hours'>Hours</label>
			<input id='hours' type='number' name='hours' min='0' max='10' step='0.01'/>
			<label for='tips'>Tips</label>
			<input id='tips' type='number' name='tips' min='0' max='500' step='1'/>
			<button type="submit">Submit</button>
		</form>
		
		<table class='log'>
			<tr>
				<th colspan='5'>Recent Rick's on Main Shifts</th>
			</tr>
			<tr>
				<th>Date</th>
				<th>Day</th>
				<th>Type</th>
				<th>Hours</th>
				<th>Tips</th>
			</tr>
			<?php echo $data_log; ?>
		</table>
		
	</main>
</body>