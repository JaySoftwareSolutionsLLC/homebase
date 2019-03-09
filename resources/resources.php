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

// Return an HTML Div with classes and ids to display progress towards a goal as well as percent of time frame used
// Tested this out with a gaining goal and a losing goal with negative and positive progress and all test cases passed
function return_timed_goal_progress_bar_html( $goal_str, $goal_id_str, $starting_value, $target_value, $current_value, $starting_date_str, $target_date_str, $today_date_str = 'now', $projection = 'linear', $goal_description = null ) {
	$timed_goal = true;
	if ( empty( $target_date_str ) || empty( $today_date_str ) ) {
		$timed_goal = false;
	}
	// Initialize the values that will be calculated
	$goal_percent_target = 0;
	$goal_percent_time_frame = 0;
	$linear_target = 0;
	$polynomial_target = 0;
	// Calculate goal progress
	$current_delta = $current_value - $starting_value;
	$target_delta = $target_value - $starting_value;
	$goal_percent_target = number_format( ( 100 * $current_delta / $target_delta ) , 2 );
	if ($goal_percent_target > 100) {
		$goal_percent_target = 100;
	}
	// Calculate percent of goal time frame
	if ( $timed_goal ) {
		$start_dt = new DateTime($starting_date_str);
		$target_dt = new DateTime($target_date_str);
		$today_dt = new DateTime($today_date_str);
		$total_time_frame = $start_dt->diff($target_dt)->days;
		$days_since_start = $start_dt->diff($today_dt)->days;
		$goal_percent_time_frame = number_format( ( 100 * $days_since_start / $total_time_frame ) , 2 );	
		$linear_target = ( ( $goal_percent_time_frame / 100 * $target_delta ) + $starting_value );
		$polynomial_target_percentage = ( (0.0068 * pow($goal_percent_time_frame, 2) ) + ( 1.6649 * $goal_percent_time_frame ) + 0.4508 );
		$polynomial_target = ( ( $polynomial_target_percentage / 100 ) * $target_delta ) + $starting_value;  // Will be used for things that tend to suffer from law of diminishing retuns (weight gain/loss, circumference increase, etc.) Based off the following calendar: 25% of time spent = 40% results | 50% of time spent = 66.67% of results | 75% of time spent = 85% of results | 100% of time spent = 100% of results.
		if ( $projection == 'linear' ) {
			$on_track_percentage = $goal_percent_time_frame;
		}
		else if ( $projection == 'polynomial' ) {
			$on_track_percentage = $polynomial_target_percentage;
		}
	}
	$default_goal_description = "<h2>$goal_str</h2>
	<h3><span>Starting Value:</span><span>" . number_format($starting_value, 2) . "</span></h3>
	<h3><span>Current Value:</span><span>" . number_format($current_value, 2) . "</span></h3>";
	if ( $projection == 'linear' ) {
		$default_goal_description .= "<h3><span>Linear Target:</span><span>" . number_format($linear_target, 2) . "</span></h3>";
	}
	else if ( $projection == 'polynomial' ) {
		$default_goal_description .= "<h3><span>Polynomial Target:</span><span>" . number_format($polynomial_target, 2) . "</span></h3>";

	}
	$default_goal_description .= "<h3><span>Target Value:</span><span>" . number_format($target_value, 2) . "</span></h3>
	<h3><span>Starting Date:</span><span>$starting_date_str</span></h3>
	<h3><span>Target Date:</span><span>$target_date_str</span></h3>";
	$goal_description = $goal_description ?? $default_goal_description;
	$str = "<div class='goal' id='$goal_id_str'>
				<span class='goal-info' style=''>
					<div class='spacer' style='width: 1.5rem;'></div>
					<h3 style=''>$goal_str</h3>
					<i class='fas fa-info' data-goal-description='$goal_description'></i>
				</span>
				<div class='progress'>
					<div class='fill' style='width: $goal_percent_target%;' data-value='$goal_percent_target'></div>";
	if ( $timed_goal ) {
		$str .= "		<div class='target-fill' style='width: $on_track_percentage%;'></div>";
	}
	$str .= "	</div>
				<h5>$goal_percent_target%</h5>
			</div>";
	return $str;
}

function time_conversion($input_type, $input_value, $output_type, $precision = 0) {
	if ($input_type == 'hours' && $output_type == 'minutes') {
		return round( ( $input_value * 60 ) , $precision );
	}
}
/*
function push_notification_object($notifications_array, $metric, $name, $warning_direction = 'greater than', $warning_threshold, $warning_message, $caution_direction = 'greater than', $caution_threshold, $caution_message ) {
	$notification = new stdClass;
	$notification->name = 'n/a';
	if ( $warning_direction == 'greater than' ) {
		if ( $metric >= $warning_threshold ) {
			$notification->name = $name;
			$notification->type = 'warning';
			$notification->message = $warning_message;
		}
		else if ( $metric >= $caution_threshold ) {
			$notification->name = $name;
			$notification->type = 'caution';
			$notification->message = $caution_message;
		}
	}
	else if ( $warning_direction == 'less than' ) {

	}
	if ($notification->name != 'n/a') {
		$notifications_array[] = $notification;
	}
}
*/
function return_metric_based_notification_object( $metric, $name, $target_min, $target_max, $warning_min, $warning_max, $caution_min_message, $warning_min_message, $caution_max_message, $warning_max_message ) {
	if ( $metric >= $target_min && $metric <= $target_max ) { // If metric is in target then do nothing
		$notification = new stdClass();
		$notification->name = $name;
		$notification->type = 'success';
		$notification->message = '';
	}
	else if ( $metric < $target_min && $metric >= $warning_min ) { // If metric is between target min and warning min give caution message
		$notification = new stdClass();
		$notification->name = $name;
		$notification->type = 'caution';
		$notification->message = $caution_min_message;
	}
	else if ( $metric < $warning_min ) { // If metric is below warning min give warning message
		$notification = new stdClass();
		$notification->name = $name;
		$notification->type = 'warning';
		$notification->message = $warning_min_message;
	}
	else if ( $metric > $target_max && $metric <= $warning_max ) { // If metric is between target min and warning min give caution message
		$notification = new stdClass();
		$notification->name = $name;
		$notification->type = 'caution';
		$notification->message = $caution_max_message;
	}
	else if ( $metric > $warning_max ) { // If metric is below warning min give warning message
		$notification = new stdClass();
		$notification->name = $name;
		$notification->type = 'warning';
		$notification->message = $warning_max_message;
	}
	return $notification;
}
