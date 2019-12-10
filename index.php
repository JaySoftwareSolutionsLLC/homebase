<?php

/* Local DB Connection will become useful if we are able to mirror databases which would allow offline work to be done efficiently...DEPRECATED until then
//---CONNECT TO LOCAL DB------------------------------------------------------------

	error_reporting(E_ERROR);
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/brettjaybrewster/homebase/resources/constants.php');
	
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
		$today_date = date('Y-m-d', mktime(0, 0, 0, 12, 31, 2018));
		$today_datetime = new DateTime('December 31st 2018');
	}
	else {
		$today_time = time();
		$today_datetime = new DateTime();
		$today_date = date_format($today_datetime, 'Y-m-d');
	}
	//echo $today_time;
	//var_dump( $today_datetime );
	//var_dump( $today_date );
	$last_sunday = "'" . date('Y-m-d', strtotime('last Sunday')) . "'";
	$days_left_in_year = floor((strtotime("January 1st, " . ($year + 1)) - $today_time) / SEC_IN_DAY); // Needs to stay this way for 2018 and 2019 to both work

	// DEPRECATED 2018.10.28 $current_bench_press = 185;

//---SELECT FROM DATABASE-----------------------------------------------------------

	// FINANCIAL--------------------------------------------------------------------

	// Time Related Variables
	$start_date_financial = date('Y-m-d', strtotime(START_DATE_STRING_FINANCIAL));
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
	$accounts = return_accounts_array($conn, $year, $today_date);

	$account_types = array();
	foreach ($accounts as $a) {
		if ( empty( $account_types["$a->type"] ) ) {
			$account_types["$a->type"] = intval($a->mrv);
			//echo $a->mrv;
		}
		else {
			$account_types["$a->type"] += intval($a->mrv);
			//echo $a->mrv;
		}
	}

	// Determine total expenses since start date
	$q = "SELECT SUM(amount) FROM finance_expenses WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$net_expenditure = $row[0];

	// Seal Hours
	$q = "SELECT SUM((time_to_sec(TIMEDIFF(departure_time, arrival_time)) / (60 * 60)) - (break_min / 60)) FROM finance_seal_shifts WHERE date >= '$start_date_financial' AND date <= '$end_date_financial'";
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
		$annual_check_adjustment = (-1 * 11 * 8 * 25) + (-62.5); // 11 days in 2019 checks were from hours worked in 2018 plus a $62.50 healthcare waive bonus 
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

	$unreceived_after_tax_seal_income = round( (ESTIMATED_AFTER_TAX_PERCENTAGE * $unreceived_seal_income / 100) , 2);

	$account_types['unreceived ATI'] = $unreceived_after_tax_seal_income;

	// NET INCOME : Hourlywage at ricks multiplied by ricks hours + net tips from ricks + net recorded income from seal and design + unreceived (but earned) income from seal and design
	$net_income = (HOURLY_WAGE_RICKS * ( $net_ricks_hours - $net_ricks_otb_hours ) ) + $net_ricks_tips + $net_seal_income + $unreceived_seal_income + $annual_check_adjustment;
	// (46.2) + 106 + 2062.5 + 1000 - 2200

	// echo "$net_income | $net_ricks_hours | $net_ricks_otb_hours | $net_ricks_tips | $net_seal_income | $unreceived_seal_income | $annual_check_adjustment ";

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

	$current_net_worth = 0;
	foreach ($account_types as $name=>$val) {
		$current_net_worth += $val;
	}
	//$current_net_worth = $current_assets + $current_cash - $current_liabilities;

	$estimated_2018_income = number_format(PRE_JUNE_RICKS_INCOME + ($adi  * ($days_left_in_year_financial + $days_active_financial)), 0);

	$estimated_2019_income = number_format(($adi * 365), 0);

	$avg_monday_pm_net_income = 0;
	$avg_thursday_pm_net_income = 0;
	$avg_saturday_pm_net_income = 0;
	$qry = "	SELECT 	
					DAYNAME(date) AS 'dow',
					ROUND( 
						AVG(
							CASE
								WHEN type IN ('AM', 'PM') THEN (tips + (hours * 7.5))
								WHEN type IN ('OTB') THEN (tips)
								ELSE 0
							END
						) , 2
					) AS 'AVG income',
					ROUND( AVG(hours) , 2 ) AS 'AVG hours'
				FROM `finance_ricks_shifts` 
				WHERE   type = 'PM'
				AND (
					MONTH(date) > MONTH(CURRENT_DATE)
					OR 
					(MONTH(date) = MONTH(CURRENT_DATE)
					AND DAY(date) >= DAY(CURRENT_DATE) ) 
					)
				GROUP BY DAYNAME(date) ";
	$res = $conn->query($qry);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			switch ($row['dow']) {
				case 'Monday':
					$avg_monday_pm_net_income = $row['AVG income'];
					break;
				case 'Thursday':
					$avg_thursday_pm_net_income = $row['AVG income'];
					break;
				case 'Saturday':
					$avg_saturday_pm_net_income = $row['AVG income'];
					break;
				default:
					break;
			}
		}
	}
	$avg_full_week_ricks_income = $avg_monday_pm_net_income + $avg_thursday_pm_net_income + $avg_saturday_pm_net_income;

	$avg_full_week_seal_income = 40 * $correct_hourly;
	$weeks_left_in_year = $days_left_in_year / 7;
	// Calculate estimated EOY Net Worth
	$appreciated_EOY_accounts_total = 0;
	foreach ($accounts as $a) { // For each account value, calculate its expected EOY value due to de/appreciation
		if ( empty( $a->exp_roi ) ) { // If it will not de/appreciate then just add current value
			$appreciated_EOY_accounts_total += $a->mrv;
			continue;
		}
		else { // If it will de/appreciate then add the projected EOY value
			$p = $a->mrv;
			$end_of_2019_val = round( ( $p * pow( ( 1 + ( $a->exp_roi / 100 ) ), ($days_left_in_year / 365) ) ) , 0 );
			$appreciated_EOY_accounts_total += $end_of_2019_val;
			continue;
		}
	}
	$appreciated_EOY_accounts_total += $account_types['unreceived ATI']; // Add unreceived ATI because this is not housed in accounts array

	$theoretical_future_pretax_income = ( CASHABLE_PTO_HOURS * $correct_hourly) + (($avg_full_week_ricks_income + $avg_full_week_seal_income) * $weeks_left_in_year);
	//echo "$theoretical_future_pretax_income<br/><br/>";
	$theoretical_EOY_net_worth = $appreciated_EOY_accounts_total + round((($theoretical_future_pretax_income) * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - (AVG_DAILY_EXPENDITURE_TARGET * $days_left_in_year) , 0 ); // In addition to projected account values
	$theoretical_income_this_year = $net_income + $theoretical_future_pretax_income;
	//echo "$theoretical_income_this_year<br/><br/>";
	/*
	echo "$appreciated_EOY_accounts_total<br/><br/>";
	echo "$theoretical_EOY_net_worth <br/><br/>";
	echo "$appreciated_EOY_accounts_total + round(((( CASHABLE_PTO_HOURS * $correct_hourly) + (($avg_full_week_ricks_income + $avg_full_week_seal_income) * $weeks_left_in_year)) * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - (AVG_DAILY_EXPENDITURE_TARGET * $days_left_in_year) , 0 );";
	*/
	// DEPRECATED: Doesn't account for seasonality well enough $estimated_EOY_net_worth += ( ( ( $adi * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100) ) - $ade ) * $days_left_in_year_financial );

	$theoretical_net_worth_contribution = round((($net_income + ( CASHABLE_PTO_HOURS * $correct_hourly) + (($avg_full_week_ricks_income + $avg_full_week_seal_income) * $weeks_left_in_year)) * (ESTIMATED_AFTER_TAX_PERCENTAGE / 100)) - ($net_expenditure + (AVG_DAILY_EXPENDITURE_TARGET * $days_left_in_year)) , 0 ); // Theoretical NW Cont. if I grind out 3 days/week @ Ricks and don't take any PTO @ S&D
	//echo $theoretical_net_worth_contribution;
	$opportunity_surplus = round($theoretical_net_worth_contribution - ANNUAL_NET_WORTH_CONTRIBUTION_TARGET , 0 );	

	$current_est_nw_contribution = ( $net_income * ( ESTIMATED_AFTER_TAX_PERCENTAGE / 100 ) ) - ( $net_expenditure );
	// echo ("Opportunity Cost Surplus: $opportunity_surplus | $net_income | $avg_full_week_ricks_income | $avg_full_week_seal_income | $weeks_left_in_year <br/><br/><br/>");

	//---FITNESS--------------------------------------------------------------------

	// Running

	$start_date_running = date('Y-m-d 00:00:00', strtotime(START_DATE_STRING_RUNNING));
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
	
	$start_date_body_weight = 	date('Y-m-d', strtotime(START_DATE_STRING_BODY_WEIGHT));
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

	// Circumference Tracking

	$most_recent_upper_arm_circ_measurement = 0;
	$qry_most_recent_upper_arm_circ_measurement = " SELECT datetime
													FROM `fitness_measurements_circumferences`
													WHERE circumference_id = 4
													ORDER BY datetime DESC
													LIMIT 1 ";
	$res_most_recent_upper_arm_circ_measurement = $conn->query($qry_most_recent_upper_arm_circ_measurement);
	$row_most_recent_upper_arm_circ_measurement = $res_most_recent_upper_arm_circ_measurement->fetch_assoc();
	$most_recent_upper_arm_circ_measurement = $row_most_recent_upper_arm_circ_measurement['datetime'];
		// Turn the following 3 lines into a return_datediff function
	$most_recent_upper_arm_circ_measurement_dt = return_date_from_str($most_recent_upper_arm_circ_measurement, 'datetime');
	$interval_since_most_recent_upper_arm_circ_measurement = date_diff($today_datetime, $most_recent_upper_arm_circ_measurement_dt, true);
	$days_since_most_recent_upper_arm_circ_measurement = $interval_since_most_recent_upper_arm_circ_measurement->days;



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
			//echo "$muscle_id | $muscle_mrf_time | $muscle_mrf_hours" . date('Y-m-d H:i:s', $muscle_mrf_time) . " <br/>";
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

	// Optimal Health
	$q = "SELECT (SUM(optimal_health) / COUNT(*)) FROM personal_day_info WHERE date >= '$start_date_financial' AND date <= '$end_date_financial' AND optimal_health IS NOT NULL";
	$res = $conn->query($q);
	$row = mysqli_fetch_row($res);
	$optimal_health_percentage = $row[0];
	//echo $optimal_health_percentage;

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

		/*
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
		$start_date_upper_arm_size = date('Y-m-d 00:00:00', strtotime(START_DATE_STRING_UPPER_ARM_CIRC));
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
		*/
		// Most Recent Expense Review
		$qry_most_recent_expense_review = " SELECT date
											FROM `personal_day_info`
											WHERE expense_review
											ORDER BY date DESC
											LIMIT 1 ";
		$res_most_recent_expense_review = $conn->query($qry_most_recent_expense_review);
		$row_most_recent_expense_review = $res_most_recent_expense_review->fetch_assoc();
		$most_recent_expense_review = $row_most_recent_expense_review['date'];
			// Turn the following 3 lines into a return_datediff function
		$most_recent_expense_review_dt = return_date_from_str($most_recent_expense_review, 'datetime');
		$interval_since_most_recent_expense_review = date_diff($today_datetime, $most_recent_expense_review_dt, true);
		$days_since_most_recent_expense_review = $interval_since_most_recent_expense_review->days;

		// Certification Hours
		$q = "SELECT SUM(software_cert_hours) FROM personal_day_info WHERE date >= '" . START_DATE_STRING_CERT_GOAL . "' AND date <= '" . END_DATE_STRING_CERT_GOAL . "'";
		$res = $conn->query($q);
		$row = mysqli_fetch_row($res);
		$software_cert_hours = $row[0];
	
	}
	//---HABITS---------------------------------------------------------------------
	$habits_list_html = return_habit_list_html($conn);

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

	// Note related cautions/warnings
	$notifications = array(); // Array to house notification objects
	$qry_caution_notes = "SELECT 	id
									,caution_datetime
									,summary
									,est_min_to_comp
							FROM `personal_notes`
							WHERE 	caution_datetime <= '" . date_format( $today_datetime, 'Y-m-d H:i:s' ) . "'
								AND (warning_datetime IS NULL OR warning_datetime > '" . date_format( $today_datetime, 'Y-m-d H:i:s' ) . "')
								AND complete_datetime IS NULL
							ORDER BY caution_datetime ASC; ";
	$res_caution_notes = $conn->query($qry_caution_notes);
	if ($res_caution_notes->num_rows > 0) {
		while($row = $res_caution_notes->fetch_assoc()) {
			$notification = new stdClass();
			$notification->type = 'caution';
			$notification->link = 'https://www.brettjaybrewster.com/homebase/resources/forms/notes.php?id=' . $row['id'];
			$notification->message = /* "(" . str_replace(' ', ' @ ', $row['caution_datetime']) . ") " . */ $row['summary'];
			$notification->est_min_to_comp = $row['est_min_to_comp'];
			$notifications[] = $notification;
		}
	}
	$qry_warning_notes = "	SELECT 	id
									,warning_datetime
									,summary
									,est_min_to_comp
							FROM `personal_notes`
							WHERE 	warning_datetime <= '" . date_format( $today_datetime, 'Y-m-d H:i:s' ) . "'
								AND complete_datetime IS NULL
								ORDER BY warning_datetime ASC; ; ";
	$res_warning_notes = $conn->query($qry_warning_notes);
	if ($res_warning_notes->num_rows > 0) {
		while($row = $res_warning_notes->fetch_assoc()) {
			$notification = new stdClass();
			$notification->type = 'warning';
			$notification->link = 'https://www.brettjaybrewster.com/homebase/resources/forms/notes.php?id=' . $row['id'];
			$notification->message = /* "(" . str_replace(' ', ' @ ', $row['warning_datetime']) . ") " . */ $row['summary'];
			$notification->est_min_to_comp = $row['est_min_to_comp'];
			$notifications[] = $notification;
		}
	}

	$warning_notes = array();

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
    <link rel="stylesheet" type="text/css" href="resources/css/modal.css">
    <link rel="stylesheet" type="text/css" href="resources/css/notifications.css">
    <link rel="stylesheet" type="text/css" href="resources/css/habits.css">
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
		var sd = '<?= $start_date_financial; ?>';
		var ed = '<?= $today_date; ?>';
	</script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="resources/resources.js"></script>
    <script type="text/javascript" src="resources/js/main.js"></script>
    <script type="text/javascript" src="resources/js/speech.js"></script>
    <script type="text/javascript" src="resources/js/greeting.js"></script>
    <script type="text/javascript" src="resources/js/goals.js"></script>
    <script type="text/javascript" src="resources/js/habits.js"></script>
	<script type="text/javascript" src="resources/js/fitness.js"></script>
    <script type="text/javascript" src="resources/js/finance.js"></script>
    <script type="text/javascript" src="resources/js/weather.js"></script>
</body>

</html>