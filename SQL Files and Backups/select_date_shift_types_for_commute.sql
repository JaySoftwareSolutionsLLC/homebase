SELECT 	rs.date
		
		, CASE
        	WHEN EXISTS(	SELECT *
                        	FROM finance_seal_shifts AS fss1
                        	WHERE fss1.date = rs.date	)
             AND EXISTS(	SELECT *
                        	FROM finance_ricks_shifts AS frs1
                        	WHERE frs1.date = rs.date	)
            THEN 'BOTH'
        	WHEN EXISTS(	SELECT *
                        	FROM finance_seal_shifts AS fss1
                        	WHERE fss1.date = rs.date	)
            AND NOT EXISTS(	SELECT *
                        	FROM finance_ricks_shifts AS frs1
                        	WHERE frs1.date = rs.date	)
          	THEN 'S&D Only'
            WHEN NOT EXISTS(	SELECT *
                        	FROM finance_seal_shifts AS fss1
                        	WHERE fss1.date = rs.date	)
            AND EXISTS(	SELECT *
                        	FROM finance_ricks_shifts AS frs1
                        	WHERE frs1.date = rs.date	)
          	THEN 'Ricks Only'
           	ELSE '???'
     	END AS 'Shift Type'
        
FROM (
    SELECT fss.date
    FROM finance_seal_shifts AS fss
    UNION
    SELECT frs.date
    FROM finance_ricks_shifts AS frs
    ) AS rs
WHERE rs.date >= '2018-07-30'
	AND rs.date <= '2019-07-29'
ORDER BY rs.date