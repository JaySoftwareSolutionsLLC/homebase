SELECT 
	muscles.id, muscles.common_name, circs.id AS 'circ_id', circs.name AS 'circ_name', circs.ideal, rec_times.ideal_recovery
	FROM `fitness_muscles` AS muscles 
	INNER JOIN `fitness_circumferences` AS circs ON (muscles.circumference_id = circs.id)
	INNER JOIN `fitness_ideal_recovery_times` AS rec_times ON (muscles.id = rec_times.muscle_id)
	WHERE rec_times.workout_structure_id = $workout_structure