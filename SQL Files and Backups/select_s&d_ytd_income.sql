/* NOTE: This formula may not accurately account for earned income from non-received checks and/or days worked in prior year. */
SELECT SUM(amount) 
FROM finance_seal_income AS fsi
WHERE fsi.date >= '2019/01/01'