INSERT INTO fitness_best_lifts (exercise_id, workout_structure_id, weight, total_reps, lift_id)
	SELECT fl.exercise_id, fl.workout_structure_id, MAX(fl.weight) AS 'weight', 
                        (SELECT total_reps 
                         FROM fitness_lifts 
                         WHERE exercise_id = fl.exercise_id 
                          AND workout_structure_id = fl.workout_structure_id 
                          AND weight = MAX(fl.weight)
                         ORDER BY total_reps DESC
                         LIMIT 1) AS 'total_reps',
                         (SELECT id 
                         FROM fitness_lifts 
                         WHERE exercise_id = fl.exercise_id 
                          AND workout_structure_id = fl.workout_structure_id 
                          AND weight = MAX(fl.weight)
                         ORDER BY total_reps DESC
                         LIMIT 1) AS 'lift_id'

                FROM `fitness_lifts` AS fl

                GROUP BY fl.exercise_id, fl.workout_structure_id
           
           		ORDER BY fl.exercise_id, fl.workout_structure_id