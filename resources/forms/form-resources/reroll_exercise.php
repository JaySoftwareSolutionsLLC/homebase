<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------
	$muscle_id = 						$_POST['muscle-id'] ?? 0;
	$exercise_id = 						$_POST['exercise-id'] ?? 0;
	$muscle_idealness = 				$_POST['muscle-idealness'] ?? 0;

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

	$q = " SELECT fe.id, fe.name, fpem.muscle_id, fe.current_weight, fm.common_name, fe.reference_url
			FROM `fitness_exercises` AS fe
			INNER JOIN `fitness_pivot_exercises_muscles` AS fpem
				ON (fe.id = fpem.exercise_id)
			INNER JOIN `fitness_muscles` AS fm 
				ON (fpem.muscle_id = fm.id) 
			WHERE fpem.muscle_id = $muscle_id
				AND fe.id <> $exercise_id 
			ORDER BY RAND()
			LIMIT 1 ";
	$res = $conn->query($q);
	$row = mysqli_fetch_array($res);
	
	$new_exercise_muscle_id = $row['muscle_id'];
	$new_exercise_muscle_name = $row['common_name'];
	$new_exercise_id = $row['id'];
	$new_exercise_name = $row['name'];
	$new_exercise_url = $row['reference_url'];
	$new_exercise_cur_weight = $row['current_weight'];

	$out_str .= "<i class='reroll fas fa-sync-alt' data-muscle-id='$new_exercise_muscle_id' data-exercise-id='$new_exercise_id' data-muscle-idealness='$muscle_idealness'></i> &nbsp; $new_exercise_muscle_name ($muscle_idealness%) - ";
	if ( ! empty( $new_exercise_url ) ) {
		$out_str .= "<a href='$new_exercise_url' target='_blank'>" . $new_exercise_name . "</a>";
	}
	else {
		$out_str .= $new_exercise_name;
	}
	$out_str .= " @ $new_exercise_cur_weight";

	echo $out_str;

?>