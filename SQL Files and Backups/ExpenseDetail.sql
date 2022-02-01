SELECT 	fe.name
		, fe.type
        , fe.jss_percentage
        , COUNT(*) AS 'Occurances'
        , ROUND(SUM(fe.amount),0) AS 'Net'
FROM `finance_expenses` fe
WHERE fe.date BETWEEN '2021-01-01' AND '2021-12-31'
GROUP BY fe.name, fe.type, fe.jss_percentage
ORDER BY fe.type, Net DESC