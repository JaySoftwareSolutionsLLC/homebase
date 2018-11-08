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
/* DEPRECATED QUERY */
/*
$qry = "SELECT 
			fe.id, fe.name, fe.current_weight, fe.description, 
				COALESCE((SELECT total_reps FROM fitness_lifts AS fl WHERE fl.exercise_id=fe.id AND fl.workout_structure_id = $current_workout_structure_id AND fl.weight=fe.current_weight ORDER BY total_reps DESC LIMIT 1), 0) AS 'target_reps',
				(SELECT datetime FROM `fitness_lifts` WHERE exercise_id IN (SELECT exercise_id FROM `fitness_pivot_exercises_muscles` WHERE muscle_id IN (SELECT muscle_id FROM `fitness_pivot_exercises_muscles`
		WHERE exercise_id = fe.id) 
		AND type = 'primary') 
		ORDER BY datetime DESC LIMIT 1) AS 'mrf_associated_muscles' 
		FROM fitness_exercises AS fe";
*/
$qry = "SELECT 
			fe.id, fe.name, fe.description, 
				(SELECT datetime FROM `fitness_lifts` WHERE exercise_id IN 
					(SELECT exercise_id FROM `fitness_pivot_exercises_muscles` WHERE muscle_id IN 
						(SELECT muscle_id FROM `fitness_pivot_exercises_muscles` WHERE exercise_id = fe.id) AND type = 'primary') ORDER BY datetime DESC LIMIT 1) AS 'mrf_associated_muscles',
            IFNULL(fbl.weight, 0) AS 'best_weight',
            IFNULL(fbl.total_reps, 0) AS 'best_total_reps'
		FROM fitness_exercises AS fe 
		LEFT JOIN fitness_best_lifts AS fbl 
			ON (fe.id = fbl.exercise_id AND $current_workout_structure_id = fbl.workout_structure_id)
		ORDER BY fe.name";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
		$this_exercise = new stdClass();
		$this_exercise->id = $row['id'];
		$this_exercise->name = $row['name'];
		$this_exercise->best_weight = $row['best_weight'];
		$this_exercise->best_total_reps = $row['best_total_reps'];
		$this_exercise->mrf_assoc_muscles = $row['mrf_associated_muscles'];
		$this_exercise->description = $row['description'];
		
		$exercises[] = $this_exercise;
	}
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
	/* REWRITE REQUIRED DUE TO WEIGHT FIELD LOCATION MOVED */
	/*
	if ($_POST['weight'] >= $this_ex->current_weight && $_POST['total-reps'] >= ($set_per_exercise * $reps_per_set)) {
		// TEST PASSED echo "Record Weight.<br/>";
		$new_weight = ($_POST['weight'] + 2.5);
		$qry = "UPDATE `fitness_exercises` SET `current_weight` = $new_weight WHERE `fitness_exercises`.`id` = $this_ex->id";
		// TEST PASSED echo $qry;
		if ($conn->query($qry) === TRUE) {
    	$entry_msg .= "Current weight set to $new_weight. For $this_ex->name </br>";
		} else {
    	$entry_msg .= "Error with query: $qry </br> $conn->error </br>";
		}
	}
	*/
	
}

$lifts = array();
$qry = " SELECT fl.datetime, 
				fm.common_name,
				fm.id AS 'muscle_id',
				fe.name AS 'ex-name',
				fe.id AS 'ex-id',
				fws.name AS 'ws-name', 
				fws.id AS 'ws-id', 
				fl.weight, 
				fl.total_reps 
			FROM fitness_lifts AS fl 
			INNER JOIN fitness_exercises AS fe 
				ON (fl.exercise_id = fe.id) 
			INNER JOIN fitness_workout_structures AS fws 
				ON (fl.workout_structure_id = fws.id) 
			INNER JOIN fitness_pivot_exercises_muscles AS fpem 
				ON (fe.id = fpem.exercise_id) 
			INNER JOIN fitness_muscles AS fm 
				ON (fpem.muscle_id = fm.id) ORDER BY fl.datetime DESC 
		";
$res = $conn->query($qry);
if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
		$this_lift = new stdClass();
		$this_lift->datetime = $row['datetime'];
		$this_lift->muscle = $row['common_name'];
		$this_lift->muscle_id = $row['muscle_id'];
		$this_lift->exercise = $row['ex-name'];
		$this_lift->exercise_id = $row['ex-id'];
		$this_lift->structure = $row['ws-name'];
		$this_lift->structure_id = $row['ws-id'];
		$this_lift->weight = $row['weight'];
		$this_lift->reps = $row['total_reps'];
		
		$lifts[] = $this_lift;
	}
	foreach ($lifts as $l) {
		$data_log .= "<tr>
					<td>" . $l->datetime . "</td>
					<td>" . $l->muscle . " (#$l->muscle_id)</td>
					<td>" . $l->exercise . "(#$l->exercise_id)</td>
					<td>" . $l->structure . "(#$l->structure_id)</td>
					<td>" . $l->weight . "</td>
					<td>" . $l->reps . "</td>
				</tr>";
	}
	
	// TEST PASSED var_dump($exercises);
}

$conn->close();

// Link to Style Sheets
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php');

?>

	<body>
		<main>

			<h1>Log Lift</h1>
			<h2 class='msg'><?php echo $entry_msg ?></h2>
			<p class='exercise-notes' style='width: 60%; background: hsl(190, 100%, 50%); color: white; padding: 0.5rem; font-size:0.75rem; margin: 1rem 0; border-radius: 0.25rem 0.75rem;'>Exercise notes populate here</p>
			<form method='post'>
				<label for='datetime'>Date</label>
				<input id='datetime' type='datetime-local' name='datetime'/>
				<label for='workout-structure-id'>Structure ID</label>
				<input id='workout-structure-id' type='number' name='workout-structure-id' value='<?php echo $current_workout_structure_id; ?>' min='1'/>
				<label for='exercise-id'>Exercise</label>
				<select name='exercise-id'>
<?php
	foreach( $exercises as $e ) {
		echo "<option type='text' data-target-reps='$e->best_total_reps' data-current-weight='$e->best_weight' data-description='$e->description' value='$e->id'>$e->name</option>";
	}		
?>
				</select>
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
					let description = $(this).find(':selected').data('description');
					if (description == '') {
						description = 'No notes yet';
					}
					console.log(description);
					$('input#weight').attr('value', weight);
					$('input#total-reps').attr('value', targetReps);
					$('p.exercise-notes').html(`${description}`);
				});
			</script>
<?php

?>
			<table class='log' id='lifts-table'>
				<thead>
					<tr>
						<th>Date</th>
						<th>Muscle</th>
						<th>Exercise</th>
						<th>Structure</th>
						<th>Weight</th>
						<th>Reps</th>
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
					$('#lifts-table').DataTable( {
						"order": [[ 0, "desc" ]]
					} );
				} );
			</script>
	</body>