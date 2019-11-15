<?php

    // File to be called via ajax to load a subset of notes in the form of cards

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Set variables
    $habit_id = $_POST['habit_id'] ?? $_GET['habit_id'] ?? null;
    $status = $_POST['status'] ?? $_GET['status'] ?? 'Completed';

    // Connect to DB
    $conn = connect_to_db();

    $qry = "INSERT INTO personal_wellness_habit_logs (habit_id, status) VALUES ($habit_id, '$status');";
    $response_object = new stdClass;
    // echo $qry;
    $response_object->qry = $qry;
    if ($conn->query($qry) === TRUE) {
        $response_object->success = true;
    } else {
        $response_object->success = false;
    }
    echo json_encode($response_object);

?>