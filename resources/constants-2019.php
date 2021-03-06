<?php

//--- Styling Constants ---
const COLOR_SEAL_AND_DESIGN = 			'hsl(340, 86%, 32%)'; // Determines the display color of seal and design data on reports
const COLOR_RICKS_ON_MAIN = 			'hsl(33, 16%, 78%)'; // Determines the display color of seal and design data on reports

//--- Universal Constants ---
const SEC_IN_DAY = 						(60 * 60 * 24);

//--- Financial Constants ---
const START_DATE_STRING_FINANCIAL = 	'January 1st 2019';

const ANNUAL_NET_WORTH_CONTRIBUTION_TARGET = 40000;
const ANNUAL_INCOME_TARGET = 			82250;	// If hit, annual After Tax Income ~$60,000
const ANNUAL_EXPENDITURE_TARGET =		20000;	// If hit and Income Target hit...net worth contribution ~$40,000

define("AVG_DAILY_INCOME_TARGET", number_format((ANNUAL_INCOME_TARGET / 365), 2));
define("AVG_DAILY_EXPENDITURE_TARGET", number_format((ANNUAL_EXPENDITURE_TARGET / 365), 2));

// DEPRECATED...should not break anything const HOURLY_WAGE_SEAL = 				21.63;
const HOURLY_WAGES_SEAL = 				array(25, 25.96, 27.88); // Array value must correspond with HOURLY_WAGES_DATESTRINGS_SEAL date. Referenced in reports/weekly-report/index.php
const HOURLY_WAGES_DATESTRINGS_SEAL =	array('2018-12-03', '2019-03-11', '2019-07-01'); // Array date must correspond with HOURLY_WAGES_SEAL value. Referenced in reports/weekly-report/index.php
const CASHABLE_PTO_HOURS =              0;//12; // Dont like this setup currently, because as PTO is used and as extra PTO is received I will have to derive and update this. Note: This will drop to zero in June 2019 because all cashable hours will have been cashed out

const HOURLY_WAGE_RICKS = 				7.50; // Looks like this doesnt change until 2020 when it will jump to 7.80
const HOURLY_WAGE_TARGET =				28.75;

const WEEKLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 7;
const WEEKLY_EXPENDITURE_TARGET = 		AVG_DAILY_EXPENDITURE_TARGET * 7;
const WEEKLY_INCOME_DIFF_TARGET = 		WEEKLY_INCOME_TARGET - WEEKLY_EXPENDITURE_TARGET;
const WEEKLY_HOURS_TARGET = 			55;

const MONTHLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 365 / 12;
const MONTHLY_EXPENDITURE_TARGET =		AVG_DAILY_EXPENDITURE_TARGET * 365 / 12;
const MONTHLY_INCOME_DIFF_TARGET = 		MONTHLY_INCOME_TARGET - MONTHLY_EXPENDITURE_TARGET;
const MONTHLY_HOURS_TARGET = 			WEEKLY_HOURS_TARGET * 52 / 12;

const LUXURY_EXPENDITURES =             array('entertainment', 'clothing', 'personal', 'giving', 'travel', /*'fee',*/ 'office'); // List of the finance_expenses.type which correspond with non-necessary expenses. Some are necessary such as clothing and fee, but as a guideline these are avoidable.

// DEPRECATED FOR 2019...will (likely) break index.php const PRE_JUNE_RICKS_INCOME =			12914;
const ESTIMATED_AFTER_TAX_PERCENTAGE =	73; // Based off of Effective Tax Rate from smartasset.com

// DEPRECATED FOR 2019...will (likely) break index.php const JUNE_1ST_NET_WORTH = 				10000;
const START_OF_YEAR_NET_WORTH =			39880;
const END_OF_YEAR_NET_WORTH_TARGET =	81000; // Based off 39.9k NW @ end of 2018 + 40k contribution + 7% ROI on investments
// DEPRECATED 03.09.2019 due to market influence const ANNUAL_NET_WORTH_CONTRIBUTION_TARGET =   END_OF_YEAR_NET_WORTH_TARGET - START_OF_YEAR_NET_WORTH;
// DEPRECATED FOR 2019...will (likely) break index.php const JUNE_1ST_DEBT =					17000;

//--- Other Constants ---
const START_DATE_STRING_CERT_GOAL =     '2019-10-15';
const END_DATE_STRING_CERT_GOAL =       '2020-01-31';
const SOFTWARE_DEV_TARGET_CERT_GOAL =   150;

//--- Fitness Constants ---
const START_DATE_STRING_RUNNING = 		'January 8th 2019';
const STARTING_MILE_TIME =				475; // In seconds
const MILE_TIME_TARGET =				390; // In seconds

const START_DATE_STRING_BODY_WEIGHT =	'January 5th 2019';
const STARTING_BODY_WEIGHT =			152.6; // In pounds
const BODY_WEIGHT_TARGET =				165.0; // In pounds

const START_DATE_STRING_UPPER_ARM_CIRC = 'January 8th 2019';
const STARTING_UPPER_ARM_CIRC = 		12.25; // In inches
const UPPER_ARM_CIRC_TARGET =			13.5; // In inches

const START_DATE_SOFTWARE_DEV_HOURS =   'January 29th 2019';
const SOFTWARE_DEV_TARGET_HOURS =       480; // Equates to ~10 hours / week

const START_DATE_MINDFULNESS_HOURS =    'January 29th 2019';
const MINDFULNESS_TARGET_HOURS =        72; // Equates to 3 sessions / week @ 30 minutes each

const START_DATE_STRING_DAY_INFO =      'January 29th 2019';

/*
const START_DATE_STRING_BENCH_PRESS = 	'June 4th 2018';
const STARTING_BENCH_PRESS =			160; // In pounds
const END_OF_YEAR_BENCH_PRESS_TARGET = 	200; // In pounds
*/


?>