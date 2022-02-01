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
	$qry = "INSERT INTO finance_expenses (date, name, type, subtype, amount, jss_percentage)
	VALUES ('" . $_POST['date'] . "', '" . $_POST['name'] . "', '" . $_POST['type'] . "', '" . $_POST['subtype'] . "', " . $_POST['amount'] . ", " . $_POST['jss_percentage'] . ");";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully";
	} else {
    	$entry_msg = "Error with query: $qry <br> $conn->error";
	}
}
$qry_common_expenses = "SELECT 	fe.name
								, fe.type
								, fe.jss_percentage
								, COUNT(*) AS 'Occurances'
						FROM `finance_expenses` fe
						WHERE fe.date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
						GROUP BY fe.name, fe.type, fe.jss_percentage
						HAVING COUNT(*) >= 3
						ORDER BY Occurances DESC;";
$res_common_expenses = $conn->query($qry_common_expenses);
if ($res_common_expenses->num_rows > 0) {
	$common_expenses_data_list = '<datalist id="common-expenses">';
	while($row_common_expenses = $res_common_expenses->fetch_assoc()) {
        $common_expenses_data_list .= "<option value='" . $row_common_expenses['name'] . "' data-type='" . $row_common_expenses['type'] . "' data-jss-percentage='" . $row_common_expenses['jss_percentage'] . "'>" . $row_common_expenses['name'] . "(" . $row_common_expenses['type'] . "~" . $row_common_expenses['jss_percentage'] . ")</option>";
    }
	$common_expenses_data_list .= "</datalist>";
}

$qry = "SELECT name, date, type, subtype, amount, jss_percentage FROM finance_expenses WHERE date >= '$start_date' ORDER BY date DESC;";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	$data_log = '';
    while($row = $res->fetch_assoc()) {
        $data_log .= "<tr>
						<td>" . $row['name'] . "</td>
						<td>" . $row['date'] . "</td>
						<td>" . $row['type'] . "</td>
						<td>" . $row['subtype'] . "</td>
						<td style='text-align: right;'>$" . nf_omit_zeros($row['amount'], 2) . "</td>
						<td style='text-align: right;'>" . $row['jss_percentage'] . "</td>
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
				<?= $common_expenses_data_list; ?>
				<input id='name' type='text' name='name' placeholder='Wegmans groceries' list='common-expenses' autocomplete/>
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
				<label for='jss_percentage'>JSS Percentage</label>
				<input id='jss_percentage' type='number' name='jss_percentage' min='0' step='0.01' value='0'/>
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
						<th>JSS %</th>
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
				$('#expenses-table').DataTable( {
					"order": [[ 1, "desc" ]]
				} );
				$("input#name").on('input', function () {
					var val = this.value;
					$('#common-expenses option').each(function(index) {
						if ($(this).val() == val) {
							$('select#type').val($(this).data('type'));
							$('input#jss_percentage').val($(this).data('jss-percentage'));
						}
					});
				});
			} );
		</script>
	</body>