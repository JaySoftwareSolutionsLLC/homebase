<?php 

function connect_to_local_db() {
	date_default_timezone_set('America/New_York');
	$serv = 'localhost';
	$user = 'root';
	$pass = 'Bc6219bAj';
	$db = 'jaysoftw_homebase';
	return new mysqli($serv, $user, $pass, $db);
}

function connect_to_db() {
	date_default_timezone_set('America/New_York');
	$serv = 'localhost';
	$user = 'jaysoftw_brett';
	$pass = 'Su944jAk127456';
	$db = 'jaysoftw_homebase';
	return new mysqli($serv, $user, $pass, $db);
}

function set_post_value($string) {
	return (isset($_POST[$string]) && ($_POST[$string]) != '') ? $_POST[$string] : null;
}

function post_is_set($string) {
	return (isset($_POST[$string]) && $_POST[$string] != '') ? true : false;
}

// Return an HTML Div with classes and ids to display progress towards a goal as well percent of time frame used
// Tested this out with a gaining goal and a losing goal with negative and positive progress and all test cases passed
function return_goal_progress_bar_html( $goal_str, $goal_id_str, $starting_value, $target_value, $current_value, $starting_date_str, $target_date_str, $today_date_str = 'now' ) { 
	// Initialize the values that will be calculated
	$goal_percent_target = 0;
	$goal_percent_time_frame = 0;
	// Calculate goal progress
	$current_delta = $current_value - $starting_value;
	$target_delta = $target_value - $starting_value;
	$goal_percent_target = number_format( ( 100 * $current_delta / $target_delta ) , 2 );
	if ($goal_percent_target > 100) {
		$goal_percent_target = 100;
	}
	// Calculate percent of goal time frame
	$start_dt = new DateTime($starting_date_str);
	$target_dt = new DateTime($target_date_str);
	$today_dt = new DateTime($today_date_str);
	$total_time_frame = $start_dt->diff($target_dt)->days;
	$days_since_start = $start_dt->diff($today_dt)->days;
	$goal_percent_time_frame = number_format( ( 100 * $days_since_start / $total_time_frame ) , 2 );	
	
	$str = "<div class='goal' id='$goal_id_str'>
				<h3>$goal_str</h3>
				<div class='progress'>
					<div class='fill' style='width: $goal_percent_target%;' data-value='$goal_percent_target'>
						
					</div>
					<div class='target-fill' style='width: $goal_percent_time_frame%;'></div>
				</div>
				<h5>$goal_percent_target%</h5>
			</div>";
	return $str;
}

?>