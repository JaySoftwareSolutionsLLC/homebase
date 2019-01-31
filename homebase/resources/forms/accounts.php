<?php 
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');

$entry_msg = "Welcome to the accounts page.";

// If variables have been posted insert into db
if(isset($_POST['date']) && isset($_POST['name']) && isset($_POST['value']) && isset($_POST['type'])) {
	$qry = "INSERT INTO finance_accounts (date, name, value, type) VALUES ('" . $_POST['date'] . "','" . $_POST['name'] . "'," . $_POST['value'] . ",'" . $_POST['type'] . "');";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$all_account_names = array();
$oldest_date = "2050-01-01";
$current_cash = 0;
$current_assets = 0;
$current_liabilities = 0;

$qry = "SELECT name, MAX(date) AS date FROM finance_accounts GROUP BY name;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	while($row = $res->fetch_assoc()) {
		$all_account_names[$row['name']] = $row['date'];
	}
}

$data_log = '';
foreach ($all_account_names as $name => $date) {
	$qry = "SELECT name, date, value, type FROM finance_accounts WHERE name = '$name' AND date = '$date';";
	$res = $conn->query($qry);
	$row = $res->fetch_assoc();
	$acnt_type = $row['type'];
	$acnt_date = $row['date'];
	if ($acnt_type == 'cash') {
		$current_cash += $row['value'];
	}
	else if ($acnt_type == 'asset') {
		$current_assets += $row['value'];
	}
	else if ($acnt_type == 'liability') {
		$current_liabilities += $row['value'];
	}
	else {
		echo "ERROR IN DETERMINING ACCOUNT VALUES";
	}
	if ($acnt_date < $oldest_date) {
		$oldest_date = $acnt_date;
	}
	$data_log .= "<tr>
						<td>" . $row['name'] . "</td>
						<td>" . $row['date'] . "</td>
						<td class='" . $acnt_type . "'>" . $row['value'] . "</td>
						<td>" . $row['type'] . "</td>
						</tr>";
}

$conn->close();

?>
<!-- Link to style sheets -->
<link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../css/reset.css">
<link rel="stylesheet" type="text/css" href="../css/main-new.css">
<link rel="stylesheet" type="text/css" href="../css/form.css">

<body>
	<main>
	
		<h1>Accounts Form</h1>
		<h2 class='msg'><?php echo $entry_msg ?></h2>
		
		<form method='post'>
			<label for='name'>Name</label>
			<input id='name' type='text' name='name' placeholder='BoA savings' autocomplete/>
			<label for='date'>Date</label>
			<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
			<label for='value'>Value</label>
			<input id='value' type='number' name='value' min='0' step='1'/>
			<label for='type'>Type</label>
			<select id='type' name='type'>
				<option value='cash'>Cash</option>
				<option value='asset'>Asset</option>
				<option value='liability'>Liability</option>
			</select>
			<button type="submit">Submit</button>
		</form>
		
		<table class='log'>
			<tr>
				<th colspan='4'>Recent Account Entries</th>
			</tr>
			<tr>
				<th>Name</th>
				<th>Date</th>
				<th>Value</th>
				<th>Type</th>
			</tr>
			<?php echo $data_log; ?>
		</table>
	</main>
</body>