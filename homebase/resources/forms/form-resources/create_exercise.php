<?php

//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------

	$new_exercise = json_decode($_POST['new-exercise'], true);
	//var_dump( $new_exercise );
	//exit;
		
//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();


	// Insert the exercise into the fitness_exercise table
	$qry_i = "INSERT INTO fitness_exercises (name)
	VALUES ( '" . $new_exercise['name'] . "' )";
	if ($conn->query($qry_i) === TRUE) {
		$return_str = "Query to insert into fitness_exercises went through.<br/>";
	} else {
    	echo "Error with query: $qry_i <br/> $conn->error <br/>";
		exit;
	}
	//echo $qry_i . "<br/>";

	// Select the newly created id from the fitness_exercise table
	$qry_s = "SELECT id FROM fitness_exercises WHERE name = '" . $new_exercise['name'] . "' ";
	$res_s = $conn->query($qry_s);
	if ($res_s->num_rows > 0) {
		$row_s = $res_s->fetch_assoc();
		$new_exercise_id = $row_s['id'];
		$return_str .= "New exercise ID equal to $new_exercise_id.<br/>";
	}
	else {
		$return_str .= "Error with query: $qry_s <br/> $conn->error <br/>";
		echo $return_str;
		exit;
	}
	//echo $qry_s . "<br/>";

	// Insert primary muscle rows into the fitness_pivot_exercises_muscles
	$qry_i = "INSERT INTO fitness_pivot_exercises_muscles (exercise_id, muscle_id, type) VALUES ";
	$p_muscle_count = count($new_exercise['p_muscles']);
	$i = 0;
	foreach($new_exercise['p_muscles'] AS $pm) {
		$qry_i .= " ( $new_exercise_id, $pm, 'primary') ";
		$i++;
		if ($i != $p_muscle_count) {
			$qry_i .= ",";
		}
	}
	if ($conn->query($qry_i) === TRUE) {
		$return_str .= "Query to insert primaries into fitness_pivot_exercises_muscles worked.<br/>";
	} else {
    	$return_str .= "Error with query: $qry_i <br/> $conn->error <br/>";
		echo $return_str;
		exit;
	}
	//echo $qry_i . "<br/>";

	// Insert secondary muscle rows into the fitness_pivot_exercises_muscles
	if ( ! empty( $new_exercise['s_muscles'] ) ) {
		$qry_i = "INSERT INTO fitness_pivot_exercises_muscles (exercise_id, muscle_id, type) VALUES ";
		$s_muscle_count = count($new_exercise['s_muscles']);
		$i = 0;
		foreach($new_exercise['s_muscles'] AS $sm) {
			$qry_i .= " ( $new_exercise_id, $sm, 'secondary') ";
			$i++;
			if ($i != $s_muscle_count) {
				$qry_i .= ",";
			}
		}
		if ($conn->query($qry_i) === TRUE) {
			$return_str .= "Query to insert into fitness_pivot_exercises_muscles worked.<br/>";
		} else {
			$return_str .= "Error with query: $qry_i <br/> $conn->error <br/>";
			echo $return_str;
			exit;
		}
	}
	//echo $qry_i . "<br/>";

	// Insert rows into the fitness_pivot_exercises_equipments
	$qry_i = "INSERT INTO fitness_pivot_exercises_equipment (exercise_id, equipment_id) VALUES ";
	$equipment_count = count($new_exercise['equipments']);
	$i = 0;
	foreach($new_exercise['equipments'] AS $e) {
		$qry_i .= " ( $new_exercise_id, $e ) ";
		$i++;
		if ($i != $equipment_count) {
			$qry_i .= ",";
		}
	}
	if ($conn->query($qry_i) === TRUE) {
		$return_str .= "Query to insert into fitness_pivot_exercises_equipment worked.<br/>";
	} else {
    	$return_str .= "Error with query: $qry_i <br/> $conn->error <br/>";
		echo $return_str;
		exit;
	}
	//echo $qry_i;

	echo "success";

?>