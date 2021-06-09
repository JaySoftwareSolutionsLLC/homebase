<?php

//--- Styling Constants ---
const COLOR_SEAL_AND_DESIGN = 			'hsl(340, 86%, 32%)'; // Determines the display color of seal and design data on reports
const COLOR_RICKS_ON_MAIN = 			'hsl(33, 16%, 78%)'; // Determines the display color of seal and design data on reports

//--- Universal Constants ---
const SEC_IN_DAY = 						(60 * 60 * 24);

//--- Financial Constants ---
const START_DATE_STRING_FINANCIAL = 	'January 1st 2020';

// As of January 6th, current target is to earn 86k, invest 10k in ROTH account(s), and save a further 32k cash. This will require only spending $20,770 during the year.
const ANNUAL_NET_WORTH_CONTRIBUTION_TARGET = 42000;
const ANNUAL_INCOME_TARGET = 			//82882; // If hit, annual After Tax Income ~$60,770 
                                        86000;	// If hit, annual After Tax Income ~$62,770
const ANNUAL_EXPENDITURE_TARGET =		20770;	// If hit and Income Target hit...net worth contribution ~$42,000

define("AVG_DAILY_INCOME_TARGET", number_format((ANNUAL_INCOME_TARGET / 365), 2));
define("AVG_DAILY_EXPENDITURE_TARGET", number_format((ANNUAL_EXPENDITURE_TARGET / 365), 2));

// DEPRECATED...should not break anything const HOURLY_WAGE_SEAL = 				21.63;
const HOURLY_WAGES_SEAL = 				array(27.88, 29.81); // Array value must correspond with HOURLY_WAGES_DATESTRINGS_SEAL date. Referenced in reports/weekly-report/index.php
const HOURLY_WAGES_DATESTRINGS_SEAL =	array('2019-07-01', '2020-06-29'); // Array date must correspond with HOURLY_WAGES_SEAL value. Referenced in reports/weekly-report/index.php
const CASHABLE_PTO_HOURS =              0; // Dont like this setup currently, because as PTO is used and as extra PTO is received I will have to derive and update this. Note: This will drop to zero in June because all cashable hours will have been cashed out

const HOURLY_WAGE_RICKS = 				7.80;
const HOURLY_WAGE_TARGET =				34.82; // Based off 2470 hours in 2020 and net income 86k;

const WEEKLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 7;
const WEEKLY_EXPENDITURE_TARGET = 		AVG_DAILY_EXPENDITURE_TARGET * 7;
const WEEKLY_INCOME_DIFF_TARGET = 		WEEKLY_INCOME_TARGET - WEEKLY_EXPENDITURE_TARGET;
const WEEKLY_HOURS_TARGET = 			47.5; // Based off 2470 hours

const MONTHLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 365 / 12;
const MONTHLY_EXPENDITURE_TARGET =		AVG_DAILY_EXPENDITURE_TARGET * 365 / 12;
const MONTHLY_INCOME_DIFF_TARGET = 		MONTHLY_INCOME_TARGET - MONTHLY_EXPENDITURE_TARGET;
const MONTHLY_HOURS_TARGET = 			WEEKLY_HOURS_TARGET * 52 / 12;

const LUXURY_EXPENDITURES =             array('entertainment', 'clothing', 'personal', 'giving', 'travel', /*'fee',*/ 'office'); // List of the finance_expenses.type which correspond with non-necessary expenses. Some are necessary such as clothing and fee, but as a guideline these are avoidable.

const ESTIMATED_AFTER_TAX_PERCENTAGE =	73; // Based off of Effective Tax Rate from smartasset.com

const START_OF_YEAR_NET_WORTH =			98286;
const END_OF_YEAR_NET_WORTH_TARGET =	138500; // Based off 73.5k invested @ end of 2019 * 1.07 (ROI) + ~20k cash + 40k NW contribution over year

//--- Other Constants ---
// const START_DATE_STRING_CERT_GOAL =     '2019-10-15';
// const END_DATE_STRING_CERT_GOAL =       '2020-01-31';
// const SOFTWARE_DEV_TARGET_CERT_GOAL =   150;

//--- Fitness Constants ---
// const START_DATE_STRING_RUNNING = 		'January 8th 2019';
// const STARTING_MILE_TIME =				475; // In seconds
// const MILE_TIME_TARGET =				390; // In seconds

// const START_DATE_STRING_BODY_WEIGHT =	'January 5th 2019';
// const STARTING_BODY_WEIGHT =			152.6; // In pounds
// const BODY_WEIGHT_TARGET =				165.0; // In pounds

// const START_DATE_STRING_UPPER_ARM_CIRC = 'January 8th 2019';
// const STARTING_UPPER_ARM_CIRC = 		12.25; // In inches
// const UPPER_ARM_CIRC_TARGET =			13.5; // In inches

// const START_DATE_SOFTWARE_DEV_HOURS =   'January 29th 2019';
// const SOFTWARE_DEV_TARGET_HOURS =       480; // Equates to ~10 hours / week

// const START_DATE_MINDFULNESS_HOURS =    'January 29th 2019';
// const MINDFULNESS_TARGET_HOURS =        72; // Equates to 3 sessions / week @ 30 minutes each

// const START_DATE_STRING_DAY_INFO =      'January 29th 2019';

/*
const START_DATE_STRING_BENCH_PRESS = 	'June 4th 2018';
const STARTING_BENCH_PRESS =			160; // In pounds
const END_OF_YEAR_BENCH_PRESS_TARGET = 	200; // In pounds
*/


?>