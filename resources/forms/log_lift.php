<?php
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$today = date('Y-m-d');

$entry_msg = "Welcome to the workout submission page.";
$data_log = '';

$qry = "SELECT workout_structure_id FROM fitness_cycles WHERE start_date <= '$today' AND end_date >= '$today'";
// TEST PASSED echo $qry;
$res = $conn->query($qry);
if ($res->num_rows > 0) {
	while($row = $res->fetch_assoc()) {
		$current_workout_structure_id = $row['workout_structure_id'];
		// TEST PASSED echo $current_workout_structure_id;
	}
}

$qry = "SELECT * FROM fitness_workout_structures WHERE id = $current_workout_structure_id";
$res = $conn->query($qry);
$row = $res->fetch_assoc();
$set_per_exercise = $row['sets_per_exercise'];
$reps_per_set = $row['reps_per_set'];


// Select all of the exercises from fitness and store each of them server side as an object inside of an array
$exercises = array();
$qry = "SELECT 
			fe.id, fe.name, fe.current_weight, 
				COALESCE((SELECT total_reps FROM fitness_lifts AS fl WHERE fl.exercise_id=fe.id AND fl.workout_structure_id = $current_workout_structure_id AND fl.weight=fe.current_weight ORDER BY total_reps DESC LIMIT 1), 0) AS 'target_reps', 
				(SELECT datetime FROM `fitness_lifts` WHERE exercise_id IN (SELECT exercise_id FROM `fitness_pivot_exercises_muscles` WHERE muscle_id IN (SELECT muscle_id FROM `fitness_pivot_exercises_muscles`
		WHERE exercise_id = fe.id) 
		AND type = 'primary') 
		ORDER BY datetime DESC LIMIT 1) AS 'mrf_associated_muscles' 
		FROM fitness_exercises AS fe";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
		$this_exercise = new stdClass();
		$this_exercise->id = $row['id'];
		$this_exercise->name = $row['name'];
		$this_exercise->current_weight = $row['current_weight'];
		$this_exercise->target_reps = $row['target_reps'];
		$this_exercise->mrf_assoc_muscles = $row['mrf_associated_muscles'];
		/*
		$subqry = "SELECT total_reps FROM `fitness_lifts` WHERE exercise_id='$this_exercise->id' AND workout_structure_id='$current_workout_structure_id' AND weight='$this_exercise->current_weight' ORDER BY total_reps DESC LIMIT 1";
		// TEST echo $subqry;
		$subres = $conn->query($subqry);
		if ($subres->num_rows > 0) {
    		while($subrow = $subres->fetch_assoc()) {
				$this_exercise->total_reps = $subrow['total_reps'];
			}
		}
		*/
		$exercises[] = $this_exercise;
	}
	foreach ($exercises as $ex) {
		$data_log .= "<tr>
					<td>" . $ex->name . "</td>
					<td>" . $ex->mrf_assoc_muscles . "</td>
					<td>" . substr($row['arrival_time'], 0, 5) . "</td>
					<td>" . substr($row['departure_time'], 0, 5) . "</td>
					<td>" . round($row['hours'], 2) . "</td>
					<td>" . $row['strain'] . "</td>
					<td>" . $row['feedback'] . "</td>
					<td>" . $row['stress'] . "</td>
				</tr>";
	}
	
	// TEST PASSED var_dump($exercises);
}

if (isset($_POST['datetime']) && isset($_POST['workout-structure-id']) && isset($_POST['total-reps']) && isset($_POST['exercise-id']) && isset($_POST['weight'])) {
	$qry = "INSERT INTO `fitness_lifts`(`exercise_id`,`workout_structure_id`,`total_reps`,`weight`,`datetime`)
	VALUES ('" . $_POST['exercise-id'] . "', '" . $_POST['workout-structure-id'] . "', '" . $_POST['total-reps'] . "', '" . $_POST['weight'] . "', '" . $_POST['datetime'] . "');";

	if ($conn->query($qry) === TRUE) {
    	$entry_msg = "New record created successfully <br/>";
	} else {
    	$entry_msg = "Error with query: $qry <br/> $conn->error <br/>";
	}
	
	$this_ex;
	foreach ($exercises as $ex) {
		if ($ex->id == $_POST['exercise-id']) {
			$this_ex = $ex;
			// TEST PASSED echo "Exercise Found.<br/>";
			// TEST PASSED var_dump($this_ex);
		}
	}
	if ($_POST['weight'] >= $this_ex->current_weight && $_POST['total-reps'] >= ($set_per_exercise * $reps_per_set)) {
		// TEST PASSED echo "Record Weight.<br/>";
		$new_weight = ($_POST['weight'] + 2.5);
		$qry = "UPDATE `fitness_exercises` SET `current_weight` = $new_weight WHERE `fitness_exercises`.`id` = $this_ex->id";
		// TEST PASSED echo $qry;
		if ($conn->query($qry) === TRUE) {
    	$entry_msg .= "Current weight set to $new_weight. </br>";
		} else {
    	$entry_msg .= "Error with query: $qry </br> $conn->error </br>";
		}
	}
	
}



        
 


$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>

	<body>
		<main>

			<h1>Log Lift</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>

			<form method='post'>
				<label for='datetime'>Date</label>
				<input id='datetime' type='datetime-local' name='datetime'/>
				<label for='exercise-id'>Exercise</label>
				<select name='exercise-id'>
<?php
	foreach( $exercises as $e ) {
		echo "<option type='text' data-target-reps='$e->target_reps' data-current-weight='$e->current_weight' value='$e->id'>$e->name</option>";
	}				
?>
				</select>
				<label for='workout-structure-id'>Structure ID</label>
				<input id='workout-structure-id' type='number' name='workout-structure-id' value='<?php echo $current_workout_structure_id; ?>' min='1'/>
				<label for='total-reps'>Total Reps</label>
				<input id='total-reps' type='number' name='total-reps' value='0' min='1'/>
				<label for='weight'>Weight</label>
				<input id='weight' type='number' name='weight' min='0' step='.01'/>
				<button type="submit">Submit</button>
			</form>
			
			<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
			<script>
				$('select').on('change', function() {
					let thisSelection = $(this).find(':selected');
					console.log(thisSelection);
					let weight = $(this).find(':selected').data('current-weight');
					let targetReps = $(this).find(':selected').data('target-reps');
					console.log($('input#weight'));
					$('input#weight').attr('value', weight);
					$('input#total-reps').attr('value', targetReps);
				});
			</script>
<?php
/*
?>
			<table class='log' id='seal-and-design-shifts-table'>
				<thead>
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
				</thead>
				<?php echo $data_log; ?>
			</table>

		</main>
<?php
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/js-files.php');
?>
			<script>
				$(document).ready( function () {
					$('#seal-and-design-shifts-table').DataTable( {
						"order": [[ 0, "desc" ]]
					} );
				} );
			</script>
	</body>

</html>

?>
*/