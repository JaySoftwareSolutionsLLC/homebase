<?php

//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------

	$ex_id = $_POST['exercise-id'];
	$wo_str = $_POST['workout-structure'];
		
//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();
	$q = " SELECT * 
			FROM fitness_best_lifts AS fbl
			WHERE 	fbl.exercise_id = $ex_id
				AND fbl.workout_structure_id = $wo_str ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		$row = mysqli_fetch_array($res);
	}
	else {
		echo "N/A";
		exit;
	}
	
	$best_lift = new stdClass();
	$best_lift->weight = $row['weight'];
	$best_lift->total_reps = $row['total_reps'];

	echo json_encode($best_lift);

?>