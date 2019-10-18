<?php 
	// Database Functions
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
	function insert_row($conn, $table_name, $row_values = array()) {
		$qry = "INSERT INTO $table_name (";
		foreach($row_values as $key => $val) {
			$qry .= "$key, ";
		}
		$qry = rtrim($qry, ", ");
		$qry .= ") ";
		$qry .= "VALUES (";
		foreach($row_values as $key => $val) {
			$qry .= "'$val', ";
		}
		$qry = rtrim($qry, ", ");
		$qry .= ")";
		if ($conn->query($qry) === TRUE) {
			return "New record created successfully";
		} else {
			return "Error with query: $qry <br> $conn->error";
		}
	}

	// Not sure this can be done with MySQL connection
	/*
	function sql_srv_connect_to_db() {
		date_default_timezone_set('America/New_York');
		$serv = 'localhost';
		$user = 'jaysoftw_brett';
		$pass = 'Su944jAk127456';
		$db = 'jaysoftw_homebase';
		$connection_info = array("Database"=>$db, "UID"=>$user, "PWD"=>$pass, "CharacterSet"=>"UTF-8");
		return sqlsrv_connect($serv, $connection_info);
	}
	*/

	function set_post_value($string) {
		return (isset($_POST[$string]) && ($_POST[$string]) != '') ? $_POST[$string] : null;
	}
	function post_is_set($string) {
		return (isset($_POST[$string]) && $_POST[$string] != '') ? true : false;
	}
	function hidden_var_dump($prm) {
		echo "<div style='display: none;'><pre>";
		var_dump($prm);
		echo "</pre></div>";
	}
	function hidden_echo($prm) {
		echo "<div style='display: none;'><pre>";
		echo($prm);
		echo "</pre></div>";
	}
	function post_values_are_set($array_of_str = array()) { // WIP
		$all_values_set = true;
		foreach($array_of_str as $str) {
			if ( ! isset( $_POST[$str] ) ) {
				$all_values_set = false;
				break;
			}
		}
		return $all_values_set;
	}
	function php_dt_to_js_datestr($datetime) {
		$month_str = ( date_format($datetime, 'm') - 1 ); // JavaScript months are zero based. PHP months are based at 1.
		if ($month_str == 0) {
			$month_str = '00';
		}
		return date_format($datetime, 'Y') . ", $month_str, " .  date_format($datetime, 'd');
	}

	// Return an HTML div with classes and ids to display progress towards a goal as well as percent of time frame used
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

	// Return an HTML div with classes
	function return_finance_stat_html($title = 'New Stat', $main_metric_value = '$69.69/hr', $sub_metric_value = '', $stat_size = '',  $stat_info = 'Really cool new stat to show something relevant to my financial situation', $stat_color = '#FFF', $stat_id='', $variants_array = array() ) {
		$str = "<div class='$stat_size stat' ";
		$str .= (empty($stat_id)) ? "" : " id='$stat_id' ";
		$str .= (count($variants_array)) ? " style='height: 5rem;' " : "";
		$str .= ">
					<h3>$title</h3>";
		if (count($variants_array)) {
			$str .= "<div class='variant-row' style='display: flex'>";
			foreach ($variants_array as $val => $disp) {
				$str .= "<button data-val='$val'>$disp</button>";
			}
			$str .= "</div>";
		}
		$str .= "	<h4 style='color: $stat_color;' class='main-metric-val'>$main_metric_value</h4>
					<h5>$sub_metric_value</h5>
					<i class='fas fa-info' data-stat-description='$stat_info'></i>
				</div>";
		return $str;
	}
	function return_finance_stat_info_html($title, $concept = '', $formula = '', $assumptions = array(), $notes = '') {
		$str = "<h2>$title</h2>
		<h3><span>Concept:</span><span>$concept</span></h3>
		<h3><span>Formula:</span><span>$formula</span></h3>";
		$str .= "<h3><span>Assumptions:</span><span><ul>";
		foreach ($assumptions as $a) {
			$str .= "<li>$a</li>";
		}
		$str .= "</ul></span></h3>
		<h3><span>Notes:</span><span>$notes</span></h3>";
		return $str;
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
	function return_metric_based_notification_object( $metric, $name, $target_min, $target_max, $warning_min, $warning_max, $caution_min_message, $warning_min_message, $caution_max_message, $warning_max_message, $est_min_to_complete ) {
		$notification = new stdClass();
		$notification->name = $name;
		$notification->est_min_to_comp = $est_min_to_complete;
		if ( $metric >= $target_min && $metric <= $target_max ) { // If metric is in target then do nothing
			$notification->type = 'success';
			$notification->message = '';
		}
		else if ( $metric < $target_min && $metric >= $warning_min ) { // If metric is between target min and warning min give caution message
			$notification->type = 'caution';
			$notification->message = $caution_min_message;
		}
		else if ( $metric < $warning_min ) { // If metric is below warning min give warning message
			$notification->type = 'warning';
			$notification->message = $warning_min_message;
		}
		else if ( $metric > $target_max && $metric <= $warning_max ) { // If metric is between target min and warning min give caution message
			$notification->type = 'caution';
			$notification->message = $caution_max_message;
		}
		else if ( $metric > $warning_max ) { // If metric is below warning min give warning message
			$notification->type = 'warning';
			$notification->message = $warning_max_message;
		}
		return $notification;
	}

// Generic HTML functions
	function return_label_and_input($id, $name, $type, $display, $attributes = array()) {
		$str = "<span style='display: inline-flex; flex-flow: column nowrap;'>";
		$str .= "<label for='$id'>$display</label>";
		$str .= "<input type='$type' name='$name' id='$id'";
		foreach($attributes as $a) {
			$str .= " $a ";
		}
		$str .= "/>";
		$str .= "</span>";
		return $str;
	}

// Date & Time functions
	function time_conversion($input_type, $input_value, $output_type, $precision = 0) {
		if ($input_type == 'hours' && $output_type == 'minutes') {
			return round( ( $input_value * 60 ) , $precision );
		}
		if ($input_type == 'min' && $output_type == 'object') {
			$obj = new stdClass;
			$obj->days = floor(($input_value / (60 * 24)));
			$obj->hours = floor((($input_value % (60 * 24)) / 60));
			$obj->minutes = ($input_value % 60);
			return $obj;
		}
	}
	function return_date_relative_to_today($modification_str = '+0 days', $output_type = 'string', $output_format = 'Y/m/d') {
		$today_dt = new datetime();
		$comparison_dt = clone $today_dt;
		$comparison_dt->modify( $modification_str );
		if ($output_type == 'string') {
			return date_format($comparison_dt, $output_format);
		}
	}
    function return_date_from_str($str = 'today', $output_type = 'string', $output_format = 'Y/m/d' ) {
        $dt = new datetime($str);
		if ($output_type == 'string') {
			return date_format($dt, $output_format);
        }
        else if ($output_type == 'datetime') {
            return $dt;
        }
	}
	function return_days_between_dates($earlier_datestr, $later_datestr) {
		$dt1 = return_date_from_str($earlier_datestr, 'datetime');
		$dt2 = return_date_from_str($later_datestr, 'datetime');
		$int = $dt1->diff($dt2);
		return ($int->invert) ? (-1 * $int->days) : $int->days;
	}
	function return_end_of_day($date_str, $output_type = 'string', $format = 'Y-m-d H:i:s') {
		$dt = new datetime($date_str);
		$dt->setTime(23, 59, 59);
		if ($output_type == 'datetime') {
			return $dt;
		}
		else if ($output_type == 'string') {
			return date_format($dt, $format);
		}
	}
	
// Query functions

	// Seal & Design
	function return_seal_hours($conn, $date_start, $date_end) {
		$query = "	SELECT SUM( ( time_to_sec( TIMEDIFF( departure_time, arrival_time ) ) / ( 60 * 60 ) ) - ( break_min / 60 ) ) AS 'Seal Hours'
					FROM finance_seal_shifts
					WHERE 	date >= '$date_start'
						AND date <= '$date_end' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return round( $row['Seal Hours'], 2 );
	}
	function return_seal_hourly_wage($conn, $day_to_check) { // Perhaps PTO should be tracked in a similar fashion to income / expenditure but all in one table
															// ie. 05/29/2019 (approval date) | 32 (hrs)
		$query = "	SELECT *
					FROM `finance_seal_hourly`
					WHERE 	start_date <= '$day_to_check'
						AND ( end_date >= '$day_to_check' OR end_date IS NULL ); 
				";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return $row['hourly_wage'];
	}
	function return_seal_pre_tax_salary($conn, $date_start, $date_end, $fuse_length) {
		$day_to_check = $date_start; // Start checking from today
		$pre_tax_salary = 0;
		$fuse = 0;
		while ($day_to_check <= $date_end) {
			$this_dow = date('D', strtotime($day_to_check));
			if ($this_dow == 'Sat' || $this_dow == 'Sun' || ($day_to_check > date('Y-m-d', strtotime('July 14th 2018')) && $day_to_check < date('Y-m-d', strtotime('July 21st 2018')))) { // Ideally unpaid PTO should be stored in an array in constants or in a table inside of MySQL DB
			; 
			} else {
				$pre_tax_salary += ( return_seal_hourly_wage($conn, $day_to_check) * 8 );
			}
			$day_to_check = date('Y-m-d', strtotime($day_to_check.'+1day'));
			$fuse++;
			if ($fuse >= $fuse_length) {
				echo "FUSE BLOWN";
				exit;
			}
		}
		return $pre_tax_salary;
	}
	function return_seal_pre_tax_bonus($conn, $date_start, $date_end) {
		$query = " 	SELECT SUM(amount) AS 'bonus value'
					FROM `finance_seal_income`
					WHERE 	date >= '$date_start'
						AND date <= '$date_end'
						AND type = 'bonus' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return $row['bonus value'];
	}
	function return_seal_received_income($conn, $date_start, $date_end) {
		$query = " 	SELECT SUM(amount) AS 'value'
					FROM `finance_seal_income`
					WHERE 	date >= '$date_start'
						AND date <= '$date_end' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return $row['value'];
	}

	// Ricks on Main
	function return_ricks_hours($conn, $date_start, $date_end) {
		$query = "	SELECT SUM(hours) AS 'Ricks Hours'
					FROM `finance_ricks_shifts` 
					WHERE date >= '$date_start' 
						AND date <= '$date_end' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return round( $row['Ricks Hours'], 2 );
	}
	function return_ricks_otb_hours($conn, $date_start, $date_end) {
		$query = "	SELECT SUM(hours) AS 'Ricks OTB Hours'
					FROM `finance_ricks_shifts` 
					WHERE date >= '$date_start' 
						AND date <= '$date_end'
						AND type = 'otb' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return round( $row['Ricks OTB Hours'], 2 );
	}
	function return_ricks_tips($conn, $date_start, $date_end) {
		$query = "	SELECT SUM(tips) 
					FROM finance_ricks_shifts 
					WHERE date >= '$date_start'
						AND date <='$date_end'";
		$res = $conn->query($query);
		$row = mysqli_fetch_row($res);
		return round($row[0], 2);
	}
	function return_ricks_pre_tax_income($conn, $date_start, $date_end, $hourly_wage) { // Ideally, hourly wage should be stored in a table similar to seal hourly. Otherwise, if Ricks hourly changes mid-way through a week/month/year then this will be slightly off
		$ricks_total_hours = return_ricks_hours($conn, $date_start, $date_end);
		$ricks_otb_hours = return_ricks_otb_hours($conn, $date_start, $date_end);
		$ricks_billable_hours = $ricks_total_hours - $ricks_otb_hours;
		return (return_ricks_tips($conn, $date_start, $date_end) + ($ricks_billable_hours * $hourly_wage));
	}
	function return_ricks_on_main_monthly_array($conn, $date_start, $date_end, $hourly_wage, $shift_type = 'all', $daystr = 'all') {
		$ricks_monthly_array = array();
		$q = "	SELECT 	YEAR(date) AS 'year'
						,MONTH(date) AS 'month'
						,MONTHNAME(date) AS 'monthname'
						/*DN ,DAYNAME(date) AS 'dow' DN*/
						,ROUND( 
							AVG(
								CASE
									WHEN type IN ('AM', 'PM') THEN (tips + (hours * $hourly_wage))
									WHEN type IN ('OTB') THEN (tips)
									ELSE 0
								END
							) , 2
						) AS 'AVG income'
						,ROUND( AVG(hours) , 2 ) AS 'AVG hours'
						,ROUND( AVG(
									CASE
										WHEN type IN ('AM', 'PM') THEN (tips + (hours * $hourly_wage))
										WHEN type IN ('OTB') THEN (tips)
										ELSE 0
									END
								)
							/
							AVG(hours)
							, 2) AS 'AVG hourly'
						,COUNT(*) AS 'Occurances'
				FROM `finance_ricks_shifts`
				WHERE  	date >= '$date_start'
					AND date <= '$date_end'
					AND type = 'PM' /* WIP this is to be changed when get preg replace working */
					/*ST AND type = '$shift_type' ST*/
					/*DN AND DAYNAME(date) = '$daystr' DN*/
				GROUP BY 	YEAR(date)
							, MONTH(date)
							/*DN , DAYNAME(date) DN*/
				ORDER BY YEAR(date), MONTH(date)
				 ";
				 // WIP
		if ($shift_type != 'all') {
			echo "SHIFT TRIGGER";
			//preg_replace('', '', $q);
			//preg_replace('/ST/g', '', $q);
		}
		if ($daystr != 'all') {
			echo "DAY TRIGGER";
			//str_replace('/*DN', '', $q);
			//str_replace('DN*/', '', $q);
		}
		//echo "<pre>";
		//echo $q;
		//echo "</pre>";
		$res = $conn->query($q);
		while ( $row = mysqli_fetch_array($res) ) {
			$month = new stdClass();
			$month->year = $row['year'];
			$month->month = $row['month'];
			$month->month_name = $row['monthname'];
			if ($daystr != 'all') {
				$month->dow = $row['dow'];
			}
			$month->avg_income = $row['AVG income'];
			$month->avg_hours = $row['AVG hours'];
			$month->avg_hourly = $row['AVG hourly'];
			$month->start_dt = new datetime("first day of " . $month->month_name . " " . $month->year);
			$ricks_monthly_array[] = $month;
		}
		return $ricks_monthly_array;
	}

	// JSS
	function return_jss_income($conn, $date_start, $date_end) { // CAUTION: date end must be 23:59:59 to appropriately capture entire end day
		$date_end = return_end_of_day($date_end);
		
		$query = "	SELECT SUM(profit) AS 'Net Profit'
					FROM `finance_jss_income` 
					WHERE datetime >= '$date_start' 
						AND datetime <= '$date_end' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return round( $row['Net Profit'], 2 );
	}

	// Expenditure
	function return_expenditure($conn, $date_start, $date_end, $category = 'all') {
		$query = "	SELECT SUM(amount) AS 'Net Expenditure'
					FROM finance_expenses 
					WHERE 	date >= '$date_start' 
						AND date <= '$date_end'";
		if ($category == 'lux') {
			$query .= "	AND type IN ('";
			$query .= implode("', '", LUXURY_EXPENDITURES);
			$query .= "')";
		}
		else if ($category == 'non') {
			$query .= "	AND type NOT IN ('";
			$query .= implode("', '", LUXURY_EXPENDITURES);
			$query .= "')";
		}
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return round($row['Net Expenditure'], 2);
	}
	function return_expenditure_array($conn, $date_start, $date_end) {
		$expenditure_array = array();
		$query = "	SELECT type, SUM(amount) AS 'Expenditure'
					FROM finance_expenses
					WHERE 	date >= '$date_start'
						AND date <= '$date_end'
					GROUP BY type 
					ORDER BY SUM(amount) DESC; ";
		$res = $conn->query($query);
		while ( $row = mysqli_fetch_array($res) ) {
			$expenditure_array[] = $row;
		}
		return $expenditure_array;
	}

	// Accounts
	function return_relevant_accounts_info_array($conn, $date) {
		$account_ids = array(); // Array to house account names
		$qry = " SELECT f_a.id, f_a.name
				FROM finance_accounts AS f_a
				WHERE 	(f_a.closed_on IS NULL 
				 	OR f_a.closed_on >= '$date')
					AND EXISTS(	SELECT value
								FROM finance_account_log AS f_a_l
								WHERE f_a.id = f_a_l.account_id
									AND f_a_l.date <= '$date' ) ";
		$res = $conn->query($qry);
		if ($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$account_ids[$row['id']] = $row['name'];
			}
		}
		return $account_ids;
	}

	function return_accounts_array($conn, $year = '2019', $date = null) { // $year is deprecated but needs to be left here until cleanup can be performed on all reports/forms
		$accounts = array(); // Array to house account objects
		$qry = " SELECT f_a.*, ( 	SELECT value
									FROM finance_account_log AS f_a_l
									WHERE f_a.id = f_a_l.account_id ";
		if (!is_null($date)) {
			$qry .= " 					AND f_a_l.date <= '$date' ";
		}
		$qry .= "					ORDER BY f_a_l.date DESC, f_a_l.id DESC
									LIMIT 1) AS 'most recent value'
				FROM finance_accounts AS f_a
				WHERE 	f_a.closed_on IS NULL ";
		if (!is_null($date)) {
			$qry .= " OR f_a.closed_on >= '$date' "; // Be cautious of this OR if additional where clauses are added to this query
		}
		$qry .= " GROUP BY f_a.id, f_a.name, f_a.type, f_a.expected_annual_return ";
		$res = $conn->query($qry);
		if ($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$account = new stdClass();
				$account_id = $row['id'];
				$account->name = $row['name'];
				$account->type = $row['type'];
				$account->exp_roi = $row['expected_annual_return'];
				$account->mrv = $row['most recent value'];
				//var_dump($account);
				//echo "<br/><br/>";
				$accounts[] = $account;
			}
		}
		return $accounts;
	}

	function return_account_value_over_time($conn, $date_start, $date_end, $account_id = 'all') {
		$array = array();
		if ($account_id == 'all') { // If all is set then we determine Net Worth
			$qry = " 	SELECT f_a.name, f_a_l.date, f_a_l.value
						FROM finance_account_log AS f_a_l
						INNER JOIN finance_accounts AS f_a
							ON (f_a_l.account_id = f_a.id)
						WHERE 	date >= '$date_start'
							AND date <= '$date_end'
							AND account_id = $account_id ";
			$res = $conn->query($qry);
			if ($res->num_rows > 0) {
				while($row = $res->fetch_assoc()) {
					$array[$row['date']] = $row['value'];
				}
			}
		}
		else { // Otherwise, we are determining for a single account
			$qry = " 	SELECT f_a.name, f_a_l.date, f_a_l.value
						FROM finance_account_log AS f_a_l
						INNER JOIN finance_accounts AS f_a
							ON (f_a_l.account_id = f_a.id)
						WHERE 	date >= '$date_start'
							AND date <= '$date_end'
							AND account_id = $account_id ";
			$res = $conn->query($qry);
			if ($res->num_rows > 0) {
				while($row = $res->fetch_assoc()) {
					$array[$row['date']] = $row['value'];
				}
			}
		}
		return $array;
	}

	// Goals & Metric tracking
	function return_dev_hours($conn, $date_start, $date_end, $precision = 2) {
		$query = "	SELECT SUM(software_dev_hours) AS 'Net Dev Hours'
					FROM personal_day_info
					WHERE 	date >= '$date_start'
						AND date <= '$date_end' ";
		$res = $conn->query($query);
		$row = mysqli_fetch_array($res);
		return round($row['Net Dev Hours'], $precision);
	}

	// Calculations
	function return_theoretical_age_60_withdrawal_rate($accounts = array(), $years_until_60 = 34.75, $exp_roi = null ) {
		$theoretical_age_60_net_worth = 0;
		$theoretical_age_60_annual_withdrawal_rate = 0;
		foreach ($accounts as $a) { // For each account determine the estimated value at age 60
			$curr_principle = $a->mrv;
			if (is_null($exp_roi)) { // If an ROI is not given then use the value on this account
				$age_60_val = ( $curr_principle * pow( (1 + ($a->exp_roi / 100)) , $years_until_60 ) );
			}
			else { // If an ROI is given then use that value to determine future value of this account
				$age_60_val = ( $curr_principle * pow( (1 + ($exp_roi / 100)) , $years_until_60 ) );
			}
			if ($age_60_val < 0) { // For depreciating assets and liabilities
				$age_60_val = 0;
			}
			$theoretical_age_60_net_worth += $age_60_val;
			if ($a->type == 'ROTH') { // If the account is a ROTH then no taxes or capital gains need to be paid
				$theoretical_age_60_annual_withdrawal_rate += round( $age_60_val * 0.04 , 2 ); // Assuming a 4% withdraw rate
			}
			elseif ($a->type == 'traditional 401k') { // If the account is a traditional 401k then taxes need to be paid
				$theoretical_age_60_annual_withdrawal_rate += round( ($age_60_val * 0.04) * 0.7 , 2 ); // Assuming a 30% Tax Rate
			}
			elseif ($a->type == 'taxable account') { // If the account 
				$percent_growth = ($age_60_val - $curr_principle) / $age_60_val;
				$theoretical_age_60_annual_withdrawal_rate += round( ($age_60_val * 0.04) - ( ($age_60_val * 0.04) * $percent_growth * 0.15) , 2); // Assuming a 15% Capital Gains Rate
			}
			// TEST PASSED 2019.03.12 echo "$a->name: $age_60_val | $theoretical_age_60_net_worth | $theoretical_age_60_annual_withdrawal_rate <br/>";
		}
		return $theoretical_age_60_annual_withdrawal_rate;
	}

	function return_estimated_commute_time($conn, $date_start, $date_end, $precision = 2) {
		$commute_min = 0; // Holds commute in units of minutes
		$q = "SELECT 	rs.date
						, CASE
							WHEN EXISTS(	SELECT *
											FROM finance_seal_shifts AS fss1
											WHERE fss1.date = rs.date	)
							AND EXISTS(	SELECT *
											FROM finance_ricks_shifts AS frs1
											WHERE frs1.date = rs.date	)
							THEN 'BOTH'
							WHEN EXISTS(	SELECT *
											FROM finance_seal_shifts AS fss1
											WHERE fss1.date = rs.date	)
							AND NOT EXISTS(	SELECT *
											FROM finance_ricks_shifts AS frs1
											WHERE frs1.date = rs.date	)
							THEN 'S&D Only'
							WHEN NOT EXISTS(	SELECT *
											FROM finance_seal_shifts AS fss1
											WHERE fss1.date = rs.date	)
							AND EXISTS(	SELECT *
											FROM finance_ricks_shifts AS frs1
											WHERE frs1.date = rs.date	)
							THEN 'Ricks Only'
							ELSE '???'
						END AS 'Shifts Type'
					
			FROM (
				SELECT fss.date
				FROM finance_seal_shifts AS fss
				UNION
				SELECT frs.date
				FROM finance_ricks_shifts AS frs
				) AS rs
			WHERE rs.date >= '$date_start'
				AND rs.date <= '$date_end'
			ORDER BY rs.date ";
		$res = $conn->query($q);
		if ($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				switch ($row['Shifts Type']) {
					case 'BOTH':
						$commute_min += 65;
						break;
					case 'S&D Only':
						$commute_min += 55;
						break;
					case 'Ricks Only':
						$commute_min += 25;
						break;
					default:
						break;
				}
			}
		}
		return round(($commute_min / 60), $precision);
	}