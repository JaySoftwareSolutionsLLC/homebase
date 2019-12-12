<?php

// File to be called via ajax to create a new fitness_lift row

// Include resources

use Elastica\Response;

include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

// Set variables
$exercise_id = $_POST['exercise_id'] ?? $_GET['exercise_id'] ?? null;
if (empty($exercise_id)) {
    exit;
}
$workout_structure_id = $_POST['workout_structure_id'] ?? $_GET['workout_structure_id'] ?? 4;
$total_reps = $_POST['total_reps'] ?? $_GET['total_reps'] ?? 'null';
$weight = $_POST['weight'] ?? $_GET['weight'] ?? 'null';
$datetime = $_POST['datetime'] ?? $_GET['datetime'] ?? return_date_from_str($str = 'now +1 hour', $output_type = 'string', $output_format = 'Y-m-d H:i:00');

// Connect to DB
$conn = connect_to_db();

$qry = "INSERT INTO `fitness_lifts`(`exercise_id`,`workout_structure_id`,`total_reps`,`weight`,`datetime`)
VALUES ($exercise_id, $workout_structure_id, $total_reps, $weight, '$datetime');";

if ($conn->query($qry) === TRUE) {
    $response = new stdClass;
    $response->success = true;
    $response->qry = $qry;
    echo json_encode($response);
}
else {
    $response = new stdClass;
    $response->success = false;
    $response->qry = $qry;
    $response->errors = $conn->error;
    echo json_encode($response);
}

?>