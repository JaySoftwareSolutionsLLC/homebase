<?php

    // File to be called via ajax to delete all started habit logs

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Connect to DB
    $conn = connect_to_db();

    $qry = " DELETE FROM personal_wellness_habit_logs WHERE status = 'Started' ";
    $response_object = new stdClass;
    $response_object->qry = $qry;
    if ($conn->query($qry)) {
        $response_object->type = 'DELETE';
        $response_object->success = true;
    } else {
        $response_object->type = 'DELETE';
        $response_object->success = false;
        $response_object->error = $conn->error;
    }
    echo json_encode($response_object);

?>