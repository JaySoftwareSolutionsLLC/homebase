<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

	$equipments = array();
	// Determine what all of the equipment options are
	$q = " SELECT * FROM fitness_equipment ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$e = new stdClass();
			$e->id = $row['id'];
			$e->name = $row['name'];
			
			$equipments[] = $e;
		}
	}
	
	$conn->close();
?>


<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <title>Lift Inputs</title>
<?php include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php'); ?>
	<link rel="stylesheet" type="text/css" href="../css/generate_lift_inputs.css">

</head>

<body>

	<main>
		
		<form action="/homebase/resources/forms/generate_lift.php" method="post">
			<label for='time-to-lift'>How many minutes do you have to lift?</label>
			<input type='number' placeholder='30' name='time-to-lift' autofocus>
			<div class='equipment-inputs'>
<?php
			foreach ($equipments as $e) {
				switch ($e->id) {
					case 5:
					case 6:
					case 7:
					case 10:
					case 11:
					case 12:
						$checked = false;
						break;
					
					default:
						$checked = true;
						break;
				}
				$str = "<span class='equipment-input ";
				$str .= $checked ? "checked" : "";
				$str .= "'>
							<label for='equipment-$e->id'>$e->name</label>
							<input type='checkbox' name='equipments[]' id='equipment-$e->id' value='$e->id' class='equipment' ";
				$str .= $checked ? "checked" : "";
				$str .= "></span>";
				echo $str;
			}
?>
			</div>
			<button type="submit">Generate</button>
		</form>
		
	</main>
	
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script>
		function changeColorOfEquipmentSpans() {
			$('input.equipment').each(function() {
				if ($(this).is(':checked')) {
					$(this).parent('span').addClass('checked');
				}
				else {
					$(this).parent('span').removeClass('checked');
				}
			})
		}
		changeColorOfEquipmentSpans();
		$('input.equipment').on('change', function() {
			changeColorOfEquipmentSpans();
		});
	</script>

</body>