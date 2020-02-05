SELECT 	h.id
		,h.name
        ,h.minutes_to_complete
        ,CONCAT(hm.influence, ' impact on ', m.name) AS 'impact_str'
        ,hm.frequency_int
        ,hm.frequency_window
        ,CONCAT(`hm`.`frequency_int`,'x Per ',`hm`.`frequency_window`) AS `freq_trgt_str`
        ,(SELECT COUNT(0) 
          FROM personal_wellness_habit_logs AS hl 
          WHERE ((`h`.`id` = `hl`.`habit_id`) and (`hl`.`status` = 'Completed') and (case when ((`hm`.`frequency_window` = 'Day') and (dayofmonth(`hl`.`datetime`) = dayofmonth(now())) and (month(`hl`.`datetime`) = month(now())) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Week') and (week(`hl`.`datetime`,0) = week(now(),0)) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Month') and (month(`hl`.`datetime`) = month(now())) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Quarter') and (quarter(`hl`.`datetime`) = quarter(now())) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Year') and (year(`hl`.`datetime`) = year(now()))) then 1 else 0 end))) AS `completed`
      	,(SELECT count(0)
          FROM `jaysoftw_homebase`.`personal_wellness_habit_logs` `hl` 
          WHERE ((`h`.`id` = `hl`.`habit_id`) and (`hl`.`status` = 'Started') and (case when ((`hm`.`frequency_window` = 'Day') and (dayofmonth(`hl`.`datetime`) = dayofmonth(now())) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Week') and (week(`hl`.`datetime`,0) = week(now(),0)) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Month') and (month(`hl`.`datetime`) = month(now())) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Quarter') and (quarter(`hl`.`datetime`) = quarter(now())) and (year(`hl`.`datetime`) = year(now()))) then 1 when ((`hm`.`frequency_window` = 'Year') and (year(`hl`.`datetime`) = year(now()))) then 1 else 0 end))) AS `started`,(select count(0) from `jaysoftw_homebase`.`personal_wellness_habit_logs` `hl` where ((`hl`.`status` = 'Completed') and (`h`.`id` = `hl`.`habit_id`) and (month(`hl`.`datetime`) = month(now())) and (dayofmonth(`hl`.`datetime`) = dayofmonth(now())))) AS `logged_today`,`hm`.`frequency_type` AS `frequency_type`
          ,h.max_logs_per_day AS max_logs_per_day
FROM personal_wellness_habits AS h
INNER JOIN personal_wellness_habit_metric AS hm
	ON (h.id = hm.habit_id)
INNER JOIN personal_wellness_metrics AS m
    ON (hm.metric_id = m.id)
INNER JOIN personal_wellness_metric_logs AS ml
	ON((`ml`.`id` = (	SELECT `mls`.`id` 
                     	FROM personal_wellness_metric_logs `mls` 
                     	WHERE (`m`.`id` = `mls`.`metric_id`) 
                     	ORDER BY `mls`.`datetime` DESC LIMIT 1))) 
WHERE 	(hm.active_threshold IS NULL 
	OR hm.active_threshold = 'Active' 
    OR ((`hm`.`active_threshold` = 'caution') and ((ml.value <= m.caution_min) OR (ml.value >= m.caution_max))) 
    OR ((`hm`.`active_threshold` = 'warning') and ((`ml`.`value` <= `m`.`warning_min`) or (`ml`.`value` >= `m`.`caution_max`))))
    AND (hm.effective_datetime <= NOW() AND (hm.expire_datetime >= NOW() OR hm.expire_datetime IS NULL))
ORDER BY `hm`.`influence` desc,`h`.`minutes_to_complete`