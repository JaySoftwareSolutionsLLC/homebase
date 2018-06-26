<?php 
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');

$entry_msg = "Welcome to the Seal & Design income submission page.";

// If variables have been posted insert into db
if(isset($_POST['date']) && isset($_POST['type']) && isset($_POST['amount'])) {
	$qry = "INSERT INTO `finance_seal_income`(`date`,`type`,`amount`)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['type'] . "', '" . $_POST['amount'] . "');";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT date, type, amount FROM finance_seal_income ORDER BY date DESC LIMIT 10;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['date'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>$" . $row['amount'] . "</td>
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
	
		<h1>Seal & Design Income</h1>
		<h2 class='msg'><?php echo $entry_msg ?></h2>
		
		<form method='post'>
			<label for='date'>Date</label>
			<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
			<label for='type'>Type</label>
			<input id='type' type='string' name='type'/>
			<label for='amount'>Amount</label>
			<input id='amount' type='number' name='amount' step='0.01' min='0'/>
			<button type="submit">Submit</button>
		</form>
		
		<table class='log'>
			<tr>
				<th colspan='6'>Recent Seal & Design Income</th>
			</tr>
			<tr>
				<th>Date</th>
				<th>Type</th>
				<th>Amount</th>
			</tr>
			<?php echo $data_log; ?>
		</table>
		
	</main>
</body>

</html>