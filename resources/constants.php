<?php

//--- Universal Constants ---
const SEC_IN_DAY = 						(60 * 60 * 24);

//--- Financial Constants ---
const START_DATE_STRING_FINANCIAL = 	'June 1st 2018';

const ANNUAL_INCOME_TARGET = 			74250;	// If hit, annual After Tax Income ~$55,000
const ANNUAL_EXPENDITURE_TARGET =		15000;	// If hit and Income Target hit...net worth contribution ~$40,000

define("AVG_DAILY_INCOME_TARGET", number_format((ANNUAL_INCOME_TARGET / 365), 2));
define("AVG_DAILY_EXPENDITURE_TARGET", number_format((ANNUAL_EXPENDITURE_TARGET / 365), 2));

const HOURLY_WAGE_SEAL = 				21.63;
const HOURLY_WAGE_RICKS = 				7.50;
const HOURLY_WAGE_TARGET =				26;

const WEEKLY_INCOME_TARGET = 			AVG_DAILY_INCOME_TARGET * 7;
const WEEKLY_EXPENDITURE_TARGET = 		AVG_DAILY_EXPENDITURE_TARGET * 7;
const WEEKLY_INCOME_DIFF_TARGET = 		WEEKLY_INCOME_TARGET - WEEKLY_EXPENDITURE_TARGET;
const WEEKLY_HOURS_TARGET = 			55;

const PRE_JUNE_RICKS_INCOME =			12914;
const ESTIMATED_AFTER_TAX_PERCENTAGE =	75;

const JUNE_1ST_NET_WORTH = 				10000;
const END_OF_YEAR_NET_WORTH_TARGET =	30000;
const JUNE_1ST_DEBT =					17000;

//--- Fitness Constants ---
const START_DATE_STRING_RUNNING = 		'June 29th 2018';
const STARTING_MILE_TIME =				515; // In seconds
const MILE_TIME_TARGET =				405; // In seconds

const START_DATE_STRING_BODY_WEIGHT =	'June 4th 2018';
const STARTING_BODY_WEIGHT =			147.0; // In Pounds
const BODY_WEIGHT_TARGET =				160.0; // In Pounds

?>