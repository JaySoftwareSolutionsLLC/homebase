<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------
	$muscle_id = 						$_POST['muscle-id'] ?? 0;
	$exercise_id = 						$_POST['exercise-id'] ?? 0;
	$muscle_idealness = 				$_POST['muscle-idealness'] ?? 0;
	$workout_structure = 				$_POST['workout-structure'] ?? 0;
	$equipments =						unserialize($_POST['equipments']) ?? 0;
	//echo $equipments;
	//exit;

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

	$q = " SELECT fe.id, fe.name, fpem.muscle_id, fm.common_name, fe.reference_url, IFNULL(fbl.weight, 0) AS 'best_weight', IFNULL(fbl.total_reps, 0) AS 'best_total_reps'
			FROM `fitness_exercises` AS fe
			INNER JOIN `fitness_pivot_exercises_muscles` AS fpem
				ON (fe.id = fpem.exercise_id)
			INNER JOIN `fitness_muscles` AS fm 
				ON (fpem.muscle_id = fm.id)
			LEFT JOIN fitness_best_lifts AS fbl
            	ON (fe.id = fbl.exercise_id
                AND $workout_structure = fbl.workout_structure_id)
			WHERE fpem.muscle_id = $muscle_id
				AND fpem.type = 'primary'
				AND fe.id <> $exercise_id
				AND ((SELECT COUNT(*) FROM fitness_pivot_exercises_equipment WHERE exercise_id = fe.id AND equipment_id NOT IN (" . implode(' , ', $equipments) . ")) = 0)
				
			ORDER BY RAND()
			LIMIT 1 ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		$row = mysqli_fetch_array($res);
	}
	else {
		echo "N/A";
		exit;
	}
	
	$new_exercise_muscle_id = $row['muscle_id'];
	$new_exercise_muscle_name = $row['common_name'];
	$new_exercise_id = $row['id'];
	$new_exercise_name = $row['name'];
	$new_exercise_url = $row['reference_url'];
	$new_exercise_best_weight = $row['best_weight'];
	$new_exercise_best_total_reps = $row['best_total_reps'];

	$out_str .= "<i class='reroll fas fa-sync-alt' data-muscle-id='$new_exercise_muscle_id' data-exercise-id='$new_exercise_id' data-muscle-idealness='$muscle_idealness'></i> &nbsp; $new_exercise_muscle_name ($muscle_idealness%) - ";
	if ( ! empty( $new_exercise_url ) ) {
		$out_str .= "<a href='$new_exercise_url' target='_blank'>" . $new_exercise_name . "</a>";
	}
	else {
		$out_str .= $new_exercise_name;
	}
	$out_str .= " @ $new_exercise_best_weight (x$new_exercise_best_total_reps)";

	echo $out_str;

?>