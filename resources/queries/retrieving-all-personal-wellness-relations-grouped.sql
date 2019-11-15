SELECT  d.id
		, d.name
		, d.description
        , AVG(dl.rating) AS 'AVG Rating'
        , ( SELECT TOP(1) dl_sub.rating
            FROM personal_wellness_dimension_logs AS dl_sub
            WHERE d.id = dl_sub.dimension_id
            ORDER BY dl_sub.datetime) AS 'MR Rating'
        
        /*
        , dl.rating
        , dl.datetime
        , m.name
        , ROUND((m.minutes_to_measure *	CASE 
                                            WHEN m.frequency = 'Daily' THEN 7
                                            WHEN m.frequency = 'Weekly' THEN 1
                                            WHEN m.frequency = 'Monthly' THEN (12/52)
                                            WHEN m.frequency = 'Quarterly' THEN 1/4
                                            WHEN m.frequency = 'Annually' THEN 1/52
                                        END ),2) AS 'metric min/wk'
		, h.name
        , h.cue
        , ROUND((h.minutes_to_complete * hm.frequency_int * 	CASE 
                                                            WHEN hm.frequency_window = 'Day' THEN 7
                                                            WHEN hm.frequency_window = 'Week' THEN 1
                                                            WHEN hm.frequency_window = 'Month' THEN (12/52)
                                                            WHEN hm.frequency_window = 'Quarter' THEN 1/4
                                                            WHEN hm.frequency_window = 'Year' THEN 1/52
                                                        END ),2) AS 'habit min/wk'
        */

FROM personal_wellness_dimensions AS d
LEFT JOIN personal_wellness_dimension_logs AS dl
ON (d.id = dl.dimension_id)
LEFT JOIN personal_wellness_dimension_metric AS dm
ON (d.id = dm.dimension_id)
LEFT JOIN personal_wellness_metrics AS m
ON (dm.metric_id = m.id)
LEFT JOIN personal_wellness_habit_metric AS hm
ON (m.id = hm.metric_id)
LEFT JOIN personal_wellness_habits AS h
ON (hm.habit_id = h.id)
GROUP BY    d.id,
            d.name,
            d.description