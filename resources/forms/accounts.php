<?php 
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');

$entry_msg = "Welcome to the accounts page.";

// If variables have been posted insert into db
if(isset($_POST['date']) && isset($_POST['name']) && isset($_POST['value'])) {
	$qry = "INSERT INTO finance_account_log (date, account_id, value) VALUES ('" . $_POST['date'] . "','" . $_POST['name'] . "'," . $_POST['value'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$data_log = '';
$accounts = array();
$qry = " SELECT f_a.*, ( 	SELECT value
							FROM finance_account_log AS f_a_l
							WHERE f_a.id = f_a_l.account_id
							ORDER BY f_a_l.date DESC, f_a_l.id DESC
							LIMIT 1) AS 'most recent value', 
							( 	SELECT date
							FROM finance_account_log AS f_a_l
							WHERE f_a.id = f_a_l.account_id
							ORDER BY f_a_l.date DESC, f_a_l.id DESC
							LIMIT 1) AS 'most recent date' 
		FROM finance_accounts AS f_a
		WHERE f_a.closed_on IS NULL
		GROUP BY f_a.id, f_a.name, f_a.type, f_a.expected_annual_return ";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	while($row = $res->fetch_assoc()) {
		$account = new stdClass();
		$account->id = $row['id'];
		$account->name = $row['name'];
		$account->type = $row['type'];
		$account->expected_annual_return = $row['expected_annual_return'];
		$account->mrdate = $row['most recent date'];
		$account->mrval = $row['most recent value'];

		$data_log .= "<tr>
						<td>" . $account->name . "</td>
						<td>" . $account->mrdate. "</td>
						<td class='" . $acnt_type . "'>" . $account->mrval . "</td>
						<td>" . $account->type . "</td>
						<td>" . $account->expected_annual_return . "</td>
					</tr>";
		$accounts[] = $account;
	}
}

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

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
			<label for='name'>Account</label>
			<select name='name' id='name'>
<?php
	foreach ($accounts as $a) {
		echo "<option value='$a->id'>$a->name</option>";
	}
?>
			</select>
			<label for='date'>Date</label>
			<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
			<label for='value'>Value</label>
			<input id='value' type='number' name='value' min='0' step='1'/>
			<button type="submit">Submit</button>
		</form>
		
		<table id='accounts-log-table' class='log'>
			<thead>
				<tr>
					<th colspan='5'>Recent Account Entries</th>
				</tr>
				<tr>
					<th>Name</th>
					<th>Date</th>
					<th>Value</th>
					<th>Type</th>
					<th>Exp. ROI</th>
				</tr>
			</thead>
			<tbody>
			<?php echo $data_log; ?>
			</tbody>
		</table>
	</main>
<?php
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
?>
	<script>
		$(document).ready( function () {
				
				$('#accounts-log-table').DataTable( {
					"order": [[ 1, "desc" ]]
				} );

		});
	</script>
</body>