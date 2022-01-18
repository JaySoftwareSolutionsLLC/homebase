SELECT 	l.account_id
		, a.name
		, l.Date
        , l.value
FROM (
	SELECT account_id, MAX(Date) as MaxTime
	FROM jaysoftw_homebase.finance_account_log
    WHERE Date <= '2021-12-31'
	GROUP BY account_id
) r
INNER JOIN jaysoftw_homebase.finance_account_log l
	ON	l.account_id = r.account_id 
	AND l.Date = r.MaxTime
INNER JOIN jaysoftw_homebase.finance_accounts a
	ON 	l.account_id = a.id
WHERE a.closed_on IS NULL;