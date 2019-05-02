SELECT caution_datetime, summary
FROM `personal_notes`
WHERE 	caution_datetime <= '2019-04-30'
    AND (warning_datetime IS NULL OR warning_datetime >= '2019-04-30')
	AND complete_datetime IS NULL