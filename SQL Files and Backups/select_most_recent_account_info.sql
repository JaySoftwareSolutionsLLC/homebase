SELECT f_a.*, ( 	SELECT value
									FROM finance_account_log AS f_a_l
									WHERE f_a.id = f_a_l.account_id
									ORDER BY f_a_l.date DESC, f_a_l.id DESC
									LIMIT 1) AS 'most recent value'
				FROM finance_accounts AS f_a
				WHERE f_a.closed_on IS NULL
				GROUP BY f_a.id, f_a.name, f_a.type, f_a.expected_annual_return