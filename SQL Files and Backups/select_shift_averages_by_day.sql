SELECT 	DAYNAME(date) AS 'dow',
		ROUND( 
            AVG(
                CASE
                    WHEN type IN ('AM', 'PM') THEN (tips + (hours * 7.5))
                    WHEN type IN ('OTB') THEN (tips)
                    ELSE 0
                END
            ) , 2
      	) AS 'AVG income',
        ROUND( AVG(hours) , 2 ) AS 'AVG hours'
FROM `finance_ricks_shifts` 
WHERE   type = 'PM'
    AND (
        MONTH(date) > MONTH(CURRENT_DATE)
        OR 
        (MONTH(date) = MONTH(CURRENT_DATE)
        AND DAY(date) >= DAY(CURRENT_DATE) ) 
        )
GROUP BY DAYNAME(date)