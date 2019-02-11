SELECT datetime 
FROM `fitness_lifts` 
WHERE exercise_id IN (  SELECT exercise_id 
                        FROM `fitness_pivot_exercises_muscles` 
                        WHERE muscle_id = $muscle_id 
                            AND datetime >= '$start_date_body_weight 00:00:00' 
                            AND datetime <= '$end_date_body_weight 23:59:59' 
                            AND type = 'primary') 
ORDER BY datetime DESC LIMIT 1