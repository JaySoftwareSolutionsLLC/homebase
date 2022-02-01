SELECT 
fe.type
, (	SELECT ROUND(SUM(fe_sub.amount),0)
 	FROM finance_expenses AS fe_sub
 	WHERE 	fe_sub.date BETWEEN '2019-01-01' AND '2019-12-31'
 		AND fe_sub.type = fe.type
 	GROUP BY fe_sub.type) AS '2019'
, (	SELECT ROUND(SUM(fe_sub.amount),0)
 	FROM finance_expenses AS fe_sub
 	WHERE 	fe_sub.date BETWEEN '2020-01-01' AND '2020-12-31'
 		AND fe_sub.type = fe.type
 	GROUP BY fe_sub.type) AS '2020'
, (	SELECT ROUND(SUM(fe_sub.amount),0)
 	FROM finance_expenses AS fe_sub
 	WHERE 	fe_sub.date BETWEEN '2021-01-01' AND '2021-12-31'
 		AND fe_sub.type = fe.type
 	GROUP BY fe_sub.type) AS '2021'
FROM finance_expenses AS fe
GROUP BY fe.type