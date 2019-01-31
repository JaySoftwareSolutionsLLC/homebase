<?php

//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------

	$ex_id = $_POST['exercise-id'];
		
//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

	$exercise = new stdClass();
	$exercise->id = $ex_id;
	$exercise->p_muscles = array();
	$exercise->s_muscles = array();
	$exercise->equipments = array();
	$exercise->workout_structures = array();

	$q = " SELECT * FROM `fitness_exercises` WHERE id = $exercise->id ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		$row = $res->fetch_assoc();
		$exercise->name = $row['name'];
		$exercise->ref_url = $row['reference_url'];
		$exercise->desc = $row['description'];
	}

	$q = " SELECT fpem.muscle_id, fpem.type
			FROM `fitness_pivot_exercises_muscles` AS fpem
			INNER JOIN `fitness_muscles` AS fm
				ON (fpem.muscle_id = fm.id)
			WHERE fpem.exercise_id = $exercise->id ";
	//echo $q;
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			if ($row['type'] == 'primary') {
				$exercise->p_muscles[] = $row['muscle_id'];
			}
			else if ($row['type'] == 'secondary') {
				$exercise->s_muscles[] = $row['muscle_id'];
			}
		}
	}
	$q = " 	SELECT equipment_id
			FROM fitness_pivot_exercises_equipment
			WHERE exercise_id = $exercise->id ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$exercise->equipments[] = $row['equipment_id'];
		}
	}
	$q = " 	SELECT fws.id, fws.name, IFNULL(fbl.weight, 0) AS 'best_weight', IFNULL(fbl.total_reps, 0) AS 'best_total_reps'
			FROM fitness_workout_structures AS fws
			LEFT JOIN fitness_best_lifts AS fbl
				ON (fws.id = fbl.workout_structure_id
				AND $exercise->id = fbl.exercise_id) ";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$wos = new stdClass();
			$wos->id = $row['id'];
			$wos->name = $row['name'];
			$wos->best_weight = $row['best_weight'];
			$wos->best_total_reps = $row['best_total_reps'];
			
			$exercise->workout_structures[] = $wos;
		}
	}

	echo json_encode($exercise);

?>