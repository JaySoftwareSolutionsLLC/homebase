<?php 
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$start_date = date('Y/m/d', strtotime('June 1st 2018'));
$today = date('Y-m-d');
$types_of_expenses; // pull this from DB enumerated field of type

$entry_msg = "Welcome to the expenses page.";

// If variables have been posted insert into db
if(isset($_POST['date']) && isset($_POST['name']) && isset($_POST['type']) && isset($_POST['amount'])) {
	$qry = "INSERT INTO finance_expenses (date, name, type, subtype, amount)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['name'] . "', '" . $_POST['type'] . "', '" . $_POST['subtype'] . "', " . $_POST['amount'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}

$qry = "SELECT name, date, type, subtype, amount FROM finance_expenses WHERE date >= '$start_date' ORDER BY date DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['name'] . "</td>
						<td>" . $row['date'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $row['subtype'] . "</td>
						<td>$" . $row['amount'] . "</td>
						</tr>";
    }
}
$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>


	<body>
		<main>

			<h1>Expense Form</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='date'>Date</label>
				<input id='date' type='date' name='date' value="<?php echo $today;?>"/>
				<label for='name'>Name</label>
				<input id='name' type='text' name='name' placeholder='Wegmans groceries' autocomplete/>
				<label for='type'>Type</label>
				<select id='type' name='type'>
					<option value='food'>Food</option>
					<option value='gas'>Gas</option>
					<option value='entertainment'>Entertainment</option>
					<option value='clothing'>Clothing</option>
					<option value='housing'>Housing</option>
					<option value='educational'>Education</option>
					<option value='personal'>Personal</option>
					<option value='health'>Health</option>
					<option value='giving'>Giving</option>
					<option value='bill'>Bill</option>
					<option value='auto'>Auto</option>
					<option value='travel'>Travel</option>
					<option value='fee'>Fee</option>
					<option value='office'>Office</option>
					<option value='tax'>Tax</option>
				</select>
				<label for='subtype'>Subtype (optional)</label>
				<input id='subtype' type='text' name='subtype'/>
				<label for='amount'>Amount</label>
				<input id='amount' type='number' name='amount' min='0' step='0.01'/>
				<button type="submit">Submit</button>
			</form>

			<table class='log' id='expenses-table'>
				<thead>
					<tr>
						<th>Name</th>
						<th>Date</th>
						<th>Type</th>
						<th>Subtype</th>
						<th>Amount</th>
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
				$('#expenses-table').DataTable();
			} );
		</script>
	</body>