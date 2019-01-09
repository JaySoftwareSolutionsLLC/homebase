<?php

//--- Styling Constants ---
const COLOR_SEAL_AND_DESIGN = 			'hsl(340, 86%, 32%)'; // Determines the display color of seal and design data on reports
const COLOR_RICKS_ON_MAIN = 			'hsl(33, 16%, 78%)'; // Determines the display color of seal and design data on reports

//--- Universal Constants ---
const SEC_IN_DAY = 						(60 * 60 * 24);

//--- Financial Constants ---
const START_DATE_STRING_FINANCIAL = 	'January 1st 2019';

const ANNUAL_INCOME_TARGET = 			82250;	// If hit, annual After Tax Income ~$60,000
const ANNUAL_EXPENDITURE_TARGET =		20000;	// If hit and Income Target hit...net worth contribution ~$40,000

define("AVG_DAILY_INCOME_TARGET", number_format((ANNUAL_INCOME_TARGET / 365), 2));
define("AVG_DAILY_EXPENDITURE_TARGET", number_format((ANNUAL_EXPENDITURE_TARGET / 365), 2));

// DEPRECATED...should not break anything const HOURLY_WAGE_SEAL = 				21.63;
const HOURLY_WAGES_SEAL = 				array(25); // Array value must correspond with HOURLY_WAGES_DATESTRINGS_SEAL date. Referenced in reports/weekly-report/index.php
const HOURLY_WAGES_DATESTRINGS_SEAL =	array('2018-12-03'); // Array date must correspond with HOURLY_WAGES_SEAL value. Referenced in reports/weekly-report/index.php


const HOURLY_WAGE_RICKS = 				8.40; // This I believe to be accurate based off of this PDF https://labor.ny.gov/formsdocs/factsheets/pdfs/p717.pdf
const HOURLY_WAGE_TARGET =				28;

const WEEKLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 7;
const WEEKLY_EXPENDITURE_TARGET = 		AVG_DAILY_EXPENDITURE_TARGET * 7;
const WEEKLY_INCOME_DIFF_TARGET = 		WEEKLY_INCOME_TARGET - WEEKLY_EXPENDITURE_TARGET;
const WEEKLY_HOURS_TARGET = 			55;

const MONTHLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 365 / 12;
const MONTHLY_EXPENDITURE_TARGET =		AVG_DAILY_EXPENDITURE_TARGET * 365 / 12;
const MONTHLY_INCOME_DIFF_TARGET = 		MONTHLY_INCOME_TARGET - MONTHLY_EXPENDITURE_TARGET;
const MONTHLY_HOURS_TARGET = 			WEEKLY_HOURS_TARGET * 52 / 12;

// DEPRECATED FOR 2019...will (likely) break index.php const PRE_JUNE_RICKS_INCOME =			12914;
const ESTIMATED_AFTER_TAX_PERCENTAGE =	73; // Based off of Effective Tax Rate from smartasset.com

// DEPRECATED FOR 2019...will (likely) break index.php const JUNE_1ST_NET_WORTH = 				10000;
const START_OF_YEAR_NET_WORTH =			36519;
const END_OF_YEAR_NET_WORTH_TARGET =	75000; // Based off 36.5k NW @ end of 2018 + 40k contribution
// DEPRECATED FOR 2019...will (likely) break index.php const JUNE_1ST_DEBT =					17000;

//--- Fitness Constants ---
const START_DATE_STRING_RUNNING = 		'January 1st 2019';
const STARTING_MILE_TIME =				464; // In seconds
// TBD const MILE_TIME_TARGET =				390; // In seconds

const START_DATE_STRING_BODY_WEIGHT =	'January 1st 2019';
const STARTING_BODY_WEIGHT =			156.4; // In pounds
const BODY_WEIGHT_TARGET =				165.0; // In pounds

/*
const START_DATE_STRING_BENCH_PRESS = 	'June 4th 2018';
const STARTING_BENCH_PRESS =			160; // In pounds
const END_OF_YEAR_BENCH_PRESS_TARGET = 	200; // In pounds
*/


?>