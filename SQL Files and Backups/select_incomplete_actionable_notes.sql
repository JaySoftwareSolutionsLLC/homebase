SELECT * FROM `personal_notes`  
WHERE 	( caution_datetime IS NOT NULL
		OR warning_datetime IS NOT NULL )
	AND complete_datetime IS NULL  
ORDER BY `personal_notes`.`caution_datetime`  ASC