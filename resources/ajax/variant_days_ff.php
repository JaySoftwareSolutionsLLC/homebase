<?php
    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2020.php');

    // Store posted exp_roi value in variable
    $withdrawal_rate = $_POST['withdrawalRate'] ?? AVG_DAILY_EXPENDITURE_TARGET; // Rate of withdrawal from cash savings
    $unreceived_ati = $_POST['unreceivedATI'];
    $date = $_POST['date'] ?? date('now'); // end date (typically today is what is passed here)
    
    $response = new stdClass;

    //echo "$withdrawal_rate | $unreceived_ati | $date";

    // Connect to DB
    $conn = connect_to_db();

    // Determine current account values
    $accounts = return_accounts_array($conn, $date);
    //var_dump($accounts);
    $days_financially_free = return_financial_freedom($accounts, $unreceived_ati, $withdrawal_rate);
    $financial_freedom_datetime = new datetime($date);
    $financial_freedom_datetime->modify("+$days_financially_free day");

    $response->days = $days_financially_free;
    $response->date = $financial_freedom_datetime->format('M jS, Y');

    echo json_encode($response);
?>