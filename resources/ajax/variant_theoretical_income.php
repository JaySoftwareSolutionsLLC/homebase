<?php

    // Include resources
    $year = date('Y');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . "/homebase/resources/constants-$year.php");
    
    // Store posted exp_roi value in variable
    $params = $_POST['params'];
    $start_of_year = "$year-01-01";
    $today = date('Y-m-d');
    $end_of_year = "$year-12-31";
    $today_dt = new DateTime($today);
    $end_of_year_dt = new DateTime($end_of_year);
    $date_diff = date_diff($today_dt, $end_of_year_dt);
    $weeks_remaining_in_year = $date_diff->days / 7;
    
    // Set Params

    $response = new stdClass;

    // Connect to DB
    $conn = connect_to_db();

    // Income to date
	$net_income = return_ricks_pre_tax_income($conn, $start_of_year, $today, HOURLY_WAGE_RICKS)
				+ return_seal_received_income($conn, $start_of_year, $today)
				+ return_jss_income($conn, $start_of_year, $today)
				+ return_seal_unreceived_income($conn, $year, $start_of_year, $today)
				+ return_annual_check_adjustments($year);
    $response->net_income = $net_income;
    // Theoretical Future Income
    $theoretical_future_pretax_income = 0;  
    if (in_array('SD', $params)) {
        $correct_hourly = HOURLY_WAGES_SEAL[count(HOURLY_WAGE_SEAL) - 1];
        $theoretical_future_pretax_income += ((($weeks_remaining_in_year * 40) + CASHABLE_PTO_HOURS) * $correct_hourly) + REMAINING_BONUSES + REMAINING_EMP_401K_DELTA;
    }
    $ricks_params = [];
    if (in_array('MPM', $params)) {
        $ricks_params[] = 'MPM';
    }
    if (in_array('TPM', $params)) {
        $ricks_params[] = 'TPM';
    }
    if (in_array('WPM', $params)) {
        $ricks_params[] = 'WPM';
    }
    if (in_array('RPM', $params)) {
        $ricks_params[] = 'RPM';
    }
    if (in_array('FPM', $params)) {
        $ricks_params[] = 'FPM';
    }
    if (in_array('SPM', $params)) {
        $ricks_params[] = 'SPM';
    }
    if (!empty($ricks_params)) {
        $theoretical_future_pretax_income += return_ricks_expected_upcoming_income($conn, $today, $end_of_year, HOURLY_WAGE_RICKS, $ricks_params);
    }
    $response->theoretical_future_pretax_income = $theoretical_future_pretax_income;

    $response->theoretical_future_income = "$" . number_format($net_income + $theoretical_future_pretax_income, 0);

    // Return response
    echo json_encode($response);

?>