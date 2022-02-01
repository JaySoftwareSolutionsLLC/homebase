SELECT 	fe.name
		, fe.type
        , fe.jss_percentage
        , COUNT(*) AS 'Occurances'
FROM `finance_expenses` fe
WHERE fe.date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
GROUP BY fe.name, fe.type, fe.jss_percentage
HAVING COUNT(*) >= 3
ORDER BY Occurances DESC