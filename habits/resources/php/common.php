<?php

function return_dates_match($datetime1, $datetime2) {
    return $datetime1->format('Y-m-d') == $datetime2->format('Y-m-d');
}

function return_dates_between($datetime1, $datetime2, $format = 'Y-m-d', $datetime1_inclusive = true, $datetime2_inclusive = true) {
    $dates = array();
    if (!$datetime1_inclusive) {
        $datetime1->modify('+1 day');
    }
    if ($datetime2_inclusive) {
        $datetime2->modify('+1 day');
    }
    $interval = new DateInterval('P1D'); 
    $period = new DatePeriod($datetime1, $interval, $datetime2); 

    foreach($period as $date) {                  
        $dates[] = $date->format($format);  
    } 

    return $dates;
}

function return_scheduled_time_estimate($date, $habit_metrics, $habit_logs) {
    $minutes = 0;
    foreach($habit_logs as $hl) {
        if ($hl->status == 'Started' && $hl->date == $date) {
            foreach($habit_metrics as $hm) {
                if ($hm->habit_id == $hl->habit_id) {
                    $minutes += $hm->habit_min_to_comp;
                    break;
                }
            }
        }
    }
    return $minutes;
}

function return_influence_points($date, $habit_metrics, $habit_logs, $status = 'Completed') {
    $inf_pts = 0;
    foreach($habit_logs as $hl) {
        if ($hl->status == $status && $hl->date == $date) {
            foreach($habit_metrics as $hm) {
                if ($hm->habit_id == $hl->habit_id) {
                    $inf_pts += $hm->influence;
                    break;
                }
            }
        }
    }
    return $inf_pts;
}

?>