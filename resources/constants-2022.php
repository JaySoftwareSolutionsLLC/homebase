<?php

//--- Styling Constants ---
const COLOR_SEAL_AND_DESIGN = 			'hsl(340, 86%, 32%)'; // Determines the display color of seal and design data on reports
const COLOR_RICKS_ON_MAIN = 			'hsl(33, 16%, 78%)'; // Determines the display color of ricks on reports

//--- Universal Constants ---
const SEC_IN_DAY = 						(60 * 60 * 24);

//--- Financial Constants ---
const START_DATE_STRING_FINANCIAL = 	'January 1st 2022';

const ANNUAL_NET_WORTH_CONTRIBUTION_TARGET =    0;
const ANNUAL_INCOME_TARGET = 			        37000; // Enough to cover my expenses.
const ANNUAL_EXPENDITURE_TARGET =		        30000;

define("AVG_DAILY_INCOME_TARGET", number_format((ANNUAL_INCOME_TARGET / 365), 2));
define("AVG_DAILY_EXPENDITURE_TARGET", number_format((ANNUAL_EXPENDITURE_TARGET / 365), 2));

const HOURLY_WAGES_SEAL = 				array(34.13); // Array value must correspond with HOURLY_WAGES_DATESTRINGS_SEAL date.
const HOURLY_WAGES_DATESTRINGS_SEAL =	array('2021-07-01'); // Array date must correspond with HOURLY_WAGES_SEAL value of same index.
const CASHABLE_PTO_HOURS =              0;
const REMAINING_BONUSES =               0;
// Previous year income can be found on paylocity {REG + PTO + PTOPO + RETRO + BONUS + MED WAIVER}
const REMAINING_EMP_401K_DELTA =        0 + 0 + 0; // Vestment increase + Safe Harbor (3% of previous year income) + Profit Sharing (~4.36% this year)

const HOURLY_WAGE_RICKS = 				8.35;
const HOURLY_WAGE_TARGET =				36.50; // For first part of year

const WEEKLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 7;
const WEEKLY_EXPENDITURE_TARGET = 		AVG_DAILY_EXPENDITURE_TARGET * 7;
const WEEKLY_INCOME_DIFF_TARGET = 		WEEKLY_INCOME_TARGET - WEEKLY_EXPENDITURE_TARGET;
const WEEKLY_HOURS_TARGET = 			46; // 40 hours @ S&D + 6 @ Ricks

const MONTHLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 365 / 12;
const MONTHLY_EXPENDITURE_TARGET =		AVG_DAILY_EXPENDITURE_TARGET * 365 / 12;
const MONTHLY_INCOME_DIFF_TARGET = 		MONTHLY_INCOME_TARGET - MONTHLY_EXPENDITURE_TARGET;
const MONTHLY_HOURS_TARGET = 			WEEKLY_HOURS_TARGET * 52 / 12;

const LUXURY_EXPENDITURES =             array('entertainment', 'clothing', 'personal', 'giving', 'travel', /*'fee',*/ 'office'); // List of the finance_expenses.type which correspond with non-necessary expenses. Some are necessary such as clothing and fee, but as a guideline these are avoidable.

const ESTIMATED_AFTER_TAX_PERCENTAGE =	72.33; // Based off of Effective Tax Rate from smartasset.com

const START_OF_YEAR_NET_WORTH =			212500;
const END_OF_YEAR_NET_WORTH_TARGET =	216750; // Current NW + 8,750 (Investment Growth) - 4,500 (Depreciation)

?>