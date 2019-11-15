SELECT  m.*
        ,(  SELECT value
            FROM personal_wellness_metric_logs AS ml
            WHERE m.id = ml.metric_id
            ORDER BY datetime DESC
            LIMIT 1) AS 'mr_value'
		,dm.active_threshold
FROM personal_wellness_metrics AS m
INNER JOIN personal_wellness_dimension_metric AS dm
    ON (m.id = dm.metric_id)
-- WHERE dm.dimension_id = 6
