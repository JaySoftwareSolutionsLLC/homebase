<?php
    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants-2019.php');

    // Store posted exp_roi value in variable
    $category = $_POST['category'] ?? 'all';
    $sd = $_POST['sd'] ?? date('2019-01-01'); // start date
    $ed = $_POST['ed'] ?? date('now'); // end date
    // Connect to DB
    $conn = connect_to_db();
    //echo "$category";
    // Determine current account values
    switch ($category) {
        case 'S&D':
            $inc = return_seal_pre_tax_salary($conn, $sd, $ed, 367);
            break;
        case 'Ricks':
            $inc = return_ricks_pre_tax_income($conn, $sd, $ed, 7.5);
            break;
        
        default:
            $inc = return_seal_pre_tax_salary($conn, $sd, $ed, 367) + return_ricks_pre_tax_income($conn, $sd, $ed, 7.5) + return_jss_income($conn, $sd, $ed) + return_seal_pre_tax_bonus($conn, $sd, $ed);
            break;
    }
    $days = return_days_between_dates($sd, $ed);
    //echo "$sd | $ed<br/>";
    //echo "$inc | $days<br/>";
    $adi = $inc / $days;

    // Return age 60 ADW as html
    echo "$" . number_format($adi, 2) . "/day";

?>