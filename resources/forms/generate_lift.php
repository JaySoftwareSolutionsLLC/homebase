<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---RETRIEVE POST VARIABLES--------------------------------------------------------
	$user_input_time_to_lift		= $_POST['time-to-lift'] ?? 0;
	$equipments = $_POST['equipments'] ?? array();
	// TEST PASSED 2018.11.08 var_dump($equipments);


//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
	$today_time = time();
	$today_date = date('Y-m-d');
	$today_datetime = new DateTime();
	$weight_redistribution_time = 120; // in seconds
	$workout_str = '';

// Retrieve all of the muscle info
	$q = "SELECT workout_structure_id FROM `fitness_cycles` WHERE start_date <= '$today_date' AND end_date >= '$today_date' LIMIT 1";
	$res = $conn->query($q);
	$row = mysqli_fetch_array($res);
	$workout_structure 				= $row['workout_structure_id'];
	//TEST PASSED echo "WORKOUT STRUCTURE: $workout_structure. <br/>";

	$q = "SELECT * FROM `fitness_workout_structures` WHERE id = $workout_structure";
	$res = $conn->query($q);
	$row = mysqli_fetch_array($res);
	$num_sets 						= $row['sets_per_exercise'];
	$reps_per_set 					= $row['reps_per_set'];
	$cadence 						= $row['cadence'];
	$rest							= $row['rest'];
	$workout_structure_name			= $row['name'];
	$estimated_sec_per_muscle		= ( ( $num_sets * ( $cadence * $reps_per_set ) ) + ( ($num_sets - 1) * $rest) ) + $weight_redistribution_time;
	$estimated_num_exercises 		= floor(($user_input_time_to_lift * 60) / $estimated_sec_per_muscle);

	//TEST PASSED echo "$workout_structure_name : <br/> $reps_per_set * $num_sets @ $cadence" . "sec/rep w/ $rest" . " sec/set. <br/>";
	
	//TEST PASSED echo "ESTIMATED TIME PER MUSCLE: $estimated_sec_per_muscle seconds. <br/>";

	//TEST PASSED echo "USER INPUT TIME TO LIFT: $user_input_time_to_lift minutes. <br/>";

	//TEST PASSED echo "ESTIMATED NUMBER OF EXERCISES: $estimated_num_exercises. <br/>";

	$muscle_objects = array();
	$q = "SELECT 
	muscles.id, muscles.common_name, circs.id AS 'circ_id', circs.name AS 'circ_name', circs.ideal, rec_times.ideal_recovery
	FROM `fitness_muscles` AS muscles 
	INNER JOIN `fitness_circumferences` AS circs ON (muscles.circumference_id = circs.id)
	INNER JOIN `fitness_ideal_recovery_times` AS rec_times ON (muscles.id = rec_times.muscle_id)
	WHERE rec_times.workout_structure_id = $workout_structure";
	$res = $conn->query($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			//var_dump($row);
			$muscle_id = 				$row['id'];
			$muscle_name = 				$row['common_name'];
			
			$muscle_associated_circ_id = $row['circ_id'];
			$muscle_associated_circ = 	$row['circ_name'];
			$muscle_ideal_circ = 		$row['ideal'];
			
			$muscle_ideal_rest =		$row['ideal_recovery'];
			
			$q_current_circ = 			"SELECT value FROM fitness_measurements_circumferences WHERE circumference_id = $muscle_associated_circ_id ORDER BY datetime DESC LIMIT 1";
			$res_current_circ = 		$conn->query($q_current_circ);
			$row_current_circ = 		mysqli_fetch_row($res_current_circ);
			$muscle_current_circ = 		$row_current_circ[0];
			
			$q_mrf = "SELECT datetime FROM `fitness_lifts` WHERE exercise_id IN (SELECT exercise_id FROM `fitness_pivot_exercises_muscles` WHERE muscle_id = $muscle_id AND type = 'primary') ORDER BY datetime DESC LIMIT 1";
			$res_mrf =					$conn->query($q_mrf);
			$row_mrf =					mysqli_fetch_row($res_mrf);
			$muscle_mrf_time =			strtotime($row_mrf[0]); // Most Recent Failure as datetime
			$muscle_mrf_hours =			ceil(($today_time - $muscle_mrf_time) / (60 * 60)); // Most Recent Failure as datetime
			if ($muscle_mrf_hours > 999) { $muscle_mrf_hours = 999; }
			
			$hur =						$muscle_ideal_rest - $muscle_mrf_hours;

			$percent_ideal = 			number_format((100 - ((($muscle_ideal_circ - $muscle_current_circ) / $muscle_ideal_circ) * 100)), 2);

			if ($percent_ideal <= 100) {
				$idealness_score = $percent_ideal;
			}
			else {
				$idealness_score = (200 - $percent_ideal);
			}
			
			$this_muscle =	 			new stdClass();
			$this_muscle -> id =		$muscle_id;
			$this_muscle -> name = 		$muscle_name;
			$this_muscle -> circ =		$muscle_associated_circ;
			$this_muscle -> curr_circ =	$muscle_current_circ;
			$this_muscle -> ideal_circ = $muscle_ideal_circ;
			$this_muscle -> ideal_rest = $muscle_ideal_rest;
			$this_muscle -> mrf =		$muscle_mrf_hours;
			$this_muscle -> hur = 		$hur;
			$this_muscle -> perc_ideal = $percent_ideal;
			
						
			$muscle_objects[] =			$this_muscle;		
		}
	}

	usort($muscle_objects, function($a, $b) {
		return ( intval( $a->perc_ideal ) >= intval( $b->perc_ideal ) );
	});

	$num_exercises 					= 0;
	$exercise_objects				= array();
	foreach($muscle_objects as $mo) {
		if ($mo->hur > 0) {
			continue;
		} 
		else {
			$q = "SELECT fm.common_name, fe.name, fe.reference_url, fe.description, fe.id, IFNULL(fbl.weight, 0) AS 'best_weight', IFNULL(fbl.total_reps, 0) AS 'best_total_reps'
					FROM `fitness_exercises` AS fe 
					INNER JOIN `fitness_pivot_exercises_muscles` AS p 
						ON (fe.id = p.exercise_id) 
					INNER JOIN `fitness_muscles` AS fm 
						ON (p.muscle_id = fm.id)
					LEFT JOIN fitness_best_lifts AS fbl
                    	ON (fe.id = fbl.exercise_id
                        AND $workout_structure = fbl.workout_structure_id)
					WHERE 	fm.id = $mo->id
                    	AND p.type = 'primary' 
                        AND ((SELECT COUNT(*) FROM fitness_pivot_exercises_equipment WHERE exercise_id = fe.id AND equipment_id NOT IN (" . implode(' , ', $equipments) . ")) = 0)
                    
                    ORDER BY RAND() LIMIT 1";
				//echo $q;
			/*
			$q = "SELECT fm.common_name, fe.name, fe.reference_url, fe.description, fe.id, IFNULL(fbl.weight, 0) AS 'best_weight', IFNULL(fbl.total_reps, 0) AS 'best_total_reps'
					FROM `fitness_exercises` AS fe 
					INNER JOIN `fitness_pivot_exercises_muscles` AS p 
						ON (fe.id = p.exercise_id) 
					INNER JOIN `fitness_muscles` AS fm 
						ON (p.muscle_id = fm.id)
					LEFT JOIN fitness_best_lifts AS fbl
                    	ON (fe.id = fbl.exercise_id
                        AND $workout_structure = fbl.workout_structure_id)
					WHERE fm.id = '" . $mo->id . "' AND p.type = 'primary' ORDER BY RAND() LIMIT 1";
			*/
			$res = $conn->query($q);
			if ($res->num_rows > 0) {
				$row = mysqli_fetch_array($res);
			}
			else {
				continue;
			}
			$exercise_name = $row['name'];
			$exercise_best_weight = $row['best_weight'];
			$exercise_best_total_reps = $row['best_total_reps'];
			$exercise_url = $row['reference_url'];
			$exercise_id = $row['id'];
			
			$this_exercise = 			new stdClass();
			$this_exercise -> id = 		$exercise_id;
			$this_exercise -> name =	$exercise_name;
			$this_exercise -> best_weight = $exercise_best_weight;
			$this_exercise -> best_total_reps = $exercise_best_total_reps;
			$this_exercise -> url = 	$exercise_url;
			$this_exercise -> muscle_name = $mo->name;
			$this_exercise -> muscle_per_ideal = $mo->perc_ideal;
			$this_exercise -> muscle_id = $mo->id;
			
			$exercise_objects[] =		$this_exercise;
		
			$num_exercises++;
		}
		if ($estimated_num_exercises <= $num_exercises) {
			break;
		}
	}
	usort($exercise_objects, function($a, $b) {
		return ( intval( $a->best_weight ) < intval( $b->best_weight ) );
	});
	
	
	foreach($exercise_objects as $eo) {
		$workout_str .= "<li><i class='reroll fas fa-sync-alt' data-muscle-id='$eo->muscle_id' data-exercise-id='$eo->id' data-muscle-idealness='$eo->muscle_per_ideal'></i> &nbsp; $eo->muscle_name ($eo->muscle_per_ideal%) - ";
		if ( ! empty( $eo->url ) ) {
			$workout_str .= "<a href='$eo->url' target='_blank'>" . $eo->name . "</a>";
		}
		else {
			$workout_str .= $eo->name;
		}
		$workout_str .= " @ $eo->best_weight (x$eo->best_total_reps)</li>";
	}
				
	$time_estimate = ceil(($num_exercises * $estimated_sec_per_muscle) / 60);

	$conn->close();

?>


<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="/homebase/resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <title>Generated Lift</title>
<?php include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/forms/form-resources/css-files.php'); ?>
    

</head>

<body>

	<main>
		
		<h1 data-workout-structure-id='<?php echo $workout_structure; ?>' data-equipments='<?php echo serialize($equipments); ?>'><?php echo $workout_structure_name; ?></h1>
		<h2 class='msg'><?php echo "($reps_per_set" . "reps * $num_sets" . "sets @ $cadence" . "sec/rep w/ $rest" . " sec rest between each set)"; ?></h2>
		<h3>Estimated Time: <?php echo $time_estimate; ?> minutes</h3>
		<ol style='width: 80%;'>
			<?php echo $workout_str; ?>
		</ol>
		
		<?php // TEST var_dump($muscle_objects); ?>
		
	</main>
	
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src='/homebase/resources/js/generate_lift.js' defer></script>

</body>