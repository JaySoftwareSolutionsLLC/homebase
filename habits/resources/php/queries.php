<?php
    $sql_wellness_dimensions = 
        "SELECT pwd.id
                , pwd.name 
        FROM `personal_wellness_dimensions` AS pwd ";

    $sql_habit_metrics = 
        "SELECT 	
            hm.id
            , m.name
            , m.id
            , h.name
            , h.id
            , hm.influence
            , h.minutes_to_complete
            , CONCAT(hm.frequency_type, ' ', hm.frequency_int, '/', hm.frequency_window) AS 'freqstr'
            , hm.effective_datetime
            , hm.expire_datetime

        FROM `personal_wellness_habit_metric` AS hm
        INNER JOIN personal_wellness_habits AS h
        ON (hm.habit_id = h.id)
        INNER JOIN personal_wellness_metrics AS m
        ON (hm.metric_id = m.id)

        WHERE 	hm.effective_datetime < ?
        AND (hm.expire_datetime IS NULL OR hm.expire_datetime > ?)

        ORDER BY hm.influence DESC ";

    $sql_habit_logs = 
        "   SELECT hl.habit_id
                    , hl.datetime
                    , hl.status 
            FROM `personal_wellness_habit_logs` AS hl
            WHERE 	DATE(datetime) >= ?
                AND DATE(datetime) <= ? ";
?>