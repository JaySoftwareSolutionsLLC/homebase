/* Version 2.0 */
SELECT  fe.id, 
        fe.name,
        GROUP_CONCAT( DISTINCT CONCAT( fm.common_name, " (", fpem.type, ")" )
            ORDER BY fpem.type ASC
            SEPARATOR "\r\n") AS 'Muscle (Type)',
        GROUP_CONCAT( DISTINCT feq.name
            ORDER BY feq.name ASC
            SEPARATOR "\r\n") AS 'Equipment',
        (   SELECT fl.datetime
            FROM fitness_lifts AS fl
		    WHERE fe.id = fl.exercise_id
            ORDER BY fl.datetime DESC LIMIT 1 ) AS 'Most Recent Lift',
        (   SELECT COUNT(*) 
            FROM fitness_lifts AS fl
		    WHERE fe.id = fl.exercise_id) AS 'Total Lift Count'        

FROM fitness_exercises AS fe
LEFT JOIN fitness_pivot_exercises_muscles AS fpem
	ON (fe.id = fpem.exercise_id)
LEFT JOIN fitness_muscles AS fm
	ON (fpem.muscle_id = fm.id)
LEFT JOIN fitness_pivot_exercises_equipment AS fpee
	ON (fe.id = fpee.exercise_id)
LEFT JOIN fitness_equipment AS feq
	ON (fpee.equipment_id = feq.id)
    
GROUP BY fe.id, fe.name

ORDER BY (	SELECT COUNT(*) 
            FROM fitness_lifts AS fl
		    WHERE fe.id = fl.exercise_id) DESC


/* Version 1.0 */
SELECT  fe.id, 
        fe.name, 
        fm.id, 
        fm.common_name, 
        fpem.type, 
        fm.anatomical_name, 
        feq.name,
        (   SELECT fl.datetime
            FROM fitness_lifts AS fl
		    WHERE fe.id = fl.exercise_id
            ORDER BY fl.datetime DESC LIMIT 1 ) AS 'Most Recent Lift',
        (   SELECT COUNT(*) 
            FROM fitness_lifts AS fl
		    WHERE fe.id = fl.exercise_id) AS 'Total Lift Count'
        

FROM fitness_exercises AS fe
LEFT JOIN fitness_pivot_exercises_muscles AS fpem
	ON (fe.id = fpem.exercise_id)
LEFT JOIN fitness_muscles AS fm
	ON (fpem.muscle_id = fm.id)
LEFT JOIN fitness_pivot_exercises_equipment AS fpee
	ON (fe.id = fpee.exercise_id)
LEFT JOIN fitness_equipment AS feq
	ON (fpee.equipment_id = feq.id)

ORDER BY (	SELECT COUNT(*) 
            FROM fitness_lifts AS fl
		    WHERE fe.id = fl.exercise_id) DESC