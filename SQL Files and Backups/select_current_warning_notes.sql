SELECT caution_datetime, summary
FROM `personal_notes`
WHERE 	warning_datetime <= '2019-04-30'
	AND complete_datetime IS NULL