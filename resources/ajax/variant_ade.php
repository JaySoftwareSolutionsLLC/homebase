<?php
    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');

    // Store posted exp_roi value in variable
    $category = $_POST['category'] ?? 'all';
    $sd = $_POST['sd'] ?? date('2021-01-01'); // start date
    $ed = $_POST['ed'] ?? date('now'); // end date
    // Connect to DB
    $conn = connect_to_db();
    //echo "$category";
    // Determine current account values
    $exp = return_expenditure($conn, $sd, $ed, $category);
    $days = return_days_between_dates($sd, $ed) + 1;
    $ade = $exp / $days;

    // Return age 60 ADW as html
    echo "$" . number_format($ade, 2) . "/day";

?>