<?php

    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    $conn = connect_to_db();
    
    //var_dump(post_values_are_set(array('test')));
    //var_dump( return_date_from_str('2019-02-28 13:54:00', 'datetime') );
    //echo return_days_between_dates('2019-06-09 08:00:00', return_date_from_str());
    $sd = return_date_relative_to_today('-364 days');
    $ed = return_date_from_str('today');
    //echo return_end_of_day($ed);
    //echo return_jss_income($conn, $sd, $ed);
    //echo insert_row($conn, 'personal_notes', array('summary' => 'Test', 'description' => 'Testing...1,2,3', 'type' => 'positive experience'));
    /*
    $ti_1 = return_ricks_pre_tax_income($conn, $sd, $ed, 7.5) + return_seal_pre_tax_salary($conn, $sd, $ed, 370) + return_jss_income($conn, $sd, $ed);
    $ti_2 = return_ricks_pre_tax_income($conn, $sd, $ed, 7.5) + return_seal_received_income($conn, $sd, $ed, 370) + return_seal_pre_tax_salary($conn, '2019-05-18', '2019-05-31', 16);
    echo "$ti_1 | $ti_2";
    */

    // 2019.08.09
    $ricks_2018_income_remainder_of_year = return_ricks_pre_tax_income($conn, $sd, return_date_from_str('2018-12-31'), 7.5);
    echo "Ricks 2018 income from this day in 2018 to end of year: $ricks_2018_income_remainder_of_year <br/>";

    // 2019.06.11
    $ricks_pre_tax_income = return_ricks_pre_tax_income($conn, $sd, $ed, 7.5);
    $seal_received_income = return_seal_received_income($conn, $sd, $ed, 370);
    $past_365_day_income = $ricks_pre_tax_income + $seal_received_income;
    echo "Ricks Pre Tax Income: $ricks_pre_tax_income <br/>";
    echo "Ricks Hours Worked: " . return_ricks_hours($conn, $sd, $ed) . "<br/>";
    echo "S&D Received Income: $seal_received_income <br/>";
    echo "Net Income (not including unreceived) $past_365_day_income <br/>";

    $past_365_day_hours_worked = return_seal_hours($conn, $sd, $ed) + return_ricks_hours($conn, $sd, $ed);
    echo $past_365_day_hours_worked . " Net Hours Worked.<br/>";
    $past_365_day_hours_commuted = return_estimated_commute_time($conn, $sd, $ed);
    echo $past_365_day_hours_commuted . " Net Hours Commuted (Estimate).<br/>";

    echo "<pre>";
    var_dump(return_accounts_array($conn, 2019));
    echo "</pre>";
    $accounts = return_accounts_array($conn, 2019);
    $updated_accounts = $accounts;
    foreach($updated_accounts as $ua) {
        if ($ua->name == 'Schwab Beneficiary') {
            $ua->mrv += 13000;
        }
    }
    echo "<pre>" . var_dump($updated_accounts). "</pre>";
    echo return_theoretical_age_60_withdrawal_rate($updated_accounts, 34.75, null);
?>