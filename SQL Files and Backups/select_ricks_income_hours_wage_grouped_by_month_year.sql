SELECT 	YEAR(date) AS 'year'
		,MONTH(date) AS 'month'
        ,MONTHNAME(date) AS 'monthname'
        ,DAYNAME(date) AS 'dow'
		,ROUND( 
            AVG(
                CASE
                    WHEN type IN ('AM', 'PM') THEN (tips + (hours * 7.5))
                    WHEN type IN ('OTB') THEN (tips)
                    ELSE 0
                END
            ) , 2
      	) AS 'AVG income'
        ,ROUND( AVG(hours) , 2 ) AS 'AVG hours'
        ,ROUND( AVG(
                    CASE
                        WHEN type IN ('AM', 'PM') THEN (tips + (hours * 7.5))
                        WHEN type IN ('OTB') THEN (tips)
                        ELSE 0
                    END
                )
               /
               AVG(hours)
              , 2) AS 'AVG hourly'
     	,COUNT(*) AS 'Occurances'
FROM `finance_ricks_shifts`
WHERE   type = 'PM'
	AND DAYNAME(date) = 'Tuesday'
GROUP BY DAYNAME(date), MONTH(date), YEAR(date)
ORDER BY YEAR(date), MONTH(date)