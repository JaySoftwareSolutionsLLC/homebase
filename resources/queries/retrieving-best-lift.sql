SELECT * 

FROM `fitness_lifts` AS fl

WHERE  	fl.exercise_id = 14
	AND fl.workout_structure_id = 2

ORDER BY fl.weight DESC, fl.total_reps DESC

LIMIT 1;