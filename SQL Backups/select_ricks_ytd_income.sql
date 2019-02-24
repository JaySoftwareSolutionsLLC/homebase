SELECT ROUND(
            SUM( 
                CASE
                    WHEN type IN ('AM', 'PM') THEN (tips) + (7.5*frs.hours)
                    WHEN type = 'OTB' THEN (tips)
                    ELSE 999999 -- Throw in huge number to give visibility to outlying cases
                END
            )
        , 0)
FROM finance_ricks_shifts AS frs
WHERE frs.date >= '2019/01/01'