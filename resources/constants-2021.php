<?php

//--- Styling Constants ---
const COLOR_SEAL_AND_DESIGN = 			'hsl(340, 86%, 32%)'; // Determines the display color of seal and design data on reports
const COLOR_RICKS_ON_MAIN = 			'hsl(33, 16%, 78%)'; // Determines the display color of seal and design data on reports

//--- Universal Constants ---
const SEC_IN_DAY = 						(60 * 60 * 24);

//--- Financial Constants ---
const START_DATE_STRING_FINANCIAL = 	'January 1st 2021';

const ANNUAL_NET_WORTH_CONTRIBUTION_TARGET =    45000;  
const ANNUAL_INCOME_TARGET = 			        95000;	// If hit, annual After Tax Income $68,713
const ANNUAL_EXPENDITURE_TARGET =		        23713;	// If hit and Income Target hit...net worth contribution ~$45,000

define("AVG_DAILY_INCOME_TARGET", number_format((ANNUAL_INCOME_TARGET / 365), 2));
define("AVG_DAILY_EXPENDITURE_TARGET", number_format((ANNUAL_EXPENDITURE_TARGET / 365), 2));

// DEPRECATED...should not break anything const HOURLY_WAGE_SEAL = 				21.63;
const HOURLY_WAGES_SEAL = 				array(29.80); // Array value must correspond with HOURLY_WAGES_DATESTRINGS_SEAL date. Referenced in reports/weekly-report/index.php
const HOURLY_WAGES_DATESTRINGS_SEAL =	array('2020-06-29'); // Array date must correspond with HOURLY_WAGES_SEAL value. Referenced in reports/weekly-report/index.php
const CASHABLE_PTO_HOURS =              0; // Dont like this setup currently, because as PTO is used and as extra PTO is received I will have to derive and update this. Note: This will drop to zero in June because all cashable hours will have been cashed out

const HOURLY_WAGE_RICKS = 				7.80;
const HOURLY_WAGE_TARGET =				38.71; // (238 working days * 8 hrs / day) + 11 hrs @ Ricks / week * 40 = 2454 (Hoping Ricks opens back up by April)

const WEEKLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 7;
const WEEKLY_EXPENDITURE_TARGET = 		AVG_DAILY_EXPENDITURE_TARGET * 7;
const WEEKLY_INCOME_DIFF_TARGET = 		WEEKLY_INCOME_TARGET - WEEKLY_EXPENDITURE_TARGET;
const WEEKLY_HOURS_TARGET = 			47.19; // Based off 2454 hours / 52 weeks

const MONTHLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 365 / 12;
const MONTHLY_EXPENDITURE_TARGET =		AVG_DAILY_EXPENDITURE_TARGET * 365 / 12;
const MONTHLY_INCOME_DIFF_TARGET = 		MONTHLY_INCOME_TARGET - MONTHLY_EXPENDITURE_TARGET;
const MONTHLY_HOURS_TARGET = 			WEEKLY_HOURS_TARGET * 52 / 12;

const LUXURY_EXPENDITURES =             array('entertainment', 'clothing', 'personal', 'giving', 'travel', /*'fee',*/ 'office'); // List of the finance_expenses.type which correspond with non-necessary expenses. Some are necessary such as clothing and fee, but as a guideline these are avoidable.

const ESTIMATED_AFTER_TAX_PERCENTAGE =	72.33; // Based off of Effective Tax Rate from smartasset.com

const START_OF_YEAR_NET_WORTH =			147679;
const END_OF_YEAR_NET_WORTH_TARGET =	195000; // Based off 95k invested @ end of 2020 * 1.07 (ROI) + 12k cash + 40k assets - 4k depreciation + 45k NW contribution over year

?>