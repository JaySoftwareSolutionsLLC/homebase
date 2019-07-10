<?php

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Store posted exp_roi value in variable
    $exp_roi = $_POST['exproi'] ?? null;

    // Connect to DB
    $conn = connect_to_db();
    
    // Determine current account values
    $accounts = return_accounts_array($conn, $year);

    // Determine years until 60
    $birthdate = return_date_from_str('April 28th 1994', 'datetime');
	$birthdate_age_sixty = clone $birthdate;
	$birthdate_age_sixty->modify('+60 years');
	$today_dt = return_date_from_str('today', 'datetime');
	$interval_until_60 = date_diff($today_dt, $birthdate_age_sixty, false);
	$days_until_60 = $interval_until_60->days;
    $years_until_60 = round( $days_until_60 / 365.25 , 2 );

    // Determine age 60 annual withdrawal rate
    $theoretical_age_60_annual_withdrawal_rate = return_theoretical_age_60_withdrawal_rate($accounts, $years_until_60, $exp_roi);
    
    // Return age 60 ADW as html
    echo "$" . number_format($theoretical_age_60_annual_withdrawal_rate / 365.25 , 2) . "/day";

?>