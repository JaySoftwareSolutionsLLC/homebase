<?php
//---CONNECT TO LOCAL DB------------------------------------------------------------
/*
	error_reporting(E_ERROR);
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/constants.php');

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_local_db();
*/

//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	$year = set_post_value('year') ?? date('Y');
	if ( $year == '2018' ) {
		include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2018.php');
	}
	else {
		include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');
	}

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();


//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------
	if ($year == '2018') {
		$today_time = strtotime('December 31st 2018');
		$today_date = date('Y/m/d', mktime(0, 0, 0, 12, 31, 2018));
		$today_datetime = new DateTime('December 31st 2018');
	}
	else {
		$today_time = time();
		$today_datetime = new DateTime();
		$today_date = date_format($today_datetime, 'Y/m/d');
	}
	//echo $today_time;
	//var_dump( $today_datetime );
	//var_dump( $today_date );
	$last_sunday = "'" . date('Y/m/d', strtotime('last Sunday')) . "'";
	$days_left_in_year = floor((strtotime("January 1st, " . ($year + 1)) - $today_time) / SEC_IN_DAY); // Needs to stay this way for 2018 and 2019 to both work

	// DEPRECATED 2018.10.28 $current_bench_press = 185;

//---SELECT FROM DATABASE-----------------------------------------------------------

	// FINANCIAL--------------------------------------------------------------------

	// Time Related Variables
	$start_date_financial = date('Y/m/d', strtotime(START_DATE_STRING_FINANCIAL));
	$end_date_financial = $today_date;
	$start_time_financial = strtotime($start_date_financial);
	$days_active_financial = ceil(($today_time - $start_time_financial) / (SEC_IN_DAY));
	if ($year == '2018') {
		$days_left_in_year_financial = 0;
	}
	else {
		$days_left_in_year_financial = (365 - (date('z') + 1));
	}

	// Retrieve Account Information
	$all_account_names = array();
	$oldest_date = "2550-01-01";
	$current_cash = 0;
	$current_assets = 0;
	$current_liabilities = 0;

	if ($year == '2018') {
		$qry = "SELECT name, MAX(date) AS date FROM finance_accounts WHERE date <= '2019-01-03' GROUP BY name;";
	}
	else {
		$qry = "SELECT name, MAX(date) AS date FROM finance_accounts GROUP BY name;";
	}
	$res = $conn->query($qry);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$all_account_names[$row['name']] = $row['date'];
		}
	}

	foreach ($all_account_names as $name => $date) {
		$qry = "SELECT name, date, value, type FROM finance_accounts WHERE name = '$name' AND date = '$date';";
		$res = $conn->query($qry);
		$row = $res->fetch_assoc();
		$acnt_type = $row['type'];
		$acnt_date = $row['date'];
		if ($acnt_type == 'cash') {
			$current_cash += $row['value'];
		}
		else if ($acnt_type == 'asset') {
			$current_assets += $row['value'];
		}
		else if ($acnt_type == 'liability') {
			$current_liabilities += $row['value'];
		}
		else {
			echo "ERROR IN DETERMINING ACCOUNT VALUES";
		}
		if ($acnt_date < $oldest_date) {
			$oldest_date = $acnt_date;
		}
	}

	// Determine total expenses since start date
	$q = "SELECT SUM(amount) FROM finance_expenses WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_expenditure = $row[0];

	// Seal Hours
	$q = "SELECT SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - 0.5) FROM finance_seal_shifts WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_seal_hours = $row[0];

	// Ricks Hours
	$q = "SELECT SUM(hours) FROM `finance_ricks_shifts` WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_hours = $row[0];

	// Ricks OTB Hours
	$q = "SELECT SUM(hours) FROM `finance_ricks_shifts` WHERE date >= '$start_date_financial' AND date <= '$end_date_financial' AND type = 'otb' ";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_otb_hours = $row[0] ?? 0;
	
	// Net Hours
	$net_hours = $net_seal_hours + $net_ricks_hours;


	// Determine total income since start date

	// Ricks Income
	$q = "SELECT SUM(tips) FROM finance_ricks_shifts WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_ricks_tips = $row[0];

	// Seal Income
		// Seal income actually received
	$q = "SELECT SUM(amount) FROM finance_seal_income WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_seal_income = $row[0];
	if ($year == '2018') {
		$annual_check_adjustment = -3 * 8 * 21.63; // 3 days in my first check were from May work days. This program only checks June 1st on so those days should not count towards total
	}
	else {
		$annual_check_adjustment = (-1 * 11 * 8 * 25) + (-62.5);
	}
	$q = "SELECT MAX(end_payperiod) FROM finance_seal_income WHERE date >= '$start_date_financial' AND date <= '$end_date_financial' AND type = 'check'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$last_seal_check_date = $row[0]; // The most recent check date
	$last_seal_check_time = strtotime($last_seal_check_date);
	$four_pm_seconds = (60 * 60 * 16);
	$unreceived_seal_income = 0;
	if ($year == '2018') {
		$unreceived_seal_income = 200;
	}
	else {
		$fuse = 0;
		$this_time_to_check = $last_seal_check_time + SEC_IN_DAY + $four_pm_seconds;
		while ($this_time_to_check < $today_time) {
			$this_dow = date('D',$this_time_to_check);
			$this_time_to_check += SEC_IN_DAY;
			if ($this_dow != 'Sat' && $this_dow != 'Sun' && ($this_time_to_check < strtotime('July 14th 2018') || $this_time_to_check > strtotime('July 21st 2018'))) {
				$num_hourly_wages = count(HOURLY_WAGES_DATESTRINGS_SEAL);
				$i = 0;
				for ($i; $i < $num_hourly_wages; $i++) {
					if (strtotime(HOURLY_WAGES_DATESTRINGS_SEAL[$i]) > $this_time_to_check) {
						break;
					}
					else {
						$correct_hourly = HOURLY_WAGES_SEAL[$i];
					}
				}
				$unreceived_seal_income += ($correct_hourly * 8);
				// TEST PASSED 2018.10.18 echo "$ $unreceived_seal_income | $days_active_financial days";
			}
			$fuse++;
			if ($fuse > 30) {
				echo 'FUSE BLOWN in finance.php';
				break;
			}
		}
	}

	// Software Dev Hours
	$q = "SELECT SUM(software_dev_hours) FROM personal_day_info WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$software_dev_hours = $row[0];

	$unreceived_after_tax_seal_income = (ESTIMATED_AFTER_TAX_PERCENTAGE * $unreceived_seal_income / 100);

	// NET INCOME : Hourlywage at ricks multiplied by ricks hours + net tips from ricks + net recorded income from seal and design + unreceived (but earned) income from seal and design
	$net_income = (HOURLY_WAGE_RICKS * ( $net_ricks_hours - $net_ricks_otb_hours ) ) + $net_ricks_tips + $net_seal_income + $unreceived_seal_income + $annual_check_adjustment;
	// (46.2) + 106 + 2062.5 + 1000 - 2200

	//echo "$net_income | $net_ricks_hours | $net_ricks_otb_hours | $net_ricks_tips | $net_seal_income | $unreceived_seal_income | $annual_check_adjustment ";

	if ($year == '2018') {
		$unpaid_vacation_adjustment = 7; // Factored in for week long trip to South Carolina (unpaid by S&D)
	}
	else {
		$unpaid_vacation_adjustment = 0;
	}
	$adi = number_format($net_income / ($days_active_financial - $unpaid_vacation_adjustment), 2); // the 7 is subtracted from days_active_financial to make ADI reflect NON-UNPAID VACATION DAYS b/c in future all vacations should be paid (at least from S&D)
	$ade = number_format($net_expenditure / ($days_active_financial), 2); // the 7 is NOT subtracted from days_active_financial because will take vacations in future
	$awh = number_format(7 * $net_hours / ($days_active_financial - $unpaid_vacation_adjustment), 2); // the 7 is subtracted from days_active_financial to make AWH reflect NON-UNPAID VACATION DAYS b/c in future all vacations should be paid (at least from S&D)
	$ahw = number_format(7 * $adi / $awh, 2);

	$current_net_worth = $current_assets + $current_cash - $current_liabilities;

	$estimated_2018_income = number_format(PRE_JUNE_RICKS_INCOME + ($adi  * ($days_left_in_year_financial + $days_active_financial)), 0);

	$estimated_2019_income = number_format(($adi * 365), 0);

	$estimated_EOY_net_worth = number_format($current_net_worth + ($unreceived_seal_income * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) + ((($adi * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - $ade) * ($days_left_in_year_financial)), 0);

	$current_est_net_worth = $unreceived_after_tax_seal_income + $current_assets + $current_cash - $current_liabilities;

	//---FITNESS--------------------------------------------------------------------

	// Running

	$start_date_running = date('Y/m/d 00:00:00', strtotime(START_DATE_STRING_RUNNING));
	$end_date_running = $today_date;
	$start_time_running = strtotime($start_date_running);

	$days_active_running = ceil(($today_time - $start_time_running) / (SEC_IN_DAY));

	//DEPRECATED 2018.10.28 $q = "SELECT TOP(1) MIN(seconds) FROM `fitness_runs` WHERE miles >= 1 ORDER BY datetime DESC;";
	$q = "SELECT seconds FROM `fitness_runs` WHERE miles = 1 AND datetime <= '$end_date_running 23:59:59' AND datetime >= '$start_date_running' ORDER BY datetime DESC LIMIT 1";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$best_mile_time = $row[0];
	// TEST PASSED 2019/01/06 echo $q;

	// Body Weight
	
	$start_date_body_weight = 	date('Y/m/d', strtotime(START_DATE_STRING_BODY_WEIGHT));
	$end_date_body_weight = $today_date;
	
	$start_time_body_weight = 	strtotime($start_date_body_weight);

	$days_active_body_weight =	ceil(($today_time - $start_time_body_weight) / (SEC_IN_DAY));
	// echo "$days_active_body_weight <br/>";

	//echo "$start_date_body_weight | $end_date_body_weight<br/>";
	$q = " SELECT pounds FROM `fitness_measurements_body_weight` WHERE datetime >= '$start_date_body_weight 00:00:00' AND datetime <= '$end_date_body_weight 23:59:59' ORDER BY datetime DESC LIMIT 1 ";
	
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$most_recent_body_weight = $row[0];
	//echo "$q | $most_recent_body_weight";

	// Lifting

	$most_recent_upper_arm_size = 0;
	$q = "SELECT workout_structure_id FROM `fitness_cycles` WHERE start_date <= '$today_date' AND end_date >= '$today_date 00:00:00' LIMIT 1";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$workout_structure = $row[0];
	// TEST PASSED 2018.10.14 echo "WORKOUT STRUCTURE: $workout_structure <br/>";

	$number_ready_muscles = 0;
	$number_total_muscles = 0;
	$number_near_ideal_muscles = 0;
	$ideal_percent_tolerance = 5; // Used to determine which muscles should be marked as 'ideal'
	$ideal_score =			0;

	$muscle_objects = array();
	$q = "SELECT 
	muscles.id, muscles.common_name, circs.id AS 'circ_id', circs.name AS 'circ_name', circs.ideal
	FROM `fitness_muscles` AS muscles 
	INNER JOIN `fitness_circumferences` AS circs ON (muscles.circumference_id = circs.id)"; // ~~~BUG~~~ this is based off today's workout structure, but the lift could have been performed 3 days ago w/ a diff workout structure recovery time
	$res = $conn->query($q);
	//var_dump ($q);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			//var_dump($row);
			$muscle_id = 				$row['id'];
			$muscle_name = 				$row['common_name'];
			
			$muscle_associated_circ_id = $row['circ_id'];
			$muscle_associated_circ = 	$row['circ_name'];
			$muscle_ideal_circ = 		$row['ideal'];
			
			//$muscle_ideal_rest =		$row['ideal_recovery'];
			
			$q_current_circ = 			"SELECT value FROM fitness_measurements_circumferences WHERE circumference_id = $muscle_associated_circ_id AND datetime >= '$start_date_body_weight 00:00:00' AND datetime <= '$end_date_body_weight 23:59:59' ORDER BY datetime DESC LIMIT 1";
			$res_current_circ = 		$conn->query($q_current_circ);
			$row_current_circ = 		mysqli_fetch_row($res_current_circ);
			$muscle_current_circ = 		$row_current_circ[0];
			if ($muscle_id == 4) {
				$most_recent_upper_arm_size = $muscle_current_circ;
			}
			
			$q_mrf = "	SELECT fl.datetime, IFNULL( firt.ideal_recovery , 48 ) AS 'ideal_recovery'
						FROM fitness_lifts AS fl
						LEFT JOIN fitness_ideal_recovery_times AS firt
							ON (fl.workout_structure_id = firt.workout_structure_id
							AND firt.muscle_id = $muscle_id)
						WHERE fl.exercise_id IN 
							(SELECT exercise_id FROM `fitness_pivot_exercises_muscles` WHERE muscle_id = $muscle_id AND datetime >= '$start_date_body_weight 00:00:00'  AND datetime <= '$end_date_body_weight 23:59:59'  AND type = 'primary')
						ORDER BY datetime DESC LIMIT 1	";
			$res_mrf =					$conn->query($q_mrf);
			$row_mrf =					mysqli_fetch_row($res_mrf);
			$muscle_mrf_time =			strtotime($row_mrf[0]); // Most Recent Failure as timestamp
			$muscle_ideal_rest = 		$row_mrf[1];
			//echo $muscle_ideal_rest;
			$muscle_mrf_hours =			ceil(($today_time - $muscle_mrf_time) / (60 * 60)); // Most Recent Failure as timestamp
			//echo "$muscle_id | $muscle_mrf_time | $muscle_mrf_hours" . date('Y/m/d H:i:s', $muscle_mrf_time) . " <br/>";
			if ($muscle_mrf_hours > 999) { $muscle_mrf_hours = 999; }
			
			$hur =						$muscle_ideal_rest - $muscle_mrf_hours;
			if ($hur <= 0) {
				$number_ready_muscles++;
			}
			$percent_ideal = 			number_format((100 - ((($muscle_ideal_circ - $muscle_current_circ) / $muscle_ideal_circ) * 100)), 2);

			if ($percent_ideal <= 100) {
				$idealness_score = $percent_ideal;
			}
			else {
				$idealness_score = (200 - $percent_ideal);
			}
			
			if ($idealness_score >= (100 - $ideal_percent_tolerance)) {
				$number_near_ideal_muscles++;
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
			
			$number_total_muscles++;
			$ideal_score += 			$idealness_score;			
			
			//echo "ID: $muscle_id | COMMON NAME: $muscle_name | CIRC.: $muscle_associated_circ | IDEAL: $muscle_ideal_circ | CURRENT: $muscle_current_circ | MRF: $muscle_mrf_hours | IDEAL REC: $muscle_ideal_rest | HUR: $hur | % IDEAL: $percent_ideal <br/>";
		}
	}

	//$q = "SELECT MAX(weight) FROM `fitness_lifts` WHERE exercise_id = 23 AND ((workout_structure_id = 2 AND total_reps > 5) OR (workout_structure_id = 3 AND total_reps > 0)) ORDER BY datetime DESC";
	$q = "SELECT MAX(weight) FROM `fitness_lifts` WHERE exercise_id = 23 AND workout_structure_id = 3 AND datetime >= '$start_date_body_weight' AND datetime <= '$end_date_body_weight' AND total_reps > 0 ORDER BY datetime DESC";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$current_bench_press = $row[0];

	$body_net_percent_ideal =			number_format(($ideal_score / $number_total_muscles), 2);

	//--HEALTH----------------------------------------------------------------------

	// Mindfulness Hours
	$q = "SELECT SUM(mindfulness_hours) FROM personal_day_info WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$mindfulness_hours = $row[0];

	//var_dump($muscle_objects);

	//---GOALS----------------------------------------------------------------------
	if ($year == '2018') {

		$percent_goal_debt_free_2018 = 	number_format(((JUNE_1ST_DEBT - $current_liabilities) / JUNE_1ST_DEBT) * 100, 2);
		if ($percent_goal_debt_free_2018 >= 100) {
			$percent_goal_debt_free_2018 = 100;
		}
		$percent_time_frame_debt_free_2018 = number_format((100 * $days_active_financial / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_FINANCIAL)) / SEC_IN_DAY)), 2);

		$percent_goal_net_worth_2018 = 	number_format((($current_cash + $current_assets - $current_liabilities - JUNE_1ST_NET_WORTH) / (END_OF_YEAR_NET_WORTH_TARGET - JUNE_1ST_NET_WORTH)) * 100, 2);
		if ($percent_goal_net_worth_2018 >= 100) {
			$percent_goal_net_worth_2018 = 100;
		}
		$percent_time_frame_net_worth_2018 = number_format((100 * $days_active_financial / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_FINANCIAL)) / SEC_IN_DAY)), 2);

		// Case Tests: mrbw = 147 --> 0% | mrbw = 160 --> 100% | mrbw = 153.5 --> 50%
		// All Case Tests PASS
		$percent_goal_body_weight_2018 = number_format(($most_recent_body_weight - STARTING_BODY_WEIGHT) * (100 / (BODY_WEIGHT_TARGET -STARTING_BODY_WEIGHT)), 2);
		if ($percent_goal_body_weight_2018 > 100) {
			$percent_goal_body_weight_2018 = 100;
		}
		$percent_time_frame_body_weight_2018 = number_format((100 * $days_active_body_weight / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_BODY_WEIGHT)) / SEC_IN_DAY)), 2);

		// Case Tests: bmt = 405 --> 100% | bmt = 515 --> 0% | bmt = 460 --> 50%
		// All Case Tests PASS
		$percent_goal_mile_time_2018 = number_format(100 - (($best_mile_time - MILE_TIME_TARGET) * (100 / (STARTING_MILE_TIME - MILE_TIME_TARGET))), 2);
		if ($percent_goal_mile_time_2018 >= 100) {
			$percent_goal_mile_time_2018 = 100;
		}
		$percent_time_frame_running_2018 = number_format((100 * $days_active_running / (((strtotime('January 1st, 2019')) - strtotime(START_DATE_STRING_RUNNING)) / SEC_IN_DAY)), 2);
		//echo "$percent_goal_mile_time_2018 | $best_mile_time";
		
		$percent_goal_bench_press_2018 = number_format( ( 100 * ( $current_bench_press - STARTING_BENCH_PRESS ) / ( END_OF_YEAR_BENCH_PRESS_TARGET - STARTING_BENCH_PRESS ) ), 2);
		if ($percent_goal_bench_press_2018 >= 100) {
			$percent_goal_bench_press_2018 = 100;
		}
		$percent_time_frame_bench_press_2018 = $percent_time_frame_body_weight_2018; // Rather than redoing the calculation, just using the same time-frame as tracking body weight
	}
	else if ($year == '2019') {
		// GOAL: Net Worth
		$percent_goal_net_worth_2019 = 	number_format((((ESTIMATED_AFTER_TAX_PERCENTAGE * $unreceived_seal_income / 100) + $current_cash + $current_assets - $current_liabilities - START_OF_YEAR_NET_WORTH) / (END_OF_YEAR_NET_WORTH_TARGET - START_OF_YEAR_NET_WORTH)) * 100, 2);
		if ($percent_goal_net_worth_2019 >= 100) {
			$percent_goal_net_worth_2019 = 100;
		}
		$percent_time_frame_net_worth_2019 = number_format((100 * $days_active_financial / 365), 2);
		// GOAL: Body Weight
		$percent_goal_body_weight_2019 = number_format(($most_recent_body_weight - STARTING_BODY_WEIGHT) * (100 / (BODY_WEIGHT_TARGET -STARTING_BODY_WEIGHT)), 2);
		if ($percent_goal_body_weight_2019 > 100) {
			$percent_goal_body_weight_2019 = 100;
		}
		$percent_time_frame_body_weight_2019 = number_format((100 * $days_active_body_weight / (((strtotime('January 1st, 2020')) - strtotime(START_DATE_STRING_BODY_WEIGHT)) / SEC_IN_DAY)), 2);
		// GOAL: Arm Size
		$start_date_upper_arm_size = date('Y/m/d 00:00:00', strtotime(START_DATE_STRING_UPPER_ARM_CIRC));
		$end_date_upper_arm_size = $today_date;
		$start_time_upper_arm_size = strtotime($start_date_upper_arm_size);
		$days_active_upper_arm_size = ceil(($today_time - $start_time_upper_arm_size) / (SEC_IN_DAY));
		
		$percent_goal_upper_arm_size_2019 = number_format(($most_recent_upper_arm_size - STARTING_UPPER_ARM_CIRC) * (100 / (UPPER_ARM_CIRC_TARGET - STARTING_UPPER_ARM_CIRC)), 2);
		if ($percent_goal_upper_arm_size_2019 > 100) {
			$percent_goal_upper_arm_size_2019 = 100;
		}
		$percent_time_frame_upper_arm_size_2019 = number_format((100 * $days_active_upper_arm_size / (((strtotime('January 1st, 2020')) - strtotime(START_DATE_STRING_UPPER_ARM_CIRC)) / SEC_IN_DAY)), 2);
		//echo " $percent_goal_upper_arm_size_2019 | $most_recent_upper_arm_size | " . STARTING_UPPER_ARM_CIRC . " | " . UPPER_ARM_CIRC_TARGET . " | " . STARTING_UPPER_ARM_CIRC . " | $days_active_upper_arm_size";
		// GOAL: Mile Time
		$percent_goal_mile_time_2019 = number_format(100 - (($best_mile_time - MILE_TIME_TARGET) * (100 / (STARTING_MILE_TIME - MILE_TIME_TARGET))), 2);
		if ($percent_goal_mile_time_2019 >= 100) {
			$percent_goal_mile_time_2019 = 100;
		}
		$percent_time_frame_running_2019 = number_format((100 * $days_active_running / (((strtotime('January 1st, 2020')) - strtotime(START_DATE_STRING_RUNNING)) / SEC_IN_DAY)), 2);
	}
//---CLOSE DATABASE CONNECTION------------------------------------------------------

//--- NOTIFICATIONS ----------------------------------------------------------------
	// Goal Progress Test
	$number_completed_goals = 0;
	$number_on_track_goals = 0;
	$number_below_par_goals = 0;
	function update_goals_status($percent_goal, $percent_time) {
		if ($percent_goal >= 100) {
			$GLOBALS['number_completed_goals'] += 1;
		}
		else if ($percent_goal >= $percent_time) {
			$GLOBALS['number_on_track_goals'] += 1;
		}
		else {
			$GLOBALS['number_below_par_goals'] += 1;
		}
	}
	if ($year == '2018') {
		update_goals_status($percent_goal_debt_free_2018, $percent_time_frame_debt_free_2018);
		update_goals_status($percent_goal_net_worth_2018, $percent_time_frame_net_worth_2018);
		update_goals_status($percent_goal_body_weight_2018, $percent_time_frame_body_weight_2018);
		update_goals_status($percent_goal_mile_time_2018, $percent_time_frame_running_2018);
		update_goals_status($percent_goal_bench_press_2018, $percent_time_frame_bench_press_2018);
	}
	$conn->close();

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" charset="utf-8">
    <meta name="description" content="change">
    <link rel="shortcut icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="resources/assets/images/favicon.png" type="image/x-icon">
    <title>Home Base 3.0</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|VT323|Orbitron:400,900" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="resources/css/reset.css">
    <link rel="stylesheet" type="text/css" href="resources/css/main-new.css">
    <link rel="stylesheet" type="text/css" href="resources/css/notifications.css">
    <link rel="stylesheet" type="text/css" href="resources/css/goals.css">
    <link rel="stylesheet" type="text/css" href="resources/css/fitness-new.css">
    <link rel="stylesheet" type="text/css" href="resources/css/weather.css">
    <link rel="stylesheet" type="text/css" href="resources/css/finance.css">
    
</head>

<body>
	<main>
	<?php
		include('resources/sections/modal.php');
		include('resources/sections/header.php');
		include('resources/sections/notifications.php');
		include('resources/sections/habits.php');
		include('resources/sections/goals.php');
		include('resources/sections/fitness.php');
		include('resources/sections/finance.php');
		include('resources/sections/weather.php');
	?>
	</main>
	<script>
		var year = <?php echo $year; ?>;
	</script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="resources/js/main.js"></script>
    <script type="text/javascript" src="resources/js/speech.js"></script>
    <script type="text/javascript" src="resources/js/greeting.js"></script>
    <script type="text/javascript" src="resources/js/goals.js"></script>
	<script type="text/javascript" src="resources/js/fitness.js"></script>
    <script type="text/javascript" src="resources/js/finance.js"></script>
    <script type="text/javascript" src="resources/js/weather.js"></script>
</body>

</html>