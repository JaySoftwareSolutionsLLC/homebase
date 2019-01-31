<?php 
// Include resources
include('../resources.php');
// Connect to DB
$conn = connect_to_db();
// Initialize variables
$title = 'Weekly Report Generator';
$start_date = set_post_value('start-date');
$end_date = set_post_value('end-date');


$entry_msg = "Welcome to the Seal & Design shift submission page.";

include 'report-header.php';

var_dump($_POST);