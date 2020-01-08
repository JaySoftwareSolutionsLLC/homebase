<?php

    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    $conn = connect_to_db();

    $sd = return_date_from_str('2019-01-01');
    $ed = return_date_from_str('2019-12-31');

    echo "S&D Received Income:" . return_seal_received_income($conn, $sd, $ed) . "<br/>";
    echo "Ricks Pre Tax Income:" . return_ricks_pre_tax_income($conn, $sd, $ed, 7.5) . "<br/>";
    echo "JSS Received Income" . return_jss_income($conn, $sd, $ed) . "<br/>";
    echo "Ricks Hours Worked: " . return_ricks_hours($conn, $sd, $ed) . "<br/>";
    echo "Ricks 2018 income from this day in 2018 to end of year: " . return_ricks_pre_tax_income($conn, return_date_from_str('2018-12-19'), return_date_from_str('2018-12-31'), 7.5) . "<br/>";
    echo "Net Received Income: " .
    (
    intval(return_ricks_pre_tax_income($conn, $sd, $ed, 7.5))
	+ intval(return_seal_received_income($conn, $sd, $ed))
    + intval(return_jss_income($conn, $sd, $ed))
    //+ intval((-1 * 11 * 8 * 25) + -62.5) // Subtract $ from this year that was worked last year
    ) . "<br/>";

    echo "RICKS Q1: $" . return_ricks_pre_tax_income($conn, '2019-01-01', '2019-03-31', 7.5) . "/" . return_ricks_hours($conn, '2019-01-01', '2019-03-31') . "hrs<br/>";
    // echo "RICKS Q1 INCOME (2018): " . return_ricks_pre_tax_income($conn, '2018-01-01', '2018-03-31', 7.5) . "<br/>";
    
    echo "RICKS Q2: $" . return_ricks_pre_tax_income($conn, '2019-04-01', '2019-06-30', 7.5) . "/" . return_ricks_hours($conn, '2019-04-01', '2019-06-30') . "hrs<br/>";
    // echo "RICKS Q2 INCOME (2018): " . return_ricks_pre_tax_income($conn, '2018-04-01', '2018-06-30', 7.5) . "<br/>";
    
    echo "RICKS Q3: $" . return_ricks_pre_tax_income($conn, '2019-07-01', '2019-09-30', 7.5) . "/" . return_ricks_hours($conn, '2019-07-01', '2019-09-30') . "hrs<br/>";
    echo "RICKS Q3 INCOME (2018): " . return_ricks_pre_tax_income($conn, '2018-07-01', '2018-09-30', 7.5) . "<br/>";

    echo "RICKS Q4: $" . return_ricks_pre_tax_income($conn, '2019-10-01', '2019-12-31', 7.5) . "/" . return_ricks_hours($conn, '2019-10-01', '2019-12-31') . "hrs<br/>";
    echo "RICKS Q4 INCOME (2018): " . return_ricks_pre_tax_income($conn, '2018-10-01', '2018-12-31', 7.5) . "<br/>";
?>