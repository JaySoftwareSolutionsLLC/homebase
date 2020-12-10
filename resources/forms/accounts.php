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

$relevant_accounts = return_relevant_accounts_info_array($conn, $today);
hidden_var_dump( $relevant_accounts );
foreach ($relevant_accounts as $a_id => $a_name) {
	$str = "{        
		type: 'line',
		name: '$a_name',
		showInLegend: true,
		yValueFormatString: '$#,##0',
		dataPoints: [";
	$values = return_account_value_over_time($conn, '2019-01-01', $today, $a_id);
	//hidden_var_dump($values);
	foreach ($values as $date => $val) {
		$dt = new Datetime($date);
		//hidden_echo( $date . " | " . $val );
		$str .= "{ x: new Date(" . php_dt_to_js_datestr($dt) . "), y: $val },";
	}
	$str .= "]},";
	//echo $str;
}

//$conn->close();

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
			<input id='value' type='number' name='value' step='1'/>
			<button type="submit">Submit</button>
		</form>

		<section class='master-graph' style='margin: 1rem 0; padding: 0; width: 100%;'>
			<div id='accounts-master-graph' style='height: 20rem; width: 100%; display: inline-flex;'></div>
		</section>
		
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
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
	<script>
		$(document).ready( function () {
				
				$('#accounts-log-table').DataTable( {
					"order": [[ 1, "desc" ]]
				} );

				function singleClick(e){
				if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible)
					e.dataSeries.visible = false;
				else
					e.dataSeries.visible = true;
				e.chart.render();
				}

				function doubleClick(e){
					var data = e.chart.options.data;
					for(var i = 0; i < data.length; i++)
						data[i].visible = false;
					e.dataSeries.visible = true;
					e.chart.render();
				}

				var accountsMasterChart = new CanvasJS.Chart("accounts-master-graph", {
					title:{
						text: "Account Values"
					},
					toolTip: {
						enabled: true,
						shared: true,
						cornerRadius: 5,
						borderThickness: 3,
						borderColor: 'hsl(190, 100%, 50%)',
						contentFormatter: function ( e ) {
							let str = "<div style='font-size: 1.25rem; font-weight: 900;'>" + e.entries[0].dataPoint.x.toLocaleDateString('en-US') + "</div><br/>";
							var arr = [];
							for(var i = 0; i < e.entries.length; i++) {
								if(e.entries[i].dataSeries.visible) {
									let dataPointStr = " <span class='tooltip'><span style='font-weight: 900; color: " + e.entries[i].dataSeries.color + "' class=''>" + e.entries[i].dataSeries.name + " : &nbsp; &nbsp; &nbsp; </span>" + e.entries[i].dataPoint.y + "</span>";
									arr.push(dataPointStr);
								}
							}
							str += arr.join('<br/>');
							return str || 'No Expenditures';
						}  
					},
					legend: {
						cursor: "pointer",
						itemclick: function (e) {
							
							// If already clicked once
							if(e.chart.options.clicked){
								doubleClick(e);
								e.chart.options.clicked = false;
								clearTimeout(this.executeDoubleClick);
								return;
							}

							this.executeDoubleClick = setTimeout(function(){
							e.chart.options.clicked = false;
							singleClick(e);
							}, 500);

							e.chart.options.clicked = true;
						},
					},
					axisX: {
						intervalType: 'month',
						interval: 1
					},
					axisY: {
						interval: 5000,
						gridDashType: "dot"
					},
					data: [
<?php
	$relevant_accounts = return_relevant_accounts_info_array($conn, $today);
	foreach ($relevant_accounts as $a_id => $a_name) {
		$str = "{        
			type: 'line',
			name: '$a_name',
			showInLegend: true,
			yValueFormatString: '$#,##0',
			dataPoints: [";
		$values = return_account_value_over_time($conn, '2020-01-01', $today, $a_id);
		//hidden_var_dump($values);
		foreach ($values as $date => $val) {
			$dt = new Datetime($date);
			//hidden_echo( $date . " | " . $val );
			$str .= "{ x: new Date(" . php_dt_to_js_datestr($dt) . "), y: $val },";
		}
		$str .= "]},";
		echo $str;
	}

	$conn->close();
?>
					]
				});

				accountsMasterChart.render();

		});
	</script>
</body>