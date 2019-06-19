<?php

    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    $conn = connect_to_db();
    
    //var_dump( return_date_from_str('2019-02-28 13:54:00', 'datetime') );
    //echo return_days_between_dates('2019-06-09 08:00:00', return_date_from_str());
    $sd = '2019-06-02';
    $ed = '2019-06-02';
    echo return_end_of_day($ed);
    echo return_jss_income($conn, $sd, $ed);
    /*
    $ti_1 = return_ricks_pre_tax_income($conn, $sd, $ed, 7.5) + return_seal_pre_tax_salary($conn, $sd, $ed, 370) + return_jss_income($conn, $sd, $ed);
    $ti_2 = return_ricks_pre_tax_income($conn, $sd, $ed, 7.5) + return_seal_received_income($conn, $sd, $ed, 370) + return_seal_pre_tax_salary($conn, '2019-05-18', '2019-05-31', 16);
    echo "$ti_1 | $ti_2";
    */
?>