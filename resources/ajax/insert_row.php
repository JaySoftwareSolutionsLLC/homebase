<?php
// Goal is to have this page allow user to insert a row into any table by providing table name and associative array of values
// Include resources
include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
// Connect to DB
$conn = connect_to_db();
// Retrieve posted values
$table_name = $_POST['table-name'] ?? '';
$table_value_array = $_POST['associative-array'] ?? array();

?>